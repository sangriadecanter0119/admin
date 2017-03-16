<?php
class HouseWeddingService extends AppModel {
    var $useTable = false;

  /**
   *
   * ハウスウェディングの新規作成
   * @param $array_params
   * @return 正常：TRUE
   *         異常：FALSE
   */
   function createHouseWeddingSheet($array_params){

    	 App::import("Model", "HouseWeddingTrn");
    	 $house = new HouseWeddingTrn();

     	 //ハウスウェディング作成
     	 $house_data = array(
     	                     "customer_id"=>$array_params['customer_id'],
     	                     "final_sheet_id"=>$array_params['final_sheet_id'],
     	                     "estimate_dtl_id"=>$array_params['estimate_dtl_id'],
     	                     "site"=>$array_params['menu'],
     	                     "vendor_id"=>$array_params['vendor_id'],
     	                     "vendor_nm"=>$array_params['vendor_nm'],
                             "attend_nm"=>$array_params['vendor_attend_nm'],
                             "phone_no"=>$array_params['vendor_phone_no'],
                             "cell_no"=>$array_params['vendor_cell_no'],
                             "email"=>$array_params['vendor_email'],
 	                         "reg_nm"=>$array_params['username'],
 	                         "reg_dt"=>date('Y-m-d H:i:s')
 	                         );
 	     $house->create();
         if($house->save($house_data)==false){
         	return array('result'=>false,'message'=>"ハウスウェディングの作成に失敗しました。",'reason'=>$house->getDbo()->error."[".date('Y-m-d H:i:s')."]");
         }
         return array('result'=>true);
     }

 /*
  * ハウスウェディングシートの複製
  * @param $old_final_sheet_id
  * @param $new_final_sheet_id
  * @param $user
  */
  function copy($old_final_sheet_id,$new_final_sheet_id,$user){

  	 App::import("Model", "HouseWeddingTrn");
  	 $house_wedding = new HouseWeddingTrn();

     $old_header = $house_wedding->find('all',array('conditions'=>array('final_sheet_id'=>$old_final_sheet_id)));
     if(count($old_header) > 0){

       //ハウスウェディングヘッダ作成
       $house_wedding_data = array(
                     "final_sheet_id"=>$new_final_sheet_id,
                     "estimate_dtl_id"=>$old_header[0]['HouseWeddingTrn']['estimate_dtl_id'],
                     "customer_id"=>$old_header[0]['HouseWeddingTrn']['customer_id'],
     	             "vendor_id"=>$old_header[0]['HouseWeddingTrn']['vendor_id'],
     	             "vendor_nm"=>$old_header[0]['HouseWeddingTrn']['vendor_nm'],
                     "attend_nm"=>$old_header[0]['HouseWeddingTrn']['attend_nm'],
                     "phone_no"=>$old_header[0]['HouseWeddingTrn']['phone_no'],
                     "cell_no"=>$old_header[0]['HouseWeddingTrn']['cell_no'],
                     "email"=>$old_header[0]['HouseWeddingTrn']['email'],
                     "site"=>$old_header[0]['HouseWeddingTrn']['site'],
                     "start_time"=>$old_header[0]['HouseWeddingTrn']['start_time'],
                     "total_time"=>$old_header[0]['HouseWeddingTrn']['total_time'],
                     "deposit_dt"=>$old_header[0]['HouseWeddingTrn']['deposit_dt'],
                     "deposit_payment"=>$old_header[0]['HouseWeddingTrn']['deposit_payment'],
                     "deposit_by"=>$old_header[0]['HouseWeddingTrn']['deposit_by'],
                     "insurance_dt"=>$old_header[0]['HouseWeddingTrn']['insurance_dt'],
                     "insurance_company"=>$old_header[0]['HouseWeddingTrn']['insurance_company'],
                     "note"=>$old_header[0]['HouseWeddingTrn']['note'],
                     "rw_note"=>$old_header[0]['HouseWeddingTrn']['rw_note'],
 	                 "reg_nm"=>$user,
 	                 "reg_dt"=>date('Y-m-d H:i:s')
 	                 );
    $house_wedding->create();
    if($house_wedding->save($house_wedding_data)==false){
      	  return array('result'=>false,'message'=>"ハウスウェディングヘッダの新規作成に失敗しました。",'reason'=>$house_wedding->getDbo()->error."[".date('Y-m-d H:i:s')."]");
    }
   }
     return array('result'=>true);
  }

  /**
   *
   * ファイナルシートIDを設定
   * @param $customer_id
   * @param $final_sheet_id
   */
  function updateFinalSheetId($customer_id,$final_sheet_id){

  	 App::import("Model", "HouseWeddingTrn");
  	 $house = new HouseWeddingTrn();

     $house_data = array( "final_sheet_id"=>$final_sheet_id );

     if($house->updateAll($house_data,array("customer_id"=>$customer_id))==false){
       return array('result'=>false,'message'=>"ハウスウェディングファイナルシートIDの更新に失敗しました。",'reason'=>$house->getDbo()->error."[".date('Y-m-d H:i:s')."]");
     }
     return array('result'=>true);
 }

