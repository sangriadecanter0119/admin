<?php
class FinalSheetService extends AppModel {
    var $useTable = false;

 /**
  *
  * 見積IDをキーにファイナルシートを新規作成する
  * @param  $estimate_id 新規作成した明細ID
  * @param  $customer_id
  * @param  $username
  * @return 正常:TRUE
  *         異常：  　
  */
  function createFinalSheetByEstimateId($estimate_id,$customer_id,$username){

  	 $tr = ClassRegistry::init('TransactionManager');
	 $tr->begin();

	 App::import("Model", "EstimateDtlTrn");
	 $estimate_dtl = new EstimateDtlTrn();
	 $estimate_dtl_data = $estimate_dtl->find("all",array('conditions'=> array('estimate_id'=>$estimate_id)));

	 /* ファイナルシートヘッダ作成  */
     App::import("Model", "FinalSheetTrn");
	 $final_sheet = new FinalSheetTrn();
	 $ret = $final_sheet->createNew($customer_id,$username);
     if($ret['result']==false){return $ret;}
     $final_sheet_id = $ret['newID'];

	 /* travelシートは初回のみ作成 */
	 App::import("Model", "TravelService");
     $travel = new TravelService();
     if($travel->exists($customer_id)==false){
        $ret = $travel->createTravelSheet($customer_id, $username,$final_sheet_id);
        if($ret['result']==false){return $ret;}
     // travelシートが存在する場合は最新のフィナルシートIDで更新する
     }else{
        $travel_id = $travel->GetLatestIdBy($customer_id);
        $ret = $travel->saveFinalSheetId($travel_id, $final_sheet_id, $username);
        if($ret['result']==false){return $ret;}
     }

       for($i =0;$i < count($estimate_dtl_data);$i++)
       {
     	   $ret = $this->_create($estimate_dtl_data[$i]['EstimateDtlTrn'],$customer_id,$username,$final_sheet_id);
     	   if($ret['result']==false){return $ret;}
       }
      $tr->commit();
      return array('result'=>true);
    }

 /**
  *
  * 見積明細IDをキーにファイナルシートを新規作成する
  * @param $estimate_dtl_ids  新規作成した見積明細IDの配列
  * @param $customer_id
  * @param $username
  * @return 正常:TRUE
  *         異常：  　
  */
  function createFinalSheetByEstimateDtlIds($estimate_dtl_ids,$customer_id,$username,$final_sheet_id){

	 App::import("Model", "EstimateDtlTrn");
	 $estimate_dtl = new EstimateDtlTrn();

       /* travelシートは初回のみ作成 */
	   App::import("Model", "TravelService");
       $travel = new TravelService();
       if($travel->exists($customer_id)==false){
          $ret = $travel->createTravelSheet($customer_id, $username,$final_sheet_id);
          if($ret['result']==false){return $ret;}
       // travelシートが存在する場合は最新のフィナルシートIDで更新する
       }else{
        $travel_id = $travel->GetLatestIdBy($customer_id);
        $ret = $travel->saveFinalSheetId($travel_id, $final_sheet_id, $username);
        if($ret['result']==false){return $ret;}
       }

       for($i =0;$i < count($estimate_dtl_ids);$i++)
       {
           $estimate_dtl_data = $estimate_dtl->findById($estimate_dtl_ids[$i]);
           $ret = $this->_create($estimate_dtl_data['EstimateDtlTrn'],$customer_id,$username,$final_sheet_id);
           if($ret['result']==false){return $ret;}
       }
      return array('result'=>true);
  }

  /**
   *
   * 単品商品とセット商品の振り分けをしてファイナルシートを作成する
   * @param $estimate_dtl
   * @param $customer_id
   * @param $username
   */
  function _create($estimate_dtl,$customer_id,$username,$final_sheet_id){

      App::import("Model", "GoodsMstView");
	  $goods_view = new GoodsMstView();
	  App::import("Model", "SetGoodsMst");
	  $set_goods = new SetGoodsMst();

      $goods_data = $goods_view->findById($estimate_dtl['goods_id']);

      /* セット商品の場合は親商品に紐づく子商品を検索してそれぞれ登録する */
      if($goods_data['GoodsMstView']['set_goods_kbn'] == SET_GOODS){

      	  App::import("Model", "SetGoodsEstimateDtlTrn");
	      $set_goods_estimate = new SetGoodsEstimateDtlTrn();

          $set_goods_data = $set_goods_estimate->find('all',array('conditions'=>array("estimate_dtl_id"=>$estimate_dtl['id'])));
     	  for($i=0;$i < count($set_goods_data);$i++)
     	  {
     	  	   /* CERENMONYの場合以外はセット商品構成名で登録する(CEREMONYの場合はプラン名) */
     	  	   if($goods_view->GetCategoryIdByGoodsId($set_goods_data[$i]['SetGoodsEstimateDtlTrn']['goods_id']) != GC_WEDDING){
     	  	      $estimate_dtl['sales_goods_nm'] = $set_goods_data[$i]['SetGoodsEstimateDtlTrn']['sales_goods_nm'];
     	  	   }

     	  	   $estimate_dtl['goods_id'] = $set_goods_data[$i]['SetGoodsEstimateDtlTrn']['goods_id'];
     	       $ret = $this->_excuteToCreate($estimate_dtl,$customer_id,$username,$final_sheet_id);
     	       if($ret['result']==false){return $ret;}
     	  }
      }else{
         /* 単品商品の登録 */
  	     $ret = $this->_excuteToCreate($estimate_dtl,$customer_id,$username,$final_sheet_id);
  	     if($ret['result']==false){return $ret;}
      }
      return array('result'=>true);
  }

