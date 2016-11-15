<?php
class GoodsMasterController extends AppController
{
 public $name = 'GoodsMaster';
 public $uses = array('GoodsService','GoodsMst','GoodsCtgMst','GoodsMstView','LatestGoodsMstView','GoodsKbnMst','EnvMst',
                      'VendorKbnMst','SetGoodsMst','VendorMst','LatestSetGoodsMstView','EstimateDtlTrn','PaymentKbnMst');
 public $layout = 'edit_mode';
 public $components = array('Auth','RequestHandler');
 public $helpers = array('Html','common','javascript');

 /**
  *
  * 商品一覧画面表示
  */
 function index(){

 	 $search = array();

 	 if (!empty($this->data)) {

 	 	$this->Session->write('filter_goods_mst_ctg_id',$this->data['GoodsMstView']['goods_ctg_id']);
 	 	$this->Session->write('filter_goods_mst_kbn_id',$this->data['GoodsMstView']['goods_kbn_id']);
 	 	$this->Session->write('filter_goods_mst_set_id',$this->data['GoodsMstView']['set_goods_kbn']);
 	 	$this->Session->write('filter_goods_mst_vendor_id',$this->data['GoodsMstView']['vendor_id']);
 	 	$this->Session->write('filter_goods_mst_internal_pay_id',$this->data['GoodsMstView']['internal_pay_flg']);
 	 	$this->Session->write('filter_goods_mst_page',1);
 	 }
	/* ソートリンクからフィルタ条件を引き継ぐ場合(GET) */
	else if(isset($this->params['named']['goods_ctg_id'])    &&
	         isset($this->params['named']['goods_kbn_id'])    &&
	         isset($this->params['named']['set_goods_kbn'])   &&
	         isset($this->params['named']['vendor_id'])       &&
	         isset($this->params['named']['internal_pay_flg']) ) {

		$this->Session->write('filter_goods_mst_ctg_id',$this->params['named']['goods_ctg_id']);
		$this->Session->write('filter_goods_mst_kbn_id',$this->params['named']['goods_kbn_id']);
		$this->Session->write('filter_goods_mst_set_id',$this->params['named']['set_goods_kbn']);
		$this->Session->write('filter_goods_mst_vendor_id',$this->params['named']['vendor_id']);
		$this->Session->write('filter_goods_mst_internal_pay_id',$this->params['named']['internal_pay_flg']);
		$this->Session->write('filter_goods_mst_page',$this->params['named']['page']);
		if(isset($this->params['named']['sort'])){
		  $this->Session->write('filter_goods_mst_sort',$this->params['named']['sort']);
		}
	}

	if($this->Session->read('filter_goods_mst_ctg_id') != -1){
		$search += array("goods_ctg_id"=>$this->Session->read('filter_goods_mst_ctg_id'));
	}
	if($this->Session->read('filter_goods_mst_kbn_id') != -1){
		$search += array("goods_kbn_id"=>$this->Session->read('filter_goods_mst_kbn_id'));
	}
	if($this->Session->read('filter_goods_mst_set_id') != -1){
		$search += array("set_goods_kbn"=>$this->Session->read('filter_goods_mst_set_id'));
	}
	if($this->Session->read('filter_goods_mst_vendor_id') != -1){
		$search += array("vendor_id"=>$this->Session->read('filter_goods_mst_vendor_id'));
	}
	if($this->Session->read('filter_goods_mst_internal_pay_id') != -1){
		$search += array("internal_pay_flg"=>$this->Session->read('filter_goods_mst_internal_pay_id'));
	}

 	  $this->paginate = array (
 	    'LatestGoodsMstView' => array(
		                  'limit' => 20,
 	    		          'page'=>$this->Session->read('filter_goods_mst_page'),
		                  'sort'=>$this->Session->read('filter_goods_mst_sort'),
 	                      'conditions'=>array('year'=>GOODS_YEAR,'del_kbn'=>EXISTS)
 	                      )
	    );
	/*
 	 * フィルタ条件をVIEWで保持する
 	 *  -1:ALL
 	 */
 	$this->set("goods_ctg_id"    ,!isset($search["goods_ctg_id"])     ? "-1" : $search["goods_ctg_id"]);
 	$this->set("goods_kbn_id"    ,!isset($search["goods_kbn_id"])     ? "-1" : $search["goods_kbn_id"]);
 	$this->set("set_goods_kbn"   ,!isset($search["set_goods_kbn"])    ? "-1" : $search["set_goods_kbn"]);
 	$this->set("vendor_id"       ,!isset($search["vendor_id"])        ? "-1" : $search["vendor_id"]);
 	$this->set("internal_pay_flg",!isset($search["internal_pay_flg"]) ? "-1" : $search["internal_pay_flg"]);

 	if(empty($search["goods_ctg_id"])  && empty($search["goods_kbn_id"]) &&
 	   empty($search["set_goods_kbn"]) && empty($search["vendor_id"])    && empty($search["internal_pay_flg"])){
 		$this->set("data",$this->paginate('LatestGoodsMstView'));
 	}else{
 		$this->set("data",$this->paginate('LatestGoodsMstView',$search));
 	}

 	/* フィルターの作成  */
    $filter_conditions = array('del_kbn'=>EXISTS);
    if(isset($search["goods_ctg_id"]))    { $filter_conditions += array('goods_ctg_id'=>$search["goods_ctg_id"]);  }
    if(isset($search["goods_kbn_id"]))    { $filter_conditions += array('goods_kbn_id'=>$search["goods_kbn_id"]);  }
    if(isset($search["set_goods_kbn"]))   { $filter_conditions += array('set_goods_kbn'=>$search["set_goods_kbn"]);  }
    if(isset($search["vendor_id"]))       { $filter_conditions += array('vendor_id'=>$search["vendor_id"]);  }
    if(isset($search["internal_pay_flg"])){ $filter_conditions += array('internal_pay_flg'=>$search["internal_pay_flg"]);  }

    $filter_goods_ctg = $this->GoodsCtgMst->find('all',array('conditions'=>array('del_kbn'=>EXISTS)));
    $filter_goods_kbn = $this->LatestGoodsMstView->find('all',array('fields'=>array("DISTINCT goods_kbn_id","goods_kbn_nm"),'conditions'=>array($filter_conditions)));
    $filter_vendors   = $this->LatestGoodsMstView->find('all',array('fields'=>array("DISTINCT vendor_id","vendor_nm"),      'conditions'=>array($filter_conditions)));

    $this->set("goods_ctg_data" ,$filter_goods_ctg);
    $this->set("goods_kbn_data" ,$filter_goods_kbn);
    $this->set("vendor_kbn_data",$filter_vendors);

 	$this->set("menu_customers","");
 	$this->set("menu_customer","disable");
 	$this->set("menu_fund","");

 	$this->set("sub_title","商品管理");
 	$this->set("user",$this->Auth->user());

 }

/**
  *
  *  商品登録画面表示及び実行
  */
 function addGoods()
 {
 	if(!empty($this->data))
 	{
 	  $tr = ClassRegistry::init('TransactionManager');
	  $tr->begin();

	  $this->layout = '';
 	  $this->autoRender =false;
 	  configure::write('debug', 0);

 	  $this->data['GoodsMst']['id'] = null;
 	  $this->data['GoodsMst']['year'] = GOODS_YEAR;
 	  $this->data['GoodsMst']['revision'] = 1;
 	  $this->data['GoodsMst']['goods_cd'] = $this->GoodsMst->getNewGoodsCode($this->data['GoodsMst']['goods_ctg_id'],GOODS_YEAR);
 	  $this->data['GoodsMst']['internal_pay_flg'] = isset($this->data['GoodsMst']['internal_pay_flg']) ? 1:0;
 	  $this->data['GoodsMst']['non_display_flg'] = isset($this->data['GoodsMst']['non_display_flg']) ? 1:0;
 	  $this->data['GoodsMst']['aw_share'] = $this->data['GoodsMst']['aw_share'] / 100;
      $this->data['GoodsMst']['rw_share'] = $this->data['GoodsMst']['rw_share'] / 100;
      $this->data['GoodsMst']['start_valid_dt'] = empty($this->data['GoodsMst']['start_valid_dt']) ? "1000-01-01" :$this->data['GoodsMst']['start_valid_dt'] ;
      $this->data['GoodsMst']['end_valid_dt']   = empty($this->data['GoodsMst']['end_valid_dt'])   ? "9999-12-31" :$this->data['GoodsMst']['end_valid_dt'] ;
      $this->data['GoodsMst']['tax']   = empty($this->data['GoodsMst']['tax'])   ? 0 :$this->data['GoodsMst']['tax'] / 100 ;
      $this->data['GoodsMst']['service_rate']   = empty($this->data['GoodsMst']['service_rate'])   ? 0 :$this->data['GoodsMst']['service_rate'] / 100 ;
      $this->data['GoodsMst']['profit_rate']   = empty($this->data['GoodsMst']['profit_rate'])   ? 0 :$this->data['GoodsMst']['profit_rate'] / 100 ;

 	  $this->data['GoodsMst']['reg_nm'] = $this->Auth->user('username');
      $this->data['GoodsMst']['reg_dt'] = date('Y-m-d H:i:s');
      $this->GoodsMst->create();
 	  if($this->GoodsMst->save($this->data)){
 	  	 $last_goods_id = $this->GoodsMst->getLastInsertID();
 	  	 $tr->commit();
  	     return json_encode(array('result'=>true,'message'=>'登録完了しました。','code'=>$this->data['GoodsMst']['goods_cd'],'newId'=>$last_goods_id,
  	                              'cost'=>$this->data['GoodsMst']['cost'],'price'=>$this->data['GoodsMst']['price'],'goodsName'=>$this->data['GoodsMst']['goods_nm']));
 	  }else{
 	   	 return json_encode(array('result'=>false,'message'=>"登録に失敗しました。",'reason'=>$this->GoodsMst->getDbo()->error."[".date('Y-m-d H:i:s')."]"));
 	  }
 	}
 	else{
 	  //商品分類リストの取得
 	  $this->set("goods_ctg_list",$this->GoodsCtgMst->find('all',array('conditions'=>array('del_kbn'=>EXISTS))));
 	  //業者リスト
 	  $this->set("vendor_list",$this->VendorMst->find('all',array('conditions'=>array('del_kbn'=>EXISTS))));
 	  //支払区分リスト
 	  $this->set("payment_kbn_list", $this->PaymentKbnMst->find('all',array('conditions'=>array('internal_payment_flg'=>0))));
 	  //環境設定情報を取得
 	  $this->set("env_data", $this->EnvMst->findById(1));

 	  $this->set("menu_customers","");
 	  $this->set("menu_customer","disable");
 	  $this->set("menu_fund","");

 	  $this->set("sub_title","商品追加");
 	  $this->set("user",$this->Auth->user());
 	}
 }

