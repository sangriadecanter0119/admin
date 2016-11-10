<?php
class CustomerMst extends AppModel {
    var $name = 'CustomerMst';


   /**
     *
     * 顧客ステータスを[問い合わせ]に設定する
     * @param $customer_id
     * @param $user_name
     * @return 正常：　TRUE　
     *         異常: FALSE
     */
    function setToiawase($customer_id,$user_name){

      $cust_fields = array('status_id','upd_nm','upd_dt');
      $cust_data = array(
 	                     "status_id"=>CS_CONTACT,
 	                     "upd_nm"=>$user_name,
 	                     "upd_dt"=>date('Y-m-d H:i:s')
 	                    );
 	  $this->id = $customer_id;
 	  if($this->save($cust_data,false,$cust_fields)==false){
 	  	return array('result'=>false,'message'=>"顧客ステータス更新に失敗しました。",'reason'=>$this->getDbo()->error."[".date('Y-m-d H:i:s')."]");
 	  }
 	  return array('result'=>true);
   }

   /**
    *
    * 顧客ステータスを[見積提示済み]に設定する
    * @param $customer_id
    * @param $user_name
    * @return 正常：　TRUE　
    *         異常: FALSE
    */
   function setEstimated($customer_id,$user_name){

   	$cust_fields = array('status_id','upd_nm','upd_dt');
   	$cust_data = array(
   			"status_id"=>CS_ESTIMATED,
   			"upd_nm"=>$user_name,
   			"upd_dt"=>date('Y-m-d H:i:s')
   	);
   	$this->id = $customer_id;
   	if($this->save($cust_data,false,$cust_fields)==false){
   		return array('result'=>false,'message'=>"顧客ステータス更新に失敗しました。",'reason'=>$this->getDbo()->error."[".date('Y-m-d H:i:s')."]");
   	}
   	return array('result'=>true);
   }

   /**
    *
    * 顧客ステータスを[仮約定]に設定する
    * @param $customer_id
    * @param $user_name
    * @return 正常：　TRUE　
    *         異常: FALSE
    */
   function setContracting($customer_id,$user_name){

   	$cust_fields = array('status_id','upd_nm','upd_dt');
   	$cust_data = array(
   			"status_id"=>CS_CONTRACTING,
   			"upd_nm"=>$user_name,
   			"upd_dt"=>date('Y-m-d H:i:s')
   	);
   	$this->id = $customer_id;
   	if($this->save($cust_data,false,$cust_fields)==false){
   		return array('result'=>false,'message'=>"顧客ステータス更新に失敗しました。",'reason'=>$this->getDbo()->error."[".date('Y-m-d H:i:s')."]");
   	}
   	return array('result'=>true);
   }

  /**
    *
    * 顧客ステータスを[成約]に設定する
    * @param $customer_id
    * @param $user_name
    * @return 正常：　TRUE　
    *         異常: FALSE
    */
   function setSeiyaku($customer_id,$user_name){

   	$cust_fields = array('status_id','upd_nm','upd_dt');
   	$cust_data = array(
   			"status_id"=>CS_CONTRACTED,
   			"upd_nm"=>$user_name,
   			"upd_dt"=>date('Y-m-d H:i:s')
   	);
   	$this->id = $customer_id;
   	if($this->save($cust_data,false,$cust_fields)==false){
   		return array('result'=>false,'message'=>"顧客ステータス更新に失敗しました。",'reason'=>$this->getDbo()->error."[".date('Y-m-d H:i:s')."]");
   	}
   	return array('result'=>true);
   }

   /**
    *
    * 顧客ステータスを[請求書発行済み]に設定する
    * @param $customer_id
    * @param $user_name
    * @return 正常：　TRUE　
    *         異常: FALSE
    */
   function setInvoice($customer_id,$user_name){

   	$cust_fields = array('status_id','upd_nm','upd_dt');
   	$cust_data = array(
   			"status_id"=>CS_INVOICED,
   			"upd_nm"=>$user_name,
   			"upd_dt"=>date('Y-m-d H:i:s')
   	);
   	$this->id = $customer_id;
   	if($this->save($cust_data,false,$cust_fields)==false){
   		return array('result'=>false,'message'=>"顧客ステータス更新に失敗しました。",'reason'=>$this->getDbo()->error."[".date('Y-m-d H:i:s')."]");
   	}
   	return array('result'=>true);
   }

   /**
    *
    * 顧客ステータスを[挙式完了・未入金]に設定する
    * @param $customer_id
    * @param $user_name
    * @return 正常：　TRUE　
    *         異常: FALSE
    */
   function setUnpaied($customer_id,$user_name){

   	$cust_fields = array('status_id','upd_nm','upd_dt');
   	$cust_data = array(
   			"status_id"=>CS_UNPAIED,
   			"upd_nm"=>$user_name,
   			"upd_dt"=>date('Y-m-d H:i:s')
   	);
   	$this->id = $customer_id;
   	if($this->save($cust_data,false,$cust_fields)==false){
   		return array('result'=>false,'message'=>"顧客ステータス更新に失敗しました。",'reason'=>$this->getDbo()->error."[".date('Y-m-d H:i:s')."]");
   	}
   	return array('result'=>true);
   }