 /**
  *
  * ファイナルシートを新規作成の実処理
  * @param $estimate_dtl
  * @param $customer_id
  * @param $username
  * @throws Exception
  * @return 正常:TRUE
  *         異常：  　
  */
  function _excuteToCreate($estimate_dtl,$customer_id,$username,$final_sheet_id){

    	App::import("Model", "GoodsMstView");
	    $goods_view = new GoodsMstView();

    	$goods = $goods_view->findById($estimate_dtl['goods_id']);
     	$goods_data = $goods['GoodsMstView'];

        $array_params = array("estimate_dtl_id"   => $estimate_dtl['id'],
                              "menu"              => $estimate_dtl['sales_goods_nm'],
                              "num"               => $estimate_dtl['num'],
                              "content"           => $goods_data['goods_kbn_nm'],
                              "vendor_id"         => $goods_data['vendor_id'],
                              "vendor_nm"         => $goods_data['vendor_nm'],
                              "vendor_attend_nm"  => $goods_data['vendor_attend_nm'],
                              "vendor_phone_no"   => $goods_data['vendor_phone_no'],
                              "vendor_cell_no"    => $goods_data['vendor_cell_no'],
                              "vendor_email"      => $goods_data['vendor_email'],
                              "final_sheet_id"    => $final_sheet_id,
                              "customer_id"       => $customer_id,
                              "username"          => $username);
     	  // debug($array_params);

     	   switch ($goods['GoodsMstView']['goods_ctg_id']){
     	   	   case GC_WEDDING:       App::import("Model", "CeremonyService");
     	   	                          $ceremony = new CeremonyService();
     	   	                          $ret = $ceremony->createCeremonySheet($array_params);
     	   		                      break;
     	   	   case GC_CEREMONY_OPTION: App::import("Model", "CeremonyOptionService");
     	   	                            $ceremony_option = new CeremonyOptionService();
     	   	                            $ret = $ceremony_option->createCeremonyOptionSheet($array_params);
     	   		                      break;
     	   	   case GC_TRANS_CPL:     App::import("Model", "TransportationService");
     	   	                          $trans_cpl = new TransportationService();
     	   	                          $ret = $trans_cpl->createTransportationCplSheet($array_params);
     	   		                      break;
     	   	   case GC_TRANS_GST:     App::import("Model", "TransportationService");
     	   	                          $trans_gst = new TransportationService();
     	   	                          $ret = $trans_gst->createTransportationGuestSheet($array_params);
     	   		                      break;
     	   	   case GC_HAIR_MAKE_CPL: App::import("Model", "HairmakeService");
     	   	                          $hair_cpl = new HairmakeService();
     	   	                          $ret = $hair_cpl->createHairmakeCplSheet($array_params);
     	   		                      break;
     	   	   case GC_HAIR_MAKE_GST: App::import("Model", "HairmakeService");
     	   	                          $hair_gst = new HairmakeService();
     	   	                          $ret = $hair_gst->createHairmakeGuestSheet($array_params);
     	   		                      break;
     	   	   case GC_COORDINATOR:   App::import("Model", "CoordinatorService");
     	   	                          $coordinator = new CoordinatorService();
     	   	                          $ret = $coordinator->createCoordinatorSheet($array_params);
     	   		                      break;
     	   	   case GC_FLOWER_MAIN:   App::import("Model", "FlowerService");
     	   	                          $flower = new FlowerService();
     	   	                          $ret = $flower->createFlowerSheet($array_params,FC_MAIN);
     	   		                      break;
     	   	   case GC_FLOWER_RECEPTION:  App::import("Model", "FlowerService");
     	   	                              $flower = new FlowerService();
     	   	                              $ret = $flower->createFlowerSheet($array_params,FC_RECEPTION);
     	   		                          break;
     	   	   case GC_FLOWER_CEREMONY:  App::import("Model", "FlowerService");
     	   	                             $flower = new FlowerService();
     	   	                             $ret = $flower->createFlowerSheet($array_params,FC_CEREMONY);
     	   		                         break;
     	   	   case GC_PHOTO:         App::import("Model", "PhotographerService");
     	   	                          $photo = new PhotographerService();
     	   	                          $ret = $photo->createPhotographerSheet($array_params);
     	   		                      break;
     	   	   case GC_VIDEO:         App::import("Model", "VideographerService");
     	   	                          $video = new VideographerService();
     	   	                          $ret = $video->createVideographerSheet($array_params);
     	   		                      break;
     	       case GC_ENTERTAINMENT: App::import("Model", "EntertainmentService");
     	   	                          $entertainment = new EntertainmentService();
     	   	                          $ret = $entertainment->createEntertainmentSheet($array_params);
     	   		                      break;
     	   	   case GC_MINISTER:      App::import("Model", "MinisterService");
     	   	                          $minister = new MinisterService();
     	   	                          $ret = $minister->createMinisterSheet($array_params);
     	   		                      break;
     	   	   case GC_MC:            App::import("Model", "McService");
     	   	                          $mc = new McService();
     	   	                          $ret = $mc->createMcSheet($array_params);
     	   		                      break;
     	   	   case GC_HOUSE_WEDDING: App::import("Model", "HouseWeddingService");
     	   	                          $house = new HouseWeddingService();
     	   	                          $ret = $house->createHouseWeddingSheet($array_params);
     	   		                      break;
     	   	   case GC_RECEPTION:     App::import("Model", "ReceptionService");
     	   	                          $recep = new ReceptionService();
     	   	                          $ret = $recep->createReceptionSheet($array_params);
     	   		                      break;
     	   	   case GC_RECEPTION_TRANS: App::import("Model", "TransRecepService");
     	   	                          $trans_recep = new TransRecepService();
     	   	                          $ret = $trans_recep->createTransRecepSheet($array_params);
     	   		                      break;
     	   	   case GC_PARTY_OPTION:  App::import("Model", "PartyOptionService");
     	   	                          $party_option = new PartyOptionService();
     	   	                          $ret = $party_option->createPartyOptionSheet($array_params);
     	   		                      break;
     	   	   case GC_CAKE:          App::import("Model", "CakeService");
     	   	                          $cake = new CakeService();
     	   	                          $ret = $cake->createCakeSheet($array_params);
     	   		                      break;
     	   	   case GC_LINEN:         App::import("Model", "LinenService");
     	   	                          $linen = new LinenService();
     	   	                          $ret = $linen->createLinenSheet($array_params);
     	   		                      break;
     	   	   case GC_AV:            App::import("Model", "AvService");
     	   	                          $av = new AvService();
     	   	                          $ret = $av->createAvSheet($array_params);
     	   		                      break;
     	   	   case GC_ALBUM:         App::import("Model", "AlbumService");
     	   	                          $album = new AlbumService();
     	   	                          $ret = $album->createAlbumSheet($array_params);
     	   		                      break;
     	   	   case GC_PAPER:         App::import("Model", "PaperService");
     	   	                          $paper = new paperService();
     	   	                          $ret = $paper->createPaperSheet($array_params);
     	   		                      break;
     	   	   default:               return array('result'=>false,'message'=>"ファイナルシート作成に失敗しました。",'reason'=>"予期しない商品カテゴリID[{$goods['GoodsMstView']['goods_ctg_id']}]です。");
     	   }
        if($ret['result']==false){return $ret;}
        return array('result'=>true);
    }

/**
  *
  * 見積明細IDをキーにファイナルシートを更新する
  * @param $estimate_dtl_ids  更新した見積明細IDの配列
  * @param $customer_id
  * @param $username
  * @return 正常:TRUE
  *         異常：  　
  */
  function updateFinalSheetByEstimateDtlIds($estimate_dtl_ids,$customer_id,$username,$final_sheet_id){

	 App::import("Model", "EstimateDtlTrn");
	 $estimate_dtl = new EstimateDtlTrn();

       for($i =0;$i < count($estimate_dtl_ids);$i++)
       {
           $estimate_dtl_data = $estimate_dtl->findById($estimate_dtl_ids[$i]);

           /* セット商品の構成商品の情報は見積から変更できないので処理を飛ばす */
           if($estimate_dtl_data['EstimateDtlTrn']['set_goods_kbn'] == UNSET_GOODS){
           	$ret = $this->_updateMenu($estimate_dtl_data['EstimateDtlTrn'],$customer_id,$username,$final_sheet_id);
             if($ret['result']==false){return $ret;}
           }
       }
      return array('result'=>true);
  }

/**
  *
  * ファイナルシートのメニュー更新の実処理
  * @param $estimate_dtl
  * @param $customer_id
  * @param $username
  * @throws Exception
  * @return 正常:TRUE
  *         異常：  　
  */
  function _updateMenu($estimate_dtl,$customer_id,$username,$final_sheet_id){

    	App::import("Model", "GoodsMstView");
	    $goods_view = new GoodsMstView();

    	$goods = $goods_view->findById($estimate_dtl['goods_id']);
     	$goods_data = $goods['GoodsMstView'];

        $array_params = array("estimate_dtl_id"   => $estimate_dtl['id'],
                              "final_sheet_id"    => $final_sheet_id,
                              "menu"              => $estimate_dtl['sales_goods_nm'],
                              "num"               => $estimate_dtl['num'],
                              "content"           => $goods_data['goods_kbn_nm'],
                              "vendor_id"         => $goods_data['vendor_id'],
                              "vendor_nm"         => $goods_data['vendor_nm'],
                              "vendor_attend_nm"  => $goods_data['vendor_attend_nm'],
                              "vendor_phone_no"   => $goods_data['vendor_phone_no'],
                              "vendor_cell_no"    => $goods_data['vendor_cell_no'],
                              "vendor_email"      => $goods_data['vendor_email'],
                              "customer_id"       => $customer_id,
                              "username"          => $username);
     	  // debug($array_params);

     	   switch ($goods['GoodsMstView']['goods_ctg_id']){
     	   	   case GC_WEDDING:       App::import("Model", "CeremonyService");
     	   	                          $ceremony = new CeremonyService();
     	   	                          $ret = $ceremony->updateMenu($array_params);
     	   		                      break;
     	   	   case GC_CEREMONY_OPTION: App::import("Model", "CeremonyOptionService");
     	   	                            $ceremony_option = new CeremonyOptionService();
     	   	                            $ret = $ceremony_option->updateMenu($array_params);
     	   		                      break;
     	   	   case GC_HAIR_MAKE_CPL: App::import("Model", "HairmakeService");
     	   	                          $hair_cpl = new HairmakeService();
     	   	                          $ret = $hair_cpl->updateHairmakeCplMenu($array_params);
     	   		                      break;
     	   	   case GC_HAIR_MAKE_GST: App::import("Model", "HairmakeService");
     	   	                          $hair_gst = new HairmakeService();
     	   	                          $ret = $hair_gst->updateHairmakeGuestMenu($array_params);
     	   		                      break;
     	       case GC_TRANS_CPL:     App::import("Model", "TransportationService");
     	   	                          $trans = new TransportationService();
     	   	                          $ret = $trans->updateTransportationCplMenu($array_params);
     	   		                      break;
     	   	   case GC_TRANS_GST:     App::import("Model", "TransportationService");
     	   	                          $trans = new TransportationService();
     	   	                          $ret = $trans->updateTransportationGuestMenu($array_params);
     	   		                      break;
     	   	   case GC_COORDINATOR:   App::import("Model", "CoordinatorService");
     	   	                          $coordinator = new CoordinatorService();
     	   	                          $ret = $coordinator->updateMenu($array_params);
     	   		                      break;
     	   	   case GC_FLOWER_MAIN:
     	   	   case GC_FLOWER_RECEPTION:
     	   	   case GC_FLOWER_CEREMONY:  App::import("Model", "FlowerService");
     	   	                             $flower = new FlowerService();
     	   	                             $ret = $flower->updateMenu($array_params);
     	   		                         break;
     	   	   case GC_PHOTO:         App::import("Model", "PhotographerService");
     	   	                          $photo = new PhotographerService();
     	   	                          $ret = $photo->updateMenu($array_params);
     	   		                      break;
     	   	   case GC_VIDEO:         App::import("Model", "VideographerService");
     	   	                          $video = new VideographerService();
     	   	                          $ret = $video->updateMenu($array_params);
     	   		                      break;
     	       case GC_ENTERTAINMENT: App::import("Model", "EntertainmentService");
     	   	                          $entertainment = new EntertainmentService();
     	   	                          $ret = $entertainment->updateMenu($array_params);
     	   		                      break;
     	   	   case GC_MINISTER:      App::import("Model", "MinisterService");
     	   	                          $minister = new MinisterService();
     	   	                          $ret = $minister->updateMenu($array_params);
     	   		                      break;
     	   	   case GC_MC:            App::import("Model", "McService");
     	   	                          $mc = new McService();
     	   	                          $ret = $mc->updateMenu($array_params);
     	   		                      break;
     	   	   case GC_HOUSE_WEDDING: App::import("Model", "HouseWeddingService");
     	   	                          $house = new HouseWeddingService();
     	   	                          $ret =  $house->updateMenu($array_params);
     	   		                      break;
     	   	   case GC_RECEPTION:     App::import("Model", "ReceptionService");
     	   	                          $recep = new ReceptionService();
     	   	                          $ret = $recep->updateMenu($array_params);
     	   		                      break;
     	   	   case GC_RECEPTION_TRANS: App::import("Model", "TransRecepService");
     	   	                          $trans_recep = new TransRecepService();
     	   	                          $ret = $trans_recep->updateMenu($array_params);
     	   		                      break;
     	   	   case GC_PARTY_OPTION:  App::import("Model", "PartyOptionService");
     	   	                          $party_option = new PartyOptionService();
     	   	                          $ret = $party_option->updateMenu($array_params);
     	   		                      break;
     	   	   case GC_CAKE:          App::import("Model", "CakeService");
     	   	                          $cake = new CakeService();
     	   	                          $ret = $cake->updateMenu($array_params);
     	   		                      break;
     	   	   case GC_LINEN:         App::import("Model", "LinenService");
     	   	                          $linen = new LinenService();
     	   	                          $ret = $linen->updateMenu($array_params);
     	   		                      break;
     	   	   case GC_AV:            App::import("Model", "AvService");
     	   	                          $av = new AvService();
     	   	                          $ret = $av->updateMenu($array_params);
     	   		                      break;
     	   	   case GC_ALBUM:         App::import("Model", "AlbumService");
     	   	                          $album = new AlbumService();
     	   	                          $ret = $album->updateMenu($array_params);
     	   		                      break;
     	   	   case GC_PAPER:         App::import("Model", "PaperService");
     	   	                          $paper = new paperService();
     	   	                          $ret = $paper->updateMenu($array_params);
     	   		                      break;
     	   	   default:               return array('result'=>false,'message'=>"ファイナルシートのメニュー更新に失敗しました。",'reason'=>"予期しない商品カテゴリID[{$goods['GoodsMstView']['goods_ctg_id']}]です。");
     	   }
        if($ret['result']==false){return $ret;}
     	return array('result'=>true);
    }

