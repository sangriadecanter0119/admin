<?php
class EstimateDtlTrn extends AppModel {
  var $name = 'EstimateDtlTrn';

  /**
   *
   * 見積明細を新規登録する
   * @param $estimate_dtl_data
   *        配列のインデックスは１からとする
   * @param $estimate_id
   * @param $user_name
   * @return 正常:TRUE
   *         異常： Exception例外
   */
  function createNew($estimate_dtl_data,$estimate_id,$user_name)
  {
  	App::import("Model", "SetGoodsEstimateDtlTrn");
  	$set_goods_estimate = new SetGoodsEstimateDtlTrn();

 	for($i=1;$i <= count($estimate_dtl_data);$i++)
    {
       if(!empty($estimate_dtl_data[$i]['goods_id']) &&  $estimate_dtl_data[$i]['goods_id'] !=0 )
       {
          //外貨ベース
          if($estimate_dtl_data[$i]['currency_kbn'] == FOREIGN)
          {
         	//3桁区切りのカンマを除去
 	    	$estimate_dtl_data[$i]['sales_price'] = str_replace(",","",$estimate_dtl_data[$i]['foreign_sales_price']);
 	        $estimate_dtl_data[$i]['sales_cost']  = str_replace(",","",$estimate_dtl_data[$i]['foreign_sales_cost']);
 	      }
 	      //邦貨ベース
 	      else if($estimate_dtl_data[$i]['currency_kbn'] == DOMESTIC)
 	      {   //3桁区切りのカンマを除去
 	        $estimate_dtl_data[$i]['sales_price'] = str_replace(",","",$estimate_dtl_data[$i]['sales_price']);
 	    	$estimate_dtl_data[$i]['sales_cost']  = str_replace(",","",$estimate_dtl_data[$i]['sales_cost']);
 	      }
 	      //予期しない通貨フラグ
 	      else
 	      {
 	      	return array('result'=>false,'message'=>"見積明細の登録に失敗しました。",'reason'=>"予期しない通貨フラグ[{$estimate_dtl_data[$i]['currency_kbn']}]が設定されています。");
 	      }
 	        $estimate_dtl_data[$i]['id'] = null;
 	        $estimate_dtl_data[$i]['no'] = $i;
 	        $estimate_dtl_data[$i]['estimate_id'] = $estimate_id;
 	        $estimate_dtl_data[$i]['reg_nm'] = $user_name;
 	        $estimate_dtl_data[$i]['reg_dt'] = date('Y-m-d H:i:s');

 	        //シングルクオートのエスケープ処理
 	        //$estimate_dtl_data[$i]['sales_goods_nm'] = str_replace("'","\'",$estimate_dtl_data[$i]['sales_goods_nm']);
 	        //$estimate_dtl_data[$i]['original_sales_goods_nm'] = str_replace("'","''",$estimate_dtl_data[$i]['original_sales_goods_nm']);
 	        //$estimate_dtl_data[$i]['vendor_nm'] = str_replace("'","''",$estimate_dtl_data[$i]['vendor_nm']);
 	        //$estimate_dtl_data[$i]['goods_kbn_nm'] = str_replace("'","''",$estimate_dtl_data[$i]['goods_kbn_nm']);
 	        //$estimate_dtl_data[$i]['goods_ctg_nm'] = str_replace("'","''",$estimate_dtl_data[$i]['goods_ctg_nm']);

 	        //フィールドの初期化
  	        $this->create();
 	        if($this->save($estimate_dtl_data[$i])==false){
 	        	return array('result'=>false,'message'=>"見積明細の登録に失敗しました。",'reason'=>$this->getDbo()->error."[".date('Y-m-d H:i:s')."]");
 	        }
 	        $last_estimate_dtl_id = $this->getLastInsertID();
 	        //セット商品なら構成情報を見積明細に登録する
 	        if($estimate_dtl_data[$i]['set_goods_kbn']== SET_GOODS){
 	           $ret = $set_goods_estimate->createNew($estimate_dtl_data[$i]['goods_id'], $last_estimate_dtl_id, $user_name);
 	           if($ret['result'] == false){return $ret;}
 	        }
 	   }
    }
    return array('result'=>true);
  }

