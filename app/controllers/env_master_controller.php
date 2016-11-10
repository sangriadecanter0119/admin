<?php
class EnvMasterController extends AppController
{
 public $name = 'EnvMaster';
 public $uses = array('EnvMst');
 public $layout = 'edit_mode';
 public $components = array('Auth');
 public $helpers = array('common');

  /**
  *
  * 環境設定一覧画面表示
  */
 function index()
 {
 	$this->set("data",$this->EnvMst->find('first'));

 	$this->set("menu_customers","");
 	$this->set("menu_customer","disable");
 	$this->set("menu_fund","");

 	$this->set("sub_title","環境設定管理");
 	$this->set("user",$this->Auth->user());
 }

 /**
  * 環境設定編集画面表示及び実行
  */
 function editEnv()
 {
 	if(!empty($this->data))
 	{
 		$tr = ClassRegistry::init('TransactionManager');
	    $tr->begin();

	    $this->layout = '';
 	    $this->autoRender =false;
 	    configure::write('debug', 0);

         $this->data['EnvMst']['hawaii_tax_rate'] = $this->data['EnvMst']['hawaii_tax_rate'] /100;
	     $this->data['EnvMst']['discount_aw_share'] = $this->data['EnvMst']['discount_aw_share'] /100;
	     $this->data['EnvMst']['discount_rw_share'] = $this->data['EnvMst']['discount_rw_share'] /100;
 		 $this->data['EnvMst']['upd_nm'] = $this->Auth->user('username');
         $this->data['EnvMst']['upd_dt'] = date('Y-m-d H:i:s');

    	 if($this->EnvMst->save($this->data['EnvMst'])==false){
 	    	return json_encode(array('result'=>false,'message'=>"更新に失敗しました。",'reason'=>$this->EnvMst->getDbo()->error."[".date('Y-m-d H:i:s')."]"));
 	     }
 	    $tr->commit();
        return json_encode(array('result'=>true,'message'=>'処理完了しました。'));

 	}else{
 	  //環境設定リスト
 	  $this->set("data",$this->EnvMst->find('first'));

 	   $this->set("menu_customers","");
 	   $this->set("menu_customer","disable");
 	   $this->set("menu_fund","");

 	  $this->set("sub_title","環境設定編集");
 	  $this->set("user",$this->Auth->user());
 	}
 }
}
?>