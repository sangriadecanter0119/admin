<?php
class TransRecepService extends AppModel {
    var $useTable = false;


    /**
     *
     *  レセプショントランスポーテーションシートの新規作成
     * @param $goods_data
     * @param $estimate_dtl_id
     * @param $customer_id
     * @param $menu
     * @param $username
     * @return 正常：TRUE
     *         異常：FALSE
     */
    function createTransRecepSheet($array_params){

    	App::import("Model", "TransRecepSubTrn");
    	App::import("Model", "TransRecepDtlTrn");
    	$trans_sub = new TransRecepSubTrn();
    	$trans_dtl = new TransRecepDtlTrn();

    	/* 同じベンダーのファイナルシートがない場合はヘッダを作成する */
        $trans_id = $this->hasHeaderDataOfVendor($array_params['vendor_id'], $array_params['customer_id']);
        if($trans_id == false){

          App::import("Model", "TransRecepTrn");
          $trans = new TransRecepTrn();

          //レセプショントランスポーテーションヘッダ作成
     	  $trans_data = array(
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
          if($trans->save($trans_data)==false){
          	return array('result'=>false,'message'=>"レセプショントランスポーテーションヘッダ作成に失敗しました。",'reason'=>$trans->getDbo()->error."[".date('Y-m-d H:i:s')."]");
          }

          $trans_id = $trans->getLastInsertID();
        }
         //レセプショントランスポーテーションサブ作成
         $trans_sub_data = array(
     	                         "trans_recep_id"=>$trans_id,
                                 "estimate_dtl_id"=>$array_params['estimate_dtl_id'],
                                 "menu"=>$array_params['menu'],
                                 "vihicular_type"=>$array_params['content'],
 	                             "reg_nm"=>$array_params['username'],
 	                             "reg_dt"=>date('Y-m-d H:i:s')
 	                             );
 	     $trans_sub->create();
         if($trans_sub->save($trans_sub_data)==false){
         	return array('result'=>false,'message'=>"レセプショントランスポーテーションサブ作成に失敗しました。",'reason'=>$trans_sub->getDbo()->error."[".date('Y-m-d H:i:s')."]");
         }

         $trans_sub_id = $trans_sub->getLastInsertID();

         //レセプショントランスポーテーション詳細作成
         $trans_dtl_data = array(
     	                             "trans_recep_sub_id"=>$trans_sub_id,
                                     "no"=>"1",
 	                                 "reg_nm"=>$array_params['username'],
 	                                 "reg_dt"=>date('Y-m-d H:i:s')
 	                                 );
 	     $trans_dtl->create();
         if($trans_dtl->save($trans_dtl_data)==false){
         	return array('result'=>false,'message'=>"レセプショントランスポーテーション詳細作成に失敗しました。",'reason'=>$trans_dtl->getDbo()->error."[".date('Y-m-d H:i:s')."]");
         }
         return array('result'=>true);
   }

  /**
    * レセプショントランスポーテーションシートの複製
    * @param $old_final_sheet_id
    * @param $new_final_sheet_id
    * @param $user
    */
   function copy($old_final_sheet_id,$new_final_sheet_id,$user){

  	 App::import("Model", "TransRecepTrn");
  	 $trans_recep = new TransRecepTrn();

  	 App::import("Model", "TransRecepSubTrn");
  	 $trans_recep_sub = new TransRecepSubTrn();

  	 App::import("Model", "TransRecepDtlTrn");
  	 $trans_recep_dtl = new TransRecepDtlTrn();

     $old_header = $trans_recep->find('all',array('conditions'=>array('final_sheet_id'=>$old_final_sheet_id)));
     if(count($old_header) > 0){
       $old_sub = $trans_recep_sub->find('all',array('conditions'=>array('trans_recep_id'=>$old_header[0]['TransRecepTrn']['id'])));

       //レセプショントランスポーテーションヘッダ作成
       $trans_recep_data = array(
                     "final_sheet_id"=>$new_final_sheet_id,
                     "customer_id"=>$old_header[0]['TransRecepTrn']['customer_id'],
     	             "vendor_id"=>$old_header[0]['TransRecepTrn']['vendor_id'],
     	             "vendor_nm"=>$old_header[0]['TransRecepTrn']['vendor_nm'],
                     "attend_nm"=>$old_header[0]['TransRecepTrn']['attend_nm'],
                     "phone_no"=>$old_header[0]['TransRecepTrn']['phone_no'],
                     "cell_no"=>$old_header[0]['TransRecepTrn']['cell_no'],
                     "email"=>$old_header[0]['TransRecepTrn']['email'],
                     "note"=>$old_header[0]['TransRecepTrn']['note'],
 	                 "reg_nm"=>$user,
 	                 "reg_dt"=>date('Y-m-d H:i:s')
 	                 );
    $trans_recep->create();
    if($trans_recep->save($trans_recep_data)==false){
      	  return array('result'=>false,'message'=>"レセプショントランスポーテーションヘッダの新規作成に失敗しました。",'reason'=>$trans_recep->getDbo()->error."[".date('Y-m-d H:i:s')."]");
    }
    $trans_recep_id = $trans_recep->getLastInsertID();

    //レセプショントランスポーテーションサブ作成
    for($i=0;$i < count($old_sub);$i++){

     $trans_recep_sub_data = array(
                           "trans_recep_id"=>$trans_recep_id,
                           "estimate_dtl_id"=>$old_sub[$i]['TransRecepSubTrn']['estimate_dtl_id'],
                           "menu"=>$old_sub[$i]['TransRecepSubTrn']['menu'],
                           "vihicular_type"=>$old_sub[$i]['TransRecepSubTrn']['vihicular_type'],
                           "final_dest"=>$old_sub[$i]['TransRecepSubTrn']['final_dest'],
                           "working_start_time"=>$old_sub[$i]['TransRecepSubTrn']['working_start_time'],
                           "working_end_time"=>$old_sub[$i]['TransRecepSubTrn']['working_end_time'],
                           "working_total"=>$old_sub[$i]['TransRecepSubTrn']['working_total'],
                           "note"=>$old_sub[$i]['TransRecepSubTrn']['note'],
 	                       "reg_nm"=>$user,
 	                       "reg_dt"=>date('Y-m-d H:i:s')
 	                       );
     $trans_recep_sub->create();

     if($trans_recep_sub->save($trans_recep_sub_data)==false){
     	 return array('result'=>false,'message'=>"レセプショントランスポーテーションサブの新規作成に失敗しました。",'reason'=>$trans_recep_sub->getDbo()->error."[".date('Y-m-d H:i:s')."]");
     }

     $trans_recep_sub_id = $trans_recep_sub->getLastInsertID();
     $old_dtl = $trans_recep_dtl->find('all',array('conditions'=>array('trans_recep_sub_id'=>$old_sub[$i]['TransRecepSubTrn']['id'])));

    //レセプショントランスポーテーション詳細作成
    for($i=0;$j < count($old_dtl);$j++){

     $trans_recep_dtl_data = array(
                           "trans_recep_sub_id"=>$trans_recep_sub_id,
                           "no"=>$old_dtl[$j]['TransRecepDtlTrn']['no'],
                           "representative_nm"=>$old_dtl[$j]['TransRecepDtlTrn']['representative_nm'],
                           "departure_time"=>$old_dtl[$j]['TransRecepDtlTrn']['departure_time'],
                           "departure_place"=>$old_dtl[$j]['TransRecepDtlTrn']['make_start_place'],
                           "total_departure_passenger"=>$old_dtl[$j]['TransRecepDtlTrn']['total_departure_passenger'],
                           "arrival_time"=>$old_dtl[$j]['TransRecepDtlTrn']['make_start_time'],
                           "arrival_place"=>$old_dtl[$j]['TransRecepDtlTrn']['make_start_place'],
                           "total_arrival_passenger"=>$old_dtl[$j]['TransRecepDtlTrn']['total_arrival_passenger'],
                           "note"=>$old_dtl[$j]['TransRecepDtlTrn']['note'],
 	                       "reg_nm"=>$user,
 	                       "reg_dt"=>date('Y-m-d H:i:s')
 	                       );
     $trans_recep_dtl->create();

     if($trans_recep_dtl->save($trans_recep_dtl_data)==false){
     	 return array('result'=>false,'message'=>"レセプショントランスポーテーション詳細の新規作成に失敗しました。",'reason'=>$trans_recep_dtl->getDbo()->error."[".date('Y-m-d H:i:s')."]");
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
  function updateFinalSheetId($customer_id,$final_sheet_id){

  	 App::import("Model", "TransRecepTrn");
  	 $trans_recep = new TransRecepTrn();

     $trans_recep_data = array( "final_sheet_id"=>$final_sheet_id );

     if($trans_recep->updateAll($trans_recep_data,array("customer_id"=>$customer_id))==false){
       return array('result'=>false,'message'=>"レセプショントランスポーテーションファイナルシートIDの更新に失敗しました。",'reason'=>$trans_recep->getDbo()->error."[".date('Y-m-d H:i:s')."]");
     }
     return array('result'=>true);
 }

    /**
     *
     *  レセプショントランスポーテーションシートのメニュー更新
     * @param $array_params
     * @return 正常：TRUE
     *         異常：FALSE
     */
    function updateMenu($array_params){

    	App::import("Model", "TransRecepSubTrn");
    	$trans_sub = new TransRecepSubTrn();

        $trans_sub_data = array(
                                     "menu"=>"'".mysql_real_escape_string($array_params['menu'])."'",
 	                                 "reg_nm"=>"'".$array_params['username']."'",
 	                                 "reg_dt"=>"'".date('Y-m-d H:i:s')."'"
 	                                 );

         /* 履歴があるので最新のメニューのIDを取得する  */
         $data = $trans_sub->find("first",array("fields"=>"MAX(id) as max_id","conditions"=>array("estimate_dtl_id"=>$array_params['estimate_dtl_id'])));
         if(count($data) > 0){  $max_id = $data[0]["max_id"]; }

         if($trans_sub->updateAll($trans_sub_data,array("id"=>$max_id))==false){
         	return array('result'=>false,'message'=>"レセプショントランスポーテーションシートのメニュー更新に失敗しました。",'reason'=>$trans_sub->getDbo()->error."[".date('Y-m-d H:i:s')."]");
         }
         return array('result'=>true);
    }


   /**
    *
    * レセプショントランスポーテーションシートの削除
    * @param $customer_id
    * @return 正常：TRUE
    *         異常：FALSE
    */
    function deleteTransRecepSheet($customer_id){

     App::import("Model", "TransRecepTrn");
     $trans = new TransRecepTrn();
     //レセプショントランスポーテーションヘッダ・サブ・明細削除[カスケード削除]
     if($trans->deleteAll(array("customer_id"=>$customer_id),true)==false){
     	 return array('result'=>false,'message'=>"レセプショントランスポーテーションシートの削除に失敗しました。",'reason'=>$trans->getDbo()->error."[".date('Y-m-d H:i:s')."]");
     }
     return array('result'=>true);
   }

  /**
    *
    * 全レセプショントランポーテーション情報を更新
    * @param $array_params
    * @param $user
    * @return 正常：TRUE
    *         異常：FALSE
    */
   function saveAll($array_params,$user){

     $tr = ClassRegistry::init('TransactionManager');
	 $tr->begin();

	  //レセプショントランスポート
	 if(!empty($array_params['TransRecepTrn']))
	 {
	   /* ヘッダ更新 */
	   for($header_index=0;$header_index < count($array_params['TransRecepTrn']);$header_index++)
	   {
	      $ret = $this->_saveTransRecep($array_params['TransRecepTrn'][$header_index],$user);
	 	  if($ret['result']==false){return $ret;}

	      /* サブ更新 */
	      for($sub_index=0;$sub_index < count($array_params['TransRecepSubTrn'][$header_index]);$sub_index++)
	      {
	         $ret = $this->_saveTransRecepSub($array_params['TransRecepSubTrn'][$header_index][$sub_index],$user);
	         if($ret['result']==false){return $ret;}

	         /* 明細更新 */
	         for($dtl_index=0;$dtl_index < count($array_params['TransRecepDtlTrn'][$header_index][$sub_index]);$dtl_index++)
	         {
	         	//配列の歯抜けのインデックスを詰める
	         	$temp_array = array_merge($array_params['TransRecepDtlTrn'][$header_index][$sub_index]);
	            $ret = $this->_saveTransRecepDtl($temp_array,$array_params['TransRecepSubTrn'][$header_index][$sub_index]["id"],$user);

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
    * レセプショントランポーテーションヘッダ情報を更新
    * @param $array_params
    * @param $user
    * @return 正常：TRUE
    *         異常：FALSE
    */
   function _saveTransRecep($array_params,$user){

   	 App::import("Model", "TransRecepTrn");

   	 $fields = array('attend_nm' ,'phone_no' ,'cell_no','email','note','upd_nm','upd_dt');

   	 $array_params['upd_nm'] = $user;
   	 $array_params['upd_dt'] = date('Y-m-d H:i:s');

     $tran = new TransRecepTrn;
	 $tran->id = $array_params['id'];

 	 if($tran->save($array_params,false,$fields)==false){
 	 	return array('result'=>false,'message'=>"レセプショントランポーテーションヘッダ情報更新に失敗しました。",'reason'=>$tran->getDbo()->error."[".date('Y-m-d H:i:s')."]");
 	 }
 	return array('result'=>true);
   }

  /**
    *
    * レセプショントランポーテーションサブ情報を更新
    * @param $array_params
    * @param $user
    * @return 正常：TRUE
    *         異常：FALSE
    */
   function _saveTransRecepSub($array_params,$user){

   	 App::import("Model", "TransRecepSubTrn");
     $tran = new TransRecepSubTrn;

   	 $fields = array('vihicular_type'   ,'final_dest'     ,'working_start_time','working_end_time','working_total',
 	                 'note','upd_nm','upd_dt');

   	 /* 稼働合計時間の計算*/
   	    if(!empty($array_params['working_start_time']) &&  !empty($array_params['working_end_time'])){
   	    	$starts = explode(":", $array_params['working_start_time']);
   	 	    $ends   = explode(":", $array_params['working_end_time']);
   	 	    $array_params['working_total'] = count($starts) == 2 && count($ends) == 2 ? (($ends[0]*60)+$ends[1]) - (($starts[0]*60)+$starts[1]) : 0;
   	 	    if($array_params['working_total'] < 0){return array('result'=>false,'message'=>"レセプショントランポーテーションサブ情報更新に失敗しました。",'reason'=>"稼働開始時間と終了時間の順序が不正です。");}
   	    }else{
   	    	$array_params['working_total'] = 0;
   	    }

   	 $array_params['upd_nm'] = $user;
   	 $array_params['upd_dt'] = date('Y-m-d H:i:s');
	 $tran->id = $array_params['id'];
 	 if($tran->save($array_params,false,$fields)==false){
 	 	return array('result'=>false,'message'=>"レセプショントランポーテーションサブ情報更新に失敗しました。",'reason'=>$tran->getDbo()->error."[".date('Y-m-d H:i:s')."]");
 	 }
     return array('result'=>true);
   }

   /**
    *
    * レセプショントランポーテーション詳細情報を更新
    * @param $array_params
    * @param $foreign_key
    * @param $user
    * @return 正常：TRUE
    *         異常：FALSE
    */
   function _saveTransRecepDtl($array_params,$foreign_key,$user){

   	 App::import("Model", "TransRecepDtlTrn");
   	 $tran = new TransRecepDtlTrn;

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
 	      $array_params[$i]['trans_recep_sub_id'] =  $foreign_key;
 	      $tran->create();
 	      if($tran->save($array_params[$i])==false){
 	      	return array('result'=>false,'message'=>"レセプショントランポーテーション詳細情報更新に失敗しました。",'reason'=>$tran->getDbo()->error."[".date('Y-m-d H:i:s')."]");
 	      }

 	      //新規作成したデータのIDを保存
 	      $last_trans_dtl_id = $tran->getLastInsertID();
 	      array_push($saving_id, $last_trans_dtl_id);
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
 	     	return array('result'=>false,'message'=>"レセプショントランポーテーション詳細情報に失敗しました。",'reason'=>$tran->getDbo()->error."[".date('Y-m-d H:i:s')."]");
 	     }
 	  }
    }
    //新規追加でも既存の明細の更新でもないデータはクライアント側で削除指定されたデータなのですべて削除
 	if($tran->deleteAll( array('trans_recep_sub_id'=>$foreign_key,'NOT'=>array('id'=>$saving_id)))==false){
 		return array('result'=>false,'message'=>"レセプショントランポーテーション詳細情報削除に失敗しました。",'reason'=>$tran->getDbo()->error."[".date('Y-m-d H:i:s')."]");
 	}
    return array('result'=>true);
   }

  /**
    *
    * レセプショントランポーテーションファイナルシートに引数のベンダーが存在するかチェック
    * @param $vendor_id
    * @param $customer_id
    * @return 正常：ゲストトランポーテーションID
    *         異常：FALSE
    */
   function hasHeaderDataOfVendor($vendor_id,$customer_id){

         App::import("Model", "TransRecepTrn");
         $trans = new TransRecepTrn();

         if($trans->hasAny(array('customer_id' => $customer_id,'vendor_id'=>$vendor_id))){
            $ret = $trans->find('first', array('fields' => 'id','conditions' => array('customer_id' => $customer_id,'vendor_id'=>$vendor_id)));
            return $ret['TransRecepTrn']['id'];
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
   	    App::import("Model", "TransRecepTrn");
        $trans = new TransRecepTrn();

        App::import("Model", "TransRecepSubTrn");
        $trans_sub = new TransRecepSubTrn();

        $header_ids = $trans->find('all', array('fields' => 'id','conditions' => array('customer_id' => $customer_id)));
        for($i=0;$i < count($header_ids);$i++)
        {
           if($trans_sub->hasAny(array('trans_id'=>$header_ids[$i]['TransRecepTrn']['id']))==false){
                 if($trans->delete($header_ids[$i]['TransRecepTrn']['id'])==false){
                 	return array('result'=>false,'message'=>"レセプションヘッダテーブル削除に失敗しました。",'reason'=>$trans_gst->getDbo()->error."[".date('Y-m-d H:i:s')."]");
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

   	 App::import("Model", "TransRecepTrn");
      $trans = new TransRecepTrn();

   	  $sql = "select
                 vendor_nm,
                 attend_nm,
                 phone_no,
                 cell_no,
                 email
               from trans_recep_trns
              where final_sheet_id = ".$final_sheet_id."
          group by vendor_id  ";

   	  $data = $trans->query($sql);

   	  if(count($data) > 0){

   	  	$arr = null;
   	  	for($i =0;$i < count($data);$i++){
   	  		$attr = $data[$i]["trans_recep_trns"];
   	  		$temp = array("part"=>"ReceptionTransportation"    ,"vendor_nm"=>$attr["vendor_nm"],"attend_nm"=>$attr["attend_nm"],
   	  		              "phone_no"=>$attr["phone_no"],"cell_no"=>$attr["cell_no"]    ,"email"=>$attr["email"]);
   	  	    $arr[] = $temp;
   	  	}
   	    return $arr;
   	  }
   	 return null;
   }

}
?>