  /**
   *
   * クローンデータ作成
   * @param $estimate_id
   */
  function createClone($id,$estimate_id)
  {
  	 $data = $this->findById($id);
  	 $data['EstimateDtlTrn']['id'] = null;
  	 $data['EstimateDtlTrn']['estimate_id'] = $estimate_id;
     //フィールドの初期化
 	 $this->create();
 	 if($this->save($data['EstimateDtlTrn'])==false){
 	   return array('result'=>false,'message'=>"見積明細の登録に失敗しました。",'reason'=>$this->getDbo()->error."[".date('Y-m-d H:i:s')."]");
     }
 	 return array('result'=>true,'newID'=>$this->getLastInsertID());
  }

  /**
   *
   * 見積明細データを適切に処理する
   *  1.新規データは新規作成する
   *  2.既存のデータは更新処理する
   *  3.引数で渡されたデータ以外は削除する
   * @param $estimate_dtl_data
   * @param $estimate_id
   * @param $user_name
   * @return 正常:新規明細IDと削除商品IDの連想配列
   *          異常：Exception例外
   */
  function ManuplateAll($estimate_dtl_data,$estimate_id,$user_name)
  {
  	App::import("Model", "SetGoodsEstimateDtlTrn");
  	$set_goods_estimate = new SetGoodsEstimateDtlTrn();

 	//見積明細テーブルの更新
    $est_dtl_fields = array('no','goods_id','sales_goods_nm','num','sales_price','sales_cost','payment_kbn_id','sales_exchange_rate','cost_exchange_rate','aw_share','rw_share','money_received_flg','upd_nm','upd_dt');
 	//新規追加または更新した明細IDを保持
    $saving_id= array();
    //新規明細ID
    $new_ids = array();
    //更新明細ID
    $update_ids = array();


 	//0行目はヘッダ用にダミーにしているので1行目から始める
    for($i=1;$i <= count($estimate_dtl_data);$i++)
    {
    	//シングルクオートのエスケープ処理
    	//$estimate_dtl_data[$i]['sales_goods_nm'] = str_replace("'","''",$estimate_dtl_data[$i]['sales_goods_nm']);

       //商品が選択されていない(空の明細)行があったらスキップする
       if(!empty($estimate_dtl_data[$i]['goods_id']) &&  $estimate_dtl_data[$i]['goods_id'] > 0 )
       {
          //明細の順番設定
          $estimate_dtl_data[$i]['no'] = $i;

          //外貨ベース
          if($estimate_dtl_data[$i]['currency_kbn'] == FOREIGN){
         	//3桁区切りのカンマを除去
 	    	$estimate_dtl_data[$i]['sales_price'] = str_replace(",","",$estimate_dtl_data[$i]['foreign_sales_price']);
 	        $estimate_dtl_data[$i]['sales_cost']  = str_replace(",","",$estimate_dtl_data[$i]['foreign_sales_cost']);
 	      }
 	      //邦貨ベース
 	      else if($estimate_dtl_data[$i]['currency_kbn'] == DOMESTIC){
 	      	//3桁区切りのカンマを除去
 	        $estimate_dtl_data[$i]['sales_price'] = str_replace(",","",$estimate_dtl_data[$i]['sales_price']);
 	    	$estimate_dtl_data[$i]['sales_cost']  = str_replace(",","",$estimate_dtl_data[$i]['sales_cost']);
 	      }
 	      //予期しない通貨フラグ
 	      else{
 	      	return array('result'=>false,'message'=>"見積明細の更新に失敗しました。",'reason'=>"予期しない通貨フラグ[{$estimate_dtl_data[$i]['currency_kbn']}]が設定されています。");
 	      }

 	     //明細IDがNULLの場合はクライアント側で新規に追加した項目なので新規作成する
 	     if(empty($estimate_dtl_data[$i]['id']) || $estimate_dtl_data[$i]['id']==null)
 	     {
 	           $estimate_dtl_data[$i]['estimate_id'] =  $estimate_id;
 	           $estimate_dtl_data[$i]['reg_nm'] = $user_name;
 	           $estimate_dtl_data[$i]['reg_dt'] = date('Y-m-d H:i:s');

 	           $this->create();
 	           if($this->save($estimate_dtl_data[$i])==false){
 	           	  return array('result'=>false,'message'=>"見積明細の登録に失敗しました。",'reason'=>$this->getDbo()->error."[".date('Y-m-d H:i:s')."]");
 	           }

 	           //新規作成したデータのIDを保存
 	           $last_estimate_dtl_id = $this->getLastInsertID();
 	           //セット商品なら構成情報を見積明細に登録する
 	           if($estimate_dtl_data[$i]['set_goods_kbn']== SET_GOODS){
 	              $ret = $set_goods_estimate->createNew($estimate_dtl_data[$i]['goods_id'], $last_estimate_dtl_id, $user_name);
 	              if($ret['result'] == false){return $ret;}
 	           }
 	           array_push($saving_id, $last_estimate_dtl_id);
 	           array_push($new_ids, $last_estimate_dtl_id);
 	     }
 	     //既存の明細の更新
 	     else
 	     {
 	           //削除されず残っているデータのIDを保存
 	           array_push($saving_id,$estimate_dtl_data[$i]['id']);
 	           array_push($update_ids, $estimate_dtl_data[$i]['id']);
 	           $estimate_dtl_data[$i]['upd_nm'] = $user_name;
 	           $estimate_dtl_data[$i]['upd_dt'] = date('Y-m-d H:i:s');
 	           $this->id = $estimate_dtl_data[$i]['id'];
 	           if($this->save($estimate_dtl_data[$i],false,$est_dtl_fields)==false){
 	           	  return array('result'=>false,'message'=>"見積明細の更新に失敗しました。",'reason'=>$this->getDbo()->error."[".date('Y-m-d H:i:s')."]");
 	           }
 	           //セット商品なら構成情報の入金済みフラグを更新する
 	           if($this->isSetGoodsEstimate($estimate_dtl_data[$i]['id']) == SET_GOODS){
 	              $ret = $set_goods_estimate->updateAllPayment($estimate_dtl_data[$i]['id'], $estimate_dtl_data[$i]['money_received_flg']);
 	              if($ret['result'] == false){return $ret;}
 	           }
 	      }
 	   }
 	}
 	    /* 削除される明細データの商品IDを保持する */
 	    $deleting_data = $this->find('all',array('conditions'=>array('estimate_id'=>$estimate_id,'NOT'=>array('id'=>$saving_id))));
 	    $result = array();
 	    $deleting_data_counter =0;
 	    for($k =0;$k < count($deleting_data);$k++)
 	    {
 	    	$result['DeletedGoodsId'][$deleting_data_counter] = $deleting_data[$k]['EstimateDtlTrn']['goods_id'];
 	    	/* セット商品の場合は紐づいている構成商品IDも保持する */
 	        $set_goods_estimate_data = $set_goods_estimate->find('all',array('conditions'=>array('estimate_dtl_id'=>$deleting_data[$k]['EstimateDtlTrn']['id'])));
 	        for($j=0;$j < count($set_goods_estimate_data);$j++){
 	        	$deleting_data_counter++;
 	        	$result['DeletedGoodsId'][$deleting_data_counter] = $set_goods_estimate_data[$j]['SetGoodsEstimateDtlTrn']['goods_id'];
 	        }
 	        $deleting_data_counter++;
 	    }
 	    $result['NewEstimateDtlId'] = $new_ids;
 	    $result['UpdateEstimateDtlId'] = $update_ids;

 	    //新規追加でも既存の明細の更新でもないデータはクライアント側で削除指定されたデータなのですべて削除
 	    if($this->deleteAll( array('estimate_id'=>$estimate_id,'NOT'=>array('id'=>$saving_id)))==false){
 	    	return array('result'=>false,'message'=>"見積明細の削除に失敗しました。",'reason'=>$this->getDbo()->error."[".date('Y-m-d H:i:s')."]");
 	    }
 	    return array('result'=>true,'info'=>$result);
  }

