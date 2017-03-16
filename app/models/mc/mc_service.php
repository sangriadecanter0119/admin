<?php
class McService extends AppModel {
    var $useTable = false;

 /**
  *
  * MCシートの新規作成
  * @param $array_params
  * @return 正常：TRUE
  *         異常：FALSE
  */
  function createMcSheet($array_params){

   	   App::import("Model", "McTrn");
   	   $mc = new McTrn();
       //Mc作成
       $mc_data = array(
     	                "customer_id"=>$array_params['customer_id'],
                        "final_sheet_id"=>$array_params['final_sheet_id'],
     	                "estimate_dtl_id"=>$array_params['estimate_dtl_id'],
     	                "menu"=>$array_params['menu'],
     	                "mc_num"=>$array_params['num'],
                        "vendor_id"=>$array_params['vendor_id'],
     	                "vendor_nm"=>$array_params['vendor_nm'],
                        "attend_nm"=>$array_params['vendor_attend_nm'],
                        "phone_no"=>$array_params['vendor_phone_no'],
                        "cell_no"=>$array_params['vendor_cell_no'],
                        "email"=>$array_params['vendor_email'],
 	                    "reg_nm"=>$array_params['username'],
 	                    "reg_dt"=>date('Y-m-d H:i:s')
 	                    );
 	   $mc->create();
       if($mc->save($mc_data)==false){
       	 return array('result'=>false,'message'=>"MCシートの作成に失敗しました。",'reason'=>$mc->getDbo()->error."[".date('Y-m-d H:i:s')."]");
       }
       return array('result'=>true);
   }

