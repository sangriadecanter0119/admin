<?php
class Mail extends AppModel {
    var $useTable = false;
      
  /**
   * 
   * デミリターで分割した複数emailアドレスのバリデーション
   * @param $emails
   * @param $demiliter
   * @return [正常]:NULL
   *         [異常]:エラーメッセ-ジ
   */
  function _isValidEmails($email,$demiliter)
  {
       $emails = explode($demiliter, $email);
 	   for($i=0;$i < count($emails);$i++)
 	   { 	   	  
 	        $ret = $this->_isValidEmail($emails[$i]);
 	        if($ret != null){ return $ret;} 	   	             
 	   }  	
 	   return null;
  }
  
  /**
   * 
   * emailアドレスのバリデーション
   * @param $email
   * @return [正常]:NULL
   *         [異常]:エラーメッセ-ジ
   */
  function _isValidEmail($email)
  {
       if (preg_match('/^[^0-9][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[@][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[.][a-zA-Z]{2,4}$/',$email)==false) {          
       	return $email;              
       }
       return null;  	
  }
  
  /**
   * 
   * emailアドレスのバリデーション入口
   * @param $data
   * @return [正常]:NULL
   *         [異常]:エラーメッセ-ジ
   */
  function _validate(&$data)
  {
  	   /* 宛先 */
       $ret = $this->_analizeEmail($data,"RECEIVER");
 	   if($ret != null){  return "メールアドレスが正しくありません。   ".$ret ; } 	   
 	   
       $ret = $this->_isValidEmails($data['ContactReceiverTrn']['receiver_mail'],";");
 	   if($ret != null){  return "メールアドレスが正しくありません。   ".$ret ; }
 	   
 	   /* CC */
       $ret =  $this->_analizeEmail($data,"CC");
 	   if($ret != null){  return "メールアドレスが正しくありません。   ".$ret ; }
 	   
 	   $ret =  $this->_isValidEmails($data['ContactReceiverTrn']['cc_mail'],";");
 	   if($ret != null){  return "メールアドレスが正しくありません。   ".$ret ; }
 	   
 	   /* BCC */
       $ret =  $this->_analizeEmail($data,"BCC");	
 	   if($ret != null){  return "メールアドレスが正しくありません。   ".$ret ; }
 	   
 	   $ret =  $this->_isValidEmails($data['ContactReceiverTrn']['bcc_mail'],";");	
 	   if($ret != null){  return "メールアドレスが正しくありません。   ".$ret ; }
  	
  	   return null;  	
  }
  
  /**
   * 
   * mayumi"<info.realweddings@gmail.com>"形式のアドレス表示からemailアドレスのみを切り出す
   * @param $data
   * @param $type
   */
  function _analizeEmail(&$data,$type)
  {
  	  $tmp_data = null;
  	  switch(strtoupper($type))
  	  {
  	  	case "RECEIVER": $tmp_data = $data['ContactReceiverTrn']['receiver_mail'];
  	  	                 break;
  	  	case "CC": $tmp_data = $data['ContactReceiverTrn']['cc_mail'];
  	  	                 break;
  	  	case "BCC": $tmp_data = $data['ContactReceiverTrn']['bcc_mail'];
  	  	                 break;
  	  	default:return "予期しないメールタイプ[".$type."]です。";
  	  }
  	  
  	  $tmp_url = explode(",",$tmp_data);
      $url="";
    
      for($i=0;$i < count($tmp_url);$i++){
        if($tmp_url[$i] != ""){

            $start_pos = strpos($tmp_url[$i],"<");
            $end_pos   = strpos($tmp_url[$i],">");
                           
            //手動入力アドレス
            if(($start_pos == -1) && ($end_pos == -1)){
          	  $url += $tmp_url[$i] + ";";
            //アドレス帳から選択アドレス
            }else if(($start_pos > -1) && ($end_pos > -1)){
            
          	   //アドレス帳からの選択の場合は [>]で1つでなければならない
          	   if(count(explode('>',$tmp_url[$i]))-1 != 1){ return "メールアドレスが正しくありません。。  ".$tmp_url[$i]; }
               //アドレス帳からの選択の場合は [<]で1つでなければならない
          	   if(count(explode('<',$tmp_url[$i]))-1 != 1){ return "メールアドレスが正しくありません。。  ".$tmp_url[$i]; }

          	              	   
          	   //アドレス帳からの選択の場合最後は [>]で終了しなければならない
               if(substr($tmp_url[$i],strlen($tmp_url[$i])-1,1) != ">"){ return "メールアドレスが正しくありません。。  ".$tmp_url[$i]; }
            
          	   $url .= substr($tmp_url[$i],$start_pos+1,$end_pos - $start_pos -1).";";     
          	        	           	   
             }else{
                return "メールアドレスが正しくありません。。  ".$tmp_url[$i];
             }                    
        }
     }
     
     switch(strtoupper($type))
  	  {
  	  	case "RECEIVER": $data['ContactReceiverTrn']['receiver_mail'] = $url;
  	  	                 break;
  	  	case "CC":  $data['ContactReceiverTrn']['cc_mail']= $url;
  	  	                 break;
  	  	case "BCC": $data['ContactReceiverTrn']['bcc_mail']= $url;
  	  	                 break;  	  	
  	  }
  	
     return null;   	
  }
 