  /**
   *
   * 支払区分更新
   * @param $estimate_dtl_data
   * @param $estimate_id
   * @param $user_name
   */
  function UpdatePaymentKbn($estimate_dtl_data,$user_name)
  {
 	//見積明細テーブルの更新
    $est_dtl_fields = array('payment_kbn_id','upd_nm','upd_dt');

 	//0行目はヘッダ用にダミーにしているので1行目から始める
    for($i=1;$i <= count($estimate_dtl_data);$i++)
    {
 	    $estimate_dtl_data[$i]['upd_nm'] = $user_name;
 	    $estimate_dtl_data[$i]['upd_dt'] = date('Y-m-d H:i:s');
 	    $this->id = $estimate_dtl_data[$i]['id'];
 	    if($this->save($estimate_dtl_data[$i],false,$est_dtl_fields)==false){
 	    	return array('result'=>false,'message'=>"見積明細の支払区分更新に失敗しました。",'reason'=>$this->getDbo()->error."[".date('Y-m-d H:i:s')."]");
 	    }
    }
 	   return array('result'=>true);
  }

  /**
   *
   * 原価為替レート更新
   * @param $estimate_id
   * @param $user_name
   */
  function UpdateAllCostExchangeRateByEstimateId($estimate_id,$cost_exchange_rate,$user_name)
  {
 	if($this->updateAll(array('cost_exchange_rate'=>"'".$cost_exchange_rate."'",
 	                          'upd_dt'=>"'".date('Y-m-d H:i:s')."'",
 	                          'upd_nm'=>"'".$user_name."'"),array("estimate_id"=>$estimate_id))==false){
 	 	return array('result'=>false,'message'=>"見積明細の原価為替レート更新に失敗しました。",'reason'=>$this->getDbo()->error."[".date('Y-m-d H:i:s')."]");
 	}
 	   return array('result'=>true);
  }

