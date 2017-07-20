@extends('admin.right')

@section('content')
<form action="javascript:;" method="post" id="form_ajax">
    {{ csrf_field() }}
    <table class="table table-striped">
        <tr>
            <td class="td_left">父栏目：</td>
            <td>
                <select name="data[parentid]" id="parentid" class="form-control input-sm">
                    <option value="0">选择栏目</option>
                    {!! $treeHtml !!}
                </select>
            </td>
        </tr>


        <tr>
            <td class="td_left">分类名称：</td>
            <td>
                <input type="text" name="data[name]" class="form-control input-sm" value="{{ $info->name }}">
                <p class="input-info"><span class="color_red">*</span>最多100字符</p>
            </td>
        </tr>

        <tr>
            <td class="td_left">seo标题：</td>
            <td>
                <input type="text" name="data[seotitle]" value="{{ $info->seotitle }}" class="form-control input-sm">
                <p class="input-info">不超过255字符</p>
            </td>
        </tr>

        <tr>
            <td class="td_left">Keyword：</td>
            <td>
                <input type="text" name="data[keyword]" value="{{ $info->keyword }}" class="form-control input-sm">
                <p class="input-info">不超过255字符</p>
            </td>
        </tr>

        <tr>
            <td class="td_left">描述：</td>
            <td>
                <textarea name="data[describe]" class="form-control" rows="4">{{ $info->describe }}</textarea> 
                <p class="input-info">不超过255字符</p>
            </td>
        </tr>

        <tr>
            <td class="td_left">缩略图：</td>
            <td>
                <div class="clearfix thumb_btn">
                    <input type="text" readonly="readonly" name="data[thumb]" id="url3" value="{{ $info->thumb }}" class="form-control input-sm">
                    <div value="选择图片" id="image3"></div>
                </div>
                <p class="input-info">图片类型jpg/jpeg/gif/png，大小不超过2M</p>
                <img src="{{ $info->thumb }}" class="img-responsive thumb-src mt10" width="130" alt="">
            </td>
        </tr>
            
        <tr>
            <td class="td_left">排序：</td>
            <td>
                <input type="text" name="data[sort]" value="{{ $info->sort }}" class="form-control input-xs">
                <p class="input-info"><span class="color_red">*</span>越大越靠前</p>
            </td>
        </tr>

        <tr>
            <td></td>
            <td>
                <div class="btn-group">
                    <button type="reset" name="reset" class="btn btn-xs btn-warning">重填</button>
                    <div onclick='ajax_submit_form("form_ajax","{{ url('/console/goodcate/edit',['id'=>$info->id]) }}")' name="dosubmit" class="btn btn-xs btn-info">提交</div>
                </div>
            </td>
        </tr>

    </table>


</form>
<script>
    KindEditor.ready(function(K) {
        var uploadbutton = K.uploadbutton({
            button : K('#image3')[0],
            fieldName : 'imgFile',
            url : "{{ url('console/attr/uploadimg') }}",
            extraFileUploadParams: {
                session_id : "{{ session('user')->id }}",
            },
            afterUpload : function(data) {
                if (data.error === 0) {
                    var url = K.formatUrl(data.url, 'absolute');
                    K('#url3').val(url);
                    $('.thumb-src').attr('src',url).removeClass('hidden');
                } else {
                    alert(data.message);
                }
            },
            afterError : function(str) {
                alert('自定义错误信息: ' + str);
            }
        });
        uploadbutton.fileBox.change(function(e) {
            uploadbutton.submit();
        });
    });
</script>

@endsection