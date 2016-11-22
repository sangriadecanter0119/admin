<?php
class GoodsUploader extends AppModel {
    var $useTable = false;

    const COL_TARGET = 'A';
    const COL_GOODS_CD = 'B';
    const COL_GOODS_CTG_ID = 'C';
    const COL_GOODS_KBN_ID = 'E';
    const COL_VENDOR_ID = 'G';
    const COL_GOODS_NM = 'I';
    const COL_TAX = 'J';
    const COL_SERVICE_RATE = 'K';
    const COL_PROFIT_RATE = 'L';
    const COL_COST1 = 'M';
    const COL_COST2 = 'N';
    const COL_COST3 = 'O';
    const COL_COST4 = 'P';
    const COL_COST5 = 'Q';
    const COL_COST6 = 'R';
    const COL_COST7 = 'S';
    const COL_COST8 = 'T';
    const COL_COST9 = 'U';
    const COL_COST10 = 'V';
    const COL_COST = 'W';
    const COL_PRICE = 'X';
    const COL_COST_EXCHANGE_RATE = 'Y';
    const COL_PRICE_EXCHANGE_RATE = 'AA';
    const COL_INTERNAL_PAY_FLG = 'AD';
    const COL_PAYMENT_KBN_ID = 'AE';
    const COL_CURRENCY_KBN = 'AF';
    const COL_HI_SHARE = 'AG';
    const COL_RW_SHARE = 'AH';
    const COL_DELETE_KBN = 'AJ';
    const COL_NOTE = 'AK';

    const DATA_START_ROW = 2;
    const COL_COUNT = 37;

    /**
     * 商品EXCELファイルをアップロードしてサーバに仮保存
     * @param unknown $tempFile
     * @param unknown $uploadingFileName
     * @return boolean[]|string[]
     */
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

    /**
     * 商品EXCELファイルを元に商品マスタを更新
     * @param unknown $file
     * @param unknown $username
     * @return boolean[]|string[]|boolean[]
     */
    function updateByFile($file,$username)
    {
    	App::import( 'Vendor', 'PHPExcel_Reader_Excel2007', array('file'=>'phpexcel' . DS . 'PHPExcel' . DS . 'Reader' . DS . 'Excel2007.php') );

    	$reader = new PHPExcel_Reader_Excel2007();
    	$objPHPExcel = $reader->load($file);
    	$tmp = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
    	//1:ヘッダ 2～:データ行
    	//$m = $tmp[2]['B'];

    	//return array('result'=>false,'message'=>$tmp[2][self::COL_GOODS_ID]);

    	//バリデーション
    	$errors = $this->validate($tmp);
    	if(isset($errors['errors']) && count($errors['errors']) > 0){ return array('result'=>false,'message'=>'商品マスタ更新に失敗しました。','errors'=>$errors['errors']);}

    	//データ振り分け
    	$data = $this->getTargetData($tmp);

    	//データ登録
    	$ret = $this->_create($data['new'], $username);
    	if($ret['result'] == false){ return $ret;}

    	//データ更新
    	$ret = $this->_update($data['update'], $username);
    	if($ret['result'] == false){ return $ret;}

    	//データ削除
    	$ret = $this->_delete($data['delete'], $username);
    	if($ret['result'] == false){ return $ret;}

    	return array('result'=>true,'message'=>'新規:'.count($data['new']).'件、更新:'.count($data['update']).'件、削除:'.count($data['delete']).'件');
    }