 /**
  *
  * 全てのファイナルシート関連テーブルを削除する
  * @param $customer_id
  * @return 正常:TRUE
  *         異常：  　
  */
  function deleteAllFinalSheet($customer_id){

	  	  /* ファイナルシートヘッダの削除 */
  	      App::import("Model", "FinalSheetTrn");
     	  $final = new FinalSheetTrn();
     	  $ret = $final->DeleteAllBy($customer_id);
     	  if($ret['result']==false){return $ret;}

	      App::import("Model", "CeremonyService");
     	  $ceremony = new CeremonyService();
     	  $ret = $ceremony->deleteCeremonySheet($customer_id);
     	  if($ret['result']==false){return $ret;}

     	  App::import("Model", "CeremonyOptionService");
     	  $ceremony_option = new CeremonyOptionService();
     	  $ret = $ceremony_option->deleteCeremonyOptionSheet($customer_id);
     	  if($ret['result']==false){return $ret;}

     	  App::import("Model", "HairmakeService");
     	  $hair = new HairmakeService();
          $ret = $hair->deleteHairmakeCplSheet($customer_id);
          if($ret['result']==false){return $ret;}
          $ret = $hair->deleteHairmakeGuestSheet($customer_id);
          if($ret['result']==false){return $ret;}

     	  App::import("Model", "TransportationService");
     	  $trans = new TransportationService();
          $ret = $trans->deleteTransportationCplSheet($customer_id);
          if($ret['result']==false){return $ret;}
          $ret = $trans->deleteTransportationGuestSheet($customer_id);
     	  if($ret['result']==false){return $ret;}

     	  App::import("Model", "CoordinatorService");
     	  $coordinator = new CoordinatorService();
          $ret = $coordinator->deleteCoordinatorSheet($customer_id);
     	  if($ret['result']==false){return $ret;}

     	  App::import("Model", "FlowerService");
     	  $flower = new FlowerService();
          $ret = $flower->deleteFlowerSheet($customer_id);
     	  if($ret['result']==false){return $ret;}

     	  App::import("Model", "PhotographerService");
     	  $photo = new PhotographerService();
     	  $ret = $photo->deletePhotographerSheet($customer_id);
     	  if($ret['result']==false){return $ret;}

     	  App::import("Model", "VideographerService");
     	  $video = new VideographerService();
     	  $ret = $video->deleteVideographerSheet($customer_id);
     	  if($ret['result']==false){return $ret;}

     	  App::import("Model", "EntertainmentService");
     	  $entertainment = new EntertainmentService();
     	  $ret = $entertainment->deleteEntertainmentSheet($customer_id);
     	  if($ret['result']==false){return $ret;}

     	  App::import("Model", "MinisterService");
     	  $minister = new MinisterService();
     	  $ret = $minister->deleteMinisterSheet($customer_id);
     	  if($ret['result']==false){return $ret;}

     	  App::import("Model", "McService");
     	  $mc = new McService();
          $ret = $mc->deleteMcSheet($customer_id);
     	  if($ret['result']==false){return $ret;}

     	  App::import("Model", "HouseWeddingService");
     	  $house = new HouseWeddingService();
          $ret = $house->deleteHouseWeddingSheet($customer_id);
     	  if($ret['result']==false){return $ret;}

     	  App::import("Model", "ReceptionService");
     	  $recep = new ReceptionService();
          $ret = $recep->deleteReceptionSheet($customer_id);
     	  if($ret['result']==false){return $ret;}

     	  App::import("Model", "TransRecepService");
     	  $trans_recep = new TransRecepService();
          $ret = $trans_recep->deleteTransRecepSheet($customer_id);
     	  if($ret['result']==false){return $ret;}

     	  App::import("Model", "PartyOptionService");
     	  $party_option = new PartyOptionService();
          $ret = $party_option->deletePartyOptionSheet($customer_id);
     	  if($ret['result']==false){return $ret;}

     	  App::import("Model", "CakeService");
     	  $cake = new CakeService();
          $ret = $cake->deleteCakeSheet($customer_id);
     	  if($ret['result']==false){return $ret;}

     	  App::import("Model", "LinenService");
     	  $linen = new LinenService();
     	  $ret = $linen->deleteLinenSheet($customer_id);
     	  if($ret['result']==false){return $ret;}

          App::import("Model", "AvService");
     	  $av = new AvService();
	      $ret = $av->deleteAvSheet($customer_id);
	      if($ret['result']==false){return $ret;}

	      App::import("Model", "AlbumService");
     	  $album = new AlbumService();
	      $ret = $album->deleteAlbumSheet($customer_id);
     	  if($ret['result']==false){return $ret;}

	      /*
	      App::import("Model", "TravelService");
          $travel = new TravelService();
          $travel->deleteTravelSheet($customer_id);
          */

          App::import("Model", "PaperService");
          $paper = new PaperService();
          $ret = $paper->deletePaperSheet($customer_id);
          if($ret['result']==false){return $ret;}

      return array('result'=>true);
    }



