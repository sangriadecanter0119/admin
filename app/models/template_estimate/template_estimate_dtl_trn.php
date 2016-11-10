<?php
class TemplateEstimateDtlTrn extends AppModel {
    var $name = 'TemplateEstimateDtlTrn'; 
    
 /**
   * 
   * テンプレート見積明細を新規登録する 
   * @param $template_estimate_dtl_data
   *        配列のインデックスは１からとする        
   * @param $estimate_id
   * @param $user_name
   * @return 正常:新規データのID　
   *         異常： Exception例外
   */
  function createNew($template_estimate_dtl_data,$template_estimate_id,$user_name)
  { 	  
 	for($i=1;$i <= count($template_estimate_dtl_data);$i++)
    {
       if(!empty($template_estimate_dtl_data[$i]['goods_id']) &&  $template_estimate_dtl_data[$i]['goods_id'] !=0 )
       {       
          //外貨ベース
          if($template_estimate_dtl_data[$i]['payment_kbn_id'] == PC_INDIRECT_ABOARD_PAY || 
             $template_estimate_dtl_data[$i]['payment_kbn_id'] == PC_DIRECT_ABOARD_PAY   ||
             $template_estimate_dtl_data[$i]['payment_kbn_id'] == PC_CREDIT_ABOARD_PAY)
          {
         	//3桁区切りのカンマを除去
 	    	$template_estimate_dtl_data[$i]['sales_price'] = str_replace(",","",$template_estimate_dtl_data[$i]['foreign_sales_price']);
 	        $template_estimate_dtl_data[$i]['sales_cost']  = str_replace(",","",$template_estimate_dtl_data[$i]['foreign_sales_cost']);
 	      }
 	      //邦貨ベース
 	      else if($template_estimate_dtl_data[$i]['payment_kbn_id'] == PC_DOMESTIC_DIRECT_PAY || 
 	              $template_estimate_dtl_data[$i]['payment_kbn_id'] == PC_DOMESTIC_CREDIT_PAY)
 	      {   //3桁区切りのカンマを除去
 	        $template_estimate_dtl_data[$i]['sales_price'] = str_replace(",","",$template_estimate_dtl_data[$i]['sales_price']);
 	    	$template_estimate_dtl_data[$i]['sales_cost']  = str_replace(",","",$template_estimate_dtl_data[$i]['sales_cost']);
 	      } 
 	      //予期しない通貨フラグ
 	      else 
 	      {
 	      	 return array('result'=>false,'message'=>"テンプレート見積明細更新に失敗しました。",'reason'=>"予期しない通貨フラグ[{$template_estimate_dtl_data[$i]['currency_kbn']}]が設定されています。"); 	      	
 	      }
 	        $template_estimate_dtl_data[$i]['id'] = null;	   
 	        $template_estimate_dtl_data[$i]['no'] = $i;	      
 	        $template_estimate_dtl_data[$i]['template_estimate_id'] = $template_estimate_id; 	    
 	        $template_estimate_dtl_data[$i]['reg_nm'] = $user_name;
 	        $template_estimate_dtl_data[$i]['reg_dt'] = date('Y-m-d H:i:s');
 	        //フィールドの初期化
  	        $this->create();
 	        if($this->save($template_estimate_dtl_data[$i])==false){
 	        	return array('result'=>false,'message'=>"テンプレート見積明細更新に失敗しました。",'reason'=>$this->getDbo()->error."[".date('Y-m-d H:i:s')."]");
 	        }
 	   }
    }
    return array('result'=>true);
  }
}
?>