    private function _create($new_data,$username)
    {
    	App::import("Model", "GoodsMst");
    	$goods = new GoodsMst();

    	for($i=0;$i < count($new_data);$i++){

    		$data = array(
    				"year"=>GOODS_YEAR,
    				"revision"=>1,
    				"goods_cd"=>$goods->getNewGoodsCode($new_data[$i][self::COL_GOODS_CTG_ID],GOODS_YEAR),
    				"goods_kbn_id"=>$new_data[$i][self::COL_GOODS_KBN_ID],
    				"vendor_id"=>$new_data[$i][self::COL_VENDOR_ID],
    				"goods_nm"=>$new_data[$i][self::COL_GOODS_NM],
    				"price"=>str_replace(',','',$new_data[$i][self::COL_PRICE]),
    				"cost"=>str_replace(',','',$new_data[$i][self::COL_COST]),
    				"cost1"=>str_replace(',','',$new_data[$i][self::COL_COST1]),
    				"cost2"=>str_replace(',','',$new_data[$i][self::COL_COST2]),
    				"cost3"=>str_replace(',','',$new_data[$i][self::COL_COST3]),
    				"cost4"=>str_replace(',','',$new_data[$i][self::COL_COST4]),
    				"cost5"=>str_replace(',','',$new_data[$i][self::COL_COST5]),
    				"cost6"=>str_replace(',','',$new_data[$i][self::COL_COST6]),
    				"cost7"=>str_replace(',','',$new_data[$i][self::COL_COST7]),
    				"cost8"=>str_replace(',','',$new_data[$i][self::COL_COST8]),
    				"cost9"=>str_replace(',','',$new_data[$i][self::COL_COST9]),
    				"cost10"=>str_replace(',','',$new_data[$i][self::COL_COST10]),
    				"tax"=>str_replace('%','',$new_data[$i][self::COL_TAX]) / 100,
    				"service_rate"=>str_replace('%','',$new_data[$i][self::COL_SERVICE_RATE]) / 100,
    				"profit_rate"=>str_replace('%','',$new_data[$i][self::COL_PROFIT_RATE]) / 100,
    				"sales_exchange_rate"=>str_replace(',','',$new_data[$i][self::COL_PRICE_EXCHANGE_RATE]),
    				"cost_exchange_rate"=>str_replace(',','',$new_data[$i][self::COL_COST_EXCHANGE_RATE]),
    				"aw_share"=>str_replace('%','',$new_data[$i][self::COL_HI_SHARE]) / 100,
    				"rw_share"=>str_replace('%','',$new_data[$i][self::COL_RW_SHARE]) / 100,
    				"currency_kbn"=>$new_data[$i][self::COL_CURRENCY_KBN],
    				"internal_pay_flg"=>$new_data[$i][self::COL_INTERNAL_PAY_FLG],
    				"set_goods_kbn"=>0,
    				"start_valid_dt"=>"1000-01-01",
    				"end_valid_dt"=>"9999-12-31",
    				"payment_kbn"=>$new_data[$i][self::COL_PAYMENT_KBN_ID],
    				"non_display_flg"=>0,
    				"reg_nm"=>$username,
    				"reg_dt"=>date('Y-m-d H:i:s')
    		);
    		$goods->create();
    		if($goods->save($data) == false){ return  array('result'=>false,'message'=>$this->getDbo()->error."[".date('Y-m-d H:i:s')."]"); }
    	}
    	return array('result'=>true);
    }

