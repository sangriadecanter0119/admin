<?php
class VendorCategoryMasterController extends AppController
{
 public $name = 'VendorCategoryMaster';
 public $uses = array('VendorKbnMst','VendorMst');
 public $layout = 'edit_mode';
 public $components = array('Auth');
 public $helpers = array('common');

 /**
  * ベンダー区分一覧画面表示
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
 	    'VendorKbnMst' => array(
		                  'limit' => '30',
		                  'sort' => $sort,
 	                      )
	 );
 	$this->set("data",$this->paginate('VendorKbnMst'));

 	$this->set("menu_customers","");
 	$this->set("menu_customer","disable");
 	$this->set("menu_fund","");

 	$this->set("sub_title","ベンダー区分管理");
 	$this->set("user",$this->Auth->user());
 }

 /**
  * ベンダー区分登録画面表示及び実行
  */
 function addVendorCategory()
 {
 	if(!empty($this->data))
 	{
 	   $tr = ClassRegistry::init('TransactionManager');
	   $tr->begin();

	   $this->layout = '';
 	   $this->autoRender =false;
 	   configure::write('debug', 0);

 	   $this->data['VendorKbnMst']['id'] = null;
 	   $this->data['VendorKbnMst']['reg_nm'] =$this->Auth->user('username');
       $this->data['VendorKbnMst']['reg_dt'] = date('Y-m-d H:i:s');
 	   if($this->VendorKbnMst->save($this->data)){
 	   	 $tr->commit();
  	     return json_encode(array('result'=>true,'message'=>'登録完了しました。'));
 	  }else{
 	   	 return json_encode(array('result'=>false,'message'=>"登録に失敗しました。",'reason'=>$this->VendorKbnMst->getDbo()->error."[".date('Y-m-d H:i:s')."]"));
 	  }

 	}else{

 	  $this->set("menu_customers","");
 	  $this->set("menu_customer","disable");
 	  $this->set("menu_fund","");

   	  $this->set("sub_title","ベンダー区分追加");
 	  $this->set("user",$this->Auth->user());
 	}
 }

 /**
  * ベンダー区分編集・削除画面表示及び実行
  */
 function editVendorCategory($id=null)
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
          if($this->VendorKbnMst->delete($this->data['VendorKbnMst']['id'])==false){
        	  return json_encode(array('result'=>false,'message'=>"削除に失敗しました。",'reason'=>$this->VendorKbnMst->getDbo()->error."[".date('Y-m-d H:i:s')."]"));
         }
      }
      /* 更新 */
      else if(strtoupper($this->params['form']['submit'])  ==  "UPDATE")
     {
     	 $this->data['VendorKbnMst']['upd_nm'] = $this->Auth->user('username');
         $this->data['VendorKbnMst']['upd_dt'] = date('Y-m-d H:i:s');
         if($this->VendorKbnMst->save($this->data['VendorKbnMst'])==false){
 	    	return json_encode(array('result'=>false,'message'=>"更新に失敗しました。",'reason'=>$this->VendorKbnMst->getDbo()->error."[".date('Y-m-d H:i:s')."]"));
 	     }

      /* 異常パラメーター */
      }else{
      	    return json_encode(array('result'=>false,'message'=>"処理が失敗しました。",'reason'=>"予期しないコード[".$this->params['form']['submit']."]です。 "));
      }
         $tr->commit();
         return json_encode(array('result'=>true,'message'=>'処理完了しました。'));
    }else{

 	  $this->set("data", $this->VendorKbnMst->findById($id));
 	  $this->set("hasChild",$this->VendorMst->find('count',array('conditions'=>array('vendor_kbn_id'=>$id))) > 0 ? true:false);
 	  $this->set("menu_customers","");
 	  $this->set("menu_customer","disable");
 	  $this->set("menu_fund","");

 	  $this->set("sub_title","業種区分編集");
 	  $this->set("user",$this->Auth->user());
    }
 }
}
?>