 /**
  *
  * セット商品登録画面表示及び実行
  */
 function addSetGoods()
 {
 	if(!empty($this->data))
 	{
 	 $tr = ClassRegistry::init('TransactionManager');
	 $tr->begin();

	 $this->layout = 'ajax';
 	 $this->autoRender =false;
 	 configure::write('debug', 0);

 	 //return json_encode(array('result'=>false,'message'=>"登録に失敗しました。",'reason'=>$this->data['GoodsMst']['cost'] ));

 	 //セット商品登録
 	 $this->data['GoodsMst']['year'] = GOODS_YEAR;
 	 $this->data['GoodsMst']['revision'] = 1;
 	 $this->data['GoodsMst']['goods_cd'] = $this->GoodsMst->getNewGoodsCode($this->data['GoodsMst']['goods_ctg_id'],GOODS_YEAR);
 	 $this->data['GoodsMst']['vendor_id'] = VC_UNKNOWN;
 	 $this->data['GoodsMst']['internal_pay_flg'] = isset($this->data['GoodsMst']['internal_pay_flg']) ? 1:0;
 	 $this->data['GoodsMst']['set_goods_kbn'] = SET_GOODS;
 	 $this->data['GoodsMst']['aw_share'] = $this->data['GoodsMst']['aw_share'] / 100;
     $this->data['GoodsMst']['rw_share'] = $this->data['GoodsMst']['rw_share'] / 100;
     $this->data['GoodsMst']['start_valid_dt'] = empty($this->data['GoodsMst']['start_valid_dt']) ? "1000-01-01" :$this->data['GoodsMst']['start_valid_dt'] ;
     $this->data['GoodsMst']['end_valid_dt']   = empty($this->data['GoodsMst']['end_valid_dt'])   ? "9999-12-31" :$this->data['GoodsMst']['end_valid_dt'] ;
 	 $this->data['GoodsMst']['reg_nm'] = $this->Auth->user('username');
     $this->data['GoodsMst']['reg_dt'] = date('Y-m-d H:i:s');

     $this->GoodsMst->create();

     if($this->GoodsMst->save($this->data)){

 	    $last_goods_id = $this->GoodsMst->getLastInsertID();
 	    //セット商品構成の登録
 	 	for($i=1;$i <= count($this->data['SetGoodsMst']);$i++)
 	    {
 	      if(!empty($this->data['SetGoodsMst'][$i]['goods_id']) &&  $this->data['SetGoodsMst'][$i]['goods_id'] !=0 )
 	      {
 	      $this->data['SetGoodsMst'][$i]['year'] = GOODS_YEAR;
 	      $this->data['SetGoodsMst'][$i]['set_goods_id'] = $last_goods_id;
 	      $this->data['SetGoodsMst'][$i]['reg_nm'] = $this->Auth->user('username');
 	      $this->data['SetGoodsMst'][$i]['reg_dt'] = date('Y-m-d H:i:s');
  	      $this->SetGoodsMst->create();

  	       if($this->SetGoodsMst->save($this->data['SetGoodsMst'][$i])==false){
  	           return json_encode(array('result'=>false,'message'=>"登録に失敗しました。",'reason'=>$this->GoodsMst->getDbo()->error."[".date('Y-m-d H:i:s')."]"));
  	       }
 	      }
 	    }
 	 }else{
  	     return json_encode(array('result'=>false,'message'=>"登録に失敗しました。",'reason'=>$this->GoodsMst->getDbo()->error."[".date('Y-m-d H:i:s')."]"));
 	 }
 	  $tr->commit();
  	  return json_encode(array('result'=>true,'message'=>'登録完了しました。','code'=>$this->data['GoodsMst']['goods_cd']));
 	}
 	else
 	{
 	  //商品分類リストの取得
 	  $this->set("goods_ctg_list",$this->GoodsCtgMst->find('all',array('conditions'=>array('del_kbn'=> EXISTS))));
 	  //業者リスト
 	  $this->set("vendor_list",$this->VendorMst->find('all',array('conditions'=>array('del_kbn'=> EXISTS))));
 	  //支払区分リスト
 	  $this->set("payment_kbn_list", $this->PaymentKbnMst->find('all',array('conditions'=>array('internal_payment_flg'=>0))));
 	  $this->set("menu_customers","");
 	  $this->set("menu_customer","disable");
 	  $this->set("menu_fund","");

   	  $this->set("sub_title","セット商品追加");
 	  $this->set("user",$this->Auth->user());
 	}
 }