    private function _update($update_data,$username)
    {
    	App::import("Model", "GoodsMst");
    	$goods = new GoodsMst();

    	for($i=0;$i < count($update_data);$i++){

    		$goods_id = $this->getLatestGoodsId($update_data[$i][self::COL_GOODS_CD]);
    		$current_data = $goods->findById($goods_id);

    		$data = array(
    				"year"=>$current_data['GoodsMst']['year'],
    				"revision"=>$current_data['GoodsMst']['revision'] + 1,
    				"goods_cd"=>$current_data['GoodsMst']['goods_cd'],
    				"goods_kbn_id"=>$update_data[$i][self::COL_GOODS_KBN_ID],
    				"vendor_id"=>$update_data[$i][self::COL_VENDOR_ID],
    				"goods_nm"=>$update_data[$i][self::COL_GOODS_NM],
    				"price"=>str_replace(',','',$update_data[$i][self::COL_PRICE]),
    				"cost"=>str_replace(',','',$update_data[$i][self::COL_COST]),
    				"cost1"=>str_replace(',','',$update_data[$i][self::COL_COST1]),
    				"cost2"=>str_replace(',','',$update_data[$i][self::COL_COST2]),
    				"cost3"=>str_replace(',','',$update_data[$i][self::COL_COST3]),
    				"cost4"=>str_replace(',','',$update_data[$i][self::COL_COST4]),
    				"cost5"=>str_replace(',','',$update_data[$i][self::COL_COST5]),
    				"cost6"=>str_replace(',','',$update_data[$i][self::COL_COST6]),
    				"cost7"=>str_replace(',','',$update_data[$i][self::COL_COST7]),
    				"cost8"=>str_replace(',','',$update_data[$i][self::COL_COST8]),
    				"cost9"=>str_replace(',','',$update_data[$i][self::COL_COST9]),
    				"cost10"=>str_replace(',','',$update_data[$i][self::COL_COST10]),
    				"tax"=>str_replace('%','',$update_data[$i][self::COL_TAX]) / 100,
    				"service_rate"=>str_replace('%','',$update_data[$i][self::COL_SERVICE_RATE]) / 100,
    				"profit_rate"=>str_replace('%','',$update_data[$i][self::COL_PROFIT_RATE]) / 100,
    				"sales_exchange_rate"=>str_replace(',','',$update_data[$i][self::COL_PRICE_EXCHANGE_RATE]),
    				"cost_exchange_rate"=>str_replace(',','',$update_data[$i][self::COL_COST_EXCHANGE_RATE]),
    				"aw_share"=>str_replace('%','',$update_data[$i][self::COL_HI_SHARE]) / 100,
    				"rw_share"=>str_replace('%','',$update_data[$i][self::COL_RW_SHARE]) / 100,
    				"currency_kbn"=>$update_data[$i][self::COL_CURRENCY_KBN],
    				"internal_pay_flg"=>$update_data[$i][self::COL_INTERNAL_PAY_FLG],
    				"set_goods_kbn"=>$current_data['GoodsMst']['set_goods_kbn'],
    				"start_valid_dt"=>$current_data['GoodsMst']['start_valid_dt'],
    				"end_valid_dt"=>$current_data['GoodsMst']['end_valid_dt'],
    				"payment_kbn"=>$update_data[$i][self::COL_PAYMENT_KBN_ID],
    				"non_display_flg"=>$current_data['GoodsMst']['non_display_flg'],
    				"reg_nm"=>$current_data['GoodsMst']['reg_nm'],
    				"reg_dt"=>$current_data['GoodsMst']['reg_dt'],
    				"upd_nm"=>$username,
    				"upd_dt"=>date('Y-m-d H:i:s')
    		);
    		$goods->create();
    		if($goods->save($data) == false){ return  array('result'=>false,'message'=>$this->getDbo()->error."[".date('Y-m-d H:i:s')."]"); }
    	}
    	return array('result'=>true);
    }

    private function _delete($delete_data,$username)
    {
    	App::import("Model", "GoodsMst");
    	$goods = new GoodsMst();
    	App::import("Model", "EstimateDtlTrn");
    	$estimate_dtl = new EstimateDtlTrn();
    	App::import("Model", "SetGoodsMst");
    	$set_goods = new SetGoodsMst();
    	App::import("Model", "LatestGoodsMstView");
    	$goods_view = new LatestGoodsMstView();

    	for($i=0;$i < count($delete_data);$i++){

    		/* 全てのリビジョンを物理or論理削除する  */
    		$target = $goods->find('all',array('fields'=>array('id'),'conditions'=>array('goods_cd'=>$delete_data[$i][self::COL_GOODS_CD])));

    		for($j=0;$j < count($target);$j++){

    			$goods_id = $target[$j]['GoodsMst']['id'];

    			/* 見積又はセット商品の商品構成で既に使用されている場合は論理削除 */
    			if($estimate_dtl->find('count',array('conditions'=>array('goods_id'=>$goods_id))) > 0 ||
    			   $set_goods->find('count',array('conditions'=>array('goods_id'=>$goods_id))) > 0){

    				$fields = array('del_kbn'=>DELETE,'del_nm'=>"'".$username."'",'del_dt'=>"'".date('Y-m-d H:i:s')."'");
    				/* 商品マスタ */
    				if($goods->updateAll($fields,array('id'=>$goods_id))==false){
    					return array('result'=>false,'message'=>"削除に失敗しました。".$goods->getDbo()->error."[".date('Y-m-d H:i:s')."]");
    				}
    			/* 物理削除 */
    			}else{
    				if($goods->delete($goods_id)==false){
    					return array('result'=>false,'message'=>"削除に失敗しました。".$goods->getDbo()->error."[".date('Y-m-d H:i:s')."]");
    				}
    			}
    		}
    	}
    	return array('result'=>true);
    }

