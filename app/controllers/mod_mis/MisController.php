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
 * @author Administrator
 */
class MisController extends \BaseController {

    public function __construct() {
        $this->beforeFilter('auth', array('except' => '/login'));
    }

    public function index() {
        $check = \User::find((\Auth::check() ? \Auth::user()->id : 0));
        $data = array(
            'title' => 'ภาพรวมฝ่ายเทคโนโลยีสารเทศ',
            'breadcrumbs' => array(
                'ภาพรวมระบบ' => '',
                'ภาพรวมฝ่ายเทคโนโลยีสารเทศ' => '#'
            ),
            'compouter_count' => \ComputerItem::where('disabled', '=', 0)->where('type_id', '=', 1)->count(),
            'notebook_count' => \ComputerItem::where('disabled', '=', 0)->where('type_id', '=', 2)->count(),
            'printer_count' => \HswareItem::where('disabled', '=', 0)->where('group_id', '=', 24)->count(),
            'hsware_count' => \HswareItem::where('disabled', '=', 0)->count(),
            'users_count' => \User::where('disabled', '=', 0)->count(),
            'supplier_count' => \Supplier::where('disabled', '=', 0)->count(),
            'hsware_group_count' => \HswareGroup::where('disabled', '=', 0)->count(),
            'hsware_model_count' => \HswareModel::where('disabled', '=', 0)->count(),
            'repairing_count' => \RepairingItem::where('disabled', '=', 0)->count(),
            'software_count' => \SoftwareItem::where('disabled', '=', 0)->count(),
            'ma_count' => \MaItem::where('disabled', '=', 0)->count(),
            'spare_count' => \HswareItem::where('spare', 1)->where('disabled', 0)->count()
        );
        if ($check->is('administrator')) {
            return \View::make('mod_mis.home.admin.index', $data);
        } elseif ($check->is('admin')) {
            return \View::make('mod_mis.home.admin.index', $data);
        } elseif ($check->is('employee')) {
            return \View::make('mod_mis.home.employee.index', $data);
        } elseif ($check->is('mis')) {
            return \View::make('mod_mis.home.mis.index', $data);
        } elseif ($check->is('hr')) {
            return \View::make('mod_mis.home.hr.index', $data);
        } else {
            return \View::make('mod_mis.home.shared.index', $data);
        }
    }

    function send_mail() {
        
    }

}