 /**
  *
  * 商品編集・削除画面表示及び実行
  * @param 商品ID
  */
 function editGoods($id=null)
 {
 	if(!empty($this->data))
 	{
 	   $tr = ClassRegistry::init('TransactionManager');
	   $tr->begin();

	   $this->layout = 'ajax';
 	   $this->autoRender =false;
 	   configure::write('debug', 0);

 	  /* 削除 */
      if(strtoupper($this->params['form']['submit'])  ==  "DELETE")
      {
      	/* 全てのリビジョンを物理or論理削除する  */
      	$target = $this->GoodsMst->find('all',array('fields'=>array('id'),'conditions'=>array('goods_cd'=>$this->data['GoodsMst']['goods_cd'])));

      	for($i=0;$i < count($target);$i++){

      		$goods_id = $target[$i]['GoodsMst']['id'];

      		/* 見積又はセット商品の商品構成で既に使用されている場合は論理削除 */
      		if($this->EstimateDtlTrn->find('count',array('conditions'=>array('goods_id'=>$goods_id))) > 0 ||
      		   $this->SetGoodsMst->find('count',array('conditions'=>array('goods_id'=>$goods_id))) > 0){

      			$fields = array('del_kbn'=>DELETE,'del_nm'=>"'".$this->Auth->user('username')."'",'del_dt'=>"'".date('Y-m-d H:i:s')."'");
      			/* 商品マスタ */
      			if($this->GoodsMst->updateAll($fields,array('id'=>$goods_id))==false){
      				return json_encode(array('result'=>false,'message'=>"削除に失敗しました。",'reason'=>$this->GoodsMst->getDbo()->error."[".date('Y-m-d H:i:s')."]"));
      			}
      			/* 物理削除 */
      		}else{

      			if($this->GoodsMst->delete($goods_id)==false){
      				return json_encode(array('result'=>false,'message'=>"削除に失敗しました。",'reason'=>$this->GoodsMst->getDbo()->error."[".date('Y-m-d H:i:s')."]"));
      			}
      		}
      	}
      }
      /* 更新 */
      else if(strtoupper($this->params['form']['submit'])  ==  "UPDATE")
      {
      	/*
        $fields = array('vendor_id','goods_nm','price','cost','aw_share','rw_share','currency_kbn','internal_pay_flg','church_code','upd_nm','upd_dt');

     	$this->GoodsMst->id = $this->data['GoodsMst']['id'];
     	$this->data['GoodsMst']['internal_pay_flg'] = isset($this->data['GoodsMst']['internal_pay_flg']) ? 1:0;
        $this->data['GoodsMst']['aw_share'] = $this->data['GoodsMst']['aw_share'] / 100;
        $this->data['GoodsMst']['rw_share'] = $this->data['GoodsMst']['rw_share'] / 100;
        $this->data['GoodsMst']['upd_nm'] = $this->Auth->user('username');
 	    $this->data['GoodsMst']['upd_dt'] = date('Y-m-d H:i:s');
 	    if($this->GoodsMst->save($this->data['GoodsMst'],false,$fields)==false){
 	    	return json_encode(array('result'=>false,'message'=>"更新に失敗しました。",'reason'=>$this->GoodsMst->getDbo()->error."[".date('Y-m-d H:i:s')."]"));
 	    }*/
      	$this->data['GoodsMst']['id'] = null;
      	$this->data['GoodsMst']['year'] = GOODS_YEAR;
      	$this->data['GoodsMst']['revision'] = $this->data['GoodsMst']['revision'] + 1;
       	$this->data['GoodsMst']['internal_pay_flg'] = isset($this->data['GoodsMst']['internal_pay_flg']) ? 1:0;
       	$this->data['GoodsMst']['non_display_flg']  = isset($this->data['GoodsMst']['non_display_flg']) ? 1:0;
      	$this->data['GoodsMst']['aw_share'] = $this->data['GoodsMst']['aw_share'] / 100;
      	$this->data['GoodsMst']['rw_share'] = $this->data['GoodsMst']['rw_share'] / 100;
      	$this->data['GoodsMst']['start_valid_dt'] = empty($this->data['GoodsMst']['start_valid_dt']) ? "1000-01-01" :$this->data['GoodsMst']['start_valid_dt'] ;
      	$this->data['GoodsMst']['end_valid_dt']   = empty($this->data['GoodsMst']['end_valid_dt'])   ? "9999-12-31" :$this->data['GoodsMst']['end_valid_dt'] ;
      	$this->data['GoodsMst']['tax']   = empty($this->data['GoodsMst']['tax'])   ? 0 :$this->data['GoodsMst']['tax'] / 100 ;
      	$this->data['GoodsMst']['service_rate']   = empty($this->data['GoodsMst']['service_rate'])   ? 0 :$this->data['GoodsMst']['service_rate'] / 100 ;
      	$this->data['GoodsMst']['profit_rate']   = empty($this->data['GoodsMst']['profit_rate'])   ? 0 :$this->data['GoodsMst']['profit_rate'] / 100 ;
      	$this->data['GoodsMst']['reg_nm'] = $this->Auth->user('username');
      	$this->data['GoodsMst']['reg_dt'] = date('Y-m-d H:i:s');
      	$this->GoodsMst->create();
      	if($this->GoodsMst->save($this->data['GoodsMst'])==false){
      		return json_encode(array('result'=>false,'message'=>"更新に失敗しました。",'reason'=>$this->GoodsMst->getDbo()->error."[".date('Y-m-d H:i:s')."]"));
      	}

      	//非表示フラグの更新
      	if($this->GoodsMst->updateAll(array('non_display_flg'=>$this->data['GoodsMst']['non_display_flg']),array('goods_cd'=>$this->data['GoodsMst']['goods_cd']))==false){
      		return json_encode(array('result'=>false,'message'=>"更新に失敗しました。",'reason'=>$this->GoodsMst->getDbo()->error."[".date('Y-m-d H:i:s')."]"));
      	}
      }
      /* 異常パラメーター */
      else{
         return json_encode(array('result'=>false,'message'=>"処理が失敗しました。",'reason'=>"予期しないコード[".$this->params['form']['submit']."]です。 "));
      }
         $last_goods_id = $this->GoodsMst->getLastInsertID();
 	     $tr->commit();
         return json_encode(array('result'=>true,'message'=>'処理完了しました。','newId'=>$last_goods_id,'newRevision'=>$this->data['GoodsMst']['revision']));
 	}
 	else{
 	  //該当商品
 	  $data = $this->LatestGoodsMstView->findById($id);
 	  $this->set("data",$data);
      //業者リスト
 	  $this->set("vendor_list",$this->VendorMst->find('all',array('conditions'=>array('del_kbn'=>EXISTS))));
 	  //支払区分リスト
 	  $this->set("payment_kbn_list", $this->PaymentKbnMst->find('all',array('conditions'=>array('internal_payment_flg'=>$data['LatestGoodsMstView']['internal_pay_flg']))));

      $this->set("menu_customers","");
 	  $this->set("menu_customer","disable");
 	  $this->set("menu_fund","");

 	  $this->set("sub_title","商品編集");
 	  $this->set("user",$this->Auth->user());
 	}
 }

/**
 *
 * セット商品編集・削除画面表示及び実行
 * @param $id
 */
 function editSetGoods($id=null)
 {
 	if(!empty($this->data))
 	{
 	   $tr = ClassRegistry::init('TransactionManager');
	   $tr->begin();

	   $this->layout = '';
 	   $this->autoRender =false;
 	   configure::write('debug', 0);

 	  //削除
      if(strtoupper($this->params['form']['submit'])  ==  "DELETE"){

      	$target = $this->GoodsMst->find('all',array('fields'=>array('id'),'conditions'=>array('goods_cd'=>$this->data['GoodsMst']['goods_cd'])));

      	for($i=0;$i < count($target);$i++){

      		$goods_id = $target[$i]['GoodsMst']['id'];

      		/* 見積で既に使用されている場合は論理削除 */
      		if($this->EstimateDtlTrn->find('count',array('conditions'=>array('goods_id'=>$goods_id))) > 0){

      			$fields = array('del_kbn'=>DELETE,'del_nm'=>"'".$this->Auth->user('username')."'",'del_dt'=>"'".date('Y-m-d H:i:s')."'");
      			/* 商品マスタ */
      			if($this->GoodsMst->updateAll($fields,array('id'=>$goods_id))==false){
      				return json_encode(array('result'=>false,'message'=>"削除に失敗しました。",'reason'=>$this->GoodsMst->getDbo()->error."[".date('Y-m-d H:i:s')."]"));
      			}

      			/* セット商品マスタ */
      			if($this->SetGoodsMst->updateAll($fields,array('set_goods_id'=>$goods_id))==false){
      				return json_encode(array('result'=>false,'message'=>"削除に失敗しました。",'reason'=>$this->SetGoodsMst->getDbo()->error."[".date('Y-m-d H:i:s')."]"));
      			}
      		/* 物理削除 */
      		}else{

      			if($this->SetGoodsMst->deleteAll(array('set_goods_id'=>$goods_id))==false){
      				return json_encode(array('result'=>false,'message'=>"削除に失敗しました。",'reason'=>$this->SetGoodsMst->getDbo()->error."[".date('Y-m-d H:i:s')."]"));
      			}
      			if($this->GoodsMst->delete($goods_id)==false){
      				return json_encode(array('result'=>false,'message'=>"削除に失敗しました。",'reason'=>$this->GoodsMst->getDbo()->error."[".date('Y-m-d H:i:s')."]"));
      			}
      		}
      	}
      }
      //新規登録(既存のデータをコピーして追加する)
      else if(strtoupper($this->params['form']['submit'])  ==  "ADD"){

      	  //セット商品登録
      	 $this->data['GoodsMst']['id'] = null;
      	 $this->data['GoodsMst']['revision'] = 1;
      	 $this->data['GoodsMst']['year'] = GOODS_YEAR;
      	 $this->data['GoodsMst']['goods_cd'] = $this->GoodsMst->getNewGoodsCode($this->data['GoodsMst']['goods_ctg_id'],GOODS_YEAR);
 	     $this->data['GoodsMst']['vendor_id'] = VC_UNKNOWN;
 	     $this->data['GoodsMst']['internal_pay_flg'] = isset($this->data['GoodsMst']['internal_pay_flg']) ? 1:0;
 	     $this->data['GoodsMst']['set_goods_kbn'] = 1;
 	     $this->data['GoodsMst']['year'] = GOODS_YEAR;
 	  	 $this->data['GoodsMst']['aw_share'] = $this->data['GoodsMst']['aw_share'] / 100;
         $this->data['GoodsMst']['rw_share'] = $this->data['GoodsMst']['rw_share'] / 100;
         $this->data['GoodsMst']['start_valid_dt'] = empty($this->data['GoodsMst']['start_valid_dt']) ? "1000-01-01" :$this->data['GoodsMst']['start_valid_dt'] ;
         $this->data['GoodsMst']['end_valid_dt']   = empty($this->data['GoodsMst']['end_valid_dt'])   ? "9999-12-31" :$this->data['GoodsMst']['end_valid_dt'] ;
 	     $this->data['GoodsMst']['reg_nm'] = $this->Auth->user('username');
         $this->data['GoodsMst']['reg_dt'] = date('Y-m-d H:i:s');
         $this->GoodsMst->create();

         if($this->GoodsMst->save($this->data['GoodsMst'])){

 	       $last_goods_id = $this->GoodsMst->getLastInsertID();

 	      //セット商品構成の登録
 	 	   for($i=1;$i <= count($this->data['SetGoodsMst']);$i++){

 	         if(!empty($this->data['SetGoodsMst'][$i]['goods_id']) &&  $this->data['SetGoodsMst'][$i]['goods_id'] !=0 ){

 	           $this->data['SetGoodsMst'][$i]['id'] = null;
 	           $this->data['SetGoodsMst'][$i]['set_goods_id'] = $last_goods_id;
 	           $this->data['SetGoodsMst'][$i]['year'] = GOODS_YEAR;
 	           $this->data['SetGoodsMst'][$i]['no'] = $i;
 	           $this->data['SetGoodsMst'][$i]['reg_nm'] = $this->Auth->user('username');
 	           $this->data['SetGoodsMst'][$i]['reg_dt'] = date('Y-m-d H:i:s');
  	           $this->SetGoodsMst->create();

 	           if($this->SetGoodsMst->save($this->data['SetGoodsMst'][$i])==false){
 	           	return json_encode(array('result'=>false,'message'=>"登録に失敗しました。",'reason'=>$this->SetGoodsMst->getDbo()->error."[".date('Y-m-d H:i:s')."]"));
 	           }
 	         }
           }
 	    }else{
 	    	return json_encode(array('result'=>false,'message'=>"登録に失敗しました。",'reason'=>$this->GoodsMst->getDbo()->error."[".date('Y-m-d H:i:s')."]"));
 	    }
     }
     //更新
    else if(strtoupper($this->params['form']['submit'])  ==  "UPDATE"){

    	//セット商品登録
    	$this->data['GoodsMst']['id'] = null;
    	$this->data['GoodsMst']['year'] = GOODS_YEAR;
    	$this->data['GoodsMst']['revision'] = $this->data['GoodsMst']['revision']+1;
    	$this->data['GoodsMst']['vendor_id'] = VC_UNKNOWN;
    	$this->data['GoodsMst']['internal_pay_flg'] = isset($this->data['GoodsMst']['internal_pay_flg']) ? 1:0;
    	$this->data['GoodsMst']['set_goods_kbn'] = SET_GOODS;
    	$this->data['GoodsMst']['start_valid_dt'] = empty($this->data['GoodsMst']['start_valid_dt']) ? "1000-01-01" :$this->data['GoodsMst']['start_valid_dt'] ;
    	$this->data['GoodsMst']['end_valid_dt']   = empty($this->data['GoodsMst']['end_valid_dt'])   ? "9999-12-31" :$this->data['GoodsMst']['end_valid_dt'] ;
    	$this->data['GoodsMst']['aw_share'] = $this->data['GoodsMst']['aw_share'] / 100;
    	$this->data['GoodsMst']['rw_share'] = $this->data['GoodsMst']['rw_share'] / 100;
    	$this->data['GoodsMst']['reg_nm'] = $this->Auth->user('username');
    	$this->data['GoodsMst']['reg_dt'] = date('Y-m-d H:i:s');

    	$this->GoodsMst->create();

    	if($this->GoodsMst->save($this->data)){

    		$last_goods_id = $this->GoodsMst->getLastInsertID();

    		//セット商品構成の登録
    		for($i=1;$i <= count($this->data['SetGoodsMst']);$i++){

    			if(!empty($this->data['SetGoodsMst'][$i]['goods_id']) &&  $this->data['SetGoodsMst'][$i]['goods_id'] !=0 ){

    		         $this->data['SetGoodsMst'][$i]['year'] = GOODS_YEAR;
    		         $this->data['SetGoodsMst'][$i]['set_goods_id'] = $last_goods_id;
 	                 $this->data['SetGoodsMst'][$i]['reg_nm'] = $this->Auth->user('username');
    	 	      	 $this->data['SetGoodsMst'][$i]['reg_dt'] = date('Y-m-d H:i:s');
    	 	      	 $this->SetGoodsMst->create();

    	 	      	 if($this->SetGoodsMst->save($this->data['SetGoodsMst'][$i])==false){
    	  	           return json_encode(array('result'=>false,'message'=>"更新に失敗しました。",'reason'=>$this->GoodsMst->getDbo()->error."[".date('Y-m-d H:i:s')."]"));
    		         }
 	            }
    		}
    	}else{
    		return json_encode(array('result'=>false,'message'=>"更新に失敗しました。",'reason'=>$this->GoodsMst->getDbo()->error."[".date('Y-m-d H:i:s')."]"));
    	}

     	/* 商品テーブルの更新
     	$fields = array('goods_nm','price','cost','aw_share','rw_share','currency_kbn','internal_pay_flg','upd_nm','upd_dt');

     	$this->GoodsMst->id = $this->data['GoodsMst']['id'];
     	$this->data['GoodsMst']['internal_pay_flg'] = isset($this->data['GoodsMst']['internal_pay_flg']) ? 1:0;
        $this->data['GoodsMst']['aw_share'] = $this->data['GoodsMst']['aw_share'] / 100;
        $this->data['GoodsMst']['rw_share'] = $this->data['GoodsMst']['rw_share'] / 100;
        $this->data['GoodsMst']['upd_nm'] = $this->Auth->user('username');
 	    $this->data['GoodsMst']['upd_dt'] = date('Y-m-d H:i:s');
 	    if($this->GoodsMst->save($this->data,false,$fields)==false){
 	    	return json_encode(array('result'=>false,'message'=>"更新に失敗しました。",'reason'=>$this->GoodsMst->getDbo()->error."[".date('Y-m-d H:i:s')."]"));
 	    }*/

 	    /* セット商品テーブルの更新
        $setl_fields = array('no','set_goods_id','goods_id','num','upd_nm','upd_dt');
 	    //新規追加または更新したセット商品IDを保持
        $saving_id= array();
 	    //0行目はヘッダ用にダミーにしているので1行目から始める
        for($i=1;$i <= count($this->data['SetGoodsMst']);$i++)
        {
          //クライアント側でもチェックしているが、空の行があったらスキップする
          if(!empty($this->data['SetGoodsMst'][$i]['goods_id']) && $this->data['SetGoodsMst'][$i]['goods_id'] !=0  )
          {
          	 $this->data['SetGoodsMst'][$i]['no'] = $i;

 	         //IDがNULLの場合はクライアント側で新規に追加した項目なので新規作成する
 	         if(empty($this->data['SetGoodsMst'][$i]['id']))
 	         {
 	           $this->data['SetGoodsMst'][$i]['set_goods_id'] =  $this->data['GoodsMst']['id'];
 	           $this->data['SetGoodsMst'][$i]['year'] = GOODS_YEAR;
 	           $this->data['SetGoodsMst'][$i]['reg_nm'] = $this->Auth->user('username');
 	           $this->data['SetGoodsMst'][$i]['reg_dt'] = date('Y-m-d H:i:s');
 	           $this->SetGoodsMst->create();
 	           if($this->SetGoodsMst->save($this->data['SetGoodsMst'][$i])==false){
 	           	  return json_encode(array('result'=>false,'message'=>"更新に失敗しました。",'reason'=>$this->SetGoodsMst->getDbo()->error."[".date('Y-m-d H:i:s')."]"));
 	           }

 	           //新規作成したデータのIDを保存
 	           $last_set_goods_id = $this->SetGoodsMst->getLastInsertID();
 	           array_push($saving_id, $last_set_goods_id);
 	         }
 	         //既存の明細の更新
 	         else
 	        {
 	           //削除されず残っているデータのIDを保存
 	           array_push($saving_id,$this->data['SetGoodsMst'][$i]['id']);
 	           $this->data['SetGoodsMst'][$i]['upd_nm'] = $this->Auth->user('username');
 	           $this->data['SetGoodsMst'][$i]['upd_dt'] = date('Y-m-d H:i:s');
 	           $this->SetGoodsMst->id = $this->data['SetGoodsMst'][$i]['id'];
 	            if($this->SetGoodsMst->save($this->data['SetGoodsMst'][$i],false,$setl_fields)==false){
 	            	return json_encode(array('result'=>false,'message'=>"更新に失敗しました。",'reason'=>$this->SetGoodsMst->getDbo()->error."[".date('Y-m-d H:i:s')."]"));
 	            }
 	          }
 	     }
 	   }
 	    //新規追加でも既存の行の更新でもないデータはクライアント側で削除指定されたデータなのですべて削除
 	    if($this->SetGoodsMst->deleteAll( array('set_goods_id'=>$this->data['GoodsMst']['id'],'NOT'=>array('id'=>$saving_id)))==false){
 	    	return json_encode(array('result'=>false,'message'=>"更新に失敗しました。",'reason'=>$this->GoodsMst->getDbo()->error."[".date('Y-m-d H:i:s')."]"));
 	    }*/
    }else{
       return json_encode(array('result'=>false,'message'=>"処理が失敗しました。",'reason'=>"予期しないコード[".$this->params['form']['submit']."]です。 "));
    }

    $tr->commit();
    return json_encode(array('result'=>true,'message'=>'処理完了しました。',
    		                 'newID'=>$this->GoodsMst->getLastInsertID(),
    		                 'newSetRevision'=>$this->data['GoodsMst']['revision'],
    		                 'newSetGoodsCd'=>$this->data['GoodsMst']['goods_cd']));
 	}
   else{
 	//業者リスト
 	$this->set("vendor_list",$this->VendorMst->find('all',array('conditions'=>array('del_kbn'=>EXISTS))));
 	//商品分類リストの取得
 	$this->set("goods_ctg_list",$this->GoodsCtgMst->find('all',array('conditions'=>array('del_kbn'=>EXISTS))));

 	$goods = $this->LatestSetGoodsMstView->find('all',array("conditions"=>array("set_goods_id"=>$id)));
 	$this->set("goods",$goods);

 	//支払区分リスト
 	$this->set("payment_kbn_list", $this->PaymentKbnMst->find('all',array('conditions'=>array('internal_payment_flg'=>$goods[0]['LatestSetGoodsMstView']['set_internal_pay_flg']))));
 	$this->set("menu_customers","");
 	$this->set("menu_customer","disable");
 	$this->set("menu_fund","");

 	$this->set("sub_title","セット商品編集");
 	$this->set("user",$this->Auth->user());
   }
 }

