<?php
class GoodsService extends AppModel {
    var $useTable = false;

    /**
     *
     * 新年度の商品及びセット商品マスタの作成
     * @param $year
     * @param $user_name
     */
    function createNewGoodsMaster($src_year,$new_year,$user_name)
    {
       $tr = ClassRegistry::init('TransactionManager');
	   $tr->begin();

	      if(empty($src_year) or empty($new_year)){
	      	return array('result'=>false,'message'=>"年度が指定されていません。",'reason'=>"");
	      }

	      if(strlen($src_year) != 4 or strlen($new_year) !=4){
	      	return array('result'=>false,'message'=>"年度形式が不正です。(YYYY)",'reason'=>"");
	      }

	      App::import("Model", "GoodsMst");
          $goods = new GoodsMst();
          App::import("Model", "SetGoodsMst");
       	  $set_goods = new SetGoodsMst();

 	      if($goods->hasGoodsMasterOfYear($src_year)==false){
 	 	    return array('result'=>false,'message'=>"複製元となる".($src_year)."年度の商品マスタは存在しません。",'reason'=>"");
 	      }

 	       /* 作成する新年度の商品マスタが既に存在すれば削除する */
      	   if($set_goods->deleteAll(array('year'=>$new_year),false)==false){
      	   	return array('result'=>false,'message'=>"セット商品マスタの削除に失敗しました。",'reason'=>$this->getDbo()->error."[".date('Y-m-d H:i:s')."]");
      	   }

 	       if($goods->deleteAll(array('year'=>$new_year),false)==false){
 	       	return array('result'=>false,'message'=>"商品マスタの削除に失敗しました。",'reason'=>$this->getDbo()->error."[".date('Y-m-d H:i:s')."]");
 	       }

 	       /* セット商品マスタの複製登録 */
 	       $ret = $set_goods->duplicate($src_year,$new_year, $user_name);
 	       if($ret['result']==false){ return $ret; }

 	       /* 指定年度の商品を全て取得して新年度の商品に複製する  */
 	       $goods_data = $goods->find('all',array("conditions"=>array("year"=>$src_year,'del_kbn'=>EXISTS)));

 	       for($i=0;$i < count($goods_data);$i++){

 	       	$old_goods_id = $goods_data[$i]['GoodsMst']['id'];

 	       	$goods_data[$i]['GoodsMst']['id']       = null;
 	       	//商品コードに含まれている年度を新年度(YY)に置換する
 	       	$goods_data[$i]['GoodsMst']['goods_cd'] =  str_replace(substr($src_year,2,2), substr($new_year,2,2), $goods_data[$i]['GoodsMst']['goods_cd']);
 	       	$goods_data[$i]['GoodsMst']['year']     = $new_year;
 	       	$goods_data[$i]['GoodsMst']['reg_nm']   = $user_name;
 	       	$goods_data[$i]['GoodsMst']['reg_dt']   = date('Y-m-d H:i:s');
 	       	$goods_data[$i]['GoodsMst']['upd_nm']   = null;
 	       	$goods_data[$i]['GoodsMst']['upd_dt']   = null;

 	       	if($goods->save($goods_data[$i])==false){return false;}
 	        $new_goods_id = $goods->getLastInsertID();

 	        /* セット商品マスタのセット商品ＩＤまたは商品ＩＤを新年度版の商品マスタの商品IDで更新する */
 	        if($goods_data[$i]['GoodsMst']['set_goods_kbn'] == SET_GOODS){
 	          $ret = $set_goods->updateSetGoodsId($new_goods_id, $old_goods_id, $new_year);
 	          if($ret['result']==false){ return $ret; }

 	        }else{
 	          $ret = $set_goods->updateGoodsId($new_goods_id, $old_goods_id, $new_year);
 	          if($ret['result']==false){ return $ret; }
 	        }
 	       }
       $tr->commit();
       return array('result'=>true);
    }

    /**
     * 引数の回数未満しか見積で使用されていない商品を削除する(一度も使用されていない商品以外は論理削除)
     * @param unknown $count
     * @return multitype:boolean string |multitype:boolean NULL
     */
    function deleteGoodsUsingLessThan($count){

    	$tr = ClassRegistry::init('TransactionManager');
    	$tr->begin();

    	App::import("Model", "GoodsMst");
    	$goods = new GoodsMst();

    	$ret = $goods->deleteGoodsUsingLessThan($count);
    	if($ret['result'] == false){ return $ret; }

    	$tr->commit();
    	return $ret;
    }

