<?php 
class TravelService extends AppModel {
    var $useTable = false;
 
 /**
  * 
  * トラベルシートの新規作成  
  * @param $customer_id 
  * @param $username
  * @return 正常：TRUE
  *         異常：FALSE
  */
   function createTravelSheet($customer_id,$username,$final_sheet_id){
  	
  	 App::import("Model", "TravelTrn");
  	 App::import("Model", "TravelDtlTrn");
  	 $travel = new TravelTrn();
  	 $travel_dtl = new TravelDtlTrn();  	 
    
  	 //トラベルヘッダ作成
     $travel_data = array(
                          "customer_id"=>$customer_id,
                          "final_sheet_id"=>$final_sheet_id, 
                          "estimate_dtl_id"=>0, 	
                          "vendor_nm"=>"", 	      
                          "attend_nm"=>"",
                          "phone_no"=>"",
                          "cell_no"=>"",
                          "email"=>"",                                 
 	                      "reg_nm"=>$username,
 	                      "reg_dt"=>date('Y-m-d H:i:s')
 	                      );
 	 $travel->create();  	        
     if($travel->save($travel_data)==false){
     	return array('result'=>false,'message'=>"トラベルヘッダ作成に失敗しました。",'reason'=>$travel->getDbo()->error."[".date('Y-m-d H:i:s')."]");
     }
               
     $last_id = $travel->getLastInsertID();    	 

     //トラベル明細作成
     $travel_dtl_data = array(
                             "travel_id"=>$last_id,     	                             
                             "no"=>"1",                                 
 	                         "reg_nm"=>$username,
 	                         "reg_dt"=>date('Y-m-d H:i:s')
 	                         );
 	 $travel_dtl->create();  	        
     if($travel_dtl->save($travel_dtl_data)==false){
     	return array('result'=>false,'message'=>"トラベル明細作成に失敗しました。",'reason'=>$travel_dtl->getDbo()->error."[".date('Y-m-d H:i:s')."]");
     }
 	 return array('result'=>true);
 }
  
/**
  * 
  * トラベルシートの複製
  * @param $old_final_sheet_id
  * @param $new_final_sheet_id
  * @param $user
  */
  function copy($old_final_sheet_id,$new_final_sheet_id,$user){
 	
  	 App::import("Model", "TravelTrn");  	
  	 $travel = new TravelTrn();
  	 
  	 App::import("Model", "TravelDtlTrn");  	
  	 $travel_dtl = new TravelDtlTrn();
  	 
     $old_header = $travel->find('all',array('conditions'=>array('final_sheet_id'=>$old_final_sheet_id)));     
     if(count($old_header) > 0){
       $old_dtl = $travel_dtl->find('all',array('conditions'=>array('travel_id'=>$old_header[0]['TravelTrn']['id'])));
    
       //トラベルヘッダ作成
       $travel_data = array(
                     "final_sheet_id"=>$new_final_sheet_id,
                     "estimate_dtl_id"=>$old_header[0]['TravelTrn']['estimate_dtl_id'], 
                     "customer_id"=>$old_header[0]['TravelTrn']['customer_id'],     	           
     	             "vendor_nm"=>$old_header[0]['TravelTrn']['vendor_nm'], 	      
                     "attend_nm"=>$old_header[0]['TravelTrn']['attend_nm'],
                     "phone_no"=>$old_header[0]['TravelTrn']['phone_no'],
                     "cell_no"=>$old_header[0]['TravelTrn']['cell_no'],
                     "email"=>$old_header[0]['TravelTrn']['email'],  
                     "arrival_dt"=>$old_header[0]['TravelTrn']['arrival_dt'],
     	             "arrival_time"=>$old_header[0]['TravelTrn']['arrival_time'], 
     	             "arrival_flight_no"=>$old_header[0]['TravelTrn']['arrival_flight_no'], 	      
                     "departure_dt"=>$old_header[0]['TravelTrn']['departure_dt'],
                     "departure_time"=>$old_header[0]['TravelTrn']['departure_time'],
                     "departure_flight_no"=>$old_header[0]['TravelTrn']['departure_flight_no'],
                     "wedding_day_hotel"=>$old_header[0]['TravelTrn']['wedding_day_hotel'],  
                     "wedding_day_room_no"=>$old_header[0]['TravelTrn']['wedding_day_room_no'],
                     "note"=>$old_header[0]['TravelTrn']['note'],                 
 	                 "reg_nm"=>$user,
 	                 "reg_dt"=>date('Y-m-d H:i:s')
 	                 );
    $travel->create();  	        
    if($travel->save($travel_data)==false){
      	  return array('result'=>false,'message'=>"トラベルヘッダの新規作成に失敗しました。",'reason'=>$travel->getDbo()->error."[".date('Y-m-d H:i:s')."]");
    }               
    $travel_id = $travel->getLastInsertID();
     
    for($i=0;$i < count($old_dtl);$i++){
    
     //トラベル詳細作成
     $travel_dtl_data = array(
                           "travel_id"=>$travel_id,                            
                           "no"=>$old_dtl[$i]['TravelDtlTrn']['no'],   	   
                           "hotel_nm"=>$old_dtl[$i]['TravelDtlTrn']['hotel_nm'],
                           "checkin_dt"=>$old_dtl[$i]['TravelDtlTrn']['checkin_dt'],  
                           "checkout_dt"=>$old_dtl[$i]['TravelDtlTrn']['checkout_dt'],   
                           "note"=>$old_dtl[$i]['TravelDtlTrn']['note'],                                            
 	                       "reg_nm"=>$user,
 	                       "reg_dt"=>date('Y-m-d H:i:s')
 	                       );
     $travel_dtl->create();  	    
 	   
     if($travel_dtl->save($travel_dtl_data)==false){
     	 return array('result'=>false,'message'=>"トラベル詳細の新規作成に失敗しました。",'reason'=>$travel_dtl->getDbo()->error."[".date('Y-m-d H:i:s')."]");
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
  	 
  	 App::import("Model", "TravelTrn");  	
  	 $travel = new TravelTrn();  	 
      	
     $travel_data = array( "final_sheet_id"=>$final_sheet_id );
     
     if($travel->updateAll($travel_data,array("customer_id"=>$customer_id))==false){
       return array('result'=>false,'message'=>"トラベルファイナルシートIDの更新に失敗しました。",'reason'=>$travel->getDbo()->error."[".date('Y-m-d H:i:s')."]");
     }  
     return array('result'=>true);
 } 	
 
  /**
    * 
    * トラベルシートの削除
    * @param $customer_id
    * @return 正常：TRUE
    *         異常：FALSE
    */
   function deleteTravelSheet($customer_id){
   
      App::import("Model", "TravelTrn");
      $travel = new TravelTrn();
      //トラベルヘッダ・明細削除[カスケード削除]
      if($travel->deleteAll(array("customer_id"=>$customer_id),true)==false){
      	return array('result'=>false,'message'=>"トラベルシートの削除に失敗しました。",'reason'=>$travel->getDbo()->error."[".date('Y-m-d H:i:s')."]");
      }
     return array('result'=>true); 
   }
    
  /**
   * 
   * 全トラベル情報を更新
   * @param $array_params
   * @param $user
   * @return 正常：TRUE
   *         異常：FALSE
   */
   function saveAll($array_params,$user){
   	
     $tr = ClassRegistry::init('TransactionManager');
	 $tr->begin();
		 
     //トラベル
	 if(!empty($array_params['TravelTrn']))
	 {
	 	/* ヘッダ更新 */
	   for($header_index=0;$header_index < count($array_params['TravelTrn']);$header_index++)
	   {	   	
	      $ret = $this->_saveTravel($array_params['TravelTrn'][$header_index],$user);
	 	  if($ret['result']==false){return $ret;}  
	      
	     /* 旅程更新 */	
	     //配列の歯抜けのインデックスを詰める
	     $temp_array = array_merge($array_params['TravelDtlTrn'][$header_index]);
	     $ret = $this->_saveTravelDtl($temp_array,$array_params['TravelTrn'][$header_index]["id"],$user);
	 	 if($ret['result']==false){return $ret;}  
	   }	   
	 }
 	
     $tr->commit();     
     return array('result'=>true);  	
   }
   
  /**
   * 
   * トラベルヘッダ情報を更新
   * @param $array_params
   * @param $user
   * @return 正常：TRUE
   *         異常：FALSE
   */
   function _saveTravel($array_params,$user){
   
   	 App::import("Model", "TravelTrn"); 
      	
   	  $fields = array('vendor_nm'        ,'attend_nm','phone_no','cell_no','email',
 	                  'arrival_dt'       ,'arrival_time'       ,'arrival_flight_no',
 	                  'departure_dt'     ,'departure_time'     ,'departure_flight_no',
 	                  'wedding_day_hotel','wedding_day_room_no','note','upd_nm','upd_dt');         	
   	
   	 if(empty($array_params['arrival_dt'])){$array_params['arrival_dt'] = null;}
     if(empty($array_params['departure_dt'])){$array_params['departure_dt'] = null;}
   	 $array_params['upd_nm'] = $user;
   	 $array_params['upd_dt'] = date('Y-m-d H:i:s');
     	   	   	
     $tr = new TravelTrn;
	 $tr->id = $array_params['id']; 	
 	
 	 if($tr->save($array_params,false,$fields)==false){ 
 	 	return array('result'=>false,'message'=>"トラベルヘッダ情報更新に失敗しました。",'reason'=>$tr->getDbo()->error."[".date('Y-m-d H:i:s')."]");
 	 }
 	    	   
 	 return array('result'=>true);	     	   	
   }   
  
   /**
    * 
    * フィナルシートIDの更新
    * @param $id
    * @param $final_sheet_id
    * @param $user
    */
   function saveFinalSheetId($id,$final_sheet_id,$user){
   
   	 App::import("Model", "TravelTrn"); 
      	
  	 $fields = array('final_sheet_id','upd_nm','upd_dt');    
  	 $array_params['final_sheet_id'] = $final_sheet_id;    	
   	 $array_params['upd_nm'] = $user;
   	 $array_params['upd_dt'] = date('Y-m-d H:i:s');
     	   	   	
     $tr = new TravelTrn;
	 $tr->id = $id; 	
 	
 	 if($tr->save($array_params,false,$fields)==false){ 
 	 	return array('result'=>false,'message'=>"トラベルのファイナルシートID更新に失敗しました。",'reason'=>$tr->getDbo()->error."[".date('Y-m-d H:i:s')."]");
 	 } 	    	   
 	 return array('result'=>true);	     	   	
   }   
   
  /**
   * 
   * トラベル情報を更新
   * @param $array_params
   * @param $foreign_key
   * @param $user
   * @return 正常：TRUE
   *         異常：FALSE
   */
   function _saveTravelDtl($array_params,$foreign_key,$user){   	
   	    	   
   	 App::import("Model", "TravelDtlTrn"); 
   	 $sub = new TravelDtlTrn;
   	 
   	 //新規追加または更新した明細IDを保持
     $saving_id= array();
     $fields = array('no','hotel_nm','checkin_dt','checkout_dt','note','upd_nm','upd_dt');    

    for($i=0;$i < count($array_params);$i++)
    {
       if(empty($array_params[$i]['checkin_dt'])){$array_params[$i]['checkin_dt'] = null;}
 	   if(empty($array_params[$i]['checkout_dt'])){$array_params[$i]['checkout_dt'] = null;}
 	     
      //明細IDがNULLの場合はクライアント側で新規に追加した項目なので新規作成する	
 	  if(empty($array_params[$i]['id']) || $array_params[$i]['id']==null)
 	  {
 	  	       $array_params[$i]['reg_nm'] = $user;
 	           $array_params[$i]['reg_dt'] = date('Y-m-d H:i:s');
 	           $array_params[$i]['travel_id'] =  $foreign_key;	   
 	           $sub->create(); 	      
 	           if($sub->save($array_params[$i])==false){
 	           	  return array('result'=>false,'message'=>"トラベル情報更新に失敗しました。",'reason'=>$sub->getDbo()->error."[".date('Y-m-d H:i:s')."]");
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
 	     	 return array('result'=>false,'message'=>"トラベル情報更新に失敗しました。",'reason'=>$sub->getDbo()->error."[".date('Y-m-d H:i:s')."]");
 	     }	 	   
 	  } 	            
    } 	
    //新規追加でも既存の明細の更新でもないデータはクライアント側で削除指定されたデータなのですべて削除 	
 	if($sub->deleteAll( array('travel_id'=>$foreign_key,'NOT'=>array('id'=>$saving_id)),true)==false){
 		 return array('result'=>false,'message'=>"トラベル情報削除に失敗しました。",'reason'=>$sub->getDbo()->error."[".date('Y-m-d H:i:s')."]");
 	} 	 	
    return array('result'=>true);
   }  

  /**
    * 
    * トラベルシートが存在有無
    * @param $customer_id
    * @return 有り：TRUE
    *         無し：FALSE
    */
   function exists($customer_id){   	
     App::import("Model", "TravelTrn");  	 
  	 $travel = new TravelTrn();
   	 return $travel->find('count',array('conditions'=>array('customer_id'=>$customer_id)))==0 ? false : true;   	 
   }   

   /**
    * 
    * トラベル履歴から最新のIDを取得する
    * @param $customer_id
    */
   function GetLatestIdBy($customer_id){
   	
  	App::import("Model", "TravelTrn");
    $travel = new TravelTrn();
    $data = $travel->find("first",array("fields"=>"MAX(id) as max_id","conditions"=>array("customer_id"=>$customer_id)));
        
   	if(count($data) > 0){  return $data[0]["max_id"]; }
   	return null;  
  } 
  
  /**
    * 
    * ベンダーリスト取得
    * @param $customer_id
    */
   function getVendorList($final_sheet_id){
   	
   	  App::import("Model", "TravelTrn");
      $travel = new TravelTrn();
   	
   	  $sql = "select                
                 vendor_nm,
                 attend_nm,
                 phone_no,
                 cell_no,
                 email
               from travel_trns
              where final_sheet_id = ".$final_sheet_id;
   	  
   	  $data = $travel->query($sql);
   	  
   	  if(count($data) > 0){
   	  	
   	  	$arr = null;
   	  	for($i =0;$i < count($data);$i++){
   	  		$attr = $data[$i]["travel_trns"];
   	  		$temp = array("part"=>"Travel"    ,"vendor_nm"=>$attr["vendor_nm"],"attend_nm"=>$attr["attend_nm"],
   	  		              "phone_no"=>$attr["phone_no"],"cell_no"=>$attr["cell_no"]    ,"email"=>$attr["email"]);
   	  	    $arr[] = $temp;
   	  	}   	  	
   	    return $arr;	
   	  }
   	 return null;   	       
   }
}
?>