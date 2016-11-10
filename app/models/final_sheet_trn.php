<?php
class FinalSheetTrn extends AppModel {
    var $name = "FinalSheetTrn";

  /**
   *
   * ファイナルシートヘッダ作成
   * @param $customer_id
   * @param $username
   */
  function createNew($customer_id,$username){

        $data = array(
                     "no"=>1,
     	             "customer_id"=>$customer_id,
 	                 "reg_nm"=>$username,
 	                 "reg_dt"=>date('Y-m-d H:i:s')
 	                  );
 	     $this->create();
         if($this->save($data)==false){
         	return array('result'=>false,'message'=>"ファイナルシートヘッダ作成に失敗しました。",'reason'=>$this->getDbo()->error."[".date('Y-m-d H:i:s')."]");
         }
         $new_id = $this->getLastInsertID();
         return array('result'=>true,'newID'=>$new_id);
  }

    /**
     *
     * ファイナルシートのコピー
     * @param $final_sheet_id
     * @param $user_name
     */
  function CopyBy($final_sheet_id,$user_name){

     $old_final_sheet_data = $this->findById($final_sheet_id);
     if(count($old_final_sheet_data) == 0){
     	return array('result'=>false,'message'=>"ファイナルシートが存在しません。",'reason'=>"[ID]".$final_sheet_id);
     }
   	 $new_final_sheet_data = array(
   	                       "no"=>$old_final_sheet_data["FinalSheetTrn"]["no"] + 1,
 	                       "customer_id"=>$old_final_sheet_data["FinalSheetTrn"]["customer_id"],
 	                       "reg_nm"=>$user_name,
 	                       "reg_dt"=>date('Y-m-d H:i:s')
 	                       );
 	//フィールドの初期化
    $this->create();
    if($this->save($new_final_sheet_data)==false){
    	return array('result'=>false,'message'=>"ファイナルシートのヘッダ作成に失敗しました。",'reason'=>$this->getDbo()->error."[".date('Y-m-d H:i:s')."]");
    }
    return array('result'=>true,'newID'=>$this->getLastInsertID());
  }

  /**
   *
   * 顧客のファイナルシートヘッダを全て削除する
   * @param $customer_id
   */
  function DeleteAllBy($customer_id){

  	 if($this->deleteAll(array("customer_id"=>$customer_id),true)==false){
      	return array('result'=>false,'message'=>"ファイナルシートヘッダの削除に失敗しました。",'reason'=>$this->getDbo()->error."[".date('Y-m-d H:i:s')."]");
     }else{
     	 return array('result'=>true);
     }
  }

  /**
   *
   * 最新のファイナルシートIDを取得する
   * @param $customer_id
   */
  function GetLatestFinalSheetIdBy($customer_id){

  	$data = $this->find("first",array("fields"=>"MAX(id) as max_id","conditions"=>array("customer_id"=>$customer_id)));
    if(count($data) > 0){  return $data[0]["max_id"]; }
    return null;
  }
}
?>