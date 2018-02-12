<?php

class CrawlerCalvary extends WeddingCrawler
{
	private $username = "japan@realweddings.jp";
	private $password = "realweddings93";
protected $site = array(
			"id" => 2,
			"name" => "Calvary",
			"itemNum" => 1
		);

	protected $items = array(
			"1" => array(
				"name" => "Calvary",
				"description"	=> "説明",
				"color" => "blue"
			)
	);

	// {{{ Calvary Step01
	function getCalendar($year, $month, $week) {

		$this->get("http://booking.wedding-bythesea.com/en-us/login.aspx");
		$content = $this->getBrowser()->getContent();
		//$content = file_get_contents( "../sampleData/calvary001.txt");
		preg_match( '#id="__EVENTVALIDATION" value="([^"]+)"#', $content, $m1 );
		preg_match( '#id="__VIEWSTATE" value="([^"]+)"#', $content, $m2 );
		preg_match( '#id="__VIEWSTATEGENERATOR" value="([^"]+)"#', $content, $m3 );
		//var_dump( $m1[1], $m2[1], $m3[1] );

		$this->post( "http://booking.wedding-bythesea.com/en-US/login.aspx",
		array(
			"ScriptManager" => 'dnn$ctr571$dnn$ctr571$Login_UPPanel|dnn$ctr571$Login$Login_DNN$cmdLogin',
			"__EVENTTARGET" => 'dnn$ctr571$Login$Login_DNN$cmdLogin',
			"__EVENTARGUMENT" => "",
			"__VIEWSTATE"   => $m2[1],
			"__VIEWSTATEGENERATOR" => $m3[1],
			"__VIEWSTATEENCRYPTED" => "",
			"__EVENTVALIDATION" => $m1[1],
			'dnn$dnnSEARCH$txtSearch' => "search",
			'dnn$ctr571$Login$Login_DNN$txtUsername' => $this->username,
			'dnn$ctr571$Login$Login_DNN$txtPassword' => $this->password,
			"__dnnVariable" => '{"__scdoff":"1"}',
			"__ASYNCPOST" => "true",
			"RadAJAXControlID" => "dnn_ctr571_Login_UP"
		));

		//foreach(
		//	array( 1, 8, 15, 22,
		//	date('t', strtotime(sprintf("%04d-%02d-01", $year, $month))) > 28 ? 29 : 28 ) as $day
		//) {
		$dayPattern = array( 1, 8, 15, 22,
				date('t', strtotime(sprintf("%04d-%02d-01", $year, $month))) > 28 ? 29 : 28 );
		$day = $dayPattern[$week-1];
		{

			$loop=0;
			do {
				$bookingUrl = sprintf(
					"http://booking.wedding-bythesea.com/en-us/calendar.aspx?CH=CAL&dp=w&sd=%d/%d/%d", $month, $day, $year
				);

				$this->get($bookingUrl);
				// sd=11/05/2017

				$body =  $this->getBrowser()->getContent();
				if( $body != "" ) {
					break;
				}
				$this->debugLog( "can't get booking data", D_DEBUG_LEVEL_WARNING, "crawler.calvary.getCalerndar():getBooking" );
			} while( ++$loop < $this->getParameter("retry") && $this->delay(3000000) );

			$this->debugLog( $year."-".$month."-".$day . "\r\n" . $body, D_DEBUG_LEVEL_NOTICE, "crawler.calvary.getCalendar()" );

			$this->delay();

			$reserve = $this->parseData( $body );

			if( is_array( $reserve ) ) {
				foreach( $reserve as $row => $value ) {
					$row = date('Y-m-d', strtotime( $year ."/". $row ));
					if( !isset($reserveList[$row]) &&
					$year == date('Y', strtotime($row)) && $month == date('m',strtotime($row)) ) {
						//$value; Confirmed
						$reserveList[$row] = $value;
					}
				}
			}
		}

		if ( isset($reserveList) ) {
			return array( 1 => $reserveList );
		} else {
			return array( 1 => array() );
		}


	}
	// }}}

	// {{{ Calvary Step02
	function parseData( $body, $localFilePath = null) {

		$timeRange = array(
			null, "07:30", "08:30", "09:30", "10:30","11:30",
			"12:30", "13:30", "14:30", "15:30","16:30"
		);

		//$body = file_get_contents( "sampleData/calvary1101.txt" );
		$dom = phpQuery::newDocument($body);

		$eachTr = pq($dom['body']->find('.tablestyle'))->find("tr:eq(0)");

		// 日時
		$i=0;
		foreach( pq($eachTr)->find("td:gt(0)") as $rowTr ) {
			$reserveDateName[$i++] = substr(trim(pq($rowTr)->text()), 0, 5);
		}

		// 時間別
		for( $i=1; $i<=10; $i++ ) {
			$eachTr = pq($dom['body']->find('.tablestyle'))->find(
				sprintf( "tr:eq(%d)", $i)
			);
			$j = 0;
			//foreach( pq($eachTr)->find("td:gt(3)") as $rowTr ) { // }
			foreach( pq($eachTr)->find("td:gt(0)") as $rowTr ) {
				//$reserveList[$i-1]['reserve'][$timeRange[$j++]] =  pq($rowTr)->text();
				//$reserveList[$j][$reserveDateName[$i-1]]['time'] = $timeRange[$i];
				//$reserveList[$j][$reserveDateName[$i-1]]['reserve'] = trim(pq($rowTr)->text() );
				$status = trim(pq($rowTr)->text());
				if( $status == "Open" ) {
					$reserveList[$reserveDateName[$j]][$timeRange[$i]] = "a";
				}
				$j++;
				// confirmedはどうするのか？
			}
		}

		if( isset($reserveList ) ) {
			$this->debugLog( $reserveList, D_DEBUG_LEVEL_NOTICE, "crawler.calvary.parseData()" );
			return $reserveList;
		}

		//var_dump( $reserveList );
	} //}}}
}
