<?php

class CrawlerCentral extends WeddingCrawler
{

	protected $site = array(
			"id" => 3,
			"name" => "Central",
			"itemNum" => 3
	);

	protected $items = array(
			"1" => array(
				"name" => "Central(G)",
				"description"	=> "説明",
				"color" => "skyblue"
			),
			"2" => array(
				"name" => "Central(B)",
				"description"	=> "説明",
				"color" => "skyblue"
			),
			"3" => array(
				"name" => "Central(S)",
				"description"	=> "説明",
				"color" => "skyblue"
			)
	);

	function __construct() {
		parent::__construct();
		$this->setParameter("delay", 30000 );
		$this->setParameter("delayFluctions", 1.6 );
		$this->setParameter("retry", 3 );
	}

	/**
	 * Central教会は90日以降しか取得できない。
	 * @param  int $year  取得対象年
	 * @param  int $month 取得対象月
	 * @return boolean        取得の可否
	 */
	function obtainableData($year, $month) {
		return true;
	}

	function getCalendar($year, $month, $week ) {

		// 週の開始と終了を計算する。
		$date = sprintf( "%04d-%02d-%02d", $year, $month, 1 + ($week-1) * 7);
		$w = date('w', strtotime( $date ) );

		$weekStartDay  = 1 + ( $week - 1 ) * 7 - $w;
		$weekEndDay    = $weekStartDay + 6;

		// マイナスの場合はプラスにする。
		$weekStartDay = $weekStartDay > 1 ? $weekStartDay : 1;

		foreach( range( 1, 3 ) as $itemId ) {

			$reserveList = array();
			//foreach( range(1, date('t', strtotime(sprintf("%04d-%02d-01", $year, $month)))) as $day ) {
			for( $day=$weekStartDay; $day <= $weekEndDay; $day++ )
			{

				$url = "https://cuc.checkfront.com/reserve/item/?inline=1&header=hide&src=https%3A%2F%2Fwww-cucwedding-org.".
				"filesusr.com&style=color%3A+535b58%3B+background-color%3A+d0e5de%3B+font-family%3A+Meiryo&lang_id=ja".
				"&locale_id=ja_JA&item_id=&filter_category_id=1%2C2%2C3%2C11%2C12%2C13&ssl=1&provider=droplet&".
				"filter_item_id=&customer_id=&original_start_date=&original_end_date=&date=&category_id=1&view=&";
				$url .= sprintf( "start_date=%d-%d-%d&end_date=%d-%d-%d&cf-month=%04d%02d01&item_id=%d",
				$year, $month, $day, $year, $month, $day, $year, $month, $itemId
			);

			// 取得失敗した場合は 3sec 停止後再取得
			$loop=0;
			do {
				$body =  @file_get_contents( $url, false );
				if( $body != "" ) {
					break;
				}
				$this->debugLog( "can't get booking data", D_DEBUG_LEVEL_WARNING, "crawler.central.getCalerndar():getBooking" );
			} while( ++$loop < $this->getParameter("retry") && $this->delay(3000000) );

			if( $body == "" ) {
				throw new ExceptionServerTimeout();
			}

			$reserve = $this->parseData( $body );

			if( is_array( $reserve ) ) {
				foreach( $reserve as $key => $value ) {
					$reserveDay = sprintf( "%04d-%02d-%02d", $year, $month, $day );
					$reserveList[$reserveDay][$value] = "a";
				}
				@ksort($reserveList[$reserveDay]);
			}

			$this->debugLog( $year."-".$month."-".$day." itemId:". $itemId . "\r\n" . $reserve,
				D_DEBUG_LEVEL_NOTICE, "crawler.central.getCalendar()" );

			$this->delay();

		}

		@ksort( $reserveList );
		$tmp[sprintf( "%d", $itemId )] = $reserveList;

	}

	return $tmp;

}

function parseData( $body, $localFilePath = null ) {

	if( $localFilePath != null) {
		$body = file_get_contents( $localFilePath );
	}

	// 取得したデータの先頭と末尾に<html>タグを付加しないとダメです。
	$body = sprintf( "<html>%s</html>", $body );
	$dom = phpQuery::newDocument($body);
	//echo pq($dom->find(".cf-timeslot-avail:eq(0)"))->text();
	//echo pq($dom->find(".cf-timeslot-avail:eq(1)"))->text();
	/*foreach( $dom->find('input') as $row ) {
	echo pq($row)->text();
}*/
$reserve = array();
foreach( pq($dom->find(".cf-timeslot-avail")) as $row ) {
	$time = substr( pq($row)->text(), 0 );
	// 午前のUTF-8表現。
	if( preg_match( '#\xe5\x8d\x88\xe5\x89\x8d#', $time ) ) {
		$reserve[] =
		sprintf( "%02d:%02d", substr($time,0,2), substr($time,2,2) );
	} else {
		$reserve[] =
		sprintf( "%02d:%02d", substr($time,0,2) + 12 , substr($time,2,2) );
	}
}

$this->debugLog( $reserve, "crawler.central.parseData()" );

return $reserve;

}

}
