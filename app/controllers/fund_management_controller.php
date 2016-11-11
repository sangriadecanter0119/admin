<?php
class FundManagementController extends AppController
{

 public $name = 'FundManagement';
 public $uses = array('FundManagementTrnView','FundManagementTrn','CustomerMst','ContractTrnView');
 public $layout = 'fund_management_main_tab';
 public $components = array('Auth','RequestHandler');
 public $helpers = array('Html','common','Javascript');

 /**
  *
  * 資金管理一覧フォーム表示
  */
 function index()
 {
     $wedding_dt = null;
 	 /* フォームからフィルタ条件を送信された場合(POST) */
 	 if (!empty($this->data)) {
 	 	$wedding_dt = $this->data['GoodsMstView']['wedding_planned_dt'];
 	 	$this->Session->write("filter_wedding_dt",$wedding_dt);
 	 }
	 /* デフォルト値 :処理年月に挙式予定の成約の顧客を表示 */
	 else{
	 	 if($this->Session->read("filter_wedding_dt") == null){
	 	 	$this->Session->write("filter_wedding_dt",date("Y-m"));
	 	 	$wedding_dt = date("Y-m");
	 	 }else{
	 	 	$wedding_dt = $this->Session->read("filter_wedding_dt");
	 	 }
	}

 	//資金管理一覧を取得
 	$this->set('data',$this->FundManagementTrnView->findAllByWeddingDateInInvoiced($wedding_dt));
 	/* 成約年月一覧を取得 */
 	$this->set("wedding_dt_list",$this->ContractTrnView->getGroupOfWeddingMonthInInvoiced());
 	/* フィルタ条件をVIEWで保持する */
 	$this->set("wedding_dt" ,$this->Session->read("filter_wedding_dt"));

 	$this->set("menu_customers","");
 	$this->set("menu_customer","disable");
 	$this->set("menu_fund","current");

 	$this->set("sub_menu_bank","");
 	$this->set("sub_menu_sales","");
 	$this->set("sub_menu_fund","current");
 	$this->set("sub_menu_remittance","");
 	$this->set("sub_menu_payment","");
 	$this->set("sub_menu_vendor_sales","");

 	$this->set("sub_title","資金管理");
 	$this->set("user",$this->Auth->user());
 }