 /*
  * MCシートの複製
  * @param $old_final_sheet_id
  * @param $new_final_sheet_id
  * @param $user
  */
  function copy($old_final_sheet_id,$new_final_sheet_id,$user){

  	 App::import("Model", "McTrn");
  	 $mc = new McTrn();

     $old_header = $mc->find('all',array('conditions'=>array('final_sheet_id'=>$old_final_sheet_id)));
     if(count($old_header) > 0){

       //MCヘッダ作成
       $mc_data = array(
                     "final_sheet_id"=>$new_final_sheet_id,
                     "estimate_dtl_id"=>$old_header[0]['McTrn']['estimate_dtl_id'],
                     "customer_id"=>$old_header[0]['McTrn']['customer_id'],
     	             "vendor_id"=>$old_header[0]['McTrn']['vendor_id'],
     	             "vendor_nm"=>$old_header[0]['McTrn']['vendor_nm'],
                     "attend_nm"=>$old_header[0]['McTrn']['attend_nm'],
                     "phone_no"=>$old_header[0]['McTrn']['phone_no'],
                     "cell_no"=>$old_header[0]['McTrn']['cell_no'],
                     "email"=>$old_header[0]['McTrn']['email'],
                     "menu"=>$old_header[0]['McTrn']['menu'],
                     "mc_num"=>$old_header[0]['McTrn']['mc_num'],
                     "working_start_time"=>$old_header[0]['McTrn']['working_start_time'],
                     "working_end_time"=>$old_header[0]['McTrn']['working_end_time'],
                     "working_total"=>$old_header[0]['McTrn']['working_total'],
                     "start_place"=>$old_header[0]['McTrn']['start_place'],
                     "end_place"=>$old_header[0]['McTrn']['end_place'],
                     "note"=>$old_header[0]['McTrn']['note'],
                     "rw_note"=>$old_header[0]['McTrn']['rw_note'],
 	                 "reg_nm"=>$user,
 	                 "reg_dt"=>date('Y-m-d H:i:s')
 	                 );
    $mc->create();
    if($mc->save($mc_data)==false){
      	  return array('result'=>false,'message'=>"MCヘッダの新規作成に失敗しました。",'reason'=>$mc->getDbo()->error."[".date('Y-m-d H:i:s')."]");
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

  	 App::import("Model", "McTrn");
  	 $mc = new McTrn();

     $mc_data = array( "final_sheet_id"=>$final_sheet_id );

     if($mc->updateAll($mc_data,array("customer_id"=>$customer_id))==false){
       return array('result'=>false,'message'=>"MCファイナルシートIDの更新に失敗しました。",'reason'=>$mc->getDbo()->error."[".date('Y-m-d H:i:s')."]");
     }
     return array('result'=>true);
 }

 /**
  *
  * MCシートのメニュー更新
  * @param $array_params
  * @return 正常：TRUE
  *         異常：FALSE
  */
  function updateMenu($array_params){

   	   App::import("Model", "McTrn");
   	   $mc = new McTrn();

       $mc_data = array(
     	                "menu"=>"'".mysql_real_escape_string($array_params['menu'])."'",
     	                "mc_num"=>$array_params['num'],
 	                    "upd_nm"=>"'".$array_params['username']."'",
 	                    "upd_dt"=>"'".date('Y-m-d H:i:s')."'"
 	                    );

       /* 履歴があるので最新のメニューのIDを取得する  */
       $data = $mc->find("first",array("fields"=>"MAX(id) as max_id","conditions"=>array("estimate_dtl_id"=>$array_params['estimate_dtl_id'])));
       if(count($data) > 0){  $max_id = $data[0]["max_id"]; }

       if($mc->updateAll($mc_data,array("id"=>$max_id))==false){
       	  return array('result'=>false,'message'=>"MCシートのメニュー更新に失敗しました。",'reason'=>$mc->getDbo()->error."[".date('Y-m-d H:i:s')."]");
       }
       return array('result'=>true);
   }

 /**
  *
  * MCシートの削除
  * @param $customer_id
  * @return 正常：TRUE
  *         異常：FALSE
  */
  function deleteMcSheet($customer_id){

    App::import("Model", "McTrn");
    $mc = new McTrn();
    //MC削除[カスケード削除]
    if($mc->deleteAll(array("customer_id"=>$customer_id),true)==false){
    	return array('result'=>false,'message'=>"MCシートの削除に失敗しました。",'reason'=>$mc->getDbo()->error."[".date('Y-m-d H:i:s')."]");
    }
    return array('result'=>true);
  }

  /**
   *
   * Mc情報を更新
   * @param $array_params
   * @param $user
   * @return 正常：TRUE
   *         異常：FALSE
   */
   function saveAll($array_params,$user){

     $tr = ClassRegistry::init('TransactionManager');
	 $tr->begin();

	 if(!empty($array_params['McTrn']))
	 {
	   for($header_index=0;$header_index < count($array_params['McTrn']);$header_index++)
	   {
	      $ret = $this->_saveMc($array_params['McTrn'][$header_index],$user);
	 	  if($ret['result']==false){return $ret;}
	   }
	 }
     $tr->commit();
     return array('result'=>true);
   }

  /**
   *
   * Mcヘッダ情報を更新
   * @param $array_params
   * @param $user
   * @return 正常：TRUE
   *         異常：FALSE
   */
   function _saveMc($array_params,$user){

   	 App::import("Model", "McTrn");

   	 $fields = array('attend_nm' ,'phone_no' ,'cell_no' ,'email' ,
   	                 'mc_num'    ,'working_start_time' ,'working_end_time' ,'working_total' ,
   	                 'start_place' ,'end_place' ,'note' ,'rw_note','upd_nm','upd_dt');

   	 /* 稼働合計時間の計算*/
   	    if(!empty($array_params['working_start_time']) &&  !empty($array_params['working_end_time'])){
   	    	$starts = explode(":", $array_params['working_start_time']);
   	 	    $ends   = explode(":", $array_params['working_end_time']);
   	 	    $array_params['working_total'] = count($starts) == 2 && count($ends) == 2 ? (($ends[0]*60)+$ends[1]) - (($starts[0]*60)+$starts[1]) : 0;
   	 	    if($array_params['working_total'] < 0){return array('result'=>false,'message'=>"Mcヘッダ情報更新に失敗しました。",'reason'=>"稼働開始時間と終了時間の順序が不正です。");}
   	    }else{
   	    	$array_params['working_total'] = 0;
   	    }

   	 $array_params['upd_nm'] = $user;
   	 $array_params['upd_dt'] = date('Y-m-d H:i:s');

     $mc = new McTrn;
	 $mc->id = $array_params['id'];

 	 if($mc->save($array_params,false,$fields)==false){
 	 	return array('result'=>false,'message'=>"Mcヘッダ情報更新に失敗しました。",'reason'=>$mc->getDbo()->error."[".date('Y-m-d H:i:s')."]");
 	 }

 	 return array('result'=>true);
   }

   /**
    *
    * ベンダーリスト取得
    * @param $customer_id
    */
   function getVendorList($final_sheet_id){

   	  App::import("Model", "McTrn");
      $mc = new McTrn();

   	  $sql = "select
                 vendor_nm,
                 attend_nm,
                 phone_no,
                 cell_no,
                 email
               from mc_trns
              where final_sheet_id = ".$final_sheet_id."
          group by vendor_id  ";

   	  $data = $mc->query($sql);

   	  if(count($data) > 0){

   	  	$arr = null;
   	  	for($i =0;$i < count($data);$i++){
   	  		$attr = $data[$i]["mc_trns"];
   	  		$temp = array("part"=>"Mc"     ,"vendor_nm"=>$attr["vendor_nm"],"attend_nm"=>$attr["attend_nm"],
   	  		             "phone_no"=>$attr["phone_no"],"cell_no"=>$attr["cell_no"]    ,"email"=>$attr["email"]);
   	  	    $arr[] = $temp;
   	  	}
   	    return $arr;
   	  }
   	 return null;
   }
}
?>