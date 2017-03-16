<?php
class VideographerService extends AppModel {
    var $useTable = false;

   /**
     *
     * ビデオグラファーシートの新規作成
     * @param $array_params
     * @return 正常：TRUE
     *         異常：FALSE
     */
   function createVideographerSheet($array_params){

       App::import("Model", "VideographerMenuTrn");
       $video_menu = new VideographerMenuTrn();

      /* 同じベンダーのファイナルシートがない場合はヘッダとタイムテーブルを作成する */
      $video_id = $this->hasHeaderDataOfVendor($array_params['vendor_id'], $array_params['customer_id']);
      if($video_id == false){

      	  App::import("Model", "VideographerTrn");
      	  $video = new VideographerTrn();

      	   //ビデオヘッダ作成
          $video_data = array(
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
 	      $video->create();
          if($video->save($video_data)==false){
          	 return array('result'=>false,'message'=>"ビデオヘッダ作成に失敗しました。",'reason'=>$video->getDbo()->error."[".date('Y-m-d H:i:s')."]");
          }

          $video_id = $video->getLastInsertID();

      	  App::import("Model", "VideographerTimeTrn");
          $video_time = new VideographerTimeTrn();

      	  //ビデオ時間作成
          $video_time_data = array(
                               "videographer_id"=>$video_id,
                               "no"=>"1",
 	                           "reg_nm"=>$array_params['username'],
 	                           "reg_dt"=>date('Y-m-d H:i:s')
 	                          );
 	      $video_time->create();
          if($video_time->save($video_time_data)==false){
          	 return array('result'=>false,'message'=>"ビデオ時間作成に失敗しました。",'reason'=>$video_time->getDbo()->error."[".date('Y-m-d H:i:s')."]");
          }
       }

       //ビデオメニュー作成
       $video_menu_data = array(
                               "videographer_id"=>$video_id,
                               "estimate_dtl_id"=>$array_params['estimate_dtl_id'],
                               "no"=>"1",
                               "menu"=>$array_params['menu'],
 	                           "reg_nm"=>$array_params['username'],
 	                           "reg_dt"=>date('Y-m-d H:i:s')
 	                           );
 	   $video_menu->create();
       if($video_menu->save($video_menu_data)==false){
       	 return array('result'=>false,'message'=>"ビデオメニュー作成に失敗しました。",'reason'=>$video_menu->getDbo()->error."[".date('Y-m-d H:i:s')."]");
       }
     return array('result'=>true);
   }

  /**
    * ビデオグラファーシートの複製
    * @param $old_final_sheet_id
    * @param $new_final_sheet_id
    * @param $user
    */
   function copy($old_final_sheet_id,$new_final_sheet_id,$user){

  	 App::import("Model", "VideographerTrn");
  	 $video = new VideographerTrn();

  	 App::import("Model", "VideographerMenuTrn");
  	 $video_menu = new VideographerMenuTrn();

  	 App::import("Model", "VideographerTimeTrn");
  	 $video_time = new VideographerTimeTrn();

     $old_header = $video->find('all',array('conditions'=>array('final_sheet_id'=>$old_final_sheet_id)));
     if(count($old_header) > 0){
       $old_menu = $video_menu->find('all',array('conditions'=>array('videographer_id'=>$old_header[0]['VideographerTrn']['id'])));
       $old_time = $video_time->find('all',array('conditions'=>array('videographer_id'=>$old_header[0]['VideographerTrn']['id'])));

       //ビデオグラファーヘッダ作成
       $video_data = array(
                     "final_sheet_id"=>$new_final_sheet_id,
                     "customer_id"=>$old_header[0]['VideographerTrn']['customer_id'],
     	             "vendor_id"=>$old_header[0]['VideographerTrn']['vendor_id'],
     	             "vendor_nm"=>$old_header[0]['VideographerTrn']['vendor_nm'],
                     "attend_nm"=>$old_header[0]['VideographerTrn']['attend_nm'],
                     "phone_no"=>$old_header[0]['VideographerTrn']['phone_no'],
                     "cell_no"=>$old_header[0]['VideographerTrn']['cell_no'],
                     "email"=>$old_header[0]['VideographerTrn']['email'],
                     "working_start_time"=>$old_header[0]['VideographerTrn']['working_start_time'],
                     "working_end_time"=>$old_header[0]['VideographerTrn']['working_end_time'],
                     "working_total_time"=>$old_header[0]['VideographerTrn']['working_total_time'],
                     "first_meeting"=>$old_header[0]['VideographerTrn']['first_meeting'],
                     "first_meeting_place"=>$old_header[0]['VideographerTrn']['first_meeting_place'],
                     "delivery_term"=>$old_header[0]['VideographerTrn']['delivery_term'],
                     "delivery_dt"=>$old_header[0]['VideographerTrn']['delivery_dt'],
                     "delivery_time"=>$old_header[0]['VideographerTrn']['delivery_time'],
                     "delivery_place"=>$old_header[0]['VideographerTrn']['delivery_place'],
                     "reciever_nm"=>$old_header[0]['VideographerTrn']['reciever_nm'],
                     "note"=>$old_header[0]['VideographerTrn']['note'],
 	                 "reg_nm"=>$user,
 	                 "reg_dt"=>date('Y-m-d H:i:s')
 	                 );
    $video->create();
    if($video->save($video_data)==false){
      	  return array('result'=>false,'message'=>"ビデオグラファーヘッダの新規作成に失敗しました。",'reason'=>$video->getDbo()->error."[".date('Y-m-d H:i:s')."]");
    }
    $video_id = $video->getLastInsertID();

    //ビデオグラファーメニュー作成
    for($i=0;$i < count($old_menu);$i++){

     $video_menu_data = array(
                           "videographer_id"=>$video_id,
                           "estimate_dtl_id"=>$old_menu[$i]['VideographerMenuTrn']['estimate_dtl_id'],
                           "menu"=>$old_menu[$i]['VideographerMenuTrn']['menu'],
                           "note"=>$old_menu[$i]['VideographerMenuTrn']['note'],
 	                       "reg_nm"=>$user,
 	                       "reg_dt"=>date('Y-m-d H:i:s')
 	                       );
     $video_menu->create();

     if($video_menu->save($video_menu_data)==false){
     	 return array('result'=>false,'message'=>"ビデオグラファーメニューの新規作成に失敗しました。",'reason'=>$video_menu->getDbo()->error."[".date('Y-m-d H:i:s')."]");
     }
    }

    //ビデオグラファー時間作成
    for($i=0;$i < count($old_time);$i++){

     $video_time_data = array(
                           "videographer_id"=>$video_id,
                           "no"=>$old_time[$i]['VideographerTimeTrn']['no'],
                           "shooting_time"=>$old_time[$i]['VideographerTimeTrn']['shooting_time'],
                           "shooting_place"=>$old_time[$i]['VideographerTimeTrn']['shooting_place'],
                           "note"=>$old_time[$i]['VideographerTimeTrn']['note'],
 	                       "reg_nm"=>$user,
 	                       "reg_dt"=>date('Y-m-d H:i:s')
 	                       );
     $video_time->create();

     if($video_time->save($video_time_data)==false){
     	 return array('result'=>false,'message'=>"ビデオグラファー時間の新規作成に失敗しました。",'reason'=>$video_time->getDbo()->error."[".date('Y-m-d H:i:s')."]");
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

  	 App::import("Model", "VideographerTrn");
  	 $video = new VideographerTrn();

     $video_data = array( "final_sheet_id"=>$final_sheet_id );

     if($video->updateAll($video_data,array("customer_id"=>$customer_id))==false){
       return array('result'=>false,'message'=>"ビデオグラファーファイナルシートIDの更新に失敗しました。",'reason'=>$video->getDbo()->error."[".date('Y-m-d H:i:s')."]");
     }
     return array('result'=>true);
 }

   /**
     *
     * ビデオグラファーシートのメニュー更新
     * @param $array_params
     * @return 正常：TRUE
     *         異常：FALSE
     */
   function updateMenu($array_params){

       App::import("Model", "VideographerMenuTrn");
       $video_menu = new VideographerMenuTrn();

       $video_menu_data = array(
                               "menu"=>"'".mysql_real_escape_string($array_params['menu'])."'",
 	                           "reg_nm"=>"'".$array_params['username']."'",
 	                           "reg_dt"=>"'".date('Y-m-d H:i:s')."'"
 	                           );

      /* 履歴があるので最新のメニューのIDを取得する  */
      $data = $video_menu->find("first",array("fields"=>"MAX(id) as max_id","conditions"=>array("estimate_dtl_id"=>$array_params['estimate_dtl_id'])));
      if(count($data) > 0){  $max_id = $data[0]["max_id"]; }

      if($video_menu->updateAll($video_menu_data,array("id"=>$max_id))==false){
      	return array('result'=>false,'message'=>"ビデオグラファーシートのメニュー更新に失敗しました。",'reason'=>$video_menu->getDbo()->error."[".date('Y-m-d H:i:s')."]");
      }
    return array('result'=>true);
   }

    /**
     *
     * ビデオグラファーシートの削除
     * @param $customer_id
     * @return 正常：TRUE
     *         異常：FALSE
     */
    function deleteVideographerSheet($customer_id){

      App::import("Model", "VideographerTrn");
      $video = new VideographerTrn();
      //ビデオグラファーヘッダ・メニュー・時間削除[カスケード削除]
      if($video->deleteAll(array("customer_id"=>$customer_id),true)==false){
        return array('result'=>false,'message'=>"ビデオグラファーシートの削除に失敗しました。",'reason'=>$video_menu->getDbo()->error."[".date('Y-m-d H:i:s')."]");
      }
      return array('result'=>true);
    }

 /**
   *
   * ビデオグラファー関連のデータのCRUD処理
   * @param $array_params
   * @param $user
   * @return 正常：TRUE
   *         異常：FALSE
   */
   function saveAll($array_params,$user){

     $tr = ClassRegistry::init('TransactionManager');
	 $tr->begin();

	 if(!empty($array_params['VideographerTrn']))
	 {
	  /* ヘッダ更新 */
	   for($header_index=0;$header_index < count($array_params['VideographerTrn']);$header_index++)
	   {
	       $ret = $this->_saveVideographer($array_params['VideographerTrn'][$header_index],$user);
	 	   if($ret['result']==false){return $ret;}

	     /* 時間更新 */
	     //配列の歯抜けのインデックスを詰める
	     $temp_array = array_merge($array_params['VideographerTimeTrn'][$header_index]);
	     $ret = $this->_saveVideographerTime($temp_array,$array_params['VideographerTrn'][$header_index]["id"],$user);
	 	 if($ret['result']==false){return $ret;}

	     /* メニュー更新 */
	     for($sub_index=0;$sub_index < count($array_params['VideographerMenuTrn'][$header_index]);$sub_index++)
	     {
	        $ret = $this->_saveVideographerMenu($array_params['VideographerMenuTrn'][$header_index][$sub_index],$user);
	 	    if($ret['result']==false){return $ret;}
	     }
	   }
	 }
     $tr->commit();
     return array('result'=>true);
   }

 /**
   *
   * ビデオグラファーヘッダ情報を更新
   * @param $array_params
   * @param $user
   * @return 正常：テーブルID
   *         異常：FALSE
   */
   function _saveVideographer($array_params,$user){

   	 App::import("Model", "VideographerTrn");

     $fields = array('attend_nm'    ,'phone_no'      ,'cell_no'     ,'email',
                     'working_start_time','working_end_time','working_total_time',
                     'first_meeting','first_meeting_place',
                     'delivery_term','delivery_place','reciever_nm' ,'note','upd_nm','upd_dt');

   /* 稼働合計時間の計算*/
   	    if(!empty($array_params['working_start_time']) &&  !empty($array_params['working_end_time'])){
   	    	$starts = explode(":", $array_params['working_start_time']);
   	 	    $ends   = explode(":", $array_params['working_end_time']);
   	 	    $array_params['working_total_time'] = count($starts) == 2 && count($ends) == 2 ? (($ends[0]*60)+$ends[1]) - (($starts[0]*60)+$starts[1]) : 0;
   	 	    if($array_params['working_total_time'] < 0){return array('result'=>false,'message'=>"ビデオグラファーヘッダ情報更新に失敗しました。",'reason'=>"稼働開始時間と終了時間の順序が不正です。");}
   	    }else{
   	    	$array_params['working_total_time'] = 0;
   	    }

   	 $array_params['upd_nm'] = $user;
   	 $array_params['upd_dt'] = date('Y-m-d H:i:s');

     $video = new VideographerTrn;
	 $video->id = $array_params['id'];

 	 if($video->save($array_params,false,$fields)==false){
 	 	return array('result'=>false,'message'=>"ビデオグラファーヘッダ情報更新に失敗しました。",'reason'=>$video->getDbo()->error."[".date('Y-m-d H:i:s')."]");
 	 }

 	 return array('result'=>true);
   }

 /**
   *
   * ビデオグラファーメニュー情報を新規作成または更新
   * @param $array_params
   * @param $user
   * @return 正常：テーブルID
   *         異常：FALSE
   */
   function _saveVideographerMenu($array_params,$user){

   	 App::import("Model", "VideographerMenuTrn");

     $fields = array('no','menu' ,'note','upd_nm','upd_dt');

   	 $array_params['upd_nm'] = $user;
   	 $array_params['upd_dt'] = date('Y-m-d H:i:s');

     $menu = new VideographerMenuTrn;
	 $menu->id = $array_params['id'];

 	 if($menu->save($array_params,false,$fields)==false){
 	 	return array('result'=>false,'message'=>"ビデオグラファーメニュー情報更新に失敗しました。",'reason'=>$menu->getDbo()->error."[".date('Y-m-d H:i:s')."]");
 	 }
 	 return $array_params['id'];
   }

 /**
   *
   * ビデオグラファー時間情報を新規作成または更新
   * @param $array_params
   * @param $foreign_key
   * @param $user
   * @return 正常：テーブルID
   *         異常：FALSE
   */
   function _saveVideographerTime($array_params,$foreign_key,$user){

   	 App::import("Model", "VideographerTimeTrn");
   	 $video_time = new VideographerTimeTrn;

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
 	           $array_params[$i]['videographer_id'] =  $foreign_key;
 	           $video_time->create();
 	           if($video_time->save($array_params[$i])==false){
 	             	return array('result'=>false,'message'=>"ビデオグラファー時間情報更新に失敗しました。",'reason'=>$video_time->getDbo()->error."[".date('Y-m-d H:i:s')."]");
 	           }

 	           //新規作成したデータのIDを保存
 	           $last_video_time_id = $video_time->getLastInsertID();
 	           array_push($saving_id, $last_video_time_id);
 	  }
 	  //既存の明細の更新
 	  else
 	  {
 	  	 $array_params[$i]['upd_nm'] = $user;
 	     $array_params[$i]['upd_dt'] = date('Y-m-d H:i:s');
 	     //削除されず残っているデータのIDを保存
 	     array_push($saving_id,$array_params[$i]['id']);
 	     $video_time->id = $array_params[$i]['id'];
 	     if($video_time->save($array_params[$i],false,$fields)==false){
 	     	return array('result'=>false,'message'=>"ビデオグラファー時間情報更新に失敗しました。",'reason'=>$video_time->getDbo()->error."[".date('Y-m-d H:i:s')."]");
 	     }
 	  }
    }
    //新規追加でも既存の明細の更新でもないデータはクライアント側で削除指定されたデータなのですべて削除
 	if($video_time->deleteAll( array('videographer_id'=>$foreign_key,'NOT'=>array('id'=>$saving_id)))==false){
 		return array('result'=>false,'message'=>"ビデオグラファー時間情報削除に失敗しました。",'reason'=>$video_time->getDbo()->error."[".date('Y-m-d H:i:s')."]");
 	}
 	return array('result'=>true);
   }

   /**
    *
    * ビデオファイナルシートに引数のベンダーが存在するかチェック
    * @param $customer_id
    * @return 正常：ビデオID
    *         異常：FALSE
    */
   function hasHeaderDataOfVendor($vendor_id,$customer_id){

         App::import("Model", "VideographerTrn");
         $videographer = new VideographerTrn();

         if($videographer->hasAny(array('customer_id' => $customer_id,'vendor_id'=>$vendor_id))){
            $ret = $videographer->find('first', array('fields' => 'id','conditions' => array('customer_id' => $customer_id,'vendor_id'=>$vendor_id)));
            return $ret['VideographerTrn']['id'];
         }else{
         	return false;
         }
   }

  /**
    *
    * ビデオヘッダテーブルがサブテーブルから参照されていなければ削除する
    * @param $customer_id
    */
   function deleteHeaderIfNoSubTableData($customer_id)
   {
   	    App::import("Model", "VideographerTrn");
        $videographer = new VideographerTrn();

        App::import("Model", "VideographerMenuTrn");
        $videographer_menu = new VideographerMenuTrn();

        $header_ids = $videographer->find('all', array('fields' => 'id','conditions' => array('customer_id' => $customer_id)));

        for($i=0;$i < count($header_ids);$i++)
        {
           if($videographer_menu->hasAny(array('videographer_id'=>$header_ids[$i]['VideographerTrn']['id']))==false){
                if($videographer->delete($header_ids[$i]['VideographerTrn']['id'])==false){
                	return array('result'=>false,'message'=>"ビデオヘッダテーブル削除に失敗しました。",'reason'=>$videographer->getDbo()->error."[".date('Y-m-d H:i:s')."]");
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

   	  App::import("Model", "VideographerTrn");
      $video = new VideographerTrn();

   	  $sql = "select
                 vendor_nm,
                 attend_nm,
                 phone_no,
                 cell_no,
                 email
               from videographer_trns
              where final_sheet_id = ".$final_sheet_id."
          group by vendor_id  ";

   	  $data = $video->query($sql);

   	  if(count($data) > 0){

   	  	$arr = null;
   	  	for($i =0;$i < count($data);$i++){
   	  		$attr = $data[$i]["videographer_trns"];
   	  		$temp = array("part"=>"Videographer"      ,"vendor_nm"=>$attr["vendor_nm"],"attend_nm"=>$attr["attend_nm"],
   	  		              "phone_no"=>$attr["phone_no"],"cell_no"=>$attr["cell_no"]    ,"email"=>$attr["email"]);
   	  	    $arr[] = $temp;
   	  	}
   	    return $arr;
   	  }
   	 return null;
   }
}
?>