   /**
    *
    * 顧客ステータスを[挙式完了・入金済み]に設定する
    * @param $customer_id
    * @param $user_name
    * @return 正常：　TRUE　
    *         異常: FALSE
    */
   function setPaied($customer_id,$user_name){

   	$cust_fields = array('status_id','upd_nm','upd_dt');
   	$cust_data = array(
   			"status_id"=>CS_PAIED,
   			"upd_nm"=>$user_name,
   			"upd_dt"=>date('Y-m-d H:i:s')
   	);
   	$this->id = $customer_id;
   	if($this->save($cust_data,false,$cust_fields)==false){
   		return array('result'=>false,'message'=>"顧客ステータス更新に失敗しました。",'reason'=>$this->getDbo()->error."[".date('Y-m-d H:i:s')."]");
   	}
   	return array('result'=>true);
   }

   /**
    *
    * 顧客ステータスを[キャンセル]に設定する
    * @param $customer_id
    * @param $user_name
    * @return 正常：　TRUE　
    *         異常: FALSE
    */
   function setCancel($customer_id,$user_name){

   	$cust_fields = array('status_id','upd_nm','upd_dt');
   	$cust_data = array(
   			"status_id"=>CS_CANCEL,
   			"upd_nm"=>$user_name,
   			"upd_dt"=>date('Y-m-d H:i:s')
   	);
   	$this->id = $customer_id;
   	if($this->save($cust_data,false,$cust_fields)==false){
   		return array('result'=>false,'message'=>"顧客ステータス更新に失敗しました。",'reason'=>$this->getDbo()->error."[".date('Y-m-d H:i:s')."]");
   	}
   	return array('result'=>true);
   }

   /**
    *
    * 顧客ステータスを[延期]に設定する
    * @param $customer_id
    * @param $user_name
    * @return 正常：　TRUE　
    *         異常: FALSE
    */
   function setPostpone($customer_id,$user_name){

   	$cust_fields = array('status_id','upd_nm','upd_dt');
   	$cust_data = array(
   			"status_id"=>CS_POSTPONE,
   			"upd_nm"=>$user_name,
   			"upd_dt"=>date('Y-m-d H:i:s')
   	);
   	$this->id = $customer_id;
   	if($this->save($cust_data,false,$cust_fields)==false){
   		return array('result'=>false,'message'=>"顧客ステータス更新に失敗しました。",'reason'=>$this->getDbo()->error."[".date('Y-m-d H:i:s')."]");
   	}
   	return array('result'=>true);
   }

   /**
    * 顧客ステータスを更新する
    * @param unknown $status
    * @param unknown $customer_id
    * @param unknown $user_name
    * @return multitype:boolean string |multitype:boolean
    */
   function setCustomerStatus($status,$customer_id,$user_name){

   	$cust_fields = array('status_id','upd_nm','upd_dt');
   	$cust_data = array(
   			"status_id"=>$status,
   			"upd_nm"=>$user_name,
   			"upd_dt"=>date('Y-m-d H:i:s')
   	);
   	$this->id = $customer_id;
   	if($this->save($cust_data,false,$cust_fields)==false){
   		return array('result'=>false,'message'=>"顧客ステータス更新に失敗しました。",'reason'=>$this->getDbo()->error."[".date('Y-m-d H:i:s')."]");
   	}
   	return array('result'=>true);
   }

   /**
    * 見積提出日を更新する
    * @param unknown $estimtae_issued_dt
    * @param unknown $customer_id
    * @param unknown $user_name
    * @return multitype:boolean string |multitype:boolean
    */
   function setEstimateIssuedDate($estimtae_issued_dt,$customer_id,$user_name){

   	$cust_fields = array('estimate_issued_dt','upd_nm','upd_dt');
   	$cust_data = array(
   			"estimate_issued_dt"=>$estimtae_issued_dt,
   			"upd_nm"=>$user_name,
   			"upd_dt"=>date('Y-m-d H:i:s')
   	);
   	$this->id = $customer_id;
   	if($this->save($cust_data,false,$cust_fields)==false){
   		return array('result'=>false,'message'=>"見積提出日更新に失敗しました。",'reason'=>$this->getDbo()->error."[".date('Y-m-d H:i:s')."]");
   	}
   	return array('result'=>true);
   }

   /**
    * 見積提出日をクリアする
    * @param unknown $customer_id
    * @param unknown $user_name
    * @return multitype:boolean string |multitype:boolean
    */
   function clearEstimateIssuedDate($customer_id,$user_name){

   	$cust_fields = array('estimate_issued_dt','upd_nm','upd_dt');
   	$cust_data = array(
   			"estimate_issued_dt"=>null,
   			"upd_nm"=>$user_name,
   			"upd_dt"=>date('Y-m-d H:i:s')
   	);
   	$this->id = $customer_id;
   	if($this->save($cust_data,false,$cust_fields)==false){
   		return array('result'=>false,'message'=>"見積提出日更新に失敗しました。",'reason'=>$this->getDbo()->error."[".date('Y-m-d H:i:s')."]");
   	}
   	return array('result'=>true);
   }

