@extends('layouts.master')

@section('style')
{{HTML::style('assets/advanced-datatable/media/css/demo_page.css')}}
{{HTML::style('assets/advanced-datatable/media/css/demo_table.css')}}
{{HTML::style('assets/data-tables/DT_bootstrap.css')}}
{{HTML::style('assets/datatables/extensions/TableTools/css/dataTables.tableTools.min.css')}}
@stop

@section('content')
@if(isset($breadcrumbs))
<div class="row">
    <div class="col-lg-12">
        <ul class="breadcrumb">
            @foreach ($breadcrumbs as $key => $val)
            @if ($val === reset($breadcrumbs))
            <li><a href="{{URL::to($val)}}"><i class="fa fa-home"></i> {{$key}}</a></li>
            @elseif ($val === end($breadcrumbs))
            <li class="active">{{$key}}</li>
            @else
            <li><a href="{{URL::to($val)}}"> {{$key}}</a></li>
            @endif
            @endforeach
        </ul>
    </div>
</div>
@endif
<div class="row">
    <div class="col-lg-12">
        <div class="panel">
            <div class="panel-body">
                <div class="pull-left">
                    <div class="btn-group">
                        <a href="javascript:;" rel="mis/software/add" class="btn btn-primary link_dialog" title="เพิ่ม Software" role="button"><i class="fa fa-plus"></i> เพิ่ม Software (F8)</a>
                        <a href="{{\URL::to('mis/software/group')}}" class="btn btn-primary" title="กลุ่ม Software ติดตั้ง" role="button"><i class="fa fa-list"></i> กลุ่ม Software ติดตั้ง</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<div class="row">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                {{$title}}
            </header>
            <div class="panel-body">
                <div class="adv-table">
                    <table id="software-list" class="table table-striped table-bordered"></table>
                </div>
            </div>
        </section>
    </div>
</div>
@stop

@section('script')
{{HTML::script('assets/datatables/1.10.4/js/jquery.dataTables.min.js')}}
{{HTML::script('assets/datatables/1.10.4/js/dataTables.bootstrap.js')}}
{{HTML::script('assets/data-tables/DT_bootstrap.js')}}
{{HTML::script('assets/datatables/extensions/TableTools/js/dataTables.tableTools.min.js')}}
@stop

@section('script_code')
<script type="text/javascript">
    $(function () {
        $('.dropdown-toggle').dropdown();
        $("#software-list").dataTable({
            "processing": true,
            "serverSide": true,
            "pageLength": 25,
            "ajax": base_url + index_page + "mis/software/listall",
            "columnDefs": [{
                    "targets": "_all",
                    "defaultContent": ""
                }],
            "columns": [
                {"data": "id", "width": "2%", "sClass": "text-center", "orderable": false, "searchable": false},
                {"data": "title", "title": "รายการ", "width": "60%", "orderable": false, "searchable": true},
                {"data": "version", "title": "Version", "width": "10%", "orderable": false, "searchable": true},
                {"data": "bit_os", "title": "Bit", "width": "5%", "orderable": false, "searchable": true},
                {"data": "free", "title": "Free", "width": "8%", "sClass": "text-center", "orderable": true, "searchable": true},
                {"data": "disabled", "title": "สถานะ", "width": "8%", "sClass": "text-center", "orderable": true, "searchable": true}
            ],
            "dom": 'T<"clear">lfrtip',
            "tableTools": {
                "sSwfPath": "../assets/datatables/extensions/TableTools/swf/copy_csv_xls_pdf.swf",
                "aButtons": [
                    {
                        "sExtends": "copy",
                        "mColumns": [1, 2, 3, 4]
                    },
                    {
                        "sExtends": "xls",
                        "mColumns": [1, 2, 3, 4],
                        "sFileName": "export_hardware-software_" + $.now() + ".csv"
                    }
                ]
            }
        });
    });
</script>
@stop