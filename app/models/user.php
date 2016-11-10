<?php
class User extends AppModel {
    var $name = "user";

    /**
     * 表示名リストを取得する
     * @return multitype:NULL
     */
    function GetAllDisplayName(){

    	$data = $this->find("all",array("fields"=>array("display_nm")));
    	$list = array();

    	for($i=0;$i < count($data);$i++){

    	  if(!empty($data[$i]["User"]["display_nm"])){
    	  	 $list[] = $data[$i]["User"]["display_nm"];
    	  }
    	}
    	return $list;
    }

    /**
     * ALL込みの表示名リストを取得する
     * @return multitype:NULL
     */
    function GetAllDisplayNameWithAll(){

    	$data = $this->find("all",array("fields"=>array("display_nm")));
    	$list = array();

    	$list[] = "ALL";
    	for($i=0;$i < count($data);$i++){

    		if(!empty($data[$i]["User"]["display_nm"])){
    			$list[] = $data[$i]["User"]["display_nm"];
    		}
    	}
    	return $list;
    }
}
?>