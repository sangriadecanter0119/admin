<?php
set_time_limit(600);

class CustomerEstimateController extends AppController
{

 public $name = 'CustomerEstimate';
 public $uses = array('EstimateService','EstimateTrnView'   ,'GoodsMst','GoodsMstView','LatestGoodsMstView' ,'GoodsCtgMst','GoodsKbnMst','VendorMst','User',
                      'EstimateTrn','EstimateDtlTrn' ,'EstimateDtlTrnView','TemplateEstimateTrn'      ,'TemplateEstimateDtlTrnView','EnvMst','CreditService','ContractTrn',
                      'CustomerMst'    ,'CustomerMstView'   ,'CustomerStatusMst','ReportMst','PaymentKbnMst','Mail'   ,'ContactAddressTrnView','ContractTrnView');
 public $layout = 'cust_indivisual_info_main_tab';
 public $components = array('Auth','RequestHandler');
 public $helpers = array('Html','common','Javascript');

 /**
  *
  * 特定顧客の見積一覧画面を表示する
  */
 function index()
 {
 	$cutomer_id = $this->Session->read('customer_id');
 	//顧客IDで見積もりを検索
 	//$data = $this->EstimateTrnView->find('all',array('conditions'=>array('EstimateTrnView.customer_id'=>$cutomer_id),'order'=>array('reg_dt desc','upd_dt desc')));
 	$data = $this->EstimateService->getCustomerAllEstimateSummary($cutomer_id);
 	$this->set(	"data",$data);
 	//顧客情報を取得
    $customer = $this->CustomerMstView->findById($cutomer_id);
    $this->set("customer",$customer);
    $this->set(	"broom",($customer['CustomerMstView']['prm_lastname_flg'] == 0 ? $customer['CustomerMstView']['grmls_kj'] : $customer['CustomerMstView']['brdls_kj']).$customer['CustomerMstView']['grmfs_kj'] );
    $this->set(	"bride",$customer['CustomerMstView']['brdfs_kj']);

 	$this->set("menu_customers","");
 	$this->set("menu_customer","current");
 	$this->set("menu_fund","");

 	$this->set("sub_menu_customer_info","");
 	$this->set("sub_menu_customer_meeting","");
 	$this->set("sub_menu_customer_wedding_reserve","");
 	$this->set("sub_menu_customer_contact","");
 	//$this->set("sub_menu_customer_schedule","");
 	$this->set("sub_menu_customer_estimate","current");

 	$this->set("sub_title","見積もり");
 	$this->set("user",$this->Auth->user());
 }

 /**
  *
  * 見積を新規登録登録画面を表示し、新規データを作成する
  */
 function addEstimate()
 {
 	$customer_id = $this->Session->read('customer_id');

    if(!empty($this->data))
 	{
 		$tr = ClassRegistry::init('TransactionManager');
	    $tr->begin();

	    $this->layout = '';
 	    $this->autoRender =false;
 	    configure::write('debug', 0);

 	    //率を小数点に戻す
 		$this->data['EstimateTrn']['hawaii_tax_rate'] =  $this->data['EstimateTrn']['hawaii_tax_rate'] / 100;
 		$this->data['EstimateTrn']['discount_rate']   =  $this->data['EstimateTrn']['discount_rate'] / 100;
 		$this->data['EstimateTrn']['service_rate']    =  $this->data['EstimateTrn']['service_rate'] / 100;
 		$this->data['EstimateTrn']['discount_aw_share'] =  $this->data['EstimateTrn']['discount_aw_share'] / 100;
		$this->data['EstimateTrn']['discount_rw_share'] =  $this->data['EstimateTrn']['discount_rw_share'] / 100;

 		for($i=1;$i <= count($this->data['EstimateDtlTrn']);$i++){
 			$this->data['EstimateDtlTrn'][$i]['aw_share'] =  $this->data['EstimateDtlTrn'][$i]['aw_share'] / 100;
 			$this->data['EstimateDtlTrn'][$i]['rw_share'] =  $this->data['EstimateDtlTrn'][$i]['rw_share'] / 100;
 			$this->data['EstimateDtlTrn'][$i]['sales_exchange_rate'] =  trim($this->data['EstimateDtlTrn'][$i]['sales_exchange_rate']);
 			$this->data['EstimateDtlTrn'][$i]['cost_exchange_rate'] =  trim($this->data['EstimateDtlTrn'][$i]['cost_exchange_rate']);
 			$this->data['EstimateDtlTrn'][$i]['sales_price'] =  trim($this->data['EstimateDtlTrn'][$i]['sales_price']);
 			$this->data['EstimateDtlTrn'][$i]['sales_cost'] =  trim($this->data['EstimateDtlTrn'][$i]['sales_cost']);

 			/* 商品情報を取得して見積明細にセットする */
 			$goods = $this->GoodsMstView->findById($this->data['EstimateDtlTrn'][$i]['goods_id']);

 			$this->data['EstimateDtlTrn'][$i]['vendor_id'] 				 =  $goods['GoodsMstView']['vendor_id'];
 			$this->data['EstimateDtlTrn'][$i]['vendor_nm'] 				 =  $goods['GoodsMstView']['vendor_nm'];
 			$this->data['EstimateDtlTrn'][$i]['goods_ctg_nm'] 			 =  $goods['GoodsMstView']['goods_ctg_nm'];
 			$this->data['EstimateDtlTrn'][$i]['goods_kbn_nm'] 			 =  $goods['GoodsMstView']['goods_kbn_nm'];
 			$this->data['EstimateDtlTrn'][$i]['goods_cd'] 				 =  $goods['GoodsMstView']['goods_cd'];
 			$this->data['EstimateDtlTrn'][$i]['original_sales_goods_nm'] =  $goods['GoodsMstView']['goods_nm'];
 			$this->data['EstimateDtlTrn'][$i]['original_sales_price'] 	 =  $goods['GoodsMstView']['price'];
 			$this->data['EstimateDtlTrn'][$i]['original_sales_cost'] 	 =  $goods['GoodsMstView']['cost'];
 			$this->data['EstimateDtlTrn'][$i]['set_goods_kbn'] 		     =  $goods['GoodsMstView']['set_goods_kbn'];
 			$this->data['EstimateDtlTrn'][$i]['currency_kbn'] 			 =  $goods['GoodsMstView']['currency_kbn'];
 		}

 		/*** 1.仮登録  ***/
     	if(strtoupper($this->params['form']['submit'])  ==  "PUBLISH")
     	{
     		//見積新規仮登録
     	    $ret = $this->EstimateService->registerTemporally($this->data,$this->Auth->user('username'));
     	}
     	/*** 2.正式登録  ***/
     	else if(strtoupper($this->params['form']['submit'])  ==  "ADOPT")
     	{
     	   //見積新規正式登録
     	   $ret = $this->EstimateService->registerFormally($this->data,$customer_id,$this->Auth->user('username'));
     	}
     	/* 異常パラメーター */
        else{
         return json_encode(array('result'=>false,'message'=>"処理が失敗しました。",'reason'=>"予期しないコード[".$this->params['form']['submit']."]です。 "));
        }

 	    if($ret['result']==false){	return json_encode($ret); }
 	    $tr->commit();
        return json_encode(array('result'=>true,'message'=>'処理完了しました。'));

 	}else{

 	$this->layout = 'edit_mode';
    //顧客情報取得
    $customer = $this->CustomerMst->findById($customer_id);
 	$this->set("customer", $customer);

     /* 複数年度の商品マスタを取得する */
 	 //$years = $this->GoodsMst->find('all',array('fields'=>array('year'),'group'=>'year','order'=>'year asc'));
 	 //$this->Session->write('goods_mst_year',$years[0]['GoodsMst']['year']);
 	 //$this->set("years",$years);

 	 //お内金額取得
 	 $prepaid = $this->CreditService->getPrepaidAmount($customer_id);
 	 $this->set("prepaid_amount",$prepaid != null ? $prepaid['amount'] : "0");
 	 //$this->set("prepaid_dt",$prepaid != null ? $prepaid['credit_dt'] : "");

 	 //顧客ステータス
 	 $this->set("customer_status",$this->CustomerMst->getCustomerStatus($customer_id));

 	//商品情報取得
 	//$this->set("goods_list", $this->GoodsMstView->find('all'));
 	//商品分類情報を取得
 	$this->set("goods_ctg_list", $this->GoodsCtgMst->find('all'));
 	//商品区分情報を取得
 	$this->set("goods_kbn_list", $this->GoodsKbnMst->find('all'));
 	//支払区分情報を取得
 	$this->set("payment_kbn_list", $this->PaymentKbnMst->find('all'));
 	//環境設定情報を取得
 	$this->set("env_data", $this->EnvMst->findById(1));
 	//テンプレート情報を取得
 	$template_id = empty($this->params['url']['id']) ? 1 : $this->params['url']['id'];
 	$this->set("data", $this->TemplateEstimateDtlTrnView->find('all',array('conditions'=>array("template_estimate_id"=>$template_id),'order'=>array('no'))));

 	//新郎新婦の名前をセット
    $this->set(	"broom",($customer['CustomerMst']['prm_lastname_flg'] == 0 ? $customer['CustomerMst']['grmls_kj'] : $customer['CustomerMst']['brdls_kj']).$customer['CustomerMst']['grmfs_kj'] );
    $this->set(	"bride",$customer['CustomerMst']['brdfs_kj']);

 	$this->set("menu_customers","");
 	$this->set("menu_customer","current");
 	$this->set("menu_fund","");

 	$this->set("sub_title","見積追加");
 	$this->set("user",$this->Auth->user());
   }
 }