   /**
    * 仮約定日を更新する
    * @param unknown $contracting_dt
    * @param unknown $customer_id
    * @param unknown $user_name
    * @return multitype:boolean string |multitype:boolean
    */
   function setContractingDate($contracting_dt,$customer_id,$user_name){

   	$cust_fields = array('contracting_dt','upd_nm','upd_dt');
   	$cust_data = array(
   			"contracting_dt"=>$contracting_dt,
   			"upd_nm"=>$user_name,
   			"upd_dt"=>date('Y-m-d H:i:s')
   	);
   	$this->id = $customer_id;
   	if($this->save($cust_data,false,$cust_fields)==false){
   		return array('result'=>false,'message'=>"仮約定日更新に失敗しました。",'reason'=>$this->getDbo()->error."[".date('Y-m-d H:i:s')."]");
   	}
   	return array('result'=>true);
   }

   /**
    * 仮約定日をクリアする
    * @param unknown $contracting_dt
    * @param unknown $user_name
    * @return multitype:boolean string |multitype:boolean
    */
   function clearContractingDate($customer_id,$user_name){

   	$cust_fields = array('contracting_dt','upd_nm','upd_dt');
   	$cust_data = array(
   			"contracting_dt"=>null,
   			"upd_nm"=>$user_name,
   			"upd_dt"=>date('Y-m-d H:i:s')
   	);
   	$this->id = $customer_id;
   	if($this->save($cust_data,false,$cust_fields)==false){
   		return array('result'=>false,'message'=>"仮約定日更新に失敗しました。",'reason'=>$this->getDbo()->error."[".date('Y-m-d H:i:s')."]");
   	}
   	return array('result'=>true);
   }

    /**
     *
     * 挙式予定日を取得する
     * @param $customer_id
     * @return 正常： 挙式予定日
     *         異常：NULL
     */
    function getPlannedWeddingDate($customer_id){
      $customer = $this->findById($customer_id);
 	  return $customer['CustomerMst']['wedding_planned_dt']==null ? null:$customer['CustomerMst']['wedding_planned_dt'];
  }

    /**
     * 顧客の予定挙式情報を取得
     * @param unknown $customer_id
     * @return Ambigous <NULL, multitype:NULL >
     */
    function getWeddingBasicInfo($customer_id){

    	$data = $this->find('all',array('fields'=>array('wedding_planned_dt','wedding_planned_place','wedding_planned_time','reception_planned_place','reception_planned_time'),
    			                        'conditions'=>array('id'=>$customer_id)));

    	return count($data) > 0 ?
    	  array('wedding_planned_dt'=>$data[0]['CustomerMst']['wedding_planned_dt'],
    			'wedding_planned_place'=>$data[0]['CustomerMst']['wedding_planned_place'],
    			'wedding_planned_time'=>$data[0]['CustomerMst']['wedding_planned_time'],
    			'reception_planned_place'=>$data[0]['CustomerMst']['reception_planned_place'],
    			'reception_planned_time'=>$data[0]['CustomerMst']['reception_planned_time']) : null;
    }

  /**
    *
    * 新規接客日を取得する
    * @param $customer_id
    * @return 正常： 新規接客日
    *         異常：NULL
    */
    function getVisitDate($customer_id){
  	  $customer = $this->findById($customer_id);
  	  return $customer['CustomerMst']['first_visited_dt']==null ? null:$customer['CustomerMst']['first_visited_dt'];
    }

   /**
     *
     * 顧客コードの連番の最大値を取得する
     * @return 正常： 次の連番
     *         異常：NULL
     */
    function _getMaxSequenceNoOfCustomerCode(){
      $sql = "SELECT MAX(SUBSTR(customer_cd,1,4)) sequence FROM customer_msts WHERE status_id  between ".CS_CONTACT." AND ".CS_POSTPONE;
      $data = $this->query($sql);

      if(count($data)==0){return '0001';}

 	  return sprintf("%04d",((int)$data[0][0]['sequence']) + 1);
    }

   /**
     *
     * ユニークな全挙式年月を取得する
     * @return 正常： 挙式年月の配列
     *         異常：NULL
     */
    function getGroupOfWeddingMonth(){
      $sql = "SELECT SUBSTR(wedding_planned_dt,1,7) wedding_planned_dt FROM customer_msts GROUP BY SUBSTR(wedding_planned_dt,1,7) Order by SUBSTR(wedding_planned_dt,1,7) desc";
      $data = $this->query($sql);

      $months = array();
      for($i=0;$i < count($data);$i++){
      	array_push($months, $data[$i][0]['wedding_planned_dt']);
      }
 	  return $months;
    }

   /**
     *
     * ユニークな全登録年月を取得する
     * @return 正常：登録年月の配列
     *         異常：NULL
     */
    function getGroupOfRegisterMonth(){
      $sql = "SELECT SUBSTR(reg_dt,1,7) reg_dt FROM customer_msts GROUP BY SUBSTR(reg_dt,1,7) Order by SUBSTR(reg_dt,1,7) desc";
      $data = $this->query($sql);

      $months = array();
      for($i=0;$i < count($data);$i++){
      	array_push($months, $data[$i][0]['reg_dt']);
      }
 	  return $months;
    }

    /**
     *
     * ユニークな全問い合わせ年月を取得する
     * @return 正常：登録年月の配列
     *         異常：NULL
     */
    function getGroupOfFirstContactedMonth(){
    	$sql = "SELECT SUBSTR(first_contact_dt,1,7) first_contact_dt FROM customer_msts GROUP BY SUBSTR(first_contact_dt,1,7) Order by SUBSTR(first_contact_dt,1,7) desc";
    	$data = $this->query($sql);

    	$months = array();
    	for($i=0;$i < count($data);$i++){
    		array_push($months, $data[$i][0]['first_contact_dt']);
    	}
    	return $months;
    }

