<?php
class ReceptionService extends AppModel {
    var $useTable = false;

   /**
     *
     * レセプションシートの新規作成
     * @param $array_params
     * @return 正常：TRUE
     *         異常：FALSE
     */
   function createReceptionSheet($array_params){

      App::import("Model", "ReceptionMenuTrn");
      $recep_menu = new ReceptionMenuTrn();

      /* 同じベンダーのファイナルシートがない場合はヘッダを作成する */
      $reception_id = $this->hasHeaderDataOfVendor($array_params['vendor_id'], $array_params['customer_id']);
      if($reception_id == false){

      	 App::import("Model", "ReceptionTrn");
      	 $reception = new ReceptionTrn();
         //レセプションヘッダ作成
         $reception_data = array(
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
 	     $reception->create();
         if($reception->save($reception_data)==false){
         	return array('result'=>false,'message'=>"レセプションヘッダ作成に失敗しました。",'reason'=>$reception->getDbo()->error."[".date('Y-m-d H:i:s')."]");
         }

         $reception_id = $reception->getLastInsertID();
     }

      //レセプションメニュー作成
      $recep_menu_data = array(
                               "reception_id"=>$reception_id,
                               "estimate_dtl_id"=>$array_params['estimate_dtl_id'],
                               "menu"=>$array_params['menu'],
                               "num"=>$array_params['num'],
 	                           "reg_nm"=>$array_params['username'],
 	                           "reg_dt"=>date('Y-m-d H:i:s')
 	                          );
 	  $recep_menu->create();
      if($recep_menu->save($recep_menu_data)==false){
      	return array('result'=>false,'message'=>"レセプションメニュー作成 に失敗しました。",'reason'=>$recep_menu->getDbo()->error."[".date('Y-m-d H:i:s')."]");
      }
      return array('result'=>true);
  }

  /**
    * レセプションシートの複製
    * @param $old_final_sheet_id
    * @param $new_final_sheet_id
    * @param $user
    */
   function copy($old_final_sheet_id,$new_final_sheet_id,$user){

  	 App::import("Model", "ReceptionTrn");
  	 $reception = new ReceptionTrn();

  	 App::import("Model", "ReceptionMenuTrn");
  	 $reception_menu = new ReceptionMenuTrn();

     $old_header = $reception->find('all',array('conditions'=>array('final_sheet_id'=>$old_final_sheet_id)));
     if(count($old_header) > 0){
       $old_menu = $reception_menu->find('all',array('conditions'=>array('reception_id'=>$old_header[0]['ReceptionTrn']['id'])));

       //レセプションヘッダ作成
       $reception_data = array(
                     "final_sheet_id"=>$new_final_sheet_id,
                     "customer_id"=>$old_header[0]['ReceptionTrn']['customer_id'],
     	             "vendor_id"=>$old_header[0]['ReceptionTrn']['vendor_id'],
     	             "vendor_nm"=>$old_header[0]['ReceptionTrn']['vendor_nm'],
                     "attend_nm"=>$old_header[0]['ReceptionTrn']['attend_nm'],
                     "phone_no"=>$old_header[0]['ReceptionTrn']['phone_no'],
                     "cell_no"=>$old_header[0]['ReceptionTrn']['cell_no'],
                     "email"=>$old_header[0]['ReceptionTrn']['email'],
                     "cpl_trans_dep_place"=>$old_header[0]['ReceptionTrn']['cpl_trans_dep_place'],
                     "cpl_trans_arrival_place"=>$old_header[0]['ReceptionTrn']['cpl_trans_arrival_place'],
                     "guest_trans_dep_place"=>$old_header[0]['ReceptionTrn']['guest_trans_dep_place'],
                     "guest_trans_arrival_place"=>$old_header[0]['ReceptionTrn']['guest_trans_arrival_place'],
                     "decoration_staff_nm"=>$old_header[0]['ReceptionTrn']['decoration_staff_nm'],
                     "mc_nm"=>$old_header[0]['ReceptionTrn']['mc_nm'],
                     "toasting_speech_nm"=>$old_header[0]['ReceptionTrn']['toasting_speech_nm'],
                     "theme_color"=>$old_header[0]['ReceptionTrn']['theme_color'],
                     "champagne_payment"=>$old_header[0]['ReceptionTrn']['champagne_payment'],
                     "menu_payment"=>$old_header[0]['ReceptionTrn']['menu_payment'],
                     "glass_count"=>$old_header[0]['ReceptionTrn']['glass_count'],
                     "allergie"=>$old_header[0]['ReceptionTrn']['allergie'],
                     "party_program_kbn"=>$old_header[0]['ReceptionTrn']['party_program_kbn'],
                     "bouquet_toss_kbn"=>$old_header[0]['ReceptionTrn']['bouquet_toss_kbn'],
                     "table_layout"=>$old_header[0]['ReceptionTrn']['table_layout'],
                     "bar_type"=>$old_header[0]['ReceptionTrn']['bar_type'],
 	                 "high_chair"=>$old_header[0]['ReceptionTrn']['high_chair'],
                     "seating_order_kbn"=>$old_header[0]['ReceptionTrn']['seating_order_kbn'],
                     "name_card_kbn"=>$old_header[0]['ReceptionTrn']['name_card_kbn'],
                     "menu_card_kbn"=>$old_header[0]['ReceptionTrn']['menu_card_kbn'],
                     "favor"=>$old_header[0]['ReceptionTrn']['favor'],
                     "note"=>$old_header[0]['ReceptionTrn']['note'],
                     "reg_nm"=>$user,
 	                 "reg_dt"=>date('Y-m-d H:i:s')
 	                 );
    $reception->create();
    if($reception->save($reception_data)==false){
      	  return array('result'=>false,'message'=>"レセプションヘッダの新規作成に失敗しました。",'reason'=>$reception->getDbo()->error."[".date('Y-m-d H:i:s')."]");
    }
    $reception_id = $reception->getLastInsertID();

    for($i=0;$i < count($old_menu);$i++){

     //レセプションメニュー作成
     $reception_menu_data = array(
                           "reception_id"=>$reception_id,
                           "estimate_dtl_id"=>$old_menu[$i]['ReceptionMenuTrn']['estimate_dtl_id'],
                           "menu_kbn"=>$old_menu[$i]['ReceptionMenuTrn']['menu_kbn'],
                           "menu"=>$old_menu[$i]['ReceptionMenuTrn']['menu'],
                           "num"=>$old_menu[$i]['ReceptionMenuTrn']['num'],
                           "note"=>$old_menu[$i]['ReceptionMenuTrn']['note'],
 	                       "reg_nm"=>$user,
 	                       "reg_dt"=>date('Y-m-d H:i:s')
 	                       );
     $reception_menu->create();

     if($reception_menu->save($reception_menu_data)==false){
     	 return array('result'=>false,'message'=>"レセプションメニューの新規作成に失敗しました。",'reason'=>$reception_menu->getDbo()->error."[".date('Y-m-d H:i:s')."]");
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

  	 App::import("Model", "ReceptionTrn");
  	 $reception = new ReceptionTrn();

     $reception_data = array( "final_sheet_id"=>$final_sheet_id );

     if($reception->updateAll($reception_data,array("customer_id"=>$customer_id))==false){
       return array('result'=>false,'message'=>"レセプションファイナルシートIDの更新に失敗しました。",'reason'=>$reception->getDbo()->error."[".date('Y-m-d H:i:s')."]");
     }
     return array('result'=>true);
 }

   /**
     *
     * レセプションシートのメニュー更新
     * @param $array_params
     * @return 正常：TRUE
     *         異常：FALSE
     */
   function updateMenu($array_params){

      App::import("Model", "ReceptionMenuTrn");
      $recep_menu = new ReceptionMenuTrn();

      $recep_menu_data = array(
                               "menu"=>"'".mysql_real_escape_string($array_params['menu'])."'",
                               "num"=>$array_params['num'],
 	                           "upd_nm"=>"'".$array_params['username']."'",
 	                           "upd_dt"=>"'".date('Y-m-d H:i:s')."'"
 	                          );

      /* 履歴があるので最新のメニューのIDを取得する  */
      $data = $recep_menu->find("first",array("fields"=>"MAX(id) as max_id","conditions"=>array("estimate_dtl_id"=>$array_params['estimate_dtl_id'])));
      if(count($data) > 0){  $max_id = $data[0]["max_id"]; }

      if($recep_menu->updateAll($recep_menu_data ,array("id"=>$max_id))==false){
      	return array('result'=>false,'message'=>"レセプションメニュー作成 に失敗しました。",'reason'=>$recep_menu->getDbo()->error."[".date('Y-m-d H:i:s')."]");
      }
      return array('result'=>true);
  }

    /**
     *
     * レセプションシートの削除
     * @param $customer_id
     * @return 正常：TRUE
     *         異常：FALSE
     */
    function deleteReceptionSheet($customer_id){

      App::import("Model", "ReceptionTrn");
      $reception = new ReceptionTrn();
      //レセプションヘッダ・メニュー削除[カスケード削除]
      if($reception->deleteAll(array("customer_id"=>$customer_id),true)==false){
      		return array('result'=>false,'message'=>"レセプションシートの削除に失敗しました。",'reason'=>$reception->getDbo()->error."[".date('Y-m-d H:i:s')."]");
      }
      return array('result'=>true);
    }

  /**
   *
   * 全レセプション情報を更新
   * @param $array_params
   * @param $user
   * @return 正常：TRUE
   *         異常：FALSE
   */
   function saveAll($array_params,$user){

     $tr = ClassRegistry::init('TransactionManager');
	 $tr->begin();

	 if(!empty($array_params['ReceptionTrn']))
	 {
	   /* ヘッダ更新 */
	   for($header_index=0;$header_index < count($array_params['ReceptionTrn']);$header_index++)
	   {
	      $ret = $this->_saveReception($array_params['ReceptionTrn'][$header_index],$user);
	 	  if($ret['result']==false){return $ret;}
	   }
	   /* 明細更新 */
	   for($sub_index=0;$sub_index < count($array_params['ReceptionMenuTrn']);$sub_index++)
	   {
	      $ret = $this->_saveReceptionMenu($array_params['ReceptionMenuTrn'][$sub_index],$user);
	      if($ret['result']==false){return $ret;}
	   }
	 }
     $tr->commit();
     return array('result'=>true);
   }

  /**
   *
   * レセプションヘッダ情報を更新
   * @param $array_params
   * @param $user
   * @return 正常：TRUE
   *         異常：FALSE
   */
   function _saveReception($array_params,$user){

   	 App::import("Model", "ReceptionTrn");
     $reception = new ReceptionTrn();

     $fields = array('attend_nm'           ,'phone_no'  ,'cell_no'            ,'email',
   	                 'cpl_trans_dep_place' ,'cpl_trans_arrival_place'         ,'guest_trans_dep_place' ,'guest_trans_arrival_place',
                     'decoration_staff_nm' ,'mc_nm'     ,'toasting_speech_nm' ,'theme_color'           ,'champagne_payment', 'menu_payment' ,
                     'glass_count'         ,'allergie'  ,'party_program_kbn'  ,'bouquet_toss_kbn'      ,'table_layout'     ,
                     'bar_type'            ,'high_chair','seating_order_kbn'  ,'name_card_kbn'         ,'menu_card_kbn'    ,'favor'         ,'note',
                     'upd_nm','upd_dt');

   	 $array_params['upd_nm'] = $user;
   	 $array_params['upd_dt'] = date('Y-m-d H:i:s');
	 $reception->id = $array_params['id'];

 	 if($reception->save($array_params,false,$fields)==false){
 	 	return array('result'=>false,'message'=>"レセプションヘッダ情報更新に失敗しました。",'reason'=>$reception->getDbo()->error."[".date('Y-m-d H:i:s')."]");
 	 }

    return array('result'=>true);
   }

  /**
   *
   * レセプションメニューを更新
   * @param $array_params
   * @param $user
   * @return 正常：TRUE
   *         異常：FALSE
   */
   function _saveReceptionMenu($array_params,$user){

   	 App::import("Model", "ReceptionMenuTrn");
   	 $rep_menu = new ReceptionMenuTrn;

     $fields = array('num','note','upd_nm','upd_dt');

     for($i=0;$i < count($array_params);$i++){
 	  	 $array_params[$i]['upd_nm'] = $user;
 	     $array_params[$i]['upd_dt'] = date('Y-m-d H:i:s');
 	     $rep_menu->id = $array_params[$i]['id'];
 	     if($rep_menu->save($array_params[$i],false,$fields)==false){
 	     	return array('result'=>false,'message'=>"レセプションメニュー更新に失敗しました。",'reason'=>$rep_menu->getDbo()->error."[".date('Y-m-d H:i:s')."]");
 	     }
     }
    return array('result'=>true);
   }

  /**
    *
    * レセプションファイナルシートに引数のベンダーが存在するかチェック
    * @param $vendor_id
    * @param $customer_id
    * @return 正常：レセプションID
    *         異常：FALSE
    */
   function hasHeaderDataOfVendor($vendor_id,$customer_id){

         App::import("Model", "ReceptionTrn");
         $reception = new ReceptionTrn();

         if($reception->hasAny(array('customer_id' => $customer_id,'vendor_id'=>$vendor_id))){
            $ret = $reception->find('first', array('fields' => 'id','conditions' => array('customer_id' => $customer_id,'vendor_id'=>$vendor_id)));
            return $ret['ReceptionTrn']['id'];
         }else{
         	return false;
         }
   }

   /**
    *
    * レセプションヘッダテーブルがサブテーブルから参照されていなければ削除する
    * @param $customer_id
    */
   function deleteHeaderIfNoSubTableData($customer_id)
   {
   	    App::import("Model", "ReceptionTrn");
        $reception = new ReceptionTrn();

        App::import("Model", "ReceptionMenuTrn");
        $reception_menu = new ReceptionMenuTrn();

        $header_ids = $reception->find('all', array('fields' => 'id','conditions' => array('customer_id' => $customer_id)));

        for($i=0;$i < count($header_ids);$i++)
        {
           if($reception_menu->hasAny(array('reception_id'=>$header_ids[$i]['ReceptionTrn']['id']))==false){
                if($reception->delete($header_ids[$i]['ReceptionTrn']['id'])==false){
                	return array('result'=>false,'message'=>"レセプションヘッダテーブル削除に失敗しました。",'reason'=>$reception->getDbo()->error."[".date('Y-m-d H:i:s')."]");
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

   	  App::import("Model", "ReceptionTrn");
      $reception = new ReceptionTrn();

   	  $sql = "select
                 vendor_nm,
                 attend_nm,
                 phone_no,
                 cell_no,
                 email
               from reception_trns
              where final_sheet_id = ".$final_sheet_id."
          group by vendor_id  ";

   	  $data = $reception->query($sql);

   	  if(count($data) > 0){

   	  	$arr = null;
   	  	for($i =0;$i < count($data);$i++){
   	  		$attr = $data[$i]["reception_trns"];
   	  		$temp = array("part"=>"Reception"     ,"vendor_nm"=>$attr["vendor_nm"],"attend_nm"=>$attr["attend_nm"],
   	  		             "phone_no"=>$attr["phone_no"],"cell_no"=>$attr["cell_no"]    ,"email"=>$attr["email"]);
   	  	    $arr[] = $temp;
   	  	}
   	    return $arr;
   	  }
   	 return null;
   }
}
?>