 /**
  *
  * 見積テンプレートフォーム
  * @param $type
  * @param $id
  */
 function templateForm($type=null,$id=null)
 {
 	$this->layout = '';
 	configure::write('debug', 0);

    if($type != null)
 	{
 		$this->autoRender = false;
 		$tr = ClassRegistry::init('TransactionManager');
	    $tr->begin();

	    if(strtoupper($type)=="CREATE" || strtoupper($type)=="UPDATE"){
	       //率を小数点に戻す
 		  $this->data['EstimateTrn']['hawaii_tax_rate'] =  $this->data['EstimateTrn']['hawaii_tax_rate'] / 100;
 		  $this->data['EstimateTrn']['discount_rate']   =  $this->data['EstimateTrn']['discount_rate'] / 100;
 		  $this->data['EstimateTrn']['service_rate']    =  $this->data['EstimateTrn']['service_rate'] / 100;
 		  $this->data['EstimateTrn']['discount_aw_share'] =  $this->data['EstimateTrn']['discount_aw_share'] / 100;
		  $this->data['EstimateTrn']['discount_rw_share'] =  $this->data['EstimateTrn']['discount_rw_share'] / 100;

 		  for($i=1;$i <= count($this->data['EstimateDtlTrn']);$i++){
 			$this->data['EstimateDtlTrn'][$i]['aw_share'] =  $this->data['EstimateDtlTrn'][$i]['aw_share'] / 100;
 			$this->data['EstimateDtlTrn'][$i]['rw_share'] =  $this->data['EstimateDtlTrn'][$i]['rw_share'] / 100;
 			$this->data['EstimateDtlTrn'][$i]['sales_exchange_rate'] =  trim($this->data['EstimateDtlTrn'][$i]['sales_exchange_rate']);
 			$this->data['EstimateDtlTrn'][$i]['cost_exchange_rate'] =  trim($this->data['EstimateDtlTrn'][$i]['cost_exchange_rate']);
 			//$this->data['EstimateDtlTrn'][$i]['sales_price'] =  trim($this->data['EstimateDtlTrn'][$i]['sales_price']);
 			//$this->data['EstimateDtlTrn'][$i]['sales_cost'] =  trim($this->data['EstimateDtlTrn'][$i]['sales_cost']);
 	 	  }
	    }

 	  	 switch(strtoupper($type)){

 	  	 	case "CREATE":
     	                   $ret = $this->EstimateService->registerTemplate($this->data,$this->Auth->user('username'));
 	  	 		           break;
 	  	 	case "UPDATE":
      		               $ret = $this->EstimateService->updateTemplate($id,$this->data,$this->Auth->user('username'));
 	  	 		           break;
 	  	 	case "DELETE":
      		               $ret = $this->EstimateService->deleteTemplate($id);
 	  	 		           break;
 	  	 	default:return json_encode(array('result'=>false,'message'=>"処理が失敗しました。",'reason'=>"予期しないタイプ[".$type."]です。 "));
 	  	 }

 	    if($ret['result']==false){	return json_encode($ret); }
 	    $tr->commit();
        return json_encode(array('result'=>true,'message'=>'処理完了しました。'));
 	}
 }

