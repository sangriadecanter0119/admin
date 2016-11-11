<?php
class WeddingReservingStateService extends AppModel {
      var $useTable = false;

      /**
       *
       * @param unknown $wedding_dt_from
       * @param unknown $wedding_dt_to
       * @return unknown
       */
      function FindByWeddingDate($wedding_dt_from,$wedding_dt_to){

      	App::import("Model", "WeddingReservingStateTrnView");
      	$da = new WeddingReservingStateTrnView();

      	$data = $da->find("all",array("conditions"=>array("SUBSTR(wedding_dt,1,7)"=>$wedding_dt_from,"SUBSTR(wedding_dt,1,7)"=>$wedding_dt_to),
      			"order"=>array("wedding_dt")));
      	return $data;
      }

      /***
       *
      */
      function GetAll(){

      	App::import("Model", "WeddingReservingStateTrnView");
      	$da = new WeddingReservingStateTrnView();

      	return $da->find("all");
      }

      function FindById($id){

      	App::import("Model", "WeddingReservingStateTrnView");
      	$da = new WeddingReservingStateTrnView();

      	return  $da->findById($id);
      }

      /**
       *
       * @param unknown $updating_data
       * @param unknown $username
       * @return multitype:boolean
       */
      function Edit($updating_data,$username){

      	$tr = ClassRegistry::init('TransactionManager');
      	$tr->begin();

      	App::import("Model", "WeddingReservingStateTrn");
      	$da = new WeddingReservingStateTrn();

      	$updating_data['username'] = $username;
      	$updating_data['upd_dt'] = date('Y-m-d H:i:s');

      	if($da->save($updating_data)==false){
      		return array('result'=>false,'message'=>"挙式予約状況の更新に失敗しました。",'reason'=>$this->getDbo()->error."[".date('Y-m-d H:i:s')."]");
      	}
      	$tr->commit();
      	return array('result'=>true);
      }

      /**
       *
       * @return multitype:boolean string |multitype:boolean
       */
      function Import(){

      	App::import("Model", "WeddingReservingStateTrn");
      	$reserving = new WeddingReservingStateTrn();

      	App::import("Model", "ContractTrn");
      	$contract = new ContractTrn();

		if($reserving->deleteAll(array('1=1'))==false) {
			return array('result' => false, 'message' => "挙式予約状況の取り込みに失敗しました。", 'reason' => $reserving->getDbo()->error . "[" . date('Y-m-d H:i:s') . "]");
		}
		$data = $contract->find("all");

      	for($i=0;$i < count($data);$i++){
      		$new_data = array(
      				"customer_id"=>$data[$i]['ContractTrn']['customer_id'],
      				"reg_nm"=>"admin",
      				"reg_dt"=>date('Y-m-d H:i:s')
      		);
      		$reserving->create();
      		if($reserving->save($new_data)==false){
      			return array('result'=>false,'message'=>"挙式予約状況の取り込みに失敗しました。",'reason'=>$reserving->getDbo()->error."[".date('Y-m-d H:i:s')."]");
      		}
      	}
      	return array('result'=>true);
      }

	function CreateAndImport($filename){

		$tr = ClassRegistry::init('TransactionManager');
		$tr->begin();

		$ret = $this->Import();
		if($ret['result'] == false){return $ret;}

		$ret = $this->getCsvFileInfo($filename);
		if($ret['result'] == false){return $ret;}

        $ret = $this->registerFile($ret['csv']);
		if($ret['result'] == false){return $ret;}

		$tr->commit();
		return $ret;
	}
	/**
	 * CSVファイルを読み込んで挙式情報を取得する
	 * @param unknown $filename
	 * @return multitype:boolean string |multitype:boolean multitype:multitype:unknown NULL
	 */
	function getCsvFileInfo($filename){

		$csv  = array();
		$data = file_get_contents($filename);
		$data = mb_convert_encoding($data, 'UTF-8', 'SJIS');
		$temp = tmpfile();
		fwrite($temp, $data);
		rewind($temp);

		while (($data = fgetcsv($temp, 0, ",")) !== FALSE) {

			if(count($data) >= 17){

				//$customer = $this->getCustomerCodeByKana(mb_convert_kana(preg_replace('/[ ]/', '', $data[4]),"KV"));
				//	if(count($customer)==0){  return array("isSuccess"=>false , "message"=>"顧客名「".mb_convert_kana($data[4],"KV")."」に該当する顧客情報が見つかりませんでした。");   }

				$csv[] = array(
					"customer_cd"=>trim($data[0])    , "camera"=>trim($data[1])         ,"camera_note"=>trim($data[2])   ,"hair_make"=>trim($data[3]),
					"hair_make_note"=>trim($data[4]) ,"hair_make_dt"=>empty($data[5]) ? null:trim($data[5])    ,"video"=>trim($data[6])          ,
					"flower"=>trim($data[7])          ,"attend"=>trim($data[8])          ,"attend_note"=>trim($data[9])    ,
					"briefing_dt"=>empty($data[10]) ? null:trim($data[10])      ,"introducer"=>trim($data[11]),
					"slide_show"=>trim($data[12])    ,"short_film1"=>trim($data[13])   ,"short_film2"=>trim($data[14])  ,"visionari_ss"=>trim($data[15]),
					"visionari_dater"=>trim($data[16])
				);
			}else{
				return array('result'=>false,'message'=>"ファイル取り込みに失敗しました。",
					           'reason'=>'ファイルの列数が足りません。('.count($data).')');
			}
		}
		fclose($temp);
		return array('result'=>true,
			           'csv'=>$csv);
	}