    /**
     *
     * ユニークな新規来店年月を取得する
     * @return 正常：新規来店年月の配列
     *         異常：NULL
     */
    function getGroupOfFirstVisitedMonth(){
    	$sql = "SELECT SUBSTR(first_visited_dt,1,7) first_visited_dt FROM customer_msts GROUP BY SUBSTR(first_visited_dt,1,7) Order by SUBSTR(first_visited_dt,1,7) desc";
    	$data = $this->query($sql);

    	$months = array();
    	for($i=0;$i < count($data);$i++){
    		array_push($months, $data[$i][0]['first_visited_dt']);
    	}
    	return $months;
    }

    /**
     *
     * ユニークな見積提出年月を取得する
     * @return 正常：見積提出年月の配列
     *         異常：NULL
     */
    function getGroupOfEestimateIssuedMonth(){
    	$sql = "SELECT SUBSTR(estimate_issued_dt,1,7) estimate_issued_dt FROM customer_msts GROUP BY SUBSTR(estimate_issued_dt,1,7) Order by SUBSTR(estimate_issued_dt,1,7) desc";
    	$data = $this->query($sql);

    	$months = array();
    	for($i=0;$i < count($data);$i++){
    		array_push($months, $data[$i][0]['estimate_issued_dt']);
    	}
    	return $months;
    }


    /**
     *
     * ユニークな成約済みの全挙式年月を取得する
     */
    function getGroupOfContractedWeddingMonth(){
      $sql = "SELECT SUBSTR(wedding_planned_dt,1,7) wedding_planned_dt FROM customer_msts WHERE status_id = ".CS_CONTRACTED." GROUP BY SUBSTR(wedding_planned_dt,1,7) Order by SUBSTR(wedding_planned_dt,1,7) desc";
      $data = $this->query($sql);

      $months = array();
      for($i=0;$i < count($data);$i++){
      	array_push($months, $data[$i][0]['wedding_planned_dt']);
      }
 	  return $months;
    }

    /**
     * ステータスＩＤを取得
     * @param unknown $customer_id
     */
    function getCustomerStatus($customer_id){

       $data = $this->find('all',array('fields'=>array('status_id'),'conditions'=>array('id'=>$customer_id)));
       return $data[0]['CustomerMst']['status_id'];
    }

    /**
     *
     * ユニークな新規担当者を取得する
     */
    function getGroupOfFirstContactPersonInStatusId($status_id){

      if($status_id = -1){
      	$sql = "SELECT first_contact_person_nm FROM customer_msts GROUP BY first_contact_person_nm";
      }else{
      	$sql = "SELECT first_contact_person_nm FROM customer_msts WHERE status_id = ".$status_id." GROUP BY first_contact_person_nm";
      }

      $data = $this->query($sql);

      $list = array();
      for($i=0;$i < count($data);$i++){
      	array_push($list, mb_convert_encoding($data[$i]['customer_msts']['first_contact_person_nm'],'SJIS','AUTO'));
      }
 	  return $list;
    }

    /**
     *
     * ユニークなプラン担当者を取得する
     */
    function getGroupOfProcessPersonInStatusId($status_id){

      if($status_id = -1){
      	$sql = "SELECT process_person_nm FROM customer_msts GROUP BY process_person_nm";
      }else{
      	$sql = "SELECT process_person_nm FROM customer_msts WHERE status_id = ".$status_id." GROUP BY process_person_nm";
      }
    	$data = $this->query($sql);

    	$list = array();
    	for($i=0;$i < count($data);$i++){
    		array_push($list, mb_convert_encoding($data[$i]['customer_msts']['process_person_nm'],'SJIS','AUTO'));
    	}
    	return $list;
    }

    /**
     *
     * 顧客コードを作成する
     * @param $first_contact_dt
     * @param $wedding_dt
     * @return 正常：顧客コード
     *         異常：例外
     */
    function createCustomerCode($first_contact_dt,$wedding_dt=null){

    	$mon =  substr($first_contact_dt,5,2);
    	$wed = empty($wedding_dt) ? '000000' : substr($wedding_dt,5,2).substr($wedding_dt,8,2).substr($wedding_dt,2,2);
    	return $this->_getMaxSequenceNoOfCustomerCode().$mon."-".$wed;
    }

   /**
     *
     * 顧客コードを作成する
     * @param $first_contact_dt
     * @param $wedding_dt
     * @return 正常：顧客コード
     *         異常：例外
     */
    function recreateCustomerCode($old_customer_code,$first_contact_dt,$wedding_dt=null){

    	$mon =  substr($first_contact_dt,5,2);
    	$wed = empty($wedding_dt) ? '000000' : substr($wedding_dt,5,2).substr($wedding_dt,8,2).substr($wedding_dt,2,2);
    	return substr($old_customer_code,0,4).$mon."-".$wed;
    }

