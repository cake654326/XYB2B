<form action="javascript:;" method="post" id="form_ajax">
    {{ csrf_field() }}
    <table class="table table-striped">

        <tr>
            <td class="td_left">标题：</td>
            <td>
                <input type="text" name="data[title]" value="{{ old('data.title') }}" class="form-control input-sm">
                <p class="input-info"><span class="color_red">*</span>不超过255字符</p>
            </td>
        </tr>
        
        <tr>
            <td class="td_left">缩略图：</td>
            <td>
                <div class="clearfix thumb_btn">
                    <input type="text" readonly="readonly" name="data[thumb]" id="url3" value="{{ old('data.thumb') }}" class="form-control input-sm">
                    <div value="选择图片" id="image3"></div>
                </div>
                <p class="input-info"><span class="color_red">*</span>图片类型jpg/jpeg/gif/png，大小不超过2M</p>
                <img src="" class="thumb-src mt10 hidden img-responsive" width="300" alt="">
            </td>
        </tr>

        <tr>
            <td class="td_left">开始时间：</td>
            <td>
                <input type="text" name="data[starttime]" class="form-control input-sm" value="{{ old('data.starttime') }}" id="laydate3">
                <p class="input-info"><span class="color_red">*</span></p>
            </td>
        </tr>

        <tr>
            <td class="td_left">结束时间：</td>
            <td>
                <input type="text" name="data[endtime]" class="form-control input-sm" value="{{ old('data.endtime') }}" id="laydate4">
                <p class="input-info"><span class="color_red">*</span></p>
            </td>
        </tr>

        <tr>
            <td class="td_left">排序：</td>
            <td>
                <input type="text" name="data[sort]" value="{{ old('data.sort',0) }}" class="form-control input-xs">
                <p class="input-info"><span class="color_red">*</span>越大越靠前</p>
            </td>
        </tr>

        <tr>
            <td class="td_left">状态：</td>
            <td>
               <label class="radio-inline"><input type="radio" name="data[status]" checked="checked" class="input-radio" value="1">
                    正常</label>
                <label class="radio-inline"><input type="radio" name="data[status]" class="input-radio" value="0">关闭</label>
            </td>
        </tr>

    </table>

    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
        <div onclick='ajax_submit_form("form_ajax","{{ url('/console/huodong/add') }}")' name="dosubmit" class="btn btn-info">提交</div>
    </div>
</form>


<!-- 实例化编辑器 -->
<script type="text/javascript">
    // 上传时要填上sessionId与csrf表单令牌，否则无法通过验证
    var uploadbutton = KindEditor.uploadbutton({
        button : $('#image3')[0],
        fieldName : 'imgFile',
        url : "{{ url('console/attr/uploadimg') }}",
        extraFileUploadParams: {
            session_id : "{{ session('user')->id }}",
        },
        afterUpload : function(data) {
            if (data.error === 0) {
                var url = KindEditor.formatUrl(data.url, 'absolute');
                $('#url3').val(url);
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
    laydate({
        elem: '#laydate3',
        format: 'YYYY-MM-DD hh:mm:ss', // 分隔符可以任意定义，该例子表示只显示年月
        istime: true,
    });
    laydate({
        elem: '#laydate4',
        format: 'YYYY-MM-DD hh:mm:ss', // 分隔符可以任意定义，该例子表示只显示年月
        istime: true,
    });
</script>