    private function getTargetData($data)
    {
    	$ret = array();
    	for($i = self::DATA_START_ROW; $i <= count($data) ;$i++){
    		if($data[$i][self::COL_TARGET] == 1){

    			if($data[$i][self::COL_DELETE_KBN] == 1){
    				$ret['delete'][] = $data[$i];

    			}else if(empty($data[$i][self::COL_GOODS_CD])){
    				$ret['new'][] = $data[$i];
    			}else{
    				$ret['update'][] = $data[$i];
    			}
    		}
    	}
    	return $ret;
    }

    private function validate($data)
    {
    	$ret = array();
    	for($i = self::DATA_START_ROW; $i <= count($data) ;$i++){

    		//処理対象データ
    		if($data[$i][self::COL_TARGET] == 1){

    			if(count($data[$i]) < self::COL_COUNT){
    				$ret['errors'][] = '列数が不足しています。('.$i.'行目)';
    			}else{

    				//更新又は削除データ
    				if(!empty($data[$i][self::COL_GOODS_CD])){

    					$id = $this->getLatestGoodsId($data[$i][self::COL_GOODS_CD]);
    					if(empty($id)){
    						$ret['errors'][] = '商品コードは存在しません('.$i.'行目)';
    					}elseif(!$this->isLatestGoods($id)){
    						$ret['errors'][] = '最新リビジョンの商品ではありません。('.$i.'行目)';
    					}else{
    						if($this->isSetGoods($id)){ $ret['errors'][] = 'セット商品は更新・削除できません。('.$i.'行目)';}
    					}

    				}

    				//新規又は更新データ
    				if($data[$i][self::COL_DELETE_KBN] != 1){

    					if(!$this->goodsCtgExists($data[$i][self::COL_GOODS_CTG_ID])){ $ret['errors'][] = '商品分類IDは存在しません。('.$i.'行目)';}
    					if(!$this->goodskbnExists($data[$i][self::COL_GOODS_KBN_ID])){ $ret['errors'][] = '商品区分IDは存在しません。('.$i.'行目)';}
    					if(!$this->vendorExists($data[$i][self::COL_VENDOR_ID])){ $ret['errors'][] = 'ベンダーIDは存在しません。('.$i.'行目)';}


    					if(empty($data[$i][self::COL_VENDOR_ID])){ $ret['errors'][] = 'ベンダーIDは必須です。('.$i.'行目)';}
    					if(empty($data[$i][self::COL_COST1])){ $ret['errors'][] = 'Cost1は必須です。('.$i.'行目)';}
    					if(empty($data[$i][self::COL_COST])){ $ret['errors'][] = '税サービス込仕入価格は必須です。('.$i.'行目)';}
    					if(empty($data[$i][self::COL_PRICE])){ $ret['errors'][] = '販売価格は必須です。('.$i.'行目)';}


    					if(!$this->isInGoodsCtg($data[$i][self::COL_GOODS_CTG_ID], $data[$i][self::COL_GOODS_KBN_ID])){ $ret['errors'][] = '商品区分は商品分類に属してません。('.$i.'行目)';}
    					if(!empty($data[$i][self::COL_TAX]) && !$this->isNumeric($data[$i][self::COL_TAX])){ $ret['errors'][] = 'Tax値が不正です。('.$i.'行目)';}
    					if(!empty($data[$i][self::COL_SERVICE_RATE]) && !$this->isNumeric($data[$i][self::COL_SERVICE_RATE])){ $ret['errors'][] = 'ServiceRate値が不正です。('.$i.'行目)';}
    					if(!empty($data[$i][self::COL_PROFIT_RATE]) && !$this->isNumeric($data[$i][self::COL_PROFIT_RATE])){ $ret['errors'][] = 'ProfitRate値が不正です。('.$i.'行目)';}
    					if(!empty($data[$i][self::COL_COST1]) && !$this->isNumeric($data[$i][self::COL_COST1])){ $ret['errors'][] = '原価1が不正です。('.$i.'行目)';}
    					if(!empty($data[$i][self::COL_COST2]) && !$this->isNumeric($data[$i][self::COL_COST2])){ $ret['errors'][] = '原価2が不正です。('.$i.'行目)';}
    					if(!empty($data[$i][self::COL_COST3]) && !$this->isNumeric($data[$i][self::COL_COST3])){ $ret['errors'][] = '原価3が不正です。('.$i.'行目)';}
    					if(!empty($data[$i][self::COL_COST4]) && !$this->isNumeric($data[$i][self::COL_COST4])){ $ret['errors'][] = '原価4が不正です。('.$i.'行目)';}
    					if(!empty($data[$i][self::COL_COST5]) && !$this->isNumeric($data[$i][self::COL_COST5])){ $ret['errors'][] = '原価5が不正です。('.$i.'行目)';}
    					if(!empty($data[$i][self::COL_COST6]) && !$this->isNumeric($data[$i][self::COL_COST6])){ $ret['errors'][] = '原価6が不正です。('.$i.'行目)';}
    					if(!empty($data[$i][self::COL_COST7]) && !$this->isNumeric($data[$i][self::COL_COST7])){ $ret['errors'][] = '原価7が不正です。('.$i.'行目)';}
    					if(!empty($data[$i][self::COL_COST8]) && !$this->isNumeric($data[$i][self::COL_COST8])){ $ret['errors'][] = '原価8が不正です。('.$i.'行目)';}
    					if(!empty($data[$i][self::COL_COST9]) && !$this->isNumeric($data[$i][self::COL_COST9])){ $ret['errors'][] = '原価9が不正です。('.$i.'行目)';}
    					if(!empty($data[$i][self::COL_COST10]) && !$this->isNumeric($data[$i][self::COL_COST10])){ $ret['errors'][] = '原価10が不正です。('.$i.'行目)';}

    					if(!empty($data[$i][self::COL_COST]) && !$this->isNumeric($data[$i][self::COL_COST])){ $ret['errors'][] = '税サービス込仕入価格が不正です。('.$i.'行目)';}
    					if(!empty($data[$i][self::COL_PRICE]) && !$this->isNumeric($data[$i][self::COL_PRICE])){ $ret['errors'][] = '販売価格が不正です。('.$i.'行目)';}
    					if(!empty($data[$i][self::COL_COST_EXCHANGE_RATE]) && !$this->isNumeric($data[$i][self::COL_COST_EXCHANGE_RATE])){ $ret['errors'][] = '仕入為替が不正です。('.$i.'行目)';}
    					if(!empty($data[$i][self::COL_PRICE_EXCHANGE_RATE]) && !$this->isNumeric($data[$i][self::COL_PRICE_EXCHANGE_RATE])){ $ret['errors'][] = '販売為替が不正です。('.$i.'行目)';}

    					if(!$this->isValidInternalPayFlg($data[$i][self::COL_INTERNAL_PAY_FLG])){ $ret['errors'][] = '国内払い値が範囲外です。('.$i.'行目)';}
    					if(!$this->isValidPaymentKbn($data[$i][self::COL_PAYMENT_KBN_ID])){ $ret['errors'][] = '支払区分値が範囲外です。('.$i.'行目)';}
    					if(!$this->isValidCurrencyKbn($data[$i][self::COL_CURRENCY_KBN])){ $ret['errors'][] = '通貨区分値が範囲外です。('.$i.'行目)';}
    					if(!$this->isSumHundredPercent($data[$i][self::COL_HI_SHARE],$data[$i][self::COL_RW_SHARE])){ $ret['errors'][] = 'HIとRWの合計値が100%になっていません。('.$i.'行目)';}
    				}
    			}
    		}
    	}
    	return $ret;
    }

