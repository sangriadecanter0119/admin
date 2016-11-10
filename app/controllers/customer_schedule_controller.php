<?php
class CustomerScheduleController extends AppController
{

 public $name = 'CustomerSchedule';
 public $uses = array('CustomerMst');
 public $layout = 'cust_indivisual_info_main_tab';
 public $components = array('Auth');
 public $helpers = array('common');

 function index()
 {
 	$id = $this->Session->read('customer_id');
 	$data = $this->CustomerMst->findById($id);
    $this->set(	"data",$data);

    //新郎新婦の名前をセット
    $this->set(	"broom",($data['CustomerMstView']['prm_lastname_flg'] == 0 ? $data['CustomerMstView']['grmls_kj'] : $data['CustomerMstView']['brdls_kj']).$data['CustomerMstView']['grmfs_kj'] );
    $this->set(	"bride",$data['CustomerMst']['brdfs_kj']);

 	$this->set("menu_customers","");
 	$this->set("menu_customer","current");
 	$this->set("menu_fund","");

 	$this->set("sub_menu_customer_info","");
 	$this->set("sub_menu_customer_meeting","");
 	$this->set("sub_menu_customer_wedding_reserve","");
 	$this->set("sub_menu_customer_contact","");
 	//$this->set("sub_menu_customer_schedule","current");
 	$this->set("sub_menu_customer_estimate","");

 	$this->set("sub_title","スケジュール");
 	$this->set("user",$this->Auth->user());
 }

}
?>