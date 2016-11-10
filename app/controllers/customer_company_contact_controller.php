<?php
class CustomerCompanyContactController extends AppController
{

 public $name = 'CustomerCompanyContact';
 public $uses = array('CustomerMst','CustomerMstView','ContactTrn','ContactTrnView','ContactAddressTrnView','GoodsCtgMst','VendorMst','Mail');
 public $layout = 'cust_indivisual_info_main_tab';
 public $components = array('Auth','RequestHandler','Qdmail');
 public $helpers = array('Html','common','Javascript');

 function index()
 {
 	$customer_id = $this->Session->read('customer_id');
 	$customer = $this->CustomerMstView->findById($customer_id);
 	$this->set(	"customer",$customer);
    //新郎新婦の名前をセット
    $this->set(	"broom",($customer['CustomerMstView']['prm_lastname_flg'] == 0 ? $customer['CustomerMstView']['grmls_kj'] : $customer['CustomerMstView']['brdls_kj']).$customer['CustomerMstView']['grmfs_kj'] );
    $this->set(	"bride",$customer['CustomerMstView']['brdfs_kj']);

    //顧客IDで問合せを検索
 	$data = $this->ContactTrnView->find('all',array('conditions'=>array('ContactTrnView.customer_id'=>$customer_id),'order'=>'id'));
 	$this->set(	"data",$data);

 	$this->set("menu_customers","");
 	$this->set("menu_customer","current");
 	$this->set("menu_fund","");

 	$this->set("sub_menu_customer_info","");
 	$this->set("sub_menu_customer_meeting","");
 	$this->set("sub_menu_customer_wedding_reserve","");
 	$this->set("sub_menu_customer_contact","current");
 	//$this->set("sub_menu_customer_schedule","");
 	$this->set("sub_menu_customer_estimate","");

 	$this->set("sub_title","問い合わせ状況");
 	$this->set("user",$this->Auth->user());
 }

 /**
  *
  * 問い合わせ追加
  */
 function addContact()
 {
 	if(!empty($this->data)){

	   $this->layout = '';
 	   $this->autoRender =false;
 	   configure::write('debug', 0);

 	   $ret = $this->Mail->sendMail($this->data,$this->Session->read('customer_id'),$this->Auth->user());
 	   if($ret != null){
 	   	  	 return json_encode(array('result'=>false,'message'=>"メール送信に失敗しました。",'reason'=>$ret));
 	   }
 	   return json_encode(array('result'=>true,'message'=>'メール送信しました。'));
 	}else{

 	  $customer_id = $this->Session->read('customer_id');
 	  $this->layout = 'edit_mode';

 	  //商品分類情報を取得
 	  $this->set("goods_ctg_list", $this->GoodsCtgMst->find('all',array('conditions'=>array('del_kbn'=> EXISTS))));
   	  //ベンダー情報を取得
 	  $this->set("vendor_list", $this->VendorMst->find('all',array('conditions'=>array('del_kbn'=> EXISTS))));

 	  $this->set("menu_customers","");
 	  $this->set("menu_customer","current");
 	  $this->set("menu_fund","");

 	  $this->set("sub_title","問合せ追加");
 	  $this->set("user",$this->Auth->user());
    }
 }


 /**
  *
  * 問合せ編集・削除画面
  * @param unknown_type $id
  * @param unknown_type $customer_id
  */
 function editContact($id=null,$customer_id=null)
 {
  	if(!empty($this->data))
 	{
 	   $tr = ClassRegistry::init('TransactionManager');
	   $tr->begin();

	   $this->layout = '';
 	   $this->autoRender =false;
 	   configure::write('debug', 0);

      /* 削除 */
      if(strtoupper($this->params['form']['submit'])  ==  "DELETE")
      {
         if($this->ContactTrn->delete($this->data['ContactTrn']['id'])==false){
        	  return json_encode(array('result'=>false,'message'=>"削除に失敗しました。",'reason'=>$this->ContactTrn->getDbo()->error."[".date('Y-m-d H:i:s')."]"));
         }
      }
      /* 更新 */
      else if(strtoupper($this->params['form']['submit'])  ==  "UPDATE")
     {
     	 //問合せテーブルの更新
     	 $est_dtl_fields = array('content_kbn','note','upd_nm','upd_dt');

 	     $this->data['ContactTrn']['upd_nm'] = $this->Auth->user('username');
 	     $this->data['ContactTrn']['upd_dt'] = date('Y-m-d H:i:s');
 	     $this->ContactTrn->save($this->data,false,$est_dtl_fields);
 	     if($this->ContactTrn->save($this->data['ContactTrn'])==false){
 	    	return json_encode(array('result'=>false,'message'=>"更新に失敗しました。",'reason'=>$this->ContactTrn->getDbo()->error."[".date('Y-m-d H:i:s')."]"));
 	     }
      /* 異常パラメーター */
      }else{
      	    return json_encode(array('result'=>false,'message'=>"処理が失敗しました。",'reason'=>"予期しないコード[".$this->params['form']['submit']."]です。 "));
      }
        $tr->commit();
        return json_encode(array('result'=>true,'message'=>'処理完了しました。'));
    }else{

 	//顧客情報一覧タブからから遷移してくる場合にセッションを開始する
 	if($customer_id != null){	$this->Session->write('customer_id',$customer_id); }

 	$this->layout = 'edit_mode';

 	$data = $this->ContactTrnView->find('all',array("conditions"=>array("id"=>$id)));
 	$this->set("data",$data);

 	$this->set("menu_customers","");
 	$this->set("menu_customer","current");
 	$this->set("menu_fund","");

 	$this->set("sub_title","問合せ編集");
 	$this->set("user",$this->Auth->user());
    }
  }

 /**
  *
  * 返答内容編集
  * @return string
  */
 function editAnswer()
 {
    if (!$this->RequestHandler->isAjax()) { die('Not found'); }

  	if(!empty($this->data))
 	{
 		$tr = ClassRegistry::init('TransactionManager');
	    $tr->begin();

	    $this->layout = '';
 	    $this->autoRender =false;
 	    configure::write('debug', 0);

     	//問合せテーブルの更新
     	$est_dtl_fields = array('answer_kbn','answer_dt','upd_nm','upd_dt');

     	$counter = count($this->data['ContactTrn']);

     	for($i=0;$i < $counter;$i++)
 	    {
 	      //返答区分が[空]の場合は返答日付をNULLにする
 	      if($this->data['ContactTrn'][$i]['answer_kbn'] == 0){
 	      	$this->data['ContactTrn'][$i]['answer_dt'] = null;
 	      }
 	      else {
 	      	$this->data['ContactTrn'][$i]['answer_dt'] = date('Y-m-d H:i:s');
 	      }
 	      $this->data['ContactTrn']['upd_nm'] = $this->Auth->user('username');
 	      $this->data['ContactTrn']['upd_dt'] = date('Y-m-d H:i:s');
 	      if($this->ContactTrn->save($this->data['ContactTrn'][$i],false,$est_dtl_fields)==false){
 	     	return json_encode(array('result'=>false,'message'=>"更新に失敗しました。",'reason'=>$this->ContactTrn->getDbo()->error."[".date('Y-m-d H:i:s')."]"));
 	      }
 	    }
       $tr->commit();
       return json_encode(array('result'=>true,'message'=>'処理完了しました。'));
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
}
?>