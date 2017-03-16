<?php
class PhotographerService extends AppModel {
    var $useTable = false;

   /**
     *
     * フォトグラファーシートの新規作成
     * @param $array_params
     * @return 正常：TRUE
     *         異常：FALSE
     */
   function createPhotographerSheet($array_params){

       App::import("Model", "PhotographerMenuTrn");
       $photo_menu = new PhotographerMenuTrn();

      /* 同じベンダーのファイナルシートがない場合はヘッダとタイムテーブルを作成する */
      $photo_id = $this->hasHeaderDataOfVendor($array_params['vendor_id'], $array_params['customer_id']);
      if($photo_id == false){

      	  App::import("Model", "PhotographerTrn");
      	  $photo = new PhotographerTrn();

      	   //フォトヘッダ作成
          $photo_data = array(
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
 	      $photo->create();
          if($photo->save($photo_data)==false){
          	  return array('result'=>false,'message'=>"フォトグラファーシートの新規作成に失敗しました。",'reason'=>$photo->getDbo()->error."[".date('Y-m-d H:i:s')."]");
          }

          $photo_id = $photo->getLastInsertID();

      	  App::import("Model", "PhotographerTimeTrn");
          $photo_time = new PhotographerTimeTrn();

      	  //フォト時間作成
          $photo_time_data = array(
                               "photographer_id"=>$photo_id,
                               "no"=>"1",
 	                           "reg_nm"=>$array_params['username'],
 	                           "reg_dt"=>date('Y-m-d H:i:s')
 	                          );
 	      $photo_time->create();
          if($photo_time->save($photo_time_data)==false){
          	 return array('result'=>false,'message'=>"フォト時間作成に失敗しました。",'reason'=>$photo_time->getDbo()->error."[".date('Y-m-d H:i:s')."]");
          }
       }

       //フォトメニュー作成
       $photo_menu_data = array(
                               "photographer_id"=>$photo_id,
                               "estimate_dtl_id"=>$array_params['estimate_dtl_id'],
                               "no"=>"1",
                               "menu"=>$array_params['menu'],
 	                           "reg_nm"=>$array_params['username'],
 	                           "reg_dt"=>date('Y-m-d H:i:s')
 	                           );
 	   $photo_menu->create();
       if($photo_menu->save($photo_menu_data)==false){
       	  return array('result'=>false,'message'=>"フォトメニュー作成に失敗しました。",'reason'=>$photo_menu->getDbo()->error."[".date('Y-m-d H:i:s')."]");
       }
     return array('result'=>true);
   }

  /**
    * フォトグラファーシートの複製
    * @param $old_final_sheet_id
    * @param $new_final_sheet_id
    * @param $user
    */
   function copy($old_final_sheet_id,$new_final_sheet_id,$user){

  	 App::import("Model", "PhotographerTrn");
  	 $photo = new PhotographerTrn();

  	 App::import("Model", "PhotographerMenuTrn");
  	 $photo_menu = new PhotographerMenuTrn();

  	 App::import("Model", "PhotographerTimeTrn");
  	 $photo_time = new PhotographerTimeTrn();

     $old_header = $photo->find('all',array('conditions'=>array('final_sheet_id'=>$old_final_sheet_id)));
     if(count($old_header) > 0){
       $old_menu = $photo_menu->find('all',array('conditions'=>array('photographer_id'=>$old_header[0]['PhotographerTrn']['id'])));
       $old_time = $photo_time->find('all',array('conditions'=>array('photographer_id'=>$old_header[0]['PhotographerTrn']['id'])));

       //フォトグラファーヘッダ作成
       $photo_data = array(
                     "final_sheet_id"=>$new_final_sheet_id,
                     "customer_id"=>$old_header[0]['PhotographerTrn']['customer_id'],
     	             "vendor_id"=>$old_header[0]['PhotographerTrn']['vendor_id'],
     	             "vendor_nm"=>$old_header[0]['PhotographerTrn']['vendor_nm'],
                     "attend_nm"=>$old_header[0]['PhotographerTrn']['attend_nm'],
                     "phone_no"=>$old_header[0]['PhotographerTrn']['phone_no'],
                     "cell_no"=>$old_header[0]['PhotographerTrn']['cell_no'],
                     "email"=>$old_header[0]['PhotographerTrn']['email'],
                     "working_start_time"=>$old_header[0]['PhotographerTrn']['working_start_time'],
                     "working_end_time"=>$old_header[0]['PhotographerTrn']['working_end_time'],
                     "working_total_time"=>$old_header[0]['PhotographerTrn']['working_total_time'],
                     "first_meeting"=>$old_header[0]['PhotographerTrn']['first_meeting'],
                     "first_meeting_place"=>$old_header[0]['PhotographerTrn']['first_meeting_place'],
                     "delivery_term"=>$old_header[0]['PhotographerTrn']['delivery_term'],
                     "delivery_dt"=>$old_header[0]['PhotographerTrn']['delivery_dt'],
                     "delivery_time"=>$old_header[0]['PhotographerTrn']['delivery_time'],
                     "delivery_place"=>$old_header[0]['PhotographerTrn']['delivery_place'],
                     "reciever_nm"=>$old_header[0]['PhotographerTrn']['reciever_nm'],
                     "note"=>$old_header[0]['PhotographerTrn']['note'],
 	                 "reg_nm"=>$user,
 	                 "reg_dt"=>date('Y-m-d H:i:s')
 	                 );
    $photo->create();
    if($photo->save($photo_data)==false){
      	  return array('result'=>false,'message'=>"フォトグラファーヘッダの新規作成に失敗しました。",'reason'=>$photo->getDbo()->error."[".date('Y-m-d H:i:s')."]");
    }
    $photo_id = $photo->getLastInsertID();

    //フォトグラファーメニュー作成
    for($i=0;$i < count($old_menu);$i++){

     $photo_menu_data = array(
                           "photographer_id"=>$photo_id,
                           "estimate_dtl_id"=>$old_menu[$i]['PhotographerMenuTrn']['estimate_dtl_id'],
                           "menu"=>$old_menu[$i]['PhotographerMenuTrn']['menu'],
                           "note"=>$old_menu[$i]['PhotographerMenuTrn']['note'],
 	                       "reg_nm"=>$user,
 	                       "reg_dt"=>date('Y-m-d H:i:s')
 	                       );
     $photo_menu->create();

     if($photo_menu->save($photo_menu_data)==false){
     	 return array('result'=>false,'message'=>"フォトグラファーメニューの新規作成に失敗しました。",'reason'=>$photo_menu->getDbo()->error."[".date('Y-m-d H:i:s')."]");
     }
    }

    //フォトグラファー時間作成
    for($i=0;$i < count($old_time);$i++){

     $photo_time_data = array(
                           "photographer_id"=>$photo_id,
                           "no"=>$old_time[$i]['PhotographerTimeTrn']['no'],
                           "shooting_time"=>$old_time[$i]['PhotographerTimeTrn']['shooting_time'],
                           "shooting_place"=>$old_time[$i]['PhotographerTimeTrn']['shooting_place'],
                           "note"=>$old_time[$i]['PhotographerTimeTrn']['note'],
 	                       "reg_nm"=>$user,
 	                       "reg_dt"=>date('Y-m-d H:i:s')
 	                       );
     $photo_time->create();

     if($photo_time->save($photo_time_data)==false){
     	 return array('result'=>false,'message'=>"フォトグラファー時間の新規作成に失敗しました。",'reason'=>$photo_time->getDbo()->error."[".date('Y-m-d H:i:s')."]");
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

  	 App::import("Model", "PhotographerTrn");
  	 $photo = new PhotographerTrn();

     $photo_data = array( "final_sheet_id"=>$final_sheet_id );

     if($photo->updateAll($photo_data,array("customer_id"=>$customer_id))==false){
       return array('result'=>false,'message'=>"フォトグラファーファイナルシートIDの更新に失敗しました。",'reason'=>$photo->getDbo()->error."[".date('Y-m-d H:i:s')."]");
     }
     return array('result'=>true);
 }

    /**
     *
     * フォトグラファーシートのメニュー更新
     * @param $array_params
     * @return 正常：TRUE
     *         異常：FALSE
     */
   function updateMenu($array_params){

       App::import("Model", "PhotographerMenuTrn");
       $photo_menu = new PhotographerMenuTrn();

       $photo_menu_data = array(
                               "menu"=>"'".mysql_real_escape_string($array_params['menu'])."'",
 	                           "reg_nm"=>"'".$array_params['username']."'",
 	                           "reg_dt"=>"'".date('Y-m-d H:i:s')."'"
 	                           );

       /* 履歴があるので最新のメニューのIDを取得する  */
       $data = $photo_menu->find("first",array("fields"=>"MAX(id) as max_id","conditions"=>array("estimate_dtl_id"=>$array_params['estimate_dtl_id'])));
       if(count($data) > 0){  $max_id = $data[0]["max_id"]; }

       if($photo_menu->updateAll($photo_menu_data,array("id"=>$max_id))==false){
       	   return array('result'=>false,'message'=>"フォトメニュー更新に失敗しました。",'reason'=>$photo_menu->getDbo()->error."[".date('Y-m-d H:i:s')."]");
       }
      return array('result'=>true);
   }

    /**
     *
     * フォトグラファーシートの削除
     * @param $customer_id
     * @return 正常：TRUE
     *         異常：FALSE
     */
    function deletePhotographerSheet($customer_id){

      App::import("Model", "PhotographerTrn");
      $photo = new PhotographerTrn();
      //フォトグラファーヘッダ・メニュー・時間削除[カスケード削除]
      if($photo->deleteAll(array("customer_id"=>$customer_id),true)==false){
      	 return array('result'=>false,'message'=>"フォトグラファーシートの削除に失敗しました。",'reason'=>$photo->getDbo()->error."[".date('Y-m-d H:i:s')."]");
      }
     return array('result'=>true);
    }

 /**
   *
   * フォトグラファー関連のデータのCRUD処理
   * @param $array_params
   * @param $user
   * @return 正常：TRUE
   *         異常：FALSE
   */
   function saveAll($array_params,$user){

     $tr = ClassRegistry::init('TransactionManager');
	 $tr->begin();

	 if(!empty($array_params['PhotographerTrn']))
	 {
	  /* ヘッダ更新 */
	   for($header_index=0;$header_index < count($array_params['PhotographerTrn']);$header_index++)
	   {
	      $ret = $this->_savePhotographer($array_params['PhotographerTrn'][$header_index],$user);
	      if($ret['result']==false){return $ret;}

	     /* 時間更新 */
	     //配列の歯抜けのインデックスを詰める
	     $temp_array = array_merge($array_params['PhotographerTimeTrn'][$header_index]);
	     $ret = $this->_savePhotographerTime($temp_array,$array_params['PhotographerTrn'][$header_index]["id"],$user);
	     if($ret['result']==false){return $ret;}

	     /* メニュー更新 */
	     for($sub_index=0;$sub_index < count($array_params['PhotographerMenuTrn'][$header_index]);$sub_index++)
	     {
	        $ret = $this->_savePhotographerMenu($array_params['PhotographerMenuTrn'][$header_index][$sub_index],$user);
	        if($ret['result']==false){return $ret;}
	     }
	   }
	 }
     $tr->commit();
     return array('result'=>true);
   }

 /**
   *
   * フォトグラファーヘッダ情報を更新
   * @param $array_params
   * @param $user
   * @return 正常：テーブルID
   *         異常：FALSE
   */
   function _savePhotographer($array_params,$user){

   	 App::import("Model", "PhotographerTrn");

     $fields = array('attend_nm'    ,'phone_no'      ,'cell_no'     ,'email',
                     'working_start_time','working_end_time','working_total_time',
                     'first_meeting','first_meeting_place',
                     'delivery_term','delivery_place','reciever_nm' ,'note','upd_nm','upd_dt');

     /* 稼働合計時間の計算*/
   	    if(!empty($array_params['working_start_time']) &&  !empty($array_params['working_end_time'])){
   	    	$starts = explode(":", $array_params['working_start_time']);
   	 	    $ends   = explode(":", $array_params['working_end_time']);
   	 	    $array_params['working_total_time'] = count($starts) == 2 && count($ends) == 2 ? (($ends[0]*60)+$ends[1]) - (($starts[0]*60)+$starts[1]) : 0;
   	 	    if($array_params['working_total_time'] < 0){return array('result'=>false,'message'=>"フォトグラファーヘッダ情報更新に失敗しました。",'reason'=>"稼働開始時間と終了時間の順序が不正です。");}
   	    }else{
   	    	$array_params['working_total_time'] = 0;
   	    }

   	 $array_params['upd_nm'] = $user;
   	 $array_params['upd_dt'] = date('Y-m-d H:i:s');

     $photo = new PhotographerTrn;
	 $photo->id = $array_params['id'];

 	 if($photo->save($array_params,false,$fields)==false){
 	 	return array('result'=>false,'message'=>"フォトグラファーヘッダ情報更新に失敗しました。",'reason'=>$photo->getDbo()->error."[".date('Y-m-d H:i:s')."]");
 	 }
 	return array('result'=>true);
   }

 /**
   *
   * フォトグラファーメニュー情報を新規作成または更新
   * @param $array_params
   * @param $user
   * @return 正常：テーブルID
   *         異常：FALSE
   */
   function _savePhotographerMenu($array_params,$user){

   	 App::import("Model", "PhotographerMenuTrn");

     $fields = array('no','menu' ,'note','upd_nm','upd_dt');

   	 $array_params['upd_nm'] = $user;
   	 $array_params['upd_dt'] = date('Y-m-d H:i:s');

     $menu = new PhotographerMenuTrn;
	 $menu->id = $array_params['id'];

 	 if($menu->save($array_params,false,$fields)==false){
 	 	return array('result'=>false,'message'=>"フォトグラファーメニュー情報更新に失敗しました。",'reason'=>$menu->getDbo()->error."[".date('Y-m-d H:i:s')."]");
 	 }
 	return array('result'=>true);
   }

 /**
   *
   * フォトグラファー時間情報を新規作成または更新
   * @param $array_params
   * @param $foreign_key
   * @param $user
   * @return 正常：テーブルID
   *         異常：FALSE
   */
   function _savePhotographerTime($array_params,$foreign_key,$user){

   	 App::import("Model", "PhotographerTimeTrn");
   	 $photo_time = new PhotographerTimeTrn;

   	 //新規追加または更新した明細IDを保持
     $saving_id= array();
     $fields = array('no','shooting_time','shooting_place','note','upd_nm','upd_dt');

     for($i=0;$i < count($array_params);$i++)
     {
	  //明細IDがNULLの場合はクライアント側で新規に追加した項目なので新規作成する
 	  if(empty($array_params[$i]['id']) || $array_params[$i]['id']==null)
 	  {
 	  	       $array_params[$i]['reg_nm'] = $user;
 	           $array_params[$i]['reg_dt'] = date('Y-m-d H:i:s');
 	           $array_params[$i]['photographer_id'] =  $foreign_key;
 	           $photo_time->create();
 	           if($photo_time->save($array_params[$i])==false){
 	           	 return array('result'=>false,'message'=>"フォトグラファー時間情報更新に失敗しました。",'reason'=>$photo_time->getDbo()->error."[".date('Y-m-d H:i:s')."]");
 	           }

 	           //新規作成したデータのIDを保存
 	           $last_photo_time_id = $photo_time->getLastInsertID();
 	           array_push($saving_id, $last_photo_time_id);
 	  }
 	  //既存の明細の更新
 	  else
 	  {
 	  	 $array_params[$i]['upd_nm'] = $user;
 	     $array_params[$i]['upd_dt'] = date('Y-m-d H:i:s');
 	     //削除されず残っているデータのIDを保存
 	     array_push($saving_id,$array_params[$i]['id']);
 	     $photo_time->id = $array_params[$i]['id'];
 	     if($photo_time->save($array_params[$i],false,$fields)==false){
 	     	return array('result'=>false,'message'=>"フォトグラファー時間情報更新に失敗しました。",'reason'=>$photo_time->getDbo()->error."[".date('Y-m-d H:i:s')."]");
 	     }
 	  }
    }
    //新規追加でも既存の明細の更新でもないデータはクライアント側で削除指定されたデータなのですべて削除
 	if($photo_time->deleteAll( array('photographer_id'=>$foreign_key,'NOT'=>array('id'=>$saving_id)))==false){
 		return array('result'=>false,'message'=>"フォトグラファー時間情報削除に失敗しました。",'reason'=>$photo_time->getDbo()->error."[".date('Y-m-d H:i:s')."]");
 	}
 	return array('result'=>true);
   }

   /**
    *
    * フォトファイナルシートに引数のベンダーが存在するかチェック
    * @param $customer_id
    * @return 正常：フォトID
    *         異常：FALSE
    */
   function hasHeaderDataOfVendor($vendor_id,$customer_id){

         App::import("Model", "PhotographerTrn");
         $photographer = new PhotographerTrn();

         if($photographer->hasAny(array('customer_id' => $customer_id,'vendor_id'=>$vendor_id))){
            $ret = $photographer->find('first', array('fields' => 'id','conditions' => array('customer_id' => $customer_id,'vendor_id'=>$vendor_id)));
            return $ret['PhotographerTrn']['id'];
         }else{
         	return false;
         }
   }

  /**
    *
    * フォトヘッダテーブルがサブテーブルから参照されていなければ削除する
    * @param $customer_id
    */
   function deleteHeaderIfNoSubTableData($customer_id)
   {
   	    App::import("Model", "PhotographerTrn");
        $photographer = new PhotographerTrn();

        App::import("Model", "PhotographerMenuTrn");
        $photographer_menu = new PhotographerMenuTrn();

        $header_ids = $photographer->find('all', array('fields' => 'id','conditions' => array('customer_id' => $customer_id)));

        for($i=0;$i < count($header_ids);$i++)
        {
           if($photographer_menu->hasAny(array('photographer_id'=>$header_ids[$i]['PhotographerTrn']['id']))==false){
                if($photographer->delete($header_ids[$i]['PhotographerTrn']['id'])==false){
                	return array('result'=>false,'message'=>"フォトヘッダテーブル削除に失敗しました。",'reason'=>$photographer->getDbo()->error."[".date('Y-m-d H:i:s')."]");
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

   	  App::import("Model", "PhotographerTrn");
      $photographer = new PhotographerTrn();

   	  $sql = "select
                 vendor_nm,
                 attend_nm,
                 phone_no,
                 cell_no,
                 email
               from photographer_trns
              where final_sheet_id = ".$final_sheet_id."
          group by vendor_id  ";

   	  $data = $photographer->query($sql);

   	  if(count($data) > 0){

   	  	$arr = null;
   	  	for($i =0;$i < count($data);$i++){
   	  		$attr = $data[$i]["photographer_trns"];
   	  		$temp = array("part"=>"Photographer"      ,"vendor_nm"=>$attr["vendor_nm"],"attend_nm"=>$attr["attend_nm"],
   	  		             "phone_no"=>$attr["phone_no"],"cell_no"=>$attr["cell_no"]    ,"email"=>$attr["email"]);
   	  	    $arr[] = $temp;
   	  	}
   	    return $arr;
   	  }
   	 return null;
   }
}
?>