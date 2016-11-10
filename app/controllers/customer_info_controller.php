<?php
class CustomerInfoController extends AppController
{
 public $name = 'CustomerInfo';
 public $uses = array('CustomerMst','CustomerProcessTrn','CustomerMstView','CustomerStatusMst','EstimateTrn','EstimateService',
 		              'ContractTrn','FundManagementTrn','RemittanceTrn','User');
 public $layout = 'cust_indivisual_info_main_tab';
 public $components = array('Auth');
 public $helpers = array('Html','common','Javascript');

 /**
  *
  * 顧客詳細画面表示
  */
 function index()
 {
 	//顧客IDをセッションから取得
 	$customer_id = $this->Session->read('customer_id');
 	//顧客情報取得
 	$data = $this->CustomerMstView->findById($customer_id);
    $this->set(	"data",$data);

    //導線1リスト
    $this->set("leading1_list",$this->CustomerMst->getLeading1List());
    //導線2リスト
    $this->set("leading2_list",$this->CustomerMst->getLeading2List());

    //顧客対応履歴情報取得
    $this->set("customer_process_data",$this->CustomerProcessTrn->find('all',array('conditions'=>array('customer_id'=>$customer_id),'order'=>'action_dt')));

    $this->set("invoice_issued_dt",$this->EstimateService->getInvoiceIssuedDateByCustomer($data['CustomerMstView']['id']));
    $this->set("contracted_dt",$this->ContractTrn->getContractedDateByCustomer($data['CustomerMstView']['id']));

    //新郎新婦の名前をセット
    $this->set(	"broom",($data['CustomerMstView']['prm_lastname_flg'] == 0 ? $data['CustomerMstView']['grmls_kj'] : $data['CustomerMstView']['brdls_kj']).$data['CustomerMstView']['grmfs_kj'] );
    $this->set(	"bride",$data['CustomerMstView']['brdfs_kj']);

 	$this->set("menu_customers","");
 	$this->set("menu_customer","current");
 	$this->set("menu_fund","");

 	$this->set("sub_menu_customer_info","current");
 	$this->set("sub_menu_customer_meeting","");
 	$this->set("sub_menu_customer_wedding_reserve","");
 	$this->set("sub_menu_customer_contact","");
 	//$this->set("sub_menu_customer_schedule","");
 	$this->set("sub_menu_customer_estimate","");

 	$this->set("sub_title","基本情報");
 	$this->set("user",$this->Auth->user());
 }

