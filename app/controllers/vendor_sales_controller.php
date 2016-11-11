<?php
set_time_limit(1300);
class VendorSalesController extends AppController
{

 public $name = 'VendorSales';
 public $uses = array('EnvMst','VendorSalesService');
 public $layout = 'fund_management_main_tab';
 public $components = array('Auth','RequestHandler');
 public $helpers = array('Html','common','Javascript');

 function index()
 {
 	$start_wedding_dt = null;
 	$end_wedding_dt = null;

 	if (!empty($this->data)) {
 		/* フィルタ条件変更*/
 		$start_wedding_dt = $this->data['GoodsMstView']['start_wedding_planned_dt'];
 		$end_wedding_dt = $this->data['GoodsMstView']['end_wedding_planned_dt'];
 		$this->Session->write("filter_start_vn_wedding_dt",$start_wedding_dt);
 		$this->Session->write("filter_end_vn_wedding_dt",$end_wedding_dt);
 	}
 	/* デフォルト値 :処理年月に挙式予定の成約の顧客を表示 */
 	else{
 		if($this->Session->read("filter_start_vn_wedding_dt") == null){
 			$this->Session->write("filter_start_vn_wedding_dt",date("Y-m"));
 			$start_wedding_dt = date("Y-m");
 		}else{
 			$start_wedding_dt = $this->Session->read("filter_start_vn_wedding_dt");
 		}

 		if($this->Session->read("filter_end_vn_wedding_dt") == null){
 			$this->Session->write("filter_end_vn_wedding_dt",date("Y-m"));
 			$end_wedding_dt = date("Y-m");
 		}else{
 			$end_wedding_dt = $this->Session->read("filter_end_vn_wedding_dt");
 		}
 	}

 	//ベンダー売上要約一覧を取得
 	$header = $this->VendorSalesService->GetVendorSalesList($start_wedding_dt,$end_wedding_dt);
 	$this->set('header',$header);

 	//ベンダー売上詳細を取得
 	$detail = $this->VendorSalesService->GetVendorSalesDetailList($start_wedding_dt,$end_wedding_dt);
 	$this->set('detail',$detail);

 	/* フィルタ条件をVIEWで保持する */
 	$this->set("start_wedding_dt" ,$this->Session->read("filter_start_vn_wedding_dt"));
 	$this->set("end_wedding_dt" ,$this->Session->read("filter_end_vn_wedding_dt"));

    $this->set("menu_customers","");
 	$this->set("menu_customer","disable");
 	$this->set("menu_fund","current");

 	$this->set("sub_menu_bank","");
 	$this->set("sub_menu_sales","");
 	$this->set("sub_menu_fund","");
 	$this->set("sub_menu_remittance","");
 	$this->set("sub_menu_payment","");
 	$this->set("sub_menu_vendor_sales","current");

 	$this->set("sub_title","ベンダー売上一覧");
 	$this->set("user",$this->Auth->user());
 }

 /**
  *
  * 売上一覧表をEXCEL出力する
  */
 function export(){

 	//ベンダー売上要約一覧を取得
 	$header = $this->VendorSalesService->GetVendorSalesList($this->Session->read("filter_start_vn_wedding_dt"),$this->Session->read("filter_end_vn_wedding_dt"));
 	$this->set('header',$header);

 	//ベンダー売上詳細を取得
 	$detail = $this->VendorSalesService->GetVendorSalesDetailList($this->Session->read("filter_start_vn_wedding_dt"),$this->Session->read("filter_end_vn_wedding_dt"));
 	$this->set('detail',$detail);

 	$temp_filename = "vendor_sales_template.xlsx";
 	$save_filename = mb_convert_encoding("ベンダー売上", "SJIS", "AUTO").$this->Session->read("filter_start_vn_wedding_dt").'_'.$this->Session->read("filter_end_vn_wedding_dt").".xlsx";

 	$this->layout = false;
 	$this->set( "start_date", $this->Session->read("filter_start_vn_wedding_dt"));
 	$this->set( "end_date"  , $this->Session->read("filter_end_vn_wedding_dt") );
 	$this->set( "sheet_name", $this->Session->read("filter_start_vn_wedding_dt").'_'.$this->Session->read("filter_end_vn_wedding_dt") );
 	$this->set( "filename", $save_filename );
 	$this->set( "template_file", $temp_filename);
 	$this->render("excel");
 }
}
?>