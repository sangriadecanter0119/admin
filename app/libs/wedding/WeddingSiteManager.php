<?php
/**
 * [WeddingSiteManager description]
 */
class WeddingSiteManager
{

	public static $crawlerClasses = array( "Kawai", "Calvary", "Central" );

	public function getCrawlers() {

		foreach( self::$crawlerClasses as $crawlerName ) {
			$class[] = self::getCrawler( $crawlerName );
		}

		return $class;
	}

	public function getCrawler($crawlerName) {

		$crawlerClassName = sprintf( "Crawler%s", ucfirst( $crawlerName ) );
    $crawlerClassPath = sprintf( "%s/crawler/%s.php", D_PATH_LIB, $crawlerClassName );

    if( file_exists($crawlerClassPath) ) {
  		include_once( $crawlerClassPath );
    }

		if( class_exists($crawlerClassName) ) {
			return new $crawlerClassName;
		}
		return false;

	}

}
