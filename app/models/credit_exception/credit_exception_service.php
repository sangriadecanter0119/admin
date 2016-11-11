<?php
class CreditExceptionService extends AppModel {
	var $useTable = false;

    /**
     * 入金例外マスタの登録
     * @param unknown $data
     * @param unknown $username
     * @return multitype:boolean string |multitype:boolean unknown
     */
    function register($data , $username){

    	$tr = ClassRegistry::init('TransactionManager');
    	$tr->begin();

    	App::import("Model", "CreditExceptionMst");
    	$credit = new CreditExceptionMst();

    	App::import("Model", "CustomerMst");
    	$customer = new CustomerMst();

    	$customer_nm = mb_convert_kana(preg_replace('/[ ]/', '', $data['CreditExceptionMst']['credit_customer_nm']),"KV");

    	//入金タイプから顧客ステータスに変換
    	$status_id = null;
    	switch($data['CreditExceptionMst']['credit_type_id']){
    		case NC_DRESS:$status_id = CS_DRESS;
    			           break;
    		case NC_TRAVEL:$status_id = CS_TRIP;
    			           break;
    	    case NC_VENDOR:$status_id = CS_VENDOR;
    			           break;
    		default:
    			 return array('result'=>false,'message'=>"予期しない入金タイプです。",'reason'=>$data['CreditExceptionMst']['credit_type_id']);
    	}

    	//顧客マスタ登録
        $ret = $customer->regiterCustomerForException($customer_nm ,$status_id,$username);
    	if($ret['result'] == false){ return $ret; }

    	$customer_id = $ret['newID'];

    	//入金例外マスタ登録
    	$credit_data = array(
    				"customer_id"=>$customer_id,
    				"credit_customer_nm"=>$customer_nm,
    				"credit_type_id"=>$data['CreditExceptionMst']['credit_type_id'],
    				"reg_nm"=>$username,
    				"reg_dt"=>date('Y-m-d H:i:s')
    		);
    	$credit->create();
    	if($credit->save($credit_data)==false){
    			return array('result'=>false,'message'=>"入金例外マスタの新規作成に失敗しました。",'reason'=>$credit->getDbo()->error."[".date('Y-m-d H:i:s')."]");
    	}
    	$tr->commit();
    	return array('result'=>true);
    }

    /**
     * 入金例外マスタの更新
     * @param unknown $data
     * @param unknown $username
     */
    function update($data , $username){

    	$tr = ClassRegistry::init('TransactionManager');
    	$tr->begin();

    	App::import("Model", "CustomerMst");
    	$customer = new CustomerMst();

    	App::import("Model", "CreditExceptionMst");
    	$credit = new CreditExceptionMst();

    	$data['credit_customer_nm'] = mb_convert_kana(preg_replace('/[ ]/', '', $data['credit_customer_nm']),"KV");

    	//顧客マスタ更新
    	if($customer->updateAll(
    			array('grmls_kn' => "'".$data['credit_customer_nm']."'",
    	              'upd_nm'=>"'".$username."'",
    				  'upd_dt'=>"'".date('Y-m-d H:i:s')."'"),
    			array('id'=> $data['customer_id'])
    	)==false){
    		return array('result'=>false,'message'=>"顧客マスタの更新に失敗しました。",'reason'=>$customer->getDbo()->error."[".date('Y-m-d H:i:s')."]");
    	}

    	//入金例外マスタの更新
    	$fields = array('credit_customer_nm' ,'note' ,'upd_nm','upd_dt');

    	$data['upd_nm'] = $username;
    	$data['upd_dt'] = date('Y-m-d H:i:s');

    	$credit->id = $data['id'];

    	if($credit->save($data,false,$fields)==false){
    		return array('result'=>false,'message'=>"入金例外マスタの更新に失敗しました。",'reason'=>$credit->getDbo()->error."[".date('Y-m-d H:i:s')."]");
    	}

    	$tr->commit();
    	return array('result'=>true);
    }

    /**
     * 入金例外マスタの削除
     * @param unknown $data
     * @param unknown $username
     */
    function delete($id,$customer_id){

    	App::import("Model", "CreditTrn");
    	$credit = new CreditTrn();

    	App::import("Model", "CreditExceptionMst");
    	$credit_exception = new CreditExceptionMst();

    	App::import("Model", "CustomerMst");
    	$customer = new CustomerMst();

    	//既に入金情報がある場合は削除不可とする
    	$tmp = $credit->find('all',array('conditions'=>array('customer_id'=>$customer_id)));
    	if(count($tmp) > 0){ return array('result'=>false,'message'=>"入金情報が登録されているので削除できません。",'reason'=>$customer_id);}

    	//入金例外マスタの削除
    	if($credit_exception->delete($id)==false){
    		return array('result'=>false,'message'=>"入金例外マスタの削除に失敗しました。",'reason'=>$credit_exception->getDbo()->error."[".date('Y-m-d H:i:s')."]");
    	}

    	//顧客マスタの削除
    	if($customer->delete($customer_id)==false){
    		return array('result'=>false,'message'=>"顧客マスタの削除に失敗しました。",'reason'=>$customer->getDbo()->error."[".date('Y-m-d H:i:s')."]");
    	}
    	return array('result'=>true);
    }
}
?>