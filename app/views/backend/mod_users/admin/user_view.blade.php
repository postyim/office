@extends('backend.layouts.master')

@section('style')
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
    <div class="col-md-4 col-sm-6 col-xs-12">
        <div class="text-center">
            <img src="http://lorempixel.com/200/200/people/9/" class="avatar img-circle img-thumbnail" alt="avatar">
        </div>
    </div>
    <div class="col-md-8 col-sm-6 col-xs-12 personal-info">
        <form class="form-horizontal" role="form">
            <h3>ข้อมูลส่วนตัว</h3>
            <div class="form-group">
                {{Form::label('firstname', 'ชื่อ-นามสกุล:', array('class' => 'col-lg-3 control-label'))}}
                <div class="col-lg-9">                   
                    <p class="form-control-static">{{$item->firstname}} {{$item->lastname}}</p>   
                </div>
            </div>
            <div class="form-group">
                {{Form::label('nickname', 'ชื่อเล่น:', array('class' => 'col-lg-3 control-label'))}}
                <div class="col-lg-9">
                    <p class="form-control-static">{{$item->nickname}}</p>           
                </div>
            </div>
            <div class="form-group">
                {{Form::label('idcard', 'รหัสไปรษณีย์:', array('class' => 'col-lg-3 control-label'))}}
                <div class="col-lg-9">
                    <p class="form-control-static">{{$item->idcard}}</p> 
                </div>
            </div>
            <div class="form-group">
                {{Form::label('birthday', 'วันเดือนปีเกิด:', array('class' => 'col-lg-3 control-label'))}}
                <div class="col-lg-9">
                    <p class="form-control-static">{{date("d F Y", strtotime($item->birthday))}}</p> 
                </div>
            </div>
            <div class="form-group">
                {{Form::label('mobile', 'เบอร์ติดต่อ:', array('class' => 'col-lg-3 control-label'))}}
                <div class="col-lg-9">
                    <p class="form-control-static">{{$item->mobile}}</p> 
                </div>
            </div>
            <div class="form-group">
                {{Form::label('email', 'อีเมล์:', array('class' => 'col-lg-3 control-label'))}}
                <div class="col-lg-6">
                    <p class="form-control-static">{{$item->email}}</p> 
                </div>
            </div>
            <div class="form-group">
                {{Form::label('address', 'ที่อยู่:', array('class' => 'col-lg-3 control-label'))}}
                <div class="col-lg-8">
                    <p class="form-control-static">{{$address}}</p> 
                </div>
            </div>            
            <div class="form-group">
                <label class="col-md-3 control-label"></label>
                <div class="col-md-8">
                    <a href="{{URL::to('users/backend/edit/'.$item->id)}}" class="btn btn-primary btn-lg" role="button">แก้ไขข้อมูล</a>
                </div>
            </div>
        </form>
    </div>
</div>
@stop

@section('script')
@stop

@section('script_code')
<script type="text/javascript">
    
</script>
@stop