    /**
     * IDで顧客コードを更新する
     * @param unknown $data
     * @param unknown $user_name
     * @return multitype:boolean string |multitype:boolean
     */
    function updateCustomerCodeById($data,$user_name){

    	$fields = array('customer_cd','upd_nm','upd_dt');

    	if(!empty($data['customer_cd'])){

    	   if($this->isCustomerCodeAlreadyTaken($data['customer_cd'])){
    	   	  return array('result'=>false,'message'=>"顧客コード「".$data['customer_cd']."」は既に割り当たってます。",'reason'=>"");
    	   }

    	   $data['upd_nm'] = $user_name;
    	   $data['upd_dt'] = date('Y-m-d H:i:s');

    	   $this->id = $data['id'];
    	   if($this->save($data,false,$fields)==false){
    		  return array('result'=>false,'message'=>"顧客コード更新に失敗しました。",'reason'=>$this->getDbo()->error."[".date('Y-m-d H:i:s')."]");
    	   }
    	}
    	return array('result'=>true);
    }

    /**
     * 顧客コードが既に割り当たっているかチェック
     * @param unknown $customer_cd
     * @return boolean
     */
    function isCustomerCodeAlreadyTaken($customer_cd){

    	$ret = $this->find('all',array('fields'=>array('id'),'conditions'=>array('customer_cd'=>$customer_cd)));
    	return count($ret) > 0 ? true : false;
    }

   /**
    *
    * ファイナルシート用のフィールド項目を更新する
    * @param $customer_data
    * @param $user_name
    * @return 正常： TRUE
    *         異常： FALSE
    */
    function updateForFinalSheet($customer_data,$user_name){

       $fields = array('note','upd_nm','upd_dt');

   	   if(empty($customer_data['grmbirth_dt'])){$customer_data['grmbirth_dt'] = null;}
	   if(empty($customer_data['brdbirth_dt'])){$customer_data['brdbirth_dt'] = null;}
   	   $customer_data['upd_nm'] = $user_name;
   	   $customer_data['upd_dt'] = date('Y-m-d H:i:s');

	   $this->id = $customer_data['id'];
       if($this->save($customer_data,false,$fields)==false){
       		return array('result'=>false,'message'=>"顧客情報更新に失敗しました。",'reason'=>$this->getDbo()->error."[".date('Y-m-d H:i:s')."]");
       }
       return array('result'=>true);
    }

    /**
     * 顧客基本情報の更新
     * @param unknown $data
     * @param unknown $user_name
     * @return multitype:boolean string |multitype:boolean
     */
    function updateCustomerBasicInfo($data,$user_name){

    	/*
    	$fields = array('customer_cd','first_contact_dt','first_visited_dt','first_contact_person_nm','estimate_created_person_nm',
    			        'process_person_nm','estimate_issued_dt','contracting_dt','wedding_planned_dt','wedding_planned_time','wedding_planned_place',
    			        'reception_planned_place','reception_planned_time','upd_nm','upd_dt');
        */
    	$fields = array('customer_cd','first_visited_dt','first_contact_person_nm','estimate_created_person_nm',
    			        'process_person_nm','wedding_planned_dt','wedding_planned_time','wedding_planned_place',
    			        'reception_planned_place','reception_planned_time','upd_nm','upd_dt');

    	$data['upd_nm'] = $user_name;
    	$data['upd_dt'] = date('Y-m-d H:i:s');

    	$this->id = $data['id'];
    	if($this->save($data,false,$fields)==false){
    		return array('result'=>false,'message'=>"顧客基本情報の更新に失敗しました。",'reason'=>$this->getDbo()->error."[".date('Y-m-d H:i:s')."]");
    	}
    	return array('result'=>true);
    }

    /**
     * 物販・ＧＩＦＴ・その他用の顧客情報の登録
     * @param unknown $customer_status
     * @param unknown $customer_last_nm
     * @param unknown $customer_first_nm
     * @param unknown $credit_dt
     * @param unknown $user_name
     * @return multitype:boolean string |multitype:boolean NULL
     */
    function regiterCustomerForCredit($customer_status,$customer_last_nm ,$customer_first_nm ,$credit_dt,$user_name){

    	$data = array('id'=>null,
    			      'customer_cd'=>$this->_createCustomerCodeForCredit($customer_status,$credit_dt),
    			      'status_id'=>$customer_status,
    			      'grmls_kn'=>$customer_last_nm,
    			      'grmfs_kn'=>$customer_first_nm,
    			      'reg_nm'=>$user_name,
    	              'reg_dt'=>date('Y-m-d H:i:s'));
    	//フィールドの初期化
    	$this->create();
    	if($this->save($data)==false){
    		return array('result'=>false,'message'=>"クレジット用の顧客データの登録に失敗しました。",'reason'=>$this->getDbo()->error."[".date('Y-m-d H:i:s')."]");
    	}
    	return array('result'=>true,'newID'=>$this->getLastInsertID());
    }

    /**
     * 物販・ＧＩＦＴ・その他用の顧客コードを作成(書式：CCyyyy-mmxxxx)
     * @param unknown $customer_status
     * @param unknown $dt
     * @return Ambigous <NULL, string>
     */
    function _createCustomerCodeForCredit($customer_status,$dt){

    	$new_code = null;
    	$tmp_code = null;

    	if($customer_status == CS_BUPPAN){
    		$tmp_code = 'BU';
    	}else if($customer_status == CS_GIFT){
    		$tmp_code = 'GI';
    	}else if($customer_status == CS_EXTRA){
    		$tmp_code = 'EX';
    	}

    	$tmp_code .= substr($dt,0,4).'-'.substr($dt,4,2);

    	$sql = "SELECT MAX(customer_cd) sequence FROM customer_msts WHERE customer_cd like '".$tmp_code."%'";
    	$data = $this->query($sql);
    	if(count($data)==0){
    		$new_code = $tmp_code.'0001';
    	}else{
    		$new_code = $tmp_code.sprintf("%04d",((int)substr($data[0][0]['sequence'],9,4)) + 1);
    	}
        return $new_code;
    }

