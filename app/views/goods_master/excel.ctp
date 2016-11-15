<?php
// Excel出力用ライブラリ
App::import( 'Vendor', 'PHPExcel', array('file'=>'phpexcel' . DS . 'PHPExcel.php') );

// Excel95用ライブラリ
App::import( 'Vendor', 'PHPExcel_Writer_Excel2007', array('file'=>'phpexcel' . DS . 'PHPExcel' . DS . 'Writer' . DS . 'Excel2007.php') );
App::import( 'Vendor', 'PHPExcel_Reader_Excel2007', array('file'=>'phpexcel' . DS . 'PHPExcel' . DS . 'Reader' . DS . 'Excel2007.php') );

    /* 指定の種類
     *   sheet->setCellValue('B1', '=A2+A3');
     *   sheet->setCellValue('A1:A5', value);
     *   sheet->setCellValueByColumnAndRow(col#, row#, value);
     */

    $reader = new PHPExcel_Reader_Excel2007();
    $objPHPExcel = $reader->load(realpath( TMP ).DS . 'excels' . DS . $template_file);
    $objPHPExcel->setActiveSheetIndex( 0 );
    $sheet = $objPHPExcel->getActiveSheet();

    $sheet->setTitle( $sheet_name );
    $sheet->getDefaultStyle()->getFont()->setName('ＭＳ Ｐゴシック');
    $sheet->getDefaultStyle()->getFont()->setSize(11);
    // 列番
    $row_cnt = 2;

     /* コンテンツ作成 */
    for($i=0;$i < count($data);$i++){

      $atr = $data[$i]["LatestGoodsMstView"];

      $sheet->setCellValue("B".$row_cnt, $atr['id']);
      $sheet->setCellValue("C".$row_cnt, $atr['goods_cd']);
      $sheet->setCellValue("D".$row_cnt, $atr['goods_ctg_id']);
      $sheet->setCellValue("E".$row_cnt, $atr['goods_ctg_nm']);
      $sheet->setCellValue("F".$row_cnt, $atr['goods_kbn_id']);
      $sheet->setCellValue("G".$row_cnt, $atr['goods_kbn_nm']);
      $sheet->setCellValue("H".$row_cnt, $atr['vendor_id']);
      $sheet->setCellValue("I".$row_cnt, $atr['vendor_nm']);
      $sheet->setCellValue("J".$row_cnt, $atr['goods_nm']);
      $sheet->setCellValue("K".$row_cnt, $atr['tax']);
      $sheet->setCellValue("L".$row_cnt, $atr['service_rate']);
      $sheet->setCellValue("M".$row_cnt, $atr['profit_rate']);

      if(empty($atr['cost1']) || $atr['cost1']==0 || $atr['cost1']==null ){
         $sheet->setCellValue("N".$row_cnt, $atr['cost']);
      }else{
         $sheet->setCellValue("N".$row_cnt, $atr['cost1']);
      }

      $sheet->setCellValue("O".$row_cnt, $atr['cost2']);
      $sheet->setCellValue("P".$row_cnt, $atr['cost3']);
      $sheet->setCellValue("Q".$row_cnt, $atr['cost4']);
      $sheet->setCellValue("R".$row_cnt, $atr['cost5']);
      $sheet->setCellValue("S".$row_cnt, $atr['cost6']);
      $sheet->setCellValue("T".$row_cnt, $atr['cost7']);
      $sheet->setCellValue("U".$row_cnt, $atr['cost8']);
      $sheet->setCellValue("V".$row_cnt, $atr['cost9']);
      $sheet->setCellValue("W".$row_cnt, $atr['cost10']);

      $cost_cols = "N".$row_cnt."+O".$row_cnt."+P".$row_cnt."+Q".$row_cnt."+R".$row_cnt."+S".$row_cnt."+T".$row_cnt."+U".$row_cnt."+V".$row_cnt."+W".$row_cnt;
      $sheet->setCellValue("X".$row_cnt, "=IF(LEN(FLOOR(((1+K".$row_cnt.") * (".$cost_cols."))*(1+L".$row_cnt."),1)) >= 6,ROUNDUP(((1+K".$row_cnt.") * (".$cost_cols."))*(1+L".$row_cnt."),-3),".
                                          "IF(LEN(FLOOR(((1+K".$row_cnt.") * (".$cost_cols."))*(1+L".$row_cnt."),1))  = 5,ROUNDUP(((1+K".$row_cnt.") * (".$cost_cols."))*(1+L".$row_cnt."),-3),".
                                          "IF(LEN(FLOOR(((1+K".$row_cnt.") * (".$cost_cols."))*(1+L".$row_cnt."),1))  = 4,ROUNDUP(((1+K".$row_cnt.") * (".$cost_cols."))*(1+L".$row_cnt."),-2),".
                                          "IF(LEN(FLOOR(((1+K".$row_cnt.") * (".$cost_cols."))*(1+L".$row_cnt."),1))  = 3,ROUNDUP(((1+K".$row_cnt.") * (".$cost_cols."))*(1+L".$row_cnt."),-1),".
                                          "IF(LEN(FLOOR(((1+K".$row_cnt.") * (".$cost_cols."))*(1+L".$row_cnt."),1))  = 2,ROUNDUP(((1+K".$row_cnt.") * (".$cost_cols."))*(1+L".$row_cnt."),0),".
                                          "IF(LEN(FLOOR(((1+K".$row_cnt.") * (".$cost_cols."))*(1+L".$row_cnt."),1))  = 1,ROUNDUP(((1+K".$row_cnt.") * (".$cost_cols."))*(1+L".$row_cnt."),0),0))))))");

     if(empty($atr['profit_rate']) || $atr['profit_rate']==0 || $atr['profit_rate']==null ){
       $sheet->setCellValue("Y".$row_cnt, $atr['price']);
     }else{
       $sheet->setCellValue("Y".$row_cnt, "=IF(LEN(FLOOR((1+M".$row_cnt.") * X".$row_cnt.",1)) >= 6,ROUNDUP((1+M".$row_cnt.") * X".$row_cnt.",-3),".
                                           "IF(LEN(FLOOR((1+M".$row_cnt.") * X".$row_cnt.",1))  = 5,ROUNDUP((1+M".$row_cnt.") * X".$row_cnt.",-3),".
                                           "IF(LEN(FLOOR((1+M".$row_cnt.") * X".$row_cnt.",1))  = 4,ROUNDUP((1+M".$row_cnt.") * X".$row_cnt.",-2),".
                                           "IF(LEN(FLOOR((1+M".$row_cnt.") * X".$row_cnt.",1))  = 3,ROUNDUP((1+M".$row_cnt.") * X".$row_cnt.",-1),".
                                           "IF(LEN(FLOOR((1+M".$row_cnt.") * X".$row_cnt.",1))  = 2,ROUNDUP((1+M".$row_cnt.") * X".$row_cnt.",0),".
                                           "IF(LEN(FLOOR((1+M".$row_cnt.") * X".$row_cnt.",1))  = 1,ROUNDUP((1+M".$row_cnt.") * X".$row_cnt.",0),0))))))");
     }

      $sheet->setCellValue("Z".$row_cnt, $atr['cost_exchange_rate']);
      $sheet->setCellValue("AB".$row_cnt, $atr['sales_exchange_rate']);
      $sheet->setCellValue("AA".$row_cnt, "=IF(AG".$row_cnt." = 0, ROUNDUP(X".$row_cnt."*Z".$row_cnt.",1), ROUNDUP(X".$row_cnt."/Z".$row_cnt.",1))");
      $sheet->setCellValue("AC".$row_cnt, "=IF(AG".$row_cnt." = 0, ROUNDUP(Y".$row_cnt."*AB".$row_cnt.",1), ROUNDUP(Y".$row_cnt."/AB".$row_cnt.",1))");
      $sheet->setCellValue("AD".$row_cnt, "=IF(AC".$row_cnt." = 0,0,(AC".$row_cnt." - AA".$row_cnt.")/AC".$row_cnt.")");

      $sheet->setCellValue("AE".$row_cnt, $atr['internal_pay_flg']);
      $sheet->setCellValue("AF".$row_cnt, $atr['payment_kbn_id']);
      $sheet->setCellValue("AG".$row_cnt, $atr['currency_kbn']);
      $sheet->setCellValue("AH".$row_cnt, $atr['aw_share']);
      $sheet->setCellValue("AI".$row_cnt, $atr['rw_share']);
      $sheet->setCellValue("AJ".$row_cnt, $atr['revision']);
      $sheet->setCellValue("AL".$row_cnt, $atr['note']);

      $row_cnt++;
    }

// Excelファイルの保存
$uploadDir = realpath( TMP );
$uploadDir .= DS . 'excels' . DS;
$path = $uploadDir . $filename;

$objWriter = new PHPExcel_Writer_Excel2007( $objPHPExcel );
$objWriter->save( $path );

// Excelファイルをクライアントに出力
Configure::write('debug', 0);       // debugコードを非表示
header("Content-disposition: attachment; filename={$filename}");
header("Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; name={$filename}");

$result = file_get_contents( $path );   // ダウンロードするデータの取得
print( $result );                       // 出力


?>