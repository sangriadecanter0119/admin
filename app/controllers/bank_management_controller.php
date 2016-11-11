<?php
class BankManagementController extends AppController
{

 public $name = 'BankManagement';
 public $uses = array('CustomerMst','EnvMst','CreditTrnView','CreditTypeMst','CreditService','CreditTrn','EstimateTrnView');
 public $layout = 'fund_management_main_tab';
 public $components = array('Auth','RequestHandler');
 public $helpers = array('Html','common','Javascript');

 /**
  *
  *
  */
 function index()
 {
 	$credit_dt = null;

 	if (!empty($this->data)) {
 		/* フィルタ条件変更*/
 		$credit_dt = $this->data['CreditTrn']['credit_dt'];
 		$this->Session->write("filter_credit_dt",$credit_dt);
 		$search = array("SUBSTR(credit_dt,1,7)"=>$credit_dt);
 	}
 	/* デフォルト値  */
 	else{
 		if($this->Session->read("filter_credit_dt") == null){
 			$this->Session->write("filter_credit_dt",date("Y-m"));
 			$credit_dt = $this->Session->read("filter_credit_dt");
 		}else{
 			$credit_dt = $this->Session->read("filter_credit_dt");
 		}
 	}

 	//入金一覧を取得
 	$data = $this->CreditService->GetCreditInfoByCreditMonth($credit_dt);
 	$this->set('data',$data);

 	/* 入金年月一覧を取得 */
 	$this->set("credit_dt_list",$this->CreditService->getGroupOfCreditMonth());
 	/* フィルタ条件をVIEWで保持する */
 	$this->set("credit_dt" ,$this->Session->read("filter_credit_dt"));

    $this->set("menu_customers","");
 	$this->set("menu_customer","disable");
 	$this->set("menu_fund","current");

 	$this->set("sub_menu_bank","current");
 	$this->set("sub_menu_sales","");
 	$this->set("sub_menu_fund","");
 	$this->set("sub_menu_remittance","");
 	$this->set("sub_menu_payment","");
 	$this->set("sub_menu_vendor_sales","");

    $this->set("sub_title","入金管理一覧");
    $this->set("user",$this->Auth->user());
 }

 /**
  * 入金情報の登録
  */
 function addCreditInfo($duplicate_acceptted=null)
 {
 	if(!empty($this->data))
 	{
 		$this->layout = '';
 		$this->autoRender =false;
 		configure::write('debug', 0);

 		$ret = $this->CreditService->register($this->data['CreditTrn'],$this->Auth->user('username'),$duplicate_acceptted);
 		if($ret['result']==false){	return json_encode($ret); }
 		return json_encode(array('result'=>true,'message'=>'処理完了しました。'));

 	}else{

 		$this->set("menu_customers","");
 		$this->set("menu_customer","disable");
 		$this->set("menu_fund","current");

 		$this->set("sub_title","入金情報追加");
 		$this->set("user",$this->Auth->user());
 		$this->layout = 'edit_mode';
 	}
 }