    /**
     * 旅行またはドレス用の顧客コードを作成
     * @param unknown $customer_nm
     * @param unknown $status_id
     * @param unknown $user_name
     * @return multitype:boolean string |multitype:boolean NULL
     */
    function regiterCustomerForException($customer_nm ,$status_id,$user_name){

    	$data = array('id'=>null,
    			'customer_cd'=>$this->_createCustomerCodeForException($status_id),
    			'status_id'=>$status_id,
    			'grmls_kn'=>$customer_nm,
    			'reg_nm'=>$user_name,
    			'reg_dt'=>date('Y-m-d H:i:s'));
    	//フィールドの初期化
    	$this->create();
    	if($this->save($data)==false){
    		return array('result'=>false,'message'=>"旅行またはドレス、業者用の顧客データの登録に失敗しました。",'reason'=>$this->getDbo()->error."[".date('Y-m-d H:i:s')."]");
    	}
    	return array('result'=>true,'newID'=>$this->getLastInsertID());
    }

    /**
     * 旅行またはドレス用の顧客コードを作成
     * @param unknown $status_id
     * @return Ambigous <NULL, string>
     */
    function _createCustomerCodeForException($status_id){

    	$new_code = null;
    	$tmp_code = null;
    	switch ($status_id){
    		case CS_DRESS:$tmp_code = "DR0000-";
    			          break;
    		case CS_TRIP: $tmp_code = "TR0000-";
    			          break;
    	    case CS_VENDOR: $tmp_code = "VE0000-";
    			          break;
    		defaul:return array('result'=>false,'message'=>"予期しないステータスコードです。",'reason'=>$status_id);
    	}

    	$sql = "SELECT MAX(customer_cd) sequence FROM customer_msts WHERE customer_cd like '".$tmp_code."%'";
    	$data = $this->query($sql);
    	if(count($data)==0){
    		$new_code = $tmp_code.'000001';
    	}else{
    		$new_code = $tmp_code.sprintf("%06d",((int)substr($data[0][0]['sequence'],7,6)) + 1);
    	}
    	return $new_code;
    }

    /**
     * １日に１回挙式日が過ぎた顧客のステータスを更新する
     * @param unknown $user_name
     */
    function autoUpdateCustomerStatusIfWeddingFinished($user_name){

    	App::import("Model", "EnvMst");
    	$env = new EnvMst();

    	//システム環境テーブルから最後にステータス更新を実行した日付を取得する
    	$last_status_update_dt = $env->find('all',array('fields'=>array("status_upd_dt")));
    	$last_status_update_dt = date('Y-m-d',strtotime($last_status_update_dt[0]['EnvMst']['status_upd_dt']));
    	$today = date('Y-m-d');

    	if($last_status_update_dt < $today){
    		$ret = $this->updateCustomerStatusIfWeddingFinished($user_name);
    	}
    }

    /**
     * 挙式日が過ぎた顧客のステータスを更新する
     * @param unknown $user_name
     */
    function updateCustomerStatusIfWeddingFinished($user_name){

    	$tr = ClassRegistry::init('TransactionManager');
    	$tr->begin();

    	App::import("Model", "EnvMst");
    	$env = new EnvMst();

    	$status_paied_count = 0;
    	$status_unpaied_count = 0;

    	App::import("Model", "CreditService");
    	$credit = new CreditService();

    	App::import("Model", "CustomerMstView");
    	$customer = new CustomerMstView();

        $data = $customer->find('all',array('fields'=>array('id','wedding_dt'),'conditions'=>array("status_id"=>array(CS_INVOICED,CS_UNPAIED))));

        for($i=0; $i < count($data);$i++){

        	$customer_id = $data[$i]['CustomerMstView']['id'];
        	$wedding_dt = date('Y-m-d',strtotime($data[$i]['CustomerMstView']['wedding_dt']));
            $today = date('Y-m-d');

            if($wedding_dt < $today){

            	if($credit->isInvoiceMatchForCredit($customer_id)){
            		$this->setPaied($customer_id,$user_name);
            		$status_paied_count++;
            	}else{
            		$this->setUnpaied($customer_id,$user_name);
            		$status_unpaied_count++;
            	}
            }
        }
        $env->updateAll(array("status_upd_dt"=>"'".date('Y-m-d H:i:s')."'","upd_nm"=>"'".$user_name."'",'upd_dt'=>"'".date('Y-m-d H:i:s')."'"));
    	$tr->commit();
    	return array('result'=>true,'message'=>'顧客ステータスの更新に成功しました。挙式済・未入金:'.$status_unpaied_count.'件      挙式済・入金済：'.$status_paied_count.'件	');
    }

