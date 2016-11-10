<?php
class GoodsMst extends AppModel {
    var $name = 'GoodsMst';

    /**
     *
     * 指定年度の商品マスタが存在するかチェック
     * @param $this_year
     */
    function hasGoodsMasterOfYear($year)
    {
      $goods_data = $this->find('all',array("conditions"=>array("year"=>$year)));
      return count($goods_data)==0 ? false : true;
    }

    /**
     *
     * 商品マスタの複製登録
     * @param unknown_type $this_year
     * @param unknown_type $user_name
     */
    function duplicate($this_year,$user_name)
    {
    	App::import("Model", "SetGoodsMst");
        $set_goods = new SetGoodsMst();

    	$goods = $this->find('all',array("conditions"=>array("year"=>$this_year-1,'del_kbn'=>EXISTS)));
 	 	for($i=0;$i < count($goods);$i++)
 	 	{
 	 	  if($goods[$i]['GoodsMst']['del_kbn'] == EXISTS){

 	 	  	$old_id = $goods[$i]['GoodsMst']['id'];
 	 	  	$goods[$i]['GoodsMst']['id']       = null;
 	 	  	$goods[$i]['GoodsMst']['goods_cd'] = (int)$goods[$i]['GoodsMst']['goods_cd'] + count($goods);
 	 		$goods[$i]['GoodsMst']['year']   = $this_year;
 	 		$goods[$i]['GoodsMst']['note']   = null;
 	 		$goods[$i]['GoodsMst']['reg_nm'] = $user_name;
            $goods[$i]['GoodsMst']['reg_dt'] = date('Y-m-d H:i:s');
            $goods[$i]['GoodsMst']['upd_nm'] = null;
            $goods[$i]['GoodsMst']['upd_dt'] = null;
            $this->create();
            if($this->save($goods[$i])==false){return false;}
            $new_id = $this->getLastInsertID();

            /* セット商品マスタのセット商品ＩＤまたは商品ＩＤを新年度版の商品マスタに関連付ける */
            if($goods[$i]['GoodsMst']['set_goods_kbn'] == SET_GOODS){
            	if($set_goods->updateSetGoodsId($new_id, $old_id, $this_year)==false){return false;}
            }else{
            	if($set_goods->updateGoodsId($new_id, $old_id, $this_year)==false){return false;}
            }
 	 	  }
 	 	}
 	   return true;
    }

