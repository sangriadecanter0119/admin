<?php

/**
* 予約システムアグリゲーションプログラム
* 動作検証用プログラム
*
* @package varification.external.wedding.enviroment
* @author rsv05657
* @version	0.01
*/

/**
* 予約情報に関する構造体
* 月日['時間']
* $reserve['20171107']['15:00']
*/
define( "APPLIBS", "/home/systems/wedding/release/www/htdocs/cakephp-1.3.21/app/libs/" );
define( "D_PATH_LIB" , APPLIBS . "wedding/" );
define( "DISPLAY_DELAY_TIMING", "" );

require( D_PATH_LIB . "WeddingCrawler.php");
require( D_PATH_LIB . "WeddingSiteManager.php");
require( D_PATH_LIB . "WeddingReserveManager.php");

//var_dump( WeddingSiteManager::getCrawlers() );
//$obj = new WeddingReserveManager();
//$obj = WeddingSiteManager::getCrawler("kawai");
//var_dump( $obj->getCalendar( 2018, 1 ) );
//$body = $obj->getCalendar( "2017-12-01");
//var_dump( $obj->parseData( $body ));

//$obj = WeddingSiteManager::getCrawler("kawai");
//var_dump( $obj->getCalendar( 2018, 4 ) );
$obj = WeddingSiteManager::getCrawler("calvary");
var_dump( $obj->getCalendar( 2018, 3 ) );
//$obj = WeddingSiteManager::getCrawler("central");
//var_dump( $obj->getCalendar( 2018, 4 ) );

//echo "*";
//var_dump( $obj->parseData($body) );
//echo "*";

//データベースの保存
//$obj = new WeddingReserveManager();
//$obj->getReserveData( 2017, 11 );
/*
["2017-12-01"]=>
array(4) {
  ["10:00"]=>
  bool(true)
  ["11:00"]=>
  bool(true)
  ["12:00"]=>
  bool(true)
  ["16:00"]=>
  bool(true)
}
["2017-12-03"]=>
array(2) {
  ["14:00"]=>
  bool(true)
  ["15:00"]=>
  bool(true)
}
*/
//$obj = new WeddingCrawler();

//$obj->parseCalendarKawai($body);
/*echo( "\r\n*************TEST CASE 01************\r\n");
$body = $obj->getCalendarKawai("2017-09-01");
$obj->parseCalendarKawai($body);
sleep(1);
$body = $obj->getCalendarKawai("2017-09-08");
$obj->parseCalendarKawai($body);
sleep(1);
$body = $obj->getCalendarKawai("2017-09-15");
$obj->parseCalendarKawai($body);
sleep(1);
$body = $obj->getCalendarKawai("2017-09-22");
$obj->parseCalendarKawai($body);
sleep(1);
$body = $obj->getCalendarKawai("2017-09-29");
$obj->parseCalendarKawai($body);*/
//echo( "\r\n*************TEST CASE 02************\r\n");
//$body = $obj->getCalendarCalvary();
//$obj->parseCalendarCalvary($body);

/*echo( "\r\n*************TEST CASE 03************\r\n");
$body =  $obj->getCalendarCentral();
$obj->parseCalendarCentral($body);*/
