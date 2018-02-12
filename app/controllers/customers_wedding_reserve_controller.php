<?php
class CustomersWeddingReserveController extends AppController
{

 public $name = 'CustomersWeddingReserve';
 public $uses = array('WeddingReservingStateService','WeddingReservingStateTrnView','CustomerMst');
 public $layout = 'cust_list_info_main_tab';
 public $components = array('Auth','RequestHandler');
 public $helpers = array('Html','common','Javascript');

 function index($page_limit=20)
 {
 	//フィルタ条件の変更時
 	if (!empty($this->data)) {

 		$this->Session->write('filter_ws_wedding_place'       ,$this->data['WeddingReservingStateTrnView']['wedding_place']);
 		$this->Session->write('filter_ws_first_contact_person',$this->data['WeddingReservingStateTrnView']['first_contact_person_nm']);
 		$this->Session->write('filter_ws_process_person'      ,$this->data['WeddingReservingStateTrnView']['process_person_nm']);
 		$this->Session->write('filter_ws_hotel'               ,$this->data['WeddingReservingStateTrnView']['wedding_day_hotel']);
 		$this->Session->write('filter_ws_reception_place'     ,$this->data['WeddingReservingStateTrnView']['reception_place']);
 		$this->Session->write('filter_ws_camera'              ,$this->data['WeddingReservingStateTrnView']['camera']);
 		$this->Session->write('filter_ws_hairmake'            ,$this->data['WeddingReservingStateTrnView']['hair_make']);
 		$this->Session->write('filter_ws_video'               ,$this->data['WeddingReservingStateTrnView']['video']);
 		$this->Session->write('filter_ws_flower'              ,$this->data['WeddingReservingStateTrnView']['flower']);
 		$this->Session->write('filter_ws_attend'              ,$this->data['WeddingReservingStateTrnView']['attend']);
 		$this->Session->write('filter_ws_wedding_dt_from'     ,$this->data['WeddingReservingStateTrnView']['wedding_dt_from']);
 		$this->Session->write('filter_ws_wedding_dt_to'       ,$this->data['WeddingReservingStateTrnView']['wedding_dt_to']);
 		$this->Session->write('filter_ws_page_limit'          ,$page_limit);

 	//初回表示時
 	}elseif ($this->Session->read('filter_ws_wedding_place') == null){

 		$this->Session->write('filter_ws_wedding_place'       ,-1);
 		$this->Session->write('filter_ws_first_contact_person',-1);
 		$this->Session->write('filter_ws_process_person'      ,-1);
 		$this->Session->write('filter_ws_hotel'               ,-1);
 		$this->Session->write('filter_ws_reception_place'     ,-1);
 		$this->Session->write('filter_ws_camera'              ,-1);
 		$this->Session->write('filter_ws_hairmake'            ,-1);
 		$this->Session->write('filter_ws_video'               ,-1);
 		$this->Session->write('filter_ws_flower'              ,-1);
 		$this->Session->write('filter_ws_attend'              ,-1);
 		$this->Session->write('filter_ws_wedding_dt_from'     ,date('Y').'-01');
 		$this->Session->write('filter_ws_wedding_dt_to'       ,date('Y').'-12');
 		$this->Session->write('filter_ws_page_limit'          ,$page_limit);

 	//初回表示済みで他画面から遷移
 	}else{

 	}

 	//フィルター条件設定
 	$search  = array("SUBSTR(wedding_dt,1,7) >="=>$this->Session->read('filter_ws_wedding_dt_from'));
 	$search += array("SUBSTR(wedding_dt,1,7) <="=>$this->Session->read('filter_ws_wedding_dt_to'));

 	if($this->Session->read('filter_ws_wedding_place') != -1){
 		$search += array("wedding_place"=>$this->Session->read('filter_ws_wedding_place'));
 	}
 	if($this->Session->read('filter_ws_first_contact_person') != -1){
 		$search += array("first_contact_person_nm"=>$this->Session->read('filter_ws_first_contact_person'));
 	}
 	if($this->Session->read('filter_ws_process_person') != -1){
 		$search += array("process_person_nm"=>$this->Session->read('filter_ws_process_person'));
 	}
 	if($this->Session->read('filter_ws_hotel') != -1){
 		$search += array("wedding_day_hotel"=>$this->Session->read('filter_ws_hotel'));
 	}
 	if($this->Session->read('filter_ws_reception_place') != -1){
 		$search += array("reception_place"=>$this->Session->read('filter_ws_reception_place'));
 	}
 	if($this->Session->read('filter_ws_camera') != -1){
 		$search += array("camara"=>$this->Session->read('filter_ws_camera'));
 	}
 	if($this->Session->read('filter_ws_hairmake') != -1){
 		$search += array("hair_make"=>$this->Session->read('filter_ws_hairmake'));
 	}
 	if($this->Session->read('filter_ws_video') != -1){
 		$search += array("video"=>$this->Session->read('filter_ws_video'));
 	}
 	if($this->Session->read('filter_ws_flower') != -1){
 		$search += array("flower"=>$this->Session->read('filter_ws_flower'));
 	}
 	if($this->Session->read('filter_ws_attend') != -1){
 		$search += array("attend"=>$this->Session->read('filter_ws_attend'));
 	}

 	$this->paginate = array(
 			'limit' =>$this->Session->read('filter_ws_page_limit'),
 			'order' =>'WeddingReservingStateTrnView.wedding_dt'
 	);

 	$this->set("data",$this->paginate('WeddingReservingStateTrnView',$search));

 	//フィルタ条件をVIEWで保持する
 	$this->set("filter_ws_wedding_place"        ,$this->Session->read('filter_ws_wedding_place'));
 	$this->set("filter_ws_first_contact_person" ,$this->Session->read('filter_ws_first_contact_person'));
 	$this->set("filter_ws_process_person"       ,$this->Session->read('filter_ws_process_person'));
 	$this->set("filter_ws_hotel"                ,$this->Session->read('filter_ws_hotel'));
 	$this->set("filter_ws_reception_place"      ,$this->Session->read('filter_ws_reception_place'));
 	$this->set("filter_ws_camera"               ,$this->Session->read('filter_ws_camera'));
 	$this->set("filter_ws_hairmake"             ,$this->Session->read('filter_ws_hairmake'));
 	$this->set("filter_ws_video"                ,$this->Session->read('filter_ws_video'));
 	$this->set("filter_ws_flower"               ,$this->Session->read('filter_ws_flower'));
 	$this->set("filter_ws_attend"               ,$this->Session->read('filter_ws_attend'));
 	$this->set("filter_ws_wedding_dt_from"      ,$this->Session->read('filter_ws_wedding_dt_from'));
 	$this->set("filter_ws_wedding_dt_to"        ,$this->Session->read('filter_ws_wedding_dt_to'));
 	$this->set("page_limit"                     ,$this->Session->read('filter_ws_page_limit'));

 	//フィルターリスト
 	$this->set("list_wedding_place"       ,$this->WeddingReservingStateService->getWeddingList());
 	$this->set("list_first_contact_person",$this->WeddingReservingStateService->getFirstContactPersonList());
 	$this->set("list_process_person"      ,$this->WeddingReservingStateService->getProcessPersonList());
 	$this->set("list_hotel"               ,$this->WeddingReservingStateService->getHotelList());
 	$this->set("list_reception_place"     ,$this->WeddingReservingStateService->getReceptionPlaceList());
 	$this->set("list_camera"              ,$this->WeddingReservingStateService->getCameraList());
 	$this->set("list_hairmake"            ,$this->WeddingReservingStateService->getHairmakeList());
 	$this->set("list_video"               ,$this->WeddingReservingStateService->getVideoList());
 	$this->set("list_flower"              ,$this->WeddingReservingStateService->getFlowerList());
 	$this->set("list_attend"              ,$this->WeddingReservingStateService->getAttendList());

 	//導線1リスト
 	$this->set("leading1_list",$this->CustomerMst->getLeading1List());
 	//導線2リスト
 	$this->set("leading2_list",$this->CustomerMst->getLeading2List());

 	$this->set("menu_customers","current");
 	$this->set("menu_customer","disable");
 	$this->set("menu_fund","");

 	$this->set("sub_menu_customers_list","");
 	$this->set("sub_menu_customers_company_contact","");
 	$this->set("sub_menu_customers_wedding_reserve","current");
 	$this->set("sub_menu_customers_schedules","");
 	$this->set("sub_menu_customers_by_each_attendant_list","");
 	$this->set("sub_menu_customers_contract_list","");
 	$this->set("sub_menu_attendant_state","");
 	$this->set("sub_menu_wedding_reservations","");

 	$this->set("sub_title","挙式予約状況");
 	$this->set("user",$this->Auth->user());
 }

