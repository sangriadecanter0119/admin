<?php
class DataConvertManager extends AppModel {
    var $useTable = false;
   
   function execute(){
    
   	$tr = ClassRegistry::init('TransactionManager');
	$tr->begin(); 
	
       App::import("Model", "EstimateTrn");
       $estimate = new EstimateTrn();
       App::import("Model", "FinalSheetTrn");
       $final_sheet = new FinalSheetTrn();
       
       /* 見積もりが採用されている顧客IDを取得する */
       $customer_ids = $estimate->find('all',array('fields'=>array('customer_id'),'conditions'=>array('adopt_flg'=>1)));
      
       /* ファイナルシートテーブルに登録 */
       for($i=0; $i < count($customer_ids);$i++){
       	       	
         $final_sheet_data = array(
                               "no"=>1,        
                               "customer_id"=>$customer_ids[$i]['EstimateTrn']['customer_id'], 	         
                               "reg_nm"=>'自動登録',
 	                           "reg_dt"=>date('Y-m-d H:i:s')
 	                           );
 	     $final_sheet->create();  	        
         if($final_sheet->save($final_sheet_data)==false){
       	   return array('result'=>false,'message'=>"ファイナルシートテーブル作成に失敗しました。",'reason'=>$final_sheet->getDbo()->error."[".date('Y-m-d H:i:s')."]");
         }  	
         
         $final_sheet_id = $final_sheet->getLastInsertID();
        
        /* 各カテゴリのファイナルシートにフィナルシートIDを設定 */
        $result = $this->_exexute($customer_ids[$i]['EstimateTrn']['customer_id'], $final_sheet_id);
        if($result==false){return $result;}   
       }   
       
    $tr->commit();
    return array('result'=>true);
  }
   
   function _exexute($customer_id,$final_sheet_id){
   	
    /* アルバムシート*/
	 App::import("Model", "AlbumService");
     $album = new AlbumService();
     $ret = $album->updateFinalSheetId($customer_id, $final_sheet_id);
     if($ret['result']==false){return $ret;}  	
   	
    /* ケーキシート */
	App::import("Model", "CakeService");
    $cake = new CakeService();
    $ret = $cake->updateFinalSheetId($customer_id, $final_sheet_id);
    if($ret['result']==false){return $ret;}	

     /* セレモニーシート */
     App::import("Model", "CeremonyService");
     $ceremony = new CeremonyService();
     $ret = $ceremony->updateFinalSheetId($customer_id, $final_sheet_id);
     if($ret['result']==false){return $ret;}
             
     /* セレモニーオプションシート */
     App::import("Model", "CeremonyOptionService");
     $ceremony_option = new CeremonyOptionService();
     $ret = $ceremony_option->updateFinalSheetId($customer_id, $final_sheet_id);
     if($ret['result']==false){return $ret;}
  
     /* トラベルシート */
     App::import("Model", "TravelService");
     $travel = new TravelService();
     $ret = $travel->updateFinalSheetId($customer_id, $final_sheet_id);
     if($ret['result']==false){return $ret;}

     /* ヘアメイクシート */
     App::import("Model", "HairmakeService");
     $hair = new HairmakeService();
     $ret = $hair->updateFinalSheetIdOfCpl($customer_id, $final_sheet_id);
     if($ret['result']==false){return $ret;}
    
     $ret = $hair->updateFinalSheetIdOfGuest($customer_id, $final_sheet_id);
     if($ret['result']==false){return $ret;}

     /* トランスポーテーションシート */
     App::import("Model", "TransportationService");
     $trans = new TransportationService();
     $ret = $trans->updateFinalSheetIdOfCpl($customer_id, $final_sheet_id);
     if($ret['result']==false){return $ret;}
     
     $ret = $trans->updateFinalSheetIdOfGuest($customer_id, $final_sheet_id);
     if($ret['result']==false){return $ret;}
  	   	  
     /* コーディネーターシート */
     App::import("Model", "CoordinatorService");
     $coordinator = new CoordinatorService();
     $ret = $coordinator->updateFinalSheetId($customer_id, $final_sheet_id);	   	                        
     if($ret['result']==false){return $ret;}

     /* フラワーシート */
     App::import("Model", "FlowerService");
     $flower = new FlowerService();
     $ret = $flower->updateFinalSheetId($customer_id, $final_sheet_id);
     if($ret['result']==false){return $ret;}
  
     /*フォトシート */
     App::import("Model", "PhotographerService");
     $photo = new PhotographerService();
     $ret = $photo->updateFinalSheetId($customer_id, $final_sheet_id);
     if($ret['result']==false){return $ret;}
  
     /* ビデオシート */
     App::import("Model", "VideographerService");
     $video = new VideographerService();
     $ret = $video->updateFinalSheetId($customer_id, $final_sheet_id);
     if($ret['result']==false){return $ret;}

     /* エンターテインメントシート */
     App::import("Model", "EntertainmentService");
     $entertainment = new EntertainmentService();
     $ret = $entertainment->updateFinalSheetId($customer_id, $final_sheet_id);
     if($ret['result']==false){return $ret;}

     /* ミニスターシート */
     App::import("Model", "MinisterService");
     $minister = new MinisterService();
     $ret = $minister->updateFinalSheetId($customer_id, $final_sheet_id);
     if($ret['result']==false){return $ret;}

     /* MCシート */
     App::import("Model", "McService");
     $mc = new McService();
     $ret = $mc->updateFinalSheetId($customer_id, $final_sheet_id);
     if($ret['result']==false){return $ret;}

     /* ハウスウェディングシート */
     App::import("Model", "HouseWeddingService");
     $house = new HouseWeddingService();
     $ret = $house->updateFinalSheetId($customer_id, $final_sheet_id);
     if($ret['result']==false){return $ret;}
  
     /* レセプションシート */     
     App::import("Model", "ReceptionService");
     $recep = new ReceptionService();
     $ret = $recep->updateFinalSheetId($customer_id, $final_sheet_id);
     if($ret['result']==false){return $ret;}

     /* トランスレセプションシート */
     App::import("Model", "TransRecepService");
     $trans_recep = new TransRecepService();     	   	                      
     $ret = $trans_recep->updateFinalSheetId($customer_id, $final_sheet_id);
     if($ret['result']==false){return $ret;}

     /* パーティオプションシート */
     App::import("Model", "PartyOptionService");
     $party_option = new PartyOptionService();
     $ret = $party_option->updateFinalSheetId($customer_id, $final_sheet_id);
     if($ret['result']==false){return $ret;}
  
     /* ケーキシート */
     App::import("Model", "CakeService");     	   
     $cake = new CakeService();
     $ret = $cake->updateFinalSheetId($customer_id, $final_sheet_id);
     if($ret['result']==false){return $ret;}

     /* リネンシート */
     App::import("Model", "LinenService");
     $linen = new LinenService();
     $ret = $linen->updateFinalSheetId($customer_id, $final_sheet_id);
     if($ret['result']==false){return $ret;}

     /* AVシート */
     App::import("Model", "AvService");
     $av = new AvService();
     $ret = $av->updateFinalSheetId($customer_id, $final_sheet_id);
     if($ret['result']==false){return $ret;}   

     /* ペーパーシート */
     App::import("Model", "PaperService");
     $paper = new paperService();
     $ret = $paper->updateFinalSheetId($customer_id, $final_sheet_id);
     if($ret['result']==false){return $ret;}
     
   	 return array('result'=>true);
   } 
}
?>