 /**
  *
  * 顧客編集・削除画面
  * @param $id
  */
 function editCustomer($id=null)
 {
 	if(!empty($this->data))
 	{
 	   $tr = ClassRegistry::init('TransactionManager');
	   $tr->begin();

	   $this->layout = '';
 	   $this->autoRender =false;
 	   configure::write('debug', 0);

 	  //削除
      if(strtoupper($this->params['form']['submit'])  ==  "DELETE")
      {
          if($this->CustomerMst->delete($this->data['CustomerMst']['id'])==false){
        	  return json_encode(array('result'=>false,'message'=>"削除に失敗しました。",'reason'=>$this->CustomerMst->getDbo()->error."[".date('Y-m-d H:i:s')."]"));
         }
      }
      //更新
      else if(strtoupper($this->params['form']['submit'])  ==  "UPDATE")
      {
         /* バリデーションチェック */
 	     if(empty($this->data['CustomerMst']['grmls_kj']) && empty($this->data['CustomerMst']['grmfs_kj]']) && empty($this->data['CustomerMst']['grmls_kn']) &&
 	        empty($this->data['CustomerMst']['grmfs_kn']) && empty($this->data['CustomerMst']['grmls_rm']) && empty($this->data['CustomerMst']['grmfs_rm']) &&
 	        empty($this->data['CustomerMst']['brdls_kj']) && empty($this->data['CustomerMst']['brdfs_kj]']) && empty($this->data['CustomerMst']['brdls_kn']) &&
 	        empty($this->data['CustomerMst']['brdfs_kn']) && empty($this->data['CustomerMst']['brdls_rm']) && empty($this->data['CustomerMst']['brdfs_rm'])){
 	          return json_encode(array('result'=>false,'message'=>"登録に失敗しました。",'reason'=>'最低1項目の顧客名情報が必要です。'));
 	     }
     	 //0000対策
         if(empty($this->data['CustomerMst']['grmbirth_dt'])){$this->data['CustomerMst']['grmbirth_dt'] = null;}
 	     if(empty($this->data['CustomerMst']['brdbirth_dt'])){$this->data['CustomerMst']['brdbirth_dt'] = null;}
 	     if(empty($this->data['CustomerMst']['wedding_planned_dt'])){$this->data['CustomerMst']['wedding_planned_dt'] = null;}
 	     if(empty($this->data['CustomerMst']['first_contact_dt'])){$this->data['CustomerMst']['first_contact_dt'] = null;}
 	     if(empty($this->data['CustomerMst']['first_visited_dt'])){$this->data['CustomerMst']['first_visited_dt'] = null;}

 	     $this->data['CustomerMst']['non_display_flg']  = isset($this->data['CustomerMst']['non_display_flg']) ? 1:0;

 	     //顧客コード作成
         $this->data['CustomerMst']['customer_cd'] = $this->CustomerMst->recreateCustomerCode($this->data['CustomerMst']['customer_cd'],$this->data['CustomerMst']['first_contact_dt'],$this->data['CustomerMst']['wedding_planned_dt']);
     	 $this->data['CustomerMst']['upd_nm'] = $this->Auth->user('username');
 	     $this->data['CustomerMst']['upd_dt'] = date('Y-m-d H:i:s');

 	     //ステータスが成約の場合は延期からの戻しの場合があるので採用済み見積の請求書発行日をクリアする
 	     if($this->data['CustomerMst']['status_id'] == CS_CONTRACTED){
 	     	$ret = $this->EstimateService->clearInvoiceDate($this->data['CustomerMst']['id']);
 	     	if($ret['result']==false){ return json_encode($ret); }
 	     }

 	     //顧客データ更新
 	     if($this->CustomerMst->save($this->data)==false){
 	     	return json_encode(array('result'=>false,'message'=>"顧客テーブルの更新に失敗しました。",'reason'=>$this->CustomerMst->getDbo()->error."[".date('Y-m-d H:i:s')."]"));
 	     }

     	 //顧客対応進捗テーブルの更新
     	 $saving_id= array();
     	 $process_fields = array('action_nm','action_dt','note','upd_nm','upd_dt');
     	   if(isset($this->data['CustomerProcessTrn'])){
     	     for($i=0; $i < count($this->data['CustomerProcessTrn']);$i++){

     	 	  //更新
     	      if($this->data['CustomerProcessTrn'][$i]['status'] == "Exist"){
     	 	     $this->data['CustomerProcessTrn'][$i]['upd_nm'] = $this->Auth->user('username');
 	             $this->data['CustomerProcessTrn'][$i]['upd_dt'] = date('Y-m-d H:i:s');
 	             if($this->CustomerProcessTrn->save($this->data['CustomerProcessTrn'][$i],false,$process_fields)==false){
 	            	  return json_encode(array('result'=>false,'message'=>"顧客進捗テーブルの更新に失敗しました。",'reason'=>$this->CustomerProcessTrn->getDbo()->error."[".date('Y-m-d H:i:s')."]"));
 	             }
 	            array_push($saving_id,$this->data['CustomerProcessTrn'][$i]['id']);
     	      }
     	      //登録
     	      else{
     	       $this->data['CustomerProcessTrn'][$i]['id'] = null;
     	       $this->data['CustomerProcessTrn'][$i]['customer_id'] = $this->data['CustomerMst']['id'];
     	       $this->data['CustomerProcessTrn'][$i]['reg_nm'] = $this->Auth->user('username');
 	           $this->data['CustomerProcessTrn'][$i]['reg_dt'] = date('Y-m-d H:i:s');
 	           if($this->CustomerProcessTrn->save($this->data['CustomerProcessTrn'][$i])==false){
 	     	      return json_encode(array('result'=>false,'message'=>"顧客進捗テーブルの登録に失敗しました。",'reason'=>$this->CustomerProcessTrn->getDbo()->error."[".date('Y-m-d H:i:s')."]"));
 	           }
 	           $last_process_id = $this->CustomerProcessTrn->getLastInsertID();
 	           array_push($saving_id, $last_process_id);
     	     }
     	    }
     	   }
           //新規追加でも既存の明細の更新でもないデータはクライアント側で削除指定されたデータなのですべて削除
 	       if($this->CustomerProcessTrn->deleteAll( array('customer_id'=>$this->data['CustomerMst']['id'],'NOT'=>array('id'=>$saving_id)))==false){
 		   return array('result'=>false,'message'=>"顧客進捗テーブルの削除に失敗しました。",'reason'=>$this->CustomerProcessTrn->getDbo()->error."[".date('Y-m-d H:i:s')."]");
 	       }

      /* 異常パラメーター */
      }else{
      	    return json_encode(array('result'=>false,'message'=>"処理が失敗しました。",'reason'=>"予期しないコード[".$this->params['form']['submit']."]です。 "));
      }
        $tr->commit();
        return json_encode(array('result'=>true,'message'=>'処理完了しました。','code'=>$this->data['CustomerMst']['customer_cd']));
 	}
 	/* 編集画面表示 */
   	else{

   	    $data = $this->CustomerMstView->findById($id);
 		$this->set("data",$data);

 		$this->set("status_list",$this->CustomerStatusMst->find('all'));
 		//担当者リスト
 		$this->set("attendant_list",$this->User->GetAllDisplayName());
 		//導線1リスト
 		$this->set("leading1_list",$this->CustomerMst->getLeading1List());
 		//導線2リスト
 		$this->set("leading2_list",$this->CustomerMst->getLeading2List());
 		//新郎新婦の名前をセット
    	$this->set(	"broom",($data['CustomerMstView']['prm_lastname_flg'] == 0 ? $data['CustomerMstView']['grmls_kj'] : $data['CustomerMstView']['brdls_kj']).$data['CustomerMstView']['grmfs_kj'] );
    	$this->set(	"bride",$data['CustomerMstView']['brdfs_kj']);

    	$this->set("invoice_issued_dt",$this->EstimateService->getInvoiceIssuedDateByCustomer($data['CustomerMstView']['id']));
    	$this->set("contracted_dt",$this->ContractTrn->getContractedDateByCustomer($data['CustomerMstView']['id']));

 		$this->set("menu_customers","");
 	    $this->set("menu_customer","current");
    	$this->set("menu_fund","");

 		$this->set("sub_title","顧客編集");
 		$this->set("user",$this->Auth->user());
 		$this->layout = 'edit_mode';
   	}
 }

 /**
  *
  * 顧客進捗情報を取得する
  * @param $id
  */
 function feedCustomerProcessList($customer_id)
 {
 	$this->layout = '';
 	$this->autoRender =false;
 	configure::write('debug', 0);

 	$data = $this->CustomerProcessTrn->find('all',array('conditions'=>array('customer_id'=>$customer_id),'order'=>'action_dt'));

 	// json データを構築
 	$response = new stdClass();
    $response->records = count($data); // 総レコード数
    for($i=0;$i < count($data);$i++){
       $attr = $data[$i]['CustomerProcessTrn'];
       $response->rows[$i]['id']  = $attr['id'];
       $response->rows[$i]['cell']= array($attr['action_dt'],$attr['action_nm'],$attr['note']);
   }
    return json_encode($response);
 }
}
?>
