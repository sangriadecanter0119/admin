<?php
class HairmakeService extends AppModel {
    var $useTable = false;

    /**
     *
     *  ヘアメイクCPLシートの新規作成
     * @param $array_params
     * @return 正常：TRUE
     *         異常：FALSE
     */
   function createHairmakeCplSheet($array_params){


   	   App::import("Model", "HairmakeCplMenuTrn");
   	   $hair_menu = new HairmakeCplMenuTrn();

   	   /* 同じベンダーのファイナルシートがない場合はヘッダを作成する */
       $hair_id = $this->hasCplHeaderDataOfVendor($array_params['vendor_id'], $array_params['customer_id']);
       if($hair_id == false)
	   {
	   	  //ヘアメイクCPLヘッダ作成
	   	  App::import("Model", "HairmakeCplTrn");
	   	  $hair = new HairmakeCplTrn();

          $hairmake_cpl_data = array(
                                   "customer_id"=>$array_params['customer_id'],
                                   "final_sheet_id"=>$array_params['final_sheet_id'],
     	                           "vendor_id"=>$array_params['vendor_id'],
                                   "vendor_nm"=>$array_params['vendor_nm'],
                                   "attend_nm"=>$array_params['vendor_attend_nm'],
                                   "phone_no"=>$array_params['vendor_phone_no'],
                                   "cell_no"=>$array_params['vendor_cell_no'],
                                   "email"=>$array_params['vendor_email'],
                                   "main_attend_kbn"=> $this->hasMainHairmake($array_params['customer_id'])==false ? HC_MAIN:HC_NONE,
                                   "total_attend"=>1,
 	                               "reg_nm"=>$array_params['username'],
 	                               "reg_dt"=>date('Y-m-d H:i:s')
 	                               );
 	     $hair->create();
         if($hair->save($hairmake_cpl_data)==false){
         	return array('result'=>false,'message'=>"ヘアメイクCPLヘッダ作成に失敗しました。",'reason'=>$hair->getDbo()->error."[".date('Y-m-d H:i:s')."]");
         }

         $hair_id = $hair->getLastInsertID();

         //ヘアメイクチェンジCPL時間作成
         App::import("Model", "HairmakeCplTimeTrn");
         $hair_time = new HairmakeCplTimeTrn();

         $hairmake_cpl_time_data = array(
     	                              "hairmake_cpl_id"=>$hair_id,
                                      "no"=>"1",
 	                                  "reg_nm"=>$array_params['username'],
 	                                  "reg_dt"=>date('Y-m-d H:i:s')
 	                                  );
 	     $hair_time->create();
         if($hair_time->save($hairmake_cpl_time_data)==false){
         	return array('result'=>false,'message'=>"ヘアメイクチェンジCPL時間作成に失敗しました。",'reason'=>$hair_time->getDbo()->error."[".date('Y-m-d H:i:s')."]");
         }
	   }

       //ヘアメイクCPL明細作成
       $hairmake_cpl_menu_data = array(
     	                              "hairmake_cpl_id"=>$hair_id,
                                      "estimate_dtl_id"=>$array_params['estimate_dtl_id'],
                                      "menu"=>$array_params['menu'],
 	                                  "reg_nm"=>$array_params['username'],
 	                                  "reg_dt"=>date('Y-m-d H:i:s')
 	                                  );
 	    $hair_menu->create();
        if($hair_menu->save($hairmake_cpl_menu_data)==false){
        	return array('result'=>false,'message'=>"ヘアメイクCPL明細作成に失敗しました。",'reason'=>$hair_menu->getDbo()->error."[".date('Y-m-d H:i:s')."]");
        }
      return array('result'=>true);
   }

