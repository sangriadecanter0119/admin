<?php
class CustomersWeddingsReserveController extends AppController
{

 public $name = 'CustomersWeddingsReserve';
 public $uses = null;
 public $layout = 'cust_list_info_main_tab';
 public $components = array('Auth','RequestHandler');
 public $helpers = array('Html','common','Javascript');

 function index()
 {
 	$this->set("menu_customers","current");
 	$this->set("menu_customer","disable");
 	$this->set("menu_fund","");

 	$this->set("sub_menu_customers_list","");
 	$this->set("sub_menu_customers_company_contact","");
 	$this->set("sub_menu_customers_schedules","");
 	$this->set("sub_menu_customers_contract_list","");

 	$this->set("sub_title","挙式予約状況");
 	$this->set("user",$this->Auth->user());
 }
}

?>
