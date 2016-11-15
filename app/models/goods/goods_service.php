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

    /**
     * 商品EXCELファイルをアップロードしてサーバに仮保存
     * @param unknown $tempFile
     * @param unknown $uploadingFileName
     * @return boolean[]|string[]
     */
    function uploadFile($tempFile,$uploadingFileName){

	    App::import("Model", "GoodsUploader");
	    $uploader = new GoodsUploader();
      	return $uploader->uploadFile($tempFile, $uploadingFileName);
    }

    /**
     * 商品EXCELファイルを元に商品マスタを更新
     * @param unknown $file
     * @param unknown $username
     * @return boolean[]|string[]|boolean[]
     */
    function updateByFile($file,$username){

    	App::import("Model", "GoodsUploader");
    	$uploader = new GoodsUploader();
    	return $uploader->updateByFile($file, $username);
    }
}
?>