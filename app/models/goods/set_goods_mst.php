<?php
class SetGoodsMst extends AppModel {
    var $name = 'SetGoodsMst';

    /**
     *
     * セット商品マスタの複製登録
     * @param unknown_type $this_year
     * @param unknown_type $user_name
     */
    function duplicate($src_year,$new_year,$user_name)
    {
        $set_goods_data = $this->find('all',array("conditions"=>array("year"=>$src_year,'del_kbn'=>EXISTS)));

 	 	for($i=0;$i < count($set_goods_data);$i++){

 	 	  $set_goods_data[$i]['SetGoodsMst']['id']       = null;
 	 	  $set_goods_data[$i]['SetGoodsMst']['year']     = $new_year;
 	 	  $set_goods_data[$i]['SetGoodsMst']['reg_nm']   = $user_name;
 	 	  $set_goods_data[$i]['SetGoodsMst']['reg_dt']   = date('Y-m-d H:i:s');
 	 	  $set_goods_data[$i]['SetGoodsMst']['upd_nm']   = null;
 	 	  $set_goods_data[$i]['SetGoodsMst']['upd_dt']   = null;

          if($this->save($set_goods_data[$i])==false){
          	return array('result'=>false,'message'=>"セット商品マスタの複製に失敗しました。",'reason'=>$this->getDbo()->error."[".date('Y-m-d H:i:s')."]");
          }
 	 	}
 	 	return array('result'=>true);
    }

    /**
     *
     * セット商品ＩＤを更新
     * @param $new_id
     * @param $old_id
     * @param $year
     */
    function updateSetGoodsId($new_id,$old_id,$year)
    {
      if($this->query("UPDATE set_goods_msts SET set_goods_id = {$new_id} WHERE set_goods_id = {$old_id} AND year = '{$year}'")==false){
      	return array('result'=>false,'message'=>"セット商品マスタのセット商品IDの更新に失敗しました。",'reason'=>$this->getDbo()->error."[".date('Y-m-d H:i:s')."]");
      }
      return array('result'=>true);
    }

    /**
     *
     * 商品ＩＤを更新
     * @param $new_id
     * @param $old_id
     * @param $year
     */
    function updateGoodsId($new_id,$old_id,$year)
    {
      if($this->query("UPDATE set_goods_msts SET goods_id = {$new_id} WHERE goods_id = {$old_id} AND year = '{$year}'")==false){
      	return array('result'=>false,'message'=>"セット商品マスタの商品IDの更新に失敗しました。",'reason'=>$this->getDbo()->error."[".date('Y-m-d H:i:s')."]");
      }
      return array('result'=>true);
    }
}
?>