    private function isSetGoods($goods_id)
    {
    	App::import("Model", "GoodsMst");
    	$goods = new GoodsMst();
    	$data = $goods->findById($goods_id);

    	return isset($data['GoodsMst']['set_goods_kbn']) && $data['GoodsMst']['set_goods_kbn'] === SET_GOODS;
    }

    private function isLatestGoods($goods_id)
    {
    	App::import("Model", "LatestGoodsMstView");
    	$goods_view = new LatestGoodsMstView();
    	return  $goods_view->find('count',array('conditions'=>array('id'=>$goods_id))) ==  1;
    }

    private function getLatestGoodsId($goods_cd)
    {
    	App::import("Model", "GoodsMst");
    	$goods = new GoodsMst();
    	$data = $goods->find('all',array('fields'=>array('id','revision','goods_cd','del_kbn'),'conditions'=>array('goods_cd'=>$goods_cd),'order'=>array('revision desc')));
     	return count($data) > 0 && $data[0]['GoodsMst']['del_kbn'] == EXISTS ? $data[0]['GoodsMst']['id'] : null;
    }


    private function goodsExists($goods_id)
    {
    	App::import("Model", "GoodsMst");
    	$goods = new GoodsMst();
    	return  $goods->find('count',array('conditions'=>array('id'=>$goods_id,'del_kbn'=>EXISTS))) ==  1;
    }

