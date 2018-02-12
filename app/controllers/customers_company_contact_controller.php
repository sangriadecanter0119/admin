<?php
class CustomersCompanyContactController extends AppController
{
 public $name = 'CustomersCompanyContact';
 public $layout = 'cust_list_info_main_tab';
 public $uses = array('CustomerMst','ContactTrn','ContactTrnView');
 public $components = array('Auth','RequestHandler');
 public $helpers = array('Html','common','Javascript');

 function index($page_limit=null)
 {
    /*
 	 * 問い合わせリストの表示件数をセッション情報に保持する
 	 */
 	if($page_limit == null){
 	   /* セッション開始後初回表示時 */
       if($this->Session->read('cust_company_contact_page_limit')==null){
       	 $page_limit = 50;
 	     $this->Session->write('cust_company_contact_page_limit',$page_limit);
       /* 初回以降の表示で他画面からの遷移時 */
       }else{
       	 $page_limit = $this->Session->read('cust_company_contact_page_limit');
       }
 	}
 	/* 同画面内での表示ページ数の変更時 */
 	else{
 		$this->Session->write('cust_company_contact_page_limit',$page_limit);
 	}

 	//ページネーション設定
 	$this->paginate = array(
                            'limit' =>$page_limit ,
 	                        'order' => array('id' => 'desc')
                            );
 	//問合せを検索
    $this->set("data",$this->paginate('ContactTrnView'));
 	$this->set("page_limit",$page_limit);

 	$this->set("menu_customers","current");
 	$this->set("menu_customer","disable");
 	$this->set("menu_fund","");

 	$this->set("sub_menu_customers_list","");
 	$this->set("sub_menu_customers_company_contact","current");
 	$this->set("sub_menu_customers_schedules","");
 	$this->set("sub_menu_customers_contract_list","");
 	$this->set("sub_menu_attendant_state","");
 	$this->set("sub_menu_wedding_reservations","");

 	$this->set("sub_title","問い合わせ状況");
 	$this->set("user",$this->Auth->user());
 }
}

?>