 function edit($id=null)
 {
 	$customer_id = $this->Session->read('customer_id');

 	if(!empty($this->data)){

 		$this->layout = '';
 		$this->autoRender =false;
 		configure::write('debug', 0);

// 		0000-00-00対策
// 		if(empty($this->data['EstimateTrn']['tts_rate_dt'])){ $this->data['EstimateTrn']['tts_rate_dt'] = null; }
// 		for($i=1;$i <= count($this->data['EstimateDtlTrn']);$i++){
// 		入金フラグ要素がない場合はチェックを外す
// 		if(array_key_exists('money_received_flg',$this->data['EstimateDtlTrn'][$i])==false){ $this->data['EstimateDtlTrn'][$i]['money_received_flg'] = 0 ;}
// 		}

 		$ret = $this->WeddingReservingStateService->Edit($this->data,$this->Auth->user('username'));
 		if($ret['result']==false){	return json_encode($ret); }
 		return json_encode(array('result'=>true,'message'=>'更新が完了しました。'));

 	}else{

 		$data = $this->WeddingReservingStateService->FindById($id);
 		$this->set("data",$data);

 		$this->set("broom",($data['WeddingReservingStateTrnView']['prm_lastname_flg'] == 0 ?
 				$data['WeddingReservingStateTrnView']['grmls_kj'] :
 				$data['WeddingReservingStateTrnView']['brdls_kj']).$data['WeddingReservingStateTrnView']['grmfs_kj'] );
 		$this->set("bride",$data['WeddingReservingStateTrnView']['brdfs_kj']);

 		$this->set("menu_customers","");
 		$this->set("menu_customer","current");
 		$this->set("menu_fund","");

 		$this->set("sub_title","挙式予約状況編集");
 		$this->set("user",$this->Auth->user());
 		$this->layout = 'edit_mode';
 	}
 }