 /**
  *
  * フィナルシートを更新する
  * @param $category_id
  * @param $data
  * @param $username
  * @throws Exception
  * @return 正常:TRUE
  *         異常：  　
  */
  function updateFinalSheet($category_id,$data,$username){

    $tr = ClassRegistry::init('TransactionManager');
	$tr->begin();

     	   switch ($category_id){
     	   	    case GC_BASIC_INFO:   App::import("Model", "ContractTrn");
     	   	                          $contract = new ContractTrn();
     	   	                          $ret = $contract->updateForFinalSheet($data['ContractTrn'], $username);
     	   		                      break;

	           case GC_PERSONAL_INFO: App::import("Model", "CustomerMst");
     	   	                          $customer = new CustomerMst();
     	   	                          $ret = $customer->updateForFinalSheet($data['CustomerMst'], $username);
     	   		                      break;
     	   	   case GC_WEDDING:       App::import("Model", "CeremonyService");
     	   	                          $ceremony = new CeremonyService();
     	   	                          $ret = $ceremony->saveAll($data, $username);
     	   		                      break;
     	   	   case GC_CEREMONY_OPTION: App::import("Model", "CeremonyOptionService");
     	   	                            $ceremony_option = new CeremonyOptionService();
     	   	                            $ret = $ceremony_option->saveAll($data, $username);
     	   		                        break;
     	   	   case GC_TRAVEL:        App::import("Model", "TravelService");
     	   	                          $travel = new TravelService();
     	   	                          $ret = $travel->saveAll($data, $username);
     	   		                      break;
     	   	   case GC_HAIR_MAKE:     App::import("Model", "HairmakeService");
     	   	                          $hair = new HairmakeService();
     	   	                          $ret = $hair->saveAll($data, $username);
     	   		                      break;
     	       case GC_TRANS_CPL:
     	   	   case GC_TRANS_GST:      App::import("Model", "TransportationService");
     	   	                           $trans = new TransportationService();
     	   	                           $ret = $trans->saveAll($data, $username);
     	   		                       break;
     	   	   case GC_COORDINATOR:   App::import("Model", "CoordinatorService");
     	   	                          $coordinator = new CoordinatorService();
     	   	                          $ret = $coordinator->saveAll($data, $username);
     	   		                      break;
     	   	   case GC_FLOWER:        App::import("Model", "FlowerService");
     	   	                          $flower = new FlowerService();
     	   	                          $ret = $flower->saveAll($data, $username);
     	   		                      break;
     	   	   case GC_PHOTO:         App::import("Model", "PhotographerService");
     	   	                          $photo = new PhotographerService();
     	   	                          $ret = $photo->saveAll($data, $username);
     	   		                      break;
     	   	   case GC_VIDEO:         App::import("Model", "VideographerService");
     	   	                          $video = new VideographerService();
     	   	                          $ret = $video->saveAll($data, $username);
     	   		                      break;
     	       case GC_ENTERTAINMENT: App::import("Model", "EntertainmentService");
     	   	                          $entertainment = new EntertainmentService();
     	   	                          $ret = $entertainment->saveAll($data, $username);
     	   		                      break;
     	   	   case GC_MINISTER:      App::import("Model", "MinisterService");
     	   	                          $minister = new MinisterService();
     	   	                          $ret = $minister->saveAll($data, $username);
     	   		                      break;
     	   	   case GC_MC:            App::import("Model", "McService");
     	   	                          $mc = new McService();
     	   	                          $ret = $mc->saveAll($data, $username);
     	   		                      break;
     	   	   case GC_HOUSE_WEDDING: App::import("Model", "HouseWeddingService");
     	   	                          $house = new HouseWeddingService();
     	   	                          $ret = $house->saveAll($data, $username);
     	   		                      break;
     	   	   case GC_RECEPTION:     App::import("Model", "ReceptionService");
     	   	                          $recep = new ReceptionService();
     	   	                          $ret = $recep->saveAll($data, $username);
     	   		                      break;
     	   	   case GC_RECEPTION_TRANS: App::import("Model", "TransRecepService");
     	   	                          $trans_recep = new TransRecepService();
     	   	                          $ret = $trans_recep->saveAll($data, $username);
     	   		                      break;
     	   	   case GC_PARTY_OPTION:  App::import("Model", "PartyOptionService");
     	   	                          $party_option = new PartyOptionService();
     	   	                          $ret = $party_option->saveAll($data, $username);
     	   		                      break;
     	   	   case GC_CAKE:          App::import("Model", "CakeService");
     	   	                          $cake = new CakeService();
     	   	                          $ret = $cake->saveAll($data, $username);
     	   		                      break;
     	   	   case GC_LINEN:         App::import("Model", "LinenService");
     	   	                          $linen = new LinenService();
     	   	                          $ret = $linen->saveAll($data, $username);
     	   		                      break;
     	   	   case GC_AV:            App::import("Model", "AvService");
     	   	                          $av = new AvService();
     	   	                          $ret = $av->saveAll($data, $username);
     	   		                      break;
     	   	   case GC_ALBUM:      	  App::import("Model", "AlbumService");
     	   	                          $album = new AlbumService();
     	   	                          $ret = $album->saveAll($data, $username);
     	   		                      break;
     	   	   case GC_PAPER:         App::import("Model", "PaperService");
     	   	                          $paper = new paperService();
     	   	                          $ret = $paper->saveAll($data, $username);
     	   		                      break;
     	   	   default:               return array('result'=>false,'message'=>"フィナルシート更新に失敗しました。",'reason'=>"予期しない商品カテゴリID[{$category_id}]です。");
     	   }
      if($ret['result']==false){return $ret;}
      $tr->commit();
      return array('result'=>true);
  }

