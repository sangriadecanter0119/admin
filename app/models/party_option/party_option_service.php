<?php
class PartyOptionService extends AppModel {
    var $useTable = false;

  /**
   *
   * パーティオプションシートの新規作成
   * @param $array_params
   * @return 正常：TRUE
   *         異常：FALSE
   */
   function createPartyOptionSheet($array_params){

       App::import("Model", "PartyOptionDtlTrn");
       $party_option_dtl = new PartyOptionDtlTrn();

       /* 同じベンダーのファイナルシートがない場合はヘッダを作成する */
      $party_option_id = $this->hasHeaderDataOfVendor($array_params['vendor_id'], $array_params['customer_id']);
      if($party_option_id == false){

         App::import("Model", "PartyOptionTrn");
         $party_option = new PartyOptionTrn();
         //パーティオプションヘッダ作成
         $party_option_data = array(
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
 	    $party_option->create();
        if($party_option->save($party_option_data)==false){
        	return array('result'=>false,'message'=>"パーティオプションヘッダ作成に失敗しました。",'reason'=>$party_option->getDbo()->error."[".date('Y-m-d H:i:s')."]");
        }

        $party_option_id = $party_option->getLastInsertID();
      }
       //パーティオプション明細作成
       $party_option_dtl_data = array(
     	                              "party_option_id"=>$party_option_id,
                                      "estimate_dtl_id"=>$array_params['estimate_dtl_id'],
                                      "menu"=>$array_params['menu'],
                                      "num"=>$array_params['num'],
                                      "content"=>$array_params['content'],
 	                                  "reg_nm"=>$array_params['username'],
 	                                  "reg_dt"=>date('Y-m-d H:i:s')
 	                                  );
 	   $party_option_dtl->create();
       if($party_option_dtl->save($party_option_dtl_data)==false){
       	  return array('result'=>false,'message'=>"パーティオプション明細作成に失敗しました。",'reason'=>$party_option_dtl->getDbo()->error."[".date('Y-m-d H:i:s')."]");
       }
       return array('result'=>true);
   }

 /*
  * パーティオプションシートの複製
  * @param $old_final_sheet_id
  * @param $new_final_sheet_id
  * @param $user
  */
  function copy($old_final_sheet_id,$new_final_sheet_id,$user){

  	 App::import("Model", "PartyOptionTrn");
  	 $party_option = new PartyOptionTrn();

  	 App::import("Model", "PartyOptionDtlTrn");
  	 $party_option_dtl = new PartyOptionDtlTrn();

     $old_header = $party_option->find('all',array('conditions'=>array('final_sheet_id'=>$old_final_sheet_id)));
     if(count($old_header) > 0){
       $old_dtl = $party_option_dtl->find('all',array('conditions'=>array('party_option_id'=>$old_header[0]['PartyOptionTrn']['id'])));

       //パーティオプションヘッダ作成
       $party_option_data = array(
                     "final_sheet_id"=>$new_final_sheet_id,
                     "customer_id"=>$old_header[0]['PartyOptionTrn']['customer_id'],
     	             "vendor_id"=>$old_header[0]['PartyOptionTrn']['vendor_id'],
     	             "vendor_nm"=>$old_header[0]['PartyOptionTrn']['vendor_nm'],
                     "attend_nm"=>$old_header[0]['PartyOptionTrn']['attend_nm'],
                     "phone_no"=>$old_header[0]['PartyOptionTrn']['phone_no'],
                     "cell_no"=>$old_header[0]['PartyOptionTrn']['cell_no'],
                     "email"=>$old_header[0]['PartyOptionTrn']['email'],
                     "setting_start_time"=>$old_header[0]['PartyOptionTrn']['setting_start_time'],
                     "setting_end_time"=>$old_header[0]['PartyOptionTrn']['setting_end_time'],
                     "setting_place"=>$old_header[0]['PartyOptionTrn']['setting_place'],
                     "delivery_term"=>$old_header[0]['PartyOptionTrn']['delivery_term'],
                     "delivery_place"=>$old_header[0]['PartyOptionTrn']['delivery_place'],
                     "note"=>$old_header[0]['PartyOptionTrn']['note'],
 	                 "reg_nm"=>$user,
 	                 "reg_dt"=>date('Y-m-d H:i:s')
 	                 );
    $party_option->create();
    if($party_option->save($party_option_data)==false){
      	  return array('result'=>false,'message'=>"パーティオプションヘッダの新規作成に失敗しました。",'reason'=>$party_option->getDbo()->error."[".date('Y-m-d H:i:s')."]");
    }
    $party_option_id = $party_option->getLastInsertID();

    for($i=0;$i < count($old_dtl);$i++){

     //パーティオプション詳細作成
     $party_option_dtl_data = array(
                           "party_option_id"=>$party_option_id,
                           "estimate_dtl_id"=>$old_dtl[$i]['PartyOptionDtlTrn']['estimate_dtl_id'],
                           "menu"=>$old_dtl[$i]['PartyOptionDtlTrn']['menu'],
                           "content"=>$old_dtl[$i]['PartyOptionDtlTrn']['content'],
                           "num"=>$old_dtl[$i]['PartyOptionDtlTrn']['num'],
                           "note"=>$old_dtl[$i]['PartyOptionDtlTrn']['note'],
 	                       "reg_nm"=>$user,
 	                       "reg_dt"=>date('Y-m-d H:i:s')
 	                       );
     $party_option_dtl->create();

     if($party_option_dtl->save($party_option_dtl_data)==false){
     	 return array('result'=>false,'message'=>"パーティオプション詳細の新規作成に失敗しました。",'reason'=>$party_option_dtl->getDbo()->error."[".date('Y-m-d H:i:s')."]");
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

  	 App::import("Model", "PartyOptionTrn");
  	 $party = new PartyOptionTrn();

     $party_data = array( "final_sheet_id"=>$final_sheet_id );

     if($party->updateAll($party_data,array("customer_id"=>$customer_id))==false){
       return array('result'=>false,'message'=>"パーティオプションファイナルシートIDの更新に失敗しました。",'reason'=>$party->getDbo()->error."[".date('Y-m-d H:i:s')."]");
     }
     return array('result'=>true);
 }

  /**
   *
   * パーティオプションシートのメニュー更新
   * @param $array_params
   * @return 正常：TRUE
   *         異常：FALSE
   */
   function updateMenu($array_params){

       App::import("Model", "PartyOptionDtlTrn");
       $party_option_dtl = new PartyOptionDtlTrn();

       $party_option_dtl_data = array(
                                      "menu"=>"'".mysql_real_escape_string($array_params['menu'])."'",
                                      "num"=>$array_params['num'],
 	                                  "upd_nm"=>"'".$array_params['username']."'",
 	                                  "upd_dt"=>"'".date('Y-m-d H:i:s')."'"
 	                                  );

       /* 履歴があるので最新のメニューのIDを取得する  */
       $data = $party_option_dtl->find("first",array("fields"=>"MAX(id) as max_id","conditions"=>array("estimate_dtl_id"=>$array_params['estimate_dtl_id'])));
       if(count($data) > 0){  $max_id = $data[0]["max_id"]; }

       if($party_option_dtl->updateAll($party_option_dtl_data,array("id"=>$max_id))==false){
       	   return array('result'=>false,'message'=>"パーティオプションシートのメニュー更新に失敗しました。",'reason'=>$party_option_dtl->getDbo()->error."[".date('Y-m-d H:i:s')."]");
       }
       return array('result'=>true);
   }

    /**
     *
     * パーティーオプションシート関連テーブルの削除
     * @param $customer_id
     * @return 正常：TRUE
     *         異常：FALSE
     */
    function deletePartyOptionSheet($customer_id){

      App::import("Model", "PartyOptionTrn");
      $party_option = new PartyOptionTrn();
      //パーティーオプションヘッダ・詳細削除[カスケード削除]
      if($party_option->deleteAll(array("customer_id"=>$customer_id),true)==false){
      	 return array('result'=>false,'message'=>"パーティオプションシートの削除に失敗しました。",'reason'=>$party_option->getDbo()->error."[".date('Y-m-d H:i:s')."]");
      }
      return array('result'=>true);
    }

  /**
   *
   * 全パーティーオプション情報を更新
   * @param $array_params
   * @param $user
   * @return 正常：TRUE
   *         異常：FALSE
   */
   function saveAll($array_params,$user){

     $tr = ClassRegistry::init('TransactionManager');
	 $tr->begin();

	 if(!empty($array_params['PartyOptionTrn']))
	 {
	   /* ヘッダ更新 */
	   for($header_index=0;$header_index < count($array_params['PartyOptionTrn']);$header_index++)
	   {
	     $ret = $this->_savePartyOption($array_params['PartyOptionTrn'][$header_index],$user);
	 	 if($ret['result']==false){return $ret;}
	   }
	   /* 明細更新 */
	   for($sub_index=0;$sub_index < count($array_params['PartyOptionDtlTrn']);$sub_index++)
	   {
	      $ret = $this->_savePartyOptionDtl($array_params['PartyOptionDtlTrn'][$sub_index],$user);
	      if($ret['result']==false){return $ret;}
	   }
	 }
     $tr->commit();
     return array('result'=>true);
   }

  /**
   *
   * パーティーオプションヘッダ情報を更新
   * @param $array_params
   * @param $user
   * @return 正常：TRUE
   *         異常：FALSE
   */
   function _savePartyOption($array_params,$user){

   	 App::import("Model", "PartyOptionTrn");

   	 $fields = array('attend_nm'         ,'phone_no'      ,'cell_no' ,'email' ,
   	                 'setting_start_time','setting_end_time','setting_place' ,'delivery_term',
   	                 'delivery_place'    ,'note','upd_nm','upd_dt');

   	 $array_params['upd_nm'] = $user;
   	 $array_params['upd_dt'] = date('Y-m-d H:i:s');

     $po = new PartyOptionTrn;
	 $po->id = $array_params['id'];

 	 if($po->save($array_params,false,$fields)==false){
 	 	return array('result'=>false,'message'=>"パーティーオプションヘッダ情報更新に失敗しました。",'reason'=>$po->getDbo()->error."[".date('Y-m-d H:i:s')."]");
 	 }

 	 return array('result'=>true);
   }

  /**
   *
   * パーティーオプション詳細情報を更新
   * @param $array_params
   * @param $user
   * @return 正常：TRUE
   *         異常：FALSE
   */
   function _savePartyOptionDtl($array_params,$user){

   	 App::import("Model", "PartyOptionDtlTrn");
   	 $dtl = new PartyOptionDtlTrn;

     $fields = array('num','note' ,'upd_nm' ,'upd_dt');

     for($i=0;$i < count($array_params);$i++)
     {
 	  	 $array_params[$i]['upd_nm'] = $user;
 	     $array_params[$i]['upd_dt'] = date('Y-m-d H:i:s');
 	     $dtl->id = $array_params[$i]['id'];
 	     if($dtl->save($array_params[$i],false,$fields)==false){
 	     	return array('result'=>false,'message'=>"パーティーオプション詳細情報更新に失敗しました。",'reason'=>$dtl->getDbo()->error."[".date('Y-m-d H:i:s')."]");
 	     }
     }
    return array('result'=>true);
   }

  /**
    *
    * パーティーオプションファイナルシートに引数のベンダーが存在するかチェック
    * @param $vendor_id
    * @param $customer_id
    * @return 正常：パーティーオプションID
    *         異常：FALSE
    */
   function hasHeaderDataOfVendor($vendor_id,$customer_id){

         App::import("Model", "PartyOptionTrn");
         $party_option = new PartyOptionTrn();

         if($party_option->hasAny(array('customer_id' => $customer_id,'vendor_id'=>$vendor_id))){
            $ret = $party_option->find('first', array('fields' => 'id','conditions' => array('customer_id' => $customer_id,'vendor_id'=>$vendor_id)));
            return $ret['PartyOptionTrn']['id'];
         }else{
         	return false;
         }
   }

  /**
    *
    * パーティーオプションヘッダテーブルがサブテーブルから参照されていなければ削除する
    * @param $customer_id
    */
   function deleteHeaderIfNoSubTableData($customer_id)
   {
   	    App::import("Model", "PartyOptionTrn");
        $party_option = new PartyOptionTrn();

        App::import("Model", "PartyOptionDtlTrn");
        $party_option_dtl = new PartyOptionDtlTrn();

        $header_ids = $party_option->find('all', array('fields' => 'id','conditions' => array('customer_id' => $customer_id)));

        for($i=0;$i < count($header_ids);$i++)
        {
           if($party_option_dtl->hasAny(array('party_option_id'=>$header_ids[$i]['PartyOptionTrn']['id']))==false){
                if($party_option->delete($header_ids[$i]['PartyOptionTrn']['id'])==false){
                	return array('result'=>false,'message'=>"パーティーオプションヘッダ削除に失敗しました。",'reason'=>$party_option_dtl->getDbo()->error."[".date('Y-m-d H:i:s')."]");
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

   	  App::import("Model", "PartyOptionTrn");
      $party_option = new PartyOptionTrn();

   	  $sql = "select
                 vendor_nm,
                 attend_nm,
                 phone_no,
                 cell_no,
                 email
               from party_option_trns
              where final_sheet_id = ".$final_sheet_id."
          group by vendor_id  ";

   	  $data = $party_option->query($sql);

   	  if(count($data) > 0){

   	  	$arr = null;
   	  	for($i =0;$i < count($data);$i++){
   	  		$attr = $data[$i]["party_option_trns"];
   	  		$temp = array("part"=>"PartyOption"     ,"vendor_nm"=>$attr["vendor_nm"],"attend_nm"=>$attr["attend_nm"],
   	  		             "phone_no"=>$attr["phone_no"],"cell_no"=>$attr["cell_no"]    ,"email"=>$attr["email"]);
   	  	    $arr[] = $temp;
   	  	}
   	    return $arr;
   	  }
   	 return null;
   }
}
?>