 function import()
 {
 	$this->layout = '';
 	$this->autoRender =false;
 	configure::write('debug', 0);

 	$ret = $this->WeddingReservingStateService->Import();
 	if($ret['result']==false){	return json_encode($ret); }
 	return json_encode(array('result'=>true,'message'=>'処理完了しました。'));
 }

 /**
  * EXCEL出力
  * @param unknown $file_type
  */
 function export()
 {
 	$save_filename = "挙式予約状況.xlsx";
 	$temp_filename = "customers_wedding_reserve_template.xlsx";
 	$render_name = "excel_customers_wedding_reserve";
 	$sheet_name = "2015";

 	//導線1リスト
 	$this->set("leading1_list",$this->CustomerMst->getLeading1List());
 	//導線2リスト
 	$this->set("leading2_list",$this->CustomerMst->getLeading2List());

 	//$this->set("customers",$this->CustomerMstView->find('all',array('conditions'=>$search,'order'=>$order)));

 	$this->layout = false;

 	$this->set( "sheet_name", $sheet_name);
 	$this->set( "filename", $save_filename );
 	$this->set( "template_file", $temp_filename);
 	$this->render($render_name);
 }

	/**
	 *
	 * [AJAX]ファイルアップロード画面を表示する
	 */
	function fileUploadForm() {
		if (!$this->RequestHandler->isAjax()){ $this->cakeError('error404'); }
		configure::write('debug', 0);
		$this->layout = '';
	}

	/**
	 * ファイルの取り込み
	 */
	function uploadFile()
	{
		$this->layout = "";

		if (is_uploaded_file($this->data['ImgForm']['ImgFile']['tmp_name']) && end(explode(".",$this->data['ImgForm']['ImgFile']['name'])) == "csv") {

			$result = $this->WeddingReservingStateService->CreateAndImport($this->data['ImgForm']['ImgFile']['tmp_name']);
			if($result['result']){
				$this->set("msg",json_encode(array('data'=>array('isSuccess'=>"true" ,'message'=>"ファイル取り込みに成功しました。"))));
			}else{
				$this->set("msg",json_encode(array('data'=>array('isSuccess'=>"false", 'message'=> $result["message"],'reason'=> $result["reason"]))));
			}
		}else{
			$this->set("msg",json_encode(array('data'=>array('isSuccess'=>"false", 'message'=> "ファイルの種類が違います。(CSVファイル)　　ファイルサイズの上限は128Mです。"))));
		}
	}
}
?>
