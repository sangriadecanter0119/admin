<?php
class SystemMasterController extends AppController
{
 public $name = null;
 public $uses = null;
 public $layout = 'edit_mode';
 public $components = array('Auth','RequestHandler');
 public $helpers = array('Html','common','Javascript');


  function index()
  {
 	$this->set("menu_customers","");
 	$this->set("menu_customer","disable");
 	$this->set("menu_fund","");

 	$this->set("sub_title","システム管理");
 	$this->set("user",$this->Auth->user());
 }

 function showSystemInfo()
 {
   $this->layout = null;
 }
}
?>