 /**
  * 入金情報の更新・削除
  */
 function editCreditInfo($id=null){

 	if(!empty($this->data)){

 		$this->layout = '';
 		$this->autoRender =false;
 		configure::write('debug', 0);

 		 /* 削除 */
        if(strtoupper($this->params['form']['submit'])  ==  "DELETE"){

           $ret = $this->CreditService->delete($this->data['CreditTrn']['id'],$this->data['CreditTrn']['customer_id'],$this->data['CreditTrn']['status_id'],$this->Auth->user('username'));
           if($ret['result']==false){	return json_encode($ret); }
        }
        /* 更新 */
        else if(strtoupper($this->params['form']['submit'])  ==  "UPDATE"){

         $this->data['CreditTrn']['uchikin_exception_flg']  = isset($this->data['CreditTrn']['uchikin_exception_flg']) ? 1:0;
         $this->data['CreditTrn']['upd_nm'] = $this->Auth->user('username');
         $this->data['CreditTrn']['upd_dt'] = date('Y-m-d H:i:s');

 	     if($this->CreditTrn->save($this->data,false,array('uchikin_exception_flg','upd_nm','upd_dt'))==false){
 	     	return json_encode(array('result'=>false,'message'=>"更新に失敗しました。",'reason'=>$this->CreditTrn->getDbo()->error."[".date('Y-m-d H:i:s')."]"));
 	     }
        /* 異常パラメーター */
        }else{
      	    return json_encode(array('result'=>false,'message'=>"処理が失敗しました。",'reason'=>"予期しないコード[".$this->params['form']['submit']."]です。 "));
        }
         return json_encode(array('result'=>true,'message'=>'処理完了しました。'));
 	}else{

 		$this->set("credit_type_list", $this->CreditTypeMst->find('all'));
 		$this->set("data",$this->CreditTrnView->findById($id));
 		$this->set("menu_customers","");
 		$this->set("menu_customer","disable");
 		$this->set("menu_fund","current");

 		$this->set("sub_title","入金詳細");
 		$this->set("user",$this->Auth->user());
 		$this->layout = 'edit_mode';
 	}
 }

 /**
  *
  * [AJAX]入金CSVファイルアップロード画面を表示する
  */
 function fileUploadForm() {
 	if (!$this->RequestHandler->isAjax()){ $this->cakeError('error404'); }
 	configure::write('debug', 0);
 	$this->layout = '';
 }

 /**
  * 入金ファイルの取り込み
  */
 function uploadCreditFile()
 {
 	$this->layout = "";

    if (is_uploaded_file($this->data['ImgForm']['ImgFile']['tmp_name']) && end(explode(".",$this->data['ImgForm']['ImgFile']['name'])) == "csv") {

		$result = $this->CreditService->getCsvFileInfo($this->data['ImgForm']['ImgFile']['tmp_name']);
		if($result["isSuccess"]){
			$this->set("msg",json_encode(array('data'=>array('isSuccess'=>"true" ,
					'message'=>"ファイル取り込みに成功しました。",
					'credit_list'=>$result["data"],
					'credit_type_list'=>$this->CreditService->getCreditTypeList()))));
		}else{
			$this->set("msg",json_encode(array('data'=>array('isSuccess'=>"false", 'message'=> $result["message"]))));
		}
	}else{
		$this->set("msg",json_encode(array('data'=>array('isSuccess'=>"false", 'message'=> "ファイルの種類が違います。(CSVファイル)　　ファイルサイズの上限は128Mです。"))));
	}
 }

 /**
  *
  * EXCEL出力する
  */
 function export(){

 	$credit_dt = $this->Session->read("filter_credit_dt");

 	//入金一覧を取得
 	$data = $this->CreditService->GetCreditInfoByCreditMonth($credit_dt);
 	$this->set('data',$data);

 	$temp_filename = "deposit_template.xlsx";
 	$save_filename = mb_convert_encoding("入金", "SJIS", "AUTO").$credit_dt.".xlsx";

 	$this->layout = false;
 	$this->set( "sheet_name", $credit_dt );
 	$this->set( "filename", $save_filename );
 	$this->set( "template_file", $temp_filename);
 	$this->render("excel");
 }

 /**
  * 入金状況一覧表示
  */
 function creditInfoForm($customer_id)
 {
 	$this->layout = '';
 	configure::write('debug', 0);

 	//入金金額合計
 	$this->set('credit',$this->CreditService->getCreditAmount($customer_id));
 	//請求金額
 	$invoice = $this->EstimateTrnView->getTotalAmountByCustomer($customer_id);
 	$this->set('invoice',$invoice);
 	//入金一覧を取得
 	$data = $this->CreditService->GetCreditInfoOfCustomer($customer_id);
 	$this->set('data',$data);
 }
}
?>
