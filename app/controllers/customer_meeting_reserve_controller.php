<?php
class CustomerMeetingReserveController extends AppController
{
 public $name = 'CustomerMeetingReserve';
 public $uses = array('CustomerMst','CustomerMstView','CustomerScheduleTrnView','User','CustomerScheduleTrn');
 public $layout = 'cust_indivisual_info_main_tab';
 public $components = array('Auth');
 public $helpers = array('Html','common','Javascript');

 function index()
 {
 	$id = $this->Session->read('customer_id');
 	$data = $this->CustomerMstView->findById($id);
    $this->set(	"data",$data);

    $this->set(	"customer_schedule",$this->CustomerScheduleTrnView->find('all',array('conditions'=>array('customer_id'=>$id))));

    //新郎新婦の名前をセット
    $this->set(	"broom",($data['CustomerMstView']['prm_lastname_flg'] == 0 ? $data['CustomerMstView']['grmls_kj'] : $data['CustomerMstView']['brdls_kj']).$data['CustomerMstView']['grmfs_kj'] );
    $this->set(	"bride",$data['CustomerMstView']['brdfs_kj']);

 	$this->set("menu_customers","");
 	$this->set("menu_customer","current");
 	$this->set("menu_fund","");

 	$this->set("sub_menu_customer_info","");
 	$this->set("sub_menu_customer_meeting","current");
 	$this->set("sub_menu_customer_wedding_reserve","");
 	$this->set("sub_menu_customer_contact","");
 	//$this->set("sub_menu_customer_schedule","");
 	$this->set("sub_menu_customer_estimate","");

 	$this->set("sub_title","来店状況");
 	$this->set("user",$this->Auth->user());
 }

 /**
  *
  * スケジュール登録
  */
function addCustomerSchedule()
 {
 	$this->layout = 'edit_mode';

 	if(!empty($this->data))
 	{
 	   $tr = ClassRegistry::init('TransactionManager');
	   $tr->begin();

	   $this->layout = '';
 	   $this->autoRender =false;
 	   configure::write('debug', 0);

 	    $this->data['CustomerScheduleTrn']['id'] = null;
 	    $dt = $this->data['CustomerScheduleTrn']['start_dt'];
 		$this->CustomerScheduleTrn->create();
 		$this->data['CustomerScheduleTrn']['start_dt'] = $dt." ".$this->data['Tmp']['start_hour'].":".$this->data['Tmp']['start_min'].":00";
 		$this->data['CustomerScheduleTrn']['end_dt']   = $dt." ".$this->data['Tmp']['end_hour'].":".$this->data['Tmp']['end_min'].":00";
 		$this->data['CustomerScheduleTrn']['editable']='1';
 		$this->data['CustomerScheduleTrn']['allday']='1';
 		$this->data['CustomerScheduleTrn']['customer_id']=$this->Session->read('customer_id');
 		$this->data['CustomerScheduleTrn']['reg_nm']=$this->Auth->user('username');
        $this->data['CustomerScheduleTrn']['reg_dt']=date('Y/m/d h:i:s');
 		if($this->CustomerScheduleTrn->save($this->data)==false){
 	   	 return json_encode(array('result'=>false,'message'=>"登録に失敗しました。",'reason'=>$this->CustomerScheduleTrn->getDbo()->error."[".date('Y-m-d H:i:s')."]"));
 	    }
 	  	 $tr->commit();
  	     return json_encode(array('result'=>true,'message'=>'登録完了しました。'));

 	}else{
 	  $id = $this->Session->read('customer_id');
 	  $data = $this->CustomerMst->findById($id);
      //$this->set("data",$data);
      $this->set("user_list",$this->User->find('all',array('conditions'=>array('del_kbn'=>EXISTS))));

      //新郎新婦の名前をセット
      $this->set("broom",($data['CustomerMst']['prm_lastname_flg'] == 0 ? $data['CustomerMst']['grmls_kj'] : $data['CustomerMst']['brdls_kj']).$data['CustomerMst']['grmfs_kj'] );
      $this->set("bride",$data['CustomerMst']['brdfs_kj']);

 	  $this->set("menu_customers","");
 	  $this->set("menu_customer","current");
 	  $this->set("menu_fund","");

 	  $this->set("sub_menu_customer_info","");
 	  $this->set("sub_menu_customer_meeting","current");
 	  $this->set("sub_menu_customer_wedding_reserve","");
 	  $this->set("sub_menu_customer_contact","");
 	  //$this->set("sub_menu_customer_schedule","");
 	  $this->set("sub_menu_customer_estimate","");

 	  $this->set("sub_title","来店予約追加");
 	  $this->set("user",$this->Auth->user());
 	}
 }

