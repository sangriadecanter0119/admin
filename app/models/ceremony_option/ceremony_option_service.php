<?php
class CeremonyOptionService extends AppModel {
    var $useTable = false;

 /**
  *
  * セレモニーオプションシートの新規作成
  * @param $array_params
  * @return 正常：TRUE
  *         異常：FALSE
  */
  function createCeremonyOptionSheet($array_params){

      App::import("Model", "CeremonyOptionDtlTrn");
      $ceremony_option_dtl = new CeremonyOptionDtlTrn();

      /* 同じベンダーのファイナルシートがない場合はヘッダを作成する */
      $ceremony_id = $this->hasHeaderDataOfVendor($array_params['vendor_id'], $array_params['customer_id']);
      if($ceremony_id == false){

       	App::import("Model", "CeremonyOptionTrn");
      	$ceremony_option = new CeremonyOptionTrn();
      	//セレモニーヘッダ作成
        $ceremony_option_data = array(
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
 	    $ceremony_option->create();
        if($ceremony_option->save($ceremony_option_data)==false){
        	return array('result'=>false,'message'=>"セレモニーオプションシートヘッダの作成に失敗しました。",'reason'=>$ceremony_option->getDbo()->error."[".date('Y-m-d H:i:s')."]");
        }

        $ceremony_id = $ceremony_option->getLastInsertID();
      }

      //セレモニーオプション詳細作成
      $ceremony_option_dtl_data = array(
                                     "ceremony_option_id"=>$ceremony_id,
                                     "estimate_dtl_id"=>$array_params['estimate_dtl_id'],
                                     "no"=>"1",
                                     "option_nm"=>$array_params['menu'],
                                     "option_count"=>$array_params['num'],
 	                                 "reg_nm"=>$array_params['username'],
 	                                 "reg_dt"=>date('Y-m-d H:i:s')
 	                                 );
 	  $ceremony_option_dtl->create();
      if($ceremony_option_dtl->save($ceremony_option_dtl_data)==false){
      	return array('result'=>false,'message'=>"セレモニーオプションシート詳細の作成に失敗しました。",'reason'=>$ceremony_option_dtl->getDbo()->error."[".date('Y-m-d H:i:s')."]");
      }
     return array('result'=>true);
    }

/**
  *
  * セレモニーオプションシートの複製
  * @param $old_final_sheet_id
  * @param $new_final_sheet_id
  * @param $user
  */
  function copy($old_final_sheet_id,$new_final_sheet_id,$user){

  	 App::import("Model", "CeremonyOptionTrn");
  	 $ceremony_option = new CeremonyOptionTrn();

  	 App::import("Model", "CeremonyOptionDtlTrn");
  	 $ceremony_option_dtl = new CeremonyOptionDtlTrn();

     $old_header = $ceremony_option->find('all',array('conditions'=>array('final_sheet_id'=>$old_final_sheet_id)));
     if(count($old_header) > 0){
       $old_dtl = $ceremony_option_dtl->find('all',array('conditions'=>array('ceremony_option_id'=>$old_header[0]['CeremonyOptionTrn']['id'])));

       //セレモニーオプションヘッダ作成
       $ceremony_option_data = array(
                     "final_sheet_id"=>$new_final_sheet_id,
                     "customer_id"=>$old_header[0]['CeremonyOptionTrn']['customer_id'],
     	             "vendor_id"=>$old_header[0]['CeremonyOptionTrn']['vendor_id'],
     	             "vendor_nm"=>$old_header[0]['CeremonyOptionTrn']['vendor_nm'],
                     "attend_nm"=>$old_header[0]['CeremonyOptionTrn']['attend_nm'],
                     "phone_no"=>$old_header[0]['CeremonyOptionTrn']['phone_no'],
                     "cell_no"=>$old_header[0]['CeremonyOptionTrn']['cell_no'],
                     "email"=>$old_header[0]['CeremonyOptionTrn']['email'],
                     "note"=>$old_header[0]['CeremonyOptionTrn']['note'],
 	                 "reg_nm"=>$user,
 	                 "reg_dt"=>date('Y-m-d H:i:s')
 	                 );
    $ceremony_option->create();
    if($ceremony_option->save($ceremony_option_data)==false){
      	  return array('result'=>false,'message'=>"セレモニーオプションヘッダの新規作成に失敗しました。",'reason'=>$ceremony_option->getDbo()->error."[".date('Y-m-d H:i:s')."]");
    }
    $ceremony_option_id = $ceremony_option->getLastInsertID();

    for($i=0;$i < count($old_dtl);$i++){

     //セレモニーオプション詳細作成
     $ceremony_option_dtl_data = array(
                           "ceremony_option_id"=>$ceremony_option_id,
                           "estimate_dtl_id"=>$old_dtl[$i]['CeremonyOptionDtlTrn']['estimate_dtl_id'],
                           "no"=>$old_dtl[$i]['CeremonyOptionDtlTrn']['no'],
                           "option_nm"=>$old_dtl[$i]['CeremonyOptionDtlTrn']['option_nm'],
                           "option_count"=>$old_dtl[$i]['CeremonyOptionDtlTrn']['option_count'],
                           "note"=>$old_dtl[$i]['CeremonyOptionDtlTrn']['note'],
 	                       "reg_nm"=>$user,
 	                       "reg_dt"=>date('Y-m-d H:i:s')
 	                       );
     $ceremony_option_dtl->create();

     if($ceremony_option_dtl->save($ceremony_option_dtl_data)==false){
     	 return array('result'=>false,'message'=>"セレモニーオプション詳細の新規作成に失敗しました。",'reason'=>$ceremony_option_dtl->getDbo()->error."[".date('Y-m-d H:i:s')."]");
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

  	 App::import("Model", "CeremonyOptionTrn");
  	 $ceremony = new CeremonyOptionTrn();

     $ceremony_data = array( "final_sheet_id"=>$final_sheet_id );

     if($ceremony->updateAll($ceremony_data,array("customer_id"=>$customer_id))==false){
       return array('result'=>false,'message'=>"セレモニーオプションファイナルシートIDの更新に失敗しました。",'reason'=>$ceremony->getDbo()->error."[".date('Y-m-d H:i:s')."]");
     }
     return array('result'=>true);
 }

/**
  *
  * セレモニーオプションシートのメニュー更新
  * @param $array_params
  * @return 正常：TRUE
  *         異常：FALSE
  */
  function updateMenu($array_params){

      App::import("Model", "CeremonyOptionDtlTrn");
      $ceremony_option_dtl = new CeremonyOptionDtlTrn();

      //セレモニーオプションメニュー更新
      $ceremony_option_dtl_data = array(
                                     "option_nm"=>"'".mysql_real_escape_string($array_params['menu'])."'",
                                     "option_count"=>$array_params['num'],
 	                                 "upd_nm"=>"'".$array_params['username']."'",
 	                                 "upd_dt"=>"'".date('Y-m-d H:i:s')."'"
 	                                 );

     /* 履歴があるので最新のメニューのIDを取得する  */
     $data = $ceremony_option_dtl->find("first",array("fields"=>"MAX(id) as max_id","conditions"=>array("estimate_dtl_id"=>$array_params['estimate_dtl_id'])));
     if(count($data) > 0){  $max_id = $data[0]["max_id"]; }

      if($ceremony_option_dtl->updateAll($ceremony_option_dtl_data,array("id"=>$max_id))==false){
         return array('result'=>false,'message'=>"セレモニーオプションシートのメニュー更新に失敗しました。",'reason'=>$ceremony_option_dtl->getDbo()->error."[".date('Y-m-d H:i:s')."]");
      }

     return array('result'=>true);
    }

 /**
  *
  * セレモニーオプションシートの削除
  * @param $customer_id
  * @return 正常：TRUE
  *         異常：FALSE
  */
  function deleteCeremonyOptionSheet($customer_id){

      App::import("Model", "CeremonyOptionTrn");
      $ceremony_option = new CeremonyOptionTrn();
      //セレモニーオプション・セレモニーオプション詳細削除[カスケード削除]
      if($ceremony_option->deleteAll(array("customer_id"=>$customer_id),true)==false){
      	return array('result'=>false,'message'=>"セレモニーオプションシートの削除に失敗しました。",'reason'=>$ceremony_option->getDbo()->error."[".date('Y-m-d H:i:s')."]");
      }
      return array('result'=>true);
  }

  /**
    *
    * 全セレモニーオプション情報を更新
    * @param $array_params
    * @param $user
    * @return 正常：TRUE
    *         異常：FALSE
    */
   function saveAll($array_params,$user){

     $tr = ClassRegistry::init('TransactionManager');
	 $tr->begin();

	 if(!empty($array_params['CeremonyOptionTrn']))
	 {
	   /* ヘッダ更新 */
	   for($header_index=0;$header_index < count($array_params['CeremonyOptionTrn']);$header_index++)
	   {
	      $ret = $this->_saveCeremonyOption($array_params['CeremonyOptionTrn'][$header_index],$user);
	 	  if($ret['result']==false){return $ret;}
	   }
	   /* 明細更新 */
	   for($sub_index=0;$sub_index < count($array_params['CeremonyOptionDtlTrn']);$sub_index++)
	   {
	      $ret = $this->_saveCeremonyOptionDtl($array_params['CeremonyOptionDtlTrn'][$sub_index],$user);
	 	  if($ret['result']==false){return $ret;}
	   }
	 }
     $tr->commit();
     return array('result'=>true);
   }

   /**
    *
    * セレモニーオプションヘッダ情報を更新
    * @param $array_params
    * @param $user
    * @return 正常：TRUE
    *         異常：FALSE
    */
   function _saveCeremonyOption($array_params,$user){

   	 App::import("Model", "CeremonyOptionTrn");

   	 $fields = array('attend_nm','phone_no','cell_no','email','note','upd_nm','upd_dt');

   	 $array_params['upd_nm'] = $user;
   	 $array_params['upd_dt'] = date('Y-m-d H:i:s');

     $ceremony_option = new CeremonyOptionTrn;
	 $ceremony_option->id = $array_params['id'];

 	 if($ceremony_option->save($array_params,false,$fields)==false){
      return array('result'=>false,'message'=>"セレモニーオプションヘッダ情報更新に失敗しました。",'reason'=>$ceremony_option->getDbo()->error."[".date('Y-m-d H:i:s')."]");
 	 }
 	 return array('result'=>true);
   }

  /**
    *
    * セレモニーオプション詳細情報を更新
    * @param $array_params
    * @param $user
    * @return 正常：TRUE
    *         異常：FALSE
    */
   function _saveCeremonyOptionDtl($array_params,$user){

   	 App::import("Model", "CeremonyOptionDtlTrn");
   	 $sub = new CeremonyOptionDtlTrn;

     $fields = array('no','option_nm','option_count','note','upd_nm','upd_dt');

    for($i=0;$i < count($array_params);$i++)
    {
 	  	 $array_params[$i]['upd_nm'] = $user;
 	     $array_params[$i]['upd_dt'] = date('Y-m-d H:i:s');
 	     $sub->id = $array_params[$i]['id'];
 	     if($sub->save($array_params[$i],false,$fields)==false){
 	        return array('result'=>false,'message'=>"セレモニーオプション詳細情報に失敗しました。",'reason'=>$sub->getDbo()->error."[".date('Y-m-d H:i:s')."]");
 	     }
    }
    return array('result'=>true);
  }

   /**
    *
    * セレモニーオプションファイナルシートに引数のベンダーが存在するかチェック
    * @param $vendor_id
    * @param $customer_id
    * @return 正常：セレモニーオプションID
    *         異常：FALSE
    */
   function hasHeaderDataOfVendor($vendor_id,$customer_id){

      App::import("Model", "CeremonyOptionTrn");
      $ceremony_option = new CeremonyOptionTrn();

      if($ceremony_option->hasAny(array('customer_id' => $customer_id,'vendor_id'=>$vendor_id))){
          $ret = $ceremony_option->find('first', array('fields' => 'id','conditions' => array('customer_id' => $customer_id,'vendor_id'=>$vendor_id)));
           return $ret['CeremonyOptionTrn']['id'];
      }else{
        	return false;
      }
   }

   /**
    *
    * セレモニーオプションヘッダテーブルがサブテーブルから参照されていなければ削除する
    * @param $customer_id
    */
   function deleteHeaderIfNoSubTableData($customer_id)
   {
   	    App::import("Model", "CeremonyOptionTrn");
        $ceremony = new CeremonyOptionTrn();

        App::import("Model", "CeremonyOptionDtlTrn");
        $ceremony_dtl = new CeremonyOptionDtlTrn();

        $header_ids = $ceremony->find('all', array('fields' => 'id','conditions' => array('customer_id' => $customer_id)));

        for($i=0;$i < count($header_ids);$i++)
        {
           if($ceremony_dtl->hasAny(array('ceremony_option_id'=>$header_ids[$i]['CeremonyOptionTrn']['id']))==false){
                if($ceremony->delete($header_ids[$i]['CeremonyOptionTrn']['id'])==false){
                	return array('result'=>false,'message'=>"セレモニーオプションヘッダテーブルの削除に失敗しました。",'reason'=>$ceremony->getDbo()->error."[".date('Y-m-d H:i:s')."]");
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

   	  App::import("Model", "CeremonyOptionTrn");
      $ceremony_option = new CeremonyOptionTrn();

   	  $sql = "select
                 vendor_nm,
                 attend_nm,
                 phone_no,
                 cell_no,
                 email
               from ceremony_option_trns
              where final_sheet_id = ".$final_sheet_id."
          group by vendor_id  ";

   	  $data = $ceremony_option->query($sql);

   	  if(count($data) > 0){

   	  	$arr = null;
   	  	for($i =0;$i < count($data);$i++){
   	  		$attr = $data[$i]["ceremony_option_trns"];
   	  		$temp = array("part"=>"CeremonyOption"    ,"vendor_nm"=>$attr["vendor_nm"],"attend_nm"=>$attr["attend_nm"],
   	  		             "phone_no"=>$attr["phone_no"],"cell_no"=>$attr["cell_no"]    ,"email"=>$attr["email"]);
   	  	    $arr[] = $temp;
   	  	}
   	    return $arr;
   	  }
   	 return null;
   }
}
?>