<?php

require( D_PATH_LIB . "thirdparty/simpletest/web_tester.php" );
require( D_PATH_LIB . "phpQuery/phpQuery-onefile.php" );

require( D_PATH_LIB . "WeddingCrawlerImpl.php");

define( "D_DEBUG_LEVEL_NOTICE" , 1 );
define( "D_DEBUG_LEVEL_WARNING", 2 );
define( "D_DEBUG_LEVEL_FATAL", 3 );

abstract class WeddingCrawler extends WebTestCase implements WeddingCrawlerImpl
{

	protected $site;
	protected $items;

	private $ua =
		"Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3163.100 Safari/537.36";

	/**
	 * クローラーのデフォルト設定値
	 * @var array
	 */
	private $params = array(
		"debugFlag" => false,
		"debugLevel" => D_DEBUG_LEVEL_WARNING,
		"debugPath" => "/tmp/wedding_log.txt",
		"delay"     => 100000,
		"delayFluctions" => 1.0,	// x倍 - 1/xの間でゆらがせる。
		"retry"			=> 1
	);

	function __construct() {

		$this->setBrowser( $this->createBrowser() );
		$this->getBrowser()->addHeader('User-Agent: ' . $this->ua );
		$this->getBrowser()->addHeader('Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8');
		$this->getBrowser()->addHeader('Accept-Encoding: sdch');
		$this->getBrowser()->addHeader('Accept-Language: ja,en-US;q=0.8,en;q=0.6');

		$this->setConnectionTimeout(120);
		$this->setMaximumRedirects(20);

	}

	function getSite() {
		return $this->site;
	}

	function getItem( $itemId ) {
		return $this->items[$itemId];
	}

	// 基本的には取得可能である。
	function obtainableData($year, $month) {
		return true;
	}

	// 標準的な動作間隔
	function delay($delay = null) {
		$sleepTime = $delay == null ? $this->getParameter("delay") : $delay;
		if( $sleepTime > 0 ) {
			$sleepTime = mt_rand(
				$sleepTime / $this->getParameter("delayFluctions"),
				$sleepTime * $this->getParameter("delayFluctions")
			);
			if( defined("DISPLAY_DELAY_TIMING") ) {
				printf( "%d\r\n", $sleepTime );
			}
			usleep( $sleepTime );
		}
		return true;
	}

	function getParameter( $paramName ) {
		if( $this->hasParameter( $paramName ) ) {
			return $this->params[$paramName];
		} else {
			throw new ExceptionOfUndefinedParameter();
		}
	}

	function setParameter( $paramName, $value ) {
		if( $this->hasParameter( $paramName ) ) {
			$this->params[$paramName] = $value;
		} else {
			throw new ExceptionOfUndefinedParameter();
		}
	}

	function hasParameter( $paramName ) {
		return isset( $this->params[$paramName]);
	}

	protected function debugLog( $data, $debugLevel, $contentName = null ) {
		if( $this->getParameter("debugFlag") == true &&
				$this->getParameter("debugLevel") <= $debugLevel ) {
			$output = sprintf( "[Time:%s]%s[Data:%s]\r\n",
				date('Y-m-d H:i:s', time()),
				$contextName == null ? " " : sprintf(" [Context:%s] ", $contextName) ,
				is_array( $data ) ? var_export($data,true) : $data
			);
		file_put_contents( $this->getParameter("debugPath"), $output, FILE_APPEND );
	}
}

}

class ExceptionOfUndefinedParameter Extends Exception {

}

class ExceptionServerTimeout extends Exception {

}
