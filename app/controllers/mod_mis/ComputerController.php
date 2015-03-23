<?php

/*
 *  @Auther : Adisorn Lamsombuth
 *  @Email : postyim@gmail.com
 *  @Website : esabay.com
 */

namespace App\Controllers;

/**
 * Description of MisController
 *
 * @author ComputerController
 */
class ComputerController extends \BaseController {

    public function __construct() {
        $this->beforeFilter('auth', array('except' => '/login'));
    }

    public function index() {
        $data = array(
            'title' => 'ระเบียนคอมพิวเตอร์',
            'breadcrumbs' => array(
                'ภาพรวมระบบ' => '',
                'ภาพรวมฝ่ายเทคโนโลยีสารเทศ' => 'mis',
                'ระเบียนคอมพิวเตอร์' => '#'
            ),
            'company' => \Company::lists('title', 'id')
        );

        return \View::make('mod_mis.computer.admin.index', $data);
    }

    public function listall() {
        $computer_item = \DB::table('computer_item')
                ->leftJoin('computer_user', 'computer_item.id', '=', 'computer_user.computer_id')
                ->leftJoin('users', 'users.id', '=', 'computer_user.user_id')
                ->join('computer_type', 'computer_item.type_id', '=', 'computer_type.id')
                ->join('company', 'computer_item.company_id', '=', 'company.id')
                ->select(array(
            'computer_item.id as id',
            'computer_item.id as item_id',
            'computer_item.serial_code as serial_code',
            'computer_item.title as title',
            \DB::raw('CONCAT(users.codes," ",users.firstname," ",users.lastname) as fullname'),
            'computer_item.ip_address as ip_address',
            'company.title as company',
            'computer_item.disabled as disabled'
        ));

        if (\Input::has('company_id')) {
            $computer_item->where('computer_item.company_id', \Input::get('company_id'));
        }

        if (\Input::has('disabled')) {
            $computer_item->where('computer_item.disabled', \Input::get('disabled'));
        }

        $link = '<div class="dropdown">';
        $link .= '<a class="dropdown-toggle" data-toggle="dropdown" href="javascript:;"><span class="fa fa-pencil-square-o"></span ></a>';
        $link .= '<ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">';
        $link .= '<li><a href="{{\URL::to("mis/computer/edit/$id")}}" title="แก้ไขรายการ"><i class="fa fa-pencil-square-o"></i> แก้ไขรายการ</a></li>';
        $link .= '<li><a href="{{\URL::to("mis/computer/export/$id")}}" title="พิมพ์ระเบียน" target="_blank"><i class="fa fa-print"></i> พิมพ์ระเบียน</a></li>';
        $link .= '<li><a href="javascript:;" rel="mis/computer/delete/{{$id}}" class="link_dialog delete" title="ลบรายการ"><i class="fa fa-trash"></i> ลบรายการ</a></li>';
        $link .= '</ul>';
        $link .= '</div>';

        return \Datatables::of($computer_item)
                        ->edit_column('id', $link)
                        ->edit_column('serial_code', function($result_obj) {
                            $str = '<a href="' . \URL::to('computer/view/' . $result_obj->item_id . '') . '" title="ดูรายละเอียด เลขระเบียน ' . $result_obj->serial_code . '">' . $result_obj->serial_code . '</a>';
                            return $str;
                        })
                        ->edit_column('disabled', '@if($disabled==0) <span class="label label-success">Active</span> @else <span class="label label-danger">Inactive</span> @endif')
                        ->make(true);
    }

    public function dialog() {
        $company = \Company::lists('title', 'id');
        $data = array(
            'company' => $company
        );
        return \View::make('mod_mis.computer.admin.dialog_add', $data);
    }

