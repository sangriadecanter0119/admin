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

    function getUserList(){

    	$sql = "SELECT id,username FROM users WHERE id IN (SELECT distinct user_id FROM login_history_trns) ORDER BY username";
    	return  $this->query($sql);
    }
}
?>