 /**
  *
  * 同じ商品分類ＩＤをもつ商品区分データを取得する
  * @param $ctg_id
  */
 function goodsKbnList($ctg_id="")
 {
     if (!$this->RequestHandler->isAjax()) { die('Not found'); }

     $data = $this->GoodsKbnMst->find('all',array('conditions'=>array('GoodsKbnMst.goods_ctg_id'=>$ctg_id,'del_kbn'=>EXISTS)));

 	 configure::write('debug', 0);
	 $this->layout = '';
	 $this->set('goods', $data);
 }

 function paymentKbnList($internal_payment_flg=0)
 {
 	if (!$this->RequestHandler->isAjax()) { die('Not found'); }

 	$data = $this->PaymentKbnMst->find('all',array('conditions'=>array('internal_payment_flg'=>$internal_payment_flg)));

 	configure::write('debug', 0);
 	$this->layout = '';
 	$this->set('payment_kbn_list', $data);
 }

/**
 *
 * 商品リスト選択フォーム表示
 * @param $goods_ctg_id
 * @param $goods_kbn_id
 * @param $current_line_no
 */
 function goodsDetailForm($goods_ctg_id,$goods_kbn_id,$current_line_no)
 {
    if (!$this->RequestHandler->isAjax()){ die('Not found');}

	configure::write('debug', 0);
	$this->layout = '';
	$this->set('current_line_no',$current_line_no);
	$this->set('goods_ctg_id',$goods_ctg_id);
	$this->set('goods_kbn_id',$goods_kbn_id);
 }

