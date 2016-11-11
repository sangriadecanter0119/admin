<?php
class FlowerService extends AppModel {
    var $useTable = false;

   /**
     *
     * フラワーシートの新規作成
     * @param $array_params
     * @param $flower_kbn
     * @return 正常：TRUE
     *         異常：FALSE
     */
    function createFlowerSheet($array_params,$flower_kbn){

       App::import("Model", "FlowerDtlTrn");
       $flower_dtl = new FlowerDtlTrn();

       /* 同じベンダーのファイナルシートがない場合はヘッダを作成する */
       $flower_id = $this->hasHeaderDataOfVendor($array_params['vendor_id'], $array_params['customer_id']);
       if($flower_id == false)
	   {
	   	  App::import("Model", "FlowerTrn");
	   	  $flower = new FlowerTrn();

         //フラワーヘッダ作成
         $flower_data = array(
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
 	    $flower->create();
        if($flower->save($flower_data)==false){
        	return array('result'=>false,'message'=>"フラワーヘッダ作成に失敗しました。",'reason'=>$flower->getDbo()->error."[".date('Y-m-d H:i:s')."]");
        }

        $flower_id = $flower->getLastInsertID();
	   }

       //フラワー明細作成
       $flower_dtl_data = array(
     	                        "flower_id"=>$flower_id,
                                "estimate_dtl_id"=>$array_params['estimate_dtl_id'],
                                "flower_kbn"=>$flower_kbn,
                                "flower_content"=>$array_params['menu'],
                                "num"=>$array_params['num'],
 	                            "reg_nm"=>$array_params['username'],
 	                            "reg_dt"=>date('Y-m-d H:i:s')
 	                           );
 	   $flower_dtl->create();
       if($flower_dtl->save($flower_dtl_data)==false){
        	return array('result'=>false,'message'=>"フラワー明細作成に失敗しました。",'reason'=>$flower_dtl->getDbo()->error."[".date('Y-m-d H:i:s')."]");
       }
      return array('result'=>true);
  }

  /**
    *
    * フラワーシートの複製
    * @param $old_final_sheet_id
    * @param $new_final_sheet_id
    * @param $user
    */
   function copy($old_final_sheet_id,$new_final_sheet_id,$user){

  	 App::import("Model", "FlowerTrn");
  	 $flower = new FlowerTrn();

  	 App::import("Model", "FlowerDtlTrn");
  	 $flower_dtl = new FlowerDtlTrn();

     $old_header = $flower->find('all',array('conditions'=>array('final_sheet_id'=>$old_final_sheet_id)));
     if(count($old_header) > 0){
       $old_dtl = $flower_dtl->find('all',array('conditions'=>array('flower_id'=>$old_header[0]['FlowerTrn']['id'])));

       //フラワーヘッダ作成
       $flower_data = array(
                     "final_sheet_id"=>$new_final_sheet_id,
                     "customer_id"=>$old_header[0]['FlowerTrn']['customer_id'],
     	             "vendor_id"=>$old_header[0]['FlowerTrn']['vendor_id'],
     	             "vendor_nm"=>$old_header[0]['FlowerTrn']['vendor_nm'],
                     "attend_nm"=>$old_header[0]['FlowerTrn']['attend_nm'],
                     "phone_no"=>$old_header[0]['FlowerTrn']['phone_no'],
                     "cell_no"=>$old_header[0]['FlowerTrn']['cell_no'],
                     "email"=>$old_header[0]['FlowerTrn']['email'],
                     "main_florist_nm"=>$old_header[0]['FlowerTrn']['main_florist_nm'],
     	             "main_delivery_term"=>$old_header[0]['FlowerTrn']['main_delivery_term'],
     	             "main_delivery_place"=>$old_header[0]['FlowerTrn']['main_delivery_place'],
                     "main_note"=>$old_header[0]['FlowerTrn']['main_note'],
                     "ceremony_florist_nm"=>$old_header[0]['FlowerTrn']['ceremony_florist_nm'],
     	             "ceremony_delivery_term"=>$old_header[0]['FlowerTrn']['ceremony_delivery_term'],
     	             "ceremony_delivery_place"=>$old_header[0]['FlowerTrn']['ceremony_delivery_place'],
                     "ceremony_note"=>$old_header[0]['FlowerTrn']['ceremony_note'],
                     "reception_florist_nm"=>$old_header[0]['FlowerTrn']['reception_florist_nm'],
     	             "reception_delivery_term"=>$old_header[0]['FlowerTrn']['reception_delivery_term'],
     	             "reception_delivery_place"=>$old_header[0]['FlowerTrn']['reception_delivery_place'],
                     "reception_note"=>$old_header[0]['FlowerTrn']['reception_note'],
 	                 "reg_nm"=>$user,
 	                 "reg_dt"=>date('Y-m-d H:i:s')
 	                 );
    $flower->create();
    if($flower->save($flower_data)==false){
      	  return array('result'=>false,'message'=>"フラワーヘッダの新規作成に失敗しました。",'reason'=>$flower->getDbo()->error."[".date('Y-m-d H:i:s')."]");
    }
    $flower_id = $flower->getLastInsertID();

    for($i=0;$i < count($old_dtl);$i++){

     //フラワー詳細作成
     $flower_dtl_data = array(
                           "flower_id"=>$flower_id,
                           "estimate_dtl_id"=>$old_dtl[$i]['FlowerDtlTrn']['estimate_dtl_id'],
                           "flower_kbn"=>$old_dtl[$i]['FlowerDtlTrn']['flower_kbn'],
                           "flower_content"=>$old_dtl[$i]['FlowerDtlTrn']['flower_content'],
                           "flower_type"=>$old_dtl[$i]['FlowerDtlTrn']['flower_type'],
                           "num"=>$old_dtl[$i]['FlowerDtlTrn']['num'],
                           "note"=>$old_dtl[$i]['FlowerDtlTrn']['note'],
 	                       "reg_nm"=>$user,
 	                       "reg_dt"=>date('Y-m-d H:i:s')
 	                       );
     $flower_dtl->create();

     if($flower_dtl->save($flower_dtl_data)==false){
     	 return array('result'=>false,'message'=>"フラワー詳細の新規作成に失敗しました。",'reason'=>$flower_dtl->getDbo()->error."[".date('Y-m-d H:i:s')."]");
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

  	 App::import("Model", "FlowerTrn");
  	 $flower = new FlowerTrn();

     $flower_data = array( "final_sheet_id"=>$final_sheet_id );

     if($flower->updateAll($flower_data,array("customer_id"=>$customer_id))==false){
       return array('result'=>false,'message'=>"フラワーファイナルシートIDの更新に失敗しました。",'reason'=>$flower->getDbo()->error."[".date('Y-m-d H:i:s')."]");
     }
     return array('result'=>true);
 }

   /**
     *
     * フラワーシートのメニュー更新
     * @param $array_params
     * @return 正常：TRUE
     *         異常：FALSE
     */
    function updateMenu($array_params){

       App::import("Model", "FlowerDtlTrn");
       $flower_dtl = new FlowerDtlTrn();

       $flower_dtl_data = array(
                                "flower_content"=>"'".mysql_real_escape_string($array_params['menu'])."'",
                                "num"=>$array_params['num'],
 	                            "reg_nm"=>"'".$array_params['username']."'",
 	                            "reg_dt"=>"'".date('Y-m-d H:i:s')."'"
 	                           );

       /* 履歴があるので最新のメニューのIDを取得する  */
       $data = $flower_dtl->find("first",array("fields"=>"MAX(id) as max_id","conditions"=>array("estimate_dtl_id"=>$array_params['estimate_dtl_id'])));
       if(count($data) > 0){  $max_id = $data[0]["max_id"]; }

       if($flower_dtl->updateAll($flower_dtl_data,array("id"=>$max_id))==false){
         	return array('result'=>false,'message'=>"フラワーシートのメニュー更新に失敗しました。",'reason'=>$flower_dtl->getDbo()->error."[".date('Y-m-d H:i:s')."]");
       }
      return array('result'=>true);
  }
    /**
     *
     * フラワーシートの削除
     * @param $customer_id
     * @return 正常：TRUE
     *         異常：FALSE
     */
    function deleteFlowerSheet($customer_id){

      App::import("Model", "FlowerTrn");
      $flower = new FlowerTrn();
      //フラワーヘッダ・明細削除[カスケード削除]
      if($flower->deleteAll(array("customer_id"=>$customer_id),true)==false){
      	return array('result'=>false,'message'=>"フラワーシートの削除に失敗しました。",'reason'=>$flower->getDbo()->error."[".date('Y-m-d H:i:s')."]");
      }
      return array('result'=>true);
    }

  /**
   *
   * 全フラワー情報を更新
   * @param $array_params
   * @param $user
   * @return 正常：TRUE
   *         異常：FALSE
   */
   function saveAll($array_params,$user){

     $tr = ClassRegistry::init('TransactionManager');
	 $tr->begin();

	 if(!empty($array_params['FlowerTrn']))
	 {
	 	/* ヘッダ更新 */
	   for($header_index=0;$header_index < count($array_params['FlowerTrn']);$header_index++)
	   {
	      $ret = $this->_saveFlower($array_params['FlowerTrn'][$header_index],$user);
	 	  if($ret['result']==false){return $ret;}
	   }
	   /* 明細更新 */
	   for($sub_index=0;$sub_index < count($array_params['FlowerDtlTrn']);$sub_index++)
	   {
	      $ret =$this->_saveFlowerDtl($array_params['FlowerDtlTrn'][$sub_index],$user);
	      if($ret['result']==false){return $ret;}
	   }
	 }
     $tr->commit();
     return array('result'=>true);
   }

 /**
   *
   * フラワーヘッダ情報を更新
   * @param $array_params
   * @param $user
   * @return 正常：TRUE
   *         異常：FALSE
   */
   function _saveFlower($array_params,$user){

   	 App::import("Model", "FlowerTrn");

     $fields = array('attend_nm'           ,'phone_no'                ,'cell_no'    ,'email',
                     'main_florist_nm'     ,'main_delivery_term'     ,'main_delivery_place'     ,'main_note',
                     'ceremony_florist_nm' ,'ceremony_delivery_term' ,'ceremony_delivery_place' ,'ceremony_note',
                     'reception_florist_nm','reception_delivery_term','reception_delivery_place','reception_note',
                     'upd_nm','upd_dt');

   	 $array_params['upd_nm'] = $user;
   	 $array_params['upd_dt'] = date('Y-m-d H:i:s');

     $flower = new FlowerTrn;
	 $flower->id = $array_params['id'];

 	   if($flower->save($array_params,false,$fields)==false){
 	      return array('result'=>false,'message'=>"フラワーヘッダ情報を更新に失敗しました。",'reason'=>$flower->getDbo()->error."[".date('Y-m-d H:i:s')."]");
 	   }
 	 return array('result'=>true);
   }

 /**
   *
   * フラワー明細情報を更新
   * @param $array_params
   * @param $user
   * @return 正常：TRUE
   *         異常：FALSE
   */
   function _saveFlowerDtl($array_params,$user){

   	 App::import("Model", "FlowerDtlTrn");
   	 $flower = new FlowerDtlTrn;

     $fields = array('flower_type','num','note','upd_nm','upd_dt');

     for($i=0;$i < count($array_params);$i++)
     {
 	  	 $array_params[$i]['upd_nm'] = $user;
 	     $array_params[$i]['upd_dt'] = date('Y-m-d H:i:s');
 	     $flower->id = $array_params[$i]['id'];
 	     if($flower->save($array_params[$i],false,$fields)==false){
 	     	return array('result'=>false,'message'=>"フラワー明細情報を更新に失敗しました。",'reason'=>$flower->getDbo()->error."[".date('Y-m-d H:i:s')."]");
 	     }
    }
    return array('result'=>true);
   }

  /**
    *
    * フラワーファイナルシートに引数のベンダーが存在するかチェック
    * @param $vendor_id
    * @param $customer_id
    * @return 正常：フラワーID
    *         異常：FALSE
    */
   function hasHeaderDataOfVendor($vendor_id,$customer_id){

         App::import("Model", "FlowerTrn");
         $flower = new FlowerTrn();

         if($flower->hasAny(array('customer_id' => $customer_id,'vendor_id'=>$vendor_id))){
            $ret = $flower->find('first', array('fields' => 'id','conditions' => array('customer_id' => $customer_id,'vendor_id'=>$vendor_id)));
            return $ret['FlowerTrn']['id'];
         }else{
         	return false;
         }
   }

   /**
    *
    * フラワーヘッダテーブルがサブテーブルから参照されていなければ削除する
    * @param $customer_id
    */
   function deleteHeaderIfNoSubTableData($customer_id)
   {
   	    App::import("Model", "FlowerTrn");
        $flower = new FlowerTrn();

        App::import("Model", "FlowerDtlTrn");
        $flower_dtl = new FlowerDtlTrn();

        $header_ids = $flower->find('all', array('fields' => 'id','conditions' => array('customer_id' => $customer_id)));

        for($i=0;$i < count($header_ids);$i++)
        {
           if($flower_dtl->hasAny(array('flower_id'=>$header_ids[$i]['FlowerTrn']['id']))==false){
                if($flower->delete($header_ids[$i]['FlowerTrn']['id'])==false){
                	return array('result'=>false,'message'=>"フラワーヘッダテーブル削除に失敗しました。",'reason'=>$flower->getDbo()->error."[".date('Y-m-d H:i:s')."]");
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

   	  App::import("Model", "FlowerTrn");
      $flower = new FlowerTrn();

   	  $sql = "select
                 vendor_nm,
                 attend_nm,
                 phone_no,
                 cell_no,
                 email
               from flower_trns
              where final_sheet_id = ".$final_sheet_id."
          group by vendor_id  ";

   	  $data = $flower->query($sql);

   	  if(count($data) > 0){

   	  	$arr = null;
   	  	for($i =0;$i < count($data);$i++){
   	  		$attr = $data[$i]["flower_trns"];
   	  		$temp = array("part"=>"Flower"                ,"vendor_nm"=>$attr["vendor_nm"],"attend_nm"=>$attr["attend_nm"],
   	  		             "phone_no"=>$attr["phone_no"],"cell_no"=>$attr["cell_no"]    ,"email"=>$attr["email"]);
   	  	    $arr[] = $temp;
   	  	}
   	    return $arr;
   	  }
   	 return null;
   }
}
?>