    public function add() {
        if (!\Request::isMethod('post')) {
            $data = array(
                'title' => 'เพิ่ม Computer',
                'breadcrumbs' => array(
                    'ภาพรวมระบบ' => '',
                    'ภาพรวมฝ่ายเทคโนโลยีสารเทศ' => 'mis',
                    'ระเบียนคอมพิวเตอร์' => 'mis/computer',
                    'เพิ่ม Computer' => '#'
                ),
                'company' => \Company::lists('title', 'id'),
                'place' => \Place::lists('title', 'id')
            );

            //return \View::make('mod_mis.computer.admin.add', $data);
            return \View::make('mod_mis.computer.admin.add_wizard', $data);
        } else {
            $rules = array(
                'company_id' => 'required',
                'title' => 'required'
            );
            $validator = \Validator::make(\Input::all(), $rules);
            if ($validator->fails()) {
                return \Response::json(array(
                            'error' => array(
                                'status' => FALSE,
                                'message' => $validator->errors()->toArray()
                            ), 400));
            } else {

                $computer_item = new \ComputerItem();
                $computer_item->company_id = \Input::get('company_id');
                $computer_item->type_id = \Input::get('type_id');
                $computer_item->serial_code = trim(\Input::get('serial_code'));
                $computer_item->access_no = trim(\Input::get('access_no'));
                $computer_item->title = trim(\Input::get('title'));
                $computer_item->ip_address = trim(\Input::get('ip_address'));
                $computer_item->mac_lan = trim(\Input::get('mac_lan'));
                $computer_item->mac_wireless = trim(\Input::get('mac_wireless'));
                $computer_item->locations = \Input::get('locations');
                $computer_item->register_date = trim(\Input::get('register_date'));
                $computer_item->disabled = (\Input::has('disabled') ? 0 : 1);
                $computer_item->created_user = \Auth::user()->id;
                $computer_item->save();
                $computer_id = $computer_item->id;
                if (\Input::has('user_item')) {
                    $computer_item->users()->sync(\Input::get('user_item'));
                    foreach (\Input::get('user_item') as $uitem) {
                        $hsware_item = \User::find($uitem);
                        $hsware_item->computer_status = 1;
                        $hsware_item->save();
                    }
                }
                if (\Input::get('hsware_item') > 0) {
                    $computer_item->hsware()->sync(\Input::get('hsware_item'));
                    foreach (\Input::get('hsware_item') as $item) {
                        $hsware_item = \HswareItem::find($item);
                        $hsware_item->status = 1;
                        $hsware_item->save();

                        $hslog = new \HswareComputerLog();
                        $hslog->hsware_id = $item;
                        $hslog->computer_id = $computer_id;
                        $hslog->created_user = \Auth::user()->id;
                        $hslog->save();
                    }
                }
                return \Response::json(array(
                            'error' => array(
                                'status' => TRUE,
                                'message' => NULL
                            ), 200));
            }
        }
    }

