<?php
class CoordinatorService extends AppModel {
    var $useTable = false;

   /**
     *
     *  コーディネーターシートの新規作成
     * @param $array_params
     * @return 正常：TRUE
     *         異常：FALSE
     */
    function createCoordinatorSheet($array_params){

    	App::import("Model", "CoordinatorMenuTrn");
    	$coordinator_menu = new CoordinatorMenuTrn();

      /* 同じベンダーのファイナルシートがない場合はヘッダとタイムテーブルを作成する */
      $coordinator_id = $this->hasHeaderDataOfVendor($array_params['vendor_id'], $array_params['customer_id']);
      if($coordinator_id == false){

      	  App::import("Model", "CoordinatorTrn");
      	  $coordinator = new CoordinatorTrn();
          //コーディネーターヘッダ作成
          $coordinator_data = array(
     	                 "customer_id"=>$array_params['customer_id'],
                         "final_sheet_id"=>$array_params['final_sheet_id'],
                         "vendor_id"=>$array_params['vendor_id'],
     	                 "vendor_nm"=>$array_params['vendor_nm'],
                         "attend_nm"=>$array_params['vendor_attend_nm'],
                         "phone_no"=>$array_params['vendor_phone_no'],
                         "cell_no"=>$array_params['vendor_cell_no'],
                         "email"=>$array_params['vendor_email'],
                         "main_attend_kbn"=> $this->hasMainCoordinator($array_params['customer_id'])==false ? CC_MAIN:CC_NONE,
                         "total_attend"=>1,
 	                     "reg_nm"=>$array_params['username'],
 	                     "reg_dt"=>date('Y-m-d H:i:s')
 	                     );
 	     $coordinator->create();
         if($coordinator->save($coordinator_data)==false){
         	return array('result'=>false,'message'=>"コーディネーターヘッダ作成に失敗しました。",'reason'=>$coordinator->getDbo()->error."[".date('Y-m-d H:i:s')."]");
         }

         $coordinator_id = $coordinator->getLastInsertID();

         //コーディネータータイム作成
         App::import("Model", "CoordinatorTimeTrn");
         $coordinator_time = new CoordinatorTimeTrn();
         $coordinator_time_data = array(
     	                              "coordinator_id"=>$coordinator_id,
                                      "no"=>1,
 	                                  "reg_nm"=>$array_params['username'],
 	                                  "reg_dt"=>date('Y-m-d H:i:s')
 	                                  );
 	    $coordinator_time->create();
        if($coordinator_time->save($coordinator_time_data)==false){
        	return array('result'=>false,'message'=>"コーディネータータイム作成に失敗しました。",'reason'=>$coordinator_time->getDbo()->error."[".date('Y-m-d H:i:s')."]");
        }
      }

        //コーディネーターメニュー作成
        $coordinator_menu_data = array(
     	                              "coordinator_id"=>$coordinator_id,
                                      "estimate_dtl_id"=>$array_params['estimate_dtl_id'],
                                      "menu"=>$array_params['menu'],
 	                                  "reg_nm"=>$array_params['username'],
 	                                  "reg_dt"=>date('Y-m-d H:i:s')
 	                                  );
 	    $coordinator_menu->create();
        if($coordinator_menu->save($coordinator_menu_data)==false){
        	return array('result'=>false,'message'=>"コーディネーターメニュー作成に失敗しました。",'reason'=>$coordinator_menu->getDbo()->error."[".date('Y-m-d H:i:s')."]");
        }
        return array('result'=>true);
   }