  /**
   *
   * ファイナルシートのスナップショット
   * @param unknown_type $final_sheet_id
   * @param unknown_type $username
   */
  function snapshotFinalSheet($final_sheet_id,$username){

    $tr = ClassRegistry::init('TransactionManager');
	$tr->begin();

	/* ファイナルシートヘッダ更新 */
	App::import("Model", "FinalSheetTrn");
    $final = new FinalSheetTrn();
    $ret = $final->CopyBy($final_sheet_id, $username);
	if($ret['result']==false){return $ret;}
	$new_final_sheet_id = $ret["newID"];

	/* アルバムシートの複製 */
	App::import("Model", "AlbumService");
    $album = new AlbumService();
    $ret = $album->copy($final_sheet_id , $new_final_sheet_id, $username);
    if($ret['result']==false){return $ret;}

    /* ケーキシートの複製 */
	App::import("Model", "CakeService");
    $cake = new CakeService();
    $ret = $cake->copy($final_sheet_id , $new_final_sheet_id, $username);
    if($ret['result']==false){return $ret;}

    /*
     	   	    case GC_BASIC_INFO:   App::import("Model", "ContractTrn");
     	   	                          $contract = new ContractTrn();
     	   	                          $ret = $contract->updateForFinalSheet($data['ContractTrn'], $username);
     	   		                      break;

	           case GC_PERSONAL_INFO: App::import("Model", "CustomerMst");
     	   	                          $customer = new CustomerMst();
     	   	                          $ret = $customer->updateForFinalSheet($data['CustomerMst'], $username);
     	   		                      break;
   */

     /* セレモニーシートの複製 */
     App::import("Model", "CeremonyService");
     $ceremony = new CeremonyService();
     $ret = $ceremony->copy($final_sheet_id , $new_final_sheet_id, $username);
     if($ret['result']==false){return $ret;}

     /* セレモニーオプションシートの複製 */
     App::import("Model", "CeremonyOptionService");
     $ceremony_option = new CeremonyOptionService();
     $ret = $ceremony_option->copy($final_sheet_id , $new_final_sheet_id, $username);
     if($ret['result']==false){return $ret;}

     /* トラベルシートの複製 */
     App::import("Model", "TravelService");
     $travel = new TravelService();
     $ret = $travel->copy($final_sheet_id , $new_final_sheet_id, $username);
     if($ret['result']==false){return $ret;}

     /* ヘアメイクシートの複製 */
     App::import("Model", "HairmakeService");
     $hair = new HairmakeService();
     $ret = $hair->copyHairmakeCpl($final_sheet_id , $new_final_sheet_id, $username);
     if($ret['result']==false){return $ret;}

     $ret = $hair->copyHairmakeGuest($final_sheet_id , $new_final_sheet_id, $username);
     if($ret['result']==false){return $ret;}

     /* トランスポーテーションシートの複製 */
     App::import("Model", "TransportationService");
     $trans = new TransportationService();
     $ret = $trans->copyTransCpl($final_sheet_id , $new_final_sheet_id, $username);
     if($ret['result']==false){return $ret;}

     $ret = $trans->copyTransGuest($final_sheet_id , $new_final_sheet_id, $username);
     if($ret['result']==false){return $ret;}

     /* コーディネーターシートの複製 */
     App::import("Model", "CoordinatorService");
     $coordinator = new CoordinatorService();
     $ret = $coordinator->copy($final_sheet_id , $new_final_sheet_id, $username);
     if($ret['result']==false){return $ret;}

     /* フラワーシートの複製 */
     App::import("Model", "FlowerService");
     $flower = new FlowerService();
     $ret = $flower->copy($final_sheet_id , $new_final_sheet_id, $username);
     if($ret['result']==false){return $ret;}

     /*フォトシートの複製 */
     App::import("Model", "PhotographerService");
     $photo = new PhotographerService();
     $ret = $photo->copy($final_sheet_id , $new_final_sheet_id, $username);
     if($ret['result']==false){return $ret;}

     /* ビデオシートの複製 */
     App::import("Model", "VideographerService");
     $video = new VideographerService();
     $ret = $video->copy($final_sheet_id , $new_final_sheet_id, $username);
     if($ret['result']==false){return $ret;}

     /* エンターテインメントシートの複製 */
     App::import("Model", "EntertainmentService");
     $entertainment = new EntertainmentService();
     $ret = $entertainment->copy($final_sheet_id , $new_final_sheet_id, $username);
     if($ret['result']==false){return $ret;}

     /* ミニスターシートの複製 */
     App::import("Model", "MinisterService");
     $minister = new MinisterService();
     $ret = $minister->copy($final_sheet_id , $new_final_sheet_id, $username);
     if($ret['result']==false){return $ret;}

     /* MCシートの複製 */
     App::import("Model", "McService");
     $mc = new McService();
     $ret = $mc->copy($final_sheet_id , $new_final_sheet_id, $username);
     if($ret['result']==false){return $ret;}

     /* ハウスウェディングシートの複製 */
     App::import("Model", "HouseWeddingService");
     $house = new HouseWeddingService();
     $ret = $house->copy($final_sheet_id , $new_final_sheet_id, $username);
     if($ret['result']==false){return $ret;}

     /* レセプションシートの複製 */
     App::import("Model", "ReceptionService");
     $recep = new ReceptionService();
     $ret = $recep->copy($final_sheet_id , $new_final_sheet_id, $username);
     if($ret['result']==false){return $ret;}

     /* トランスレセプションシートの複製 */
     App::import("Model", "TransRecepService");
     $trans_recep = new TransRecepService();
     $ret = $trans_recep->copy($final_sheet_id , $new_final_sheet_id, $username);
     if($ret['result']==false){return $ret;}

     /* パーティオプションシートの複製 */
     App::import("Model", "PartyOptionService");
     $party_option = new PartyOptionService();
     $ret = $party_option->copy($final_sheet_id , $new_final_sheet_id, $username);
     if($ret['result']==false){return $ret;}

     /* ケーキシートの複製 */
     App::import("Model", "CakeService");
     $cake = new CakeService();
     $ret = $cake->copy($final_sheet_id , $new_final_sheet_id, $username);
     if($ret['result']==false){return $ret;}

     /* リネンシートの複製 */
     App::import("Model", "LinenService");
     $linen = new LinenService();
     $ret = $linen->copy($final_sheet_id , $new_final_sheet_id, $username);
     if($ret['result']==false){return $ret;}

     /* AVシートの複製 */
     App::import("Model", "AvService");
     $av = new AvService();
     $ret = $av->copy($final_sheet_id , $new_final_sheet_id, $username);
     if($ret['result']==false){return $ret;}

     /* ペーパーシートの複製 */
     App::import("Model", "PaperService");
     $paper = new paperService();
     $ret = $paper->copy($final_sheet_id , $new_final_sheet_id, $username);
     if($ret['result']==false){return $ret;}

      $tr->commit();
      return array('result'=>true);
  }

