<?php
class LinenService extends AppModel {
    var $useTable = false;

 /**
  *
  * リネンシートの新規作成
  * @param $array_params
  * @return 正常：TRUE
  *         異常：FALSE
  */
  function createLinenSheet($array_params){

      App::import("Model", "LinenDtlTrn");
      $linen_dtl = new LinenDtlTrn();

      /* 同じベンダーのファイナルシートがない場合はヘッダを作成する */
      $line_id = $this->hasHeaderDataOfVendor($array_params['vendor_id'], $array_params['customer_id']);
      if($line_id == false){

      	App::import("Model", "LinenTrn");
      	$linen = new LinenTrn();

      	//リネンヘッダ作成
        $linen_data = array(
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
 	    $linen->create();
        if($linen->save($linen_data)==false){
        	return array('result'=>false,'message'=>"リネンヘッダ作成に失敗しました。",'reason'=>$linen->getDbo()->error."[".date('Y-m-d H:i:s')."]");
        }

        $line_id = $linen->getLastInsertID();
      }

      //リネン詳細作成
      $linen_dtl_data = array(
                              "linen_id"=>$line_id,
                              "estimate_dtl_id"=>$array_params['estimate_dtl_id'],
                              "menu"=>$array_params['menu'],
                              "num"=>$array_params['num'],
 	                          "reg_nm"=>$array_params['username'],
 	                          "reg_dt"=>date('Y-m-d H:i:s')
 	                          );
 	  $linen_dtl->create();
      if($linen_dtl->save($linen_dtl_data)==false){
      	return array('result'=>false,'message'=>"リネン詳細作成 に失敗しました。",'reason'=>$linen_dtl->getDbo()->error."[".date('Y-m-d H:i:s')."]");
      }
      return array('result'=>true);
   }

/**
  *
  * リネンシートの複製
  * @param $old_final_sheet_id
  * @param $new_final_sheet_id
  * @param $user
  */
  function copy($old_final_sheet_id,$new_final_sheet_id,$user){

  	 App::import("Model", "LinenTrn");
  	 $linen = new LinenTrn();

  	 App::import("Model", "LinenDtlTrn");
  	 $linen_dtl = new LinenDtlTrn();

     $old_header = $linen->find('all',array('conditions'=>array('final_sheet_id'=>$old_final_sheet_id)));
     if(count($old_header) > 0){
       $old_dtl = $linen_dtl->find('all',array('conditions'=>array('linen_id'=>$old_header[0]['LinenTrn']['id'])));

       //リネンヘッダ作成
       $linen_data = array(
                     "final_sheet_id"=>$new_final_sheet_id,
                     "customer_id"=>$old_header[0]['LinenTrn']['customer_id'],
     	             "vendor_id"=>$old_header[0]['LinenTrn']['vendor_id'],
     	             "vendor_nm"=>$old_header[0]['LinenTrn']['vendor_nm'],
                     "attend_nm"=>$old_header[0]['LinenTrn']['attend_nm'],
                     "phone_no"=>$old_header[0]['LinenTrn']['phone_no'],
                     "cell_no"=>$old_header[0]['LinenTrn']['cell_no'],
                     "email"=>$old_header[0]['LinenTrn']['email'],
                     "delivery_term"=>$old_header[0]['LinenTrn']['delivery_term'],
                     "delivery_place"=>$old_header[0]['LinenTrn']['delivery_place'],
                     "delivery_nm"=>$old_header[0]['LinenTrn']['delivery_nm'],
                     "note"=>$old_header[0]['LinenTrn']['note'],
 	                 "reg_nm"=>$user,
 	                 "reg_dt"=>date('Y-m-d H:i:s')
 	                 );
    $linen->create();
    if($linen->save($linen_data)==false){
      	  return array('result'=>false,'message'=>"リネンヘッダの新規作成に失敗しました。",'reason'=>$linen->getDbo()->error."[".date('Y-m-d H:i:s')."]");
    }
    $linen_id = $linen->getLastInsertID();

    for($i=0;$i < count($old_dtl);$i++){

     //リネン詳細作成
     $linen_dtl_data = array(
                           "linen_id"=>$linen_id,
                           "estimate_dtl_id"=>$old_dtl[$i]['LinenDtlTrn']['estimate_dtl_id'],
                           "linen_kbn"=>$old_dtl[$i]['LinenDtlTrn']['linen_kbn'],
                           "menu"=>$old_dtl[$i]['LinenDtlTrn']['menu'],
                           "type"=>$old_dtl[$i]['LinenDtlTrn']['type'],
                           "size"=>$old_dtl[$i]['LinenDtlTrn']['size'],
                           "color"=>$old_dtl[$i]['LinenDtlTrn']['color'],
                           "num"=>$old_dtl[$i]['LinenDtlTrn']['num'],
                           "note"=>$old_dtl[$i]['LinenDtlTrn']['note'],
 	                       "reg_nm"=>$user,
 	                       "reg_dt"=>date('Y-m-d H:i:s')
 	                       );
     $linen_dtl->create();

     if($linen_dtl->save($linen_dtl_data)==false){
     	 return array('result'=>false,'message'=>"リネン詳細の新規作成に失敗しました。",'reason'=>$linen_dtl->getDbo()->error."[".date('Y-m-d H:i:s')."]");
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

  	 App::import("Model", "LinenTrn");
  	 $linen = new LinenTrn();

     $linen_data = array( "final_sheet_id"=>$final_sheet_id );

     if($linen->updateAll($linen_data,array("customer_id"=>$customer_id))==false){
       return array('result'=>false,'message'=>"リネンファイナルシートIDの更新に失敗しました。",'reason'=>$linen->getDbo()->error."[".date('Y-m-d H:i:s')."]");
     }
     return array('result'=>true);
 }

 /**
  *
  * リネンシートのメニュー更新
  * @param $array_params
  * @return 正常：TRUE
  *         異常：FALSE
  */
  function updateMenu($array_params){

      App::import("Model", "LinenDtlTrn");
      $linen_dtl = new LinenDtlTrn();

      $linen_dtl_data = array(
                              "menu"=>"'".mysql_real_escape_string($array_params['menu'])."'",
                              "num"=>$array_params['num'],
 	                          "upd_nm"=>"'".$array_params['username']."'",
 	                          "upd_dt"=>"'".date('Y-m-d H:i:s')."'"
 	                          );

     /* 履歴があるので最新のメニューのIDを取得する  */
     $data = $linen_dtl->find("first",array("fields"=>"MAX(id) as max_id","conditions"=>array("estimate_dtl_id"=>$array_params['estimate_dtl_id'])));
     if(count($data) > 0){  $max_id = $data[0]["max_id"]; }

      if($linen_dtl->updateAll($linen_dtl_data,array("id"=>$max_id))==false){
      	return array('result'=>false,'message'=>"リネンシートのメニュー更新に失敗しました。",'reason'=>$linen_dtl->getDbo()->error."[".date('Y-m-d H:i:s')."]");
      }
     return array('result'=>true);
   }

 /**
  *
  * リネンシート関連テーブルの削除
  * @param $customer_id
  * @return 正常：TRUE
  *         異常：FALSE
  */
  function deleteLinenSheet($customer_id){

      App::import("Model", "LinenTrn");
      $linen = new LinenTrn();
      //リネンオプションヘッダ・詳細削除[カスケード削除]
      if($linen->deleteAll(array("customer_id"=>$customer_id),true)==false){
      	return array('result'=>false,'message'=>"リネンシート関連テーブルの削除に失敗しました。",'reason'=>$linen->getDbo()->error."[".date('Y-m-d H:i:s')."]");
      }
      return array('result'=>true);
  }

  /**
   *
   * 全リネン情報を更新
   * @param $array_params
   * @param $user
   * @return 正常：TRUE
   *         異常：FALSE
   */
   function saveAll($array_params,$user){

     $tr = ClassRegistry::init('TransactionManager');
	 $tr->begin();

	 if(!empty($array_params['LinenTrn']))
	 {
	 	/* ヘッダ更新 */
	   for($header_index=0;$header_index < count($array_params['LinenTrn']);$header_index++)
	   {
	      $ret = $this->_saveLinen($array_params['LinenTrn'][$header_index],$user);
	 	  if($ret['result']==false){return $ret;}
	   }
	   /* 明細更新 */
	   for($sub_index=0;$sub_index < count($array_params['LinenDtlTrn']);$sub_index++)
	   {
	     $ret = $this->_saveLinenDtl($array_params['LinenDtlTrn'][$sub_index],$user);
	     if($ret['result']==false){return $ret;}
	   }
	 }
     $tr->commit();
     return array('result'=>true);
   }

  /**
   *
   * リネンヘッダ情報を更新
   * @param $array_params
   * @param $user
   * @return 正常：TRUE
   *         異常：FALSE
   */
   function _saveLinen($array_params,$user){

   	 App::import("Model", "LinenTrn");

   	 $fields = array('attend_nm' ,'phone_no','cell_no' ,'email' ,
   	                 'delivery_term','delivery_place','delivery_nm','note','upd_nm','upd_dt');

   	 $array_params['upd_nm'] = $user;
   	 $array_params['upd_dt'] = date('Y-m-d H:i:s');

     $linen = new LinenTrn;
	 $linen->id = $array_params['id'];

 	   if($linen->save($array_params,false,$fields)==false){
 	   	 return array('result'=>false,'message'=>"リネンヘッダ情報更新に失敗しました。",'reason'=>$linen->getDbo()->error."[".date('Y-m-d H:i:s')."]");
 	   }

 	 return array('result'=>true);
   }

  /**
   *
   * リネン詳細情報を更新
   * @param $array_params
   * @param $user
   * @return 正常：TRUE
   *         異常：FALSE
   */
   function _saveLinenDtl($array_params,$user){

   	 App::import("Model", "LinenDtlTrn");
   	 $dtl = new LinenDtlTrn;

     $fields = array('type','size','color','num','note' ,'upd_nm' ,'upd_dt');

     for($i=0;$i < count($array_params);$i++)
     {
 	  	 $array_params[$i]['upd_nm'] = $user;
 	     $array_params[$i]['upd_dt'] = date('Y-m-d H:i:s');

 	     $dtl->id = $array_params[$i]['id'];
 	     if($dtl->save($array_params[$i],false,$fields)==false){
 	     	return array('result'=>false,'message'=>"リネンヘッダ情報更新に失敗しました。",'reason'=>$dtl->getDbo()->error."[".date('Y-m-d H:i:s')."]");
 	     }
     }
     return array('result'=>true);
   }

  /**
    *
    * リネンファイナルシートに引数のベンダーが存在するかチェック
    * @param $vendor_id
    * @param $customer_id
    * @return 正常：リネンID
    *         異常：FALSE
    */
   function hasHeaderDataOfVendor($vendor_id,$customer_id){

         App::import("Model", "LinenTrn");
         $linen = new LinenTrn();

         if($linen->hasAny(array('customer_id' => $customer_id,'vendor_id'=>$vendor_id))){
            $ret = $linen->find('first', array('fields' => 'id','conditions' => array('customer_id' => $customer_id,'vendor_id'=>$vendor_id)));
            return $ret['LinenTrn']['id'];
         }else{
         	return false;
         }
   }

   /**
    *
    * リネンヘッダテーブルがサブテーブルから参照されていなければ削除する
    * @param $customer_id
    */
   function deleteHeaderIfNoSubTableData($customer_id)
   {
   	    App::import("Model", "LinenTrn");
        $linen = new LinenTrn();

        App::import("Model", "LinenDtlTrn");
        $linen_dtl = new LinenDtlTrn();

        $header_ids = $linen->find('all', array('fields' => 'id','conditions' => array('customer_id' => $customer_id)));

        for($i=0;$i < count($header_ids);$i++)
        {
           if($linen_dtl->hasAny(array('linen_id'=>$header_ids[$i]['LinenTrn']['id']))==false){
                if($linen->delete($header_ids[$i]['LinenTrn']['id'])==false){
                	return array('result'=>false,'message'=>"リネンヘッダテーブル削除に失敗しました。",'reason'=>$linen->getDbo()->error."[".date('Y-m-d H:i:s')."]");
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

   	  App::import("Model", "LinenTrn");
      $linen = new LinenTrn();

   	  $sql = "select
                 vendor_nm,
                 attend_nm,
                 phone_no,
                 cell_no,
                 email
               from linen_trns
              where final_sheet_id = ".$final_sheet_id."
          group by vendor_id  ";

   	  $data = $linen->query($sql);

   	  if(count($data) > 0){

   	  	$arr = null;
   	  	for($i =0;$i < count($data);$i++){
   	  		$attr = $data[$i]["linen_trns"];
   	  		$temp = array("part"=>"Linen"     ,"vendor_nm"=>$attr["vendor_nm"],"attend_nm"=>$attr["attend_nm"],
   	  		             "phone_no"=>$attr["phone_no"],"cell_no"=>$attr["cell_no"]    ,"email"=>$attr["email"]);
   	  	    $arr[] = $temp;
   	  	}
   	    return $arr;
   	  }
   	 return null;
   }
}
?>