    /**
     * 挙式日が過ぎた顧客のステータスを更新する
     * @param unknown $customer_id
     * @param unknown $user_name
     */
    function updateCustomerStatusIfWeddingFinishedByCustomerId($customer_id,$user_name){

    	App::import("Model", "CreditService");
    	$credit = new CreditService();

    	App::import("Model", "ContractTrn");
    	$contract = new ContractTrn();

    	$data = $contract->find('all',array('conditions'=>array("customer_id"=>$customer_id)));

    	if(!empty($data)){

    		$wedding_dt = date('Y-m-d',strtotime($data[0]['ContractTrn']['wedding_dt']));
    		$today = date('Y-m-d');

    		if($wedding_dt < $today){

    			if($credit->isInvoiceMatchForCredit($customer_id)){
    				$this->setPaied($customer_id,$user_name);
    				return array('status_id'=>CS_PAIED,'status_nm'=>'挙式完了・入金済み');
    			}else{
    				$this->setUnpaied($customer_id,$user_name);
    				return array('status_id'=>CS_UNPAIED,'status_nm'=>'挙式完了・未入金');
    			}
    		}
    	}
    	return null;
    }

    /**
     *
     * ステータスが請求書提示済みのユニークな初回見積提示年月を取得する
     * @return 正常： 初回見積提示年月の配列
     *         異常：NULL
     */
    function getGroupOfEstimateIssuedDate(){
    	$sql = "SELECT SUBSTR(estimate_issued_dt,1,7) estimate_issued_dt FROM customer_msts WHERE status_id = ".CS_ESTIMATED.
    			" GROUP BY SUBSTR(estimate_issued_dt,1,7) Order by SUBSTR(estimate_issued_dt,1,7) desc";
    	$data = $this->query($sql);

    	$months = array();
    	for($i=0;$i < count($data);$i++){
    		array_push($months, $data[$i][0]['estimate_issued_dt']);
    	}
    	return $months;
    }  

    /**
     * 初回見積日ベースの顧客情報を月数分取得
     * @param unknown $estimate_issued_dt  スタート年月
     * @param unknown $month_count 取得月数
     * @return multitype:NULL
     */
    function getCustomerListForCandidate($estimate_issued_dt,$month_count,$first_contact_person,$process_person){

        App::import("Model", "CustomerMstView");
    	$customer = new CustomerMstView();

    	$start_dt = date("Y-m", strtotime($estimate_issued_dt." -".($month_count-1)." month"));

    	if($first_contact_person == "ALL" && $process_person == "ALL"){
    		return $customer->find('all',array('conditions'=>array("SUBSTR(estimate_issued_dt,1,7) <="=>$estimate_issued_dt,
    				                                               "SUBSTR(estimate_issued_dt,1,7) >="=>$start_dt,
    				                                               "status_id"=>CS_ESTIMATED),
    				                           'order'=>array("estimate_issued_dt")));

    	}else if($first_contact_person == "ALL"){
    		return $customer->find('all',array('conditions'=>array("SUBSTR(estimate_issued_dt,1,7) <="=>$estimate_issued_dt,
    				                                               "SUBSTR(estimate_issued_dt,1,7) >="=>$start_dt,
    				                                               "status_id"=>CS_ESTIMATED,"process_person_nm"=>$process_person),
    				                           'order'=>array("estimate_issued_dt")));
    	}else if($process_person == "ALL"){
    		return $customer->find('all',array('conditions'=>array("SUBSTR(estimate_issued_dt,1,7) <="=>$estimate_issued_dt,
    				                                               "SUBSTR(estimate_issued_dt,1,7) >="=>$start_dt,
    				                                               "status_id"=>CS_ESTIMATED,"first_contact_person_nm"=>$first_contact_person),
    				                           'order'=>array("estimate_issued_dt")));
    	}else{
    		return $customer->find('all',array('conditions'=>array("SUBSTR(estimate_issued_dt,1,7) <="=>$estimate_issued_dt,
    				                                               "SUBSTR(estimate_issued_dt,1,7) >="=>$start_dt,
    				                                               "status_id"=>CS_ESTIMATED,"process_person_nm"=>$process_person,"first_contact_person_nm"=>$first_contact_person),
    				                           'order'=>array("estimate_issued_dt")));
    	}
    }
    
    /**
     * 契約日ベースの顧客リストを取得する
     * @param unknown $contract_dt
     * @return unknown
     */
    function GetCustomersByContract($contract_dt,$first_contact_person,$process_person){

    	App::import("Model", "ContractTrnView");
    	$contract = new ContractTrnView();

    	if($first_contact_person == "ALL" && $process_person == "ALL"){
    		return $contract->find("all",array("conditions"=>array("SUBSTR(contract_dt,1,7)"=>$contract_dt),'order'=>array("wedding_dt")));

    	}else if($first_contact_person == "ALL"){
    		return $contract->find("all",array("conditions"=>array("SUBSTR(contract_dt,1,7)"=>$contract_dt,"process_person_nm"=>$process_person),'order'=>array("wedding_dt")));
    	}else if($process_person == "ALL"){
    		return $contract->find("all",array("conditions"=>array("SUBSTR(contract_dt,1,7)"=>$contract_dt,"first_contact_person_nm"=>$first_contact_person),'order'=>array("wedding_dt")));
    	}else{
    		return $contract->find("all",array("conditions"=>array("SUBSTR(contract_dt,1,7)"=>$contract_dt,"process_person_nm"=>$process_person,"first_contact_person_nm"=>$first_contact_person),'order'=>array("wedding_dt")));
    	}
    	return $data;
    }