  /**
    * コーディネーターシートの複製
    * @param $old_final_sheet_id
    * @param $new_final_sheet_id
    * @param $user
    */
   function copy($old_final_sheet_id,$new_final_sheet_id,$user){

  	 App::import("Model", "CoordinatorTrn");
  	 $coordinator = new CoordinatorTrn();

  	 App::import("Model", "CoordinatorMenuTrn");
  	 $coordinator_menu = new CoordinatorMenuTrn();

  	 App::import("Model", "CoordinatorTimeTrn");
  	 $coordinator_time = new CoordinatorTimeTrn();

     $old_header = $coordinator->find('all',array('conditions'=>array('final_sheet_id'=>$old_final_sheet_id)));
     if(count($old_header) > 0){
       $old_menu = $coordinator_menu->find('all',array('conditions'=>array('coordinator_id'=>$old_header[0]['CoordinatorTrn']['id'])));
       $old_time = $coordinator_time->find('all',array('conditions'=>array('coordinator_id'=>$old_header[0]['CoordinatorTrn']['id'])));

       //コーディネーターヘッダ作成
       $coordinator_data = array(
                     "final_sheet_id"=>$new_final_sheet_id,
                     "customer_id"=>$old_header[0]['CoordinatorTrn']['customer_id'],
     	             "vendor_id"=>$old_header[0]['CoordinatorTrn']['vendor_id'],
     	             "vendor_nm"=>$old_header[0]['CoordinatorTrn']['vendor_nm'],
                     "attend_nm"=>$old_header[0]['CoordinatorTrn']['attend_nm'],
                     "phone_no"=>$old_header[0]['CoordinatorTrn']['phone_no'],
                     "cell_no"=>$old_header[0]['CoordinatorTrn']['cell_no'],
                     "email"=>$old_header[0]['CoordinatorTrn']['email'],
                     "working_start_time"=>$old_header[0]['CoordinatorTrn']['working_start_time'],
                     "working_end_time"=>$old_header[0]['CoordinatorTrn']['working_end_time'],
                     "working_total"=>$old_header[0]['CoordinatorTrn']['working_total'],
                     "main_attend_kbn"=>$old_header[0]['CoordinatorTrn']['main_attend_kbn'],
                     "total_attend"=>$old_header[0]['CoordinatorTrn']['total_attend'],
                     "briefing_dt"=>$old_header[0]['CoordinatorTrn']['briefing_dt'],
                     "briefing_start_time"=>$old_header[0]['CoordinatorTrn']['briefing_start_time'],
                     "briefing_end_time"=>$old_header[0]['CoordinatorTrn']['briefing_end_time'],
                     "briefing_place"=>$old_header[0]['CoordinatorTrn']['briefing_place'],
                     "briefing_name"=>$old_header[0]['CoordinatorTrn']['briefing_name'],
                     "note"=>$old_header[0]['CoordinatorTrn']['note'],
 	                 "reg_nm"=>$user,
 	                 "reg_dt"=>date('Y-m-d H:i:s')
 	                 );
    $coordinator->create();
    if($coordinator->save($coordinator_data)==false){
      	  return array('result'=>false,'message'=>"コーディネーターヘッダの新規作成に失敗しました。",'reason'=>$coordinator->getDbo()->error."[".date('Y-m-d H:i:s')."]");
    }
    $coordinator_id = $coordinator->getLastInsertID();

    //コーディネーターメニュー作成
    for($i=0;$i < count($old_menu);$i++){

     $coordinator_menu_data = array(
                           "coordinator_id"=>$coordinator_id,
                           "estimate_dtl_id"=>$old_menu[$i]['CoordinatorMenuTrn']['estimate_dtl_id'],
                           "menu"=>$old_menu[$i]['CoordinatorMenuTrn']['menu'],
                           "note"=>$old_menu[$i]['CoordinatorMenuTrn']['note'],
 	                       "reg_nm"=>$user,
 	                       "reg_dt"=>date('Y-m-d H:i:s')
 	                       );
     $coordinator_menu->create();

     if($coordinator_menu->save($coordinator_menu_data)==false){
     	 return array('result'=>false,'message'=>"コーディネーターメニューの新規作成に失敗しました。",'reason'=>$coordinator_menu->getDbo()->error."[".date('Y-m-d H:i:s')."]");
     }
    }

    //コーディネーター時間作成
    for($i=0;$i < count($old_time);$i++){

     $coordinator_time_data = array(
                           "coordinator_id"=>$coordinator_id,
                           "no"=>$old_time[$i]['CoordinatorTimeTrn']['no'],
                           "start_time"=>$old_time[$i]['CoordinatorTimeTrn']['start_time'],
                           "start_place"=>$old_time[$i]['CoordinatorTimeTrn']['start_place'],
                           "end_time"=>$old_time[$i]['CoordinatorTimeTrn']['end_time'],
                           "end_place"=>$old_time[$i]['CoordinatorTimeTrn']['end_place'],
                           "transportation"=>$old_time[$i]['CoordinatorTimeTrn']['transportation'],
                           "note"=>$old_time[$i]['CoordinatorTimeTrn']['note'],
 	                       "reg_nm"=>$user,
 	                       "reg_dt"=>date('Y-m-d H:i:s')
 	                       );
     $coordinator_time->create();

     if($coordinator_time->save($coordinator_time_data)==false){
     	 return array('result'=>false,'message'=>"コーディネーター時間の新規作成に失敗しました。",'reason'=>$coordinator_time->getDbo()->error."[".date('Y-m-d H:i:s')."]");
     }
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

  	 App::import("Model", "CoordinatorTrn");
  	 $coordinator = new CoordinatorTrn();

     $coordinator_data = array( "final_sheet_id"=>$final_sheet_id );

     if($coordinator->updateAll($coordinator_data,array("customer_id"=>$customer_id))==false){
       return array('result'=>false,'message'=>"コーディネーターファイナルシートIDの更新に失敗しました。",'reason'=>$coordinator->getDbo()->error."[".date('Y-m-d H:i:s')."]");
     }
     return array('result'=>true);
 }

   /**
     *
     *  コーディネーターシートのメニュー更新
     * @param $array_params
     * @return 正常：TRUE
     *         異常：FALSE
     */
    function updateMenu($array_params){

    	App::import("Model", "CoordinatorMenuTrn");
    	$coordinator_menu = new CoordinatorMenuTrn();

        $coordinator_menu_data = array(
                                      "menu"=>"'".mysql_real_escape_string($array_params['menu'])."'",
 	                                  "reg_nm"=>"'".$array_params['username']."'",
 	                                  "reg_dt"=>"'".date('Y-m-d H:i:s')."'"
 	                                  );
        /* 履歴があるので最新のメニューのIDを取得する  */
        $data = $coordinator_menu->find("first",array("fields"=>"MAX(id) as max_id","conditions"=>array("estimate_dtl_id"=>$array_params['estimate_dtl_id'])));
        if(count($data) > 0){  $max_id = $data[0]["max_id"]; }

        if($coordinator_menu->updateAll($coordinator_menu_data,array("id"=>$max_id))==false){
          return array('result'=>false,'message'=>"コーディネーターシートのメニュー更新に失敗しました。",'reason'=>$coordinator_menu->getDbo()->error."[".date('Y-m-d H:i:s')."]");
        }
        return array('result'=>true);
   }

   /**
     *
     * コーディネーターシートの削除
     * @param $customer_id
     * @return 正常：TRUE
     *         異常：FALSE
     */
    function deleteCoordinatorSheet($customer_id){

      App::import("Model", "CoordinatorTrn");
      $coordinator = new CoordinatorTrn();
      //コーディネーターヘッダ・メニュー・タイム削除[カスケード削除]
      if($coordinator->deleteAll(array("customer_id"=>$customer_id),true)==false){
      	return array('result'=>false,'message'=>"コーディネーターシートの削除に失敗しました。",'reason'=>$coordinator->getDbo()->error."[".date('Y-m-d H:i:s')."]");
      }
      return array('result'=>true);
    }

   /**
    * 全コーディネーター情報を更新
    * @param $menu
    * @param $username
    * @return 正常：TRUE
    *         異常：FALSE
    */
   function saveAll($array_params,$user){

     $tr = ClassRegistry::init('TransactionManager');
	 $tr->begin();

	 //コーディネーター
	 if(!empty($array_params['CoordinatorTrn']))
	 {
	 	/* ヘッダ更新 */
	   for($header_index=0;$header_index < count($array_params['CoordinatorTrn']);$header_index++)
	   {
	     $ret = $this->_saveCoordinator($array_params['CoordinatorTrn'][$header_index],$user);
	 	 if($ret['result']==false){return $ret;}

	     /* 時間更新 */
	     //配列の歯抜けのインデックスを詰める
	     $temp_array = array_merge($array_params['CoordinatorTimeTrn'][$header_index]);
	     $ret = $this->_saveCoordinatorTime($temp_array,$array_params['CoordinatorTrn'][$header_index]["id"],$user);
	 	 if($ret['result']==false){return $ret;}

	     /* メニュー更新 */
	     for($sub_index=0;$sub_index < count($array_params['CoordinatorMenuTrn'][$header_index]);$sub_index++)
	     {
	        $ret = $this->_saveCoordinatorMenu($array_params['CoordinatorMenuTrn'][$header_index][$sub_index],$user);
	 	    if($ret['result']==false){return $ret;}
	     }
	   }
	 }
     $tr->commit();
     return array('result'=>true);
   }

   /**
    * コーディネーターヘッダ情報を更新
    * @param $menu
    * @param $username
    * @return 正常：TRUE
    *         異常：FALSE
    */
   function _saveCoordinator($array_params,$user){

   	 App::import("Model", "CoordinatorTrn");

   	 /* メインコーディネーターとそれ以外で更新内容を分ける */
   	 if($array_params['main_attend_kbn'] == CC_MAIN){

   	 	$fields = array('attend_nm','phone_no','cell_no','email','working_start_time','working_end_time','working_total','main_attend_kbn',
   	                    'total_attend','briefing_dt','briefing_start_time','briefing_end_time','briefing_place','briefing_name',
 	                    'note','upd_nm','upd_dt');
   	 }else{
   	 	$array_params['main_attend_kbn'] = CC_NONE;
   	 	$fields = array('attend_nm','phone_no','cell_no','email','working_start_time','working_end_time','working_total','main_attend_kbn',
 	                    'note','upd_nm','upd_dt');
   	 }

   	  /* 稼働合計時間の計算*/
   	 if(!empty($array_params['working_start_time']) &&  !empty($array_params['working_end_time'])){
   	 	$starts = explode(":", $array_params['working_start_time']);
   	 	$ends   = explode(":", $array_params['working_end_time']);
   	 	$array_params['working_total'] = count($starts) == 2 && count($ends) == 2 ? (($ends[0]*60)+$ends[1]) - (($starts[0]*60)+$starts[1]) : 0;
   	 	if($array_params['working_total'] < 0){return array('result'=>false,'message'=>"コーディネーターヘッダ情報更新に失敗しました。",'reason'=>"稼働開始時間と終了時間の順序が不正です。");}
   	 }else{
   	 	$array_params['working_total'] = 0;
   	 }

   	 if(empty($array_params['briefing_dt'])){$array_params['briefing_dt'] = null;}
   	 $array_params['upd_nm'] = $user;
   	 $array_params['upd_dt'] = date('Y-m-d H:i:s');

     $co = new CoordinatorTrn;
	 $co->id = $array_params['id'];

 	 if($co->save($array_params,false,$fields)==false){
 	   return array('result'=>false,'message'=>"コーディネーターヘッダ情報更新に失敗しました。",'reason'=>$co->getDbo()->error."[".date('Y-m-d H:i:s')."]");
 	 }

 	 return array('result'=>true);
   }

   /**
    *  コーディネーターメニュー情報を更新
    * @param $menu
    * @param $username
    * @return 正常：TRUE
    *         異常：FALSE
    */
   function _saveCoordinatorMenu($array_params,$user){
 	 App::import("Model", "CoordinatorMenuTrn");
   	 $menu = new CoordinatorMenuTrn;

     $fields = array('note','upd_nm','upd_dt');

 	 $array_params['upd_nm'] = $user;
 	 $array_params['upd_dt'] = date('Y-m-d H:i:s');

 	 $menu->id = $array_params['id'];
 	 if($menu->save($array_params,false,$fields)==false){
 	 	return array('result'=>false,'message'=>" コーディネーターメニュー情報更新に失敗しました。",'reason'=>$menu->getDbo()->error."[".date('Y-m-d H:i:s')."]");
 	 }
    return array('result'=>true);
   }

   /*
    *  コーディネーター時間情報を更新
    *
    *   $array_params : 更新データ
    *   $user         : 更新ユーザー名
    */
   function _saveCoordinatorTime($array_params,$foreign_key,$user){

   	 App::import("Model", "CoordinatorTimeTrn");
   	 $co_time = new CoordinatorTimeTrn;

   	 //新規追加または更新した明細IDを保持
     $saving_id= array();
     $fields = array('no','start_time','start_place','end_time','end_place','transportation',
 	                 'note','upd_nm','upd_dt');

     for($i=0;$i < count($array_params);$i++)
     {
	  //明細IDがNULLの場合はクライアント側で新規に追加した項目なので新規作成する
 	  if(empty($array_params[$i]['id']) || $array_params[$i]['id']==null)
 	  {
 	  	       $array_params[$i]['reg_nm'] = $user;
 	           $array_params[$i]['reg_dt'] = date('Y-m-d H:i:s');
 	           $array_params[$i]['coordinator_id'] =  $foreign_key;
 	           $co_time->create();
 	           if($co_time->save($array_params[$i])==false){
 	              return array('result'=>false,'message'=>"コーディネーター時間情報作成に失敗しました。",'reason'=>$co_time->getDbo()->error."[".date('Y-m-d H:i:s')."]");
 	           }

 	           //新規作成したデータのIDを保存
 	           $last_coordinator_time_id = $co_time->getLastInsertID();
 	           array_push($saving_id, $last_coordinator_time_id);
 	  }
 	  //既存の明細の更新
 	  else
 	  {
 	  	 $array_params[$i]['upd_nm'] = $user;
 	     $array_params[$i]['upd_dt'] = date('Y-m-d H:i:s');
 	     //削除されず残っているデータのIDを保存
 	     array_push($saving_id,$array_params[$i]['id']);
 	     $co_time->id = $array_params[$i]['id'];
 	     if($co_time->save($array_params[$i],false,$fields)==false){
 	        return array('result'=>false,'message'=>"コーディネーター時間情報更新に失敗しました。",'reason'=>$co_time->getDbo()->error."[".date('Y-m-d H:i:s')."]");
 	     }
 	  }
    }
    //新規追加でも既存の明細の更新でもないデータはクライアント側で削除指定されたデータなのですべて削除
 	if($co_time->deleteAll( array('coordinator_id'=>$foreign_key,'NOT'=>array('id'=>$saving_id)))==false){
 		return array('result'=>false,'message'=>"コーディネーター時間情報削除に失敗しました。",'reason'=>$co_time->getDbo()->error."[".date('Y-m-d H:i:s')."]");
 	}
 	return array('result'=>true);
   }

  /**
    *
    * コーディネーターファイナルシートに引数のベンダーが存在するかチェック
    * @param $vendor_id
    * @param $customer_id
    * @return 正常：コーディネーターID
    *         異常：FALSE
    */
   function hasHeaderDataOfVendor($vendor_id,$customer_id){

         App::import("Model", "CoordinatorTrn");
         $coordinator = new CoordinatorTrn();

         if($coordinator->hasAny(array('customer_id' => $customer_id,'vendor_id'=>$vendor_id))){
            $ret = $coordinator->find('first', array('fields' => 'id','conditions' => array('customer_id' => $customer_id,'vendor_id'=>$vendor_id)));
            return $ret['CoordinatorTrn']['id'];
         }else{
         	return false;
         }
   }

   /**
    *
    * コーディネーターファイナルシートにメインのコーディネーターが存在するかチェック
    * @param $customer_id
    * @return 正常：コーディネーターID
    *         異常：FALSE
    */
   function hasMainCoordinator($customer_id){

         App::import("Model", "CoordinatorTrn");
         $coordinator = new CoordinatorTrn();

         if($coordinator->hasAny(array('customer_id' => $customer_id,'main_attend_kbn'=>CC_MAIN))){
            $ret = $coordinator->find('first', array('fields' => 'id','conditions' => array('customer_id' => $customer_id,'main_attend_kbn'=>CC_MAIN)));
            return $ret['CoordinatorTrn']['id'];
         }else{
         	return false;
         }
   }

 /**
    *
    * コーディネーターヘッダテーブルがサブテーブルから参照されていなければ削除する
    * @param $customer_id
    */
   function deleteHeaderIfNoSubTableData($customer_id)
   {
   	    App::import("Model", "CoordinatorTrn");
        $coordinator = new CoordinatorTrn();

        App::import("Model", "CoordinatorMenuTrn");
        $coordinator_menu = new CoordinatorMenuTrn();

        $header_ids = $coordinator->find('all', array('fields' => 'id','conditions' => array('customer_id' => $customer_id)));

        for($i=0;$i < count($header_ids);$i++)
        {
           if($coordinator_menu->hasAny(array('coordinator_id'=>$header_ids[$i]['CoordinatorTrn']['id']))==false){
                if($coordinator->delete($header_ids[$i]['CoordinatorTrn']['id'])==false){
                	return array('result'=>false,'message'=>"コーディネーターヘッダテーブル削除に失敗しました。",'reason'=>$coordinator->getDbo()->error."[".date('Y-m-d H:i:s')."]");
                }
           }
        }
        return array('result'=>true);
   }

   /**
    *
    * ベンダーリスト取得
    * @param $customer_id
    */
   function getVendorList($final_sheet_id){

   	  App::import("Model", "CoordinatorTrn");
      $coordinator = new CoordinatorTrn();

   	  $sql = "select
                 vendor_nm,
                 attend_nm,
                 phone_no,
                 cell_no,
                 email
               from coordinator_trns
              where final_sheet_id = ".$final_sheet_id."
          group by vendor_id  ";

   	  $data = $coordinator->query($sql);

   	  if(count($data) > 0){

   	  	$arr = null;
   	  	for($i =0;$i < count($data);$i++){
   	  		$attr = $data[$i]["coordinator_trns"];
   	  		$temp = array("part"=>"Coordinator"       ,"vendor_nm"=>$attr["vendor_nm"],"attend_nm"=>$attr["attend_nm"],
   	  		             "phone_no"=>$attr["phone_no"],"cell_no"=>$attr["cell_no"]    ,"email"=>$attr["email"]);
   	  	    $arr[] = $temp;
   	  	}
   	    return $arr;
   	  }
   	 return null;
   }
}
?>