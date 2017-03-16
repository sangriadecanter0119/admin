<?php
class CakeService extends AppModel {
    var $useTable = false;

  /**
   *
   * ケーキシートの新規作成
   * @param $array_params
   * @return 正常：TRUE
   *         異常：FALSE
   */
   function createCakeSheet($array_params){

      App::import("Model", "CakeMenuTrn");
      $cake_menu = new CakeMenuTrn();

       /* 同じベンダーのファイナルシートがない場合はヘッダを作成する */
      $cake_id = $this->hasHeaderDataOfVendor($array_params['vendor_id'], $array_params['customer_id']);
      if($cake_id == false){

      	  App::import("Model", "CakeTrn");
      	  $cake = new CakeTrn();
          //ケーキヘッダ作成
          $cake_data = array(
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
 	     $cake->create();
         if($cake->save($cake_data)==false){
         	return array('result'=>false,'message'=>"ケーキヘッダ作成に失敗しました。",'reason'=>$cake->getDbo()->error."[".date('Y-m-d H:i:s')."]");
         }
         $cake_id = $cake->getLastInsertID();
      }
      //ケーキメニュー作成
      $cake_menu_data = array(
                              "cake_id"=>$cake_id,
                              "estimate_dtl_id"=>$array_params['estimate_dtl_id'],
                              "menu"=>$array_params['menu'],
 	                          "reg_nm"=>$array_params['username'],
 	                          "reg_dt"=>date('Y-m-d H:i:s')
 	                         );
 	  $cake_menu->create();
      if($cake_menu->save($cake_menu_data)==false){
      	return array('result'=>false,'message'=>"ケーキメニュー作成に失敗しました。",'reason'=>$cake_menu->getDbo()->error."[".date('Y-m-d H:i:s')."]");
      }
      return array('result'=>true);
   }

   /**
    * ケーキシートの複製
    * @param $old_final_sheet_id
    * @param $new_final_sheet_id
    * @param $user
    */
   function copy($old_final_sheet_id,$new_final_sheet_id,$user){

  	 App::import("Model", "CakeTrn");
  	 $cake = new CakeTrn();

  	 App::import("Model", "CakeMenuTrn");
  	 $cake_menu = new CakeMenuTrn();

     $old_header = $cake->find('all',array('conditions'=>array('final_sheet_id'=>$old_final_sheet_id)));
     if(count($old_header) > 0){
       $old_menu = $cake_menu->find('all',array('conditions'=>array('cake_id'=>$old_header[0]['CakeTrn']['id'])));

       //ケーキヘッダ作成
       $cake_data = array(
                     "final_sheet_id"=>$new_final_sheet_id,
                     "customer_id"=>$old_header[0]['CakeTrn']['customer_id'],
     	             "vendor_id"=>$old_header[0]['CakeTrn']['vendor_id'],
     	             "vendor_nm"=>$old_header[0]['CakeTrn']['vendor_nm'],
                     "attend_nm"=>$old_header[0]['CakeTrn']['attend_nm'],
                     "phone_no"=>$old_header[0]['CakeTrn']['phone_no'],
                     "cell_no"=>$old_header[0]['CakeTrn']['cell_no'],
                     "email"=>$old_header[0]['CakeTrn']['email'],
                     "delivery_term"=>$old_header[0]['CakeTrn']['delivery_term'],
                     "delivery_place"=>$old_header[0]['CakeTrn']['delivery_place'],
                     "delivery_nm"=>$old_header[0]['CakeTrn']['delivery_nm'],
                     "note"=>$old_header[0]['CakeTrn']['note'],
 	                 "reg_nm"=>$user,
 	                 "reg_dt"=>date('Y-m-d H:i:s')
 	                 );
    $cake->create();
    if($cake->save($cake_data)==false){
      	  return array('result'=>false,'message'=>"ケーキヘッダの新規作成に失敗しました。",'reason'=>$cake->getDbo()->error."[".date('Y-m-d H:i:s')."]");
    }
    $cake_id = $cake->getLastInsertID();

    for($i=0;$i < count($old_menu);$i++){

     //ケーキ詳細作成
     $cake_menu_data = array(
                           "cake_id"=>$cake_id,
                           "estimate_dtl_id"=>$old_menu[$i]['CakeMenuTrn']['estimate_dtl_id'],
                           "menu"=>$old_menu[$i]['CakeMenuTrn']['menu'],
                           "eating_place"=>$old_menu[$i]['CakeMenuTrn']['eating_place'],
                           "size"=>$old_menu[$i]['CakeMenuTrn']['size'],
                           "shaping"=>$old_menu[$i]['CakeMenuTrn']['shaping'],
                           "topping"=>$old_menu[$i]['CakeMenuTrn']['topping'],
                           "name_plate"=>$old_menu[$i]['CakeMenuTrn']['name_plate'],
                           "flavor"=>$old_menu[$i]['CakeMenuTrn']['flavor'],
                           "filling"=>$old_menu[$i]['CakeMenuTrn']['filling'],
                           "frosting"=>$old_menu[$i]['CakeMenuTrn']['frosting'],
                           "note"=>$old_menu[$i]['CakeMenuTrn']['note'],
 	                       "reg_nm"=>$user,
 	                       "reg_dt"=>date('Y-m-d H:i:s')
 	                       );
     $cake_menu->create();

     if($cake_menu->save($cake_menu_data)==false){
     	 return array('result'=>false,'message'=>"ケーキ詳細の新規作成に失敗しました。",'reason'=>$cake_menu->getDbo()->error."[".date('Y-m-d H:i:s')."]");
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

  	 App::import("Model", "CakeTrn");
  	 $cake = new CakeTrn();

     $cake_data = array( "final_sheet_id"=>$final_sheet_id );

     if($cake->updateAll($cake_data,array("customer_id"=>$customer_id))==false){
       return array('result'=>false,'message'=>"ケーキファイナルシートIDの更新に失敗しました。",'reason'=>$cake->getDbo()->error."[".date('Y-m-d H:i:s')."]");
     }
     return array('result'=>true);
 }

   /**
   *
   * ケーキシートのメニュー更新
   * @param $array_params
   * @return 正常：TRUE
   *         異常：FALSE
   */
   function updateMenu($array_params){

      App::import("Model", "CakeMenuTrn");
      $cake_menu = new CakeMenuTrn();

      $cake_menu_data = array(
                              "menu"=>"'".mysql_real_escape_string($array_params['menu'])."'",
 	                          "upd_nm"=>"'".$array_params['username']."'",
 	                          "upd_dt"=>"'".date('Y-m-d H:i:s')."'"
 	                         );

     /* 履歴があるので最新のメニューのIDを取得する  */
     $data = $cake_menu->find("first",array("fields"=>"MAX(id) as max_id","conditions"=>array("estimate_dtl_id"=>$array_params['estimate_dtl_id'])));
     if(count($data) > 0){  $max_id = $data[0]["max_id"]; }

      if($cake_menu->updateAll($cake_menu_data,array("id"=>$max_id))==false){
        return array('result'=>false,'message'=>"ケーキメニュー更新に失敗しました。",'reason'=>$cake_menu->getDbo()->error."[".date('Y-m-d H:i:s')."]");
      }
      return array('result'=>true);
   }


 /**
  *
  * ケーキシートの削除
  * @param $customer_id
  * @return 正常：TRUE
  *         異常：FALSE
  */
  function deleteCakeSheet($customer_id){

    App::import("Model", "CakeTrn");
    $cake = new CakeTrn();
    //ケーキヘッダ・メニュー削除[カスケード削除]
    if($cake->deleteAll(array("customer_id"=>$customer_id),true)==false){
    	return array('result'=>false,'message'=>"ケーキシート削除に失敗しました。",'reason'=>$this->getDbo()->error."[".date('Y-m-d H:i:s')."]");
    }
    return array('result'=>true);
  }

  /**
   *
   * 全ケーキ情報を更新
   * @param $array_params
   * @param $user
   * @return 正常：TRUE
   *         異常：FALSE
   */
   function saveAll($array_params,$user){

     $tr = ClassRegistry::init('TransactionManager');
	 $tr->begin($array_params);

	 if(!empty($array_params['CakeTrn']))
	 {
	 	 /* ヘッダ更新 */
	   for($header_index=0;$header_index < count($array_params['CakeTrn']);$header_index++)
	   {
	      $ret = $this->_saveCake($array_params['CakeTrn'][$header_index],$user);
	      if($ret['result']==false){ return $ret;}
	   }
	   /* 明細更新 */
	   for($sub_index=0;$sub_index < count($array_params['CakeMenuTrn']);$sub_index++)
	   {
	      $ret = $this->_saveCakeMenu($array_params['CakeMenuTrn'][$sub_index],$user);
	      if($ret['result']==false){ return $ret;}
	   }
	 }
     $tr->commit();
     return array('result'=>true);
   }

  /**
   *
   * ケーキヘッダ情報を更新
   * @param $array_params
   * @param $user
   * @return 正常：TRUE
   *         異常：FALSE
   */
   function _saveCake($array_params,$user){

   	 App::import("Model", "CakeTrn");

   	 $fields = array('attend_nm'     ,'phone_no'       ,'cell_no'    ,'email' ,
                     'delivery_term' ,'delivery_place' ,'delivery_nm','note'  ,'upd_nm','upd_dt');

   	 $array_params['upd_nm'] = $user;
   	 $array_params['upd_dt'] = date('Y-m-d H:i:s');

     $cake = new CakeTrn;
	 $cake->id = $array_params['id'];

 	 if($cake->save($array_params,false,$fields)==false){
       return array('result'=>false,'message'=>"ケーキヘッダ更新に失敗しました。",'reason'=>$cake->getDbo()->error."[".date('Y-m-d H:i:s')."]");
 	 }
 	return array('result'=>true);
   }

  /**
   *
   * ケーキメニュー情報を更新
   * @param $array_params
   * @param $user
   * @return 正常：TRUE
   *         異常：FALSE
   */
   function _saveCakeMenu($array_params,$user){

   	 App::import("Model", "CakeMenuTrn");
   	 $menu = new CakeMenuTrn;

     $fields = array('eating_place','size'   ,'shaping','topping','name_plate'  ,'flavor',
                     'filling','frosting','decoration','flower','note'  ,'upd_nm' ,'upd_dt');

     for($i=0;$i < count($array_params);$i++)
     {
 	  	 $array_params[$i]['upd_nm'] = $user;
 	     $array_params[$i]['upd_dt'] = date('Y-m-d H:i:s');
 	     $menu->id = $array_params[$i]['id'];
 	     if($menu->save($array_params[$i],false,$fields)==false){
 	       return array('result'=>false,'message'=>"ケーキメニュー更新に失敗しました。",'reason'=>$menu->getDbo()->error."[".date('Y-m-d H:i:s')."]");
 	     }
    }
    return array('result'=>true);
   }

  /**
    *
    * ケーキファイナルシートに引数のベンダーが存在するかチェック
    * @param $vendor_id
    * @param $customer_id
    * @return 正常：ケーキID
    *         異常：FALSE
    */
   function hasHeaderDataOfVendor($vendor_id,$customer_id){

         App::import("Model", "CakeTrn");
         $cake = new CakeTrn();

         if($cake->hasAny(array('customer_id' => $customer_id,'vendor_id'=>$vendor_id))){
            $ret = $cake->find('first', array('fields' => 'id','conditions' => array('customer_id' => $customer_id,'vendor_id'=>$vendor_id)));
            return $ret['CakeTrn']['id'];
         }else{
         	return false;
         }
   }

   /**
    *
    * Cakeヘッダテーブルがサブテーブルから参照されていなければ削除する
    * @param $customer_id
    */
   function deleteHeaderIfNoSubTableData($customer_id)
   {
   	    App::import("Model", "CakeTrn");
        $cake = new CakeTrn();

        App::import("Model", "CakeMenuTrn");
        $cake_menu = new CakeMenuTrn();

        $header_ids = $cake->find('all', array('fields' => 'id','conditions' => array('customer_id' => $customer_id)));

        for($i=0;$i < count($header_ids);$i++)
        {
           if($cake_menu->hasAny(array('cake_id'=>$header_ids[$i]['CakeTrn']['id']))==false){
                if($cake->delete($header_ids[$i]['CakeTrn']['id'])==false){
                	return array('result'=>false,'message'=>"ケーキヘッダ削除に失敗しました。",'reason'=>$cake_menu->getDbo()->error."[".date('Y-m-d H:i:s')."]");
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

   	  App::import("Model", "CakeTrn");
      $cake = new CakeTrn();

   	  $sql = "select
                 vendor_nm,
                 attend_nm,
                 phone_no,
                 cell_no,
                 email
               from cake_trns
              where final_sheet_id = ".$final_sheet_id."
          group by vendor_id  ";

   	  $data = $cake->query($sql);

   	  if(count($data) > 0){

   	  	$arr = null;
   	  	for($i =0;$i < count($data);$i++){
   	  		$attr = $data[$i]["cake_trns"];
   	  		$temp = array("part"=>"Cake"              ,"vendor_nm"=>$attr["vendor_nm"],"attend_nm"=>$attr["attend_nm"],
   	  		             "phone_no"=>$attr["phone_no"],"cell_no"=>$attr["cell_no"]    ,"email"=>$attr["email"]);
   	  	    $arr[] = $temp;
   	  	}
   	    return $arr;
   	  }
   	 return null;
   }
}
?>