    private function goodsCtgExists($goods_ctg_id)
    {
    	App::import("Model", "GoodsCtgMst");
    	$ctg = new GoodsCtgMst();
    	return  $ctg->find('count',array('conditions'=>array('id'=>$goods_ctg_id,'del_kbn'=>EXISTS))) ==  1;
    }

    private function goodsKbnExists($goods_kbn_id)
    {
    	App::import("Model", "GoodsKbnMst");
    	$kbn = new GoodsKbnMst();
    	return $kbn->find('count',array('conditions'=>array('id'=>$goods_kbn_id,'del_kbn'=>EXISTS))) ==  1;
    }

    private function vendorExists($vendor_id)
    {
    	App::import("Model", "VendorMst");
    	$vendor = new VendorMst();
    	return  $vendor->find('count',array('conditions'=>array('id'=>$vendor_id,'del_kbn'=>EXISTS))) ==  1;
    }

    private function isInGoodsCtg($goods_ctg_id,$goods_kbn_id)
    {
    	App::import("Model", "GoodsKbnMst");
    	$kbn = new GoodsKbnMst();
    	return  $kbn->find('count',array('conditions'=>array('id'=>$goods_kbn_id, 'goods_ctg_id'=>$goods_ctg_id,'del_kbn'=>EXISTS))) >  0;
    }

    private function isNumeric($data)
    {
    	$tmp = str_replace("%", "", $data);
    	$tmp = str_replace(",", "", $tmp);
    	return is_numeric($tmp);
    }

    private function isSumHundredPercent($hi,$rw)
    {
    	$hi = str_replace("%", "", $hi);
    	$hi = str_replace(",", "", $hi);

    	$rw = str_replace("%", "", $rw);
    	$rw = str_replace(",", "", $rw);

    	return ($hi + $rw) === 100;
    }

    private function isValidInternalPayFlg($data)
    {
    	return strcmp($data, 0) === 0  || strcmp($data, 1) === 0;
    }

    private function isValidPaymentKbn($data)
    {
    	return strcmp($data, PC_INDIRECT_ABOARD_PAY) === 0 || strcmp($data, PC_DIRECT_ABOARD_PAY) === 0 || strcmp($data, PC_CREDIT_ABOARD_PAY) === 0 ||
    	       strcmp($data, PC_DOMESTIC_DIRECT_PAY) === 0 || strcmp($data, PC_DOMESTIC_CREDIT_PAY) === 0;
    }

    private function isValidCurrencyKbn($data)
    {
    	return strcmp($data, FOREIGN) === 0 || strcmp($data, DOMESTIC) === 0;
    }
}
?>