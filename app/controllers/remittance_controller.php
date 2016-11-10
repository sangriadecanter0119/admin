<?php
set_time_limit(1300);
class RemittanceController extends AppController
{
 public $name = 'Remittance';
 public $uses = array('EstimateDtlTrnView','ContractTrnView','RemittanceService','RemittanceTrn','RemittanceTrnView',
 		              'CustomerMst','PaymentKbnMst','EnvMst','EstimateService');
 public $layout = 'fund_management_main_tab';
 public $components = array('Auth','RequestHandler');
 public $helpers = array('Html','common','Javascript');

 /**
  *
  * 送金一覧表を表示
  */
 function index($remittance_rate=0,$cost_rate=0)
 {
     $wedding_dt = null;

 	 if (!empty($this->data)) {
 	    /* フィルタ条件変更*/
 	 	if(array_key_exists("RateUpdate", $this->data) == false){
 	 		$wedding_dt = $this->data['GoodsMstView']['wedding_planned_dt'];
 	 	    $this->Session->write("filter_wedding_dt",$wedding_dt);
 	 	/* 為替レート変更 */
 	 	}else{
 	 		$this->layout = '';
 	        $this->autoRender =false;
 	        configure::write('debug', 0);

 	 		$ret = $this->RemittanceService->UpdateAllRate($this->data['RateUpdate'],$this->Auth->user('username'));

 	 		if($ret['result']==false){	return json_encode($ret); }
      	    return json_encode(array('result'=>true,'message'=>'処理完了しました。'));
 	 	}
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

 	//送金一覧を取得
 	$data = $this->RemittanceTrnView->findAllByWeddingDateInInvoiced($wedding_dt);
 	$this->set('data',$data);

 	$this->set('remittance_rate',$remittance_rate);
 	$this->set('cost_rate',$cost_rate);

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
 	$this->set("sub_menu_remittance","current");
 	$this->set("sub_menu_payment","");

 	$this->set("sub_title","送金一覧");
 	$this->set("user",$this->Auth->user());
 }

 /**
  *
  * 個別顧客の送金一覧を表示及び更新
  * @param $estimate_id
  */
 function editRemittance($estimate_id=null)
 {
 	$customer_id = $this->Session->read('customer_id');

    if(!empty($this->data))
 	{
 		$this->layout = '';
 	    $this->autoRender =false;
 	    configure::write('debug', 0);

      	 /* 送金画面内容の更新 */
      	 $ret = $this->EstimateService->updateForRemittance($this->data,$this->Auth->user('username'));
      	 if($ret['result']==false){	return json_encode($ret); }
     	 return json_encode(array('result'=>true,'message'=>'処理完了しました。'));

 	}else{

 	  //見積明細を取得
 	  $data = $this->EstimateDtlTrnView->find('all',array('conditions'=>array('estimate_id'=>$estimate_id),
 	                                                      'order'=>'no'));

 	  $this->set("payment_kbn_list", $this->PaymentKbnMst->find('all'));
      //環境設定情報を取得
   	  $this->set("env_data", $this->EnvMst->findById(1));
 	  //新郎新婦の名前をセット
      $this->set(	"broom",$data[0]['EstimateDtlTrnView']['grmls_kj'].$data[0]['EstimateDtlTrnView']['grmfs_kj']);
      $this->set(	"bride",$data[0]['EstimateDtlTrnView']['brdfs_kj']);

 	  $this->set("data",$data);
 	  $this->set("menu_customers","");
 	  $this->set("menu_customer","disable");
 	  $this->set("menu_fund","current");

 	  $this->set("sub_title","送金内容詳細");
 	  $this->set("user",$this->Auth->user());
 	  $this->layout = 'edit_mode';
 	}
 }

 /**
  * 【暫定機能】顧客コードの一括変換
  */
 /*
 function updateCustomerCode(){

  	if(!empty($this->data))
 	{
 		$this->layout = '';
 		$this->autoRender =false;
 		configure::write('debug', 0);

 		 $tr = ClassRegistry::init('TransactionManager');
	     $tr->begin();

	     for($i=0; $i < count($this->data['CustomerMst']);$i++){
 	    	$ret = $this->CustomerMst->updateCustomerCodeById($this->data['CustomerMst'][$i],$this->Auth->user('username'));
 		    if($ret['result']==false){	return json_encode($ret); }
	     }
 		$tr->commit();
 		return json_encode(array('result'=>true,'message'=>'処理完了しました。'));
 	}
 }
*/

 /**
  *
  * 送金一覧表をEXCEL出力する
  */
 function export(){

   //送金一覧を取得
   $data = $this->RemittanceTrnView->findAllByWeddingDateInInvoiced($this->Session->read("filter_wedding_dt"));
   $this->set('data',$data);

   $estiamte_ids = array();
   for($i=0;$i < count($data);$i++){
   	  $estiamte_ids[] = $data[$i]['RemittanceTrnView']['estimate_id'];
   }
   //見積明細を取得
   $estimate_data = $this->EstimateDtlTrnView->find('all',array('conditions'=>array('estimate_id'=>$estiamte_ids),
 	                                                                 'order'=>'estimate_id,no'));
   $this->set('estimate_data',$estimate_data);
   //支払区分リストを取得
   $this->set("payment_kbn_list", $this->PaymentKbnMst->find('all'));

   $temp_filename = "remittance_template.xlsx";
   $save_filename = mb_convert_encoding("送金", "SJIS", "AUTO").date('Ym',strtotime($data[0]['RemittanceTrnView']['wedding_dt'])).".xlsx";

   $this->layout = false;
   $this->set( "sheet_name", "送金全体像" );
   $this->set( "filename", $save_filename );
   $this->set( "template_file", $temp_filename);
   $this->render("excel");
 }

}
?>