<?php
class CustomersContractListController extends AppController
{

 public $name = 'CustomersContractList';
 public $uses = array('CustomerMst','ContractTrnView','CustomersContractListService','User');
 public $layout = 'cust_list_info_main_tab';
 public $components = array('Auth','RequestHandler');
 public $helpers = array('Html','common','Javascript');
 const SHOWING_MONTH_COUNT = 12;
 /**
  * 顧客挙式・約定一覧
  *
  */
 function index()
 {
  	$wedding_dt = null;
  	$attendant = "ALL";

 	if (!empty($this->data)) {
 		/* フィルタ条件変更*/
 		$wedding_dt = $this->data['GoodsMstView']['wedding_planned_dt'];
 		$attendant = $this->data['GoodsMstView']['attendant'];
 		$this->Session->write("filter_wedding_dt",$wedding_dt);
 		$this->Session->write("filter_attendant",$attendant);
 	}
 	/* デフォルト値 :表示年月に処理年の１月を初期表示とする */
 	else{
 		if($this->Session->read("filter_wedding_dt") == null){
 			$this->Session->write("filter_wedding_dt",date("Y")."-01");
 			$wedding_dt = $this->Session->read("filter_wedding_dt");
 		}else{
 			$wedding_dt = $this->Session->read("filter_wedding_dt");
 		}

 		if($this->Session->read("filter_attendant") == null){
 			$this->Session->write("filter_attendant","ALL");
 			$attendant = $this->Session->read("filter_attendant");
 		}else{
 			$attendant = $this->Session->read("filter_attendant");
 		}
 	}

 	$this->set('wedding_data',$this->CustomersContractListService->getCustomerListForWedding($wedding_dt,self::SHOWING_MONTH_COUNT ,$attendant));
 	$this->set('contract_data',$this->CustomersContractListService->getCustomerListForContract($wedding_dt,self::SHOWING_MONTH_COUNT ,$attendant));

 	/* 成約年月一覧を取得 */
 	$this->set("wedding_dt_list",$this->ContractTrnView->getGroupOfWeddingMonthInInvoiced());
 	/* 担当者一覧を取得 */
 	$this->set("attendant_list",$this->User->GetAllDisplayNameWithAll());
 	/* フィルタ条件をVIEWで保持する */
 	$this->set("wedding_dt" ,$this->Session->read("filter_wedding_dt"));
 	$this->set("attendant" ,$this->Session->read("filter_attendant"));
    /* 表示年月数*/
 	$this->set("showing_month_count",self::SHOWING_MONTH_COUNT );
    $this->set("menu_customers","current");
 	$this->set("menu_customer","disable");
 	$this->set("menu_fund","");

 	$this->set("sub_menu_customers_list","");
 	$this->set("sub_menu_customers_company_contact","");
 	$this->set("sub_menu_customers_wedding_reserve","");
 	$this->set("sub_menu_customers_schedules","");
 	$this->set("sub_menu_customers_by_each_attendant_list","");
 	$this->set("sub_menu_customers_contract_list","current");
 	$this->set("sub_menu_attendant_state","");
 	$this->set("sub_menu_wedding_reservations","");

    $this->set("sub_title","顧客挙式・約定一覧");
    $this->set("user",$this->Auth->user());
 }

 /**
  * 挙式・約定のEXCEL出力
  */
 function export(){

 	$wedding_dt = $this->Session->read("filter_wedding_dt");
 	$attendant = $this->Session->read("filter_attendant");

 	$this->set('wedding_data',$this->CustomersContractListService->getCustomerListForWedding($wedding_dt,self::SHOWING_MONTH_COUNT ,$attendant));
 	$this->set('contract_data',$this->CustomersContractListService->getCustomerListForContract($wedding_dt,self::SHOWING_MONTH_COUNT ,$attendant));

 	$this->set("wedding_dt" ,$wedding_dt);
 	$this->set("showing_month_count",self::SHOWING_MONTH_COUNT );

 	$save_filename = mb_convert_encoding("顧客一覧", "SJIS", "AUTO").date('Ymd').".xlsx";

 	$this->layout = false;
 	$this->set( "filename", $save_filename );
 	$this->set( "template_file", "customer_list_template.xlsx");
 	$this->render("excel");
 }


}
?>