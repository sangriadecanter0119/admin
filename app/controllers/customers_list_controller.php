<?php
set_time_limit(1300);
class CustomersListController extends AppController
{
 public $name = 'CustomersList';
 public $uses = array('CustomerMst','EstimateTrn','CustomerMstView','CustomerStatusMst','Prefecture','User');
 public $layout = 'cust_list_info_main_tab';
 public $components = array('Auth');
 public $helpers = array('Html','common','Javascript');

 //顧客一覧画面
 function index($page_limit=null)
 {
 	 $search = array();
     $order = "first_contact_dt desc";
     $delimiter = "_";

     //1日に1回挙式日の過ぎた顧客のステータスを更新する
     //$ret =$this->CustomerMst->autoUpdateCustomerStatusIfWeddingFinished($this->Auth->user('username'));

 	 $divisions = $this->Prefecture->GetListOfDivisions();
 //debug($this->Prefecture->GetPrefecturesByDivision(1));

 	 /* フォームからフィルタ条件を送信された場合(POST) */
 	 if (!empty($this->data)) {

 		 $this->Session->write('filter_status_id',$this->data['GoodsMstView']['status_id']);
 		 $this->Session->write('filter_non_display_flg',$this->data['GoodsMstView']['non_display_flg']);
	     $this->Session->write('filter_wedding_planned_dt',$this->data['GoodsMstView']['wedding_planned_dt']);
	     $this->Session->write('order_wedding_planned_dt',$this->data['GoodsMstView']['wedding_planned_dt_order']);
	     $this->Session->write('filter_first_contact_dt',$this->data['GoodsMstView']['first_contact_dt']);
	     $this->Session->write('filter_estimate_issued_dt',$this->data['GoodsMstView']['estimate_issued_dt']);
	     $this->Session->write('filter_customer_name',$this->data['GoodsMstView']['customer_name']);
	     $this->Session->write('filter_pref',$this->data['GoodsMstView']['pref']);
	     $this->Session->write('filter_first_contact_person_name',$this->data['GoodsMstView']['first_contact_person_name']);
	     $this->Session->write('filter_process_person_name',$this->data['GoodsMstView']['process_person_name']);
	     $this->Session->write('filter_phone_no',$this->data['GoodsMstView']['phone_no']);
 	 }
	 /* デフォルト値 :処理年月に挙式予定の成約の顧客を表示 */
	 else if($this->Session->read('filter_status_id')==null){
		$this->Session->write('filter_status_id',-1);
		$this->Session->write('filter_wedding_planned_dt',-1);
		$this->Session->write('order_wedding_planned_dt','');
		$this->Session->write('filter_first_contact_dt',-1);
		$this->Session->write('filter_estimate_issued_dt',-1);
		$this->Session->write('filter_customer_name',"");
		$this->Session->write('filter_pref',-1);
		$this->Session->write('filter_first_contact_person_name',-1);
		$this->Session->write('filter_process_person_name',-1);
		$this->Session->write('filter_non_display_flg',0);
		$this->Session->write('filter_phone_no',"");
	}

	/* フィルター条件がALL(-1)でない場合のみ設定 */
	if($this->Session->read('filter_status_id') != -1){
   	   //$search += array("status_id"=>$this->Session->read('filter_status_id'));
   	   $search += array("status_id" =>explode("_",$this->Session->read('filter_status_id')));
	}

	if($this->Session->read('filter_first_contact_person_name') != -1){
		$search += array("first_contact_person_nm"=>$this->Session->read('filter_first_contact_person_name'));
	}

	if($this->Session->read('filter_process_person_name') != -1){
		$search += array("process_person_nm"=>$this->Session->read('filter_process_person_name'));
	}

	if($this->Session->read('filter_phone_no') != ""){
		 $search += array("OR"=>array(
   	       		                        "grm_phone_no LIKE"=>'%'.$this->Session->read('filter_phone_no').'%',
   	                                    "grm_cell_no  LIKE"=>'%'.$this->Session->read('filter_phone_no').'%',
   	       		                        "brd_phone_no LIKE"=>'%'.$this->Session->read('filter_phone_no').'%',
   	       		                        "brd_cell_no  LIKE"=>'%'.$this->Session->read('filter_phone_no').'%'
   	       ));
	}

    if($this->Session->read('filter_wedding_planned_dt') != -1){
       if($this->Session->read('filter_wedding_planned_dt') == null){
          if($this->Session->read('filter_status_id') <= CS_CONTRACTING){
              $search += array("wedding_planned_dt"=>null);
          }else{
               $search += array("wedding_dt"=>null);
          }
       }else{
          if($this->Session->read('filter_status_id') <= CS_CONTRACTING){
             $search += array("SUBSTR(wedding_planned_dt,1,7)"=>$this->Session->read('filter_wedding_planned_dt'));
          }else{
             $search += array("SUBSTR(wedding_dt,1,7)"=>$this->Session->read('filter_wedding_planned_dt'));
          }
       }
	}
    if($this->Session->read('filter_first_contact_dt') != -1){
       if($this->Session->read('filter_first_contact_dt') == null){
       	    $search += array("first_contact_dt"=>null);
       }else{
       	    $search += array("SUBSTR(first_contact_dt,1,7)"=>$this->Session->read('filter_first_contact_dt'));
       }
	}
	if($this->Session->read('filter_estimate_issued_dt') != -1){
		if($this->Session->read('filter_estimate_issued_dt') == null){
			$search += array("estimate_issued_dt"=>null);
		}else{
			$search += array("SUBSTR(estimate_issued_dt,1,7)"=>$this->Session->read('filter_estimate_issued_dt'));
		}
	}
	if($this->Session->read('filter_non_display_flg') == 0){
		$search += array("non_display_flg"=>DISPLAY);
	}

	/* 顧客名と当道府県の検索条件設定  */
	if($this->Session->read('filter_customer_name') != "" && $this->Session->read('filter_pref') != -1){

   	   $search += array(
   	        array(
   	          "OR"=>array(
   	               "grmls_kj LIKE"=>'%'.$this->Session->read('filter_customer_name').'%',
   	               "grmfs_kj LIKE"=>'%'.$this->Session->read('filter_customer_name').'%',
   	          	   "grmls_kn LIKE"=>'%'.$this->Session->read('filter_customer_name').'%',
   	               "grmfs_kn LIKE"=>'%'.$this->Session->read('filter_customer_name').'%',
   	          	   "grmls_rm LIKE"=>'%'.$this->Session->read('filter_customer_name').'%',
   	          	   "grmfs_rm LIKE"=>'%'.$this->Session->read('filter_customer_name').'%',

   	          	   "brdls_kj LIKE"=>'%'.$this->Session->read('filter_customer_name').'%',
   	               "brdfs_kj LIKE"=>'%'.$this->Session->read('filter_customer_name').'%',
   	          	   "brdls_kn LIKE"=>'%'.$this->Session->read('filter_customer_name').'%',
   	          	   "brdfs_kn LIKE"=>'%'.$this->Session->read('filter_customer_name').'%',
   	          	   "brdls_rm LIKE"=>'%'.$this->Session->read('filter_customer_name').'%',
   	          	   "brdfs_rm LIKE"=>'%'.$this->Session->read('filter_customer_name').'%'
   	              ),
   	             ),
   	        array(
   	          "OR"=>array(
   	               "brd_pref"=>$this->Prefecture->GetPrefecturesByDivision($this->Session->read('filter_pref')),
   	               "grm_pref"=>$this->Prefecture->GetPrefecturesByDivision($this->Session->read('filter_pref'))
   	                     ),
   	             ),
	   );
	}else{
	    /* 顧客名の検索条件設定  */
        if($this->Session->read('filter_customer_name') != ""){
   	       $search += array("OR"=>array(
   	       		                        "grmls_kj LIKE"=>'%'.$this->Session->read('filter_customer_name').'%',
   	                                    "grmfs_kj LIKE"=>'%'.$this->Session->read('filter_customer_name').'%',
   	       		                        "grmls_kn LIKE"=>'%'.$this->Session->read('filter_customer_name').'%',
   	       		                        "grmfs_kn LIKE"=>'%'.$this->Session->read('filter_customer_name').'%',
   	       		                        "grmls_rm LIKE"=>'%'.$this->Session->read('filter_customer_name').'%',
   	       	 	                        "grmfs_rm LIKE"=>'%'.$this->Session->read('filter_customer_name').'%',

   	                                    "brdls_kj LIKE"=>'%'.$this->Session->read('filter_customer_name').'%',
   	                                    "brdfs_kj LIKE"=>'%'.$this->Session->read('filter_customer_name').'%',
   	       		                        "brdls_kn LIKE"=>'%'.$this->Session->read('filter_customer_name').'%',
   	       		                        "brdfs_kn LIKE"=>'%'.$this->Session->read('filter_customer_name').'%',
   	       		                        "brdls_rm LIKE"=>'%'.$this->Session->read('filter_customer_name').'%',
   	                                    "brdfs_rm LIKE"=>'%'.$this->Session->read('filter_customer_name').'%'
   	       ));
	    }

        /* 当道府県の検索条件設定 */
       if($this->Session->read('filter_pref') != -1){
   	       $search += array("OR"=>array("brd_pref"=>$this->Prefecture->GetPrefecturesByDivision($this->Session->read('filter_pref')),
   	                                    "grm_pref"=>$this->Prefecture->GetPrefecturesByDivision($this->Session->read('filter_pref'))
   	       ));
	   }
	}

	if($this->Session->read('order_wedding_planned_dt') != ''){
            if($this->Session->read('filter_status_id') <= CS_CONTRACTING){
               	$order = "wedding_planned_dt ".$this->Session->read('order_wedding_planned_dt');
            }else{
                $order = "wedding_dt ".$this->Session->read('order_wedding_planned_dt');
            }
	}

 	/*
 	 * 顧客リストの表示件数をセッション情報に保持する
 	 */
 	/* セッション開始後初回表示時 又は他画面からの遷移 */
    if($page_limit == null){
 	   /* セッション開始後初回表示時 */
       if($this->Session->read('cust_list_page_limit')==null){
       	  $this->Session->write('cust_list_page_limit',50);
       }
 	}
 	/* 同画面内での表示ページ数の変更時 */
 	else{
 		$this->Session->write('cust_list_page_limit',$page_limit);
 	}
 	$page_limit = 50;//$this->Session->read('cust_list_page_limit');

 	//ページネーション設定
 	$this->paginate = array(
                            'limit' =>$this->Session->read('cust_list_page_limit'),
 	                        'order' =>$order
                            );

 	$this->set("data",$this->paginate('CustomerMstView',$search));

 	/* フィルタ条件をVIEWで保持する */
 	$this->set("status_id"          ,$this->Session->read('filter_status_id'));
 	$this->set("non_display_flg"    ,$this->Session->read('filter_non_display_flg'));
 	$this->set("wedding_planned_dt" ,$this->Session->read('filter_wedding_planned_dt'));
 	$this->set("wedding_planned_dt_order" ,$this->Session->read('order_wedding_planned_dt'));
 	$this->set("estimate_issued_dt" ,$this->Session->read('filter_estimate_issued_dt'));
 	$this->set("first_contact_dt" ,$this->Session->read('filter_first_contact_dt'));
 	$this->set("customer_name" ,$this->Session->read('filter_customer_name'));
 	$this->set("pref" ,$this->Session->read('filter_pref'));
 	$this->set("first_contact_person_name" ,$this->Session->read('filter_first_contact_person_name'));
 	$this->set("process_person_name" ,$this->Session->read('filter_process_person_name'));
 	$this->set("phone_no" ,$this->Session->read('filter_phone_no'));

 	$this->set("page_limit",$this->Session->read('cust_list_page_limit'));
 	$this->set("customer_status",$this->CustomerStatusMst->find('all'));
  	$this->set("wedding_dt_list",$this->CustomerMst->getGroupOfWeddingMonth());
  	$this->set("estimate_issued_dt_list",$this->CustomerMst->getGroupOfEestimateIssuedMonth());
  	$this->set("first_contact_dt_list",$this->CustomerMst->getGroupOfFirstContactedMonth());
  	$this->set("division_list",$divisions);
  	$this->set("first_contact_person_list",$this->CustomerMst->getGroupOfFirstContactPersonInStatusId($this->Session->read('filter_status_id'),$delimiter));
  	$this->set("process_person_list",$this->CustomerMst->getGroupOfProcessPersonInStatusId($this->Session->read('filter_status_id'),$delimiter));

 	//メニューとサブメニューのアクティブ化
 	$this->set("menu_customers","current");
 	$this->set("menu_customer","disable");
 	$this->set("menu_fund","");

 	$this->set("sub_menu_customers_list","current");
 	$this->set("sub_menu_customers_company_contact","");
 	$this->set("sub_menu_customers_wedding_reserve","");
 	$this->set("sub_menu_customers_schedules","");
 	$this->set("sub_menu_customers_by_each_attendant_list","");
 	$this->set("sub_menu_customers_contract_list","");
 	$this->set("sub_menu_attendant_state","");

 	$this->set("sub_title","顧客一覧");
 	$this->set("user",$this->Auth->user());
 }