 /**
   *
   * ハウスウェディングのメニュー更新
   * @param $array_params
   * @return 正常：TRUE
   *         異常：FALSE
   */
   function updateMenu($array_params){

    	 App::import("Model", "HouseWeddingTrn");
    	 $house = new HouseWeddingTrn();

     	 $house_data = array(
     	                     "site"=>"'".mysql_real_escape_string($array_params['menu'])."'",
 	                         "upd_nm"=>"'".$array_params['username']."'",
 	                         "upd_dt"=>"'".date('Y-m-d H:i:s')."'"
 	                         );

        /* 履歴があるので最新のメニューのIDを取得する  */
        $data = $house->find("first",array("fields"=>"MAX(id) as max_id","conditions"=>array("estimate_dtl_id"=>$array_params['estimate_dtl_id'])));
        if(count($data) > 0){  $max_id = $data[0]["max_id"]; }

         if($house->updateAll($house_data,array("id"=>$max_id))==false){
         	return array('result'=>false,'message'=>"ハウスウェディングのメニュー更新に失敗しました。",'reason'=>$house->getDbo()->error."[".date('Y-m-d H:i:s')."]");
         }
         return array('result'=>true);
     }

 /**
  *
  * ハウスウェディングシートの削除
  * @param $customer_id
  * @return 正常：TRUE
  *         異常：FALSE
  */
  function deleteHouseWeddingSheet($customer_id){

    App::import("Model", "HouseWeddingTrn");
    $house_wedding = new HouseWeddingTrn();
    //ハウスウェディング削除[カスケード削除]
    if($house_wedding->deleteAll(array("customer_id"=>$customer_id),true)==false){
    	return array('result'=>false,'message'=>"ハウスウェディングシートの削除に失敗しました。",'reason'=>$house_wedding->getDbo()->error."[".date('Y-m-d H:i:s')."]");
    }
    return array('result'=>true);
  }

  /**
   *
   *ハウスウェディング情報を更新
   * @param $array_params
   * @param $user
   * @return 正常：TRUE
   *         異常：FALSE
   */
   function saveAll($array_params,$user){

     $tr = ClassRegistry::init('TransactionManager');
	 $tr->begin();

	 if(!empty($array_params['HouseWeddingTrn']))
	 {
	   for($header_index=0;$header_index < count($array_params['HouseWeddingTrn']);$header_index++)
	   {
	      $ret = $this->_saveHouseWedding($array_params['HouseWeddingTrn'][$header_index],$user);
	 	  if($ret['result']==false){return $ret;}
	   }
	 }
     $tr->commit();
     return array('result'=>true);
   }

  /**
   *
   *ハウスウェディングヘッダ情報を更新
   * @param $array_params
   * @param $user
   * @return 正常：TRUE
   *         異常：FALSE
   */
   function _saveHouseWedding($array_params,$user){

   	 App::import("Model", "HouseWeddingTrn");

   	 $fields = array('attend_nm'   ,'phone_no'  ,'cell_no'      ,'email' ,
   	                 'start_time'  ,'end_time'  ,'total_time'   ,
   	                 'deposit_dt'  ,'deposit_payment','deposit_by','insurance_dt' ,'insurance_company' ,
   	                 'note'        ,'rw_note'        ,'upd_nm','upd_dt');

   	 /* 稼働合計時間の計算*/
   	 if(!empty($array_params['start_time']) &&  !empty($array_params['end_time'])){
   	    	$starts = explode(":", $array_params['start_time']);
   	 	    $ends   = explode(":", $array_params['end_time']);
   	 	    $array_params['total_time'] = count($starts) == 2 && count($ends) == 2 ? (($ends[0]*60)+$ends[1]) - (($starts[0]*60)+$starts[1]) : 0;
   	 	    if($array_params['total_time'] < 0){return array('result'=>false,'message'=>"ハウスウェディングヘッダ情報更新に失敗しました。",'reason'=>"稼働開始時間と終了時間の順序が不正です。");}
   	 }else{
   	    	$array_params['total_time'] = 0;
   	 }

     if(empty($array_params['deposit_dt'])){$array_params['deposit_dt'] = null;}
     if(empty($array_params['insurance_dt'])){$array_params['insurance_dt'] = null;}
     $array_params['upd_nm'] = $user;
   	 $array_params['upd_dt'] = date('Y-m-d H:i:s');

     $hw = new HouseWeddingTrn;
	 $hw->id = $array_params['id'];

 	 if($hw->save($array_params,false,$fields)==false){
 	 	return array('result'=>false,'message'=>"ハウスウェディングヘッダ情報更新に失敗しました。",'reason'=>$hw->getDbo()->error."[".date('Y-m-d H:i:s')."]");
 	 }

 	 return array('result'=>true);
   }

   /**
    *
    * ベンダーリスト取得
    * @param $customer_id
    */
   function getVendorList($final_sheet_id){

   	  App::import("Model", "HouseWeddingTrn");
      $house = new HouseWeddingTrn();

   	  $sql = "select
                 vendor_nm,
                 attend_nm,
                 phone_no,
                 cell_no,
                 email
               from house_wedding_trns
              where final_sheet_id = ".$final_sheet_id."
          group by vendor_id  ";

   	  $data = $house->query($sql);

   	  if(count($data) > 0){

   	  	$arr = null;
   	  	for($i =0;$i < count($data);$i++){
   	  		$attr = $data[$i]["house_wedding_trns"];
   	  		$temp = array("part"=>"HouseWedding"     ,"vendor_nm"=>$attr["vendor_nm"],"attend_nm"=>$attr["attend_nm"],
   	  		             "phone_no"=>$attr["phone_no"],"cell_no"=>$attr["cell_no"]    ,"email"=>$attr["email"]);
   	  	    $arr[] = $temp;
   	  	}
   	    return $arr;
   	  }
   	 return null;
   }
}
?>