    public function add_wizard() {
        if (!\Request::isMethod('post')) {
            $data = array(
                'title' => 'เพิ่ม Computer',
                'breadcrumbs' => array(
                    'ภาพรวมระบบ' => '',
                    'ภาพรวมฝ่ายเทคโนโลยีสารเทศ' => 'mis',
                    'ระเบียนคอมพิวเตอร์' => 'mis/computer',
                    'เพิ่ม Computer' => '#'
                ),
                'company' => \Company::lists('title', 'id'),
                'place' => \Place::lists('title', 'id')
            );

            return \View::make('mod_mis.computer.admin.add_wizard', $data);
        } else {
            $rules = array(
                'company_id' => 'required',
                'title' => 'required'
            );
            $validator = \Validator::make(\Input::all(), $rules);
            if ($validator->fails()) {
                return \Response::json(array(
                            'error' => array(
                                'status' => FALSE,
                                'message' => $validator->errors()->toArray()
                            ), 400));
            } else {
                $model = \Input::get('model_id');
                $sub_model = \Input::get('sub_model');
                $warranty_date = \Input::get('warranty_date');
                $spec_value_1 = \Input::get('spec_value_1');
                $spec_value_2 = \Input::get('spec_value_2');
                $spec_value_3 = \Input::get('spec_value_3');
                $spec_value_4 = \Input::get('spec_value_4');
                $spec_value_5 = \Input::get('spec_value_5');
                $spec_value_12 = \Input::get('spec_value_12');
                $spec_value_13 = \Input::get('spec_value_13');
                $spec_value_18 = \Input::get('spec_value_18');
                $spec_value_19 = \Input::get('spec_value_19');
                $spec_value_20 = \Input::get('spec_value_20');
                $spec_value_28 = \Input::get('spec_value_28');
                $spec_value_29 = \Input::get('spec_value_29');

                $computer_item = new \ComputerItem();
                $computer_item->company_id = \Input::get('company_id');
                $computer_item->type_id = \Input::get('type_id');
                $computer_item->serial_code = trim(\Input::get('serial_code'));
                $computer_item->access_no = trim(\Input::get('access_no'));
                $computer_item->title = trim(\Input::get('title'));
                $computer_item->ip_address = trim(\Input::get('ip_address'));
                $computer_item->mac_lan = trim(\Input::get('mac_lan'));
                $computer_item->mac_wireless = trim(\Input::get('mac_wireless'));
                $computer_item->locations = \Input::get('locations');
                $computer_item->register_date = trim(\Input::get('register_date'));
                $computer_item->disabled = (\Input::has('disabled') ? 0 : 1);
                $computer_item->created_user = \Auth::user()->id;
                $computer_item->save();
                $computer_id = $computer_item->id;

                if (\Input::has('user_item')) {
                    $computer_item->users()->sync(\Input::get('user_item'));
                    foreach (\Input::get('user_item') as $uitem) {
                        $hsware_item = \User::find($uitem);
                        $hsware_item->computer_status = 1;
                        $hsware_item->save();
                    }
                }

                if (isset($model[2][0])) {
                    $hsware_item2 = new \HswareItem();
                    $hsware_item2->group_id = 2;
                    $hsware_item2->company_id = \Input::get('company_id');
                    $hsware_item2->model_id = (isset($model[2][0]) ? $model[2][0] : 0);
                    $hsware_item2->sub_model = (isset($sub_model[2][0]) ? $sub_model[2][0] : 0);
                    $hsware_item2->spec_value_1 = (isset($spec_value_1[2][0]) ? $spec_value_1[2][0] : NULL);
                    $hsware_item2->spec_value_2 = (isset($spec_value_2[2][0]) ? $spec_value_2[2][0] : NULL);
                    $hsware_item2->spec_value_3 = (isset($spec_value_3[2][0]) ? $spec_value_3[2][0] : NULL);
                    $hsware_item2->spec_value_28 = (isset($spec_value_28[2][0]) ? $spec_value_28[2][0] : NULL);
                    $hsware_item2->locations = trim(\Input::get('locations'));
                    $hsware_item2->register_date = trim(\Input::get('register_date'));
                    $hsware_item2->warranty_date = (isset($warranty_date[2][0]) != '' ? trim($warranty_date[2][0]) : NULL);
                    $hsware_item2->status = 1;
                    $hsware_item2->disabled = 0;
                    $hsware_item2->created_user = \Auth::user()->id;
                    $hsware_item2->save();
                    $hsware_id2 = $hsware_item2->id;
                    $hs_com2 = new \ComputerHsware();
                    $hs_com2->computer_id = $computer_id;
                    $hs_com2->hsware_id = $hsware_id2;
                    $hs_com2->save();
                    $hslog2 = new \HswareComputerLog();
                    $hslog2->hsware_id = $hsware_id2;
                    $hslog2->computer_id = $computer_id;
                    $hslog2->created_user = \Auth::user()->id;
                    $hslog2->save();
                }

                if (isset($model[8][0])) {
                    $hsware_item8 = new \HswareItem();
                    $hsware_item8->group_id = 8;
                    $hsware_item8->company_id = \Input::get('company_id');
                    $hsware_item8->model_id = (isset($model[8][0]) ? $model[8][0] : 0);
                    $hsware_item8->sub_model = (isset($sub_model[8][0]) ? $sub_model[8][0] : 0);
                    $hsware_item8->spec_value_12 = (isset($spec_value_12[8][0]) ? $spec_value_12[8][0] : NULL);
                    $hsware_item8->spec_value_13 = (isset($spec_value_13[8][0]) ? $spec_value_13[8][0] : NULL);
                    $hsware_item8->spec_value_29 = (isset($spec_value_29[8][0]) ? $spec_value_29[8][0] : NULL);
                    $hsware_item8->locations = trim(\Input::get('locations'));
                    $hsware_item8->register_date = trim(\Input::get('register_date'));
                    $hsware_item8->warranty_date = (isset($warranty_date[8][0]) != '' ? trim($warranty_date[8][0]) : NULL);
                    $hsware_item8->status = 1;
                    $hsware_item8->disabled = 0;
                    $hsware_item8->created_user = \Auth::user()->id;
                    $hsware_item8->save();
                    $hsware_id8 = $hsware_item8->id;
                    $hs_com8 = new \ComputerHsware();
                    $hs_com8->computer_id = $computer_id;
                    $hs_com8->hsware_id = $hsware_id8;
                    $hs_com8->save();
                    $hslog8 = new \HswareComputerLog();
                    $hslog8->hsware_id = $hsware_id8;
                    $hslog8->computer_id = $computer_id;
                    $hslog8->created_user = \Auth::user()->id;
                    $hslog8->save();
                }

                if (isset($model[22][0])) {
                    $hsware_item22 = new \HswareItem();
                    $hsware_item22->group_id = 22;
                    $hsware_item22->company_id = \Input::get('company_id');
                    $hsware_item22->model_id = (isset($model[22][0]) ? $model[22][0] : 0);
                    $hsware_item22->spec_value_4 = (isset($spec_value_4[22][0]) ? $spec_value_4[22][0] : NULL);
                    $hsware_item22->locations = trim(\Input::get('locations'));
                    $hsware_item22->register_date = trim(\Input::get('register_date'));
                    $hsware_item22->warranty_date = (isset($warranty_date[22][0]) != '' ? trim($warranty_date[22][0]) : NULL);
                    $hsware_item22->status = 1;
                    $hsware_item22->disabled = 0;
                    $hsware_item22->created_user = \Auth::user()->id;
                    $hsware_item22->save();
                    $hsware_id22 = $hsware_item22->id;
                    $hs_com22 = new \ComputerHsware();
                    $hs_com22->computer_id = $computer_id;
                    $hs_com22->hsware_id = $hsware_id22;
                    $hs_com22->save();
                    $hslog22 = new \HswareComputerLog();
                    $hslog22->hsware_id = $hsware_id22;
                    $hslog22->computer_id = $computer_id;
                    $hslog22->created_user = \Auth::user()->id;
                    $hslog22->save();
                }

                if (isset($model[3][0])) {
                    $hsware_item3 = new \HswareItem();
                    $hsware_item3->group_id = 3;
                    $hsware_item3->company_id = \Input::get('company_id');
                    $hsware_item3->model_id = (isset($model[3][0]) ? $model[3][0] : 0);
                    $hsware_item3->spec_value_2 = (isset($spec_value_2[3][0]) ? $spec_value_2[3][0] : NULL);
                    $hsware_item3->spec_value_4 = (isset($spec_value_4[3][0]) ? $spec_value_4[3][0] : NULL);
                    $hsware_item3->spec_value_5 = (isset($spec_value_5[3][0]) ? $spec_value_5[3][0] : NULL);
                    $hsware_item3->locations = trim(\Input::get('locations'));
                    $hsware_item3->register_date = trim(\Input::get('register_date'));
                    $hsware_item3->warranty_date = (isset($warranty_date[3][0]) != '' ? trim($warranty_date[3][0]) : NULL);
                    $hsware_item3->status = 1;
                    $hsware_item3->disabled = 0;
                    $hsware_item3->created_user = \Auth::user()->id;
                    $hsware_item3->save();
                    $hsware_id3 = $hsware_item3->id;
                    $hs_com3 = new \ComputerHsware();
                    $hs_com3->computer_id = $computer_id;
                    $hs_com3->hsware_id = $hsware_id3;
                    $hs_com3->save();
                    $hslog3 = new \HswareComputerLog();
                    $hslog3->hsware_id = $hsware_id3;
                    $hslog3->computer_id = $computer_id;
                    $hslog3->created_user = \Auth::user()->id;
                    $hslog3->save();
                }

                if (isset($model[3][1])) {
                    $hsware_item31 = new \HswareItem();
                    $hsware_item31->group_id = 3;
                    $hsware_item31->company_id = \Input::get('company_id');
                    $hsware_item31->model_id = (isset($model[3][1]) ? $model[3][1] : 0);
                    $hsware_item31->spec_value_2 = (isset($spec_value_2[3][1]) ? $spec_value_2[3][1] : NULL);
                    $hsware_item31->spec_value_4 = (isset($spec_value_4[3][1]) ? $spec_value_4[3][1] : NULL);
                    $hsware_item31->spec_value_5 = (isset($spec_value_5[3][1]) ? $spec_value_5[3][1] : NULL);
                    $hsware_item31->locations = trim(\Input::get('locations'));
                    $hsware_item31->register_date = trim(\Input::get('register_date'));
                    $hsware_item31->warranty_date = (isset($warranty_date[3][0]) != '' ? trim($warranty_date[3][0]) : NULL);
                    $hsware_item31->status = 1;
                    $hsware_item31->disabled = 0;
                    $hsware_item31->created_user = \Auth::user()->id;
                    $hsware_item31->save();
                    $hsware_id31 = $hsware_item31->id;
                    $hs_com31 = new \ComputerHsware();
                    $hs_com31->computer_id = $computer_id;
                    $hs_com31->hsware_id = $hsware_id31;
                    $hs_com31->save();
                    $hslog31 = new \HswareComputerLog();
                    $hslog31->hsware_id = $hsware_id31;
                    $hslog31->computer_id = $computer_id;
                    $hslog31->created_user = \Auth::user()->id;
                    $hslog31->save();
                }

                if (isset($model[14][0])) {
                    $hsware_item14 = new \HswareItem();
                    $hsware_item14->group_id = 14;
                    $hsware_item14->company_id = \Input::get('company_id');
                    $hsware_item14->model_id = (isset($model[14][0]) ? $model[14][0] : 0);
                    $hsware_item14->sub_model = (isset($sub_model[14][0]) ? $sub_model[14][0] : 0);
                    $hsware_item14->spec_value_19 = (isset($spec_value_19[14][0]) ? $spec_value_19[14][0] : NULL);
                    $hsware_item14->spec_value_20 = (isset($spec_value_20[14][0]) ? $spec_value_20[14][0] : NULL);
                    $hsware_item14->locations = trim(\Input::get('locations'));
                    $hsware_item14->register_date = trim(\Input::get('register_date'));
                    $hsware_item14->warranty_date = (isset($warranty_date[14][0]) != '' ? trim($warranty_date[14][0]) : NULL);
                    $hsware_item14->status = 1;
                    $hsware_item14->disabled = 0;
                    $hsware_item14->created_user = \Auth::user()->id;
                    $hsware_item14->save();
                    $hsware_id14 = $hsware_item14->id;
                    $hs_com14 = new \ComputerHsware();
                    $hs_com14->computer_id = $computer_id;
                    $hs_com14->hsware_id = $hsware_id14;
                    $hs_com14->save();
                    $hslog14 = new \HswareComputerLog();
                    $hslog14->hsware_id = $hsware_id14;
                    $hslog14->computer_id = $computer_id;
                    $hslog14->created_user = \Auth::user()->id;
                    $hslog14->save();
                }

                if (isset($model[13][0])) {
                    $hsware_item13 = new \HswareItem();
                    $hsware_item13->group_id = 13;
                    $hsware_item13->company_id = \Input::get('company_id');
                    $hsware_item13->model_id = (isset($model[13][0]) ? $model[13][0] : 0);
                    $hsware_item13->spec_value_18 = (isset($spec_value_18[13][0]) ? $spec_value_18[13][0] : NULL);
                    $hsware_item13->locations = trim(\Input::get('locations'));
                    $hsware_item13->register_date = trim(\Input::get('register_date'));
                    $hsware_item13->warranty_date = (isset($warranty_date[13][0]) != '' ? trim($warranty_date[13][0]) : NULL);
                    $hsware_item13->status = 1;
                    $hsware_item13->disabled = 0;
                    $hsware_item13->created_user = \Auth::user()->id;
                    $hsware_item13->save();
                    $hsware_id13 = $hsware_item13->id;
                    $hs_com13 = new \ComputerHsware();
                    $hs_com13->computer_id = $computer_id;
                    $hs_com13->hsware_id = $hsware_id13;
                    $hs_com13->save();
                    $hslog13 = new \HswareComputerLog();
                    $hslog13->hsware_id = $hsware_id13;
                    $hslog13->computer_id = $computer_id;
                    $hslog13->created_user = \Auth::user()->id;
                    $hslog13->save();
                }

                return \Response::json(array(
                            'error' => array(
                                'status' => TRUE,
                                'message' => NULL
                            ), 200));
            }
        }
    }