  /**
    * ヘアメイクCPLシートの複製
    * @param $old_final_sheet_id
    * @param $new_final_sheet_id
    * @param $user
    */
   function copyHairmakeCpl($old_final_sheet_id,$new_final_sheet_id,$user){

  	 App::import("Model", "HairmakeCplTrn");
  	 $hairmake_cpl = new HairmakeCplTrn();

  	 App::import("Model", "HairmakeCplMenuTrn");
  	 $hairmake_cpl_menu = new HairmakeCplMenuTrn();

  	 App::import("Model", "HairmakeCplTimeTrn");
  	 $hairmake_cpl_time = new HairmakeCplTimeTrn();

     $old_header = $hairmake_cpl->find('all',array('conditions'=>array('final_sheet_id'=>$old_final_sheet_id)));
     if(count($old_header) > 0){
       $old_menu = $hairmake_cpl_menu->find('all',array('conditions'=>array('hairmake_cpl_id'=>$old_header[0]['HairmakeCplTrn']['id'])));
       $old_time = $hairmake_cpl_time->find('all',array('conditions'=>array('hairmake_cpl_id'=>$old_header[0]['HairmakeCplTrn']['id'])));

       //ヘアメイクCPLヘッダ作成
       $hairmake_cpl_data = array(
                     "final_sheet_id"=>$new_final_sheet_id,
                     "customer_id"=>$old_header[0]['HairmakeCplTrn']['customer_id'],
     	             "vendor_id"=>$old_header[0]['HairmakeCplTrn']['vendor_id'],
     	             "vendor_nm"=>$old_header[0]['HairmakeCplTrn']['vendor_nm'],
                     "attend_nm"=>$old_header[0]['HairmakeCplTrn']['attend_nm'],
                     "phone_no"=>$old_header[0]['HairmakeCplTrn']['phone_no'],
                     "cell_no"=>$old_header[0]['HairmakeCplTrn']['cell_no'],
                     "email"=>$old_header[0]['HairmakeCplTrn']['email'],
                     "total_attend"=>$old_header[0]['HairmakeCplTrn']['total_attend'],
                     "rehasal_dt"=>$old_header[0]['HairmakeCplTrn']['rehasal_dt'],
                     "rehasal_start_time"=>$old_header[0]['HairmakeCplTrn']['rehasal_start_time'],
                     "rehasal_end_time"=>$old_header[0]['HairmakeCplTrn']['rehasal_end_time'],
                     "rehasal_place"=>$old_header[0]['HairmakeCplTrn']['rehasal_place'],
                     "rehasal_name"=>$old_header[0]['HairmakeCplTrn']['rehasal_name'],
                     "working_start_time"=>$old_header[0]['HairmakeCplTrn']['working_start_time'],
                     "working_end_time"=>$old_header[0]['HairmakeCplTrn']['working_end_time'],
                     "working_total"=>$old_header[0]['HairmakeCplTrn']['working_total'],
                     "working_start_place"=>$old_header[0]['HairmakeCplTrn']['working_start_place'],
                     "working_end_place"=>$old_header[0]['HairmakeCplTrn']['working_end_place'],
                     "transportation"=>$old_header[0]['HairmakeCplTrn']['transportation'],
                     "main_attend_kbn"=>$old_header[0]['HairmakeCplTrn']['main_attend_kbn'],
                     "note"=>$old_header[0]['HairmakeCplTrn']['note'],
 	                 "reg_nm"=>$user,
 	                 "reg_dt"=>date('Y-m-d H:i:s')
 	                 );
    $hairmake_cpl->create();
    if($hairmake_cpl->save($hairmake_cpl_data)==false){
      	  return array('result'=>false,'message'=>"ヘアメイクCPLヘッダの新規作成に失敗しました。",'reason'=>$hairmake_cpl->getDbo()->error."[".date('Y-m-d H:i:s')."]");
    }
    $hairmake_cpl_id = $hairmake_cpl->getLastInsertID();

    //ヘアメイクCPLメニュー作成
    for($i=0;$i < count($old_menu);$i++){

     $hairmake_cpl_menu_data = array(
                           "hairmake_cpl_id"=>$hairmake_cpl_id,
                           "estimate_dtl_id"=>$old_menu[$i]['HairmakeCplMenuTrn']['estimate_dtl_id'],
                           "menu"=>$old_menu[$i]['HairmakeCplMenuTrn']['menu'],
                           "note"=>$old_menu[$i]['HairmakeCplMenuTrn']['note'],
 	                       "reg_nm"=>$user,
 	                       "reg_dt"=>date('Y-m-d H:i:s')
 	                       );
     $hairmake_cpl_menu->create();

     if($hairmake_cpl_menu->save($hairmake_cpl_menu_data)==false){
     	 return array('result'=>false,'message'=>"ヘアメイクCPLメニューの新規作成に失敗しました。",'reason'=>$hairmake_cpl_menu->getDbo()->error."[".date('Y-m-d H:i:s')."]");
     }
    }

    //ヘアメイクCPL時間作成
    for($i=0;$i < count($old_time);$i++){

     $hairmake_cpl_time_data = array(
                           "hairmake_cpl_id"=>$hairmake_cpl_id,
                           "no"=>$old_time[$i]['HairmakeCplTimeTrn']['no'],
                           "make_start_time"=>$old_time[$i]['HairmakeCplTimeTrn']['make_start_time'],
                           "make_start_place"=>$old_time[$i]['HairmakeCplTimeTrn']['make_start_place'],
                           "note"=>$old_time[$i]['HairmakeCplTimeTrn']['note'],
 	                       "reg_nm"=>$user,
 	                       "reg_dt"=>date('Y-m-d H:i:s')
 	                       );
     $hairmake_cpl_time->create();

     if($hairmake_cpl_time->save($hairmake_cpl_time_data)==false){
     	 return array('result'=>false,'message'=>"ヘアメイクCPL時間の新規作成に失敗しました。",'reason'=>$hairmake_cpl_time->getDbo()->error."[".date('Y-m-d H:i:s')."]");
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

  	 App::import("Model", "HairmakeCplTrn");
  	 $hairmake_cpl = new HairmakeCplTrn();

     $hairmake_cpl_data = array( "final_sheet_id"=>$final_sheet_id );

     if($hairmake_cpl->updateAll($hairmake_cpl_data,array("customer_id"=>$customer_id))==false){
       return array('result'=>false,'message'=>"ヘアメイクCPLファイナルシートIDの更新に失敗しました。",'reason'=>$hairmake_cpl->getDbo()->error."[".date('Y-m-d H:i:s')."]");
     }
     return array('result'=>true);
 }

  /**
    * ヘアメイクGuestシートの複製
    * @param $old_final_sheet_id
    * @param $new_final_sheet_id
    * @param $user
    */
   function copyHairmakeGuest($old_final_sheet_id,$new_final_sheet_id,$user){

  	 App::import("Model", "HairmakeGuestTrn");
  	 $hairmake_guest = new HairmakeGuestTrn();

  	 App::import("Model", "HairmakeGuestSubTrn");
  	 $hairmake_guest_sub = new HairmakeGuestSubTrn();

  	 App::import("Model", "HairmakeGuestDtlTrn");
  	 $hairmake_guest_dtl = new HairmakeGuestDtlTrn();

     $old_header = $hairmake_guest->find('all',array('conditions'=>array('final_sheet_id'=>$old_final_sheet_id)));
     if(count($old_header) > 0){
       $old_sub = $hairmake_guest_sub->find('all',array('conditions'=>array('hairmake_guest_id'=>$old_header[0]['HairmakeGuestTrn']['id'])));

       //ヘアメイクGuestヘッダ作成
       $hairmake_guest_data = array(
                     "final_sheet_id"=>$new_final_sheet_id,
                     "customer_id"=>$old_header[0]['HairmakeGuestTrn']['customer_id'],
     	             "vendor_id"=>$old_header[0]['HairmakeGuestTrn']['vendor_id'],
     	             "vendor_nm"=>$old_header[0]['HairmakeGuestTrn']['vendor_nm'],
                     "attend_nm"=>$old_header[0]['HairmakeGuestTrn']['attend_nm'],
                     "phone_no"=>$old_header[0]['HairmakeGuestTrn']['phone_no'],
                     "cell_no"=>$old_header[0]['HairmakeGuestTrn']['cell_no'],
                     "email"=>$old_header[0]['HairmakeGuestTrn']['email'],
                     "note"=>$old_header[0]['HairmakeGuestTrn']['note'],
 	                 "reg_nm"=>$user,
 	                 "reg_dt"=>date('Y-m-d H:i:s')
 	                 );
    $hairmake_guest->create();
    if($hairmake_guest->save($hairmake_guest_data)==false){
      	  return array('result'=>false,'message'=>"ヘアメイクGuestヘッダの新規作成に失敗しました。",'reason'=>$hairmake_guest->getDbo()->error."[".date('Y-m-d H:i:s')."]");
    }
    $hairmake_guest_id = $hairmake_guest->getLastInsertID();

    //ヘアメイクGuestメニュー作成
    for($i=0;$i < count($old_sub);$i++){

     $hairmake_guest_sub_data = array(
                           "hairmake_guest_id"=>$hairmake_guest_id,
                           "estimate_dtl_id"=>$old_sub[$i]['HairmakeGuestSubTrn']['estimate_dtl_id'],
                           "menu"=>$old_sub[$i]['HairmakeGuestSubTrn']['menu'],
                           "note"=>$old_sub[$i]['HairmakeGuestSubTrn']['note'],
 	                       "reg_nm"=>$user,
 	                       "reg_dt"=>date('Y-m-d H:i:s')
 	                       );
     $hairmake_guest_sub->create();

     if($hairmake_guest_sub->save($hairmake_guest_sub_data)==false){
     	 return array('result'=>false,'message'=>"ヘアメイクGuestメニューの新規作成に失敗しました。",'reason'=>$hairmake_guest_sub->getDbo()->error."[".date('Y-m-d H:i:s')."]");
     }

    $hairmake_guest_sub_id = $hairmake_guest_sub->getLastInsertID();
    $old_dtl = $hairmake_guest_dtl->find('all',array('conditions'=>array('hairmake_guest_sub_id'=>$old_sub[$i]['HairmakeGuestSubTrn']['id'])));
    //ヘアメイクGuest時間作成
    for($j=0;$j < count($old_dtl);$j++){

     $hairmake_guest_dtl_data = array(
                           "hairmake_guest_sub_id"=>$hairmake_guest_sub_id,
                           "no"=>$old_dtl[$j]['HairmakeGuestDtlTrn']['no'],
                           "attend_nm"=>$old_dtl[$j]['HairmakeGuestDtlTrn']['attend_nm'],
                           "guest_nm"=>$old_dtl[$j]['HairmakeGuestDtlTrn']['guest_nm'],
                           "make_start_time"=>$old_dtl[$j]['HairmakeGuestDtlTrn']['make_start_time'],
                           "make_start_place"=>$old_dtl[$j]['HairmakeGuestDtlTrn']['make_start_place'],
                           "make_end_time"=>$old_dtl[$j]['HairmakeGuestDtlTrn']['make_end_time'],
                           "make_end_place"=>$old_dtl[$j]['HairmakeGuestDtlTrn']['make_end_place'],
                           "note"=>$old_dtl[$j]['HairmakeGuestDtlTrn']['note'],
 	                       "reg_nm"=>$user,
 	                       "reg_dt"=>date('Y-m-d H:i:s')
 	                       );
     $hairmake_guest_dtl->create();

     if($hairmake_guest_dtl->save($hairmake_guest_dtl_data)==false){
     	 return array('result'=>false,'message'=>"ヘアメイクGuest時間の新規作成に失敗しました。",'reason'=>$hairmake_guest_dtl->getDbo()->error."[".date('Y-m-d H:i:s')."]");
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

  	 App::import("Model", "HairmakeGuestTrn");
  	 $hairmake_guest = new HairmakeGuestTrn();

     $hairmake_guest_data = array( "final_sheet_id"=>$final_sheet_id );

     if($hairmake_guest->updateAll($hairmake_guest_data,array("customer_id"=>$customer_id))==false){
       return array('result'=>false,'message'=>"ヘアメイクGUESTファイナルシートIDの更新に失敗しました。",'reason'=>$hairmake_guest->getDbo()->error."[".date('Y-m-d H:i:s')."]");
     }
     return array('result'=>true);
 }

   /**
     *
     *  ヘアメイクCPLシートのメニュー更新
     * @param $array_params
     * @return 正常：TRUE
     *         異常：FALSE
     */
   function updateHairmakeCplMenu($array_params){

   	   App::import("Model", "HairmakeCplMenuTrn");
   	   $hair_menu = new HairmakeCplMenuTrn();

       //ヘアメイクCPL明細メニュー更新
       $hairmake_cpl_menu_data = array(
                                      "menu"=>"'".mysql_real_escape_string($array_params['menu'])."'",
 	                                  "reg_nm"=>"'".$array_params['username']."'",
 	                                  "reg_dt"=>"'".date('Y-m-d H:i:s')."'"
 	                                  );

       /* 履歴があるので最新のメニューのIDを取得する  */
       $data = $hair_menu->find("first",array("fields"=>"MAX(id) as max_id","conditions"=>array("estimate_dtl_id"=>$array_params['estimate_dtl_id'])));
       if(count($data) > 0){  $max_id = $data[0]["max_id"]; }

       if($hair_menu->updateAll($hairmake_cpl_menu_data,array("id"=>$max_id))==false){
       	return array('result'=>false,'message'=>"ヘアメイクCPL明細メニュー更新に失敗しました。",'reason'=>$hair_menu->getDbo()->error."[".date('Y-m-d H:i:s')."]");
       }
     return array('result'=>true);
   }

  /**
   *
   * ヘアメイクCPLシートの削除
   * @param $customer_id
   * @return 正常：TRUE
   *         異常：FALSE
   */
   function deleteHairmakeCplSheet($customer_id){

      App::import("Model", "HairmakeCplTrn");
      $hair_cpl = new HairmakeCplTrn();
      //ヘアメイクCPLヘッダ・明細・サブ削除[カスケード削除]
      if($hair_cpl->deleteAll(array("customer_id"=>$customer_id),true)==false){
      	return array('result'=>false,'message'=>"ヘアメイクCPLシートの削除に失敗しました。",'reason'=>$hair_cpl->getDbo()->error."[".date('Y-m-d H:i:s')."]");
      }
      return array('result'=>true);
   }

  /**
   *
   * へメイクゲストシートの削除
   * @param $customer_id
   * @return 正常：TRUE
   *         異常：FALSE
   */
   function deleteHairmakeGuestSheet($customer_id){

      App::import("Model", "HairmakeGuestTrn");
      $hair_gst = new HairmakeGuestTrn();
      //ヘアメイクGUESTヘッダ・明細・サブ削除[カスケード削除]
      if($hair_gst->deleteAll(array("customer_id"=>$customer_id),true)==false){
      	return array('result'=>false,'message'=>"へメイクゲストシートの削除に失敗しました。",'reason'=>$hair_gst->getDbo()->error."[".date('Y-m-d H:i:s')."]");
      }
     return array('result'=>true);
    }

    /**
     *
     *  ヘアメイクGUESTシートの新規作成
     * @param $array_params
     * @return 正常：TRUE
     *         異常：FALSE
     */
   function createHairmakeGuestSheet($array_params){

   	  App::import("Model", "HairmakeGuestSubTrn");
   	  App::import("Model", "HairmakeGuestDtlTrn");
   	  $hair_sub = new HairmakeGuestSubTrn();
   	  $hair_dtl = new HairmakeGuestDtlTrn();

   	    /* 同じベンダーのファイナルシートがない場合はヘッダを作成する */
       $hairmake_id = $this->hasGuestHeaderDataOfVendor($array_params['vendor_id'], $array_params['customer_id']);
       if($hairmake_id == false)
	   {
	   	   App::import("Model", "HairmakeGuestTrn");
	   	   $hair = new HairmakeGuestTrn();

	   	    //ヘアメイクゲストヘッダ作成
           $hairmake_gst_data = array(
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
 	       $hair->create();
           if($hair->save($hairmake_gst_data)==false){
           	   return array('result'=>false,'message'=>"ヘアメイクゲストヘッダ作成に失敗しました。",'reason'=>$hair->getDbo()->error."[".date('Y-m-d H:i:s')."]");
           }

           $hairmake_id = $hair->getLastInsertID();
	   }

	  //ヘアメイクゲストサブ作成
      $hairmake_gst_sub_data = array(
     	                              "hairmake_guest_id"=>$hairmake_id,
                                      "estimate_dtl_id"=>$array_params['estimate_dtl_id'],
     	                              "menu"=>$array_params['menu'],
 	                                  "reg_nm"=>$array_params['username'],
 	                                  "reg_dt"=>date('Y-m-d H:i:s')
 	                                  );
 	  $hair_sub->create();
      if($hair_sub->save($hairmake_gst_sub_data)==false){
      	   return array('result'=>false,'message'=>"ヘアメイクゲストサブ作成に失敗しました。",'reason'=>$hair_sub->getDbo()->error."[".date('Y-m-d H:i:s')."]");
      }

      $hairmake_sub_id = $hair_sub->getLastInsertID();

      //ヘアメイクゲストサブ作成
      $hairmake_gst_dtl_data = array(
     	                              "hairmake_guest_sub_id"=>$hairmake_sub_id,
                                      "no"=>"1",
 	                                  "reg_nm"=>$array_params['username'],
 	                                  "reg_dt"=>date('Y-m-d H:i:s')
 	                                  );
 	  $hair_dtl->create();
      if($hair_dtl->save($hairmake_gst_dtl_data)==false){
      	  return array('result'=>false,'message'=>"ヘアメイクゲストサブ作成に失敗しました。",'reason'=>$hair_dtl->getDbo()->error."[".date('Y-m-d H:i:s')."]");
      }
     return array('result'=>true);
  }

   /**
     *
     *  ヘアメイクGUESTシートのメニュー更新
     * @param $array_params
     * @return 正常：TRUE
     *         異常：FALSE
     */
   function updateHairmakeGuestMenu($array_params){

   	  App::import("Model", "HairmakeGuestSubTrn");
   	  $hair_sub = new HairmakeGuestSubTrn();

	  //ヘアメイクゲストサブメニュー更新
      $hairmake_gst_sub_data = array(
     	                              "menu"=>"'".mysql_real_escape_string($array_params['menu'])."'",
 	                                  "reg_nm"=>"'".$array_params['username']."'",
 	                                  "reg_dt"=>"'".date('Y-m-d H:i:s')."'"
 	                                  );
      /* 履歴があるので最新のメニューのIDを取得する  */
      $data = $hair_sub->find("first",array("fields"=>"MAX(id) as max_id","conditions"=>array("estimate_dtl_id"=>$array_params['estimate_dtl_id'])));
      if(count($data) > 0){  $max_id = $data[0]["max_id"]; }

      if($hair_sub->updateAll($hairmake_gst_sub_data,array("id"=>$max_id))==false){
      	return array('result'=>false,'message'=>"ヘアメイクゲストサブメニュー更新に失敗しました。",'reason'=>$hair_sub->getDbo()->error."[".date('Y-m-d H:i:s')."]");
      }
     return array('result'=>true);
  }

   /*
    *   全ヘアメイク情報を更新
    *
    *   $array_params : 更新データ
    *   $user         : 更新ユーザー名
    */
   function saveAll($array_params,$user){

     $tr = ClassRegistry::init('TransactionManager');
	 $tr->begin();

	 //ヘアメイクCPL
	 if(!empty($array_params['HairmakeCplTrn']))
	 {
	 	/* ヘッダ更新 */
	   for($header_index=0;$header_index < count($array_params['HairmakeCplTrn']);$header_index++)
	   {
	      $ret = $this->_saveHairmakeCpl($array_params['HairmakeCplTrn'][$header_index],$user);
	 	  if($ret['result']==false){return $ret;}

	     /* 時間更新 */
	     //配列の歯抜けのインデックスを詰める
	     $temp_array = array_merge($array_params['HairmakeCplTimeTrn'][$header_index]);
	     $ret = $this->_saveHairmakeCplTime($temp_array,$array_params['HairmakeCplTrn'][$header_index]["id"],$user);
	 	 if($ret['result']==false){return $ret;}

	     /* メニュー更新 */
	     for($sub_index=0;$sub_index < count($array_params['HairmakeCplMenuTrn'][$header_index]);$sub_index++)
	     {
	        $ret = $this->_saveHairmakeCplMenu($array_params['HairmakeCplMenuTrn'][$header_index][$sub_index],$user);
	 	    if($ret['result']==false){return $ret;}
	     }
	   }
	 }
	 //へメイクGUEST
	 if(!empty($array_params['HairmakeGuestTrn']))
	 {
	 	/* ヘッダ更新 */
	   for($header_index=0;$header_index < count($array_params['HairmakeGuestTrn']);$header_index++)
	   {
	      $ret = $this->_saveHairmakeGuest($array_params['HairmakeGuestTrn'][$header_index],$user);
	 	  if($ret['result']==false){return $ret;}

	      /* サブ更新 */
	      for($sub_index=0;$sub_index < count($array_params['HairmakeGuestSubTrn'][$header_index]);$sub_index++)
	      {
	         $ret = $this->_saveHairmakeGuestSub($array_params['HairmakeGuestSubTrn'][$header_index][$sub_index],$user);
	         if($ret['result']==false){return $ret;}

	         /* 明細更新 */
	         for($dtl_index=0;$dtl_index < count($array_params['HairmakeGuestDtlTrn'][$header_index][$sub_index]);$dtl_index++)
	         {
	         	//配列の歯抜けのインデックスを詰める
	         	$temp_array = array_merge($array_params['HairmakeGuestDtlTrn'][$header_index][$sub_index]);
	            $ret = $this->_saveHairmakeGuestDtl($temp_array,$array_params['HairmakeGuestSubTrn'][$header_index][$sub_index]["id"],$user);
	            if($ret['result']==false){return $ret;}
	         }
	       }
	    }
	 }
     $tr->commit();
     return array('result'=>true);
   }

   /*
    *  ＣＰＬ用のヘアメイクヘッダ情報を更新
    *
    *   $array_params : 更新データ
    *   $user         : 更新ユーザー名
    */
   function _saveHairmakeCpl($array_params,$user){

   	 App::import("Model", "HairmakeCplTrn");

    /* メインヘアメイとそれ以外で更新内容を分ける */
   	 if($array_params['main_attend_kbn'] == HC_MAIN){

   	 	$fields = array('attend_nm'         ,'phone_no'        ,'cell_no'      ,'email','menu',
                        'working_start_time','working_end_time','working_total','working_start_place',
                        'working_end_place' ,'transportation',
   	                    'total_attend' ,'rehasal_dt' ,'rehasal_start_time','rehasal_end_time','rehasal_place','rehasal_name',
 	                    'note','upd_nm','upd_dt');
   	 }else{
   	 	$array_params['main_attend_kbn'] = HC_NONE;
   	 	$fields = array('attend_nm'         ,'phone_no'        ,'cell_no'      ,'email','menu',
                        'working_start_time','working_end_time','working_total','working_start_place',
                        'working_end_place' ,'transportation'  ,'note','upd_nm','upd_dt');
   	 }

   	    /* 稼働合計時間の計算*/
   	    if(!empty($array_params['working_start_time']) &&  !empty($array_params['working_end_time'])){
   	    	$starts = explode(":", $array_params['working_start_time']);
   	 	    $ends   = explode(":", $array_params['working_end_time']);
   	 	    $array_params['working_total'] = count($starts) == 2 && count($ends) == 2 ? (($ends[0]*60)+$ends[1]) - (($starts[0]*60)+$starts[1]) : 0;
   	 	    if($array_params['working_total'] < 0){return array('result'=>false,'message'=>"ＣＰＬ用のヘアメイクヘッダ情報更新に失敗しました。",'reason'=>"稼働開始時間と終了時間の順序が不正です。");}
   	    }else{
   	    	$array_params['working_total'] = 0;
   	    }

     if(empty($array_params['rehasal_dt'])){$array_params['rehasal_dt'] = null;}
   	 $array_params['upd_nm'] = $user;
   	 $array_params['upd_dt'] = date('Y-m-d H:i:s');

     $hair = new HairmakeCplTrn;
	 $hair->id = $array_params['id'];

 	 if($hair->save($array_params,false,$fields)==false){
 	 	return array('result'=>false,'message'=>"ＣＰＬ用のヘアメイクヘッダ情報更新に失敗しました。",'reason'=>$hair->getDbo()->error."[".date('Y-m-d H:i:s')."]");
 	 }

 	 return array('result'=>true);
   }

   /*
    *  ＣＰＬ用のヘアメイクメニュー情報を更新
    *
    *   $array_params : 更新データ
    *   $user         : 更新ユーザー名
    */
   function _saveHairmakeCplMenu($array_params,$user){

   	 App::import("Model", "HairmakeCplMenuTrn");

   	 //新規追加または更新した明細IDを保持
     $saving_id= array();
     $fields = array('note','upd_nm','upd_dt');

     $array_params['upd_nm'] = $user;
   	 $array_params['upd_dt'] = date('Y-m-d H:i:s');

     $menu = new HairmakeCplMenuTrn;
	 $menu->id = $array_params['id'];

 	 if($menu->save($array_params,false,$fields)==false){
 	 	return array('result'=>false,'message'=>"ＣＰＬ用のヘアメイクメニュー情報を更新に失敗しました。",'reason'=>$menu->getDbo()->error."[".date('Y-m-d H:i:s')."]");
 	 }
     return array('result'=>true);
   }

   /*
    *  ＣＰＬ用のヘアメイク時間情報を更新
    *
    *   $array_params : 更新データ
    *   $user         : 更新ユーザー名
    */
   function _saveHairmakeCplTime($array_params,$foreign_key,$user){
 	  App::import("Model", "HairmakeCplTimeTrn");
   	  $time = new HairmakeCplTimeTrn;

   	 //新規追加または更新した明細IDを保持
     $saving_id= array();
     $fields = array('no','make_start_time','make_start_place','note','upd_nm','upd_dt');

     for($i=0;$i < count($array_params);$i++)
     {
	    //明細IDがNULLの場合はクライアント側で新規に追加した項目なので新規作成する
 	    if(empty($array_params[$i]['id']) || $array_params[$i]['id']==null)
 	    {
 	  	   $array_params[$i]['reg_nm'] = $user;
 	       $array_params[$i]['reg_dt'] = date('Y-m-d H:i:s');
 	       $array_params[$i]['hairmake_cpl_id'] =  $foreign_key;
 	       $time->create();
 	       if($time->save($array_params[$i])==false){
 	       	  return array('result'=>false,'message'=>"ＣＰＬ用のヘアメイク時間情報更新に失敗しました。",'reason'=>$time->getDbo()->error."[".date('Y-m-d H:i:s')."]");
 	       }

 	       //新規作成したデータのIDを保存
 	       $last_hairmake_cpl_time_id = $time->getLastInsertID();
 	       array_push($saving_id, $last_hairmake_cpl_time_id);
 	  }
 	  //既存の明細の更新
 	  else
 	  {
 	  	 $array_params[$i]['upd_nm'] = $user;
 	     $array_params[$i]['upd_dt'] = date('Y-m-d H:i:s');
 	     //削除されず残っているデータのIDを保存
 	     array_push($saving_id,$array_params[$i]['id']);
 	     $time->id = $array_params[$i]['id'];
 	     if($time->save($array_params[$i],false,$fields)==false){
 	     	return array('result'=>false,'message'=>"ＣＰＬ用のヘアメイク時間情報更新に失敗しました。",'reason'=>$time->getDbo()->error."[".date('Y-m-d H:i:s')."]");
 	     }
 	  }
    }
    //新規追加でも既存の明細の更新でもないデータはクライアント側で削除指定されたデータなのですべて削除
 	if($time->deleteAll( array('hairmake_cpl_id'=>$foreign_key,'NOT'=>array('id'=>$saving_id)))==false){
 		return array('result'=>false,'message'=>"ＣＰＬ用のヘアメイク時間情報削除に失敗しました。",'reason'=>$time->getDbo()->error."[".date('Y-m-d H:i:s')."]");
 	}
    return array('result'=>true);
   }

   /**
    *
    * CPL用のヘアメイクファイナルシートに引数のベンダーが存在するかチェック
    * @param $vendor_id
    * @param $customer_id
    * @return 正常：CPLヘアメイクID
    *         異常：FALSE
    */
   function hasCplHeaderDataOfVendor($vendor_id,$customer_id){

         App::import("Model", "HairmakeCplTrn");
         $hairmake_cpl = new HairmakeCplTrn();

         if($hairmake_cpl->hasAny(array('customer_id' => $customer_id,'vendor_id'=>$vendor_id))){
            $ret = $hairmake_cpl->find('first', array('fields' => 'id','conditions' => array('customer_id' => $customer_id,'vendor_id'=>$vendor_id)));
            return $ret['HairmakeCplTrn']['id'];
         }else{
         	return false;
         }
   }

   /**
    *
    * CPL用のヘアメイクファイナルシートにメインのヘアメイクが存在するかチェック
    * @param $customer_id
    * @return 正常：コーディネーターID
    *         異常：FALSE
    */
   function hasMainHairmake($customer_id){

         App::import("Model", "HairmakeCplTrn");
         $hairmake = new HairmakeCplTrn();

         if($hairmake->hasAny(array('customer_id' => $customer_id,'main_attend_kbn'=>HC_MAIN))){
            $ret = $hairmake->find('first', array('fields' => 'id','conditions' => array('customer_id' => $customer_id,'main_attend_kbn'=>HC_MAIN)));
            return $ret['HairmakeCplTrn']['id'];
         }else{
         	return false;
         }
   }

   /*
    *  GUEST用のヘアメイクヘッダ情報を更新
    *
    *   $array_params : 更新データ
    *   $user         : 更新ユーザー名
    */
   function _saveHairmakeGuest($array_params,$user){
   	 App::import("Model", "HairmakeGuestTrn");

   	 $fields = array('attend_nm' ,'phone_no' ,'cell_no','email','upd_nm','upd_dt');

   	 $array_params['upd_nm'] = $user;
   	 $array_params['upd_dt'] = date('Y-m-d H:i:s');

     $hair = new HairmakeGuestTrn;
	 $hair->id = $array_params['id'];

 	 if($hair->save($array_params,false,$fields)==false){
 	 	return array('result'=>false,'message'=>"GUEST用のヘアメイクヘッダ情報更新に失敗しました。",'reason'=>$hair->getDbo()->error."[".date('Y-m-d H:i:s')."]");
 	 }

	 return array('result'=>true);
   }

   /**
    *
    * GUEST用のヘアメイクサブ情報を更新
    * @param $array_params
    * @param $user
    * @return 正常：TRUE
    *         異常：FALSE
    */
   function _saveHairmakeGuestSub($array_params,$user){

   	 App::import("Model", "HairmakeGuestSubTrn");
   	 $hair = new HairmakeGuestSubTrn;

   	 $fields = array('note','upd_nm','upd_dt');

   	 $array_params['upd_nm'] = $user;
   	 $array_params['upd_dt'] = date('Y-m-d H:i:s');
	 $hair->id = $array_params['id'];
 	 if($hair->save($array_params,false,$fields)==false){
 	 	return array('result'=>false,'message'=>"GUEST用のヘアメイクサブ情報更新に失敗しました。",'reason'=>$hair->getDbo()->error."[".date('Y-m-d H:i:s')."]");
 	 }
 	 return array('result'=>true);
   }

   /*
    *  GUEST用のヘアメイク詳細情報を更新
    *
    *   $array_params : 更新データ
    *   $user         : 更新ユーザー名
    */
   function _saveHairmakeGuestDtl($array_params,$foreign_key,$user){

   	 App::import("Model", "HairmakeGuestDtlTrn");
   	 $hair = new HairmakeGuestDtlTrn;

   	 //新規追加または更新した明細IDを保持
     $saving_id= array();
     $fields = array('no','attend_nm','guest_nm','make_start_time','make_start_place','make_end_time','make_end_place',
 	                 'note','upd_nm' ,'upd_dt');

     for($i=0;$i < count($array_params);$i++)
     {
	  //明細IDがNULLの場合はクライアント側で新規に追加した項目なので新規作成する
 	  if(empty($array_params[$i]['id']) || $array_params[$i]['id']==null)
 	  {
 	  	       $array_params[$i]['reg_nm'] = $user;
 	           $array_params[$i]['reg_dt'] = date('Y-m-d H:i:s');
 	           $array_params[$i]['hairmake_guest_sub_id'] =  $foreign_key;
 	           $hair->create();
 	           if($hair->save($array_params[$i])==false){
 	           	  return array('result'=>false,'message'=>"GUEST用のヘアメイク詳細情報更新に失敗しました。",'reason'=>$hair->getDbo()->error."[".date('Y-m-d H:i:s')."]");
 	           }

 	           //新規作成したデータのIDを保存
 	           $last_hairmake_cpl_dtl_id = $hair->getLastInsertID();
 	           array_push($saving_id, $last_hairmake_cpl_dtl_id);
 	  }
 	  //既存の明細の更新
 	  else
 	  {
 	  	 $array_params[$i]['upd_nm'] = $user;
 	     $array_params[$i]['upd_dt'] = date('Y-m-d H:i:s');
 	     //削除されず残っているデータのIDを保存
 	     array_push($saving_id,$array_params[$i]['id']);
 	     $hair->id = $array_params[$i]['id'];
 	     if($hair->save($array_params[$i],false,$fields)==false){
 	     	return array('result'=>false,'message'=>"GUEST用のヘアメイク詳細情報更新に失敗しました。",'reason'=>$hair->getDbo()->error."[".date('Y-m-d H:i:s')."]");
 	     }
 	  }
    }
    //新規追加でも既存の明細の更新でもないデータはクライアント側で削除指定されたデータなのですべて削除
 	if($hair->deleteAll( array('hairmake_guest_sub_id'=>$foreign_key,'NOT'=>array('id'=>$saving_id)))==false){
 		return array('result'=>false,'message'=>"GUEST用のヘアメイク詳細情報削除に失敗しました。",'reason'=>$hair->getDbo()->error."[".date('Y-m-d H:i:s')."]");
 	}

    return array('result'=>true);
   }

   /**
    *
    * GUEST用のヘアメイクファイナルシートに引数のベンダーが存在するかチェック
    * @param $vendor_id
    * @param $customer_id
    * @return 正常：ゲストヘアメイクID
    *         異常：FALSE
    */
   function hasGuestHeaderDataOfVendor($vendor_id,$customer_id){

         App::import("Model", "HairmakeGuestTrn");
         $hairmake_gst = new HairmakeGuestTrn();

         if($hairmake_gst->hasAny(array('customer_id' => $customer_id,'vendor_id'=>$vendor_id))){
            $ret = $hairmake_gst->find('first', array('fields' => 'id','conditions' => array('customer_id' => $customer_id,'vendor_id'=>$vendor_id)));
            return $ret['HairmakeGuestTrn']['id'];
         }else{
         	return false;
         }
   }


   /**
    *
    * CPLヘッダテーブルがサブテーブルから参照されていなければ削除する
    * @param $customer_id
    */
   function deleteHairmakeCplIfNoSubTableData($customer_id)
   {
   	    App::import("Model", "HairmakeCplTrn");
        $hairmake = new HairmakeCplTrn();

        App::import("Model", "HairmakeCplMenuTrn");
        $hairmake_menu = new HairmakeCplMenuTrn();

        $header_ids = $hairmake->find('all', array('fields' => 'id','conditions' => array('customer_id' => $customer_id)));

        for($i=0;$i < count($header_ids);$i++)
        {
           if($hairmake_menu->hasAny(array('hairmake_cpl_id'=>$header_ids[$i]['HairmakeCplTrn']['id']))==false){
                if($hairmake->delete($header_ids[$i]['HairmakeCplTrn']['id'])==false){
                	return array('result'=>false,'message'=>"CPLヘッダテーブル削除に失敗しました。",'reason'=>$hairmake->getDbo()->error."[".date('Y-m-d H:i:s')."]");
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
   function deleteHairmakeGuestIfNoSubTableData($customer_id)
   {
   	    App::import("Model", "HairmakeGuestTrn");
        $hairmake = new HairmakeGuestTrn();

        App::import("Model", "HairmakeGuestSubTrn");
        $hairmake_sub = new HairmakeGuestSubTrn();

        $header_ids = $hairmake->find('all', array('fields' => 'id','conditions' => array('customer_id' => $customer_id)));
        for($i=0;$i < count($header_ids);$i++)
        {
           if($hairmake_sub->hasAny(array('hairmake_guest_id'=>$header_ids[$i]['HairmakeGuestTrn']['id']))==false){
                 if($hairmake->delete($header_ids[$i]['HairmakeGuestTrn']['id'])==false){
                 	return array('result'=>false,'message'=>"Guestヘッダテーブル削除に失敗しました。",'reason'=>$hairmake->getDbo()->error."[".date('Y-m-d H:i:s')."]");
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

   	  App::import("Model", "HairmakeCplTrn");
      $hairmake_cpl = new HairmakeCplTrn();

   	  $sql = "select
                 vendor_nm,
                 attend_nm,
                 phone_no,
                 cell_no,
                 email
               from hairmake_cpl_trns
              where final_sheet_id = ".$final_sheet_id."
          group by vendor_id  ";

   	  $data = $hairmake_cpl->query($sql);

   	  if(count($data) > 0){

   	  	$arr = null;
   	  	for($i =0;$i < count($data);$i++){
   	  		$attr = $data[$i]["hairmake_cpl_trns"];
   	  		$temp = array("part"=>"Hairmake(Cpl)"    ,"vendor_nm"=>$attr["vendor_nm"],"attend_nm"=>$attr["attend_nm"],
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

   	  App::import("Model", "HairmakeGuestTrn");
      $hairmake_gst = new HairmakeGuestTrn();

   	  $sql = "select
                 vendor_nm,
                 attend_nm,
                 phone_no,
                 cell_no,
                 email
               from hairmake_guest_trns
              where final_sheet_id = ".$final_sheet_id."
          group by vendor_id  ";

   	  $data = $hairmake_gst->query($sql);

   	  if(count($data) > 0){

   	  	$arr = null;
   	  	for($i =0;$i < count($data);$i++){
   	  		$attr = $data[$i]["hairmake_guest_trns"];
   	  		$temp = array("part"=>"Hairmake(Guest)"   ,"vendor_nm"=>$attr["vendor_nm"],"attend_nm"=>$attr["attend_nm"],
   	  		             "phone_no"=>$attr["phone_no"],"cell_no"=>$attr["cell_no"]    ,"email"=>$attr["email"]);
   	  	    $arr[] = $temp;
   	  	}
   	    return $arr;
   	  }
   	 return null;
   }
}
?>