 //顧客登録画面
 function addCustomer()
 {
 	$this->layout = 'edit_mode';

 	if(!empty($this->data))
 	{
 	   $tr = ClassRegistry::init('TransactionManager');
	   $tr->begin();

	   $this->layout = '';
 	   $this->autoRender =false;
 	   configure::write('debug', 0);

 	   /* バリデーションチェック */
 	   if(empty($this->data['CustomerMst']['grmls_kj']) && empty($this->data['CustomerMst']['grmfs_kj]']) && empty($this->data['CustomerMst']['grmls_kn']) &&
 	      empty($this->data['CustomerMst']['grmfs_kn']) && empty($this->data['CustomerMst']['grmls_rm']) && empty($this->data['CustomerMst']['grmfs_rm']) &&
 	      empty($this->data['CustomerMst']['brdls_kj']) && empty($this->data['CustomerMst']['brdfs_kj]']) && empty($this->data['CustomerMst']['brdls_kn']) &&
 	      empty($this->data['CustomerMst']['brdfs_kn']) && empty($this->data['CustomerMst']['brdls_rm']) && empty($this->data['CustomerMst']['brdfs_rm'])){
 	          return json_encode(array('result'=>false,'message'=>"登録に失敗しました。",'reason'=>'最低1項目の顧客名情報が必要です。'));
 	   }

 	   $this->data['CustomerMst']['id'] = null;
 	   if(empty($this->data['CustomerMst']['grmbirth_dt'])){$this->data['CustomerMst']['grmbirth_dt'] = null;}
 	   if(empty($this->data['CustomerMst']['brdbirth_dt'])){$this->data['CustomerMst']['brdbirth_dt'] = null;}
 	   if(empty($this->data['CustomerMst']['wedding_planned_dt'])){$this->data['CustomerMst']['wedding_planned_dt'] = null;}
 	   if(empty($this->data['CustomerMst']['first_contact_dt'])){$this->data['CustomerMst']['first_contact_dt'] = null;}
 	   if(empty($this->data['CustomerMst']['first_visited_dt'])){$this->data['CustomerMst']['first_visited_dt'] = null;}
       //顧客コード作成
       $this->data['CustomerMst']['customer_cd'] = $this->CustomerMst->createCustomerCode($this->data['CustomerMst']['first_contact_dt'],$this->data['CustomerMst']['wedding_planned_dt']);
 	   $this->data['CustomerMst']['reg_nm'] = $this->Auth->user('username');
 	   $this->data['CustomerMst']['reg_dt'] = date('Y-m-d H:i:s');

 	   if($this->CustomerMst->save($this->data)==false){
 	   	  return json_encode(array('result'=>false,'message'=>"登録に失敗しました。",'reason'=>$this->CustomerMst->getDbo()->error."[".date('Y-m-d H:i:s')."]"));
 	   }
 	     $tr->commit();
  	     return json_encode(array('result'=>true,'message'=>'登録完了しました。','code'=>$this->data['CustomerMst']['customer_cd']));
 	}else{

 	  //登録時のステータスは[問合せ]のみ
 	  $this->set("status_list",$this->CustomerStatusMst->find('all',array('conditions'=>array("id"=>CS_CONTACT))));
 	  //担当者リスト
 	  $this->set("attendant_list",$this->User->GetAllDisplayName());
 	  //導線1リスト
 	  $this->set("leading1_list",$this->CustomerMst->getLeading1List());
 	  //導線2リスト
 	  $this->set("leading2_list",$this->CustomerMst->getLeading2List());
 	  //メニューとサブメニューのアクティブ化
 	  $this->set("menu_customers","current");
 	  $this->set("menu_customer","disable");
 	  $this->set("menu_fund","");

 	  $this->set("sub_menu_customers_list","current");
 	  $this->set("sub_menu_customers_company_contact","");
 	  $this->set("sub_menu_customers_wedding_reserve","");
 	  $this->set("sub_menu_customers_schedules","");
 	  $this->set("sub_menu_customers_by_each_attendant_list","");
 	  $this->set("sub_menu_customers_contract_list","");
 	  $this->set("sub_menu_attendant_state","");

 	  $this->set("sub_title","顧客追加");
 	  $this->set("user",$this->Auth->user());
 	}
 }