 /**
  * 顧客基本情報編集画面
  */
 function basicInfoEditForm()
 {
 	$this->layout = '';
 	configure::write('debug', 0);

 	$customer_id = $this->Session->read('customer_id');

    if(!empty($this->data))
 	{
 		$this->autoRender = false;

 		//0000-00-00対策
 		if(empty($this->data['CustomerMst']['first_visited_dt'])){ $this->data['CustomerMst']['first_visited_dt'] = null; }
 		if(empty($this->data['CustomerMst']['estimate_issued_dt'])){ $this->data['CustomerMst']['estimate_issued_dt'] = null; }
 		if(empty($this->data['CustomerMst']['contracting_dt'])){ $this->data['CustomerMst']['contracting_dt'] = null; }
 		if(empty($this->data['CustomerMst']['wedding_planned_dt'])){ $this->data['CustomerMst']['wedding_planned_dt'] = null; }

 		$ret = $this->EstimateService->updateCustomerBasicInfo($this->data,$this->Auth->user('username'));
 		if($ret['result']==false){	return json_encode($ret); }

 		return json_encode(array('result'=>true,'message'=>'処理完了しました。',
 				                 'status_nm'=>$ret['status_nm'],'status_id'=>$ret['status_id'],
 		                         'customer_cd'=>$ret['customer_cd'],'contracting_dt'=>$ret['contracting_dt'],));
 	}else{

 		//顧客情報取得
 		$customer = $this->CustomerMstView->findById($customer_id);
 		//担当者リスト
 		$this->set("attendant_list",$this->User->GetAllDisplayName());
 		$this->set("data",$customer);
 		$this->set("status_list",$this->CustomerStatusMst->find('all'));
 		$this->set("invoice_issued_dt",$this->EstimateService->getInvoiceIssuedDateByCustomer($customer_id));
 		$this->set("contracted_dt",$this->ContractTrn->getContractedDateByCustomer($customer_id));
 	}
 }

