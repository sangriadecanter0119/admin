<?php
class RemittanceTrnView extends AppModel {
    var $name = 'RemittanceTrnView';

    /**
     * ステータスが請求済みの送金一覧を取得する
     * @param unknown $wedding_dt
     */
    function findAllByWeddingDateInInvoiced($wedding_dt){

    	return $this->find('all',
    			array('conditions'=>
    					array('SUBSTR(wedding_dt,1,7)'=>$wedding_dt,'status_id'=>array(CS_INVOICED,CS_PAIED,CS_UNPAIED)),'order'=>'wedding_dt'));
    }
}