  /**
   *
   * 見積明細データから挙式に関する商品があるか調べて、挙式会場名を取得する
   *   @param $estimate_dtl_data
   *   @return 正常:挙式会場名
   *           異常： NULL
   */
  function findWeddingPlace($estimate_dtl_data)
  {
  	  App::import("Model", "GoodsMstView");
  	  $goods_view = new GoodsMstView();

      //挙式分類に所属する商品区分名を取得
 	  for($i=1;$i <= count($estimate_dtl_data);$i++)
      {
        $goods = $goods_view->findById($estimate_dtl_data[$i]['goods_id']);
        if($goods['GoodsMstView']['goods_ctg_id'] == GC_WEDDING)
        {
          return $goods['GoodsMstView']['goods_kbn_nm'];
        }
      }
      return null;
  }

  /**
   *
   * 見積明細データから挙式に関する商品があるか調べて、教会コードを取得する
   * @param $estimate_dtl_data
   * @return 正常:教会コード
   *         異常： NULL
   */
  function findChurchCode($estimate_dtl_data)
  {
  	   App::import("Model", "GoodsKbnMst");
  	   $goods_kbn = new GoodsKbnMst();

      //挙式分類に所属する商品区分名を取得
      for($i=0;$i < count($estimate_dtl_data);$i++)
      {
      	if($estimate_dtl_data[$i]['EstimateDtlTrnView']['goods_ctg_id'] == GC_WEDDING){
      	   $goods_kbn_data = $goods_kbn->findById($estimate_dtl_data[$i]['EstimateDtlTrnView']['goods_kbn_id']);
           return $goods_kbn_data['GoodsKbnMst']['church_code'];
        }
      }
      return null;
  }

  /**
   *
   * 見積明細データからレセプションに関する商品があるか調べて、レセプション会場名を取得する
   *   @param $estimate_dtl_data
   *   @return 正常:レセプション会場名
   *           異常： NULL
   */
  function findReceptionPlace($estimate_dtl_data)
  {
  	  App::import("Model", "GoodsMstView");
  	  $goods_view = new GoodsMstView();

  	  //レセプション分類に所属する商品区分名を取得
 	  for($i=1;$i <= count($estimate_dtl_data);$i++)
      {
        $goods = $goods_view->findById($estimate_dtl_data[$i]['goods_id']);
        if($goods['GoodsMstView']['goods_ctg_id'] == GC_RECEPTION)
        {
          return $goods['GoodsMstView']['goods_kbn_nm'];
        }
      }
    return null;
   }

  /**
   * セット商品の見積もりであることをチェック
   * @param unknown $id
   * @return boolean
   */
  function isSetGoodsEstimate($id){

  	$data = $this->findById($id);
    return $data['EstimateDtlTrn']['set_goods_kbn'] == 1 ? true :false;
   }
}
?>