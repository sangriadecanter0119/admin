<?php
class GoodsKbnMasterController extends AppController
{

 public $name = 'GoodsKbnMaster';
 public $uses = array('GoodsKbnMst','GoodsKbnMstView','GoodsCtgMst','GoodsMst');
 public $layout = 'edit_mode';
 public $components = array('Auth','RequestHandler');
 public $helpers = array('Html','common','Javascript');


 /**
  * 商品区分一覧画面表示
  */
 function index()
 {
 	$search = array();

	/* フォームからフィルタ条件を送信された場合(POST) */
 	if (!empty($this->data)) {
			//$search = $this->data['GoodsKbnMstView']['goods_ctg_id'];

			$this->Session->write('filter_goods_ctg_mst_id',$this->data['GoodsKbnMstView']['goods_ctg_id']);
			$this->Session->write('filter_goods_ctg_mst_page',1);
	}
	/* ソートリンクからフィルタ条件を引き継ぐ場合(GET) */
	else if(!empty($this->params['named']['goods_ctg_id'])) {
			//$search = $this->params['named']['goods_ctg_id'];

			$this->Session->write('filter_goods_ctg_mst_id',$this->params['named']['goods_ctg_id']);
			$this->Session->write('filter_goods_ctg_mst_page',$this->params['named']['page']);
			if(isset($this->params['named']['sort'])){
				$this->Session->write('filter_goods_ctg_mst_sort',$this->params['named']['sort']);
			}
	}

	if($this->Session->read('filter_goods_ctg_mst_id') != -1){
		$search += array("goods_ctg_id"=>$this->Session->read('filter_goods_ctg_mst_id'));
	}

	$this->paginate = array (
 	    'GoodsKbnMstView' => array(
		                  'limit' => 30,
 	    		          'page'=>$this->Session->read('filter_goods_ctg_mst_page'),
		                  'sort'=>$this->Session->read('filter_goods_ctg_mst_sort'),
 	                      'conditions'=>array('del_kbn'=>EXISTS)
 	                      )
	    );

	/*
 	 * フィルタ条件をVIEWで保持する
 	 *  -1:ALL
 	 */
  	$this->set("goods_ctg_id",!isset($search["goods_ctg_id"]) ? "-1" : $search["goods_ctg_id"]);

  	if(empty($search["goods_ctg_id"])){

  		$this->set("data",$this->paginate('GoodsKbnMstView'));
  	}else{
  		$this->set("data",$this->paginate('GoodsKbnMstView',$search));
  	}

 	$this->set("goods_ctg_data",$this->GoodsCtgMst->find('all'));

 	$this->set("menu_customers","");
 	$this->set("menu_customer","disable");
 	$this->set("menu_fund","");

 	$this->set("sub_title","商品区分管理");
 	$this->set("user",$this->Auth->user());

 }

 /**
  * 商品区分登録画面表示及び実行
  */
 function addGoodsKbn()
 {
 	if(!empty($this->data))
 	{
 	   $tr = ClassRegistry::init('TransactionManager');
	   $tr->begin();

	   $this->layout = '';
 	   $this->autoRender =false;
 	   configure::write('debug', 0);

 	   $this->data['GoodsKbnMst']['id'] = null;
 	   $this->data['GoodsKbnMst']['reg_nm'] = $this->Auth->user('username');
       $this->data['GoodsKbnMst']['reg_dt'] = date('Y-m-d H:i:s');

 	   if($this->GoodsKbnMst->save($this->data)==false){
 	   	 return json_encode(array('result'=>false,'message'=>"登録に失敗しました。",'reason'=>$this->GoodsKbnMst->getDbo()->error."[".date('Y-m-d H:i:s')."]"));
 	   }
 	  	 $tr->commit();
  	     return json_encode(array('result'=>true,'message'=>'登録完了しました。'));
 	}else{

 	  $this->set("menu_customers","");
 	  $this->set("menu_customer","disable");
 	  $this->set("menu_fund","");

 	  //商品分類テーブルの取得
 	  $this->set("goods_ctg_list",$this->GoodsCtgMst->find('all',array('conditions'=>array('del_kbn'=> EXISTS))));
 	  $this->set("sub_title","商品区分追加");
 	  $this->set("user",$this->Auth->user());
   }
 }

 /**
  * 商品区分編集・削除画面表示及び実行
  */
 function editGoodsKbn($id=null)
 {
 	if(!empty($this->data))
 	{
 	   $tr = ClassRegistry::init('TransactionManager');
	   $tr->begin();

	   $this->layout = '';
 	   $this->autoRender =false;
 	   configure::write('debug', 0);

      /* 削除 */
      if(strtoupper($this->params['form']['submit'])  ==  "DELETE")
      {
      	//論理削除
      	if($this->GoodsMst->find('count',array('conditions'=>array('goods_kbn_id'=>$this->data['GoodsKbnMst']['id']))) > 0){

      		if($this->GoodsKbnMst->save(array('GoodsKbnMst'=>array('id'=>$this->data['GoodsKbnMst']['id'],'del_kbn' =>DELETE)),false,array("del_kbn"))==false){
      			return json_encode(array('result'=>false,'message'=>"削除に失敗しました。",'reason'=>$this->GoodsKbnMst->getDbo()->error."[".date('Y-m-d H:i:s')."]"));
      		}
      	//物理削除
      	}else{
      		if($this->GoodsKbnMst->delete($this->data['GoodsKbnMst']['id'])==false){
      			return json_encode(array('result'=>false,'message'=>"削除に失敗しました。",'reason'=>$this->GoodsKbnMst->getDbo()->error."[".date('Y-m-d H:i:s')."]"));
      		}
      	}
      }
      /* 更新 */
      else if(strtoupper($this->params['form']['submit'])  ==  "UPDATE")
      {
     	 $this->data['GoodsKbnMst']['upd_nm'] = $this->Auth->user('username');
         $this->data['GoodsKbnMst']['upd_dt'] = date('Y-m-d H:i:s');
 	     if($this->GoodsKbnMst->save($this->data['GoodsKbnMst'])==false){
 	    	return json_encode(array('result'=>false,'message'=>"更新に失敗しました。",'reason'=>$this->GoodsKbnMst->getDbo()->error."[".date('Y-m-d H:i:s')."]"));
 	     }

      /* 異常パラメーター */
      }else{
      	    return json_encode(array('result'=>false,'message'=>"処理が失敗しました。",'reason'=>"予期しないコード[".$this->params['form']['submit']."]です。 "));
      }
        $tr->commit();
        return json_encode(array('result'=>true,'message'=>'処理完了しました。'));
    }else{

 	  $this->set("data",$this->GoodsKbnMst->findById($id));
 	  $this->set("menu_customers","");
 	  $this->set("menu_customer","disable");
 	  $this->set("menu_fund","");
 	  //商品分類テーブルの取得
   	  $this->set("goods_ctg_list",$this->GoodsCtgMst->find('all'));
 	  $this->set("sub_title","商品区分編集");
 	  $this->set("user",$this->Auth->user());
    }
 }

}
?>