    public function edit($param) {
        if (!\Request::isMethod('post')) {
            $item = \DB::table('computer_item')
                    ->leftJoin('computer_user', 'computer_item.id', '=', 'computer_user.computer_id')
                    ->leftJoin('users', 'users.id', '=', 'computer_user.user_id')
                    ->leftJoin('position_item', 'position_item.id', '=', 'users.position_id')
                    ->where('computer_item.id', $param)
                    ->select(array(
                        'computer_item.id as id',
                        'computer_item.title as title',
                        'computer_item.company_id as company_id',
                        'computer_item.serial_code as serial_code',
                        'computer_item.access_no as access_no',
                        'computer_item.type_id as type_id',
                        'computer_item.locations as locations',
                        'computer_item.ip_address as ip_address',
                        'computer_item.mac_lan as mac_lan',
                        'computer_item.mac_wireless as mac_wireless',
                        'computer_item.register_date as register_date',
                        'computer_item.disabled as disabled',
                        \DB::raw('CONCAT(users.firstname," ",users.lastname) as fullname'),
                        'position_item.title as position'
                    ))
                    ->first();
            $data = array(
                'title' => 'แก่ไข Computer ' . $item->title,
                'breadcrumbs' => array(
                    'ภาพรวมระบบ' => '',
                    'ภาพรวมฝ่ายเทคโนโลยีสารเทศ' => 'mis',
                    'ระเบียนคอมพิวเตอร์' => 'mis/computer',
                    'แก่ไข Computer ' . $item->title => '#'
                ),
                'item' => $item,
                'company' => \Company::lists('title', 'id'),
                'place' => \Place::lists('title', 'id')
            );

            return \View::make('mod_mis.computer.admin.edit', $data);
        } else {
            $rules = array(
                'company_id' => 'required',
                'title' => 'required',
                'ip_address' => 'ip'
            );
            $validator = \Validator::make(\Input::all(), $rules);
            if ($validator->fails()) {
                return \Response::json(array(
                            'error' => array(
                                'status' => FALSE,
                                'message' => $validator->errors()->toArray()
                            ), 400));
            } else {
                $computer_item = \ComputerItem::find($param);
                $computer_item->company_id = \Input::get('company_id');
                $computer_item->type_id = \Input::get('type_id');
                $computer_item->serial_code = trim(\Input::get('serial_code'));
                $computer_item->access_no = trim(\Input::get('access_no'));
                $computer_item->ip_address = trim(\Input::get('ip_address'));
                $computer_item->title = trim(\Input::get('title'));
                $computer_item->ip_address = trim(\Input::get('ip_address'));
                $computer_item->mac_lan = trim(\Input::get('mac_lan'));
                $computer_item->mac_wireless = trim(\Input::get('mac_wireless'));
                $computer_item->locations = \Input::get('locations');
                $computer_item->register_date = trim(\Input::get('register_date'));
                $computer_item->disabled = (\Input::has('disabled') ? 0 : 1);
                $computer_item->updated_user = \Auth::user()->id;
                $computer_item->save();
                $computer_id = $computer_item->id;
                if (\Input::has('user_item')) {
                    \DB::table('users')
                            ->join('computer_user', 'users.id', '=', 'computer_user.user_id')
                            ->where('computer_user.computer_id', $param)
                            ->update(array('users.computer_status' => 0));
                    $computer_item->users()->sync(\Input::get('user_item'));
                    foreach (\Input::get('user_item') as $uitem) {
                        $hsware_item = \User::find($uitem);
                        $hsware_item->computer_status = 1;
                        $hsware_item->save();
                    }
                }
                if (\Input::get('hsware_item') > 0) {
                    \DB::table('hsware_item')
                            ->join('computer_hsware', 'hsware_item.id', '=', 'computer_hsware.hsware_id')
                            ->where('computer_hsware.computer_id', $param)
                            ->update(array('hsware_item.status' => 0));
                    $computer_item->hsware()->sync(\Input::get('hsware_item'));
                    foreach (\Input::get('hsware_item') as $item) {
                        $hsware_item = \HswareItem::find($item);
                        $hsware_item->status = 1;
                        $hsware_item->save();

                        $hslog = new \HswareComputerLog();
                        $hslog->hsware_id = $item;
                        $hslog->computer_id = $computer_id;
                        $hslog->updated_user = \Auth::user()->id;
                        $hslog->save();
                    }
                }
                return \Response::json(array(
                            'error' => array(
                                'status' => TRUE,
                                'message' => NULL
                            ), 200));
            }
        }
    }