 /**
  *
  * 現在の見積もりは残して、新規見積を作成する(更新の度に履歴を残す)
  */
 function editEstimate($estimate_id=null)
 {
 	$customer_id = $this->Session->read('customer_id');

    if(!empty($this->data))
 	{
 		$tr = ClassRegistry::init('TransactionManager');
	    $tr->begin();

	    $this->layout = '';
 	    $this->autoRender =false;
 	    configure::write('debug', 0);

 	    //率を小数点に戻す
 		$this->data['EstimateTrn']['hawaii_tax_rate'] =  $this->data['EstimateTrn']['hawaii_tax_rate'] / 100;
 		$this->data['EstimateTrn']['discount_rate']   =  $this->data['EstimateTrn']['discount_rate'] / 100;
 		$this->data['EstimateTrn']['service_rate']    =  $this->data['EstimateTrn']['service_rate'] / 100;
 		$this->data['EstimateTrn']['discount_aw_share'] =  $this->data['EstimateTrn']['discount_aw_share'] / 100;
		$this->data['EstimateTrn']['discount_rw_share'] =  $this->data['EstimateTrn']['discount_rw_share'] / 100;

		//0000-00-00対策
		if(empty($this->data['EstimateTrn']['tts_rate_dt'])){ $this->data['EstimateTrn']['tts_rate_dt'] = null; }
 	    if(empty($this->data['EstimateTrn']['estimate_issued_dt'])){ $this->data['EstimateTrn']['estimate_issued_dt'] = null; }
 	    if(empty($this->data['EstimateTrn']['invoice_issued_dt'])){ $this->data['EstimateTrn']['invoice_issued_dt'] = null; }

 		for($i=1;$i <= count($this->data['EstimateDtlTrn']);$i++){
 			$this->data['EstimateDtlTrn'][$i]['aw_share'] =  $this->data['EstimateDtlTrn'][$i]['aw_share'] / 100;
 			$this->data['EstimateDtlTrn'][$i]['rw_share'] =  $this->data['EstimateDtlTrn'][$i]['rw_share'] / 100;
 			$this->data['EstimateDtlTrn'][$i]['sales_exchange_rate'] =  trim($this->data['EstimateDtlTrn'][$i]['sales_exchange_rate']);
 			$this->data['EstimateDtlTrn'][$i]['cost_exchange_rate'] =  trim($this->data['EstimateDtlTrn'][$i]['cost_exchange_rate']);
 			$this->data['EstimateDtlTrn'][$i]['sales_price'] =  trim($this->data['EstimateDtlTrn'][$i]['sales_price']);
 			$this->data['EstimateDtlTrn'][$i]['sales_cost'] =  trim($this->data['EstimateDtlTrn'][$i]['sales_cost']);
            //入金フラグ要素がない場合はチェックを外す
 			if(array_key_exists('money_received_flg',$this->data['EstimateDtlTrn'][$i])==false){ $this->data['EstimateDtlTrn'][$i]['money_received_flg'] = 0 ;}
 		}

    	/*** 1.削除  ***/
        if(strtoupper($this->params['form']['submit'])  ==  "DELETE")
        {
            //見積削除
            $ret = $this->EstimateService->deleteAll($this->data['EstimateTrn']['id'],$customer_id,$this->Auth->user('username'));
        }
 		/*** 2.通常更新  ***/
     	else if(strtoupper($this->params['form']['submit'])  ==  "NORMAL_UPDATE")
     	{
        	for($i=1;$i <= count($this->data['EstimateDtlTrn']);$i++){
 			  /* 商品情報を取得して見積明細にセットする */
 			  $goods = $this->GoodsMstView->findById($this->data['EstimateDtlTrn'][$i]['goods_id']);

 			  $this->data['EstimateDtlTrn'][$i]['vendor_id'] 				 =  $goods['GoodsMstView']['vendor_id'];
 			  $this->data['EstimateDtlTrn'][$i]['vendor_nm'] 				 =  $goods['GoodsMstView']['vendor_nm'];
 			  $this->data['EstimateDtlTrn'][$i]['goods_ctg_nm'] 			 =  $goods['GoodsMstView']['goods_ctg_nm'];
 			  $this->data['EstimateDtlTrn'][$i]['goods_kbn_nm'] 			 =  $goods['GoodsMstView']['goods_kbn_nm'];
 			  $this->data['EstimateDtlTrn'][$i]['goods_cd'] 				 =  $goods['GoodsMstView']['goods_cd'];
 			  $this->data['EstimateDtlTrn'][$i]['original_sales_goods_nm'] =  $goods['GoodsMstView']['goods_nm'];
 			  $this->data['EstimateDtlTrn'][$i]['original_sales_price'] 	 =  $goods['GoodsMstView']['price'];
 			  $this->data['EstimateDtlTrn'][$i]['original_sales_cost'] 	 =  $goods['GoodsMstView']['cost'];
 			  $this->data['EstimateDtlTrn'][$i]['set_goods_kbn'] 		     =  $goods['GoodsMstView']['set_goods_kbn'];
 			  $this->data['EstimateDtlTrn'][$i]['currency_kbn'] 			 =  $goods['GoodsMstView']['currency_kbn'];
 		    }
     	    $ret = $this->EstimateService->registerTemporally($this->data,$this->Auth->user('username'));
     	}
     	/*** 3.見積採用更新 ***/
     	else if(strtoupper($this->params['form']['submit'])  ==  "ADOPT_UPDATE")
     	{
            for($i=1;$i <= count($this->data['EstimateDtlTrn']);$i++){
 			   /* 商品情報を取得して見積明細にセットする */
 			   $goods = $this->GoodsMstView->findById($this->data['EstimateDtlTrn'][$i]['goods_id']);

 			  $this->data['EstimateDtlTrn'][$i]['vendor_id'] 				 =  $goods['GoodsMstView']['vendor_id'];
 			  $this->data['EstimateDtlTrn'][$i]['vendor_nm'] 				 =  $goods['GoodsMstView']['vendor_nm'];
 			  $this->data['EstimateDtlTrn'][$i]['goods_ctg_nm'] 			 =  $goods['GoodsMstView']['goods_ctg_nm'];
 			  $this->data['EstimateDtlTrn'][$i]['goods_kbn_nm'] 			 =  $goods['GoodsMstView']['goods_kbn_nm'];
 			  $this->data['EstimateDtlTrn'][$i]['goods_cd'] 				 =  $goods['GoodsMstView']['goods_cd'];
 			  $this->data['EstimateDtlTrn'][$i]['original_sales_goods_nm'] =  $goods['GoodsMstView']['goods_nm'];
 			  $this->data['EstimateDtlTrn'][$i]['original_sales_price'] 	 =  $goods['GoodsMstView']['price'];
 			  $this->data['EstimateDtlTrn'][$i]['original_sales_cost'] 	 =  $goods['GoodsMstView']['cost'];
 			  $this->data['EstimateDtlTrn'][$i]['set_goods_kbn'] 		     =  $goods['GoodsMstView']['set_goods_kbn'];
 			  $this->data['EstimateDtlTrn'][$i]['currency_kbn'] 			 =  $goods['GoodsMstView']['currency_kbn'];
 		    }
     	   $ret = $this->EstimateService->registerFormally($this->data,$customer_id,$this->Auth->user('username'));
     	}
     	/*** 4.採用済み見積もりの更新  ***/
     	else if(strtoupper($this->params['form']['submit'])  ==  "UPDATE")
     	{
     	      for($i=1;$i <= count($this->data['EstimateDtlTrn']);$i++){
     	        /* 新規追加された商品のみ設定する */
 			    if($this->data['EstimateDtlTrn'][$i]['id'] == null || $this->data['EstimateDtlTrn'][$i]['id'] == ""){
 			    /* 商品情報を取得して見積明細にセットする */
 			    $goods = $this->GoodsMstView->findById($this->data['EstimateDtlTrn'][$i]['goods_id']);

 			    $this->data['EstimateDtlTrn'][$i]['vendor_id'] 				 =  $goods['GoodsMstView']['vendor_id'];
 			    $this->data['EstimateDtlTrn'][$i]['vendor_nm'] 				 =  $goods['GoodsMstView']['vendor_nm'];
 			    $this->data['EstimateDtlTrn'][$i]['goods_ctg_nm'] 			 =  $goods['GoodsMstView']['goods_ctg_nm'];
 			    $this->data['EstimateDtlTrn'][$i]['goods_kbn_nm'] 			 =  $goods['GoodsMstView']['goods_kbn_nm'];
 			    $this->data['EstimateDtlTrn'][$i]['goods_cd'] 				 =  $goods['GoodsMstView']['goods_cd'];
 			    $this->data['EstimateDtlTrn'][$i]['original_sales_goods_nm']   =  $goods['GoodsMstView']['goods_nm'];
 			    $this->data['EstimateDtlTrn'][$i]['original_sales_price'] 	 =  $goods['GoodsMstView']['price'];
 			    $this->data['EstimateDtlTrn'][$i]['original_sales_cost'] 	     =  $goods['GoodsMstView']['cost'];
 			    $this->data['EstimateDtlTrn'][$i]['set_goods_kbn'] 		     =  $goods['GoodsMstView']['set_goods_kbn'];
 			    $this->data['EstimateDtlTrn'][$i]['currency_kbn'] 			 =  $goods['GoodsMstView']['currency_kbn'];
 			   }
 		     }
 		     $ret = $this->EstimateService->updateFormally($this->data,$this->data['EstimateTrn']['id'],$customer_id,$this->Auth->user('username'));
     	}
     	/* 異常パラメーター */
        else{
         return json_encode(array('result'=>false,'message'=>"処理が失敗しました。",'reason'=>"予期しないコード[".$this->params['form']['submit']."]です。 "));
        }

 	    if($ret['result']==false){	return json_encode($ret); }
 	    $tr->commit();
        return json_encode(array('result'=>true,'message'=>'処理完了しました。'));

 	}else{

 	//見積明細を取得
 	$data = $this->EstimateDtlTrnView->find('all',array('conditions'=>array('estimate_id'=>$estimate_id),
 	                                                    'order'=>'no'));

 	 /* 複数年度の商品マスタを取得する */
 	 //$years = $this->GoodsMst->find('all',array('fields'=>array('year'),'group'=>'year','order'=>'year asc'));
 	 //$this->Session->write('goods_mst_year',$years[0]['GoodsMst']['year']);
 	 //$this->set("years",$years);

 	//入金金額取得
 	$credit_amount = $this->CreditService->getCreditAmountWithoutUchikinException($customer_id);
 	$this->set("credit_amount",$credit_amount);
 	//$this->set("prepaid_dt",$prepaid != null ? $prepaid['credit_dt'] : "");

 	//顧客ステータス
 	$this->set("customer_status",$this->CustomerMst->getCustomerStatus($customer_id));

 	//商品分類情報を取得
 	$this->set("goods_ctg_list", $this->GoodsCtgMst->find('all'));
 	//支払区分情報を取得
 	$this->set("payment_kbn_list", $this->PaymentKbnMst->find('all'));
    //環境設定情報を取得
 	$this->set("env_data", $this->EnvMst->findById(1));
 	//新郎新婦の名前をセット
    $this->set("broom",($data[0]['EstimateDtlTrnView']['prm_lastname_flg'] == 0 ? $data[0]['EstimateDtlTrnView']['grmls_kj'] : $data[0]['EstimateDtlTrnView']['brdls_kj']).$data[0]['EstimateDtlTrnView']['grmfs_kj'] );
    $this->set("bride",$data[0]['EstimateDtlTrnView']['brdfs_kj']);

 	$this->set("data",$data);
 	$this->set("menu_customers","");
 	$this->set("menu_customer","current");
 	$this->set("menu_fund","");

 	$this->set("sub_title","見積編集");
 	$this->set("user",$this->Auth->user());
 	$this->layout = 'edit_mode';
   }
 }