    function uploadFile($tempFile,$uploadingFileName){

    	$targetPath = "uploads".DS."goods".DS;
    	$tempFileNameWihtoutExtension = pathinfo($uploadingFileName, PATHINFO_FILENAME);
    	//$targetFile =  mb_convert_encoding($targetPath.$tempFileNameWihtoutExtension.'_'.date('YmdHis').'.xlsx', "SJIS", "AUTO");
        $targetFile =  $targetPath.date('YmdHis').'.xlsx';

    	//ファイル削除
    	//unlink($targetFile);
    	//ファイル保存
    	chmod($targetPath, 0777);
    	move_uploaded_file($tempFile,$targetFile);
    	if($this->data['ImgForm']['ImgFile']['error']!=0){
    		switch ($this->data['ImgForm']['ImgFile']['error'])
    		{
    			case 0:
    				$msg = "アップロードが完了しました。";
    				break;
    			case 1:
    				$msg = "The file is bigger than this PHP installation allows";
    				break;
    			case 2:
    				$msg = "The file is bigger than this form allows";
    				break;
    			case 3:
    				$msg = "Only part of the file was uploaded";
    				break;
    			case 4:
    				$msg = "No file was uploaded";
    				break;
    			case 6:
    				$msg = "Missing a temporary folder";
    				break;
    			case 7:
    				$msg = "Failed to write file to disk";
    				break;
    			case 8:
    				$msg = "File upload stopped by extension";
    				break;
    			default:
    				$msg = "unknown error ".$this->data['ImgForm']['ImgFile']['error'];
    				break;
    		}
    		return array('result'=>false,'message'=>$msg);
    	}else{
    		return array('result'=>true,'filePath'=>$targetFile);
    	}
    }

