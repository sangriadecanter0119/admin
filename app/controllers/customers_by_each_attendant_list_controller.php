<?php
class CustomersByEachAttendantListController extends AppController
{

 public $name = 'CustomersByEachAttendantList';
 public $uses = array('CustomerMst','CustomersContractListService','AttendantStateService','User');
 public $layout = 'cust_list_info_main_tab';
 public $components = array('Auth','RequestHandler');
 public $helpers = array('Html','common','Javascript');

 /**
  * 担当者別顧客一覧
  *
  */
 function index()
 {
 	$showing_month=6;
  	$estimate_issued_dt = date("Y")."-01";
  	$first_contact_person = "ALL";
  	$process_person = "ALL";

 	if (!empty($this->data)) {
 		/* フィルタ条件変更*/
 		$showing_month = $this->data['GoodsMstView']['showing_month'];
 		$estimate_issued_dt = $this->data['GoodsMstView']['estimate_issued_dt'];
 		$first_contact_person = $this->data['GoodsMstView']['first_contact_person'];
 		$process_person = $this->data['GoodsMstView']['process_person'];
 		$this->Session->write("filter_cs_estimate_issued_dt",$estimate_issued_dt);
 		$this->Session->write("filter_cs_first_contact_person",$first_contact_person);
 		$this->Session->write("filter_cs_process_person",$process_person);
        $this->Session->write("filter_cs_showing_month",$showing_month);
 	}
 	/* デフォルト値 :表示年月に処理年の１月を初期表示とする */
 	else{
 		if($this->Session->read("filter_cs_showing_month") == null){
 			$this->Session->write("filter_cs_showing_month",$showing_month);
 		}else{
 			$showing_month = $this->Session->read("filter_cs_showing_month");
 		}

 		if($this->Session->read("filter_cs_estimate_issued_dt") == null){
 			$this->Session->write("filter_cs_estimate_issued_dt",$estimate_issued_dt);
 		}else{
 			$estimate_issued_dt = $this->Session->read("filter_cs_estimate_issued_dt");
 		}

 		if($this->Session->read("filter_cs_first_contact_person") == null){
 			$this->Session->write("filter_cs_first_contact_person","ALL");
 			$first_contact_person = $this->Session->read("filter_cs_first_contact_person");
 		}else{
 			$first_contact_person = $this->Session->read("filter_cs_first_contact_person");
 		}

 		if($this->Session->read("filter_cs_process_person") == null){
 			$this->Session->write("filter_cs_process_person","ALL");
 			$process_person = $this->Session->read("filter_cs_process_person");
 		}else{
 			$process_person = $this->Session->read("filter_cs_process_person");
 		}
 	}

 	//見積提示済みデータ
 	$this->set('estimate_data',$this->CustomerMst->getCustomerListForCandidate($estimate_issued_dt,$showing_month ,$first_contact_person,$process_person));
 	//当月成約データ
 	$this->set('contract_data',$this->CustomerMst->GetCustomersByContract(date("Y-m"),$first_contact_person,$process_person));
 	//当月挙式データ
 	$this->set('wedding_data',$this->CustomerMst->GetCustomersByWedding(date("Y-m"),$first_contact_person,$process_person));
 	//翌月挙式データ
 	$this->set('next_wedding_data',$this->CustomerMst->GetCustomersByWedding(date('Y-m', strtotime(date("Y-m").' +1 month')),$first_contact_person,$process_person));
 	//翌月以降(翌月は含まない)の挙式データ
 	$this->set('future_wedding_data',$this->CustomerMst->GetCustomersByMoreThanWedding(date('Y-m', strtotime(date("Y-m").' +1 month')),$first_contact_person,$process_person));

 	/* 検索年月を設定 */
    $months = array();
    array_push($months, date("Y-m"));
   	for($i=1;$i <= 24;$i++){
        array_push($months, date("Y-m", strtotime("-".$i." month")));
    }
 	$this->set("search_dt_list",$months);
 	/* 新規担当者一覧を取得 */
 	$this->set("first_contact_person_list",$this->User->GetAllDisplayNameWithAll());
 	/* プラン担当者一覧を取得 */
 	$this->set("process_person_list",$this->User->GetAllDisplayNameWithAll());
 	/* フィルタ条件をVIEWで保持する */
 	$this->set("estimate_issued_dt" ,$this->Session->read("filter_cs_estimate_issued_dt"));
 	$this->set("first_contact_person" ,$this->Session->read("filter_cs_first_contact_person"));
 	$this->set("process_person" ,$this->Session->read("filter_cs_process_person"));
   	$this->set("showing_month",$showing_month);

   	$this->set("menu_customers","current");
 	$this->set("menu_customer","disable");
 	$this->set("menu_fund","");

 	$this->set("sub_menu_customers_list","");
 	$this->set("sub_menu_customers_company_contact","");
 	$this->set("sub_menu_customers_wedding_reserve","");
 	$this->set("sub_menu_customers_schedules","");
 	$this->set("sub_menu_customers_by_each_attendant_list","current");
 	$this->set("sub_menu_customers_contract_list","");
 	$this->set("sub_menu_attendant_state","");
 	$this->set("sub_menu_wedding_reservations","");

    $this->set("sub_title","見積提示済み顧客一覧");
    $this->set("user",$this->Auth->user());
 }

 /**
  * 担当者別顧客一覧のEXCEL出力
  */
 function export(){

 	$estimate_issued_dt = $this->Session->read("filter_cs_estimate_issued_dt");
 	$first_contact_person = $this->Session->read("filter_cs_first_contact_person");
 	$process_person = $this->Session->read("filter_cs_process_person");
 	$showing_month = $this->Session->read("filter_cs_showing_month");

 	//見積提示済みデータ
 	$this->set('estimate_data',$this->CustomerMst->getCustomerListForCandidate($estimate_issued_dt,$showing_month ,$first_contact_person,$process_person));
 	//当月成約データ
 	$this->set('contract_data',$this->CustomerMst->GetCustomersByContract(date("Y-m"),$first_contact_person,$process_person));
 	//当月挙式データ
 	$this->set('wedding_data',$this->CustomerMst->GetCustomersByWedding(date("Y-m"),$first_contact_person,$process_person));
 	//翌月挙式データ
 	$this->set('next_wedding_data',$this->CustomerMst->GetCustomersByWedding(date('Y-m', strtotime(date("Y-m").' +1 month')),$first_contact_person,$process_person));
 	//翌月以降(翌月は含まない)の挙式データ
 	$this->set('future_wedding_data',$this->CustomerMst->GetCustomersByMoreThanWedding(date('Y-m', strtotime(date("Y-m").' +1 month')),$first_contact_person,$process_person));

 	$save_filename = mb_convert_encoding("担当者別顧客一覧", "SJIS", "AUTO").date('Ymd').".xlsx";

 	$this->layout = false;
 	$this->set( "filename", $save_filename );
 	$this->set( "template_file", "customers_by_attendant_list_template.xlsx");
 	$this->render("excel");
 }


}
?>