 /**
  *
  * 見積もりの更新・削除処理画面を表示し、更新・削除処理を行う
  * ※使用不可
  * @param  $estimate_id
  */
 function _editEstimate($estimate_id=null)
 {
 	$customer_id = $this->Session->read('customer_id');

 	if(!empty($this->data))
 	{
 		$tr = ClassRegistry::init('TransactionManager');
	    $tr->begin();

	    $this->layout = '';
 	    $this->autoRender =false;
 	    configure::write('debug', 0);

 	    //率を小数点に戻す
 		$this->data['EstimateTrn']['hawaii_tax_rate']   =  $this->data['EstimateTrn']['hawaii_tax_rate'] / 100;
 		$this->data['EstimateTrn']['discount_rate']     =  $this->data['EstimateTrn']['discount_rate'] / 100;
 		$this->data['EstimateTrn']['service_rate']      =  $this->data['EstimateTrn']['service_rate'] / 100;
 		$this->data['EstimateTrn']['discount_aw_share'] =  $this->data['EstimateTrn']['discount_aw_share'] / 100;
		$this->data['EstimateTrn']['discount_rw_share'] =  $this->data['EstimateTrn']['discount_rw_share'] / 100;

 		for($i=1;$i <= count($this->data['EstimateDtlTrn']);$i++){
 			$this->data['EstimateDtlTrn'][$i]['sales_exchange_rate'] =  trim($this->data['EstimateDtlTrn'][$i]['sales_exchange_rate']);
 			$this->data['EstimateDtlTrn'][$i]['cost_exchange_rate'] =  trim($this->data['EstimateDtlTrn'][$i]['cost_exchange_rate']);
 			$this->data['EstimateDtlTrn'][$i]['sales_price'] =  trim($this->data['EstimateDtlTrn'][$i]['sales_price']);
 			$this->data['EstimateDtlTrn'][$i]['sales_cost'] =  trim($this->data['EstimateDtlTrn'][$i]['sales_cost']);
 			$this->data['EstimateDtlTrn'][$i]['aw_share'] =  $this->data['EstimateDtlTrn'][$i]['aw_share'] / 100;
 			$this->data['EstimateDtlTrn'][$i]['rw_share'] =  $this->data['EstimateDtlTrn'][$i]['rw_share'] / 100;

 			/* 新規追加された商品のみ設定する */
 			if($this->data['EstimateDtlTrn'][$i]['id'] == null || $this->data['EstimateDtlTrn'][$i]['id'] == ""){
 			  /* 商品情報を取得して見積明細にセットする */
 			  $goods = $this->GoodsMstView->findById($this->data['EstimateDtlTrn'][$i]['goods_id']);

 			  $this->data['EstimateDtlTrn'][$i]['vendor_id'] 				 =  $goods['GoodsMstView']['vendor_id'];
 			  $this->data['EstimateDtlTrn'][$i]['vendor_nm'] 				 =  $goods['GoodsMstView']['vendor_nm'];
 			  $this->data['EstimateDtlTrn'][$i]['goods_ctg_nm'] 			 =  $goods['GoodsMstView']['goods_ctg_nm'];
 			  $this->data['EstimateDtlTrn'][$i]['goods_kbn_nm'] 			 =  $goods['GoodsMstView']['goods_kbn_nm'];
 			  $this->data['EstimateDtlTrn'][$i]['goods_cd'] 				 =  $goods['GoodsMstView']['goods_cd'];
 			  $this->data['EstimateDtlTrn'][$i]['original_sales_goods_nm']   =  $goods['GoodsMstView']['goods_nm'];
 			  $this->data['EstimateDtlTrn'][$i]['original_sales_price'] 	 =  $goods['GoodsMstView']['price'];
 			  $this->data['EstimateDtlTrn'][$i]['original_sales_cost'] 	     =  $goods['GoodsMstView']['cost'];
 			  $this->data['EstimateDtlTrn'][$i]['set_goods_kbn'] 		     =  $goods['GoodsMstView']['set_goods_kbn'];
 			  $this->data['EstimateDtlTrn'][$i]['currency_kbn'] 			 =  $goods['GoodsMstView']['currency_kbn'];
 			}
 		}

      /** 1削除 **/
      if(strtoupper($this->params['form']['submit'])  ==  "DELETE")
      {
         //見積削除
         $ret = $this->EstimateService->deleteAll($this->data['EstimateTrn']['id'],$customer_id,$this->Auth->user('username'));
      }
      /** 2.既存の見積もりを複製して新規作成 **/
      else if(strtoupper($this->params['form']['submit'])  ==  "COPY")
      {
      	/* 画面側のデータでは保持していない見積明細情報をセットする */
      	 for($i=1;$i <= count($this->data['EstimateDtlTrn']);$i++){
      	 	/* 更新データのみ(新規データは前の処理で設定済み) */
      	 	if($this->data['EstimateDtlTrn'][$i]['id'] != null && $this->data['EstimateDtlTrn'][$i]['id'] != ""){
   			  $estimate_dtl = $this->EstimateDtlTrn->findById($this->data['EstimateDtlTrn'][$i]['id']);

 			  $this->data['EstimateDtlTrn'][$i]['vendor_id'] 				 =  $estimate_dtl['EstimateDtlTrn']['vendor_id'];
 			  $this->data['EstimateDtlTrn'][$i]['vendor_nm'] 				 =  $estimate_dtl['EstimateDtlTrn']['vendor_nm'];
 			  $this->data['EstimateDtlTrn'][$i]['goods_ctg_nm'] 			 =  $estimate_dtl['EstimateDtlTrn']['goods_ctg_nm'];
 			  $this->data['EstimateDtlTrn'][$i]['goods_kbn_nm'] 			 =  $estimate_dtl['EstimateDtlTrn']['goods_kbn_nm'];
 			  $this->data['EstimateDtlTrn'][$i]['goods_cd'] 				 =  $estimate_dtl['EstimateDtlTrn']['goods_cd'];
 			  $this->data['EstimateDtlTrn'][$i]['original_sales_goods_nm']   =  $estimate_dtl['EstimateDtlTrn']['original_sales_goods_nm'];
 			  $this->data['EstimateDtlTrn'][$i]['original_sales_price'] 	 =  $estimate_dtl['EstimateDtlTrn']['original_sales_price'];
 			  $this->data['EstimateDtlTrn'][$i]['original_sales_cost'] 	     =  $estimate_dtl['EstimateDtlTrn']['original_sales_cost'];
 			  $this->data['EstimateDtlTrn'][$i]['set_goods_kbn'] 		     =  $estimate_dtl['EstimateDtlTrn']['set_goods_kbn'];
 			  $this->data['EstimateDtlTrn'][$i]['currency_kbn'] 			 =  $estimate_dtl['EstimateDtlTrn']['currency_kbn'];
      	 	}
      	 }
         //見積新規仮登録
     	 $ret = $this->EstimateService->copy($this->data,$this->Auth->user('username'));
      }
      /** 3.通常発行更新 **/
      else if(strtoupper($this->params['form']['submit'])  ==  "PUBLISH")
      {
         $ret =  $this->EstimateService->updateTemporally($this->data,$this->data['EstimateTrn']['id'],$this->Auth->user('username'));
      }
      /** 4.採用発行更新 **/
      else if(strtoupper($this->params['form']['submit'])  ==  "ADOPT")
      {
      	 $ret = $this->EstimateService->updateFormally($this->data,$this->data['EstimateTrn']['id'],$customer_id,$this->Auth->user('username'));
      }
      /* 異常パラメーター */
      else{
            return json_encode(array('result'=>false,'message'=>"処理が失敗しました。",'reason'=>"予期しないコード[".$this->params['form']['submit']."]です。 "));
      }
        if($ret['result']==false){	return json_encode($ret); }
        $tr->commit();
        return json_encode(array('result'=>true,'message'=>'処理完了しました。'));
 	}else{

 	//見積明細を取得
 	$data = $this->EstimateDtlTrnView->find('all',array('conditions'=>array('estimate_id'=>$estimate_id),
 	                                                    'order'=>'no'));

 	 /* 複数年度の商品マスタを取得する */
 	 //$years = $this->GoodsMst->find('all',array('fields'=>array('year'),'group'=>'year','order'=>'year desc'));
 	 //$this->Session->write('goods_mst_year',$years[0]['GoodsMst']['year']);
 	 //$this->set("years",$years);

 	//お内金額取得
 	$this->set("prepaid_amount",$this->EstimateService->getPrepaidAmount($estimate_id));

 	//商品分類情報を取得
 	$this->set("goods_ctg_list", $this->GoodsCtgMst->find('all'));
 	//支払区分情報を取得
 	$this->set("payment_kbn_list", $this->PaymentKbnMst->find('all'));
    //環境設定情報を取得
 	$this->set("env_data", $this->EnvMst->findById(1));
 	//新郎新婦の名前をセット
    $this->set(	"broom",$data[0]['EstimateDtlTrnView']['grmls_kj'].$data[0]['EstimateDtlTrnView']['grmfs_kj']);
    $this->set(	"bride",$data[0]['EstimateDtlTrnView']['brdfs_kj']);

 	$this->set("data",$data);
 	$this->set("menu_customers","");
 	$this->set("menu_customer","current");
 	$this->set("menu_fund","");

 	$this->set("sub_title","見積編集");
 	$this->set("user",$this->Auth->user());
 	$this->layout = 'edit_mode';
   }
 }

