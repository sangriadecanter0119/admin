<?php
class ToDoSchedulesController extends AppController
{

 public $name = 'ToDoSchedules';
 public $uses = null;
 public $layout = 'calendar';
 public $components = array('Auth');

 function index()
 {
 	$this->set("menu_customers","current");
 	$this->set("menu_customer","disable");
 	$this->set("menu_fund","");

 	$this->set("sub_menu_customers_list","");
 	$this->set("sub_menu_customers_company_contact","");
 	$this->set("sub_menu_customers_schedules","");
 	$this->set("sub_menu_customers_contract_list","");

 	$this->set("sub_title","業務スケジュール");
 	$this->set("user",$this->Auth->user());
 }
}
?>