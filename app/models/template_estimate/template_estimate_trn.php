<?php
class TemplateEstimateTrn extends AppModel {
    var $name = 'TemplateEstimateTrn'; 
    
  /**
   * 
   * テンプレート見積ヘッダのデータを新規登録する
   * @param $estimate_data
   * @param $user_name
   * @return 正常 :新規データのID　
   *          異常 : 
   */
  function createNew($template_estimate_data,$user_name)
  { 	
    $template_estimate_data['reg_nm'] = $user_name;
    $template_estimate_data['reg_dt'] = date('Y-m-d H:i:s');
 	$template_estimate_data['id'] = null;
    //フィールドの初期化
 	$this->create();  
 	if($this->save($template_estimate_data)==false){
 	  return array('result'=>false,'message'=>"テンプレート見積ヘッダの登録に失敗しました。",'reason'=>$this->getDbo()->error."[".date('Y-m-d H:i:s')."]");
 	}
 	return array('result'=>true,'newID'=>$this->getLastInsertID()); 
  }  
  
  /**
   * 
   * テンプレート見積ヘッダのデータ更新
   * @param $template_estimate_data
   * @param $user_name
   */
  function update($template_estimate_data,$id,$user_name)
  { 
  	$fields = array('hawaii_tax_rate','service_rate_nm','service_rate','discount_nm','discount','discount_rate_nm','discount_rate',
  	                'exchange_rate','upd_nm','upd_dt');	
    $template_estimate_data['upd_nm'] = $user_name;
    $template_estimate_data['upd_dt'] = date('Y-m-d H:i:s'); 	
    $this->id = $id;
   
 	if($this->save($template_estimate_data,false,$fields)==false){
 	  return array('result'=>false,'message'=>"テンプレート見積ヘッダの更新に失敗しました。",'reason'=>$this->getDbo()->error."[".date('Y-m-d H:i:s')."]");
 	}
 	return array('result'=>true); 
  }  
}