 /**
  *
  * [AJAX]引数の$keywordによって商品区分リストまたは商品リストを取得する
  * @param $keyword  GOODS_CTG_LIST or GOODS_KBN_LIST or GOODS_LIST
  * @param $id            null      or 商品カテゴリID or 商品区分ID
  */
 function feed($keyword,$id=null){

 	//AJAX CALLのみ処理する
    if (!$this->RequestHandler->isAjax()){ 	$this->cakeError("error404");  }

    configure::write('debug', 0);
	$this->layout = '';
    $data = null;
    $renderName =null;
 	switch (strtoupper($keyword)){
 		case "GOODS_CTG_LIST":
 			    $data = $this->GoodsCtgMst->find('all');
 			    $renderName = "goodsCtgList";
 			    break;
 		case "GOODS_KBN_LIST":
 			    $data = $this->GoodsKbnMst->find('all',array('conditions'=>array('goods_ctg_id'=>$id,'del_kbn'=>EXISTS),'order'=>array('goods_kbn_nm')));
 			    $renderName = "goodsKbnList";
 			    break;

 	    case "GOODS":
 	    	    $this->autoRender =false;
 			    $data = $this->GoodsMstView->findById($id);
 			    return json_encode($data);

 	    /* 商品区分IDの商品リストをJSONデータで取得する */
 		case "GOODS_LIST":
 			    $this->autoRender =false;
 			    $data = $this->GoodsMstView->find('all',array('conditions'=>
 			    		                                array('goods_kbn_id'=>$id,'year'=>GOODS_YEAR,'non_display_flg'=>DISPLAY,
 			    		                                	  'start_valid_dt <='=>date('Y-m-d'),'end_valid_dt >='=>date('Y-m-d'),'del_kbn'=>EXISTS),
 			    		                       'order'=>array('goods_cd','revision desc')));

 			    $current_goods_cd = "";
 			    $dummy_key = "";
 	            $comps = array();
 	            for($i=0;$i < count($data);$i++){

 	              if(empty($current_goods_cd) || $current_goods_cd != $data[$i]['GoodsMstView']['goods_cd']){
 	            		$current_goods_cd = $data[$i]['GoodsMstView']['goods_cd'];
 	            		$dummy_key = $data[$i]['GoodsMstView']['goods_cd']."    ".$data[$i]['GoodsMstView']['goods_nm'];
 	              }

 	 	          $sign = $data[$i]['GoodsMstView']['currency_kbn'] == FOREIGN ? "$" : "￥";
 	  	          array_push($comps, array("id" => $data[$i]['GoodsMstView']['id'], "cell" => array($dummy_key,
 	  	                                                                                  $data[$i]['GoodsMstView']['goods_cd'],
 	  	                                                                                  $data[$i]['GoodsMstView']['goods_nm'],
 	  	                                                                                  $data[$i]['GoodsMstView']['revision'],
                                                                                          $data[$i]['GoodsMstView']['price'] == null ? $sign."0" : $sign.$data[$i]['GoodsMstView']['price'],
                                                                                          $data[$i]['GoodsMstView']['cost']  == null ? $sign."0" : $sign.$data[$i]['GoodsMstView']['cost'],
                                                                                          $data[$i]['GoodsMstView']['vendor_nm'])
                                  )
                   );
 	            }
                return json_encode(array('rows' => $comps));
         /* 見積テンプレートリストをJSONデータで取得する */
 		case "TEMPLATE_LIST":
 			    $this->autoRender =false;
 			    //固定データ以外を取得(ID1のデータは更新系の処理はさせない）
 			    $data = $this->TemplateEstimateTrn->find('all',array('conditions'=>array('id !='=>1),'order'=>array('reg_dt desc')));

 	            $comps = array();
 	            for($i=0;$i < count($data);$i++){
 	 	          /*
 	  	          array_push($comps, array("id" => $data[$i]['TemplateEstimateTrn']['id'], "cell" => array("<a href='addEstimate?id=".   $data[$i]['TemplateEstimateTrn']['id']."' style='text-decoration:underline;color:#29F'>".$data[$i]['TemplateEstimateTrn']['template_nm']."</a>",
 	  	           																						   $data[$i]['TemplateEstimateTrn']['reg_dt'],
                                                                                                           $data[$i]['TemplateEstimateTrn']['reg_nm'],
                                                                                                           $data[$i]['TemplateEstimateTrn']['upd_dt'],
                                                                                                           $data[$i]['TemplateEstimateTrn']['upd_nm'])
                   */
 	              array_push($comps, array("id" => $data[$i]['TemplateEstimateTrn']['id'], "cell" => array($data[$i]['TemplateEstimateTrn']['template_nm'],
 	  	           																						   $data[$i]['TemplateEstimateTrn']['reg_dt'],
                                                                                                           $data[$i]['TemplateEstimateTrn']['reg_nm'],
                                                                                                           $data[$i]['TemplateEstimateTrn']['upd_dt'],
                                                                                                           $data[$i]['TemplateEstimateTrn']['upd_nm'])
                                  )
                   );
 	            }
                return json_encode(array('rows' => $comps));
 		default:
 			    //TO DO エラー処理
 			    //AJAX　CALL時のエラーではエラーメッセージやステータスをJSONとしてクライアントに送って、クライアント側で処理する。
 			    //cakeErrorやdieは上手く動作しない
 			    //redirectが必要な場合はJSでwindow.locationを使用する
 			    return json_encode(array('result'=>false,'message'=>"処理が失敗しました。",'reason'=>"予期しないコード[".$keyword."]です。 "));
 			    break;
 	}

	$this->set('goods', $data);
	$this->render($renderName);
 }


