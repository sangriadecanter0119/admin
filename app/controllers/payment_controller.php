<?php
class PaymentController extends AppController
{

 public $name = 'Payment';
 public $uses = array('EstimateDtlTrnView','CustomerMst','ContractTrn','PaymentService','ContractTrnView');
 public $layout = 'fund_management_main_tab';
 public $components = array('Auth','RequestHandler');
 public $helpers = array('Html','common','Javascript');

 /**
  *
  * 仲介業者用が各ベンダーに支払う一覧表を表示
  */
 function index()
 {
 	 /* フォームからフィルタ条件を送信された場合(POST) */
 	 if (!empty($this->data)) {
 	 	$this->Session->write("filter_wedding_dt",$this->data['GoodsMstView']['wedding_planned_dt']);
 	 }
	 /* デフォルト値 :処理年月に挙式予定の成約の顧客を表示 */
	 else if($this->Session->read("filter_wedding_dt") == null){
	 	$this->Session->write("filter_wedding_dt",date("Y-m"));
	}

 	$data = $this->PaymentService->getEachCustomerPaymentByWeddingDate($this->Session->read("filter_wedding_dt"));
 	$this->set('data',$data);

 	/* 商品カテゴリ順に並び替えて再取得 */
 	$data_by_category = $this->PaymentService->getEachCustomerPaymentByWeddingDate($this->Session->read("filter_wedding_dt"),'category');
 	$this->set('data_by_category',$data_by_category);

 	/* 成約年月一覧を取得 */
 	$this->set("wedding_dt_list",$this->ContractTrnView->getGroupOfWeddingMonthInInvoiced());
 	/* フィルタ条件をVIEWで保持する */
 	$this->set("wedding_dt" ,$this->Session->read("filter_wedding_dt"));

 	$this->set("menu_customers","");
 	$this->set("menu_customer","disable");
 	$this->set("menu_fund","current");

 	$this->set("sub_menu_bank","");
 	$this->set("sub_menu_sales","");
 	$this->set("sub_menu_fund","");
 	$this->set("sub_menu_remittance","");
 	$this->set("sub_menu_payment","current");
 	$this->set("sub_menu_vendor_sales","");

 	$this->set("sub_title","現地支払い");
 	$this->set("user",$this->Auth->user());
 }

 /**
  *
  * 支払一覧表をEXCEL出力する
  */
 function export(){

    $data = $this->PaymentService->getEachCustomerPaymentByWeddingDate($this->Session->read("filter_wedding_dt"));
 	$this->set('data',$data);

 	/* 商品カテゴリ順に並び替えて再取得 */
 	$data_by_category = $this->PaymentService->getEachCustomerPaymentByWeddingDate($this->Session->read("filter_wedding_dt"),'category');
 	$this->set('data_by_category',$data_by_category);

   $temp_filename = "payment_template.xlsx";
   $save_filename = mb_convert_encoding("支払", "SJIS", "AUTO").$this->Session->read("filter_wedding_dt").".xlsx";

   $this->layout = false;
   $this->set( "sheet_name", "HI振込用" );
   $this->set( "filename", $save_filename );
   $this->set( "template_file", $temp_filename);
   $this->render("excel");
 }


 function editEstimate(){

     if (!$this->RequestHandler->isAjax()){ die('Not found');}
 	 configure::write('debug', 0);
	 $this->layout = '';
	 $this->autoRender =false;

      for($i=0;$i < count($this->data['EstimateDtlTrn']);$i++){
            //入金フラグ要素がない場合はチェックを外す
 			if(array_key_exists('money_received_flg',$this->data['EstimateDtlTrn'][$i])==false){ $this->data['EstimateDtlTrn'][$i]['money_received_flg'] = 0 ;}
 	  }
 	  $ret = $this->PaymentService->updatePayment($this->data['EstimateDtlTrn'],$this->Auth->user('username'));
 	return json_encode($ret);
 	}

 function updateSetGoodsEstimate(){

 		if (!$this->RequestHandler->isAjax()){ die('Not found');}
 		configure::write('debug', 0);
 		$this->layout = '';
 		$this->autoRender =false;

 		$ret = $this->PaymentService->updateSetGoodsOriginalPrice();
 		return json_encode($ret);
 	}
}
?>