    function updateByFile($file,$username){

    	App::import("Model", "GoodsMst");
    	$goods = new GoodsMst();

    	App::import("Model", "EstimateDtlTrn");
    	$estimate_dtl = new EstimateDtlTrn();
    	App::import("Model", "SetGoodsMst");
    	$set_goods = new SetGoodsMst();
    	App::import("Model", "LatestGoodsMstView");
    	$goods_view = new LatestGoodsMstView();
    	App::import( 'Vendor', 'PHPExcel_Reader_Excel2007', array('file'=>'phpexcel' . DS . 'PHPExcel' . DS . 'Reader' . DS . 'Excel2007.php') );

    	$reader = new PHPExcel_Reader_Excel2007();
    	$objPHPExcel = $reader->load($file);
    	$tmp = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
    			//1:ヘッダ 2～:データ行
    			//$m = $tmp[2]['B'];

 	//return array('result'=>false,'message'=>$tmp[2]['AF']);   

    	for($i=2;$i <= count($tmp);$i++){

    		$id = $tmp[$i]['A'];
    		if(!empty($id) && $id !="" && $id != null){

    			$current_data = $goods_view->findById($id);
    			if($current_data == null){ return  array('result'=>false,'message'=>$i.'行目のID'.$id.'の商品が存在しません。');}

    		    //削除
    			if($tmp[$i]['AH'] == 1){
    				/* 全てのリビジョンを物理or論理削除する  */
    				$target = $goods->find('all',array('fields'=>array('id'),'conditions'=>array('goods_cd'=>$current_data['LatestGoodsMstView']['goods_cd'])));

    				for($j=0;$j < count($target);$j++){

    					$goods_id = $target[$j]['GoodsMst']['id'];

    					/* 見積又はセット商品の商品構成で既に使用されている場合は論理削除 */
    					if($estimate_dtl->find('count',array('conditions'=>array('goods_id'=>$goods_id))) > 0 ||
    				       $set_goods->find('count',array('conditions'=>array('goods_id'=>$goods_id))) > 0){

    						$fields = array('del_kbn'=>DELETE,'del_nm'=>"'".$username."'",'del_dt'=>"'".date('Y-m-d H:i:s')."'");
    						/* 商品マスタ */
    						if($goodsMst->updateAll($fields,array('id'=>$goods_id))==false){
    							return array('result'=>false,'message'=>"削除に失敗しました。".$goods->getDbo()->error."[".date('Y-m-d H:i:s')."]");
    						}
    						/* 物理削除 */
    					}else{

    						if($goods->delete($goods_id)==false){
    							return array('result'=>false,'message'=>"削除に失敗しました。".$goods->getDbo()->error."[".date('Y-m-d H:i:s')."]");
    						}
    					}
    				}
    			//更新
    			}else{
    				$data = array(
    						"year"=>$current_data['LatestGoodsMstView']['year'],
    						"revision"=>$current_data['LatestGoodsMstView']['revision'] + 1,
    						"goods_cd"=>$current_data['LatestGoodsMstView']['goods_cd'],
    						"goods_kbn_id"=>$tmp[$i]['E'],
    						"vendor_id"=>$tmp[$i]['G'],
    						"goods_nm"=>$tmp[$i]['I'],
    						"price"=>str_replace(',','',$tmp[$i]['Z']),
    						"cost"=>str_replace(',','',$tmp[$i]['X']),
    						"cost1"=>str_replace(',','',$tmp[$i]['M']),
    						"cost2"=>str_replace(',','',$tmp[$i]['N']),
    						"cost3"=>str_replace(',','',$tmp[$i]['O']),
    						"cost4"=>str_replace(',','',$tmp[$i]['P']),
    						"cost5"=>str_replace(',','',$tmp[$i]['Q']),
    						"cost6"=>str_replace(',','',$tmp[$i]['R']),
    						"cost7"=>str_replace(',','',$tmp[$i]['S']),
    						"cost8"=>str_replace(',','',$tmp[$i]['T']),
    						"cost9"=>str_replace(',','',$tmp[$i]['U']),
    						"cost10"=>str_replace(',','',$tmp[$i]['V']),
    						"tax"=>str_replace('%','',$tmp[$i]['J']) / 100,
    						"service_rate"=>str_replace('%','',$tmp[$i]['K']) / 100,
    						"profit_rate"=>str_replace('%','',$tmp[$i]['L']) / 100,
    						"aw_share"=>str_replace('%','',$tmp[$i]['AE']) / 100,
    						"rw_share"=>str_replace('%','',$tmp[$i]['AF']) / 100,
    						"currency_kbn"=>$tmp[$i]['AD'],
    						"internal_pay_flg"=>$tmp[$i]['AB'],
    						"set_goods_kbn"=>$current_data['LatestGoodsMstView']['set_goods_kbn'],
    						"start_valid_dt"=>$current_data['LatestGoodsMstView']['start_valid_dt'],
    						"end_valid_dt"=>$current_data['LatestGoodsMstView']['end_valid_dt'],
    						"payment_kbn"=>$tmp[$i]['AC'],
    						"non_display_flg"=>$current_data['LatestGoodsMstView']['non_display_flg'],
    						"reg_nm"=>$current_data['LatestGoodsMstView']['reg_nm'],
    						"reg_dt"=>$current_data['LatestGoodsMstView']['reg_dt'],
    						"upd_nm"=>$username,
    						"upd_dt"=>date('Y-m-d H:i:s')
    				);
    				$goods->create();
    				if($goods->save($data) == false){ return  array('result'=>false,'message'=>$i.'行目の更新に失敗しました。'); }
    			}
    		//新規
    		}else{
    			$data = array(
    						"year"=>GOODS_YEAR,
    						"revision"=>1,
    						"goods_cd"=>$goods->getNewGoodsCode($tmp[$i]['C'],GOODS_YEAR),
    						"goods_kbn_id"=>$tmp[$i]['E'],
    						"vendor_id"=>$tmp[$i]['G'],
    						"goods_nm"=>$tmp[$i]['I'],
    						"price"=>str_replace(',','',$tmp[$i]['Z']),
    						"cost"=>str_replace(',','',$tmp[$i]['X']),
    						"cost1"=>str_replace(',','',$tmp[$i]['M']),
    						"cost2"=>str_replace(',','',$tmp[$i]['N']),
    						"cost3"=>str_replace(',','',$tmp[$i]['O']),
    						"cost4"=>str_replace(',','',$tmp[$i]['P']),
    						"cost5"=>str_replace(',','',$tmp[$i]['Q']),
    						"cost6"=>str_replace(',','',$tmp[$i]['R']),
    						"cost7"=>str_replace(',','',$tmp[$i]['S']),
    						"cost8"=>str_replace(',','',$tmp[$i]['T']),
    						"cost9"=>str_replace(',','',$tmp[$i]['U']),
    						"cost10"=>str_replace(',','',$tmp[$i]['V']),
    						"tax"=>str_replace('%','',$tmp[$i]['J']) / 100,
    						"service_rate"=>str_replace('%','',$tmp[$i]['K']) / 100,
    						"profit_rate"=>str_replace('%','',$tmp[$i]['L']) / 100,
    						"aw_share"=>str_replace('%','',$tmp[$i]['AE']) / 100,
    						"rw_share"=>str_replace('%','',$tmp[$i]['AF']) / 100,
    						"currency_kbn"=>$tmp[$i]['AD'],
    						"internal_pay_flg"=>$tmp[$i]['AB'],
    						"set_goods_kbn"=>0,
    						"start_valid_dt"=>"1000-01-01",
    						"end_valid_dt"=>"9999-12-31",
    						"payment_kbn"=>$tmp[$i]['AC'],
    						"non_display_flg"=>0,
    						"reg_nm"=>$username,
    						"reg_dt"=>date('Y-m-d H:i:s')
    				);
    			$goods->create();
    			if($goods->save($data) == false){ return  array('result'=>false,'message'=>$i.'行目の更新に失敗しました。'); }
    		}
    	}
    	return array('result'=>true);
    }
}
?>