 /**
  *
  * 商品区分IDの商品リストをJSONデータで取得する
  * @param $goods_kbn_id
  */
 function FeedGoodsList($goods_kbn_id)
 {
 	 if (!$this->RequestHandler->isAjax()){ die('Not found');}

 	 $this->layout = '';
 	 $this->autoRender =false;
 	 configure::write('debug', 0);

 	 $data = $this->GoodsMstView->find('all',array('conditions'=>array('goods_kbn_id'=>$goods_kbn_id,'year'=>GOODS_YEAR,'set_goods_kbn'=>UNSET_GOODS,'del_kbn'=>EXISTS),'order'=>array('goods_cd','revision desc')));

 	 $current_goods_cd = "";
 	 $dummy_key = "";
 	 $comps = array();
 	 for($i=0;$i < count($data);$i++){

 	 	if(empty($current_goods_cd) || $current_goods_cd != $data[$i]['GoodsMstView']['goods_cd']){
 	 		$current_goods_cd = $data[$i]['GoodsMstView']['goods_cd'];
 	 		$dummy_key = $data[$i]['GoodsMstView']['goods_cd']."    ".$data[$i]['GoodsMstView']['goods_nm'];
 	 	}

 	 	$sign = $data[$i]['GoodsMstView']['currency_kbn'] == FOREIGN ? "$" : "￥";
 	 	array_push($comps, array("id" => $data[$i]['GoodsMstView']['id'], "cell" => array($dummy_key,
 	 	                                                                                  $data[$i]['GoodsMstView']['goods_cd'],
 	 	                                                                                  $data[$i]['GoodsMstView']['goods_nm'],
 	                                                                                      $data[$i]['GoodsMstView']['revision'],
                                                                                          $data[$i]['GoodsMstView']['price'] == null ? $sign."0" : $sign.$data[$i]['GoodsMstView']['price'],
                                                                                          $data[$i]['GoodsMstView']['cost']  == null ? $sign."0" : $sign.$data[$i]['GoodsMstView']['cost'],
                                                                                          $data[$i]['GoodsMstView']['vendor_nm'])
                                  )
                   );
 	 }
    return json_encode(array('rows' => $comps));
 }

