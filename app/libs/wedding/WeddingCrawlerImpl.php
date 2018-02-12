<?php

/**
*
* @author rsv05657
*/
interface WeddingCrawlerImpl
{
	public function getCalendar($year, $month, $week);
	public function parseData($data, $localFilePath = null);
	public function delay($delay = null);
  public function obtainableData($year, $month);
}
