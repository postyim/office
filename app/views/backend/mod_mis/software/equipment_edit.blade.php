@extends('backend.layouts.master')

@section('style')
{{HTML::style('assets/backend/js/plugins/bootstrap-datepicker/css/datepicker3.css')}}
@stop

@section('content')
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">{{$title}}</h1>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <ul class="breadcrumb">
            @foreach ($breadcrumbs as $key => $val)
            @if ($val === reset($breadcrumbs))
            <li><a href="{{URL::to($val)}}"><i class="icon-home"></i> {{$key}}</a></li>
            @elseif ($val === end($breadcrumbs))
            <li class="active">{{$key}}</li>
            @else
            <li><a href="{{URL::to($val)}}"> {{$key}}</a></li>
            @endif
            @endforeach
        </ul>
    </div>
</div>    
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-body">
                {{Form::open(array('name'=>'form-add','id'=>'form-add','method' => 'POST','role'=>'form','class'=>'form-horizontal'))}}
                <div role="tabpanel">

                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs" role="tablist">
                        <li role="presentation" class="active"><a href="#home" aria-controls="home" role="tab" data-toggle="tab">ข้อมูลทั่วไป</a></li>
                        <li role="presentation"><a href="#option" aria-controls="option" role="tab" data-toggle="tab">คุณสมบัติ</a></li>
                    </ul>

                    <!-- Tab panes -->
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane active" id="home">
                            <div class="panel-body">
                                <div class="form-group">
                                    {{Form::label('category_id', 'หมวดหมู่', array('class' => 'col-sm-2 control-label'));}}
                                    <div class="col-sm-3">
                                        {{ \Form::select('category_id', $category,$item->category_id, array('class' => 'form-control', 'id' => 'category_id')); }}
                                    </div>
                                </div>
                                <div class="form-group">
                                    {{Form::label('company_id', 'สินทรัพย์บริษัท', array('class' => 'col-sm-2 control-label'));}}
                                    <div class="col-sm-3">
                                        {{ \Form::select('company_id', $company, $item->company_id, array('class' => 'form-control', 'id' => 'company_id')); }}
                                    </div>
                                </div>
                                <div class="form-group">
                                    {{Form::label('equipment_code', 'เลขระเบียน', array('class' => 'col-sm-2 control-label'))}}
                                    <div class="col-sm-3">
                                        {{Form::text('equipment_code', $item->equipment_code,array('class'=>'form-control','id'=>'equipment_code'))}}
                                    </div>
                                </div>                                
                                <div class="form-group">
                                    {{Form::label('title', 'ชื่อโปรแกรม/ซอฟต์แวร์', array('class' => 'col-sm-2 control-label'))}}
                                    <div class="col-sm-5">
                                        {{Form::text('title', $item->title,array('class'=>'form-control','id'=>'title'))}}
                                    </div>
                                </div>  
                                <div class="form-group">
                                    {{Form::label('description', 'คำอธิบาย', array('class' => 'col-sm-2 control-label'))}}
                                    <div class="col-sm-5">
                                        {{Form::textarea('description',  $item->description,array('class'=>'form-control','id'=>'description'))}}
                                    </div>
                                </div>
                                <div class="form-group">
                                    {{Form::label('register_date', 'วันที่ลงทะเบียน', array('class' => 'col-sm-2 control-label'))}}
                                    <div class="col-sm-2">
                                        {{Form::text('register_date', $item->register_date,array('class'=>'form-control datepicker','id'=>'register_date'))}}
                                    </div>
                                </div>
                                <div class="form-group">
                                    {{Form::label('disabled', '&nbsp;', array('class' => 'col-sm-2 control-label'))}}
                                    <div class="col-sm-3">
                                        <label>
                                            {{Form::checkbox('disabled', 1,($item->disabled==0?TRUE:FALSE))}} เปิดใช้งาน
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="option">
                            <div class="panel-body">
                                @for($i=1; $i<=20; $i++)
                                @if($spec_label['label_'.($i<10?'0'.$i:$i)]!=NULL)
                                <div class="form-group">
                                    {{Form::label(($i<10?'label_0'.$i:'label_'.$i),$spec_label['label_'.($i<10?'0'.$i:$i)], array('class' => 'col-sm-2 control-label'))}}
                                    <div class="col-sm-3">
                                        {{Form::text(($i<10?'spec_value_0'.$i:'spec_value_'.$i), $spec_value['spec_value_'.($i<10?'0'.$i:$i)],array('class'=>'form-control','id'=>($i<10?'spec_value_0'.$i:'spec_value_'.$i)))}}
                                    </div>
                                </div>
                                @endif
                                @endfor
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        {{Form::button('บันทึกการเปลี่ยนแปลง',array('class'=>'btn btn-primary btn-lg','id'=>'btnSave'))}}    
                    </div>
                </div>
                {{Form::hidden('id',$item->id)}}
                {{ Form::close() }}
            </div>
        </div>
    </div>
</div>
@stop

@section('script')
{{HTML::script('assets/backend/js/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js')}}
{{HTML::script('assets/backend/js/plugins/bootstrap-datepicker/js/locales/bootstrap-datepicker.th.js')}}
@stop

@section('script_code')
<script type="text/javascript">
    $('.datepicker').datepicker({
        autoclose: true,
        todayHighlight: true,
        format: 'yyyy-mm-dd',
        language: 'th'
    });
    $('#btnSave').click(function () {
        $(this).attr('disabled', 'disabled');
        $(this).after('&nbsp;<span id="spinner_loading"><i class="fa fa-spinner fa-spin"></i>&nbsp;&nbsp;Loading...</span>');
        $.ajax({
            type: "post",
            url: base_url + index_page + "mis/backend/software/edit/{{$item->id}}",
            data: $('#form-add, select input:not(#btnSave)').serializeArray(),
            success: function (data) {
                if (data.error.status == false) {
                    $('form .form-group').removeClass('has-error');
                    $('form .help-block').remove();
                    $('#btnSave').removeAttr('disabled');
                    $.each(data.error.message, function (key, value) {
                        $('#' + key).parent().parent().addClass('has-error');
                        $('#' + key).after('<p class="help-block">' + value + '</p>');
                    });
                } else {
                    window.location.href = base_url + index_page + "mis/backend/software";
                }
            }
        });
    });
</script>
@stop