 /**
  *
  * 新規商品登録フォーム表示
  * @param $goods_ctg_id
  * @param $goods_kbn_id
  */
 function GoodsAdditionForm($goods_ctg_id,$goods_kbn_id,$current_line_no)
 {
 	 if (!$this->RequestHandler->isAjax()){ die('Not found');}
 	 configure::write('debug', 0);
	 $this->layout = '';

	 $this->set("user",$this->Auth->user());
	 //商品分類リストの取得
 	 $this->set("goods_ctg",$this->GoodsCtgMst->findById($goods_ctg_id));
 	 //商品区分リスト
 	 $this->set("goods_kbn",$this->GoodsKbnMst->findById($goods_kbn_id));
 	 //業者リスト
 	 $this->set("vendor_list",$this->VendorMst->find('all',array('conditions'=>array('del_kbn'=>EXISTS))));
 	 //年度
 	 //$this->set("year",$this->Session->read("goods_mst_year"));
 	 $this->set('current_line_no',$current_line_no);
 }

 /**
  *
  * [AJAX]EXCELファイルアップロード画面を表示する
  */
 function fileUploadForm() {
 	if (!$this->RequestHandler->isAjax()){ $this->cakeError('error404'); }
 	configure::write('debug', 0);
 	$this->layout = '';
 }

