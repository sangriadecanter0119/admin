<?php
class FileMst extends AppModel {
    var $name = 'FileMst';

    /**
     *
     * ファイル情報の登録
     * @return multitype:boolean string |multitype:boolean unknown
     */
    function registerFileInfo($array_params){
      $data = array(
                  "root_path"=>$array_params['root_path'],
                  "folder_nm"=>$array_params['folder_nm'],
                  "file_nm"=>$array_params['file_nm'],
                  "file_size"=>$array_params['file_size'],
                  "customer_id"=>$array_params['customer_id'],
                  "reg_nm"=>$array_params['username'],
                  "reg_dt"=>date('Y-m-d H:i:s')
                  );
       $this->create();
       if($this->save($data)==false){
          return array('result'=>false,'message'=>"ファイル情報登録に失敗しました。",'reason'=>$this->getDbo()->error."[".date('Y-m-d H:i:s')."]");
       }
       $new_id = $this->getLastInsertID();
       return array('result'=>true,'new_id'=>$new_id);
    }

    /**
     * ファイル情報の削除
     * @param unknown $file_id
     * @return multitype:boolean string |multitype:boolean
     */
    function deleteFileInfoById($file_id){

       if($this->delete($file_id,false)==false){
         return array('result'=>false,'message'=>"ファイル情報削除に失敗しました。",'reason'=>$this->getDbo()->error."[".date('Y-m-d H:i:s')."]");
       }
       return array('result'=>true);
    }

    /**
     * ファイルのファイナルシート出力フラグをONにする
     * @param unknown $file_id
     * @param unknown $user_name
     * @return multitype:boolean string |multitype:boolean
     */
    function setFinalSheetOutputOn($file_id,$user_name){

      $fields = array('output_flg','upd_nm','upd_dt');
      $data = array(
                     "output_flg"=>1,
                     "upd_nm"=>$user_name,
                     "upd_dt"=>date('Y-m-d H:i:s')
                    );
      $this->id = $file_id;
      if($this->save($data,false,$fields)==false){
      return array('result'=>false,'message'=>"ファイナルシート出力フラグの更新に失敗しました。",'reason'=>$this->getDbo()->error."[".date('Y-m-d H:i:s')."]");
      }
      return array('result'=>true);
   }

   /**
     * ファイルのファイナルシート出力フラグをOFFにする
     * @param unknown $file_id
     * @param unknown $user_name
     * @return multitype:boolean string |multitype:boolean
     */
    function setFinalSheetOutputOff($file_id,$user_name){

      $fields = array('output_flg','upd_nm','upd_dt');
      $data = array(
                     "output_flg"=>0,
                     "upd_nm"=>$user_name,
                     "upd_dt"=>date('Y-m-d H:i:s')
                    );
      $this->id = $file_id;
      if($this->save($data,false,$fields)==false){
      return array('result'=>false,'message'=>"ファイナルシート出力フラグの更新に失敗しました。",'reason'=>$this->getDbo()->error."[".date('Y-m-d H:i:s')."]");
      }
      return array('result'=>true);
   }

    /**
     *
     * ファイナルシート出力フラグの有無
     * @return boolean
     */
    function isSetFinalSheetOutputOn($file_id){

      $data = $this->findById($file_id);
      return $data['FileMst']['output_flg'] == 0 ? false :true;
   }

    /**
     * 顧客のファイル一覧を取得する
     * @param unknown $customer_id
     */
    function getFileInfoOfCustomer($customer_id){
      return $this->find("all",array("conditions"=>array("customer_id"=>$customer_id)));
  }

   /**
    * ファイルパスを取得する
    * @param unknown $file_id
    * @return string
    */
    function getFilePathById($file_id){
      $ret = $this->findById($file_id);
      return $ret['FileMst']['root_path']."/".$ret['FileMst']['customer_id']."/" .$ret['FileMst']['folder_nm']."/". $ret['FileMst']['file_nm'];
    }

  /**
   * 顧客のファイル一覧をフォルダ指定で取得する
   * @param unknown $customer_id
   */
    function getFileInfoOfCustomerByFolder($customer_id,$folder_nm){
      return $this->find("all",array("conditions"=>array("customer_id"=>$customer_id,"folder_nm"=>$folder_nm)));
  }

  /**
   * 顧客のファイナル出力用のファイル一覧を取得する
   * @param unknown $customer_id
   */
   function getFinalSheetOutputFileInfoOfCustomer($customer_id){
     return $this->find("all",array("conditions"=>array("customer_id"=>$customer_id,"output_flg"=>1)));
  }
}
?>