 /**
  *
  * [AJAX]商品マスタの年度の切り替え
  * @param $year
  */
 /*
 function setYearOfGoodsMaster($year=null){

 	//AJAX CALLのみ処理する
    if (!$this->RequestHandler->isAjax()){ 	$this->cakeError("error404");  }

 	$this->Session->write("goods_mst_year",$year);

 	configure::write('debug', 0);
 	$this->autoRender =false;
	$this->layout = '';
 }*/


 /**
  *
  * [AJAX]問い合わせ画面を表示し、また問い合わせデータを新規作成する
  */
 function contactForm($estimate_dtl_id=null,$current_line_no= null)
 {
 	$this->layout = '';
 	if(!empty($this->data)){

	   $this->autoRender =false;
 	   configure::write('debug', 0);

 	   $ret = $this->Mail->sendMail($this->data,$this->Session->read('customer_id'),$this->Auth->user());
 	   if($ret != null){
 	   	  	 return json_encode(array('result'=>false,'message'=>"メール送信に失敗しました。",'reason'=>$ret));
 	   }
 	   return json_encode(array('result'=>true,'message'=>'メール送信しました。'));
 	}else{

 	  $this->set("user",$this->Auth->user());
 	  $this->set("current_line_no",$current_line_no);
 	  $this->set("estimate_dtl_data",$this->EstimateDtlTrnView->findById($estimate_dtl_id));
 	  $this->set("vendor_list",$this->VendorMst->find("all",array("conditions"=>array("del_kbn"=>EXISTS))));
    }
 }

/**
  *
  * [AJAX]連絡帳選択画面を表示する
  * @param $page
  */
 function addressListForm()
 {
    //AJAX CALLのみ処理する
    if (!$this->RequestHandler->isAjax()){ $this->cakeError("error404"); }

    configure::write('debug', 0);
	$this->layout = '';
 }

 /**
  *
  * [AJAX] 連絡先帳をJSONデータで取得する
  */
 function AddressList()
 {
 	if (!$this->RequestHandler->isAjax()){ die('Not found');}

 	 $this->layout = '';
 	 $this->autoRender =false;
 	 configure::write('debug', 0);

 	 $data =  $this->ContactAddressTrnView->find('all');

 	 $comps = array();
 	 for($i=0;$i < count($data);$i++){

 	 	array_push($comps, array("id" => $i, "cell" => array($data[$i]['ContactAddressTrnView']['name'] ,
 	 	                                                     $data[$i]['ContactAddressTrnView']['email'],
 	 	                                                     $data[$i]['ContactAddressTrnView']['master_kbn'])
                                  )
                   );
 	 }
    return json_encode(array('rows' => $comps));
 }

 /**
  *
  * 見積明細を出力する
  * @param $file_type    EXCEL or PDF
  * @param $estimate_id
  */
 function export($file_type,$estimate_id,$credit_amount = 0,$invoice_deadline=null,$pdf_note=null){

  if(strtoupper($file_type) == "ESTIMATE_YEN" || strtoupper($file_type) == "ESTIMATE_DOLLAR"){
  	 $this->EstimateTrn->updatePdfNote($estimate_id,empty($pdf_note) ? "" : $pdf_note);
  }

   $customer_id = $this->Session->read('customer_id');
   $estimate_dtl = $this->EstimateDtlTrnView->find('all',array('conditions'=>array('estimate_id'=>$estimate_id),'order'=>array('no'=>'asc')));
   $estimate_header = $this->EstimateTrnView->find('all',array('conditions'=>array('estimate_id'=>$estimate_id)));
   $contract = $this->ContractTrnView->find('all',array('conditions'=>array('customer_id'=>$customer_id)));
   /* 保存ファイル名作成(契約済みなら契約情報の挙式日を優先する) */
   $file_name = null;
   if(count($contract)==0){
     $customer = $this->CustomerMstView->find('all',array('conditions'=>array('id'=>$customer_id)));
     $file_name = ($customer[0]['CustomerMstView']['wedding_dt']==null ? "": date('mdY',strtotime($customer[0]['CustomerMstView']['wedding_dt']))).mb_convert_encoding($customer[0]['CustomerMstView']['grmls_kj'], "SJIS", "AUTO").'('.$customer[0]['CustomerMstView']['grmls_rm'].')';
   } else{
     $file_name = date('mdY',strtotime($contract[0]['ContractTrnView']['wedding_dt'])).mb_convert_encoding($contract[0]['ContractTrnView']['grmls_kj'], "SJIS", "AUTO").'('.$contract[0]['ContractTrnView']['grmls_rm'].')';
   }

   $report = null;
   $save_filename = null;
   $temp_filename = null;
   $render_name = null;
   $report = null;

   switch (strtoupper($file_type)) {
   	case "EXCEL":
                 $temp_filename = "estimate_template.xlsx";
   		         $save_filename = "Estimate".$file_name.".xlsx";
                 $render_name = "excel";
   	       	     break;
   	case "ESTIMATE_YEN":
   		         $report = $this->ReportMst->find('all',array('conditions'=>array('code'=>'RPT01')));
   		         $save_filename = "Estimate".$file_name.".pdf";
   		         $render_name = "estimate_pdf";
   		         break;
    case "ESTIMATE_DOLLAR":
   		         $report = $this->ReportMst->find('all',array('conditions'=>array('code'=>'RPT01')));
   		         $save_filename = "Estimate".$file_name.".pdf";
   		         $render_name = "estimate_dollar_pdf";
   		         break;
    case "INVOICE_YEN":
   		         $save_filename = "Invoice".$file_name.".pdf";
   		         $render_name = "invoice_pdf";
   		         $this->set("invoice_deadline",$invoice_deadline);

   		         /* ステータスが「成約」の場合は「請求書発行済み」に移行し、請求書発行日を設定する  */
   		         if($contract[0]['ContractTrnView']['status_id'] == CS_CONTRACTED){
   		         	$this->CustomerMst->setInvoice($customer_id,$this->Auth->user('username'));
   		         	$this->EstimateService->setInvoiceDate($customer_id,date('Y-m-d H:i:s'));
   		         }

   		         break;
    case "INVOICE_DOLLAR":
   		         $save_filename = "Invoice".$file_name.".pdf";
   		         $render_name = "invoice_dollar_pdf";
   		         $this->set("invoice_deadline",$invoice_deadline);

   		         /* ステータスが「成約」の場合は「請求書発行済み」に移行し、請求書発行日を設定する  */
   		         if($contract[0]['ContractTrnView']['status_id'] == CS_CONTRACTED){
   		         	$this->CustomerMst->setInvoice($customer_id,$this->Auth->user('username'));
   		         	$this->EstimateService->setInvoiceDate($customer_id,date('Y-m-d H:i:s'));
   		         }

   		         break;

   case "CREDIT_YEN":
    	         $save_filename = "Credit".$file_name.".pdf";
   		         $render_name = "credit_pdf";
   		         $this->set("credit_deadline",$invoice_deadline);

   		         break;
   	default:
   		    $this->cakeError("error", array("message" => "予期しないファイルタイプ[{$file_type}]です。"));
       	    break;
   }
   $this->layout = false;
   $this->set("credit_amount",$credit_amount);
   $this->set("customer",$this->CustomerMstView->findById($customer_id));
   $this->set("estimate_dtl", $estimate_dtl );
   $this->set("estimate_header", $estimate_header );
   $this->set("sheet_name", "Estimate" );
   $this->set("filename", $save_filename );
   $this->set("template_file", $temp_filename);
   $this->set("report", $report);
   $this->render($render_name);
 }