 /**
  * EXCELファイル取り込み
  */
 function uploadFile()
 {
 	$this->layout = "";
 	if (is_uploaded_file($this->data['ImgForm']['ImgFile']['tmp_name']) && end(explode(".",$this->data['ImgForm']['ImgFile']['name'])) == "xlsx") {

 		//アップロードファイルを仮保存
 		$ret = $this->GoodsService->uploadFile($this->data['ImgForm']['ImgFile']['tmp_name'],$this->data['ImgForm']['ImgFile']['name']);
 		if($ret['result']==false){
 			$this->set("msg",json_encode(array('data'=>array('isSuccess'=>"false", 'message'=>$ret['message']))));
 			return;
 		}
 		$tr = ClassRegistry::init('TransactionManager');
 		$tr->begin();

 		//ファイルから商品マスタを更新
 		$ret = $this->GoodsService->updateByFile($ret['filePath'],$this->Auth->user('username'));
 		if($ret['result']==true){
 			$this->set("msg",json_encode(array('data'=>array('isSuccess'=>"true",'message'=>"ファイル取り込みに成功しました。  ".$ret["message"]))));
 			$tr->commit();
 		}else{
 			$this->set("msg",json_encode(array('data'=>array('isSuccess'=>"false", 'message'=> $ret["message"],"reasons"=>$ret['errors']))));
 		}
 	}else{
 		$this->set("msg",json_encode(array('data'=>array('isSuccess'=>"false", 'message'=> "ファイルの種類が違います。(EXCELファイル) ファイルサイズの上限は128Mです。"))));
 	}
 }

