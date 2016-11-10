<?php
set_time_limit(1300);
class SalesManagementController extends AppController
{

 public $name = 'SalesManagement';
 public $uses = array('CustomerMst','EnvMst','SalesManagementService','ContractTrnView');
 public $layout = 'fund_management_main_tab';
 public $components = array('Auth','RequestHandler');
 public $helpers = array('Html','common','Javascript');

 /**
  *
  *
  */
 function index()
 {
 	$wedding_dt = null;

 	if (!empty($this->data)) {
 		/* フィルタ条件変更*/
 		$wedding_dt = $this->data['GoodsMstView']['wedding_planned_dt'];
 		$this->Session->write("filter_wedding_dt",$wedding_dt);
 	}
 	/* デフォルト値 :処理年月に挙式予定の成約の顧客を表示 */
 	else{
 		if($this->Session->read("filter_wedding_dt") == null){
 			$this->Session->write("filter_wedding_dt",date("Y-m"));
 			$wedding_dt = date("Y-m");
 		}else{
 			$wedding_dt = $this->Session->read("filter_wedding_dt");
 		}
 	}

 	//売上一覧を取得
 	$data = $this->SalesManagementService->GetSalesList($wedding_dt);
 	$this->set('data',$data);

 	/* 成約年月一覧を取得 */
 	$this->set("wedding_dt_list",$this->ContractTrnView->getGroupOfWeddingMonthInInvoiced());
 	/* フィルタ条件をVIEWで保持する */
 	$this->set("wedding_dt" ,$this->Session->read("filter_wedding_dt"));

    $this->set("menu_customers","");
 	$this->set("menu_customer","disable");
 	$this->set("menu_fund","current");

 	$this->set("sub_menu_bank","");
 	$this->set("sub_menu_sales","current");
 	$this->set("sub_menu_fund","");
 	$this->set("sub_menu_remittance","");
 	$this->set("sub_menu_payment","");

 	$this->set("sub_title","売上一覧");
 	$this->set("user",$this->Auth->user());
 }

 /**
  *
  * 売上一覧表をEXCEL出力する
  */
 function export(){

 	//売上一覧を取得
 	$data = $this->SalesManagementService->GetSalesList($this->Session->read("filter_wedding_dt"));
 	$this->set('data',$data);

 	$temp_filename = "sales_template.xlsx";
 	$save_filename = mb_convert_encoding("売上", "SJIS", "AUTO").$this->Session->read("filter_wedding_dt").".xlsx";

 	$this->layout = false;
 	$this->set( "sheet_name", $this->Session->read("filter_wedding_dt") );
 	$this->set( "filename", $save_filename );
 	$this->set( "template_file", $temp_filename);
 	$this->render("excel");
 }
}
?>