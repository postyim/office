{{HTML::style('assets/bootstrap-datepicker/css/datepicker3.css')}}

{{Form::open(array('name'=>'form-add','id'=>'form-add','method' => 'POST','role'=>'form','class'=>'form-horizontal'))}}
<div class="form-group">
    {{Form::label('title', 'Server', array('class' => 'col-sm-3 control-label req'))}}
    <div class="col-sm-6">
        {{Form::text('title', NULL,array('class'=>'form-control','id'=>'title'))}}
    </div>
</div>
<div class="form-group">
    {{Form::label('cost', 'ค่าใช้จ่าย', array('class' => 'col-sm-3 control-label'))}}
    <div class="col-sm-2">
        {{Form::text('cost', NULL,array('class'=>'form-control','id'=>'cost'))}}
    </div>
</div>
<div class="form-group">
    {{Form::label('register_date', 'วันที่สมัคร', array('class' => 'col-sm-3 control-label'))}}
    <div class="col-sm-4">
        {{Form::text('register_date', NULL,array('class'=>'form-control datepicker','id'=>'register_date'))}}
    </div>
</div>
<div class="form-group">
    {{Form::label('expire_date', 'วันหมดอายุ', array('class' => 'col-sm-3 control-label req'))}}
    <div class="col-sm-4">
        {{Form::text('expire_date', NULL,array('class'=>'form-control datepicker','id'=>'expire_date'))}}
    </div>
</div>
<div class="form-group">
    {{Form::label('contact_id', 'ผู้ติดต่อ', array('class' => 'col-sm-3 control-label'));}}
    <div class="col-sm-5">
        {{ \Form::select('contact_id', array('' => 'กรุณาเลือกผู้ติดต่อ') + $contact, null, array('class' => 'form-control', 'id' => 'contact_id')); }}
    </div>
</div>
<div class="form-group">
    {{Form::label('remark', 'หมายเหตุ', array('class' => 'col-sm-3 control-label'))}}
    <div class="col-sm-8">
        {{Form::text('remark', NULL,array('class'=>'form-control','id'=>'remark'))}}
    </div>
</div>
<div class="form-group">
    {{Form::label('disabled', '&nbsp;', array('class' => 'col-sm-3 control-label'))}}
    <div class="col-sm-3">
        {{Form::checkbox('disabled', 1)}} เปิดใช้งาน
    </div>
</div>
<div class="form-group">
    <div class="col-sm-offset-3 col-sm-10">
        {{Form::button('บันทึกการเปลี่ยนแปลง',array('class'=>'btn btn-primary btn-lg','id'=>'btnSave'))}}    
    </div>
</div>
{{ Form::close() }}
{{HTML::script('assets/bootstrap-datepicker/js/bootstrap-datepicker.js')}}
{{HTML::script('assets/bootstrap-datepicker/js/locales/bootstrap-datepicker.th.js')}}
<script type="text/javascript">
    $('.datepicker').datepicker({
        autoclose: true,
        todayHighlight: true,
        format: 'yyyy-mm-dd',
        language: 'th'
    });
    $('#btnSave').click(function () {
        $(this).attr('disabled', 'disabled');
        $.ajax({
            type: "post",
            url: base_url + index_page + "domain/server/add",
            data: $('#form-add, select input:not(#btnSave)').serializeArray(),
            success: function (data) {
                if (data.error.status == false) {
                    $('#btnSave').removeAttr('disabled');
                    $('form .form-group').removeClass('has-error');
                    $('form .help-block').remove();
                    $.each(data.error.message, function (key, value) {
                        $('#' + key).parent().parent().addClass('has-error');
                        $('#' + key).after('<p class="help-block">' + value + '</p>');
                    });
                } else {
                    window.location.href = base_url + index_page + "domain/server";
                }
            }
        });
    });
</script>