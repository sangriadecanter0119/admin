<?php

$year = 2017;
$month = 12;
$week = 1;

$day = sprintf( "%04d-%02d-%02d", $year, $month, 1 + ($week-1) * 7);
$w = date('w', strtotime( $day ) );

$weekStartDay  = 1 + ( $week - 1 ) * 7 - $w;
$weekEndDay   += $weekStartDay + 6;

// マイナスの場合はプラスにする。
$weekStartDay = $weekStartDay > 1 ? $weekStartDay : 1;

//echo $weekStartDay;
//echo "\r\n";
//echo $weekEndDay;
