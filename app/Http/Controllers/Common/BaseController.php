<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Good;
use App\Models\GoodFormat;
use App\Models\GoodSpecPrice;
use App\Models\Order;
use App\Models\OrderGood;
use App\Models\User;
use DB;
use Illuminate\Http\Request;
use Storage;

class BaseController extends Controller
{
    public $theme = 'home';
    public function __construct()
    {
        $this->theme = isset(cache('config')['theme']) && cache('config')['theme'] != null ? cache('config')['theme'] : 'home';
    }
    // 更新购物车
	public function updateCart($uid)
    {
        $sid = session()->getId();
        // 找出老数据库购物车里的东西
        $old_carts = Cart::where('user_id',$uid)->get();
        // 把session_id更新过来
        Cart::where('user_id',$uid)->update(['session_id'=>$sid]);
        $old_carts = $old_carts->keyBy('good_id')->toArray();
        // 找出新加入购物车的东西
        $new_carts = Cart::where('session_id',$sid)->get();
        // 先循环来整合现在session_id与数据库的cart
        if ($new_carts->count() > 0) {
            $tmp = [];
            foreach ($new_carts as $k => $v) {
                $gid = $v->good_id;
                // 判断一下现在的session_id里有没有同一款产品
                if (isset($old_carts[$gid]) && $old_carts[$gid]['good_spec_key'] == $v['good_spec_key']) {
                    $nums = $v->nums + $old_carts[$gid]['nums'];
                    $price = $v->price;
                    $v = ['session_id'=>$sid,'user_id'=>$uid,'good_id'=>$gid,'good_spec_key'=>$v['good_spec_key'],'nums'=>$nums,'price'=>$price,'total_prices'=>$nums * $price];
                    // 把旧的删除，新的更新
                    Cart::where('user_id',$uid)->where('good_id',$gid)->where('good_spec_key',$v['good_spec_key'])->delete();
                    Cart::create($v);
                }
                else
                {
                    $v = ['user_id'=>$uid];
                    Cart::where('session_id',$sid)->where('good_id',$gid)->update($v);
                }
            }
        }
    }
    // 更新库存
    public function updateStore($oid = '',$type = 0)
    {
        // 事务
        DB::beginTransaction();
        try {
            if ($type) {
                // 加库存，先找出来所有的商品ID与商品属性ID
                $goods = OrderGood::where('order_id',$oid)->where('status',1)->select('id','good_id','good_spec_key','nums')->get();
                // 循环，判断是直接减商品库存，还是减带属性的库存
                foreach ($goods as $k => $v) {
                    if ($v->good_spec_key != '') {
                        GoodSpecPrice::where('good_id',$v->good_id)->where('key',$v->spec_key)->increment('store',$v->nums);
                    }
                    Good::where('id',$v->good_id)->increment('store',$v->nums); 
                    // 加销量
                    Good::where('id',$v->good_id)->decrement('sales',$v->nums);
                }
            }
            else
            {
                // 减库存，先找出来所有的商品ID与商品属性ID
                $goods = OrderGood::where('order_id',$oid)->where('status',1)->select('id','good_id','good_spec_key','nums')->get();
                // 循环，判断是直接减商品库存，还是减带属性的库存
                foreach ($goods as $k => $v) {
                    if ($v->good_spec_key != '') {
                        GoodSpecPrice::where('good_id',$v->good_id)->where('key',$v->spec_key)->decrement('store',$v->nums);
                    }
                    Good::where('id',$v->good_id)->decrement('store',$v->nums); 
                    // 加销量
                    Good::where('id',$v->good_id)->increment('sales',$v->nums);
                }
            }
            // 没出错，提交事务
            DB::commit();
            return true;
        } catch (\Exception $e) {
            // 出错回滚
            DB::rollBack();
            // dd($e->getMessage());
            Storage::prepend('updateStore.log',json_encode($e->getMessage()).date('Y-m-d H:i:s'));
            return false;
        }
    }
    // 消费记录
    public function updateOrder($order = '',$paymod = '余额')
    {
        // 事务
        DB::beginTransaction();
        try {
            Order::where('id',$order->id)->update(['paystatus'=>1,'pay_name'=>$paymod]);
            User::where('id',$order->user_id)->increment('points',$order->total_prices);
            // 消费记录
            app('com')->consume($order->user_id,$order->id,$order->total_prices,$paymod.'支付订单');
            // 没出错，提交事务
            DB::commit();
            return true;
        } catch (\Exception $e) {
            // 出错回滚
            DB::rollBack();
            // dd($e->getMessage());
            Storage::prepend('updateOrder.log',json_encode($e->getMessage()).date('Y-m-d H:i:s'));
            return false;
        }
    }
    // ajax返回
    public function ajaxReturn($code = '1',$msg = '')
    {
        exit(json_encode(['code'=>$code,'msg'=>$msg]));
        return;
    }
}
