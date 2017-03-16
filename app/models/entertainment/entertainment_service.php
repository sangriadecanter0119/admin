<?php
class EntertainmentService extends AppModel {
    var $useTable = false;

    /**
     *
     * エンターテイメントシートの新規作成
     * @param $array_params
     * @return 正常：TRUE
     *         異常：FALSE
     */
   function createEntertainmentSheet($array_params){

         App::import("Model", "EntertainmentMenuTrn");
         $entertainment_menu = new EntertainmentMenuTrn();

         /* 同じベンダーのファイナルシートがない場合はヘッダを作成する */
         $entertainment_id = $this->hasHeaderDataOfVendor($array_params['vendor_id'], $array_params['customer_id']);
         if($entertainment_id == false){

           App::import("Model", "EntertainmentTrn");
           $entertainment = new EntertainmentTrn();

     	  //エンターテイメントヘッダ作成
     	  $entertainment_data = array(
     	                             "customer_id"=>$array_params['customer_id'],
     	                             "final_sheet_id"=>$array_params['final_sheet_id'],
     	                             "vendor_id"=>$array_params['vendor_id'],
     	                             "vendor_nm"=>$array_params['vendor_nm'],
                                     "attend_nm"=>$array_params['vendor_attend_nm'],
                                     "phone_no"=>$array_params['vendor_phone_no'],
                                     "cell_no"=>$array_params['vendor_cell_no'],
                                     "email"=>$array_params['vendor_email'],
 	                                 "reg_nm"=>$array_params['username'],
 	                                 "reg_dt"=>date('Y-m-d H:i:s')
 	                                 );
 	       $entertainment->create();
           if($entertainment->save($entertainment_data)==false){
           	  return array('result'=>false,'message'=>"エンターテイメントヘッダ作成に失敗しました。",'reason'=>$entertainment->getDbo()->error."[".date('Y-m-d H:i:s')."]");
           }

           $entertainment_id = $entertainment->getLastInsertID();
          }

          //エンターテイメントメニュー作成
          $entertainment_menu_data = array(
     	                              "entertainment_id"=>$entertainment_id,
                                      "estimate_dtl_id"=>$array_params['estimate_dtl_id'],
                                      "menu"=>$array_params['menu'],
                                      "type"=>$array_params['content'],
                                      "artist_count"=>$array_params['num'],
 	                                  "reg_nm"=>$array_params['username'],
 	                                  "reg_dt"=>date('Y-m-d H:i:s')
 	                                  );
 	      $entertainment_menu->create();
          if($entertainment_menu->save($entertainment_menu_data)==false){
          	 return array('result'=>false,'message'=>"エンターテイメントメニュー作成作成に失敗しました。",'reason'=>$entertainment_menu->getDbo()->error."[".date('Y-m-d H:i:s')."]");
          }
         return array('result'=>true);
     }

