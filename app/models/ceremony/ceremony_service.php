<?php
class CeremonyService extends AppModel {
    var $useTable = false;

 /**
  *
  * セレモニーシートの新規作成
  * @param $array_params
  * @return 正常：TRUE
  *         異常：FALSE
  */
  function createCeremonySheet($array_params){

      App::import("Model", "CeremonyTrn");
      App::import("Model", "CeremonyRingTrn");
      App::import("Model", "CeremonyFlowerTrn");
      App::import("Model", "CeremonyBrideMadeTrn");
      App::import("Model", "CeremonyGroomMadeTrn");

      $ceremony = new CeremonyTrn();
      $ring = new CeremonyRingTrn();
      $flower = new CeremonyFlowerTrn();
      $bride = new CeremonyBrideMadeTrn();
      $groom = new CeremonyGroomMadeTrn();

      //セレモニーヘッダ作成
      $ceremony_data = array(
                             "customer_id"=>$array_params['customer_id'],
                             "final_sheet_id"=>$array_params['final_sheet_id'],
     	                     "estimate_dtl_id"=>$array_params['estimate_dtl_id'],
     	                     "menu"=>$array_params['menu'],
                             "vendor_id"=>$array_params['vendor_id'],
     	                     "vendor_nm"=>$array_params['vendor_nm'],
                             "attend_nm"=>$array_params['vendor_attend_nm'],
                             "phone_no"=>$array_params['vendor_phone_no'],
                             "cell_no"=>$array_params['vendor_cell_no'],
                             "email"=>$array_params['vendor_email'],
 	                         "reg_nm"=>$array_params['username'],
 	                         "reg_dt"=>date('Y-m-d H:i:s')
 	                         );
 	  $ceremony->create();
      if($ceremony->save($ceremony_data)==false){
      	return array('result'=>false,'message'=>"セレモニーヘッダ作成に失敗しました。",'reason'=>$ceremony->getDbo()->error."[".date('Y-m-d H:i:s')."]");
      }

      $last_id = $ceremony->getLastInsertID();

      //セレモニーリング作成
      $ceremony_ring_data = array(
                                   "ceremony_id"=>$last_id,
                                   "no"=>"1",
 	                               "reg_nm"=>$array_params['username'],
 	                               "reg_dt"=>date('Y-m-d H:i:s')
 	                              );
 	  $ring->create();
      if($ring->save($ceremony_ring_data)==false){
      	return array('result'=>false,'message'=>"セレモニーリング作成に失敗しました。",'reason'=>$ring->getDbo()->error."[".date('Y-m-d H:i:s')."]");
      }

      //セレモニーフラワー作成
      $ceremony_flower_data = array(
                                    "ceremony_id"=>$last_id,
                                    "no"=>"1",
 	                                "reg_nm"=>$array_params['username'],
 	                                "reg_dt"=>date('Y-m-d H:i:s')
 	                                );
 	  $flower->create();
      if($flower->save($ceremony_flower_data)==false){
      	return array('result'=>false,'message'=>"セレモニーフラワー作成に失敗しました。",'reason'=>$flower->getDbo()->error."[".date('Y-m-d H:i:s')."]");
      }

      //セレモニーBride/Made作成
      $ceremony_bride_made_data = array(
                                      "ceremony_id"=>$last_id,
                                      "no"=>"1",
 	                                  "reg_nm"=>$array_params['username'],
 	                                  "reg_dt"=>date('Y-m-d H:i:s')
 	                                  );
 	  $bride->create();
      if($bride->save($ceremony_bride_made_data)==false){
      	return array('result'=>false,'message'=>"セレモニーBride/Made作成に失敗しました。",'reason'=>$bride->getDbo()->error."[".date('Y-m-d H:i:s')."]");
      }

      //セレモニーGroom/Made作成
      $ceremony_groom_made_data = array(
      	                               "ceremony_id"=>$last_id,
                                       "no"=>"1",
 	                                   "reg_nm"=>$array_params['username'],
 	                                   "reg_dt"=>date('Y-m-d H:i:s')
 	                                   );
 	  $groom->create();
      if($groom->save($ceremony_groom_made_data)==false){
      	return array('result'=>false,'message'=>"セレモニーGroom/Made作成に失敗しました。",'reason'=>$bride->getDbo()->error."[".date('Y-m-d H:i:s')."]");
      }
      return array('result'=>true);
    }