 //顧客詳細画面へ遷移
 function goToCustomerInfo($customer_id)
 {
 	$this->Session->write('customer_id',$customer_id);
 	$this->redirect('/customer_info');
 }

 /**
  * EXCEL出力
  * @param unknown $file_type
  */
 function export($file_type)
 {
    $save_filename = null;
    $temp_filename = null;
    $render_name = null;
    $sheet_name = null;
    $order = "first_contact_dt desc";
    $search = array();


   switch (strtoupper($file_type)) {

   	case "EXCEL_CUSTOMER_LIST":
   		$sheet_name = "顧客リスト";
   		$save_filename = mb_convert_encoding("顧客リスト.xlsx", "SJIS", "AUTO");
   		$render_name = "excel_customer_list";
   		break;

   	case "EXCEL_NEW_YEARS_CARD":
   		         $sheet_name = "年賀状リスト";
   		         $save_filename = mb_convert_encoding("年賀状リスト.xlsx", "SJIS", "AUTO");
                 $render_name = "excel";
   	       	     break;

   	case "EXCEL_CUSTOMER_MAIL":
   	    	     $sheet_name = "メールリスト";
   	       	     $save_filename = mb_convert_encoding("メールリスト.xlsx", "SJIS", "AUTO");
   	       	     $render_name = "excel_customer_mail";
   	       	     break;

   	default:
   		    $this->cakeError("error", array("message" => "予期しないファイルタイプ[{$file_type}]です。"));
       	    break;
   }

	/* フィルター条件がALL(-1)でない場合のみ設定 */
	if($this->Session->read('filter_status_id') != -1){
   	   //$search += array("status_id"=>$this->Session->read('filter_status_id'));
	   //$search += array("status_id" =>explode("_",$this->Session->read('filter_status_id')))
		$status_ = explode("_",$this->Session->read('filter_status_id'));
		if(count($status_) == 1){
			$search += array("status_id" =>$status_[0]);
		}else{
			$search += array("status_id" =>$status_);
		}
	}

	if($this->Session->read('filter_first_contact_person_name') != -1){
		$search += array("first_contact_person_nm"=>$this->Session->read('filter_first_contact_person_name'));
	}

	if($this->Session->read('filter_process_person_name') != -1){
		$search += array("process_person_nm"=>$this->Session->read('filter_process_person_name'));
	}

	if($this->Session->read('filter_phone_no') != ""){
		$search += array("OR"=>array(
				"grm_phone_no LIKE"=>'%'.$this->Session->read('filter_phone_no').'%',
				"grm_cell_no  LIKE"=>'%'.$this->Session->read('filter_phone_no').'%',
				"brd_phone_no LIKE"=>'%'.$this->Session->read('filter_phone_no').'%',
				"brd_cell_no  LIKE"=>'%'.$this->Session->read('filter_phone_no').'%'
		));
	}

    if($this->Session->read('filter_wedding_planned_dt') != -1){
       if($this->Session->read('filter_wedding_planned_dt') == null){
          if($this->Session->read('filter_status_id') <= CS_CONTRACTING){
              $search += array("wedding_planned_dt"=>null);
          }else{
               $search += array("wedding_dt"=>null);
          }
       }else{
          if($this->Session->read('filter_status_id') <= CS_CONTRACTING){
             $search += array("SUBSTR(wedding_planned_dt,1,7)"=>$this->Session->read('filter_wedding_planned_dt'));
          }else{
             $search += array("SUBSTR(wedding_dt,1,7)"=>$this->Session->read('filter_wedding_planned_dt'));
          }
       }
	}
    if($this->Session->read('filter_first_contact_dt') != -1){
       if($this->Session->read('filter_first_contact_dt') == null){
       	    $search += array("first_contact_dt"=>null);
       }else{
       	    $search += array("SUBSTR(first_contact_dt,1,7)"=>$this->Session->read('filter_first_contact_dt'));
       }
	}
	if($this->Session->read('filter_estimate_issued_dt') != -1){
		if($this->Session->read('filter_estimate_issued_dt') == null){
			$search += array("estimate_issued_dt"=>null);
		}else{
			$search += array("SUBSTR(estimate_issued_dt,1,7)"=>$this->Session->read('filter_estimate_issued_dt'));
		}
	}

	/* 顧客名と当道府県の検索条件設定  */
	if($this->Session->read('filter_customer_name') != "" && $this->Session->read('filter_pref') != -1){

   	   $search += array(
   	        array(
   	          "OR"=>array(
   	               "grmls_kj LIKE"=>'%'.$this->Session->read('filter_customer_name').'%',
   	               "grmfs_kj LIKE"=>'%'.$this->Session->read('filter_customer_name').'%',
   	          	   "grmls_kn LIKE"=>'%'.$this->Session->read('filter_customer_name').'%',
   	               "grmfs_kn LIKE"=>'%'.$this->Session->read('filter_customer_name').'%',
   	          	   "grmls_rm LIKE"=>'%'.$this->Session->read('filter_customer_name').'%',
   	          	   "grmfs_rm LIKE"=>'%'.$this->Session->read('filter_customer_name').'%',

   	          	   "brdls_kj LIKE"=>'%'.$this->Session->read('filter_customer_name').'%',
   	               "brdfs_kj LIKE"=>'%'.$this->Session->read('filter_customer_name').'%',
   	          	   "brdls_kn LIKE"=>'%'.$this->Session->read('filter_customer_name').'%',
   	          	   "brdfs_kn LIKE"=>'%'.$this->Session->read('filter_customer_name').'%',
   	          	   "brdls_rm LIKE"=>'%'.$this->Session->read('filter_customer_name').'%',
   	          	   "brdfs_rm LIKE"=>'%'.$this->Session->read('filter_customer_name').'%'
   	              ),
   	             ),
   	        array(
   	          "OR"=>array(
   	               "brd_pref"=>$this->Prefecture->GetPrefecturesByDivision($this->Session->read('filter_pref')),
   	               "grm_pref"=>$this->Prefecture->GetPrefecturesByDivision($this->Session->read('filter_pref'))
   	                     ),
   	             ),
	   );
	}else{
	    /* 顧客名の検索条件設定  */
        if($this->Session->read('filter_customer_name') != ""){
   	       $search += array("OR"=>array(
   	       		                        "grmls_kj LIKE"=>'%'.$this->Session->read('filter_customer_name').'%',
   	                                    "grmfs_kj LIKE"=>'%'.$this->Session->read('filter_customer_name').'%',
   	       		                        "grmls_kn LIKE"=>'%'.$this->Session->read('filter_customer_name').'%',
   	       		                        "grmfs_kn LIKE"=>'%'.$this->Session->read('filter_customer_name').'%',
   	       		                        "grmls_rm LIKE"=>'%'.$this->Session->read('filter_customer_name').'%',
   	       	 	                        "grmfs_rm LIKE"=>'%'.$this->Session->read('filter_customer_name').'%',

   	                                    "brdls_kj LIKE"=>'%'.$this->Session->read('filter_customer_name').'%',
   	                                    "brdfs_kj LIKE"=>'%'.$this->Session->read('filter_customer_name').'%',
   	       		                        "brdls_kn LIKE"=>'%'.$this->Session->read('filter_customer_name').'%',
   	       		                        "brdfs_kn LIKE"=>'%'.$this->Session->read('filter_customer_name').'%',
   	       		                        "brdls_rm LIKE"=>'%'.$this->Session->read('filter_customer_name').'%',
   	                                    "brdfs_rm LIKE"=>'%'.$this->Session->read('filter_customer_name').'%'
   	       ));
	    }

        /* 当道府県の検索条件設定 */
       if($this->Session->read('filter_pref') != -1){
   	       $search += array("OR"=>array("brd_pref"=>$this->Prefecture->GetPrefecturesByDivision($this->Session->read('filter_pref')),
   	                                    "grm_pref"=>$this->Prefecture->GetPrefecturesByDivision($this->Session->read('filter_pref'))
   	       ));
	   }
	}

	if($this->Session->read('order_wedding_planned_dt') != ''){
            if($this->Session->read('filter_status_id') <= CS_CONTRACTING){
               	$order = "wedding_planned_dt ".$this->Session->read('order_wedding_planned_dt');
            }else{
                $order = "wedding_dt ".$this->Session->read('order_wedding_planned_dt');
            }
	}
 	//ページネーション設定
 	/*$this->paginate = array(
 			                'limit'=>null,
 	                        'order' =>$order
                            );
    $this->set("tmp",$search);
    $this->set("customers",$this->paginate('CustomerMstView',$search));*/
 	$this->set("customers",$this->CustomerMstView->find('all',array('conditions'=>$search,'order'=>$order)));

   $this->layout = false;

   //導線1リスト
   $this->set("leading1_list",$this->CustomerMst->getLeading1List());
   //導線2リスト
   $this->set("leading2_list",$this->CustomerMst->getLeading2List());

   $this->set( "sheet_name", $sheet_name);
   $this->set( "filename", $save_filename );
   $this->set( "template_file", $temp_filename);
   $this->render($render_name);
 }

 /**
  * [開発用]顧客コードの更新
  */
 function updateCustomerCode(){

 	$this->layout = '';
 	$this->autoRender =false;
 	configure::write('debug', 0);

 	$ret = $this->CustomerMst->updateCustomerCodeTemp();
 	return json_encode($ret);
 }

 /**
  * [開発用]挙式済みの顧客ステータスの更新
  */
 function updateWeddingCustomerStatus(){

 	$this->layout = '';
 	$this->autoRender =false;
 	configure::write('debug', 0);

 	$ret = $this->CustomerMst->updateCustomerStatusIfWeddingFinished($this->Auth->user('username'));
 	return json_encode($ret);
 }


}
?>
