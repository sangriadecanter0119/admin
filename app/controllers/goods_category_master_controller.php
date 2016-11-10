<?php
class GoodsCategoryMasterController extends AppController
{
 public $name = 'GoodsCategoryMaster';
 public $uses = array('GoodsCtgMst');
 public $layout = 'edit_mode';
 public $components = array('Auth');
 public $helpers = array('common');

 /**
  * 商品カテゴリ一覧画面表示
  */
 function index()
 {
 	$this->set("data",$this->GoodsCtgMst->find('all'));

 	$this->set("menu_customers","");
 	$this->set("menu_customer","disable");
 	$this->set("menu_fund","");

 	$this->set("sub_title","商品分類管理");
 	$this->set("user",$this->Auth->user());
 }

 /**
  * 商品カテゴリ登録画面表示及び実行
  */
 function addGoodsCategory()
 {
 	if(!empty($this->data))
 	{
 	  $tr = ClassRegistry::init('TransactionManager');
	  $tr->begin();

	  $this->layout = '';
 	  $this->autoRender =false;
 	  configure::write('debug', 0);

 	  $this->data['GoodsCtgMst']['id'] = null;
 	  $this->data['GoodsCtgMst']['reg_nm'] = $this->Auth->user('username');
      $this->data['GoodsCtgMst']['reg_dt'] = date('Y-m-d H:i:s');

       if($this->GoodsCtgMst->save($this->data)){
 	  	 $tr->commit();
  	     return json_encode(array('result'=>true,'message'=>'登録完了しました。'));
 	  }else{
 	   	 return json_encode(array('result'=>false,'message'=>"登録に失敗しました。",'reason'=>$this->GoodsCtgMst->getDbo()->error."[".date('Y-m-d H:i:s')."]"));
 	  }
 	}else{

 	  $this->set("menu_customers","");
 	  $this->set("menu_customer","disable");
 	  $this->set("menu_fund","");

   	  $this->set("sub_title","商品分類追加");
 	  $this->set("user",$this->Auth->user());
 	}
 }

 /**
  * 商品カテゴリ編集・削除画面表示及び実行
  */
 function editGoodsCategory($id=null)
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
         if($this->GoodsCtgMst->delete($this->data['GoodsCtgMst']['id'])==false){
         	  return json_encode(array('result'=>false,'message'=>"削除に失敗しました。",'reason'=>$this->GoodsCtgMst->getDbo()->error."[".date('Y-m-d H:i:s')."]"));
         }
      }
      /* 更新 */
      else if(strtoupper($this->params['form']['submit'])  ==  "UPDATE")
      {
     	$this->data['GoodsCtgMst']['upd_nm'] = $this->Auth->user('username');
        $this->data['GoodsCtgMst']['upd_dt'] = date('Y-m-d H:i:s');

        if($this->GoodsCtgMst->save($this->data['GoodsCtgMst'])==false){
 	    	return json_encode(array('result'=>false,'message'=>"更新に失敗しました。",'reason'=>$this->GoodsCtgMst->getDbo()->error."[".date('Y-m-d H:i:s')."]"));
 	    }
      }
      /* 異常パラメーター */
      else{
         return json_encode(array('result'=>false,'message'=>"処理が失敗しました。",'reason'=>"予期しないコード[".$this->params['form']['submit']."]です。 "));
      }
 	     $tr->commit();
         return json_encode(array('result'=>true,'message'=>'処理完了しました。'));
 	}else{

 	  $this->set("menu_customers","");
 	  $this->set("menu_customer","disable");
 	  $this->set("menu_fund","");

 	  $this->set("data",$this->GoodsCtgMst->findById($id));

 	  $this->set("sub_title","商品分類編集");
 	  $this->set("user",$this->Auth->user());
 	}
 }
}
?>