  /**
   * 
   * メール送信とメールDB保存の開始
   * @param $data
   * @param $customer_id
   * @param $username
   */
  function sendMail($data,$customer_id,$user)
  {
  	  $tr = ClassRegistry::init('TransactionManager');
	  $tr->begin();
	 
  	  /* emailアドレスチェック  */
  	  $ret = $this->_validate($data);
 	  if($ret != null){ return $ret; }

      /* メールDB保存 */
 	  $ret = $this->_saveMail($data,$customer_id,$user);
 	  if($ret != null){ return $ret; }
 	  
 	  /* メール送信 */ 	  
 	  //$ret = $this->_sendMail($data,$user);
 	  //if($ret != null){ return $ret; }
 	   	   	  
 	  $tr->commit();     
      return null;
  }
  
  /**
   * 
   * メール送信実行
   * @param $data
   */
  function _sendMail($data,$user)
  {
  	  App::import('Component','Qdmail');
  	  App::import('Component','Qdsmtp');
      $gdmail = new Qdmail();
      /*環境定義の読み込み */
      App::import("Model", "EnvMst");  	
  	  $env = new EnvMst();
  	  $env_data = $env->find("first");
  	   	
  	  /* 宛先アドレスの設定 */  	  
  	  $tmp_receiver = explode(";",$data['ContactReceiverTrn']['receiver_mail']);  	 
  	  
  	  $receivers = array();
  	  for($i=0;$i < count($tmp_receiver);$i++)
  	  {
  	  	if($tmp_receiver[$i] != null){
  	  	  array_push($receivers, array($tmp_receiver[$i],""));
  	  	}
  	  }
  	   	  
  	  /* CCアドレスの設定 */
 	  $tmp_ccs = explode(";",$data['ContactReceiverTrn']['cc_mail']);
 	  $ccs = array();
      for($i=0;$i < count($tmp_ccs);$i++)
  	  {
  	  	if($tmp_ccs[$i] != null){
  	  	   array_push($ccs, array($tmp_ccs[$i],""));
  	  	}
  	  }
  	  
  	  /* BCCアドレスの設定 */
 	  $tmp_bccs = explode(";",$data['ContactReceiverTrn']['bcc_mail']);
      $bccs = array();
 	  for($i=0;$i < count($tmp_bccs);$i++)
  	  {
  	  	if($tmp_bccs[$i] != null){
  	  	  array_push($bccs, array($tmp_bccs[$i],""));
  	  	}
  	  }
  	 
  	  /* メール送信エラーの場合
 	   *   対処法:OpenSSLを有効にする
 	   *   内容:php.ini -> extension=php_openssl.dllをコメントアウト
       *   場所:\xampp\apache\bin\php.ini
       *   
       *   *userには[gmail.com]を含める
 	   */
 	  //メール送信
 	  if(empty($user['User']['email_username'])){return "メールのユーザー名が設定されてません。";}
      if(empty($user['User']['email_password'])){return "メールのパスワードが設定されてません。";}
  
 	  $gdmail->smtp(true);
      $gdmail->smtpServer(
              array(
               'host'     => $env_data['EnvMst']['mail_host'],
               'port'     => $env_data['EnvMst']['mail_port'],  
               'from'     => $data['ContactTrn']['sender_email'],
               'protocol' => $env_data['EnvMst']['mail_protocol'],
               'user'     => $user['User']['email_username'],
               'pass'     => $user['User']['email_password'],
              ));
 	                 
       //debug($receivers);
       //debug($ccs);
       //debug($bccs);
       $gdmail->to($receivers); 	   
       if(!empty($ccs)){ $gdmail->cc($ccs);}
 	   if(!empty($bccs)){$gdmail->bcc($bccs);}
 	   $gdmail->subject($data['ContactTrn']['title']);
       $gdmail->from($data['ContactTrn']['sender_email'] , $data['ContactTrn']['sender_nm'] );
       $gdmail->text($data['ContactTrn']['content']);
              
       /*  添付ファイル
              $this->Qdmail->simpleAttach(true);  
              $attach = array();  
              $attach[] = $this->data['Join']['kao']['tmp_name'];  
              $attach[] = $this->data['Join']['menkyo']['tmp_name'];  
              $this->Qdmail->attach($attach);  
       */
       //送信実行
       if($gdmail->send()==false){return "メール送信に失敗しました。";}
  	   return null;
  }
  
