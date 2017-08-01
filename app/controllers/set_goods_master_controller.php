<?php
class SetGoodsMasterController extends AppController
{

 public $name = 'SetGoodsMaster';
 public $uses = array('SetGoodsMst','SetGoodsMstView','GoodsMst');
 public $layout = 'edit_mode';
 public $components = array('Auth');
 public $helpers = array('common');

 //セット商品一覧画面
 function index()
 {
 	$this->set("data",$this->SetGoodsMstView->find('all'));

 	$this->set("sub_title","セット商品管理");
 	$this->set("user",$this->Auth->user());

 }

 //セット商品登録画面
 function addSetGoods()
 {
 	if(!empty($this->data))
 	{
 	 $this->SetGoodsMst->save($this->data);
 	}

 	//商品リストの取得
 	$this->set("goods_list",$this->GoodsMst->find('all',array('conditions'=>array('GoodsMst.set_goods_kbn'=>'0'))));
 	//セット商品リストの取得
 	$this->set("set_goods_list",$this->GoodsMst->find('all',array('conditions'=>array('GoodsMst.set_goods_kbn'=>'1'))));

 	$this->set("sub_title","セット商品追加");
 	$this->set("user",$this->Auth->user());
 }

 //セット商品編集・削除画面
 function editSetGoods($id)
 {
 	$data = $this->SetGoodsMst->findById($id);

 	if(!empty($this->data))
 	{
 	  //削除
      if(isset($this->params['form']['delete']))
      {
         $this->SetGoodsMst->delete($id);
         //$this->redirect('.');
         $this->redirect('https://'.$_SERVER['HTTP_HOST'].'/admin/SetGoodsMaster');
      }
      //更新
      else
     {
 	     $this->SetGoodsMst->save($this->data);
    	 //$this->redirect('.');
 	     $this->redirect('https://'.$_SERVER['HTTP_HOST'].'/admin/SetGoodsMaster');
      }
 	}

 	//商品リストの取得
 	$this->set("goods_list",$this->GoodsMst->find('all',array('conditions'=>array('GoodsMst.set_goods_kbn'=>'0'))));
 	//セット商品リストの取得
 	$this->set("set_goods_list",$this->GoodsMst->find('all',array('conditions'=>array('GoodsMst.set_goods_kbn'=>'1'))));

 	$this->set("data",$data);

 	$this->set("sub_title","セット商品編集");
 	$this->set("user",$this->Auth->user());

 }
}
?>