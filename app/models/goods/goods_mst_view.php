<?php
class GoodsMstView extends AppModel {
   var $name = 'GoodsMstView'; 
  
   /**
    * 
    * 商品IDから商品カテゴリIDを取得する
    * @param $goods_id
    * @return 正常： 商品カテゴリID
    *         異常:
    */
   function GetCategoryIdByGoodsId($goods_id){
     $data = $this->findById($goods_id);
     return count($data["GoodsMstView"])==0 ? null : $data["GoodsMstView"]['goods_ctg_id'];
   }
 
}
?>