  /**
   * 
   * メールDB保存実行
   * @param $data
   * @param $customer_id
   * @param $username
   */
  function _saveMail($data,$customer_id,$user)
  {
  	  App::import("Model", "ContactTrn");  	
  	  $contact = new ContactTrn();
  	   	   	    	
  	   //DB登録
       $data['ContactTrn']['customer_id'] = $customer_id;          
 	   $data['ContactTrn']['contact_no'] = $contact->getMaxSequenceNo($customer_id);
 	   $data['ContactTrn']['reg_nm'] = $user['User']['username'];
 	   $data['ContactTrn']['reg_dt'] = date('Y-m-d H:i:s');
       $contact->create();
 	   if($contact->save($data)==false){ return "DB登録に失敗しました。 ".$this->ContactTrn->getDbo()->error."[".date('Y-m-d H:i:s')."]";} 		
 	         
 	   $last_id = $contact->getLastInsertID();
       $data['ContactReceiverTrn']['contact_id'] = $last_id;
       $data['ContactReceiverTrn']['reg_nm'] = $user['User']['username'];
 	   $data['ContactReceiverTrn']['reg_dt'] = date('Y-m-d H:i:s');

 	   $receivers = $data['ContactReceiverTrn']['receiver_mail'] != null ? explode(";",$data['ContactReceiverTrn']['receiver_mail']) : null;
 	   $ccs = $data['ContactReceiverTrn']['cc_mail']!=null ? explode(";",$data['ContactReceiverTrn']['cc_mail']) : null;
 	   $bccs = $data['ContactReceiverTrn']['bcc_mail'] != null ? explode(";",$data['ContactReceiverTrn']['bcc_mail']) : null;

 	   /* メイン送信先 保存 */
 	   $ret = $this->_saveContactReciever($data, $receivers, RECEIVER);
 	   if($ret != null){ return $ret;}
 	   
 	   /* CC送信先 保存 */ 	 
 	   $ret =$this->_saveContactReciever($data, $ccs, CC);
       if($ret != null){ return $ret;}
       
 	   /* BCC送信先保存  */
 	   $ret =$this->_saveContactReciever($data, $bccs, BCC);
       if($ret != null){ return $ret;}
 	 
 	   return null;
  }
  
  /**
   * 
   * 送り先情報のDB保存
   * @param $data
   * @param $mails
   * @param $category
   */
  function _saveContactReciever($data,$mails,$category)
  {  	   
  	   App::import("Model", "ContactReceiverTrn");  	
  	   $contact_receiver = new ContactReceiverTrn();  
  	   App::import("Model", "User");  	
  	   $user = new User();  
  	   App::import("Model", "VendorMst");  	
  	   $vendor = new VendorMst();
  	 
 	   for($i=0;$i < count($mails);$i++)
 	   { 
 	   	  if($mails[$i] != null){
 	   	    $data['ContactReceiverTrn']['name'] = null;
 	   	    /* ユーザーテーブルまたはベンダーテーブルに登録されているemailなら名前を設定する */
 	   	    $sender_name = $vendor->find('all',array("conditions"=>array("email"=>$mails[$i])));
 	   	    if(count($sender_name)==1){
 	   	    	$data['ContactReceiverTrn']['name'] = $sender_name[0]['VendorMst']['attend_nm'];
 	   	    }else{
 	   	        $sender_name = $user->find('all',array("conditions"=>array("email"=>$mails[$i])));	   	   
 	   	        if(count($sender_name)==1){ 	   	        	
 	   	    	  $data['ContactReceiverTrn']['name'] = $sender_name[0]['user']['username'];
 	   	        }	
 	   	    }
 	   
 	    	$data['ContactReceiverTrn']['email'] = $mails[$i]; 	      
 	       	$data['ContactReceiverTrn']['receiver_kbn'] = $category; 	         
            $contact_receiver->create();
 	        if($contact_receiver->save($data)==false){ return "DB登録に失敗しました。   ".$contact_receiver->getDbo()->error."[".date('Y-m-d H:i:s')."]";}
 	   	  } 	
 	    } 	        
 	    return null; 	
  }
}
?>