 /**
 *
 * 商品リスト選択フォーム表示
 * @param $goods_ctg_id
 * @param $goods_kbn_id
 * @param $current_line_no
 */
 function goodsDetailForm($goods_ctg_id,$goods_kbn_id,$current_line_no)
 {
    if (!$this->RequestHandler->isAjax()){ die('Not found');}

	configure::write('debug', 0);
	$this->layout = '';
	$this->set('current_line_no',$current_line_no);
	$this->set('goods_ctg_id',$goods_ctg_id);
	$this->set('goods_kbn_id',$goods_kbn_id);
 }


  /**
  *
  * 新規商品登録フォーム表示
  * @param $goods_ctg_id
  * @param $goods_kbn_id
  */
 function GoodsAdditionForm($goods_ctg_id,$goods_kbn_id,$current_line_no)
 {
 	 if (!$this->RequestHandler->isAjax()){ die('Not found');}
 	 configure::write('debug', 0);
	 $this->layout = '';

	 $this->set("user",$this->Auth->user());
	 //商品分類リストの取得
 	 $this->set("goods_ctg",$this->GoodsCtgMst->findById($goods_ctg_id));
 	 //商品区分リスト
 	 $this->set("goods_kbn",$this->GoodsKbnMst->findById($goods_kbn_id));
 	 //業者リスト
 	 $this->set("vendor_list",$this->VendorMst->find('all',array('conditions'=>array('del_kbn'=>EXISTS))));
 	 //年度
 	 //$this->set("year",$this->Session->read("goods_mst_year"));
 	 $this->set('current_line_no',$current_line_no);
 }


 /**
  *
  * 新規商品の追加
  */
 function addGoods()
 {
 	if (!$this->RequestHandler->isAjax()){ die('Not found');}

    if(!empty($this->data))
 	{
 	  $tr = ClassRegistry::init('TransactionManager');
	  $tr->begin();

	  $this->layout = '';
 	  $this->autoRender =false;
 	  configure::write('debug', 0);

 	  $this->data['GoodsMst']['id'] = null;
 	  $this->data['GoodsMst']['revision'] = 1;
 	  $this->data['GoodsMst']['year'] = GOODS_YEAR;
 	  $this->data['GoodsMst']['goods_cd'] = $this->GoodsMst->getNewGoodsCode($this->data['GoodsMst']['goods_ctg_id'],GOODS_YEAR);
 	  $this->data['GoodsMst']['internal_pay_flg'] = isset($this->data['GoodsMst']['internal_pay_flg']) ? 1:0;
 	  $this->data['GoodsMst']['aw_share'] = $this->data['GoodsMst']['aw_share'] / 100;
      $this->data['GoodsMst']['rw_share'] = $this->data['GoodsMst']['rw_share'] / 100;
      $this->data['GoodsMst']['start_valid_dt'] = empty($this->data['GoodsMst']['start_valid_dt']) ? "1000-01-01" :$this->data['GoodsMst']['start_valid_dt'] ;
      $this->data['GoodsMst']['end_valid_dt']   = empty($this->data['GoodsMst']['end_valid_dt'])   ? "9999-12-31" :$this->data['GoodsMst']['end_valid_dt'] ;
      $this->data['GoodsMst']['cost1']   = $this->data['GoodsMst']['cost'];
 	  $this->data['GoodsMst']['reg_nm'] = $this->Auth->user('username');
      $this->data['GoodsMst']['reg_dt'] = date('Y-m-d H:i:s');
      $this->GoodsMst->create();
 	  if($this->GoodsMst->save($this->data)){
 	  	 $last_goods_id = $this->GoodsMst->getLastInsertID();
 	  	 $tr->commit();
  	     return json_encode(array('result'=>true,'message'=>'登録完了しました。','code'=>$this->data['GoodsMst']['goods_cd'],'newId'=>$last_goods_id,
  	                              'cost'=>$this->data['GoodsMst']['cost'],'price'=>$this->data['GoodsMst']['price'],'goodsName'=>$this->data['GoodsMst']['goods_nm']));
 	  }else{
 	   	 return json_encode(array('result'=>false,'message'=>"登録に失敗しました。",'reason'=>$this->GoodsMst->getDbo()->error."[".date('Y-m-d H:i:s')."]"));
 	  }
 	}
 }

 /**
  * 入金状況一覧表示
  */
 function creditInfoForm()
 {
 	$this->layout = '';
 	configure::write('debug', 0);

 	$customer_id = $this->Session->read('customer_id');

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