    /**
     *
     * 商品コードの新規取得
     * @param $goods_ctg_id
     * @param $this_year
     */
    function getNewGoodsCode($goods_ctg_id,$this_year)
    {
    	$category_code = $this->_getCategoryCodeById($goods_ctg_id);
    	if($category_code == null){ return "予期しないカテゴリID[".$goods_ctg_id."]です。"; }

    	$sql = sprintf("SELECT MAX(substr(goods_cd,6,3)) goods_cd
                             FROM goods_msts
                            WHERE goods_cd like '%s%s%%'",$category_code,substr($this_year,2));
    	$data = $this->query($sql);

        if(count($data)==0){return $category_code.substr($this_year,2).'-001';}

 	    return sprintf("%s%s-%03d",$category_code,substr($this_year,2),((int)$data[0][0]['goods_cd']) + 1);
    }

    /**
     *
     * カテゴリIDに対応するカテゴリコードを取得する
     * @param $goods_ctg_id
     */
    function _getCategoryCodeById($goods_ctg_id){

    	switch ($goods_ctg_id) {
    		case GC_WEDDING: return "CM";
    		case GC_HAIR_MAKE_CPL: return "HC";
    		case GC_HAIR_MAKE_GST: return "HG";
    		case GC_TRANS_CPL: return "TC";
    		case GC_TRANS_GST: return "TG";
    		case GC_COORDINATOR: return "CD";
    		case GC_FLOWER: return "FW";
    		case GC_ALBUM: return "AB";
    		case GC_PHOTO: return "PG";
    		case GC_VIDEO: return "VG";
    		case GC_ENTERTAINMENT: return "ET";
    		case GC_MINISTER: return "MS";
    		case GC_RECEPTION: return "RC";
    		case GC_RECEPTION_TRANS: return "RT";
    		case GC_PARTY_OPTION: return "PO";
    		case GC_LINEN: return "LN";
    		case GC_AV: return "AV";
    		case GC_CAKE: return "CK";
    		case GC_PAPER: return "PP";
    		case GC_MC: return "MC";
    		case GC_HOUSE_WEDDING: return "HW";
    		case GC_CEREMONY_OPTION: return "CO";
    		case GC_FLOWER_MAIN: return "FM";
    		case GC_FLOWER_CEREMONY: return "FC";
    		case GC_FLOWER_RECEPTION: return "FR";

    		default:return null;
    	}
    }

   /**
     *
     * 商品がセット商品かチェック
     * @param $goods_id
     */
    function isSetGoods($goods_id)
    {
      $goods_data = $this->findById($goods_id);
      return $goods_data["GoodsMst"]["set_goods_kbn"] == SET_GOODS ? true: false;
    }

    /**
     * 引数の回数未満しか見積で使用されていない商品を削除する(一度も使用されていない商品以外は論理削除)
     * @param unknown $count
     * @return multitype:boolean string |multitype:boolean NULL
     */
    function deleteGoodsUsingLessThan($count){

    	$ret1 = $this->_deleteUnusedGoods();
    	if($ret1['result'] == false){  return $ret1; }

    	$ret2 = $this->_deleteGoodsUsingLessThan($count);
    	if($ret2['result'] == false){  return $ret2; }

    	return array('result'=>'true',
    			     'message'=>'商品削除に成功しました',
    			     'total'=> $ret1['unused_goods_count'] + $ret2['less_used_count'],
    			     'unused_goods_count'=>$ret1['unused_goods_count'],
    			     'less_used_count'=>$ret2['less_used_count']);
    }

   /**
    * 見積で使用された履歴がない商品を物理削除する
    * @return multitype:boolean string |multitype:boolean NULL
    */
    function _deleteUnusedGoods(){

    	$sql = "SELECT
                   gg.goods_cd	      goods_cd,
                   gg.count           count
                FROM
                   (SELECT
                      gp.goods_cd	      goods_cd,
                      COUNT(gp.goods_cd)  count
                    FROM
                         (SELECT
                            gd.goods_cd 		goods_cd
                          FROM goods_msts gd
                          LEFT JOIN estimate_dtl_trns est ON gd.id = est.goods_id) gp
                    GROUP BY gp.goods_cd) gg
                WHERE gg.count = 0 ";

    	$data = $this->query($sql);
    	for($i=0;$i < count($data);$i++){

         if($this->deleteAll(array("goods_cd"=>$data[$i]['gg']['goods_cd'],false))==false){
             return array('result'=>'false','message'=>"商品削除に失敗しました。",'reason'=>$this->getDbo()->error."[".date('Y-m-d H:i:s')."]");
         }
    	}
    	return array('result'=>'true','unused_goods_count'=>count($data));
    }

    /**
     * 引数の回数未満しか見積で使用されていない商品を論理削除する
     * @param unknown $count
     * @return multitype:boolean string |multitype:boolean NULL
     */
    function _deleteGoodsUsingLessThan($count){

    	$sql = "SELECT
                   gg.goods_cd	      goods_cd,
                   gg.count           count
                FROM
                   (SELECT
                      gp.goods_cd	      goods_cd,
                      COUNT(gp.goods_cd)  count
                    FROM
                         (SELECT
                            gd.goods_cd 		goods_cd
                          FROM goods_msts gd
                          LEFT JOIN estimate_dtl_trns est ON gd.id = est.goods_id) gp
                    GROUP BY gp.goods_cd) gg
                WHERE gg.count < ".$count;

    	$data = $this->query($sql);
    	for($i=0;$i < count($data);$i++){

    		$fields = array(
    				"del_kbn"=>DELETE,
    				"del_nm"=>"'admin'",
    				"del_dt"=>"'".date('Y-m-d H:i:s')."'"
    		);
    		if($this->updateAll($fields,array("goods_cd"=>$data[$i]['gg']['goods_cd']))==false){
    			return array('result'=>'false','message'=>"商品削除に失敗しました。",'reason'=>$this->getDbo()->error."[".date('Y-m-d H:i:s')."]");
    		}
    	}
    	return array('result'=>'true','less_used_count'=>count($data));
    }
}
?>