<?php
class TransportationService extends AppModel {
    var $useTable = false;

    /**
     *
     *  トランスポーテーションCPLシートの新規作成
     * @param $array_params
     * @return 正常：TRUE
     *         異常：FALSE
     */
    function createTransportationCplSheet($array_params){

         App::import("Model", "TransCplSubTrn");
    	 App::import("Model", "TransCplDtlTrn");
    	 $trans_sub = new TransCplSubTrn();
    	 $trans_dtl = new TransCplDtlTrn();

    	/* 同じベンダーのファイナルシートがない場合はヘッダを作成する */
        $trans_id = $this->hasCplHeaderDataOfVendor($array_params['vendor_id'], $array_params['customer_id']);

        if($trans_id == false){

          App::import("Model", "TransCplTrn");
          $trans = new TransCplTrn();

          //トランスポーテーションCPLヘッダ作成
     	  $trans_cpl_data = array(
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
 	      $trans->create();
          if($trans->save($trans_cpl_data)==false){
          	return array('result'=>false,'message'=>"トランスポーテーションCPLヘッダ作成に失敗しました。",'reason'=>$trans->getDbo()->error."[".date('Y-m-d H:i:s')."]");
          }

          $trans_id = $trans->getLastInsertID();
        }
         //トランスポーテーションCPLサブ作成
         $trans_cpl_sub_data = array(
     	                             "trans_cpl_id"=>$trans_id,
                                     "estimate_dtl_id"=>$array_params['estimate_dtl_id'],
                                     "menu"=>$array_params['menu'],
                                     "vihicular_type"=>$array_params['content'],
                                     "passenger_bg"=>2,
                                     "total_passenger"=>2,
 	                                 "reg_nm"=>$array_params['username'],
 	                                 "reg_dt"=>date('Y-m-d H:i:s')
 	                                 );
 	     $trans_sub->create();
         if($trans_sub->save($trans_cpl_sub_data)==false){
         	return array('result'=>false,'message'=>"トランスポーテーションCPLサブ作成に失敗しました。",'reason'=>$trans_sub->getDbo()->error."[".date('Y-m-d H:i:s')."]");
         }

         $trans_sub_id = $trans_sub->getLastInsertID();

         //トランスポーテーションCPL詳細作成
         $trans_cpl_dtl_data = array(
     	                             "trans_cpl_sub_id"=>$trans_sub_id,
                                     "no"=>"1",
 	                                 "reg_nm"=>$array_params['username'],
 	                                 "reg_dt"=>date('Y-m-d H:i:s')
 	                                 );
 	     $trans_dtl->create();
         if($trans_dtl->save($trans_cpl_dtl_data)==false){
         	return array('result'=>false,'message'=>"トランスポーテーションCPL詳細作成に失敗しました。",'reason'=>$trans_dtl->getDbo()->error."[".date('Y-m-d H:i:s')."]");
         }
       return array('result'=>true);
    }

  /**
    * トランスポーテーションCPLシートの複製
    * @param $old_final_sheet_id
    * @param $new_final_sheet_id
    * @param $user
    */
   function copyTransCpl($old_final_sheet_id,$new_final_sheet_id,$user){

  	 App::import("Model", "TransCplTrn");
  	 $trans_cpl = new TransCplTrn();

  	 App::import("Model", "TransCplSubTrn");
  	 $trans_cpl_sub = new TransCplSubTrn();

  	 App::import("Model", "TransCplDtlTrn");
  	 $trans_cpl_dtl = new TransCplDtlTrn();

     $old_header = $trans_cpl->find('all',array('conditions'=>array('final_sheet_id'=>$old_final_sheet_id)));
     if(count($old_header) > 0){
       $old_sub = $trans_cpl_sub->find('all',array('conditions'=>array('trans_cpl_id'=>$old_header[0]['TransCplTrn']['id'])));

       //トランスポーテーションCPLヘッダ作成
       $trans_cpl_data = array(
                     "final_sheet_id"=>$new_final_sheet_id,
                     "customer_id"=>$old_header[0]['TransCplTrn']['customer_id'],
     	             "vendor_id"=>$old_header[0]['TransCplTrn']['vendor_id'],
     	             "vendor_nm"=>$old_header[0]['TransCplTrn']['vendor_nm'],
                     "attend_nm"=>$old_header[0]['TransCplTrn']['attend_nm'],
                     "phone_no"=>$old_header[0]['TransCplTrn']['phone_no'],
                     "cell_no"=>$old_header[0]['TransCplTrn']['cell_no'],
                     "email"=>$old_header[0]['TransCplTrn']['email'],
                     "note"=>$old_header[0]['TransCplTrn']['note'],
 	                 "reg_nm"=>$user,
 	                 "reg_dt"=>date('Y-m-d H:i:s')
 	                 );
    $trans_cpl->create();
    if($trans_cpl->save($trans_cpl_data)==false){
      	  return array('result'=>false,'message'=>"トランスポーテーションCPLヘッダの新規作成に失敗しました。",'reason'=>$trans_cpl->getDbo()->error."[".date('Y-m-d H:i:s')."]");
    }
    $trans_cpl_id = $trans_cpl->getLastInsertID();

    //トランスポーテーションCPLサブ作成
    for($i=0;$i < count($old_sub);$i++){

     $trans_cpl_sub_data = array(
                           "trans_cpl_id"=>$trans_cpl_id,
                           "estimate_dtl_id"=>$old_sub[$i]['TransCplSubTrn']['estimate_dtl_id'],
                           "menu"=>$old_sub[$i]['TransCplSubTrn']['menu'],
                           "vihicular_type"=>$old_sub[$i]['TransCplSubTrn']['vihicular_type'],
     		               "dep_place"=>$old_sub[$i]['TransCplSubTrn']['dep_place'],
                           "final_dest"=>$old_sub[$i]['TransCplSubTrn']['final_dest'],
                           "working_start_time"=>$old_sub[$i]['TransCplSubTrn']['working_start_time'],
                           "working_end_time"=>$old_sub[$i]['TransCplSubTrn']['working_end_time'],
                           "working_total"=>$old_sub[$i]['TransCplSubTrn']['working_total'],
                           "passenger_bg"=>$old_sub[$i]['TransCplSubTrn']['passenger_bg'],
                           "passenger_guest"=>$old_sub[$i]['TransCplSubTrn']['passenger_guest'],
                           "passenger_ph"=>$old_sub[$i]['TransCplSubTrn']['passenger_ph'],
                           "passenger_hm"=>$old_sub[$i]['TransCplSubTrn']['passenger_hm'],
                           "passenger_att"=>$old_sub[$i]['TransCplSubTrn']['passenger_att'],
                           "passenger_vh"=>$old_sub[$i]['TransCplSubTrn']['passenger_vh'],
                           "total_passenger"=>$old_sub[$i]['TransCplSubTrn']['total_passenger'],
                           "note"=>$old_sub[$i]['TransCplSubTrn']['note'],
 	                       "reg_nm"=>$user,
 	                       "reg_dt"=>date('Y-m-d H:i:s')
 	                       );
     $trans_cpl_sub->create();

     if($trans_cpl_sub->save($trans_cpl_sub_data)==false){
     	 return array('result'=>false,'message'=>"トランスポーテーションCPLサブの新規作成に失敗しました。",'reason'=>$trans_cpl_sub->getDbo()->error."[".date('Y-m-d H:i:s')."]");
     }

     $trans_cpl_sub_id = $trans_cpl_sub->getLastInsertID();
     $old_dtl = $trans_cpl_dtl->find('all',array('conditions'=>array('trans_cpl_sub_id'=>$old_sub[$i]['TransCplSubTrn']['id'])));

    //トランスポーテーションCPL詳細作成
    for($j=0;$j < count($old_dtl);$j++){

     $trans_cpl_dtl_data = array(
                           "trans_cpl_sub_id"=>$trans_cpl_sub_id,
                           "no"=>$old_dtl[$j]['TransCplDtlTrn']['no'],
                           "departure_time"=>$old_dtl[$j]['TransCplDtlTrn']['departure_time'],
                           "departure_place"=>$old_dtl[$j]['TransCplDtlTrn']['departure_place'],
                           "arrival_time"=>$old_dtl[$j]['TransCplDtlTrn']['arrival_time'],
                           "arrival_place"=>$old_dtl[$j]['TransCplDtlTrn']['arrival_place'],
                           "note"=>$old_dtl[$j]['TransCplDtlTrn']['note'],
 	                       "reg_nm"=>$user,
 	                       "reg_dt"=>date('Y-m-d H:i:s')
 	                       );
     $trans_cpl_dtl->create();

     if($trans_cpl_dtl->save($trans_cpl_dtl_data)==false){
     	 return array('result'=>false,'message'=>"トランスポーテーションCPL詳細の新規作成に失敗しました。",'reason'=>$trans_cpl_dtl->getDbo()->error."[".date('Y-m-d H:i:s')."]");
     }
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
  function updateFinalSheetIdOfCpl($customer_id,$final_sheet_id){

  	 App::import("Model", "TransCplTrn");
  	 $trans_cpl = new TransCplTrn();

     $trans_cpl_data = array( "final_sheet_id"=>$final_sheet_id );

     if($trans_cpl->updateAll($trans_cpl_data,array("customer_id"=>$customer_id))==false){
       return array('result'=>false,'message'=>"トランスポーテーションCPLファイナルシートIDの更新に失敗しました。",'reason'=>$trans_cpl->getDbo()->error."[".date('Y-m-d H:i:s')."]");
     }
     return array('result'=>true);
 }

  /**
    * トランスポーテーションGuestシートの複製
    * @param $old_final_sheet_id
    * @param $new_final_sheet_id
    * @param $user
    */
   function copyTransGuest($old_final_sheet_id,$new_final_sheet_id,$user){

  	 App::import("Model", "TransGuestTrn");
  	 $trans_guest = new TransGuestTrn();

  	 App::import("Model", "TransGuestSubTrn");
  	 $trans_guest_sub = new TransGuestSubTrn();

  	 App::import("Model", "TransGuestDtlTrn");
  	 $trans_guest_dtl = new TransGuestDtlTrn();

     $old_header = $trans_guest->find('all',array('conditions'=>array('final_sheet_id'=>$old_final_sheet_id)));
     if(count($old_header) > 0){
       $old_sub = $trans_guest_sub->find('all',array('conditions'=>array('trans_guest_id'=>$old_header[0]['TransGuestTrn']['id'])));

       //トランスポーテーションGuestヘッダ作成
       $trans_guest_data = array(
                     "final_sheet_id"=>$new_final_sheet_id,
                     "customer_id"=>$old_header[0]['TransGuestTrn']['customer_id'],
     	             "vendor_id"=>$old_header[0]['TransGuestTrn']['vendor_id'],
     	             "vendor_nm"=>$old_header[0]['TransGuestTrn']['vendor_nm'],
                     "attend_nm"=>$old_header[0]['TransGuestTrn']['attend_nm'],
                     "phone_no"=>$old_header[0]['TransGuestTrn']['phone_no'],
                     "cell_no"=>$old_header[0]['TransGuestTrn']['cell_no'],
                     "email"=>$old_header[0]['TransGuestTrn']['email'],
                     "note"=>$old_header[0]['TransGuestTrn']['note'],
 	                 "reg_nm"=>$user,
 	                 "reg_dt"=>date('Y-m-d H:i:s')
 	                 );
    $trans_guest->create();
    if($trans_guest->save($trans_guest_data)==false){
      	  return array('result'=>false,'message'=>"トランスポーテーションGuestヘッダの新規作成に失敗しました。",'reason'=>$trans_guest->getDbo()->error."[".date('Y-m-d H:i:s')."]");
    }
    $trans_guest_id = $trans_guest->getLastInsertID();

    //トランスポーテーションGuestサブ作成
    for($i=0;$i < count($old_sub);$i++){

     $trans_guest_sub_data = array(
                           "trans_guest_id"=>$trans_guest_id,
                           "estimate_dtl_id"=>$old_sub[$i]['TransGuestSubTrn']['estimate_dtl_id'],
                           "menu"=>$old_sub[$i]['TransGuestSubTrn']['menu'],
                           "vihicular_type"=>$old_sub[$i]['TransGuestSubTrn']['vihicular_type'],
                           "final_dest"=>$old_sub[$i]['TransGuestSubTrn']['final_dest'],
                           "working_start_time"=>$old_sub[$i]['TransGuestSubTrn']['working_start_time'],
                           "working_end_time"=>$old_sub[$i]['TransGuestSubTrn']['working_end_time'],
                           "working_total"=>$old_sub[$i]['TransGuestSubTrn']['working_total'],
                           "note"=>$old_sub[$i]['TransGuestSubTrn']['note'],
 	                       "reg_nm"=>$user,
 	                       "reg_dt"=>date('Y-m-d H:i:s')
 	                       );
     $trans_guest_sub->create();

     if($trans_guest_sub->save($trans_guest_sub_data)==false){
     	 return array('result'=>false,'message'=>"トランスポーテーションGuestサブの新規作成に失敗しました。",'reason'=>$trans_guest_sub->getDbo()->error."[".date('Y-m-d H:i:s')."]");
     }

     $trans_guest_sub_id = $trans_guest_sub->getLastInsertID();
     $old_dtl = $trans_guest_dtl->find('all',array('conditions'=>array('trans_guest_sub_id'=>$old_sub[$i]['TransGuestSubTrn']['id'])));

    //トランスポーテーションGuest詳細作成
    for($j=0;$j < count($old_dtl);$j++){

     $trans_guest_dtl_data = array(
                           "trans_guest_sub_id"=>$trans_guest_sub_id,
                           "no"=>$old_dtl[$j]['TransGuestDtlTrn']['no'],
                           "representative_nm"=>$old_dtl[$j]['TransGuestDtlTrn']['representative_nm'],
                           "departure_time"=>$old_dtl[$j]['TransGuestDtlTrn']['departure_time'],
                           "departure_place"=>$old_dtl[$j]['TransGuestDtlTrn']['departure_place'],
                           "total_departure_passenger"=>$old_dtl[$j]['TransGuestDtlTrn']['total_departure_passenger'],
                           "arrival_time"=>$old_dtl[$j]['TransGuestDtlTrn']['arrival_time'],
                           "arrival_place"=>$old_dtl[$j]['TransGuestDtlTrn']['arrival_place'],
                           "total_arrival_passenger"=>$old_dtl[$j]['TransGuestDtlTrn']['total_arrival_passenger'],
                           "note"=>$old_dtl[$j]['TransGuestDtlTrn']['note'],
 	                       "reg_nm"=>$user,
 	                       "reg_dt"=>date('Y-m-d H:i:s')
 	                       );
     $trans_guest_dtl->create();

     if($trans_guest_dtl->save($trans_guest_dtl_data)==false){
     	 return array('result'=>false,'message'=>"トランスポーテーションGuest詳細の新規作成に失敗しました。",'reason'=>$trans_guest_dtl->getDbo()->error."[".date('Y-m-d H:i:s')."]");
     }
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
  function updateFinalSheetIdOfGuest($customer_id,$final_sheet_id){

  	 App::import("Model", "TransGuestTrn");
  	 $trans_guest = new TransGuestTrn();

     $trans_guest_data = array( "final_sheet_id"=>$final_sheet_id );

     if($trans_guest->updateAll($trans_guest_data,array("customer_id"=>$customer_id))==false){
       return array('result'=>false,'message'=>"トランスポーテーションGUESTファイナルシートIDの更新に失敗しました。",'reason'=>$trans_guest->getDbo()->error."[".date('Y-m-d H:i:s')."]");
     }
     return array('result'=>true);
 }

   /**
     *
     *  トランスポーテーションCPLシートのメニュー更新
     * @param $array_params
     * @return 正常：TRUE
     *         異常：FALSE
     */
    function updateTransportationCplMenu($array_params){

    	App::import("Model", "TransCplSubTrn");
    	$trans_sub = new TransCplSubTrn();

        $trans_cpl_sub_data = array(
                                     "menu"=>"'".mysql_real_escape_string($array_params['menu'])."'",
 	                                 "reg_nm"=>"'".$array_params['username']."'",
 	                                 "reg_dt"=>"'".date('Y-m-d H:i:s')."'"
 	                                 );

         /* 履歴があるので最新のメニューのIDを取得する  */
         $data = $trans_sub->find("first",array("fields"=>"MAX(id) as max_id","conditions"=>array("estimate_dtl_id"=>$array_params['estimate_dtl_id'])));
         if(count($data) > 0){  $max_id = $data[0]["max_id"]; }

         if($trans_sub->updateAll($trans_cpl_sub_data,array("id"=>$max_id))==false){
         	return array('result'=>false,'message'=>"トランスポーテーションCPLシートのメニュー更新に失敗しました。",'reason'=>$trans_sub->getDbo()->error."[".date('Y-m-d H:i:s')."]");
         }
         return array('result'=>true);
    }

    /**
     *
     *  トランスポーテーションGUESTシートの新規作成
     * @param $goods_data
     * @param $estimate_dtl_id
     * @param $customer_id
     * @param $menu
     * @param $username
     * @return 正常：TRUE
     *         異常：FALSE
     */
    function createTransportationGuestSheet($array_params){

    	App::import("Model", "TransGuestSubTrn");
    	App::import("Model", "TransGuestDtlTrn");
    	$trans_sub = new TransGuestSubTrn();
    	$trans_dtl = new TransGuestDtlTrn();

    	/* 同じベンダーのファイナルシートがない場合はヘッダを作成する */
        $trans_id = $this->hasGuestHeaderDataOfVendor($array_params['vendor_id'], $array_params['customer_id']);
        if($trans_id == false){

          App::import("Model", "TransGuestTrn");
          $trans = new TransGuestTrn();

          //トランスポーテーションGUESTヘッダ作成
     	  $trans_gst_data = array(
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
 	      $trans->create();
          if($trans->save($trans_gst_data)==false){
          	return array('result'=>false,'message'=>"トランスポーテーションGUESTヘッダ作成に失敗しました。",'reason'=>$trans->getDbo()->error."[".date('Y-m-d H:i:s')."]");
          }

          $trans_id = $trans->getLastInsertID();
        }
         //トランスポーテーションGUESTサブ作成
         $trans_gst_sub_data = array(
     	                         "trans_guest_id"=>$trans_id,
                                 "estimate_dtl_id"=>$array_params['estimate_dtl_id'],
                                 "menu"=>$array_params['menu'],
                                 "vihicular_type"=>$array_params['content'],
 	                             "reg_nm"=>$array_params['username'],
 	                             "reg_dt"=>date('Y-m-d H:i:s')
 	                             );
 	     $trans_sub->create();
         if($trans_sub->save($trans_gst_sub_data)==false){
         	return array('result'=>false,'message'=>"トランスポーテーションGUESTサブ作成に失敗しました。",'reason'=>$trans_sub->getDbo()->error."[".date('Y-m-d H:i:s')."]");
         }

         $trans_sub_id = $trans_sub->getLastInsertID();

         //トランスポーテーションGUEST詳細作成
         $trans_gst_dtl_data = array(
     	                             "trans_guest_sub_id"=>$trans_sub_id,
                                     "no"=>"1",
 	                                 "reg_nm"=>$array_params['username'],
 	                                 "reg_dt"=>date('Y-m-d H:i:s')
 	                                 );
 	     $trans_dtl->create();
         if($trans_dtl->save($trans_gst_dtl_data)==false){
         	return array('result'=>false,'message'=>"トランスポーテーションGUEST詳細作成に失敗しました。",'reason'=>$trans_dtl->getDbo()->error."[".date('Y-m-d H:i:s')."]");
         }
         return array('result'=>true);
   }

    /**
     *
     *  トランスポーテーションGUESTシートのメニュー更新
     * @param $array_params
     * @return 正常：TRUE
     *         異常：FALSE
     */
    function updateTransportationGuestMenu($array_params){

    	App::import("Model", "TransGuestSubTrn");
    	$trans_sub = new TransGuestSubTrn();

        $trans_gst_sub_data = array(
                                     "menu"=>"'".$array_params['menu']."'",
 	                                 "reg_nm"=>"'".$array_params['username']."'",
 	                                 "reg_dt"=>"'".date('Y-m-d H:i:s')."'"
 	                                 );

         /* 履歴があるので最新のメニューのIDを取得する  */
         $data = $trans_sub->find("first",array("fields"=>"MAX(id) as max_id","conditions"=>array("estimate_dtl_id"=>$array_params['estimate_dtl_id'])));
         if(count($data) > 0){  $max_id = $data[0]["max_id"]; }

         if($trans_sub->updateAll($trans_gst_sub_data,array("id"=>$max_id))==false){
         	return array('result'=>false,'message'=>"トランスポーテーションGUESTシートのメニュー更新に失敗しました。",'reason'=>$trans_sub->getDbo()->error."[".date('Y-m-d H:i:s')."]");
         }
         return array('result'=>true);
    }

    /**
     *
     * トランスポーテーションCPLシートの削除
     * @param $customer_id
     * @return 正常：TRUE
     *         異常：FALSE
     */
    function deleteTransportationCplSheet($customer_id){

      App::import("Model", "TransCplTrn");
      $trans_cpl = new TransCplTrn();
      //トランスポーテーションCPLヘッダ・サブ・明細削除[カスケード削除]
      if($trans_cpl->deleteAll(array("customer_id"=>$customer_id),true)==false){
      	 return array('result'=>false,'message'=>"トランスポーテーションCPLシートの削除に失敗しました。",'reason'=>$trans_cpl->getDbo()->error."[".date('Y-m-d H:i:s')."]");
      }
      return array('result'=>true);
    }

   /**
    *
    * トランスポーテーションゲストシートの削除
    * @param $customer_id
    * @return 正常：TRUE
    *         異常：FALSE
    */
    function deleteTransportationGuestSheet($customer_id){

     App::import("Model", "TransGuestTrn");
     $trans_gst = new TransGuestTrn();
     //トランスポーテーションGUESTヘッダ・サブ・明細削除[カスケード削除]
     if($trans_gst->deleteAll(array("customer_id"=>$customer_id),true)==false){
     	 return array('result'=>false,'message'=>"トランスポーテーションゲストシートの削除に失敗しました。",'reason'=>$trans_cpl->getDbo()->error."[".date('Y-m-d H:i:s')."]");
     }
     return array('result'=>true);
   }

  /**
    *
    * 全トランポーテーション情報を更新
    * @param $array_params
    * @param $user
    * @return 正常：TRUE
    *         異常：FALSE
    */
   function saveAll($array_params,$user){

     $tr = ClassRegistry::init('TransactionManager');
	 $tr->begin();

	 //トランスポートCPL
	 if(!empty($array_params['TransCplTrn']))
	 {
	   /* ヘッダ更新 */
	   for($header_index=0;$header_index < count($array_params['TransCplTrn']);$header_index++)
	   {
	      $ret = $this->_saveTransCpl($array_params['TransCplTrn'][$header_index],$user);
	 	  if($ret['result']==false){return $ret;}

	      /* サブ更新 */
	      for($sub_index=0;$sub_index < count($array_params['TransCplSubTrn'][$header_index]);$sub_index++)
	      {
	         $ret = $this->_saveTransCplSub($array_params['TransCplSubTrn'][$header_index][$sub_index],$user);
	         if($ret['result']==false){return $ret;}

	         /* 明細更新 */
	         for($dtl_index=0;$dtl_index < count($array_params['TransCplDtlTrn'][$header_index][$sub_index]);$dtl_index++)
	         {
	         	//配列の歯抜けのインデックスを詰める
	         	$temp_array = array_merge($array_params['TransCplDtlTrn'][$header_index][$sub_index]);
	            $ret = $this->_saveTransCplDtl($temp_array,$array_params['TransCplSubTrn'][$header_index][$sub_index]["id"],$user);
	            if($ret['result']==false){return $ret;}
	         }
	       }
	    }
	 }

	  //トランスポートGUEST
	 if(!empty($array_params['TransGuestTrn']))
	 {
	   /* ヘッダ更新 */
	   for($header_index=0;$header_index < count($array_params['TransGuestTrn']);$header_index++)
	   {
	      $ret = $this->_saveTransGuest($array_params['TransGuestTrn'][$header_index],$user);
	 	  if($ret['result']==false){return $ret;}

	      /* サブ更新 */
	      for($sub_index=0;$sub_index < count($array_params['TransGuestSubTrn'][$header_index]);$sub_index++)
	      {
	         $ret = $this->_saveTransGuestSub($array_params['TransGuestSubTrn'][$header_index][$sub_index],$user);
	         if($ret['result']==false){return $ret;}

	         /* 明細更新 */
	         for($dtl_index=0;$dtl_index < count($array_params['TransGuestDtlTrn'][$header_index][$sub_index]);$dtl_index++)
	         {
	         	//配列の歯抜けのインデックスを詰める
	         	$temp_array = array_merge($array_params['TransGuestDtlTrn'][$header_index][$sub_index]);
	            $ret = $this->_saveTransGuestDtl($temp_array,$array_params['TransGuestSubTrn'][$header_index][$sub_index]["id"],$user);

	            if($ret['result']==false){return $ret;}
	         }
	       }
	    }
	 }

     $tr->commit();
     return array('result'=>true);
   }

   /**
    *
    * ＣＰＬ用のトランポーテーションヘッダ情報を更新
    * @param $array_params
    * @param $user
    * @return 正常：TRUE
    *         異常：FALSE
    */
   function _saveTransCpl($array_params,$user){

   	 App::import("Model", "TransCplTrn");

   	 $fields = array('attend_nm' ,'phone_no','cell_no','email', 'note','upd_nm','upd_dt');

   	 $array_params['upd_nm'] = $user;
   	 $array_params['upd_dt'] = date('Y-m-d H:i:s');

     $tran = new TransCplTrn;
	 $tran->id = $array_params['id'];

 	 if($tran->save($array_params,false,$fields)==false){
 	 	return array('result'=>false,'message'=>"ＣＰＬ用のトランポーテーションヘッダ情報更新に失敗しました。",'reason'=>$tran->getDbo()->error."[".date('Y-m-d H:i:s')."]");
 	 }

 	 return array('result'=>true);
   }

  /**
    *
    * ＣＰＬ用のトランポーテーションサブ情報を更新
    * @param $array_params
    * @param $user
    * @return 正常：TRUE
    *         異常：FALSE
    */
   function _saveTransCplSub($array_params,$user){

   	 App::import("Model", "TransCplSubTrn");
   	 $tran = new TransCplSubTrn;

   	 $fields = array('vihicular_type' ,'dep_place'      ,'final_dest'   ,'working_start_time','working_end_time','working_total',
 	                 'passenger_bg'   ,'passenger_guest','passenger_ph' ,'passenger_hm'    ,'passenger_att',
 	                 'passenger_vh'   ,'total_passenger','note'         ,'upd_nm'          ,'upd_dt');

   	 /* 稼働合計時間の計算*/
   	 if(!empty($array_params['working_start_time']) &&  !empty($array_params['working_end_time'])){
   	 	$starts = explode(":", $array_params['working_start_time']);
   	 	$ends   = explode(":", $array_params['working_end_time']);
   	 	$array_params['working_total'] = count($starts) == 2 && count($ends) == 2 ? (($ends[0]*60)+$ends[1]) - (($starts[0]*60)+$starts[1]) : 0;
   	 	if($array_params['working_total'] < 0){return array('result'=>false,'message'=>"ＣＰＬ用のトランポーテーションサブ情報更新に失敗しました。",'reason'=>"稼働開始時間と終了時間の順序が不正です。");}
   	 }else{
   	 	$array_params['working_total'] = 0;
   	 }

   	 $array_params['upd_nm'] = $user;
   	 $array_params['upd_dt'] = date('Y-m-d H:i:s');
	 $tran->id = $array_params['id'];
 	 if($tran->save($array_params,false,$fields)==false){
 	 	return array('result'=>false,'message'=>"ＣＰＬ用のトランポーテーションサブ情報更新に失敗しました。",'reason'=>$tran->getDbo()->error."[".date('Y-m-d H:i:s')."]");
 	 }
 	 return array('result'=>true);
   }

   /**
    *
    * ＣＰＬ用のトランポーテーション詳細情報を更新
    * @param $array_params
    * @param $foreign_key
    * @param $user
    * @return 正常：TRUE
    *         異常：FALSE
    */
   function _saveTransCplDtl($array_params,$foreign_key,$user){

   	 App::import("Model", "TransCplDtlTrn");
   	 $tran = new TransCplDtlTrn;

   	 //新規追加または更新した明細IDを保持
     $saving_id= array();
     $fields = array('no'  ,'departure_time','departure_place','arrival_time','arrival_place',
 	                 'note','upd_nm'        ,'upd_dt');

     for($i=0;$i < count($array_params);$i++)
     {
	  //明細IDがNULLの場合はクライアント側で新規に追加した項目なので新規作成する
 	  if(empty($array_params[$i]['id']) || $array_params[$i]['id']==null)
 	  {
 	  	  $array_params[$i]['reg_nm'] = $user;
 	      $array_params[$i]['reg_dt'] = date('Y-m-d H:i:s');
 	      $array_params[$i]['trans_cpl_sub_id'] =  $foreign_key;
 	      $tran->create();
 	      if($tran->save($array_params[$i])==false){
 	      	return array('result'=>false,'message'=>"ＣＰＬ用のトランポーテーション詳細情報更新に失敗しました。",'reason'=>$tran->getDbo()->error."[".date('Y-m-d H:i:s')."]");
 	      }

 	      //新規作成したデータのIDを保存
 	      $last_trans_cpl_dtl_id = $tran->getLastInsertID();
 	      array_push($saving_id, $last_trans_cpl_dtl_id);
 	  }
 	  //既存の明細の更新
 	  else
 	  {
 	     //削除されず残っているデータのIDを保存
 	     array_push($saving_id,$array_params[$i]['id']);
 	     $array_params[$i]['upd_nm'] = $user;
 	     $array_params[$i]['upd_dt'] = date('Y-m-d H:i:s');
 	     $tran->id = $array_params[$i]['id'];
 	     if($tran->save($array_params[$i],false,$fields)==false){
 	     	return array('result'=>false,'message'=>"ＣＰＬ用のトランポーテーション詳細情報更新に失敗しました。",'reason'=>$tran->getDbo()->error."[".date('Y-m-d H:i:s')."]");
 	     }
 	  }
    }
    //新規追加でも既存の明細の更新でもないデータはクライアント側で削除指定されたデータなのですべて削除
 	if($tran->deleteAll( array('trans_cpl_sub_id'=>$foreign_key,'NOT'=>array('id'=>$saving_id)))==false){
 		return array('result'=>false,'message'=>"ＣＰＬ用のトランポーテーション詳細情報削除に失敗しました。",'reason'=>$tran->getDbo()->error."[".date('Y-m-d H:i:s')."]");
 	}
    return array('result'=>true);
   }

   /**
    *
    * ＣＰＬ用のトランポーテーションファイナルシートに引数のベンダーが存在するかチェック
    * @param $vendor_id
    * @param $customer_id
    * @return 正常：ＣＰＬトランポーテーションID
    *         異常：FALSE
    */
   function hasCplHeaderDataOfVendor($vendor_id,$customer_id){

         App::import("Model", "TransCplTrn");
         $trans_cpl = new TransCplTrn();

         if($trans_cpl->hasAny(array('customer_id' => $customer_id,'vendor_id'=>$vendor_id))){
            $ret = $trans_cpl->find('first', array('fields' => 'id','conditions' => array('customer_id' => $customer_id,'vendor_id'=>$vendor_id)));
            return $ret['TransCplTrn']['id'];
         }else{
         	return false;
         }
   }

   /**
    *
    * ゲスト用のトランポーテーションヘッダ情報を更新
    * @param $array_params
    * @param $user
    * @return 正常：TRUE
    *         異常：FALSE
    */
   function _saveTransGuest($array_params,$user){

   	 App::import("Model", "TransGuestTrn");

   	 $fields = array('attend_nm' ,'phone_no' ,'cell_no','email','note','upd_nm','upd_dt');

   	 $array_params['upd_nm'] = $user;
   	 $array_params['upd_dt'] = date('Y-m-d H:i:s');

     $tran = new TransGuestTrn;
	 $tran->id = $array_params['id'];

 	 if($tran->save($array_params,false,$fields)==false){
 	 	return array('result'=>false,'message'=>"ゲスト用のトランポーテーションヘッダ情報更新に失敗しました。",'reason'=>$tran->getDbo()->error."[".date('Y-m-d H:i:s')."]");
 	 }
 	return array('result'=>true);
   }

  /**
    *
    * ゲスト用のトランポーテーションサブ情報を更新
    * @param $array_params
    * @param $user
    * @return 正常：TRUE
    *         異常：FALSE
    */
   function _saveTransGuestSub($array_params,$user){

   	 App::import("Model", "TransGuestSubTrn");
     $tran = new TransGuestSubTrn;

   	 $fields = array('vihicular_type'   ,'final_dest'     ,'working_start_time','working_end_time','working_total',
 	                 'note','upd_nm','upd_dt');

   	  /* 稼働合計時間の計算*/
   	 if(!empty($array_params['working_start_time']) &&  !empty($array_params['working_end_time'])){
   	 	$starts = explode(":", $array_params['working_start_time']);
   	 	$ends   = explode(":", $array_params['working_end_time']);
   	 	$array_params['working_total'] = count($starts) == 2 && count($ends) == 2 ? (($ends[0]*60)+$ends[1]) - (($starts[0]*60)+$starts[1]) : 0;
   	 	if($array_params['working_total'] < 0){return array('result'=>false,'message'=>"ゲスト用のトランポーテーションサブ情報更新に失敗しました。",'reason'=>"稼働開始時間と終了時間の順序が不正です。");}
   	 }else{
   	 	$array_params['working_total'] = 0;
   	 }

   	 $array_params['upd_nm'] = $user;
   	 $array_params['upd_dt'] = date('Y-m-d H:i:s');
	 $tran->id = $array_params['id'];
 	 if($tran->save($array_params,false,$fields)==false){
 	 	return array('result'=>false,'message'=>"ゲスト用のトランポーテーションサブ情報更新に失敗しました。",'reason'=>$tran->getDbo()->error."[".date('Y-m-d H:i:s')."]");
 	 }
     return array('result'=>true);
   }

   /**
    *
    * ゲスト用のトランポーテーション詳細情報を更新
    * @param $array_params
    * @param $foreign_key
    * @param $user
    * @return 正常：TRUE
    *         異常：FALSE
    */
   function _saveTransGuestDtl($array_params,$foreign_key,$user){

   	 App::import("Model", "TransGuestDtlTrn");
   	 $tran = new TransGuestDtlTrn;

   	 //新規追加または更新した明細IDを保持
     $saving_id= array();
     $fields = array('no'  ,'representative_nm','departure_time','departure_place' ,'total_departure_passenger',
                     'arrival_time','arrival_place', 'total_arrival_passenger'     ,'note','upd_nm' ,'upd_dt');

     for($i=0;$i < count($array_params);$i++)
     {

	  //明細IDがNULLの場合はクライアント側で新規に追加した項目なので新規作成する
 	  if(empty($array_params[$i]['id']) || $array_params[$i]['id']==null)
 	  {
 	  	  $array_params[$i]['reg_nm'] = $user;
 	      $array_params[$i]['reg_dt'] = date('Y-m-d H:i:s');
 	      $array_params[$i]['trans_guest_sub_id'] =  $foreign_key;
 	      $tran->create();
 	      if($tran->save($array_params[$i])==false){
 	      	return array('result'=>false,'message'=>"ゲスト用のトランポーテーション詳細情報更新に失敗しました。",'reason'=>$tran->getDbo()->error."[".date('Y-m-d H:i:s')."]");
 	      }

 	      //新規作成したデータのIDを保存
 	      $last_trans_cpl_dtl_id = $tran->getLastInsertID();
 	      array_push($saving_id, $last_trans_cpl_dtl_id);
 	  }
 	  //既存の明細の更新
 	  else
 	  {
 	     //削除されず残っているデータのIDを保存
 	     array_push($saving_id,$array_params[$i]['id']);
 	     $array_params[$i]['upd_nm'] = $user;
 	     $array_params[$i]['upd_dt'] = date('Y-m-d H:i:s');
 	     $tran->id = $array_params[$i]['id'];
 	     if($tran->save($array_params[$i],false,$fields)==false){
 	     	return array('result'=>false,'message'=>"ゲスト用のトランポーテーション詳細情報に失敗しました。",'reason'=>$tran->getDbo()->error."[".date('Y-m-d H:i:s')."]");
 	     }
 	  }
    }
    //新規追加でも既存の明細の更新でもないデータはクライアント側で削除指定されたデータなのですべて削除
 	if($tran->deleteAll( array('trans_guest_sub_id'=>$foreign_key,'NOT'=>array('id'=>$saving_id)))==false){
 		return array('result'=>false,'message'=>"ゲスト用のトランポーテーション詳細情報削除に失敗しました。",'reason'=>$tran->getDbo()->error."[".date('Y-m-d H:i:s')."]");
 	}
    return array('result'=>true);
   }

  /**
    *
    * ゲスト用のトランポーテーションファイナルシートに引数のベンダーが存在するかチェック
    * @param $vendor_id
    * @param $customer_id
    * @return 正常：ゲストトランポーテーションID
    *         異常：FALSE
    */
   function hasGuestHeaderDataOfVendor($vendor_id,$customer_id){

         App::import("Model", "TransGuestTrn");
         $trans_gst = new TransGuestTrn();

         if($trans_gst->hasAny(array('customer_id' => $customer_id,'vendor_id'=>$vendor_id))){
            $ret = $trans_gst->find('first', array('fields' => 'id','conditions' => array('customer_id' => $customer_id,'vendor_id'=>$vendor_id)));
            return $ret['TransGuestTrn']['id'];
         }else{
         	return false;
         }
   }

  /**
    *
    * CPLヘッダテーブルがサブテーブルから参照されていなければ削除する
    * @param $customer_id
    */
   function deleteTransCplIfNoSubTableData($customer_id)
   {
   	    App::import("Model", "TransCplTrn");
        $trans_cpl = new TransCplTrn();

        App::import("Model", "TransCplSubTrn");
        $trans_cpl_sub= new TransCplSubTrn();

        $header_ids = $trans_cpl->find('all', array('fields' => 'id','conditions' => array('customer_id' => $customer_id)));

        for($i=0;$i < count($header_ids);$i++)
        {
           if($trans_cpl_sub->hasAny(array('trans_cpl_id'=>$header_ids[$i]['TransCplTrn']['id']))==false){
                if($trans_cpl->delete($header_ids[$i]['TransCplTrn']['id'])==false){
                	return array('result'=>false,'message'=>"CPLヘッダテーブル削除に失敗しました。",'reason'=>$trans_cpl->getDbo()->error."[".date('Y-m-d H:i:s')."]");
                }
           }
        }
        return array('result'=>true);
   }

   /**
    *
    * Guestヘッダテーブルがサブテーブルから参照されていなければ削除する
    * @param $customer_id
    */
   function deleteTransGuestIfNoSubTableData($customer_id)
   {
   	    App::import("Model", "TransGuestTrn");
        $trans_gst = new TransGuestTrn();

        App::import("Model", "TransGuestSubTrn");
        $trans_gst_sub = new TransGuestSubTrn();

        $header_ids = $trans_gst->find('all', array('fields' => 'id','conditions' => array('customer_id' => $customer_id)));
        for($i=0;$i < count($header_ids);$i++)
        {
           if($trans_gst_sub->hasAny(array('trans_guest_id'=>$header_ids[$i]['TransGuestTrn']['id']))==false){
                 if($trans_gst->delete($header_ids[$i]['TransGuestTrn']['id'])==false){
                 	return array('result'=>false,'message'=>"Guestヘッダテーブル削除に失敗しました。",'reason'=>$trans_gst->getDbo()->error."[".date('Y-m-d H:i:s')."]");
                 }
           }
        }
   	 return array('result'=>true);
   }

   /**
    *
    * ベンダーリストCpl取得
    * @param $customer_id
    */
   function getVendorListCpl($final_sheet_id){

   	  App::import("Model", "TransCplTrn");
      $trans_cpl = new TransCplTrn();

   	  $sql = "select
                 vendor_nm,
                 attend_nm,
                 phone_no,
                 cell_no,
                 email
               from trans_cpl_trns
              where final_sheet_id = ".$final_sheet_id."
          group by vendor_id  ";

   	  $data = $trans_cpl->query($sql);

   	  if(count($data) > 0){

   	  	$arr = null;
   	  	for($i =0;$i < count($data);$i++){
   	  		$attr = $data[$i]["trans_cpl_trns"];
   	  		$temp = array("part"=>"Transportation(Cpl)"    ,"vendor_nm"=>$attr["vendor_nm"],"attend_nm"=>$attr["attend_nm"],
   	  		              "phone_no"=>$attr["phone_no"],"cell_no"=>$attr["cell_no"]    ,"email"=>$attr["email"]);
   	  	    $arr[] = $temp;
   	  	}
   	    return $arr;
   	  }
   	 return null;
   }

   /**
    *
    * ベンダーリストGst取得
    * @param $customer_id
    */
   function getVendorListGst($final_sheet_id){

   	 App::import("Model", "TransGuestTrn");
      $trans_gst = new TransGuestTrn();

   	  $sql = "select
                 vendor_nm,
                 attend_nm,
                 phone_no,
                 cell_no,
                 email
               from trans_guest_trns
              where final_sheet_id = ".$final_sheet_id."
          group by vendor_id  ";

   	  $data = $trans_gst->query($sql);

   	  if(count($data) > 0){

   	  	$arr = null;
   	  	for($i =0;$i < count($data);$i++){
   	  		$attr = $data[$i]["trans_guest_trns"];
   	  		$temp = array("part"=>"Transportation(Guest)"    ,"vendor_nm"=>$attr["vendor_nm"],"attend_nm"=>$attr["attend_nm"],
   	  		              "phone_no"=>$attr["phone_no"],"cell_no"=>$attr["cell_no"]    ,"email"=>$attr["email"]);
   	  	    $arr[] = $temp;
   	  	}
   	    return $arr;
   	  }
   	 return null;
   }

}
?>