 /**
  *
  * 更新処理開始
  * @param $fund_management_id
  */
 function edit($fund_management_id)
 {
 	if(!empty($this->data))
 	{
 		 $tr = ClassRegistry::init('TransactionManager');
	     $tr->begin();

	     $this->layout = '';
 	     $this->autoRender =false;
 	     configure::write('debug', 0);

 		 //DATE型で内容が空の場合はNULLを代入
 		 $this->data['FundManagementTrn']['wedding_dt'] = empty($this->data['FundManagementTrn']['wedding_dt']) ? null : $this->data['FundManagementTrn']['wedding_dt'];
 		 $this->data['FundManagementTrn']['wedding_deposit_dt'] = empty($this->data['FundManagementTrn']['wedding_deposit_dt']) ? null : $this->data['FundManagementTrn']['wedding_deposit_dt'];
 		 $this->data['FundManagementTrn']['wedding_fee_dt'] = empty($this->data['FundManagementTrn']['wedding_fee_dt']) ? null : $this->data['FundManagementTrn']['wedding_fee_dt'];
 		 $this->data['FundManagementTrn']['church_deposit_dt'] = empty($this->data['FundManagementTrn']['church_deposit_dt']) ? null : $this->data['FundManagementTrn']['church_deposit_dt'];
 		 $this->data['FundManagementTrn']['party_deposit_dt'] = empty($this->data['FundManagementTrn']['party_deposit_dt']) ? null : $this->data['FundManagementTrn']['party_deposit_dt'];
 		 $this->data['FundManagementTrn']['visionari_deposit_dt'] = empty($this->data['FundManagementTrn']['visionari_deposit_dt']) ? null : $this->data['FundManagementTrn']['visionari_deposit_dt'];
 		 $this->data['FundManagementTrn']['travel_invoice'] = empty($this->data['FundManagementTrn']['travel_invoice']) ? null : $this->data['FundManagementTrn']['travel_invoice'];
 		 $this->data['FundManagementTrn']['travel_fee_dt'] = empty($this->data['FundManagementTrn']['travel_fee_dt']) ? null : $this->data['FundManagementTrn']['travel_fee_dt'];
 		 $this->data['FundManagementTrn']['dress_invoice'] = empty($this->data['FundManagementTrn']['dress_invoice']) ? null : $this->data['FundManagementTrn']['dress_invoice'];
 		 $this->data['FundManagementTrn']['dress_fee_dt'] = empty($this->data['FundManagementTrn']['dress_fee_dt']) ? null : $this->data['FundManagementTrn']['dress_fee_dt'];
 		 $this->data['FundManagementTrn']['album_fee_dt'] = empty($this->data['FundManagementTrn']['album_fee_dt']) ? null : $this->data['FundManagementTrn']['album_fee_dt'];
 		 $this->data['FundManagementTrn']['beauty_invoice'] = empty($this->data['FundManagementTrn']['beauty_invoice']) ? null : $this->data['FundManagementTrn']['beauty_invoice'];
 		 $this->data['FundManagementTrn']['beauty_fee_dt'] = empty($this->data['FundManagementTrn']['beauty_fee_dt']) ? null : $this->data['FundManagementTrn']['beauty_fee_dt'];
 		 $this->data['FundManagementTrn']['cosmetic_invoice'] = empty($this->data['FundManagementTrn']['cosmetic_invoice']) ? null : $this->data['FundManagementTrn']['cosmetic_invoice'];
 		 $this->data['FundManagementTrn']['cosmetic_fee_dt'] = empty($this->data['FundManagementTrn']['cosmetic_fee_dt']) ? null : $this->data['FundManagementTrn']['cosmetic_fee_dt'];
 		 $this->data['FundManagementTrn']['dental_invoice'] = empty($this->data['FundManagementTrn']['etc3_dt']) ? null : $this->data['FundManagementTrn']['dental_invoice'];
 		 $this->data['FundManagementTrn']['dental_fee_dt'] = empty($this->data['FundManagementTrn']['dental_fee_dt']) ? null : $this->data['FundManagementTrn']['dental_fee_dt'];
 		 $this->data['FundManagementTrn']['goods_invoice'] = empty($this->data['FundManagementTrn']['goods_invoice']) ? null : $this->data['FundManagementTrn']['goods_invoice'];
 		 $this->data['FundManagementTrn']['goods_fee_dt'] = empty($this->data['FundManagementTrn']['goods_fee_dt']) ? null : $this->data['FundManagementTrn']['goods_fee_dt'];
 		 $this->data['FundManagementTrn']['kickback_dt'] = empty($this->data['FundManagementTrn']['kickback_dt']) ? null : $this->data['FundManagementTrn']['kickback_dt'];
 		 $this->data['FundManagementTrn']['etc1_dt'] = empty($this->data['FundManagementTrn']['etc1_dt']) ? null : $this->data['FundManagementTrn']['etc1_dt'];
 		 $this->data['FundManagementTrn']['etc2_dt'] = empty($this->data['FundManagementTrn']['etc2_dt']) ? null : $this->data['FundManagementTrn']['etc2_dt'];
 		 $this->data['FundManagementTrn']['etc3_dt'] = empty($this->data['FundManagementTrn']['etc3_dt']) ? null : $this->data['FundManagementTrn']['etc3_dt'];

 		 //3桁区切りのカンマを除去
 		 $this->data['FundManagementTrn']['wedding_deposit']= str_replace(",","",$this->data['FundManagementTrn']['wedding_deposit']);
 		 $this->data['FundManagementTrn']['wedding_fee']= str_replace(",","",$this->data['FundManagementTrn']['wedding_fee']);
 		 $this->data['FundManagementTrn']['church_deposit']= str_replace(",","",$this->data['FundManagementTrn']['church_deposit']);
 		 $this->data['FundManagementTrn']['party_deposit']= str_replace(",","",$this->data['FundManagementTrn']['party_deposit']);
 		 $this->data['FundManagementTrn']['visionari_deposit']= str_replace(",","",$this->data['FundManagementTrn']['visionari_deposit']);
 		 $this->data['FundManagementTrn']['travel_fee']= str_replace(",","",$this->data['FundManagementTrn']['travel_fee']);
 		 $this->data['FundManagementTrn']['dress_fee']= str_replace(",","",$this->data['FundManagementTrn']['dress_fee']);
 		 $this->data['FundManagementTrn']['album_fee']= str_replace(",","",$this->data['FundManagementTrn']['album_fee']);
 		 $this->data['FundManagementTrn']['beauty_fee']= str_replace(",","",$this->data['FundManagementTrn']['beauty_fee']);
 		 $this->data['FundManagementTrn']['cosmetic_fee']= str_replace(",","",$this->data['FundManagementTrn']['cosmetic_fee']);
 		 $this->data['FundManagementTrn']['dental_fee']= str_replace(",","",$this->data['FundManagementTrn']['dental_fee']);
 		 $this->data['FundManagementTrn']['goods_fee']= str_replace(",","",$this->data['FundManagementTrn']['goods_fee']);
 		 $this->data['FundManagementTrn']['kickback_fee']= str_replace(",","",$this->data['FundManagementTrn']['kickback_fee']);
 		 $this->data['FundManagementTrn']['etc1_fee']= str_replace(",","",$this->data['FundManagementTrn']['etc1_fee']);
 		 $this->data['FundManagementTrn']['etc2_fee']= str_replace(",","",$this->data['FundManagementTrn']['etc2_fee']);
 		 $this->data['FundManagementTrn']['etc3_fee']= str_replace(",","",$this->data['FundManagementTrn']['etc3_fee']);

 		 $this->data['FundManagementTrn']['id'] = $fund_management_id;
 		 $this->data['FundManagementTrn']['upd_nm'] = $this->Auth->user('username');
 		 $this->data['FundManagementTrn']['upd_dt'] =date('Y-m-d H:i:s');

 	     if($this->FundManagementTrn->save($this->data)==false){
 	     	 return json_encode(array('result'=>false,'message'=>"資金管理情報更新に失敗しました。",'reason'=>$this->FundManagementTrn->getDbo()->error."[".date('Y-m-d H:i:s')."]"));
 	     }
         $tr->commit();
         return json_encode(array('result'=>true,'message'=>'処理完了しました。'));

 	}else{

 	   $this->layout = 'customer_edit_mode_default';
 	   $data = $this->FundManagementTrnView->findById($fund_management_id);
       $this->set("data",$data);

 	   //新郎新婦の名前をセット
       $this->set("broom",$data['FundManagementTrnView']['grmls_kj'].$data['FundManagementTrnView']['grmfs_kj'] );
       $this->set("bride",$data['FundManagementTrnView']['brdfs_kj']);

 	   $this->set("menu_customers","");
 	   $this->set("menu_customer","disable");
 	   $this->set("menu_fund","current");

 	   $this->set("sub_title","");
 	   $this->set("user",$this->Auth->user());
 	}
 }
}
?>