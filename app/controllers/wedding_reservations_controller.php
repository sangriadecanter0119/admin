<?php

App::import("libs", "test");

class WeddingReservationsController extends AppController {

    public $uses = null;
	public $layout = 'cust_list_info_main_tab';
	public $components = array('Auth');
	public $helpers = array('Html','common','Javascript');

	// キャッシュの有効期限
	private $cacheLimit = 604800;

	public function index() {

		define( "D_PATH_LIB" , APPLIBS . "wedding/" );
		//define( "DISPLAY_DELAY_TIMING", "" );

		require( D_PATH_LIB . "WeddingCrawler.php");
		require( D_PATH_LIB . "WeddingSiteManager.php");
		require( D_PATH_LIB . "WeddingReserveManager.php");

		if( isset( $this->params['url']['year'] ) &&
		isset( $this->params['url']['month'] ) ) {

			$objWedding = new WeddingReserveManager();
			$year  = $this->params['url']['year'];
			$month = $this->params['url']['month'];
			$week = $this->params['url']['week'];
			$cacheKey  = sprintf( "%04d%02d%02d", $year, $month, $week );
			$cacheDate = sprintf( "%04d%02d%02d_date", $year, $month, $week );

			if( !($data = unserialize(Cache::read($cacheKey))) ||
					$this->params['url']['submit'] == '取得' ) {

				$data = array();
				foreach( array("kawai","calvary", "central") as $siteName ) {

					$obj = WeddingSiteManager::getCrawler($siteName);
					$tmpData = $obj->getCalendar( $year, $month, $week );
					$tmpSite = $obj->getSite();
					for( $i=1; $i<=$tmpSite['itemNum']; $i++ ) {
						$tmpItem = $obj->getItem( $i );
						$data[] = array(
							"name"  => $tmpItem['name'],
							"color" => $tmpItem['color'],
							"data"  => $tmpData[$i]
						);
					}
				}

				$cacheData = preg_replace_callback (
					'!s:(\d+):"(.*?)";!',
					array( $this, 'serializeRepair'),
					serialize($data) );

				//Cache::set( array('duration' => $this->cacheLimit ) );
				Cache::write( $cacheKey, $cacheData );
				//Cache::set( array('duration' => $this->cacheLimit ) );
				Cache::write( $cacheDate, time() );

			}

			$this->set( "data",
			$objWedding->createCalerndarHtml($data,$year,$month,$week) );
			$this->set( "year", $year );
			$this->set( "month", $month );
			$this->set( "week", $week );
			$this->set( "cache", Cache::read($cacheDate) ?
				date('Y-m-d H:i-s',Cache::read($cacheDate)) :
				date('Y-m-d H:i-s',time() ) );

		}else{
			$this->set( "year", date('Y') );
			$this->set( "month", date('n',strtotime("6 month")) );
			//$this->set( "week", $week );
		}
		//メニューとサブメニューのアクティブ化
		$this->set("menu_customers","current");
		$this->set("menu_customer","disable");
		$this->set("menu_fund","");

		$this->set("sub_menu_customers_list","");
		$this->set("sub_menu_customers_company_contact","");
		$this->set("sub_menu_customers_wedding_reserve","");
		$this->set("sub_menu_customers_schedules","");
		$this->set("sub_menu_customers_by_each_attendant_list","");
		$this->set("sub_menu_customers_contract_list","");
		$this->set("sub_menu_attendant_state","");
		$this->set("sub_menu_wedding_reservations","current");

		$this->set("sub_title","挙式場予約状況");
		$this->set("user",$this->Auth->user());
	}

	public function serializeRepair($match) {
	   return ($match[1] == strlen($match[2])) ? $match[0] : 's:' . strlen($match[2]) . ':"' . $match[2] . '";';
	}
}
