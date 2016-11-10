<?php
class VendorMasterController extends AppController
{
 public $name = 'VendorMaster';
 public $uses = array('VendorMst','VendorKbnMst','VendorMstView','GoodsMst');
 public $layout = 'edit_mode';
 public $components = array('Auth','RequestHandler');
 public $helpers = array('Html','common','Javascript');


 /**
  *
  *  ベンダー一覧画面の表示
  */
  function index()
 {
 	/*
 	 * ページネーションの設定
 	 */
    if(!empty($this->params['named']['sort'])) {
		$sort = $this->params['named']['sort'];
	}
	else{
		$sort = "id";
	}
 	  $this->paginate = array (
 	    'VendorMstView' => array(
		                  'limit' => 20,
		                  'sort' => $sort,
 	                      )
	    );

	/* フォームからフィルタ条件を送信された場合(POST) */
 	if (!empty($this->data)) {
			$search = $this->data['VendorMstView']['vendor_kbn_id'];
	}
	/* ソートリンクからフィルタ条件を引き継ぐ場合(GET) */
	else if(!empty($this->params['named']['vendor_kbn_id'])) {
			$search = $this->params['named']['vendor_kbn_id'];
	}
	/* 初回訪問時 */
	else{
		    $search = '';
	}

	if ($search) {
			$conditions = array('VendorMstView.vendor_kbn_id' => "{$search}");
	} else {
			$conditions = array();
	}

 	$this->set("data",$this->paginate('VendorMstView',$conditions));

 	/*
 	 * フィルタ条件をVIEWで保持する
 	 *  0:ALL
 	 */
 	$this->set("vendor_kbn_id",$search);
 	if($search == ''){
 	$this->set("vendor_kbn_id",0);
 	}

    $this->set("vendor_kbn_data",$this->VendorKbnMst->find('all'));

 	$this->set("menu_customers","");
 	$this->set("menu_customer","disable");
 	$this->set("menu_fund","");

 	$this->set("sub_title","ベンダー管理");
 	$this->set("user",$this->Auth->user());
 }

 /**
  *
  * ベンダー登録画面の表示及び実行
  *
  */
 function addVendor()
 {
 	if(!empty($this->data))
 	{
 	   $tr = ClassRegistry::init('TransactionManager');
	   $tr->begin();

	   $this->layout = '';
 	   $this->autoRender =false;
 	   configure::write('debug', 0);

 	   $this->data['VendorMst']['id'] = null;
 	   $this->data['VendorMst']['reg_nm'] = $this->Auth->user('username');
       $this->data['VendorMst']['reg_dt'] = date('Y-m-d H:i:s');
 	   if($this->VendorMst->save($this->data)){
 	   	 $tr->commit();
  	     return json_encode(array('result'=>true,'message'=>'登録完了しました。'));
 	  }else{
 	   	 return json_encode(array('result'=>false,'message'=>"登録に失敗しました。",'reason'=>$this->VendorMst->getDbo()->error."[".date('Y-m-d H:i:s')."]"));
 	  }

 	}else{

 	  //業種区分リストの取得
 	  $this->set("vendor_kbn_list",$this->VendorKbnMst->find('all',array('conditions'=>array('del_kbn'=>EXISTS))));

 	  $this->set("menu_customers","");
 	  $this->set("menu_customer","disable");
 	  $this->set("menu_fund","");

   	  $this->set("sub_title","ベンダー追加");
 	  $this->set("user",$this->Auth->user());
 	}
 }

 /**
  *
  *  ベンダー編集・削除画面の表示及び実行
  *
  */
 function editVendor($id=null)
 {
 	if(!empty($this->data))
 	{
 	   $tr = ClassRegistry::init('TransactionManager');
	   $tr->begin();

	   $this->layout = 'ajax';
 	   $this->autoRender =false;
 	   configure::write('debug', 0);

 	  /* 削除 */
      if(strtoupper($this->params['form']['submit'])  ==  "DELETE")
      {
         if($this->VendorMst->delete($this->data['VendorMst']['id'])==false){
        	  return json_encode(array('result'=>false,'message'=>"削除に失敗しました。",'reason'=>$this->VendorMst->getDbo()->error."[".date('Y-m-d H:i:s')."]"));
         }
      }
      /* 更新 */
      else if(strtoupper($this->params['form']['submit'])  ==  "UPDATE")
     {
     	 $this->data['VendorMst']['upd_nm'] = $this->Auth->user('username');
         $this->data['VendorMst']['upd_dt'] = date('Y-m-d H:i:s');
         if($this->VendorMst->save($this->data['VendorMst'])==false){
 	    	return json_encode(array('result'=>false,'message'=>"更新に失敗しました。",'reason'=>$this->VendorMst->getDbo()->error."[".date('Y-m-d H:i:s')."]"));
 	     }

      /* 異常パラメーター */
      }else{
      	    return json_encode(array('result'=>false,'message'=>"処理が失敗しました。",'reason'=>"予期しないコード[".$this->params['form']['submit']."]です。 "));
      }
        $tr->commit();
        return json_encode(array('result'=>true,'message'=>'処理完了しました。'));
    }else{

 	  //ベンダーマスタのデータを取得
 	  $this->set("data",$this->VendorMst->findById($id));
 	  //ベンダー区分リストの取得
 	  $this->set("vendor_kbn_list",$this->VendorKbnMst->find('all'));
      $this->set("hasChild",$this->GoodsMst->find('count',array('conditions'=>array('vendor_id'=>$id))) > 0 ? true:false);
 	  $this->set("menu_customers","");
 	  $this->set("menu_customer","disable");
 	  $this->set("menu_fund","");

 	  $this->set("sub_title","ベンダー編集");
 	  $this->set("user",$this->Auth->user());
    }
 }

}
?>