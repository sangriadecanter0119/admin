<?php
/**
 * OpenSSLが必須です。
 */

class CrawlerKawai extends WeddingCrawler
{
	protected $site = array (
			"id" => 1,
			"name" => "kawai",
			"itemNum" => 1
	);

	protected $items = array(
			"1" => array(
				"name" => "kawai",
				"description"	=> "説明",
				"color" => "green"
			)
	);

	private $cal = "rVZLrXhvWddL5hQua5w2";

	/** {{{ Kawaiahao Step01
	* Kawaiahao Church Royal Weddings Step01
	*/
	function getCalendar($year, $month, $week){

		$reserveList = array();
		//foreach(
		//	array( 1, 8, 15, 22, date('t', strtotime(sprintf("%04d-%02d-01", $year, $month))) > 28 ? 29 : 28 ) as $day
		//) {
		$dayPattern = array( 1, 8, 15, 22,
				date('t', strtotime(sprintf("%04d-%02d-01", $year, $month))) > 28 ? 29 : 28 );
		$day = $dayPattern[$week-1];
		{

			$loop=0;
			do {
				$this->post( "https://classic.youcanbook.me/v2/jsps/cal.jsp",
				array(
					"jumpDate" => sprintf( "%04d-%02d-%02d", $year, $month, $day ),
					"cal" => $this->cal,
					"jumpButton" => "true" )
				);
				$body = $this->getBrowser()->getContent();
				if( $body != "" ) {
					break;
				}
				$this->debugLog( "can't get booking data", D_DEBUG_LEVEL_WARNING, "crawler.kawai.getCalerndar():getBooking" );
			} while( ++$loop < $this->getParameter("retry") && $this->delay(3000000) );

			$this->debugLog( $year ."-". $month ."-". $day . "\r\n" . $body, D_DEBUG_LEVEL_NOTICE, "crawler.kawai.getCalendar()" );

			$reserve = $this->parseData( $body );

			if( is_array( $reserve ) ) {
				foreach( $reserve as $row => $value ) {
					if( !isset($reserveList[$row]) &&
					$year == date('Y', strtotime($row)) && $month == date('m',strtotime($row)) ) {
						$reserveList[$row] = $value;
					}
				}
			}
			$this->delay();

		}

		return array( 1 => $reserveList );
	} // }}}

	/** {{{ Kawaiahao Step02
	* Kawaiahao Church Royal Weddings Step02
	*/
	function parseData($body, $localFilePath = null) {

		if( $localFilePath !== null ) {
			$body = file_get_contents( $localFilePath );
			//$body = file_get_contents( "sampleData/kawai_1101.txt" );
		}
		$dom = phpQuery::newDocument($body);
		//var_dump( pq(".gridHeader .gridHeaderDate")->attr("data-content"));
		$eachDom = $dom["body"]->find(".gridDay");
		foreach( $eachDom as $rowDom ) {
			// 日付の取得
			$date = trim(pq(pq($rowDom)->find(".gridHeaderDate"))->attr("data-content"));

			$eachSlot = pq($rowDom)->find(".gridSlot");
			foreach( $eachSlot as $rowSlot ) {

				// 対象の時間
				$time = trim(pq($rowSlot)->text());

				// 空き状況 gridBusyは空きがあるか？
				// gridBusyが1の場合は空きがない事を差している。
				$busy = trim(pq($rowSlot)->hasClass("gridBusy"));

				// 空き状況 gridNoFreeは過去の予約か？
				// gridNoFreeが1の場合は過去の予約を差している。
				$free = trim(pq($rowSlot)->hasClass("gridNoFree"));

				if( $busy == 0 && $free == 0 ) {
					$reserve[date('Y-m-d',strtotime($date))][date('H',strtotime($time)) .":00"] = "a";
				}

			}

		}

		if( isset($reserve) ) {
			$this->debugLog( $reserve, D_DEBUG_LEVEL_NOTICE, "crawler.kawai.parseData()" );
			return $reserve;
		} else {
			return array();
		}

	} // }}}
}