    /**
     * 挙式日ベースの顧客リストを取得する
     * @param unknown $contract_dt
     * @return unknown
     */
    function GetCustomersByWedding($wedding_dt,$first_contact_person,$process_person){

    	App::import("Model", "ContractTrnView");
    	$contract = new ContractTrnView();

    	if($first_contact_person == "ALL" && $process_person == "ALL"){
    		return $contract->find("all",array("conditions"=>array("SUBSTR(wedding_dt,1,7)"=>$wedding_dt,"status_id"=>array(CS_CONTRACTED,CS_INVOICED,CS_PAIED,CS_UNPAIED)),"order"=>array("wedding_dt")));

    	}else if($first_contact_person == "ALL"){
    		return $contract->find("all",array("conditions"=>array("SUBSTR(wedding_dt,1,7)"=>$wedding_dt,"process_person_nm"=>$process_person,"status_id"=>array(CS_CONTRACTED,CS_INVOICED,CS_PAIED,CS_UNPAIED)),"order"=>array("wedding_dt")));
    	}else if($process_person == "ALL"){
    		return $contract->find("all",array("conditions"=>array("SUBSTR(wedding_dt,1,7)"=>$wedding_dt,"first_contact_person_nm"=>$first_contact_person,"status_id"=>array(CS_CONTRACTED,CS_INVOICED,CS_PAIED,CS_UNPAIED)),"order"=>array("wedding_dt")));
    	}else{
    		return $contract->find("all",array("conditions"=>array("SUBSTR(wedding_dt,1,7)"=>$wedding_dt,"process_person_nm"=>$process_person,"first_contact_person_nm"=>$first_contact_person,"status_id"=>array(CS_CONTRACTED,CS_INVOICED,CS_PAIED,CS_UNPAIED)),"order"=>array("wedding_dt")));
    	}
    	return $data;
    }

    /**
     * 挙式日以降の顧客リストを取得する
     * @param unknown $contract_dt
     * @return unknown
     */
    function GetCustomersByMoreThanWedding($wedding_dt,$first_contact_person,$process_person){

    	App::import("Model", "ContractTrnView");
    	$contract = new ContractTrnView();

    	if($first_contact_person == "ALL" && $process_person == "ALL"){
    		return  $contract->find("all",array("conditions"=>array("SUBSTR(wedding_dt,1,7) >"=>$wedding_dt,"status_id"=>array(CS_CONTRACTED,CS_INVOICED,CS_PAIED,CS_UNPAIED)),"order"=>array("wedding_dt")));

    	}else if($first_contact_person == "ALL"){
    		return  $contract->find("all",array("conditions"=>array("SUBSTR(wedding_dt,1,7) >"=>$wedding_dt,"process_person_nm"=>$process_person,"status_id"=>array(CS_CONTRACTED,CS_INVOICED,CS_PAIED,CS_UNPAIED)),"order"=>array("wedding_dt")));
    	}else if($process_person == "ALL"){
    		return  $contract->find("all",array("conditions"=>array("SUBSTR(wedding_dt,1,7) >"=>$wedding_dt,"first_contact_person_nm"=>$first_contact_person,"status_id"=>array(CS_CONTRACTED,CS_INVOICED,CS_PAIED,CS_UNPAIED)),"order"=>array("wedding_dt")));
    	}else{
    		return  $contract->find("all",array("conditions"=>array("SUBSTR(wedding_dt,1,7) >"=>$wedding_dt,"process_person_nm"=>$process_person,"first_contact_person_nm"=>$first_contact_person,"status_id"=>array(CS_CONTRACTED,CS_INVOICED,CS_PAIED,CS_UNPAIED)),"order"=>array("wedding_dt")));
    	}
    	return $data;
    }

    /**
     * [開発用] 連番が「0000」になっている顧客コードを正しい連番に振り直す
     * @return multitype:boolean string |multitype:boolean NULL
     */
    function updateCustomerCodeTemp(){

    	$tr = ClassRegistry::init('TransactionManager');
    	$tr->begin();

    	$sql = "SELECT MAX(SUBSTR(customer_cd,1,4)) sequence FROM customer_msts";
        $data = $this->query($sql);
 	    $next_seq = sprintf("%04d",((int)$data[0][0]['sequence']) + 1);

 	    $sql = "SELECT id , customer_cd FROM customer_msts WHERE customer_cd LIKE '0000%' ORDER BY id ASC";
 	    $data = $this->query($sql);

 	    for($i=0;$i < count($data);$i++){

 	    	$fields = array('customer_cd');
 	    	$customer_data = array();
 	    	$customer_data['customer_cd'] = $next_seq.substr($data[$i]['customer_msts']['customer_cd'],4);

 	    	$this->id = $data[$i]['customer_msts']['id'];
 	    	if($this->save($customer_data,false,$fields)==false){
 	    		return array('result'=>false,'message'=>"顧客コード更新に失敗しました。",'reason'=>$this->getDbo()->error."[".date('Y-m-d H:i:s')."]");
 	    	}
 	    	$next_seq = sprintf("%04d",((int)$next_seq) + 1);
 	    }
 	    $tr->commit();
 	    return array('result'=>true,'count'=>count($data));
    }


    function getLeading1List(){
    	return array(LD1_NONE=>"",LD1_MAIL=>"メール",LD1_PHONE=>"電話");
    }

    function getLeading2List(){
    	return array(LD2_NONE=>"",LD2_GENERAL=>"一般",LD2_INTRODUCING=>"紹介");
    }
}
?>