 /**
  *
  * スケジュール更新
  * @param $schedule_id
  */
function editCustomerSchedule($schedule_id=null)
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
         if($this->CustomerScheduleTrn->delete($this->data['CustomerScheduleTrn']['id'])==false){
        	  return json_encode(array('result'=>false,'message'=>"削除に失敗しました。",'reason'=>$this->CustomerScheduleTrn->getDbo()->error."[".date('Y-m-d H:i:s')."]"));
         }
      }
      /* 更新 */
      else if(strtoupper($this->params['form']['submit'])  ==  "UPDATE")
      {
      	 $fields = array('start_dt','end_dt','title','note','attend_id','upd_nm','upd_dt');
 		 $dt = $this->data['CustomerScheduleTrn']['start_dt'];
 		 $this->data['CustomerScheduleTrn']['start_dt'] = $dt." ".$this->data['Tmp']['start_hour'].":".$this->data['Tmp']['start_min'].":00";
 		 $this->data['CustomerScheduleTrn']['end_dt']   = $dt." ".$this->data['Tmp']['end_hour'].":".$this->data['Tmp']['end_min'].":00";
         $this->data['CustomerScheduleTrn']['upd_nm'] = $this->Auth->user('username');
         $this->data['CustomerScheduleTrn']['upd_dt'] = date('Y-m-d H:i:s');
 	     if($this->CustomerScheduleTrn->save($this->data['CustomerScheduleTrn'],false,$fields)==false){
 	    	return json_encode(array('result'=>false,'message'=>"更新に失敗しました。",'reason'=>$this->CustomerScheduleTrn->getDbo()->error."[".date('Y-m-d H:i:s')."]"));
 	     }

      /* 異常パラメーター */
      }else{
      	    return json_encode(array('result'=>false,'message'=>"処理が失敗しました。",'reason'=>"予期しないコード[".$this->params['form']['submit']."]です。 "));
      }
        $tr->commit();
        return json_encode(array('result'=>true,'message'=>'処理完了しました。'));
    }else{

 	  if($schedule_id==null){ return; }
 	  $id = $this->Session->read('customer_id');
 	  $data = $this->CustomerMst->findById($id);

      $this->set("user_list",$this->User->find('all'));
      $this->set("sche_list",$this->CustomerScheduleTrnView->findById($schedule_id));

      //新郎新婦の名前をセット
      $this->set("broom",($data['CustomerMst']['prm_lastname_flg'] == 0 ? $data['CustomerMst']['grmls_kj'] : $data['CustomerMst']['brdls_kj']).$data['CustomerMst']['grmfs_kj'] );
      $this->set("bride",$data['CustomerMst']['brdfs_kj']);

 	  $this->set("menu_customers","");
 	  $this->set("menu_customer","current");
 	  $this->set("menu_fund","");

 	  $this->set("sub_menu_customer_info","");
 	  $this->set("sub_menu_customer_meeting","current");
 	  $this->set("sub_menu_customer_wedding_reserve","");
 	  $this->set("sub_menu_customer_contact","");
 	//$this->set("sub_menu_customer_schedule","");
 	  $this->set("sub_menu_customer_estimate","");

 	  $this->set("sub_title","来店予約追加");
 	  $this->set("user",$this->Auth->user());
 	  $this->layout = 'edit_mode';
   }
 }
}
?>