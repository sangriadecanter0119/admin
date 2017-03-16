<?php
class PaperService extends AppModel {
    var $useTable = false;

  /**
  *
  * ペーパーシートの新規作成
  * @param $array_params
  * @return 正常：TRUE
  *         異常：FALSE
  */
  function createPaperSheet($array_params){

  	 App::import("Model", "PaperDtlTrn");
  	 $paper_dtl = new PaperDtlTrn();

  	 /* 同じベンダーのファイナルシートがない場合はヘッダを作成する */
      $paper_id = $this->hasHeaderDataOfVendor($array_params['vendor_id'], $array_params['customer_id']);
      if($paper_id == false){

        App::import("Model", "PaperTrn");
        $paper = new PaperTrn();
       //ペーパーヘッダ作成
       $paper_data = array(
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
 	  $paper->create();
      if($paper->save($paper_data)==false){
      	return array('result'=>false,'message'=>"ペーパーヘッダ作成に失敗しました。",'reason'=>$paper->getDbo()->error."[".date('Y-m-d H:i:s')."]");
      }

      $paper_id = $paper->getLastInsertID();
     }

     //ペーパーメニュー作成
     $paper_dtl_data = array(
                           "paper_id"=>$paper_id,
                           "estimate_dtl_id"=>$array_params['estimate_dtl_id'],
                           "menu"=>$array_params['menu'],
                           "type"=>$array_params['content'],
                           "num"=>$array_params['num'],
 	                       "reg_nm"=>$array_params['username'],
 	                       "reg_dt"=>date('Y-m-d H:i:s')
 	                       );
 	 $paper_dtl->create();
     if($paper_dtl->save($paper_dtl_data)==false){
     	return array('result'=>false,'message'=>"ペーパーメニュー作成に失敗しました。",'reason'=>$paper_dtl->getDbo()->error."[".date('Y-m-d H:i:s')."]");
     }
    return array('result'=>true);
 }

/**
  *
  * ペーパーシートの複製
  * @param $old_final_sheet_id
  * @param $new_final_sheet_id
  * @param $user
  */
  function copy($old_final_sheet_id,$new_final_sheet_id,$user){

  	 App::import("Model", "PaperTrn");
  	 $paper = new PaperTrn();

  	 App::import("Model", "PaperDtlTrn");
  	 $paper_dtl = new PaperDtlTrn();

     $old_header = $paper->find('all',array('conditions'=>array('final_sheet_id'=>$old_final_sheet_id)));
     if(count($old_header) > 0){
       $old_dtl = $paper_dtl->find('all',array('conditions'=>array('paper_id'=>$old_header[0]['PaperTrn']['id'])));

       //ペーパーヘッダ作成
       $paper_data = array(
                     "final_sheet_id"=>$new_final_sheet_id,
                     "customer_id"=>$old_header[0]['PaperTrn']['customer_id'],
     	             "vendor_id"=>$old_header[0]['PaperTrn']['vendor_id'],
     	             "vendor_nm"=>$old_header[0]['PaperTrn']['vendor_nm'],
                     "attend_nm"=>$old_header[0]['PaperTrn']['attend_nm'],
                     "phone_no"=>$old_header[0]['PaperTrn']['phone_no'],
                     "cell_no"=>$old_header[0]['PaperTrn']['cell_no'],
                     "email"=>$old_header[0]['PaperTrn']['email'],
                     "delivery_term"=>$old_header[$i]['PaperTrn']['delivery_term'],
                     "delivery_place"=>$old_header[$i]['PaperTrn']['delivery_place'],
                     "delivery_nm"=>$old_header[$i]['PaperTrn']['delivery_nm'],
                     "note"=>$old_header[$i]['PaperTrn']['note'],
 	                 "reg_nm"=>$user,
 	                 "reg_dt"=>date('Y-m-d H:i:s')
 	                 );
    $paper->create();
    if($paper->save($paper_data)==false){
      	  return array('result'=>false,'message'=>"ペーパーヘッダの新規作成に失敗しました。",'reason'=>$paper->getDbo()->error."[".date('Y-m-d H:i:s')."]");
    }
    $paper_id = $paper->getLastInsertID();

    for($i=0;$i < count($old_dtl);$i++){

     //ペーパー詳細作成
     $paper_dtl_data = array(
                           "paper_id"=>$paper_id,
                           "estimate_dtl_id"=>$old_dtl[$i]['PaperDtlTrn']['estimate_dtl_id'],
                           "paper_kbn"=>$old_dtl[$i]['PaperDtlTrn']['paper_kbn'],
                           "menu"=>$old_dtl[$i]['PaperDtlTrn']['menu'],
                           "type"=>$old_dtl[$i]['PaperDtlTrn']['type'],
                           "design"=>$old_dtl[$i]['PaperDtlTrn']['design'],
                           "color"=>$old_dtl[$i]['PaperDtlTrn']['color'],
                           "num"=>$old_dtl[$i]['PaperDtlTrn']['num'],
                           "note"=>$old_dtl[$i]['PaperDtlTrn']['note'],
 	                       "reg_nm"=>$user,
 	                       "reg_dt"=>date('Y-m-d H:i:s')
 	                       );
     $paper_dtl->create();

     if($paper_dtl->save($paper_dtl_data)==false){
     	 return array('result'=>false,'message'=>"ペーパー詳細の新規作成に失敗しました。",'reason'=>$paper_dtl->getDbo()->error."[".date('Y-m-d H:i:s')."]");
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

  	 App::import("Model", "PaperTrn");
  	 $paper = new PaperTrn();

     $paper_data = array( "final_sheet_id"=>$final_sheet_id );

     if($paper->updateAll($paper_data,array("customer_id"=>$customer_id))==false){
       return array('result'=>false,'message'=>"ペーパーファイナルシートIDの更新に失敗しました。",'reason'=>$papaer->getDbo()->error."[".date('Y-m-d H:i:s')."]");
     }
     return array('result'=>true);
 }

 /**
  *
  * ペーパーシートのメニュー更新
  * @param $array_params
  * @return 正常：TRUE
  *         異常：FALSE
  */
  function updateMenu($array_params){

  	 App::import("Model", "PaperDtlTrn");
  	 $paper_dtl = new PaperDtlTrn();

     $paper_dtl_data = array(
                           "menu"=>"'".mysql_real_escape_string($array_params['menu'])."'",
                           "num"=>$array_params['num'],
 	                       "upd_nm"=>"'".$array_params['username']."'",
 	                       "upd_dt"=>"'".date('Y-m-d H:i:s')."'"
 	                       );

     /* 履歴があるので最新のメニューのIDを取得する  */
     $data = $paper_dtl->find("first",array("fields"=>"MAX(id) as max_id","conditions"=>array("estimate_dtl_id"=>$array_params['estimate_dtl_id'])));
     if(count($data) > 0){  $max_id = $data[0]["max_id"]; }

     if($paper_dtl->updateAll($paper_dtl_data,array("id"=>$max_id))==false){
     	return array('result'=>false,'message'=>"ペーパーメニュー更新に失敗しました。",'reason'=>$paper_dtl->getDbo()->error."[".date('Y-m-d H:i:s')."]");
     }
     return array('result'=>true);
 }

  /**
   *
   * ペーパーシートの削除
   * @param $customer_id
   * @return 正常：TRUE
   *         異常：FALSE
   */
   function deletePaperSheet($customer_id){

      App::import("Model", "PaperTrn");
      $paper = new PaperTrn();
      //ペーパーヘッダ・詳細削除[カスケード削除]
      if($paper->deleteAll(array("customer_id"=>$customer_id),true)==false){
      	return array('result'=>false,'message'=>"ペーパーシートの削除に失敗しました。",'reason'=>$paper->getDbo()->error."[".date('Y-m-d H:i:s')."]");
      }
      return array('result'=>true);
    }

   /**
    *
    * 全ペーパー情報を更新
    * @param $array_params
    * @param $user
    * @return 正常：TRUE
    *         異常：FALSE
    */
   function saveAll($array_params,$user){

     $tr = ClassRegistry::init('TransactionManager');
	 $tr->begin();

	 if(!empty($array_params['PaperTrn']))
	 {
	   /* ヘッダ更新 */
	   for($header_index=0;$header_index < count($array_params['PaperTrn']);$header_index++)
	   {
	     $ret = $this->_savePaper($array_params['PaperTrn'][$header_index],$user);
	 	 if($ret['result']==false){return $ret;}
	   }
	   /* 明細更新 */
	   for($sub_index=0;$sub_index < count($array_params['PaperDtlTrn']);$sub_index++)
	   {
	      $ret = $this->_savePaperDtl($array_params['PaperDtlTrn'][$sub_index],$user);
	      if($ret['result']==false){return $ret;}
	   }
	 }
     $tr->commit();
     return array('result'=>true);
   }

  /**
    *
    * ペーパーヘッダ情報を更新
    * @param $array_params
    * @param $user
    * @return 正常：TRUE
    *         異常：FALSE
    */
   function _savePaper($array_params,$user){

   	 App::import("Model", "PaperTrn");

   	 $fields = array('attend_nm'    ,'phone_no','cell_no' ,'email' ,
   	                 'delivery_term','delivery_place','delivery_nm','note','upd_nm','upd_dt');

   	 $array_params['upd_nm'] = $user;
   	 $array_params['upd_dt'] = date('Y-m-d H:i:s');

     $paper = new PaperTrn;
	 $paper->id = $array_params['id'];

 	   if($paper->save($array_params,false,$fields)==false){
 	   	return array('result'=>false,'message'=>"ペーパーヘッダ情報更新に失敗しました。",'reason'=>$paper->getDbo()->error."[".date('Y-m-d H:i:s')."]");
 	   }

 	 return array('result'=>true);
   }

   /**
    *
    * ペーパー詳細情報を更新
    * @param $array_params
    * @param $user
    * @return 正常：TRUE
    *         異常：FALSE
    */
   function _savePaperDtl($array_params,$user){

   	 App::import("Model", "PaperDtlTrn");
   	 $dtl = new PaperDtlTrn;

     $fields = array('type','paper_kbn','num','note' ,'upd_nm' ,'upd_dt');

     for($i=0;$i < count($array_params);$i++)
     {
 	  	 $array_params[$i]['upd_nm'] = $user;
 	     $array_params[$i]['upd_dt'] = date('Y-m-d H:i:s');

 	     $dtl->id = $array_params[$i]['id'];
 	     if($dtl->save($array_params[$i],false,$fields)==false){
 	     	return array('result'=>false,'message'=>"ペーパー詳細情報更新に失敗しました。",'reason'=>$dtl->getDbo()->error."[".date('Y-m-d H:i:s')."]");
 	     }
    }
    return array('result'=>true);
   }

  /**
    *
    * ペーパーファイナルシートに引数のベンダーが存在するかチェック
    * @param $vendor_id
    * @param $customer_id
    * @return 正常：ペーパーID
    *         異常：FALSE
    */
   function hasHeaderDataOfVendor($vendor_id,$customer_id){

         App::import("Model", "PaperTrn");
         $paper = new PaperTrn();

         if($paper->hasAny(array('customer_id' => $customer_id,'vendor_id'=>$vendor_id))){
            $ret = $paper->find('first', array('fields' => 'id','conditions' => array('customer_id' => $customer_id,'vendor_id'=>$vendor_id)));
            return $ret['PaperTrn']['id'];
         }else{
         	return false;
         }
   }

  /**
    *
    * ペーパーヘッダテーブルがサブテーブルから参照されていなければ削除する
    * @param $customer_id
    */
   function deleteHeaderIfNoSubTableData($customer_id)
   {
   	    App::import("Model", "PaperTrn");
        $paper = new PaperTrn();

        App::import("Model", "PaperDtlTrn");
        $paper_dtl = new PaperDtlTrn();

        $header_ids = $paper->find('all', array('fields' => 'id','conditions' => array('customer_id' => $customer_id)));

        for($i=0;$i < count($header_ids);$i++)
        {
           if($paper_dtl->hasAny(array('paper_id'=>$header_ids[$i]['PaperTrn']['id']))==false){
                if($paper->delete($header_ids[$i]['PaperTrn']['id'])==false){
                	return array('result'=>false,'message'=>"ペーパーヘッダテーブル削除に失敗しました。",'reason'=>$paper->getDbo()->error."[".date('Y-m-d H:i:s')."]");
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

   	  App::import("Model", "PaperTrn");
      $paper = new PaperTrn();

   	  $sql = "select
                 vendor_nm,
                 attend_nm,
                 phone_no,
                 cell_no,
                 email
               from paper_trns
              where final_sheet_id = ".$final_sheet_id."
          group by vendor_id  ";

   	  $data = $paper->query($sql);

   	  if(count($data) > 0){

   	  	$arr = null;
   	  	for($i =0;$i < count($data);$i++){
   	  		$attr = $data[$i]["paper_trns"];
   	  		$temp = array("part"=>"Paper"     ,"vendor_nm"=>$attr["vendor_nm"],"attend_nm"=>$attr["attend_nm"],
   	  		             "phone_no"=>$attr["phone_no"],"cell_no"=>$attr["cell_no"]    ,"email"=>$attr["email"]);
   	  	    $arr[] = $temp;
   	  	}
   	    return $arr;
   	  }
   	 return null;
   }
}
?>