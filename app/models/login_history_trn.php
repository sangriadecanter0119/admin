<?php
class LoginHistoryTrn extends AppModel {
    var $name = "LoginHistoryTrn";

    function Add($user_id){

    	App::import("Model", "User");
    	$user = new User();

    	$user_data = $user->findById($user_id);

    	if(!empty($user_data)){

    		$this->create();
    		$this->save(['user_id'=>$user_data['user']['id'],
    				     'username'=>$user_data['user']['username'],
    				     'display_nm'=>$user_data['user']['display_nm'],
    				     'user_kbn_id'=>$user_data['user']['user_kbn_id'],
    				     'login_dt'=>date('Y/m/d H:i:s')]);
    	}
    }
}
?>