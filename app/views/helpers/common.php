<?php
class CommonHelper extends AppHelper
{
  //入力オブジェクトがNULLなら&nbspを返す(文字列型)
  function evalNbsp($obj)
  {
  	$obj = trim($obj);
  	return (is_null($obj) or empty($obj)) ? "&nbsp":$obj;
  }

  //入力オブジェクトがNULLなら&nbspを返す(Date型:ロング)
  function evalNbspForLongDate($obj)
  {
  	return (is_null($obj) or empty($obj) or $obj == '0000-00-00 00:00:00') ? "&nbsp":date('Y/m/d H:i:s',strtotime($obj));
  }

  //入力オブジェクトがNULLなら&nbspを返す(Date型:ショート)
  function evalNbspForShortDate($obj)
  {
  	return (is_null($obj) or empty($obj) or $obj == '0000-00-00 00:00:00') ? "&nbsp":date('Y/m/d',strtotime($obj));
  }

  //入力オブジェクトがNULLなら&nbspを返す(Date型:日付のみ)
  function evalNbspForDayOnly($obj)
  {
  	return (is_null($obj) or empty($obj)) ? "&nbsp":date('d',strtotime($obj));
  }

  //入力オブジェクトがNULLなら空文字を返す(Date型:ロング)
  function evaForLongDate($obj)
  {
  	return (is_null($obj) or empty($obj) or $obj == '0000-00-00 00:00:00') ? "":date('Y/m/d H:i:s',strtotime($obj));
  }

  //入力オブジェクトがNULLなら空文字を返す(Date型:ショート)
  function evalForShortDate($obj)
  {
  	return (is_null($obj) or empty($obj) or $obj == '0000-00-00 00:00:00') ? "":date('Y/m/d',strtotime($obj));
  }

  //入力オブジェクトがNULLなら空文字を返す
  function evalForTime($obj)
  {
  	return (is_null($obj) or empty($obj)) ? "":substr($obj,0,2).":".substr($obj,2,2);
  }

  function hasValue($obj)
  {
  	return (is_null($obj) or empty($obj) or $obj == "&nbsp") ? false : true;
  }
}