  /**
   *
   * ヘッダと詳細形式のファイナルシートのテーブルでヘッダのみのカテゴリは全て削除する
   * @param $goods_ids
   * @param $customer_id
   */
  function deleteFinalSheetIfHeaderOnly($goods_ids,$customer_id){

    	App::import("Model", "GoodsMstView");
	    $goods_view = new GoodsMstView();

	 for($i=0;$i < count($goods_ids);$i++)
	 {
    	$goods = $goods_view->findById($goods_ids[$i]);
     	$goods_data = $goods['GoodsMstView'];

     	   switch ($goods['GoodsMstView']['goods_ctg_id']){
     	   	   case GC_WEDDING:      $ret['result'] = true;
     	   	                         break;
     	   	   case GC_CEREMONY_OPTION: App::import("Model", "CeremonyOptionService");
     	   	                            $ceremony_option = new CeremonyOptionService();
     	   	                            $ret = $ceremony_option->deleteHeaderIfNoSubTableData($customer_id);
     	   		                      break;
     	   	   case GC_HAIR_MAKE_CPL: App::import("Model", "HairmakeService");
     	   	                          $hair_cpl = new HairmakeService();
     	   	                          $ret = $hair_cpl->deleteHairmakeCplIfNoSubTableData($customer_id);
     	   		                      break;
     	   	   case GC_HAIR_MAKE_GST: App::import("Model", "HairmakeService");
     	   	                          $hair_gst = new HairmakeService();
     	   	                          $ret = $hair_gst->deleteHairmakeGuestIfNoSubTableData($customer_id);
     	   		                      break;
     	       case GC_TRANS_CPL:     App::import("Model", "TransportationService");
     	   	                          $trans = new TransportationService();
     	   	                          $ret = $trans->deleteTransCplIfNoSubTableData($customer_id);
     	   		                      break;
     	   	   case GC_TRANS_GST:     App::import("Model", "TransportationService");
     	   	                          $trans = new TransportationService();
     	   	                          $ret = $trans->deleteTransGuestIfNoSubTableData($customer_id);
     	   		                      break;
     	   	   case GC_COORDINATOR:   App::import("Model", "CoordinatorService");
     	   	                          $coordinator = new CoordinatorService();
     	   	                          $ret = $coordinator->deleteHeaderIfNoSubTableData($customer_id);
     	   		                      break;
     	   	   case GC_FLOWER_MAIN:
     	   	   case GC_FLOWER_RECEPTION:
     	   	   case GC_FLOWER_CEREMONY:  App::import("Model", "FlowerService");
     	   	                             $flower = new FlowerService();
     	   	                             $ret = $flower->deleteHeaderIfNoSubTableData($customer_id);
     	   		                         break;
     	   	   case GC_PHOTO:         App::import("Model", "PhotographerService");
     	   	                          $photo = new PhotographerService();
     	   	                          $ret = $photo->deleteHeaderIfNoSubTableData($customer_id);
     	   		                      break;
     	   	   case GC_VIDEO:         App::import("Model", "VideographerService");
     	   	                          $video = new VideographerService();
     	   	                          $ret = $video->deleteHeaderIfNoSubTableData($customer_id);
     	   		                      break;
     	       case GC_ENTERTAINMENT: App::import("Model", "EntertainmentService");
     	   	                          $entertainment = new EntertainmentService();
     	   	                          $ret = $entertainment->deleteHeaderIfNoSubTableData($customer_id);
     	   		                      break;
     	   	   case GC_MINISTER:
     	   	   case GC_MC:
     	   	   case GC_HOUSE_WEDDING: $ret['result'] = true;
     	   	   	                      break;
     	   	   case GC_RECEPTION:     App::import("Model", "ReceptionService");
     	   	                          $recep = new ReceptionService();
     	   	                          $ret = $recep->deleteHeaderIfNoSubTableData($customer_id);
     	   		                      break;
     	   	   case GC_RECEPTION_TRANS: App::import("Model", "TransRecepService");
     	   	                          $trans_recep = new TransRecepService();
     	   	                          $ret = $trans_recep->deleteHeaderIfNoSubTableData($customer_id);
     	   		                      break;
     	   	   case GC_PARTY_OPTION:  App::import("Model", "PartyOptionService");
     	   	                          $party_option = new PartyOptionService();
     	   	                          $ret = $party_option->deleteHeaderIfNoSubTableData($customer_id);
     	   		                      break;
     	   	   case GC_CAKE:          App::import("Model", "CakeService");
     	   	                          $cake = new CakeService();
     	   	                          $ret = $cake->deleteHeaderIfNoSubTableData($customer_id);
     	   		                      break;
     	   	   case GC_LINEN:         App::import("Model", "LinenService");
     	   	                          $linen = new LinenService();
     	   	                          $ret = $linen->deleteHeaderIfNoSubTableData($customer_id);
     	   		                      break;
     	   	   case GC_AV:            App::import("Model", "AvService");
     	   	                          $av = new AvService();
     	   	                          $ret = $av->deleteHeaderIfNoSubTableData($customer_id);
     	   		                      break;
     	   	   case GC_ALBUM:         App::import("Model", "AlbumService");
     	   	                          $album = new AlbumService();
     	   	                          $ret = $album->deleteHeaderIfNoSubTableData($customer_id);
     	   		                      break;
     	   	   case GC_PAPER:         App::import("Model", "PaperService");
     	   	                          $paper = new paperService();
     	   	                          $ret = $paper->deleteHeaderIfNoSubTableData($customer_id);
     	   		                      break;
     	   	   default:               return array('result'=>false,'message'=>"ファイナルシート削除に失敗しました。",'reason'=>"予期しない商品カテゴリID[{$goods_data['GoodsMstView']['goods_ctg_id']}]です。");
     	   }
     	   if($ret['result']==false){return $ret;}
	 }
	   return array('result'=>true);
    }

