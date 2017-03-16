<?php
class AvService extends AppModel {
    var $useTable = false;

 /**
  *
  * AVシートの新規作成
  * @param $array_params
  * @return 正常：TRUE
  *         異常：FALSE
  */
  function createAvSheet($array_params){

  	 App::import("Model", "AvMenuTrn");
  	 $av_menu = new AvMenuTrn();

  	  /* 同じベンダーのファイナルシートがない場合はヘッダを作成する */
      $av_id = $this->hasHeaderDataOfVendor($array_params['vendor_id'], $array_params['customer_id']);
      if($av_id == false){

      	App::import("Model", "AvTrn");
      	$av = new AvTrn();
        //AVヘッダ作成
        $av_data = array(
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
 	   $av->create();
       if($av->save($av_data)==false){
       	 return array('result'=>false,'message'=>"AVヘッダ作成に失敗しました。",'reason'=>$av_menu->getDbo()->error."[".date('Y-m-d H:i:s')."]");
       }

       $av_id = $av->getLastInsertID();
     }
     //AVメニュー作成
     $av_menu_data = array(
                           "av_id"=>$av_id,
                           "estimate_dtl_id"=>$array_params['estimate_dtl_id'],
                           "menu"=>$array_params['menu'],
                           "av_count"=>$array_params['num'],
 	                       "reg_nm"=>$array_params['username'],
 	                       "reg_dt"=>date('Y-m-d H:i:s')
 	                       );
 	 $av_menu->create();
     if($av_menu->save($av_menu_data)==false){
     	return array('result'=>false,'message'=>"AVメニュー作成に失敗しました。",'reason'=>$av_menu->getDbo()->error."[".date('Y-m-d H:i:s')."]");
     }
     return array('result'=>true);
 }

  /**
    * AVの複製
    * @param $old_final_sheet_id
    * @param $new_final_sheet_id
    * @param $user
    */
   function copy($old_final_sheet_id,$new_final_sheet_id,$user){

  	 App::import("Model", "AvTrn");
  	 $av = new AvTrn();

  	 App::import("Model", "AvMenuTrn");
  	 $av_menu = new AvMenuTrn();

     $old_header = $av->find('all',array('conditions'=>array('final_sheet_id'=>$old_final_sheet_id)));
     if(count($old_header) > 0){
       $old_menu = $av_menu->find('all',array('conditions'=>array('av_id'=>$old_header[0]['AvTrn']['id'])));

       //AVヘッダ作成
       $av_data = array(
                     "final_sheet_id"=>$new_final_sheet_id,
                     "customer_id"=>$old_header[0]['AvTrn']['customer_id'],
     	             "vendor_id"=>$old_header[0]['AvTrn']['vendor_id'],
     	             "vendor_nm"=>$old_header[0]['AvTrn']['vendor_nm'],
                     "attend_nm"=>$old_header[0]['AvTrn']['attend_nm'],
                     "phone_no"=>$old_header[0]['AvTrn']['phone_no'],
                     "cell_no"=>$old_header[0]['AvTrn']['cell_no'],
                     "email"=>$old_header[0]['AvTrn']['email'],
                     "note"=>$old_header[0]['AvTrn']['note'],
 	                 "reg_nm"=>$user,
 	                 "reg_dt"=>date('Y-m-d H:i:s')
 	                 );
    $av->create();
    if($av->save($av_data)==false){
      	  return array('result'=>false,'message'=>"AVヘッダの新規作成に失敗しました。",'reason'=>$av->getDbo()->error."[".date('Y-m-d H:i:s')."]");
    }
    $av_id = $av->getLastInsertID();

    for($i=0;$i < count($old_menu);$i++){

     //AV詳細作成
     $av_menu_data = array(
                           "av_id"=>$av_id,
                           "estimate_dtl_id"=>$old_menu[$i]['AvMenuTrn']['estimate_dtl_id'],
                           "menu"=>$old_menu[$i]['AvMenuTrn']['menu'],
                           "av_count"=>$old_menu[$i]['AvMenuTrn']['av_count'],
                           "setting_start_time"=>$old_menu[$i]['AvMenuTrn']['setting_start_time'],
                           "setting_end_time"=>$old_menu[$i]['AvMenuTrn']['setting_end_time'],
                           "setting_place"=>$old_menu[$i]['AvMenuTrn']['setting_place'],
                           "note"=>$old_menu[$i]['AvMenuTrn']['note'],
 	                       "reg_nm"=>$user,
 	                       "reg_dt"=>date('Y-m-d H:i:s')
 	                       );
     $av_menu->create();

     if($av_menu->save($av_menu_data)==false){
     	 return array('result'=>false,'message'=>"AV詳細の新規作成に失敗しました。",'reason'=>$av_menu->getDbo()->error."[".date('Y-m-d H:i:s')."]");
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

  	 App::import("Model", "AvTrn");
  	 $av = new AvTrn();

     $av_data = array( "final_sheet_id"=>$final_sheet_id );

     if($av->updateAll($av_data,array("customer_id"=>$customer_id))==false){
       return array('result'=>false,'message'=>"AVファイナルシートIDの更新に失敗しました。",'reason'=>$av->getDbo()->error."[".date('Y-m-d H:i:s')."]");
     }
     return array('result'=>true);
 }

 /**
  *
  * AVシートのメニュー更新
  * @param $array_params
  * @return 正常：TRUE
  *         異常：FALSE
  */
  function updateMenu($array_params){

  	 App::import("Model", "AvMenuTrn");
  	 $av_menu = new AvMenuTrn();

     //AVメニュー更新
     $av_menu_data = array(
                           "menu"=>"'".mysql_real_escape_string($array_params['menu'])."'",
                           "av_count"=>$array_params['num'],
 	                       "upd_nm"=>"'".$array_params['username']."'",
 	                       "upd_dt"=>"'".date('Y-m-d H:i:s')."'"
 	                       );

     /* 履歴があるので最新のメニューのIDを取得する  */
     $data = $av_menu->find("first",array("fields"=>"MAX(id) as max_id","conditions"=>array("estimate_dtl_id"=>$array_params['estimate_dtl_id'])));
     if(count($data) > 0){  $max_id = $data[0]["max_id"]; }

     if($av_menu->updateAll($av_menu_data,array("id"=>$max_id))==false){
       return array('result'=>false,'message'=>"AVメニュー更新に失敗しました。",'reason'=>$av_menu->getDbo()->error."[".date('Y-m-d H:i:s')."]");
     }
     return array('result'=>true);
 }

 /**
  *
  * AVシート関連テーブルの削除
  * @param $customer_id
  * @return 正常：TRUE
  *         異常：FALSE
  */
  function deleteAvSheet($customer_id){

    App::import("Model", "AvTrn");
    $av = new AvTrn();
    //AVヘッダ・メニュー削除[カスケード削除]
    if($av->deleteAll(array("customer_id"=>$customer_id),true)==false){
    	return array('result'=>false,'message'=>"AV情報削除に失敗しました。",'reason'=>$av->getDbo()->error."[".date('Y-m-d H:i:s')."]");
    }
    return array('result'=>true);
  }

  /**
   *
   * 全Av情報を更新
   * @param $array_params
   * @param $user
   * @return 正常：TRUE
   *         異常：FALSE
   */
   function saveAll($array_params,$user){

     $tr = ClassRegistry::init('TransactionManager');
	 $tr->begin();

	 if(!empty($array_params['AvTrn']))
	 {
	   /* ヘッダ更新 */
	   for($header_index=0;$header_index < count($array_params['AvTrn']);$header_index++)
	   {
	      $ret = $this->_saveAv($array_params['AvTrn'][$header_index],$user);
	 	  if($ret['result']==false){return $ret;}
	   }
	   /* 明細更新 */
	   for($sub_index=0;$sub_index < count($array_params['AvMenuTrn']);$sub_index++)
	   {
	       $ret = $this->_saveAvMenu($array_params['AvMenuTrn'][$sub_index],$user);
	       if($ret['result']==false){return $ret;}
	   }
	 }
     $tr->commit();
     return array('result'=>true);
   }

  /**
   *
   * Avヘッダ情報を更新
   * @param $array_params
   * @param $user
   * @return 正常：TRUE
   *         異常：FALSE
   */
   function _saveAv($array_params,$user){

   	 App::import("Model", "AvTrn");

   	 $fields = array('attend_nm','phone_no' ,'cell_no' ,'email' ,'note' ,'upd_nm','upd_dt');

   	 $array_params['upd_nm'] = $user;
   	 $array_params['upd_dt'] = date('Y-m-d H:i:s');

     $av = new AvTrn;
	 $av->id = $array_params['id'];

 	 if($av->save($array_params,false,$fields)==false){
        return array('result'=>false,'message'=>"AVヘッダ更新に失敗しました。",'reason'=>$av->getDbo()->error."[".date('Y-m-d H:i:s')."]");
 	 }
 	return array('result'=>true);
   }

  /**
   *
   * Avメニュー情報を更新
   * @param $array_params
   * @param $user
   * @return 正常：TRUE
   *         異常：FALSE
   */
   function _saveAvMenu($array_params,$user){

   	 App::import("Model", "AvMenuTrn");
   	 $av = new AvMenuTrn;

     $fields = array('av_count','setting_start_time','setting_end_time','setting_place','note','upd_nm' ,'upd_dt');

     for($i=0;$i < count($array_params);$i++)
     {
 	  	 $array_params[$i]['upd_nm'] = $user;
 	     $array_params[$i]['upd_dt'] = date('Y-m-d H:i:s');
 	     $av->id = $array_params[$i]['id'];
 	     if($av->save($array_params[$i],false,$fields)==false){
 	        return array('result'=>false,'message'=>"AVメニュー更新に失敗しました。",'reason'=>$av->getDbo()->error."[".date('Y-m-d H:i:s')."]");
 	     }
    }
    return array('result'=>true);
   }

  /**
    *
    * Avファイナルシートに引数のベンダーが存在するかチェック
    * @param $vendor_id
    * @param $customer_id
    * @return 正常：AvID
    *         異常：FALSE
    */
   function hasHeaderDataOfVendor($vendor_id,$customer_id){

         App::import("Model", "AvTrn");
         $av = new AvTrn();

         if($av->hasAny(array('customer_id' => $customer_id,'vendor_id'=>$vendor_id))){
            $ret = $av->find('first', array('fields' => 'id','conditions' => array('customer_id' => $customer_id,'vendor_id'=>$vendor_id)));
            return $ret['AvTrn']['id'];
         }else{
         	return false;
         }
   }

   /**
    *
    * Avヘッダテーブルがサブテーブルから参照されていなければ削除する
    * @param $customer_id
    */
   function deleteHeaderIfNoSubTableData($customer_id)
   {
   	    App::import("Model", "AvTrn");
        $av = new AvTrn();

        App::import("Model", "AvMenuTrn");
        $av_menu = new AvMenuTrn();

        $header_ids = $av->find('all', array('fields' => 'id','conditions' => array('customer_id' => $customer_id)));

        for($i=0;$i < count($header_ids);$i++)
        {
           if($av_menu->hasAny(array('av_id'=>$header_ids[$i]['AvTrn']['id']))==false){
                if($av->delete($header_ids[$i]['AvTrn']['id'])==false){
                	return array('result'=>false,'message'=>"AVヘッダ削除に失敗しました。",'reason'=>$av_menu->getDbo()->error."[".date('Y-m-d H:i:s')."]");
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

   	  App::import("Model", "AvTrn");
      $av = new AvTrn();

   	  $sql = "select
                 vendor_nm,
                 attend_nm,
                 phone_no,
                 cell_no,
                 email
               from av_trns
              where final_sheet_id = ".$final_sheet_id."
          group by vendor_id  ";

   	  $data = $av->query($sql);

   	  if(count($data) > 0){

   	  	$arr = null;
   	  	for($i =0;$i < count($data);$i++){
   	  		$attr = $data[$i]["av_trns"];
   	  		$temp = array("part"=>"Av"                ,"vendor_nm"=>$attr["vendor_nm"],"attend_nm"=>$attr["attend_nm"],
   	  		             "phone_no"=>$attr["phone_no"],"cell_no"=>$attr["cell_no"]    ,"email"=>$attr["email"]);
   	  	    $arr[] = $temp;
   	  	}
   	    return $arr;
   	  }
   	 return null;
   }
}
?>