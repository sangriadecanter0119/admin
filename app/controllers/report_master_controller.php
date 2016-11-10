<?php
class ReportMasterController extends AppController
{
 public $name = 'ReportMaster';
 public $uses = array('ReportMst');
 public $layout = 'edit_mode';
 public $components = array('Auth');
 public $helpers = array('common');

 /**
  * レポート編集画面表示
  */
 function index()
 {
 	//暫定で見積PDF帳票構成データを固定で検索
    $this->set('data',$this->ReportMst->find('all',array('conditions'=>array('code'=>'RPT01'))));

 	$this->set("menu_customers","");
 	$this->set("menu_customer","disable");
 	$this->set("menu_fund","");

 	$this->set("sub_title","帳票管理");
 	$this->set("user",$this->Auth->user());
 }


 /**
  * レポート編集画面表示及び実行
  */
 function editReport()
 {
 	if(!empty($this->data))
 	{
 		$tr = ClassRegistry::init('TransactionManager');
	    $tr->begin();

	    $this->layout = '';
 	    $this->autoRender =false;
 	    configure::write('debug', 0);

     	$this->data['ReportMst']['upd_nm'] = $this->Auth->user('username');
        $this->data['ReportMst']['upd_dt'] = date('Y-m-d H:i:s');
 	    if($this->ReportMst->save($this->data)==false){
 	    	return json_encode(array('result'=>false,'message'=>"更新に失敗しました。",'reason'=>$this->ReportMst->getDbo()->error."[".date('Y-m-d H:i:s')."]"));
 	    }
 	    $tr->commit();
        return json_encode(array('result'=>true,'message'=>'処理完了しました。'));
 	}
 }
}
?>