 /**
  *
  * 複数カテゴリ作成が禁止されているカテゴリが複数作成されているかチェック
  * @param $estimate_dtl
  * @throws Exception
  * @return 正常:TRUE
  *         異常：
  */
  function checkIfDuplicatedCategory($estimate_dtl){

  	 $categories = array("wedding"=>0,"house_wedding"=>0,"reception"=>0);
  	 App::import("Model", "GoodsMstView");
	 $goods_view = new GoodsMstView();

     for($i=1;$i <= count($estimate_dtl);$i++){
        $goods = $goods_view->findById($estimate_dtl[$i]['goods_id']);
     	$goods_data = $goods['GoodsMstView'];

     	switch ($goods['GoodsMstView']['goods_ctg_id']){
     	   	   case GC_WEDDING:      $categories["wedding"] = $categories["wedding"] + 1;
     	   	                         if($categories["wedding"] > 1){
     	   	                         	 return array('result'=>false,'message'=>"ファイナルシート作成に失敗しました。",'reason'=>"商品カテゴリ[Wedding]が重複しています。");
     	   	                         }
     	   		                     break;
     	   }
     }
      return array('result'=>true);
  }

  /**
   *
   * ベンダーリストを取得する
   * @param $customer_id
   */
  function getVendorList($final_sheet_id){

     $arr= null;
     //GC_Travel:
  	 App::import("Model", "TravelService");
     $travel = new TravelService();
     $ret = $travel->getVendorList($final_sheet_id);
   	 if($ret != null){
   	 	for($i=0;$i < count($ret);$i++){
   	 	   $arr[] = $ret[$i];
   	 	}
   	 }

  	 //GC_WEDDING:
  	 App::import("Model", "CeremonyService");
     $ceremony = new CeremonyService();
     $ret = $ceremony->getVendorList($final_sheet_id);
   	 if($ret != null){
   	 	for($i=0;$i < count($ret);$i++){
   	 	   $arr[] = $ret[$i];
   	 	}
   	 }

     //GC_CEREMONY_OPTION:
     App::import("Model", "CeremonyOptionService");
     $ceremony_option = new CeremonyOptionService();
     $ret = $ceremony_option->getVendorList($final_sheet_id);
   	 if($ret != null){
   	 	for($i=0;$i < count($ret);$i++){
   	 	   $arr[] = $ret[$i];
   	 	}
   	 }

     //GC_HAIR_MAKE_CPL:
     App::import("Model", "HairmakeService");
     $hair = new HairmakeService();
     $ret = $hair->getVendorListCpl($final_sheet_id);
   	 if($ret != null){
   	 	for($i=0;$i < count($ret);$i++){
   	 	   $arr[] = $ret[$i];
   	 	}
   	 }

     //GC_HAIR_MAKE_GST:
     $ret = $hair->getVendorListGst($final_sheet_id);
   	 if($ret != null){
   	 	for($i=0;$i < count($ret);$i++){
   	 	   $arr[] = $ret[$i];
   	 	}
   	 }

     //GC_TRANS_CPL:
     App::import("Model", "TransportationService");
     $trans = new TransportationService();
     $ret = $trans->getVendorListCpl($final_sheet_id);
   	 if($ret != null){
   	 	for($i=0;$i < count($ret);$i++){
   	 	   $arr[] = $ret[$i];
   	 	}
   	 }

     //GC_TRANS_GST:
     $ret = $trans->getVendorListGst($final_sheet_id);
   	 if($ret != null){
   	 	for($i=0;$i < count($ret);$i++){
   	 	   $arr[] = $ret[$i];
   	 	}
   	 }

     //GC_COORDINATOR:
     App::import("Model", "CoordinatorService");
     $coordinator = new CoordinatorService();
     $ret = $coordinator->getVendorList($final_sheet_id);
   	 if($ret != null){
   	 	for($i=0;$i < count($ret);$i++){
   	 	   $arr[] = $ret[$i];
   	 	}
   	 }

     //GC_FLOWER_MAIN:
     App::import("Model", "FlowerService");
     $flower = new FlowerService();
     $ret = $flower->getVendorList($final_sheet_id);
   	 if($ret != null){
   	 	for($i=0;$i < count($ret);$i++){
   	 	   $arr[] = $ret[$i];
   	 	}
   	 }


     //GC_PHOTO:
     App::import("Model", "PhotographerService");
     $photo = new PhotographerService();
     $ret = $photo->getVendorList($final_sheet_id);
   	 if($ret != null){
   	 	for($i=0;$i < count($ret);$i++){
   	 	   $arr[] = $ret[$i];
   	 	}
   	 }

     //GC_VIDEO:
     App::import("Model", "VideographerService");
     $video = new VideographerService();
     $ret = $video->getVendorList($final_sheet_id);
   	 if($ret != null){
   	 	for($i=0;$i < count($ret);$i++){
   	 	   $arr[] = $ret[$i];
   	 	}
   	 }

     //GC_ENTERTAINMENT:
     App::import("Model", "EntertainmentService");
     $entertainment = new EntertainmentService();
     $ret = $entertainment->getVendorList($final_sheet_id);
   	 if($ret != null){
   	 	for($i=0;$i < count($ret);$i++){
   	 	   $arr[] = $ret[$i];
   	 	}
   	 }

     //GC_MINISTER:
     App::import("Model", "MinisterService");
     $minister = new MinisterService();
     $ret = $minister->getVendorList($final_sheet_id);
   	 if($ret != null){
   	 	for($i=0;$i < count($ret);$i++){
   	 	   $arr[] = $ret[$i];
   	 	}
   	 }

     //GC_MC:
     App::import("Model", "McService");
     $mc = new McService();
     $ret = $mc->getVendorList($final_sheet_id);
   	 if($ret != null){
   	 	for($i=0;$i < count($ret);$i++){
   	 	   $arr[] = $ret[$i];
   	 	}
   	 }

     //GC_HOUSE_WEDDING:
     App::import("Model", "HouseWeddingService");
     $house = new HouseWeddingService();
     $ret = $house->getVendorList($final_sheet_id);
   	 if($ret != null){
   	 	for($i=0;$i < count($ret);$i++){
   	 	   $arr[] = $ret[$i];
   	 	}
   	 }

     //GC_RECEPTION:
     App::import("Model", "ReceptionService");
     $recep = new ReceptionService();
     $ret = $recep->getVendorList($final_sheet_id);
   	 if($ret != null){
   	 	for($i=0;$i < count($ret);$i++){
   	 	   $arr[] = $ret[$i];
   	 	}
   	 }

     //GC_RECEPTION_TRANS:
     App::import("Model", "TransRecepService");
     $trans_recep = new TransRecepService();
     $ret = $trans_recep->getVendorList($final_sheet_id);
   	 if($ret != null){
   	 	for($i=0;$i < count($ret);$i++){
   	 	   $arr[] = $ret[$i];
   	 	}
   	 }

     //GC_PARTY_OPTION:
     App::import("Model", "PartyOptionService");
     $party_option = new PartyOptionService();
     $ret = $party_option->getVendorList($final_sheet_id);
   	 if($ret != null){
   	 	for($i=0;$i < count($ret);$i++){
   	 	   $arr[] = $ret[$i];
   	 	}
   	 }

     //GC_CAKE:
     App::import("Model", "CakeService");
     $cake = new CakeService();
     $ret = $cake->getVendorList($final_sheet_id);
   	 if($ret != null){
   	 	for($i=0;$i < count($ret);$i++){
   	 	   $arr[] = $ret[$i];
   	 	}
   	 }

     //GC_LINEN:
     App::import("Model", "LinenService");
     $linen = new LinenService();
     $ret = $linen->getVendorList($final_sheet_id);
   	 if($ret != null){
   	 	for($i=0;$i < count($ret);$i++){
   	 	   $arr[] = $ret[$i];
   	 	}
   	 }

     //GC_AV:
     App::import("Model", "AvService");
     $av = new AvService();
     $ret = $av->getVendorList($final_sheet_id);
   	 if($ret != null){
   	 	for($i=0;$i < count($ret);$i++){
   	 	   $arr[] = $ret[$i];
   	 	}
   	 }

     //GC_ALBUM:
     App::import("Model", "AlbumService");
     $album = new AlbumService();
     $ret = $album->getVendorList($final_sheet_id);
   	 if($ret != null){
   	 	for($i=0;$i < count($ret);$i++){
   	 	   $arr[] = $ret[$i];
   	 	}
   	 }

     //GC_PAPER:
     App::import("Model", "PaperService");
     $paper = new paperService();
     $ret = $paper->getVendorList($final_sheet_id);
   	 if($ret != null){
   	 	for($i=0;$i < count($ret);$i++){
   	 	   $arr[] = $ret[$i];
   	 	}
   	 }

    return $arr;
  }