 /**
  * 商品マスタのEXCEL出力
  */
 function export(){

 	$search = array('year'=>GOODS_YEAR,'del_kbn'=>EXISTS);

 	if($this->Session->read('filter_goods_mst_ctg_id') != -1){
 		$search += array("goods_ctg_id"=>$this->Session->read('filter_goods_mst_ctg_id'));
 	}
 	if($this->Session->read('filter_goods_mst_kbn_id') != -1){
 		$search += array("goods_kbn_id"=>$this->Session->read('filter_goods_mst_kbn_id'));
 	}
 	if($this->Session->read('filter_goods_mst_set_id') != -1){
 		$search += array("set_goods_kbn"=>$this->Session->read('filter_goods_mst_set_id'));
 	}
 	if($this->Session->read('filter_goods_mst_vendor_id') != -1){
 		$search += array("vendor_id"=>$this->Session->read('filter_goods_mst_vendor_id'));
 	}
 	if($this->Session->read('filter_goods_mst_internal_pay_id') != -1){
 		$search += array("internal_pay_flg"=>$this->Session->read('filter_goods_mst_internal_pay_id'));
 	}
/*
 	$this->paginate = array (
 			'LatestGoodsMstView' => array(
 					'limit' => 20,
 					'page'=>$this->Session->read('filter_goods_mst_page'),
 					'sort'=>$this->Session->read('filter_goods_mst_sort'),
 					'conditions'=>array('year'=>GOODS_YEAR,'del_kbn'=>EXISTS)
 			)
 	);

 	$this->set("data",$this->paginate('LatestGoodsMstView',$search));
 */
    $this->set("data",$this->LatestGoodsMstView->find('all',array('conditions'=>$search)));
    //$this->set("set_data",$this->LatestSetGoodsMstView->find('all'));

 	$temp_filename = "goods_mst_template.xlsx";
 	$save_filename = mb_convert_encoding("商品マスタ", "SJIS", "AUTO").date('Ymd').".xlsx";

 	$this->layout = false;
 	$this->set( "sheet_name", "商品マスタ" );
 	$this->set( "filename", $save_filename );
 	$this->set( "template_file", $temp_filename);
 	$this->render("excel");
 }



 /**
  * 引数の回数未満しか見積で使用されていない商品を削除する(一度も使用されていない商品以外は論理削除)
  * @param unknown $count
  * @return multitype:boolean string |multitype:boolean NULL
  */
 /*
 function deleteGoodsUsingLessThan($count=3){

 	if (!$this->RequestHandler->isAjax()){ 	$this->cakeError("error404");  }

 	$this->layout = 'ajax';
 	$this->autoRender =false;
 	configure::write('debug', 0);

 	$ret = $this->GoodsService->deleteGoodsUsingLessThan($count);
 	return json_encode($ret);
 }*/
}
?>