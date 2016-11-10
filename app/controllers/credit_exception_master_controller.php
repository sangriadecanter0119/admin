<?php
class CreditExceptionMasterController extends AppController
{
 public $name = 'CreditExceptionMaster';
 public $uses = array('CreditExceptionMstView','CreditExceptionService');
 public $layout = 'edit_mode';
 public $components = array('Auth');
 public $helpers = array('common');

 /**
  * 入金例外マスタ一覧画面表示
  */
 function index()
 {
 	$this->set("data",$this->CreditExceptionMstView->find('all'));

 	$this->set("menu_customers","");
 	$this->set("menu_customer","disable");
 	$this->set("menu_fund","");

 	$this->set("sub_title","入金例外管理");
 	$this->set("user",$this->Auth->user());
 }

 /**
  * 入金例外マスタ登録画面表示及び実行
  */
 function addCreditException()
 {
 	if(!empty($this->data))
 	{
	  $this->layout = '';
 	  $this->autoRender =false;
 	  configure::write('debug', 0);

      $result = $this->CreditExceptionService->register($this->data,$this->Auth->user('username'));
 	  if($result['result']){
 	  	return json_encode(array('result'=>true,'message'=>'登録完了しました。'));
 	  }else{
 	  	return json_encode($result);
 	  }
 	}else{

 	  $this->set("menu_customers","");
 	  $this->set("menu_customer","disable");
 	  $this->set("menu_fund","");

   	  $this->set("sub_title","入金例外追加");
 	  $this->set("user",$this->Auth->user());
 	}
 }

 /**
  * 入金例外マスタ編集・削除画面表示及び実行
  */
 function editCreditException($id=null)
 {
 	if(!empty($this->data))
 	{
	   $this->layout = 'ajax';
 	   $this->autoRender =false;
 	   configure::write('debug', 0);

      /* 削除 */
      if(strtoupper($this->params['form']['submit'])  ==  "DELETE")
      {
         $ret = $this->CreditExceptionService->delete($this->data['CreditExceptionMst']['id'],$this->data['CreditExceptionMst']['customer_id']);
         if($ret['result']==false){  return json_encode($ret); }

      /* 更新 */
      }else if(strtoupper($this->params['form']['submit'])  ==  "UPDATE"){

        $ret = $this->CreditExceptionService->update($this->data['CreditExceptionMst'],$this->Auth->user('username'));
        if($ret['result']==false){  return json_encode($ret); }
      }
      /* 異常パラメーター */
      else{
         return json_encode(array('result'=>false,'message'=>"処理が失敗しました。",'reason'=>"予期しないコード[".$this->params['form']['submit']."]です。 "));
      }
         return json_encode(array('result'=>true,'message'=>'処理完了しました。'));
 	}else{

 	  $this->set("menu_customers","");
 	  $this->set("menu_customer","disable");
 	  $this->set("menu_fund","");

 	  $this->set("data",$this->CreditExceptionMstView->findById($id));

 	  $this->set("sub_title","入金例外編集");
 	  $this->set("user",$this->Auth->user());
 	}
 }
}
?>