	function registerFile($csv){

		App::import("Model", "WeddingReservingStateTrn");
		$reserving = new WeddingReservingStateTrn();

		App::import("Model", "WeddingReservingStateTrnView");
		$reservingView = new WeddingReservingStateTrnView();

		for($i=0;$i < count($csv);$i++){

			$data = $reservingView->find("all",array('conditions'=>array('customer_cd'=>$csv[$i]['customer_cd'])));

			if(empty($data) || count($data) == 0){
				return array('result'=>false,'message'=>"ファイル取り込みに失敗しました。",'reason'=>'顧客情報('.$csv[$i]['customer_cd'].')が登録されていません。');
			}

			$updating = $reserving->findById($data[0]['WeddingReservingStateTrnView']['id']);
			$updating['WeddingReservingStateTrn']['max_pax'] = $csv[$i]['max_pax'];
			$updating['WeddingReservingStateTrn']['camera'] = $csv[$i]['camera'];
			$updating['WeddingReservingStateTrn']['camera_note'] = $csv[$i]['camera_note'];
			$updating['WeddingReservingStateTrn']['hair_make'] = $csv[$i]['hair_make'];
			$updating['WeddingReservingStateTrn']['hair_make_dt'] = $csv[$i]['hair_make_dt'];
			$updating['WeddingReservingStateTrn']['hair_make_note'] = $csv[$i]['hair_make_note'];
			$updating['WeddingReservingStateTrn']['video'] = $csv[$i]['video'];
			$updating['WeddingReservingStateTrn']['video_note'] = $csv[$i]['video_note'];
			$updating['WeddingReservingStateTrn']['flower'] = $csv[$i]['flower'];
			$updating['WeddingReservingStateTrn']['flower_note'] = $csv[$i]['flower_note'];
			$updating['WeddingReservingStateTrn']['attend'] = $csv[$i]['attend'];
			$updating['WeddingReservingStateTrn']['attend_note'] = $csv[$i]['attend_note'];
			$updating['WeddingReservingStateTrn']['briefing_dt'] = $csv[$i]['briefing_dt'];
			$updating['WeddingReservingStateTrn']['introducer'] = $csv[$i]['introducer'];
			$updating['WeddingReservingStateTrn']['slide_show'] = $csv[$i]['slide_show'];
			$updating['WeddingReservingStateTrn']['short_film1'] = $csv[$i]['short_film1'];
			$updating['WeddingReservingStateTrn']['short_film2'] = $csv[$i]['short_film2'];
			$updating['WeddingReservingStateTrn']['visionari_ss'] = $csv[$i]['visionari_ss'];
			$updating['WeddingReservingStateTrn']['visionari_dater'] = $csv[$i]['visionari_dater'];

			if($reserving->save($updating)==false){
				return array('result'=>false,'message'=>"挙式予約状況の取り込みに失敗しました。",'reason'=>$reserving->getDbo()->error."[".date('Y-m-d H:i:s')."]");
			}
		}
		return array('result'=>true);
	}
	function getWeddingList(){
		App::import("Model", "WeddingReservingStateTrnView");
		$reservingView = new WeddingReservingStateTrnView();
		return $reservingView->find('all', array('fields'=>'DISTINCT wedding_place'));
	}
	function getFirstContactPersonList(){
		App::import("Model", "WeddingReservingStateTrnView");
		$reservingView = new WeddingReservingStateTrnView();
		return $reservingView->find('all', array('fields'=>'DISTINCT first_contact_person_nm'));
	}
	function getProcessPersonList(){
		App::import("Model", "WeddingReservingStateTrnView");
		$reservingView = new WeddingReservingStateTrnView();
		return $reservingView->find('all', array('fields'=>'DISTINCT process_person_nm'));
	}
	function getHotelList(){
		App::import("Model", "WeddingReservingStateTrnView");
		$reservingView = new WeddingReservingStateTrnView();
		return $reservingView->find('all', array('fields'=>'DISTINCT wedding_day_hotel'));
	}
	function getReceptionPlaceList(){
		App::import("Model", "WeddingReservingStateTrnView");
		$reservingView = new WeddingReservingStateTrnView();
		return $reservingView->find('all', array('fields'=>'DISTINCT reception_place'));
	}
	function getCameraList(){
		App::import("Model", "WeddingReservingStateTrnView");
		$reservingView = new WeddingReservingStateTrnView();
		return $reservingView->find('all', array('fields'=>'DISTINCT camera'));
	}
	function getHairmakeList(){
		App::import("Model", "WeddingReservingStateTrnView");
		$reservingView = new WeddingReservingStateTrnView();
		return $reservingView->find('all', array('fields'=>'DISTINCT hair_make'));
	}
	function getVideoList(){
		App::import("Model", "WeddingReservingStateTrnView");
		$reservingView = new WeddingReservingStateTrnView();
		return $reservingView->find('all', array('fields'=>'DISTINCT video'));
	}
	function getFlowerList(){
		App::import("Model", "WeddingReservingStateTrnView");
		$reservingView = new WeddingReservingStateTrnView();
		return $reservingView->find('all', array('fields'=>'DISTINCT flower'));
	}
	function getAttendList(){
		App::import("Model", "WeddingReservingStateTrnView");
		$reservingView = new WeddingReservingStateTrnView();
		return $reservingView->find('all', array('fields'=>'DISTINCT attend'));
	}
}
?>