  /**
    * エンターテイメントシートの複製
    * @param $old_final_sheet_id
    * @param $new_final_sheet_id
    * @param $user
    */
   function copy($old_final_sheet_id,$new_final_sheet_id,$user){

  	 App::import("Model", "EntertainmentTrn");
  	 $entertainment = new EntertainmentTrn();

  	 App::import("Model", "EntertainmentMenuTrn");
  	 $entertainment_menu = new EntertainmentMenuTrn();

     $old_header = $entertainment->find('all',array('conditions'=>array('final_sheet_id'=>$old_final_sheet_id)));
     if(count($old_header) > 0){
       $old_menu = $entertainment_menu->find('all',array('conditions'=>array('entertainment_id'=>$old_header[0]['EntertainmentTrn']['id'])));

       //エンターテイメントヘッダ作成
       $entertainment_data = array(
                     "final_sheet_id"=>$new_final_sheet_id,
                     "customer_id"=>$old_header[0]['EntertainmentTrn']['customer_id'],
     	             "vendor_id"=>$old_header[0]['EntertainmentTrn']['vendor_id'],
     	             "vendor_nm"=>$old_header[0]['EntertainmentTrn']['vendor_nm'],
                     "attend_nm"=>$old_header[0]['EntertainmentTrn']['attend_nm'],
                     "phone_no"=>$old_header[0]['EntertainmentTrn']['phone_no'],
                     "cell_no"=>$old_header[0]['EntertainmentTrn']['cell_no'],
                     "email"=>$old_header[0]['EntertainmentTrn']['email'],
                     "note"=>$old_header[0]['EntertainmentTrn']['note'],
 	                 "reg_nm"=>$user,
 	                 "reg_dt"=>date('Y-m-d H:i:s')
 	                 );
    $entertainment->create();
    if($entertainment->save($entertainment_data)==false){
      	  return array('result'=>false,'message'=>"エンターテイメントヘッダの新規作成に失敗しました。",'reason'=>$entertainment->getDbo()->error."[".date('Y-m-d H:i:s')."]");
    }
    $entertainment_id = $entertainment->getLastInsertID();

    for($i=0;$i < count($old_menu);$i++){

     //エンターテイメントメニュー作成
     $entertainment_menu_data = array(
                           "entertainment_id"=>$entertainment_id,
                           "estimate_dtl_id"=>$old_menu[$i]['EntertainmentMenuTrn']['estimate_dtl_id'],
                           "menu"=>$old_menu[$i]['EntertainmentMenuTrn']['menu'],
                           "type"=>$old_menu[$i]['EntertainmentMenuTrn']['type'],
                           "artist_count"=>$old_menu[$i]['EntertainmentMenuTrn']['artist_count'],
                           "working_start_time"=>$old_menu[$i]['EntertainmentMenuTrn']['working_start_time'],
                           "working_end_time"=>$old_menu[$i]['EntertainmentMenuTrn']['working_end_time'],
                           "working_total_time"=>$old_menu[$i]['EntertainmentMenuTrn']['working_total_time'],
                           "start_place"=>$old_menu[$i]['EntertainmentMenuTrn']['start_place'],
                           "end_place"=>$old_menu[$i]['EntertainmentMenuTrn']['end_place'],
                           "note"=>$old_menu[$i]['EntertainmentMenuTrn']['note'],
 	                       "reg_nm"=>$user,
 	                       "reg_dt"=>date('Y-m-d H:i:s')
 	                       );
     $entertainment_menu->create();

     if($entertainment_menu->save($entertainment_menu_data)==false){
     	 return array('result'=>false,'message'=>"エンターテイメントメニューの新規作成に失敗しました。",'reason'=>$entertainment_menu->getDbo()->error."[".date('Y-m-d H:i:s')."]");
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

  	 App::import("Model", "EntertainmentTrn");
  	 $entertainment = new EntertainmentTrn();

     $entertainment_data = array( "final_sheet_id"=>$final_sheet_id );

     if($entertainment->updateAll($entertainment_data,array("customer_id"=>$customer_id))==false){
       return array('result'=>false,'message'=>"エンターテイメントファイナルシートIDの更新に失敗しました。",'reason'=>$entertainment->getDbo()->error."[".date('Y-m-d H:i:s')."]");
     }
     return array('result'=>true);
 }

   /**
     *
     * エンターテイメントシートのメニュー更新
     * @param $array_params
     * @return 正常：TRUE
     *         異常：FALSE
     */
    function updateMenu($array_params){

         App::import("Model", "EntertainmentMenuTrn");
         $entertainment_menu = new EntertainmentMenuTrn();

         $entertainment_menu_data = array(
                                      "menu"=>"'".mysql_real_escape_string($array_params['menu'])."'",
                                      "artist_count"=>$array_params['num'],
 	                                  "reg_nm"=>"'".$array_params['username']."'",
 	                                  "reg_dt"=>"'".date('Y-m-d H:i:s')."'"
 	                                  );

        /* 履歴があるので最新のメニューのIDを取得する  */
        $data = $entertainment_menu->find("first",array("fields"=>"MAX(id) as max_id","conditions"=>array("estimate_dtl_id"=>$array_params['estimate_dtl_id'])));
        if(count($data) > 0){  $max_id = $data[0]["max_id"]; }

         if($entertainment_menu->updateAll($entertainment_menu_data,array("id"=>$max_id))==false){
         	return array('result'=>false,'message'=>"エンターテイメントシートのメニュー更新に失敗しました。",'reason'=>$entertainment_menu->getDbo()->error."[".date('Y-m-d H:i:s')."]");
         }
         return array('result'=>true);
     }

   /**
    *
    * エンターテイメントシートの削除
    * @param $customer_id
    * @return 正常：TRUE
    *         異常：FALSE
    */
    function deleteEntertainmentSheet($customer_id){

      App::import("Model", "EntertainmentTrn");
      $entertainment = new EntertainmentTrn();
      //エンターテイメントヘッダ・メニュー削除[カスケード削除]
      if($entertainment->deleteAll(array("customer_id"=>$customer_id),true)==false){
      	return array('result'=>false,'message'=>"エンターテイメントシートのメニュー削除に失敗しました。",'reason'=>$entertainment_menu->getDbo()->error."[".date('Y-m-d H:i:s')."]");
      }
      return array('result'=>true);
    }

  /**
   *
   * 全エンターテイメント情報を更新
   * @param $array_params
   * @param $user
   * @param $foreign_key
   * @return 正常：TRUE
   *         異常：FALSE
   */
   function saveAll($array_params,$user){

     $tr = ClassRegistry::init('TransactionManager');
	 $tr->begin();

	 if(!empty($array_params['EntertainmentTrn']))
	 {
	   /* ヘッダ更新 */
	   for($header_index=0;$header_index < count($array_params['EntertainmentTrn']);$header_index++)
	   {
	      $ret = $this->_saveEntertainment($array_params['EntertainmentTrn'][$header_index],$user);
	 	  if($ret['result']==false){return $ret;}
	   }
	   /* 明細更新 */
	   for($sub_index=0;$sub_index < count($array_params['EntertainmentMenuTrn']);$sub_index++)
	   {
	      $ret = $this->_saveEntertainmentMenu($array_params['EntertainmentMenuTrn'][$sub_index],$user);
	      if($ret['result']==false){return $ret;}
	   }
	 }
     $tr->commit();
     return array('result'=>true);
   }

  /**
   *
   * エンターテイメントヘッダ情報を更新
   * @param $array_params
   * @param $user
   * @param $foreign_key
   * @return 正常：TRUE
   *         異常：FALSE
   */
   function _saveEntertainment($array_params,$user){

   	 App::import("Model", "EntertainmentTrn");

   	 $fields = array('attend_nm' ,'phone_no','cell_no' ,'email' ,'note','upd_nm','upd_dt');

   	 $array_params['upd_nm'] = $user;
   	 $array_params['upd_dt'] = date('Y-m-d H:i:s');

     $ent = new EntertainmentTrn;
	 $ent->id = $array_params['id'];

 	 if($ent->save($array_params,false,$fields)==false){
 	 	return array('result'=>false,'message'=>"エンターテイメントヘッダ情報を更新に失敗しました。",'reason'=>$ent->getDbo()->error."[".date('Y-m-d H:i:s')."]");
 	 }

 	 return array('result'=>true);
   }

  /**
   *
   * エンターテイメントメニュー情報を更新
   * @param $array_params
   * @param $user
   * @return 正常：TRUE
   *         異常：FALSE
   */
   function _saveEntertainmentMenu($array_params,$user){

   	 App::import("Model", "EntertainmentMenuTrn");
   	 $ent = new EntertainmentMenuTrn;

     $fields = array('artist_count','working_start_time','working_end_time','working_total_time',
                     'start_place' ,'end_place'   ,'note'  ,'upd_nm' ,'upd_dt');

     for($i=0;$i < count($array_params);$i++)
     {
     	/* 稼働合計時間の計算*/
   	    if(!empty($array_params[$i]['working_start_time']) &&  !empty($array_params[$i]['working_end_time'])){
   	    	$starts = explode(":", $array_params[$i]['working_start_time']);
   	 	    $ends   = explode(":", $array_params[$i]['working_end_time']);
   	 	    $array_params[$i]['working_total_time'] = count($starts) == 2 && count($ends) == 2 ? (($ends[0]*60)+$ends[1]) - (($starts[0]*60)+$starts[1]) : 0;
   	 	    if($array_params[$i]['working_total_time'] < 0){return array('result'=>false,'message'=>"エンターテイメントメニュー情報を更新に失敗しました。。",'reason'=>"稼働開始時間と終了時間の順序が不正です。");}
   	    }else{
   	    	$array_params[$i]['working_total_time'] = 0;
   	    }

 	  	 $array_params[$i]['upd_nm'] = $user;
 	     $array_params[$i]['upd_dt'] = date('Y-m-d H:i:s');

 	     $ent->id = $array_params[$i]['id'];
 	     if($ent->save($array_params[$i],false,$fields)==false){
 	     	return array('result'=>false,'message'=>"エンターテイメントメニュー情報を更新に失敗しました。",'reason'=>$ent-->getDbo()->error."[".date('Y-m-d H:i:s')."]");
 	     }
    }
    return array('result'=>true);
   }

   /**
    *
    * エンターテイメントファイナルシートに引数のベンダーが存在するかチェック
    * @param $vendor_id
    * @param $customer_id
    * @return 正常：エンターテインメントID
    *         異常：FALSE
    */
   function hasHeaderDataOfVendor($vendor_id,$customer_id){

         App::import("Model", "EntertainmentTrn");
         $entertainment = new EntertainmentTrn();

         if($entertainment->hasAny(array('customer_id' => $customer_id,'vendor_id'=>$vendor_id))){
            $ret = $entertainment->find('first', array('fields' => 'id','conditions' => array('customer_id' => $customer_id,'vendor_id'=>$vendor_id)));
            return $ret['EntertainmentTrn']['id'];
         }else{
         	return false;
         }
   }

  /**
    *
    * エンターテイメントヘッダテーブルがサブテーブルから参照されていなければ削除する
    * @param $customer_id
    */
   function deleteHeaderIfNoSubTableData($customer_id)
   {
   	    App::import("Model", "EntertainmentTrn");
        $entertainment = new EntertainmentTrn();

        App::import("Model", "EntertainmentMenuTrn");
        $entertainment_menu = new EntertainmentMenuTrn();

        $header_ids = $entertainment->find('all', array('fields' => 'id','conditions' => array('customer_id' => $customer_id)));

        for($i=0;$i < count($header_ids);$i++)
        {
           if($entertainment_menu->hasAny(array('entertainment_id'=>$header_ids[$i]['EntertainmentTrn']['id']))==false){
                if($entertainment->delete($header_ids[$i]['EntertainmentTrn']['id'])==false){
                	return array('result'=>false,'message'=>" エンターテイメントヘッダテーブル削除に失敗しました。",'reason'=>$entertainment->getDbo()->error."[".date('Y-m-d H:i:s')."]");
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

   	  App::import("Model", "EntertainmentTrn");
      $entertainment = new EntertainmentTrn();

   	  $sql = "select
                 vendor_nm,
                 attend_nm,
                 phone_no,
                 cell_no,
                 email
               from entertainment_trns
              where final_sheet_id = ".$final_sheet_id."
          group by vendor_id  ";

   	  $data = $entertainment->query($sql);

   	  if(count($data) > 0){

   	  	$arr = null;
   	  	for($i =0;$i < count($data);$i++){
   	  		$attr = $data[$i]["entertainment_trns"];
   	  		$temp = array("part"=>"Entertainment"                ,"vendor_nm"=>$attr["vendor_nm"],"attend_nm"=>$attr["attend_nm"],
   	  		             "phone_no"=>$attr["phone_no"],"cell_no"=>$attr["cell_no"]    ,"email"=>$attr["email"]);
   	  	    $arr[] = $temp;
   	  	}
   	    return $arr;
   	  }
   	 return null;
   }
}
?>