    public function delete($param) {
        $computer_item = \ComputerItem::find($param);
        \DB::table('users')
                ->join('computer_user', 'users.id', '=', 'computer_user.user_id')
                ->where('computer_user.computer_id', $param)
                ->update(array('users.computer_status' => 0));
        $computer_item->users()->restore();

        \DB::table('hsware_item')
                ->join('computer_hsware', 'hsware_item.id', '=', 'computer_hsware.hsware_id')
                ->where('computer_hsware.computer_id', $param)
                ->update(array('hsware_item.status' => 0));
        $computer_item->hsware()->restore();
        $computer_item->delete();
        return \Response::json(array(
                    'error' => array(
                        'status' => true,
                        'message' => 'ลบรายการสำเร็จ',
                        'redirect' => 'mis/backend'
                    ), 200));
    }

    public function export($param) {
        $item = \DB::table('computer_item')
                ->leftJoin('computer_user', 'computer_item.id', '=', 'computer_user.computer_id')
                ->leftJoin('users', 'users.id', '=', 'computer_user.user_id')
                ->leftJoin('position_item', 'position_item.id', '=', 'users.position_id')
                ->leftJoin('company', 'computer_item.company_id', '=', 'company.id')
                ->leftJoin('place', 'computer_item.locations', '=', 'place.id')
                ->where('computer_item.id', $param)
                ->select(array(
                    'computer_item.id as id',
                    'computer_item.title as title',
                    'company.title as company',
                    'computer_item.company_id as company_id',
                    'computer_item.serial_code as serial_code',
                    'computer_item.access_no as access_no',
                    'computer_item.type_id as type_id',
                    'computer_item.locations as locations',
                    'computer_item.ip_address as ip_address',
                    'computer_item.mac_lan as mac_lan',
                    'computer_item.mac_wireless as mac_wireless',
                    'computer_item.register_date as register_date',
                    'place.title as place',
                    'position_item.title as position',
                    'computer_item.disabled as disabled',
                    \DB::raw('CONCAT(users.firstname," ",users.lastname) as fullname'),
                    'position_item.title as position'
                ))
                ->first();
        $data = array(
            'item' => $item
        );
        if ($item->company_id == 1) {
            return \View::make('mod_mis.computer.admin.word_export_arf', $data);
        } else {
            return \View::make('mod_mis.computer.admin.word_export_att', $data);
        }
    }

}