  /**
    * セレモニーシートの複製
    * @param $old_final_sheet_id
    * @param $new_final_sheet_id
    * @param $user
    */
   function copy($old_final_sheet_id,$new_final_sheet_id,$user){

  	 App::import("Model", "CeremonyTrn");
  	 $ceremony = new CeremonyTrn();

  	 App::import("Model", "CeremonyRingTrn");
  	 $ceremony_ring = new CeremonyRingTrn();

  	 App::import("Model", "CeremonyFlowerTrn");
  	 $ceremony_flower = new CeremonyFlowerTrn();

  	 App::import("Model", "CeremonyBrideMadeTrn");
  	 $ceremony_bride_made = new CeremonyBrideMadeTrn();

  	 App::import("Model", "CeremonyGroomMadeTrn");
  	 $ceremony_groom_made = new CeremonyGroomMadeTrn();

     $old_header = $ceremony->find('all',array('conditions'=>array('final_sheet_id'=>$old_final_sheet_id)));
     if(count($old_header) > 0){
       $old_ring       = $ceremony_ring->find('all',array('conditions'=>array('ceremony_id'=>$old_header[0]['CeremonyTrn']['id'])));
       $old_flower     = $ceremony_flower->find('all',array('conditions'=>array('ceremony_id'=>$old_header[0]['CeremonyTrn']['id'])));
       $old_bride_made = $ceremony_bride_made->find('all',array('conditions'=>array('ceremony_id'=>$old_header[0]['CeremonyTrn']['id'])));
       $old_groom_made = $ceremony_groom_made->find('all',array('conditions'=>array('ceremony_id'=>$old_header[0]['CeremonyTrn']['id'])));

       //セレモニーヘッダ作成
       $ceremony_data = array(
                     "final_sheet_id"=>$new_final_sheet_id,
                     "estimate_dtl_id"=>$old_header[0]['CeremonyTrn']['estimate_dtl_id'],
                     "customer_id"=>$old_header[0]['CeremonyTrn']['customer_id'],
     	             "vendor_id"=>$old_header[0]['CeremonyTrn']['vendor_id'],
     	             "vendor_nm"=>$old_header[0]['CeremonyTrn']['vendor_nm'],
                     "attend_nm"=>$old_header[0]['CeremonyTrn']['attend_nm'],
                     "phone_no"=>$old_header[0]['CeremonyTrn']['phone_no'],
                     "cell_no"=>$old_header[0]['CeremonyTrn']['cell_no'],
                     "email"=>$old_header[0]['CeremonyTrn']['email'],
                     "menu"=>$old_header[0]['CeremonyTrn']['menu'],
                     "rehearsal"=>$old_header[0]['CeremonyTrn']['rehearsal'],
                     "bride_escorted"=>$old_header[0]['CeremonyTrn']['bride_escorted'],
                     "ring_pollow"=>$old_header[0]['CeremonyTrn']['ring_pollow'],
                     "champagne"=>$old_header[0]['CeremonyTrn']['champagne'],
                     "toasting_speech_nm"=>$old_header[0]['CeremonyTrn']['toasting_speech_nm'],
                     "legal_wedding_kbn"=>$old_header[0]['CeremonyTrn']['legal_wedding_kbn'],
                     "procedure_nm"=>$old_header[0]['CeremonyTrn']['procedure_nm'],
                     "procedure_dt"=>$old_header[0]['CeremonyTrn']['procedure_dt'],
                     "bouquet_toss_kbn"=>$old_header[0]['CeremonyTrn']['bouquet_toss_kbn'],
                     "flower_shower_kbn"=>$old_header[0]['CeremonyTrn']['flower_shower_kbn'],
                     "bubble_shower_kbn"=>$old_header[0]['CeremonyTrn']['bubble_shower_kbn'],
                     "lei_ceremony_kbn"=>$old_header[0]['CeremonyTrn']['lei_ceremony_kbn'],
                     "lei_ceremony_count"=>$old_header[0]['CeremonyTrn']['lei_ceremony_count'],
                     "lei_ceremony_place"=>$old_header[0]['CeremonyTrn']['lei_ceremony_place'],
                     "note"=>$old_header[0]['CeremonyTrn']['note'],
                     "rw_note"=>$old_header[0]['CeremonyTrn']['rw_note'],
                     "reg_nm"=>$user,
 	                 "reg_dt"=>date('Y-m-d H:i:s')
 	                 );
    $ceremony->create();
    if($ceremony->save($ceremony_data)==false){
      	  return array('result'=>false,'message'=>"セレモニーヘッダの新規作成に失敗しました。",'reason'=>$ceremony->getDbo()->error."[".date('Y-m-d H:i:s')."]");
    }
    $ceremony_id = $ceremony->getLastInsertID();

    //セレモニーリング作成
    for($i=0;$i < count($old_ring);$i++){
     $ceremony_ring_data = array(
                           "ceremony_id"=>$ceremony_id,
                           "no"=>$old_ring[$i]['CeremonyRingTrn']['no'],
                           "ring_bg_nm"=>$old_ring[$i]['CeremonyRingTrn']['ring_bg_nm'],
                           "age"=>$old_ring[$i]['CeremonyRingTrn']['age'],
                           "sex"=>$old_ring[$i]['CeremonyRingTrn']['sex'],
                           "note"=>$old_ring[$i]['CeremonyRingTrn']['note'],
 	                       "reg_nm"=>$user,
 	                       "reg_dt"=>date('Y-m-d H:i:s')
 	                       );
     $ceremony_ring->create();

     if($ceremony_ring->save($ceremony_ring_data)==false){
     	 return array('result'=>false,'message'=>"セレモニーリングの新規作成に失敗しました。",'reason'=>$ceremony_ring->getDbo()->error."[".date('Y-m-d H:i:s')."]");
     }
    }

    //セレモニーフラワー作成
    for($i=0;$i < count($old_flower);$i++){
     $ceremony_flower_data = array(
                           "ceremony_id"=>$ceremony_id,
                           "no"=>$old_flower[$i]['CeremonyFlowerTrn']['no'],
                           "flower_bg_nm"=>$old_flower[$i]['CeremonyFlowerTrn']['flower_bg_nm'],
                           "age"=>$old_flower[$i]['CeremonyFlowerTrn']['age'],
                           "sex"=>$old_flower[$i]['CeremonyFlowerTrn']['sex'],
                           "note"=>$old_flower[$i]['CeremonyFlowerTrn']['note'],
 	                       "reg_nm"=>$user,
 	                       "reg_dt"=>date('Y-m-d H:i:s')
 	                       );
     $ceremony_flower->create();

     if($ceremony_flower->save($ceremony_flower_data)==false){
     	 return array('result'=>false,'message'=>"セレモニーフラワーの新規作成に失敗しました。",'reason'=>$ceremony_flower->getDbo()->error."[".date('Y-m-d H:i:s')."]");
     }
    }

    //セレモニーブライドメイドグ作成
    for($i=0;$i < count($old_bride_made);$i++){
     $ceremony_bride_made_data = array(
                           "ceremony_id"=>$ceremony_id,
                           "no"=>$old_bride_made[$i]['CeremonyBrideMadeTrn']['no'],
                           "bride_made_nm"=>$old_bride_made[$i]['CeremonyBrideMadeTrn']['bride_made_nm'],
                           "count"=>$old_bride_made[$i]['CeremonyBrideMadeTrn']['count'],
                           "note"=>$old_bride_made[$i]['CeremonyBrideMadeTrn']['note'],
 	                       "reg_nm"=>$user,
 	                       "reg_dt"=>date('Y-m-d H:i:s')
 	                       );
     $ceremony_bride_made->create();

     if($ceremony_bride_made->save($ceremony_bride_made_data)==false){
     	 return array('result'=>false,'message'=>"セレモニーブライドメイドの新規作成に失敗しました。",'reason'=>$ceremony_bride_made->getDbo()->error."[".date('Y-m-d H:i:s')."]");
     }
    }

     //セレモニーグルームメイド作成
    for($i=0;$i < count($old_groom_made);$i++){
     $ceremony_groom_made_data = array(
                           "ceremony_id"=>$ceremony_id,
                           "no"=>$old_groom_made[$i]['CeremonyGroomMadeTrn']['no'],
                           "groom_made_nm"=>$old_groom_made[$i]['CeremonyGroomMadeTrn']['groom_made_nm'],
                           "count"=>$old_groom_made[$i]['CeremonyGroomMadeTrn']['count'],
                           "note"=>$old_groom_made[$i]['CeremonyGroomMadeTrn']['note'],
 	                       "reg_nm"=>$user,
 	                       "reg_dt"=>date('Y-m-d H:i:s')
 	                       );
     $ceremony_groom_made->create();

     if($ceremony_groom_made->save($ceremony_groom_made_data)==false){
     	 return array('result'=>false,'message'=>"セレモニーグルームメイドの新規作成に失敗しました。",'reason'=>$ceremony_groom_made->getDbo()->error."[".date('Y-m-d H:i:s')."]");
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

  	 App::import("Model", "CeremonyTrn");
  	 $ceremony = new CeremonyTrn();

     $ceremony_data = array( "final_sheet_id"=>$final_sheet_id );

     if($ceremony->updateAll($ceremony_data,array("customer_id"=>$customer_id))==false){
       return array('result'=>false,'message'=>"セレモニーファイナルシートIDの更新に失敗しました。",'reason'=>$ceremony->getDbo()->error."[".date('Y-m-d H:i:s')."]");
     }
     return array('result'=>true);
 }

/**
  *
  * セレモニーシートのメニュー更新
  * @param $array_params
  * @return 正常：TRUE
  *         異常：FALSE
  */
  function updateMenu($array_params){

      App::import("Model", "CeremonyTrn");
      $ceremony = new CeremonyTrn();

      //セレモニーヘッダ更新
      $ceremony_data = array(
     	                     "menu"=>"'".mysql_real_escape_string($array_params['menu'])."'",
 	                         "upd_nm"=>"'".$array_params['username']."'",
 	                         "upd_dt"=>"'".date('Y-m-d H:i:s')."'"
 	                         );
     /* 履歴があるので最新のメニューのIDを取得する  */
     $data = $ceremony->find("first",array("fields"=>"MAX(id) as max_id","conditions"=>array("estimate_dtl_id"=>$array_params['estimate_dtl_id'])));
     if(count($data) > 0){  $max_id = $data[0]["max_id"]; }

      if($ceremony->updateAll($ceremony_data,array("id"=>$max_id))==false){
         return array('result'=>false,'message'=>"セレモニーヘッダ更新に失敗しました。",'reason'=>$ceremony->getDbo()->error."[".date('Y-m-d H:i:s')."]");
      }

      return array('result'=>true);
    }
 /**
  *
  * セレモニーシートの削除
  * @param $customer_id
  * @return 正常：TRUE
  *         異常：FALSE
  */
  function deleteCeremonySheet($customer_id){

      App::import("Model", "CeremonyTrn");
      $ceremony = new CeremonyTrn();
      //セレモニー・ブライドメイド・グルームメイド・フラワーメイド・リングメイド削除[カスケード削除]
      if($ceremony->deleteAll(array("customer_id"=>$customer_id),true)==false){
      	return array('result'=>false,'message'=>"セレモニーシートの削除に失敗しました。",'reason'=>$ceremony->getDbo()->error."[".date('Y-m-d H:i:s')."]");
      }
      return array('result'=>true);
  }

  /**
    *
    * 全セレモニー情報を更新
    * @param $array_params
    * @param $user
    * @return 正常：TRUE
    *         異常：FALSE
    */
   function saveAll($array_params,$user){

     $tr = ClassRegistry::init('TransactionManager');
	 $tr->begin();

	 if(!empty($array_params['CeremonyTrn']))
	 {
	   //セレモニー
	   $ret = $this->_saveCeremony($array_params['CeremonyTrn'],$user);
	   if($ret['result']==false){return $ret;}

	   //セレモニーリング
       $ret = $this->_saveCeremonyRing(array_merge($array_params['CeremonyRingTrn']),$user,$array_params['CeremonyTrn']['id']);
	   if($ret['result']==false){return $ret;}

	   //セレモニーフラワー
	   $ret = $this->_saveCeremonyFlower(array_merge($array_params['CeremonyFlowerTrn']),$user,$array_params['CeremonyTrn']['id']);
	   if($ret['result']==false){return $ret;}

       //セレモニーブライドメイド
	   $ret = $this->_saveCeremonyBride(array_merge($array_params['CeremonyBrideMadeTrn']),$user,$array_params['CeremonyTrn']['id']);
	   if($ret['result']==false){return $ret;}

	   //セレモニーグルームメイド
	   $ret = $this->_saveCeremonyGroom(array_merge($array_params['CeremonyGroomMadeTrn']),$user,$array_params['CeremonyTrn']['id']);
	   if($ret['result']==false){return $ret;}
	 }

     $tr->commit();
     return array('result'=>true);
   }

   /**
    *
    * セレモニーヘッダ情報を更新
    * @param $array_params
    * @param $user
    * @return 正常：TRUE
    *         異常：FALSE
    */
   function _saveCeremony($array_params,$user){

   	 App::import("Model", "CeremonyTrn");

   	  $fields = array('vendor_nm','attend_nm','phone_no','cell_no','email',
 	                  'menu'             ,'rehearsal'         ,'bride_escorted'    ,'ring_pollow',
 	                  'champagne'        ,'toasting_speech_nm','legal_wedding_kbn',
 	                  'procedure_nm'     ,'procedure_dt'      ,'bouquet_toss_kbn'  ,'flower_shower_kbn',
 	                  'bubble_shower_kbn','lei_ceremony_kbn'  ,'lei_ceremony_count','lei_ceremony_place',
 	                  'note'             ,'rw_note'           ,'upd_nm'            ,'upd_dt');

   	 if(empty($array_params['procedure_dt'])){$array_params['procedure_dt'] = null;}
   	 $array_params['upd_nm'] = $user;
   	 $array_params['upd_dt'] = date('Y-m-d H:i:s');

     $ce = new CeremonyTrn;
	 $ce->id = $array_params['id'];

 	 if($ce->save($array_params,false,$fields)==false){
       return array('result'=>false,'message'=>"セレモニーヘッダ更新に失敗しました。",'reason'=>$ce->getDbo()->error."[".date('Y-m-d H:i:s')."]");
 	 }
 	 return array('result'=>true);
   }

  /**
    *
    * セレモニーリング情報を更新
    * @param $array_params
    * @param $user
    * @return 正常：TRUE
    *         異常：FALSE
    */
   function _saveCeremonyRing($array_params,$user,$foreign_key){

   	 App::import("Model", "CeremonyRingTrn");
   	 $sub = new CeremonyRingTrn;

   	 //新規追加または更新した明細IDを保持
     $saving_id= array();
     $fields = array('no','ring_bg_nm','age','sex','note','upd_nm','upd_dt');

    for($i=0;$i < count($array_params);$i++)
    {
      //明細IDがNULLの場合はクライアント側で新規に追加した項目なので新規作成する
 	  if(empty($array_params[$i]['id']) || $array_params[$i]['id']==null)
 	  {
 	  	       $array_params[$i]['reg_nm'] = $user;
 	           $array_params[$i]['reg_dt'] = date('Y-m-d H:i:s');
 	           $array_params[$i]['ceremony_id'] =  $foreign_key;
 	           $sub->create();
 	           if($sub->save($array_params[$i])==false){
 	            	return array('result'=>false,'message'=>"セレモニーリング更新に失敗しました。",'reason'=>$sub->getDbo()->error."[".date('Y-m-d H:i:s')."]");
 	           }

 	           //新規作成したデータのIDを保存
 	           $last_sub_id = $sub->getLastInsertID();
 	           array_push($saving_id, $last_sub_id);
 	  }
 	  //既存の明細の更新
 	  else
 	  {
 	  	 $array_params[$i]['upd_nm'] = $user;
 	     $array_params[$i]['upd_dt'] = date('Y-m-d H:i:s');
 	     //削除されず残っているデータのIDを保存
 	     array_push($saving_id,$array_params[$i]['id']);
 	     $sub->id = $array_params[$i]['id'];
 	     if($sub->save($array_params[$i],false,$fields)==false){
 	        return array('result'=>false,'message'=>"セレモニーリング更新に失敗しました。",'reason'=>$sub->getDbo()->error."[".date('Y-m-d H:i:s')."]");
 	     }
 	  }
    }
    //新規追加でも既存の明細の更新でもないデータはクライアント側で削除指定されたデータなのですべて削除
 	if($sub->deleteAll( array('ceremony_id'=>$foreign_key,'NOT'=>array('id'=>$saving_id)),true)==false){
 	  return array('result'=>false,'message'=>"セレモニーリング削除に失敗しました。",'reason'=>$sub->getDbo()->error."[".date('Y-m-d H:i:s')."]");
 	}
    return array('result'=>true);
   }

  /**
    *
    * セレモニーフラワー情報を更新
    * @param $array_params
    * @param $user
    * @return 正常：TRUE
    *         異常：FALSE
    */
   function _saveCeremonyFlower($array_params,$user,$foreign_key){

   	 App::import("Model", "CeremonyFlowerTrn");
   	 $sub = new CeremonyFlowerTrn;

   	 //新規追加または更新した明細IDを保持
     $saving_id= array();
     $fields = array('no','flower_bg_nm','age','sex','note','upd_nm','upd_dt');

    for($i=0;$i < count($array_params);$i++)
    {
      //明細IDがNULLの場合はクライアント側で新規に追加した項目なので新規作成する
 	  if(empty($array_params[$i]['id']) || $array_params[$i]['id']==null)
 	  {
 	  	       $array_params[$i]['reg_nm'] = $user;
 	           $array_params[$i]['reg_dt'] = date('Y-m-d H:i:s');
 	           $array_params[$i]['ceremony_id'] =  $foreign_key;
 	           $sub->create();
 	           if($sub->save($array_params[$i])==false){
 	             return array('result'=>false,'message'=>"セレモニーフラワー更新に失敗しました。",'reason'=>$sub->getDbo()->error."[".date('Y-m-d H:i:s')."]");
 	           }

 	           //新規作成したデータのIDを保存
 	           $last_sub_id = $sub->getLastInsertID();
 	           array_push($saving_id, $last_sub_id);
 	  }
 	  //既存の明細の更新
 	  else
 	  {
 	  	 $array_params[$i]['upd_nm'] = $user;
 	     $array_params[$i]['upd_dt'] = date('Y-m-d H:i:s');
 	     //削除されず残っているデータのIDを保存
 	     array_push($saving_id,$array_params[$i]['id']);
 	     $sub->id = $array_params[$i]['id'];
 	     if($sub->save($array_params[$i],false,$fields)==false){
 	       return array('result'=>false,'message'=>"セレモニーフラワー更新に失敗しました。",'reason'=>$sub->getDbo()->error."[".date('Y-m-d H:i:s')."]");
 	     }
 	  }
    }
    //新規追加でも既存の明細の更新でもないデータはクライアント側で削除指定されたデータなのですべて削除
 	if($sub->deleteAll( array('ceremony_id'=>$foreign_key,'NOT'=>array('id'=>$saving_id)),true)==false){
 	  return array('result'=>false,'message'=>"セレモニーフラワー削除に失敗しました。",'reason'=>$sub->getDbo()->error."[".date('Y-m-d H:i:s')."]");
 	}
    return array('result'=>true);
   }

  /**
    *
    * セレモニーブライドメイド情報を更新
    * @param $array_params
    * @param $user
    * @return 正常：TRUE
    *         異常：FALSE
    */
   function _saveCeremonyBride($array_params,$user,$foreign_key){

   	 App::import("Model", "CeremonyBrideMadeTrn");
   	 $sub = new CeremonyBrideMadeTrn;

   	 //新規追加または更新した明細IDを保持
     $saving_id= array();
     $fields = array('no','bride_made_nm','count','note','upd_nm','upd_dt');

    for($i=0;$i < count($array_params);$i++)
    {
      //明細IDがNULLの場合はクライアント側で新規に追加した項目なので新規作成する
 	  if(empty($array_params[$i]['id']) || $array_params[$i]['id']==null)
 	  {
 	  	       $array_params[$i]['reg_nm'] = $user;
 	           $array_params[$i]['reg_dt'] = date('Y-m-d H:i:s');
 	           $array_params[$i]['ceremony_id'] =  $foreign_key;
 	           $sub->create();
 	           if($sub->save($array_params[$i])==false){
 	             return array('result'=>false,'message'=>"セレモニーブライドメイド更新に失敗しました。",'reason'=>$sub->getDbo()->error."[".date('Y-m-d H:i:s')."]");
 	           }

 	           //新規作成したデータのIDを保存
 	           $last_sub_id = $sub->getLastInsertID();
 	           array_push($saving_id, $last_sub_id);
 	  }
 	  //既存の明細の更新
 	  else
 	  {
 	  	 $array_params[$i]['upd_nm'] = $user;
 	     $array_params[$i]['upd_dt'] = date('Y-m-d H:i:s');
 	     //削除されず残っているデータのIDを保存
 	     array_push($saving_id,$array_params[$i]['id']);
 	     $sub->id = $array_params[$i]['id'];
 	     if($sub->save($array_params[$i],false,$fields)==false){
 	       return array('result'=>false,'message'=>"セレモニーブライドメイド更新に失敗しました。",'reason'=>$sub->getDbo()->error."[".date('Y-m-d H:i:s')."]");
 	     }
 	  }
    }
    //新規追加でも既存の明細の更新でもないデータはクライアント側で削除指定されたデータなのですべて削除
 	if($sub->deleteAll( array('ceremony_id'=>$foreign_key,'NOT'=>array('id'=>$saving_id)),true)==false){
 	  return array('result'=>false,'message'=>"セレモニーブライドメイド削除に失敗しました。",'reason'=>$sub->getDbo()->error."[".date('Y-m-d H:i:s')."]");
 	}
    return array('result'=>true);
   }

  /**
    *
    * グルームブライドメイド情報を更新
    * @param $array_params
    * @param $user
    * @return 正常：TRUE
    *         異常：FALSE
    */
   function _saveCeremonyGroom($array_params,$user,$foreign_key){

   	 App::import("Model", "CeremonyGroomMadeTrn");
   	 $sub = new CeremonyGroomMadeTrn;

   	 //新規追加または更新した明細IDを保持
     $saving_id= array();
     $fields = array('no','groom_made_nm','count','note','upd_nm','upd_dt');

    for($i=0;$i < count($array_params);$i++)
    {
      //明細IDがNULLの場合はクライアント側で新規に追加した項目なので新規作成する
 	  if(empty($array_params[$i]['id']) || $array_params[$i]['id']==null)
 	  {
 	  	       $array_params[$i]['reg_nm'] = $user;
 	           $array_params[$i]['reg_dt'] = date('Y-m-d H:i:s');
 	           $array_params[$i]['ceremony_id'] =  $foreign_key;
 	           $sub->create();
 	           if($sub->save($array_params[$i])==false){
 	             return array('result'=>false,'message'=>"グルームブライドメイド更新に失敗しました。",'reason'=>$sub->getDbo()->error."[".date('Y-m-d H:i:s')."]");
 	           }

 	           //新規作成したデータのIDを保存
 	           $last_sub_id = $sub->getLastInsertID();
 	           array_push($saving_id, $last_sub_id);
 	  }
 	  //既存の明細の更新
 	  else
 	  {
 	  	 $array_params[$i]['upd_nm'] = $user;
 	     $array_params[$i]['upd_dt'] = date('Y-m-d H:i:s');
 	     //削除されず残っているデータのIDを保存
 	     array_push($saving_id,$array_params[$i]['id']);
 	     $sub->id = $array_params[$i]['id'];
 	     if($sub->save($array_params[$i],false,$fields)==false){
 	       return array('result'=>false,'message'=>"グルームブライドメイド更新に失敗しました。",'reason'=>$sub->getDbo()->error."[".date('Y-m-d H:i:s')."]");
 	     }
 	  }
    }
    //新規追加でも既存の明細の更新でもないデータはクライアント側で削除指定されたデータなのですべて削除
 	if($sub->deleteAll( array('ceremony_id'=>$foreign_key,'NOT'=>array('id'=>$saving_id)),true)==false){
 	   return array('result'=>false,'message'=>"グルームブライドメイド削除に失敗しました。",'reason'=>$sub->getDbo()->error."[".date('Y-m-d H:i:s')."]");
 	}
    return array('result'=>true);
   }

/**
    *
    * ベンダーリスト取得
    * @param $customer_id
    */
   function getVendorList($final_sheet_id){

   	  App::import("Model", "CeremonyTrn");
      $ceremony = new CeremonyTrn();

   	  $sql = "select
                 vendor_nm,
                 attend_nm,
                 phone_no,
                 cell_no,
                 email
               from ceremony_trns
              where final_sheet_id = ".$final_sheet_id."
          group by vendor_id  ";

   	  $data = $ceremony->query($sql);

   	  if(count($data) > 0){

   	  	$arr = null;
   	  	for($i =0;$i < count($data);$i++){
   	  		$attr = $data[$i]["ceremony_trns"];
   	  		$temp = array("part"=>"Ceremony"      ,"vendor_nm"=>$attr["vendor_nm"],"attend_nm"=>$attr["attend_nm"],
   	  		             "phone_no"=>$attr["phone_no"],"cell_no"=>$attr["cell_no"]    ,"email"=>$attr["email"]);
   	  	    $arr[] = $temp;
   	  	}
   	    return $arr;
   	  }
   	 return null;
   }
}
?>