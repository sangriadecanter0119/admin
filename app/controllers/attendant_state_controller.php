<?php
class AttendantStateController extends AppController
{
 public $name = 'AttendantState';
 public $uses = array('AttendantStateService');
 public $layout = 'cust_list_info_main_tab';
 public $components = array('Auth','RequestHandler');
 public $helpers = array('Html','common','Javascript');

 /**
  *
  *
  */
 function index($showing_dt=null)
 {
 	if(empty($showing_dt)) {  $showing_dt =  date("Y-m") ; }

 	$this->set('estimate_data',$this->AttendantStateService->GetAllAttendantStateOfContract($showing_dt));
 	//基準月の翌月の挙式データ
 	$this->set('invoice_data',$this->AttendantStateService->GetAllAttendantStateOfWedding(date('Y-m', strtotime($showing_dt."-1" . ' +1 month'))));

 	/* 年月一覧を取得 */
 	$this->set("wedding_dt_list",$this->AttendantStateService->getGroupOfWeddingMonth());
  	$this->set("showing_dt" ,$showing_dt);

  	//メニューとサブメニューのアクティブ化
  	$this->set("menu_customers","current");
  	$this->set("menu_customer","disable");
  	$this->set("menu_fund","");

 	$this->set("sub_menu_customers_list","");
 	$this->set("sub_menu_customers_company_contact","");
 	$this->set("sub_menu_customers_wedding_reserve","");
 	$this->set("sub_menu_customers_schedules","");
 	$this->set("sub_menu_customers_by_each_attendant_list","");
 	$this->set("sub_menu_customers_contract_list","");
 	$this->set("sub_menu_attendant_state","current");
 	$this->set("sub_menu_wedding_reservations","");

    $this->set("sub_title","担当者状況一覧");
    $this->set("user",$this->Auth->user());
 }
}
?>