  /**
   *
   * 最新のファイナルシートIDを取得する
   * @param $customer_id
   */
  function GetLatestFinalSheetIdBy($customer_id){

    App::import("Model", "FinalSheetTrn");
    $final = new FinalSheetTrn();
    return $final->GetLatestFinalSheetIdBy($customer_id);
  }

  /**
   * 挙式会場名を取得
   * @param unknown $customer_id
   * @return NULL
   */
  function GetWeddingPlace($customer_id){

  	App::import("Model", "CeremonyTrn");
  	$ceremony = new CeremonyTrn();

  	App::import("Model", "EstimateDtlTrn");
  	$estiamte = new EstimateDtlTrn();

  	$data = $ceremony->find('all',array('fields'=>array('estimate_dtl_id'),'conditions'=>array('customer_id'=>$customer_id)));
  	if(count($data) == 0){ return  null; }

  	$data = $estiamte->find('all',array('fields'=>array('goods_kbn_nm'),'conditions'=>array('id'=>$data[0]['CeremonyTrn']['estimate_dtl_id'])));
  	return $data[0]['EstimateDtlTrn']['goods_kbn_nm'];
  }

  /**
   * レセプション会場名を取得
   * @param unknown $customer_id
   * @return NULL
   */
  function GetReceptionPlace($customer_id){

  	App::import("Model", "ReceptionTrnView");
  	$reception = new ReceptionTrnView();

  	App::import("Model", "EstimateDtlTrn");
  	$estiamte = new EstimateDtlTrn();

  	$data = $reception->find('all',array('fields'=>array('estimate_dtl_id'),'conditions'=>array('customer_id'=>$customer_id)));
  	if(count($data) == 0){ return  null; }

  	$data = $estiamte->find('all',array('fields'=>array('goods_kbn_nm'),'conditions'=>array('id'=>$data[0]['ReceptionTrnView']['estimate_dtl_id'])));
  	return $data[0]['EstimateDtlTrn']['goods_kbn_nm'];
  }


}