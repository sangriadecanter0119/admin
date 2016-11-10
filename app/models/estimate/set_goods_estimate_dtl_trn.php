<?php
class SetGoodsEstimateDtlTrn extends AppModel {
  var $name = 'SetGoodsEstimateDtlTrn';

  /**
   *
   * セット商品見積明細に登録する
   * @param $goods_id
   * @param $estimate_dtl_id
   * @param $username
   */
  function createNew($goods_id,$estimate_dtl_id,$username){

  	  App::import("Model", "EstimateDtlTrn");
  	  $estimate_dtl = new EstimateDtlTrn();

  	  App::import("Model", "SetGoodsMstView");
  	  $set_goods = new SetGoodsMstView();

  	 //見積明細を取得
  	 $estimate_dtl_data = $estimate_dtl->findById($estimate_dtl_id);
  	 //セット商品の構成商品を取得
  	 $set_goods_data = $set_goods->find("all",array("conditions"=>array("set_goods_id"=>$goods_id)));

  	  	for($i=0;$i < count($set_goods_data);$i++){

  	  		$attr['id']              = null;
  	  		$attr["no"]              = $set_goods_data[$i]["SetGoodsMstView"]["no"];
  	  		$attr['estimate_dtl_id'] = $estimate_dtl_id;
  	  		$attr["goods_id"]        = $set_goods_data[$i]["SetGoodsMstView"]["goods_id"];
  	  		$attr["vendor_id"]       = $set_goods_data[$i]["SetGoodsMstView"]["vendor_id"];
  	  		$attr["vendor_nm"]       = $set_goods_data[$i]["SetGoodsMstView"]["vendor_nm"];
  	  		$attr["goods_ctg_nm"]    = $set_goods_data[$i]["SetGoodsMstView"]["goods_ctg_nm"];
  	  		$attr["goods_kbn_nm"]    = $set_goods_data[$i]["SetGoodsMstView"]["goods_kbn_nm"];
  	  		$attr["goods_cd"]	     = $set_goods_data[$i]["SetGoodsMstView"]["goods_cd"];
  	  		$attr["sales_goods_nm"]  = $set_goods_data[$i]["SetGoodsMstView"]["goods_nm"];
  	  		$attr["num"]             = $set_goods_data[$i]["SetGoodsMstView"]["num"];
  	  		$attr["sales_price"]     = $set_goods_data[$i]["SetGoodsMstView"]["price"];
  	  		$attr["sales_cost"]      = $set_goods_data[$i]["SetGoodsMstView"]["cost"];
  	  		$attr["original_sales_price"]     = $set_goods_data[$i]["SetGoodsMstView"]["price"];
  	  		$attr["original_sales_cost"]      = $set_goods_data[$i]["SetGoodsMstView"]["cost"];
  	  		$attr["sales_exchange_rate"]  = $estimate_dtl_data["EstimateDtlTrn"]["sales_exchange_rate"];
  	  		$attr["cost_exchange_rate"]   = $estimate_dtl_data["EstimateDtlTrn"]["cost_exchange_rate"];
  	  		$attr["payment_kbn_id"]       = $estimate_dtl_data["EstimateDtlTrn"]["payment_kbn_id"];
  	  		$attr["money_received_flg"]       = $estimate_dtl_data["EstimateDtlTrn"]["money_received_flg"];
  	  		$attr["currency_kbn"] 	      = $set_goods_data[$i]["SetGoodsMstView"]["currency_kbn"];
  	  		$attr["aw_share"]        = $set_goods_data[$i]["SetGoodsMstView"]["aw_share"];
  	  		$attr["rw_share"]        = $set_goods_data[$i]["SetGoodsMstView"]["rw_share"];
 	        $attr['reg_nm'] = $username;
 	        $attr['reg_dt'] = date('Y-m-d H:i:s');
 	        //フィールドの初期化
  	        $this->create();
 	        if($this->save($attr)==false){
 	        	return array('result'=>false,'message'=>"セット商品見積明細の登録に失敗しました。",'reason'=>$this->getDbo()->error."[".date('Y-m-d H:i:s')."]");
 	        }
  	  	}
  	return array('result'=>true);
  }

  /**
   * セット商品の入金済みフラグを更新する
   * @param unknown $estimate_dtl_id
   * @param unknown $money_received_flg
   * @param unknown $username
   * @return multitype:boolean string |multitype:boolean
   */
  function updateAllPayment($estimate_dtl_id,$money_received_flg){

    App::import("Model", "SetGoodsEstimateDtlTrn");
  	$set_estimate = new SetGoodsEstimateDtlTrn();

  	$data = $set_estimate->find('all',array('conditions'=>array('estimate_dtl_id'=>$estimate_dtl_id)));

  	for($i=0; $i < count($data);$i++){

  		$set_estimate->id = $data[$i]['SetGoodsEstimateDtlTrn']['id'];
  		if($set_estimate->save(array('money_received_flg'=>$money_received_flg),false)==false){
  			return array('result'=>false,'message'=>"セット商品の入金済みフラグの更新に失敗しました。",'reason'=>$set_estimate->getDbo()->error."[".date('Y-m-d H:i:s')."]");
  		}
  	}
  	return array('result'=>true);
  }
}
?>