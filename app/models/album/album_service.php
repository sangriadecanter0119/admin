<?php
class AlbumService extends AppModel {
    var $useTable = false;

 /**
  *
  * アルバムシートの新規作成
  * @param $array_params
  * @return 正常：TRUE
  *         異常：FALSE
  */
  function createAlbumSheet($array_params){

  	 App::import("Model", "AlbumDtlTrn");
  	 $album_dtl = new AlbumDtlTrn();

  	 /* 同じベンダーのファイナルシートがない場合はヘッダを作成する */
     $album_id = $this->hasHeaderDataOfVendor($array_params['vendor_id'], $array_params['customer_id']);
     if($album_id == false){

      	App::import("Model", "AlbumTrn");
      	$album = new AlbumTrn();
        //アルバムヘッダ作成
        $album_data = array(
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
 	   $album->create();
       if($album->save($album_data)==false){
       	  return array('result'=>false,'message'=>"アルバムヘッダの新規作成に失敗しました。",'reason'=>$album->getDbo()->error."[".date('Y-m-d H:i:s')."]");
       }
       $album_id = $album->getLastInsertID();
     }
     //アルバム詳細作成
     $album_dtl_data = array(
                           "album_id"=>$album_id,
                           "estimate_dtl_id"=>$array_params['estimate_dtl_id'],
                           "type"=>$array_params['menu'],
 	                       "reg_nm"=>$array_params['username'],
 	                       "reg_dt"=>date('Y-m-d H:i:s')
 	                       );
 	 $album_dtl->create();
     if($album_dtl->save($album_dtl_data)==false){
     	 return array('result'=>false,'message'=>"アルバム詳細の新規作成に失敗しました。",'reason'=>$album->getDbo()->error."[".date('Y-m-d H:i:s')."]");
     }
     return array('result'=>true);
 }

  /**
  *
  * アルバムシートのメニュー更新
  * @param $array_params
  * @return 正常：TRUE
  *         異常：FALSE
  */
  function updateMenu($array_params){

  	 App::import("Model", "AlbumDtlTrn");
  	 $album_dtl = new AlbumDtlTrn();

     $album_dtl_data = array(
                           "type"=>"'".mysql_real_escape_string($array_params['menu'])."'",
 	                       "upd_nm"=>"'".$array_params['username']."'",
 	                       "upd_dt"=>"'".date('Y-m-d H:i:s')."'"
 	                       );

 	 /* 履歴があるので最新のメニューのIDを取得する  */
     $data = $album_dtl->find("first",array("fields"=>"MAX(id) as max_id","conditions"=>array("estimate_dtl_id"=>$array_params['estimate_dtl_id'])));
     if(count($data) > 0){  $max_id = $data[0]["max_id"]; }

     if($album_dtl->updateAll($album_dtl_data,array("id"=>$max_id))==false){
       return array('result'=>false,'message'=>"アルバムシートのメニュー更新に失敗しました。",'reason'=>$album_dtl->getDbo()->error."[".date('Y-m-d H:i:s')."]");
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

  	 App::import("Model", "AlbumTrn");
  	 $album = new AlbumTrn();

     $album_data = array( "final_sheet_id"=>$final_sheet_id );

     if($album->updateAll($album_data,array("customer_id"=>$customer_id))==false){
       return array('result'=>false,'message'=>"アルバムファイナルシートIDの更新に失敗しました。",'reason'=>$album->getDbo()->error."[".date('Y-m-d H:i:s')."]");
     }
     return array('result'=>true);
 }

 /**
  *
  * アルバムシートの複製
  * @param $old_final_sheet_id
  * @param $new_final_sheet_id
  * @param $user
  */
  function copy($old_final_sheet_id,$new_final_sheet_id,$user){

  	 App::import("Model", "AlbumTrn");
  	 $album = new AlbumTrn();

  	 App::import("Model", "AlbumDtlTrn");
  	 $album_dtl = new AlbumDtlTrn();

     $old_header = $album->find('all',array('conditions'=>array('final_sheet_id'=>$old_final_sheet_id)));
     if(count($old_header) > 0){
       $old_dtl = $album_dtl->find('all',array('conditions'=>array('album_id'=>$old_header[0]['AlbumTrn']['id'])));

       //アルバムヘッダ作成
       $album_data = array(
                     "final_sheet_id"=>$new_final_sheet_id,
                     "customer_id"=>$old_header[0]['AlbumTrn']['customer_id'],
     	             "vendor_id"=>$old_header[0]['AlbumTrn']['vendor_id'],
     	             "vendor_nm"=>$old_header[0]['AlbumTrn']['vendor_nm'],
                     "attend_nm"=>$old_header[0]['AlbumTrn']['attend_nm'],
                     "phone_no"=>$old_header[0]['AlbumTrn']['phone_no'],
                     "cell_no"=>$old_header[0]['AlbumTrn']['cell_no'],
                     "email"=>$old_header[0]['AlbumTrn']['email'],
                     "note"=>$old_header[0]['AlbumTrn']['note'],
 	                 "reg_nm"=>$user,
 	                 "reg_dt"=>date('Y-m-d H:i:s')
 	                 );
    $album->create();
    if($album->save($album_data)==false){
      	  return array('result'=>false,'message'=>"アルバムヘッダの新規作成に失敗しました。",'reason'=>$album->getDbo()->error."[".date('Y-m-d H:i:s')."]");
    }
    $album_id = $album->getLastInsertID();

    for($i=0;$i < count($old_dtl);$i++){

     //アルバム詳細作成
     $album_dtl_data = array(
                           "album_id"=>$album_id,
                           "estimate_dtl_id"=>$old_dtl[$i]['AlbumDtlTrn']['estimate_dtl_id'],
                           "no"=>$old_dtl[$i]['AlbumDtlTrn']['no'],
                           "type"=>$old_dtl[$i]['AlbumDtlTrn']['type'],
                           "delivery_term"=>$old_dtl[$i]['AlbumDtlTrn']['delivery_term'],
                           "note"=>$old_dtl[$i]['AlbumDtlTrn']['note'],
 	                       "reg_nm"=>$user,
 	                       "reg_dt"=>date('Y-m-d H:i:s')
 	                       );
     $album_dtl->create();

     if($album_dtl->save($album_dtl_data)==false){
     	 return array('result'=>false,'message'=>"アルバム詳細の新規作成に失敗しました。",'reason'=>$album_dtl->getDbo()->error."[".date('Y-m-d H:i:s')."]");
     }
    }
   }
     return array('result'=>true);
  }

 /**
  *
  * アルバムシート関連テーブルの削除
  * @param $customer_id
  * @return 正常：TRUE
  *         異常：FALSE
  */
  function deleteAlbumSheet($customer_id){

    App::import("Model", "AlbumTrn");
    $album = new AlbumTrn();
    //アルバムヘッダ・詳細削除[カスケード削除]
    if($album->deleteAll(array("customer_id"=>$customer_id),true)==false){
    	return array('result'=>false,'message'=>"アルバム情報更新に失敗しました。",'reason'=>$album->getDbo()->error."[".date('Y-m-d H:i:s')."]");
    }
    return array('result'=>true);
  }

  /**
   *
   * 全Album情報を更新
   * @param $array_params
   * @param $user
   * @return 正常：TRUE
   *         異常：FALSE
   */
   function saveAll($array_params,$user){

     $tr = ClassRegistry::init('TransactionManager');
	 $tr->begin();

	 if(!empty($array_params['AlbumTrn']))
	 {
	   /* ヘッダ更新 */
	   for($header_index=0;$header_index < count($array_params['AlbumTrn']);$header_index++)
	   {
	      $ret = $this->_saveAlbum($array_params['AlbumTrn'][$header_index],$user);
	      if($ret['result'] == false){ return $ret; }
	   }
	   /* 明細更新 */
	   for($sub_index=0;$sub_index < count($array_params['AlbumDtlTrn']);$sub_index++)
	   {
	     $ret =$this->_saveAlbumDtl($array_params['AlbumDtlTrn'][$sub_index],$user);
	     if($ret['result'] == false){ return $ret; }
	   }
	 }
     $tr->commit();
     return array('result'=>true);
   }

  /**
   *
   * Albumヘッダ情報を更新
   * @param $array_params
   * @param $user
   * @return 正常：TRUE
   *         異常：FALSE
   */
   function _saveAlbum($array_params,$user){

   	 App::import("Model", "AlbumTrn");

   	 $fields = array('attend_nm','phone_no' ,'cell_no' ,'email' ,'note' ,'upd_nm','upd_dt');

   	 $array_params['upd_nm'] = $user;
   	 $array_params['upd_dt'] = date('Y-m-d H:i:s');

     $album = new AlbumTrn;
	 $album->id = $array_params['id'];

 	 if($album->save($array_params,false,$fields)==false){
       return array('result'=>false,'message'=>"Albumヘッダ情報更新に失敗しました。",'reason'=>$album->getDbo()->error."[".date('Y-m-d H:i:s')."]");
 	 }
 	return array('result'=>true);
   }

  /**
   *
   * Album詳細情報を更新
   * @param $array_params
   * @param $user
   * @return 正常：TRUE
   *         異常：FALSE
   */
   function _saveAlbumDtl($array_params,$user){

   	 App::import("Model", "AlbumDtlTrn");
   	 $album_dtl = new AlbumDtlTrn;

     $fields = array('type','delivery_term','note','upd_nm' ,'upd_dt');

     for($i=0;$i < count($array_params);$i++)
     {
 	  	 $array_params[$i]['upd_nm'] = $user;
 	     $array_params[$i]['upd_dt'] = date('Y-m-d H:i:s');
 	     $album_dtl->id = $array_params[$i]['id'];
 	     if($album_dtl->save($array_params[$i],false,$fields)==false){
 	     	return array('result'=>false,'message'=>"Album詳細情報更新に失敗しました。",'reason'=>$album_dtl->getDbo()->error."[".date('Y-m-d H:i:s')."]");
 	     }
    }
    return array('result'=>true);
   }

  /**
    *
    * Albumファイナルシートに引数のベンダーが存在するかチェック
    * @param $vendor_id
    * @param $customer_id
    * @return 正常：AlbumID
    *         異常：FALSE
    */
   function hasHeaderDataOfVendor($vendor_id,$customer_id){

         App::import("Model", "AlbumTrn");
         $album = new AlbumTrn();

         if($album->hasAny(array('customer_id' => $customer_id,'vendor_id'=>$vendor_id))){
            $ret = $album->find('first', array('fields' => 'id','conditions' => array('customer_id' => $customer_id,'vendor_id'=>$vendor_id)));
            return $ret['AlbumTrn']['id'];
         }else{
         	return false;
         }
   }

  /**
    *
    * Albumヘッダテーブルがサブテーブルから参照されていなければ削除する
    * @param $customer_id
    */
   function deleteHeaderIfNoSubTableData($customer_id)
   {
   	    App::import("Model", "AlbumTrn");
        $album = new AlbumTrn();

        App::import("Model", "AlbumDtlTrn");
        $album_dtl = new AlbumDtlTrn();

        $header_ids = $album->find('all', array('fields' => 'id','conditions' => array('customer_id' => $customer_id)));

        for($i=0;$i < count($header_ids);$i++)
        {
           if($album_dtl->hasAny(array('album_id'=>$header_ids[$i]['AlbumTrn']['id']))==false){
                if($album->delete($header_ids[$i]['AlbumTrn']['id'])==false){
                	return array('result'=>false,'message'=>"Albumヘッダテーブル削除に失敗しました。",'reason'=>$album->getDbo()->error."[".date('Y-m-d H:i:s')."]");
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

   	  App::import("Model", "AlbumTrn");
      $album = new AlbumTrn();

   	  $sql = "select
                 vendor_nm,
                 attend_nm,
                 phone_no,
                 cell_no,
                 email
               from album_trns
              where final_sheet_id = ".$final_sheet_id."
          group by vendor_id  ";

   	  $data = $album->query($sql);

   	  if(count($data) > 0){

   	  	$arr = null;
   	  	for($i =0;$i < count($data);$i++){
   	  		$attr = $data[$i]["album_trns"];
   	  		$temp = array("part"=>"Album"             ,"vendor_nm"=>$attr["vendor_nm"],"attend_nm"=>$attr["attend_nm"],
   	  		             "phone_no"=>$attr["phone_no"],"cell_no"=>$attr["cell_no"]    ,"email"=>$attr["email"]);
   	  	    $arr[] = $temp;
   	  	}
   	    return $arr;
   	  }
   	 return null;
   }
}
?>