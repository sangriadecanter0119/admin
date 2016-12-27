<?php
// Excel出力用ライブラリ
App::import( 'Vendor', 'PHPExcel', array('file'=>'phpexcel' . DS . 'PHPExcel.php') );
//App::import( 'Vendor', 'PHPExcel_IOFactory', array('file'=>'phpexcel' . DS . 'PHPExcel' . DS . 'IOFactory.php') );
//App::import( 'Vendor', 'PHPExcel_Cell_AdvancedValueBinder', array('file'=>'phpexcel' . DS . 'PHPExcel' . DS . 'Cell' . DS . 'AdvancedValueBinder.php') );

// Excel95用ライブラリ
App::import( 'Vendor', 'PHPExcel_Writer_Excel2007', array('file'=>'phpexcel' . DS . 'PHPExcel' . DS . 'Writer' . DS . 'Excel2007.php') );
App::import( 'Vendor', 'PHPExcel_Reader_Excel2007', array('file'=>'phpexcel' . DS . 'PHPExcel' . DS . 'Reader' . DS . 'Excel2007.php') );

$cols = array("A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z",
              "AA","AB","AC","AD","AE","AF","AG","AH","AI","AJ","AK","AL","AM","AN","AO","AP","AQ","AR","AS","AT","AU","AV","AW","AX","AY","AZ");

    $reader = new PHPExcel_Reader_Excel2007();
    $objPHPExcel = $reader->load(realpath( TMP ).DS . 'excels' . DS . $template_file);

    /* ドル表記
     --------------------------------------------------------*/
    // Set active sheet index to the first sheet, so Excel opens this as the first sheet
    $objPHPExcel->setActiveSheetIndex( 0 );
    $sheet = $objPHPExcel->getActiveSheet();

    // Rename sheet
    // シート名をつける
    $sheet->setTitle( $sheet_name."_ドル表記" );
    // デフォルトのフォント
    $sheet->getDefaultStyle()->getFont()->setName('ＭＳ Ｐゴシック');
    // デフォルトのフォントサイズ
    $sheet->getDefaultStyle()->getFont()->setSize(11);
    // 列番
    $row_cnt = 5;

    for($i=0;$i < count($data);$i++){

    	$sheet->setCellValue("B".$row_cnt, $i+1);
    	$sheet->setCellValue("C".$row_cnt, date("Y-m-d",strtotime($data[$i]['wedding_dt'])));
    	$sheet->setCellValue("D".$row_cnt, $data[$i]['grmls_kj'].$data[$i]['grmfs_kj']);
    	$sheet->setCellValue("E".$row_cnt, $data[$i]['foreign_total']);
    	$sheet->setCellValue("F".$row_cnt, $data[$i]['foreign_credit_domestic_pay_amount']+$data[$i]['foreign_credit_aboard_pay_amount']);
    	$sheet->setCellValue("G".$row_cnt, $data[$i]['foreign_service_fee']);
    	$sheet->setCellValue("H".$row_cnt, $data[$i]['foreign_hi_total']);
    	$sheet->setCellValue("I".$row_cnt, $data[$i]['foreign_rw_total']);
    	$sheet->setCellValue("J".$row_cnt, $data[$i]['foreign_hawaii_tax']);
    	$sheet->setCellValue("K".$row_cnt, $data[$i]['foreign_remittance_hawaii_tax']);
    	$sheet->setCellValue("L".$row_cnt, $data[$i]['foreign_rw_discount']);
    	$sheet->setCellValue("M".$row_cnt, $data[$i]['foreign_total_discount']);
    	$sheet->setCellValue("N".$row_cnt, $data[$i]['foreign_rw_sum']);
    	$sheet->setCellValue("O".$row_cnt, $data[$i]['foreign_rw_total_rate']/100);
    	$sheet->setCellValue("P".$row_cnt, $data[$i]['foreign_gross_total']);
    	$sheet->setCellValue("Q".$row_cnt, $data[$i]['foreign_gross_total_rate']/100);
    	$sheet->setCellValue("R".$row_cnt, $data[$i]['sales_rate']);
    	$sheet->setCellValue("S".$row_cnt, $data[$i]['cost_rate']);


    	$sheet->getStyle("E".$row_cnt)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
    	$sheet->getStyle("F".$row_cnt)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
    	$sheet->getStyle("G".$row_cnt)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
    	$sheet->getStyle("H".$row_cnt)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
    	$sheet->getStyle("I".$row_cnt)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
    	$sheet->getStyle("J".$row_cnt)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
    	$sheet->getStyle("K".$row_cnt)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
    	$sheet->getStyle("L".$row_cnt)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
    	$sheet->getStyle("M".$row_cnt)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
    	$sheet->getStyle("N".$row_cnt)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
    	$sheet->getStyle("O".$row_cnt)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
    	$sheet->getStyle("P".$row_cnt)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
    	$sheet->getStyle("Q".$row_cnt)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);

        $row_cnt++;
        $sheet->insertNewRowBefore( $row_cnt, 1 );
     }

     $sheet->removeRow($row_cnt);
     $sheet->removeRow($row_cnt);

     //合計行
     $sheet->setCellValue("E".$row_cnt, "=SUM(E5:E".($row_cnt-1).")");
     $sheet->setCellValue("F".$row_cnt, "=SUM(F5:F".($row_cnt-1).")");
     $sheet->setCellValue("G".$row_cnt, "=SUM(G5:G".($row_cnt-1).")");
     $sheet->setCellValue("H".$row_cnt, "=SUM(H5:H".($row_cnt-1).")");
     $sheet->setCellValue("I".$row_cnt, "=SUM(I5:I".($row_cnt-1).")");
     $sheet->setCellValue("J".$row_cnt, "=SUM(J5:J".($row_cnt-1).")");
     $sheet->setCellValue("K".$row_cnt, "=SUM(K5:K".($row_cnt-1).")");
     $sheet->setCellValue("L".$row_cnt, "=SUM(L5:L".($row_cnt-1).")");
     $sheet->setCellValue("M".$row_cnt, "=SUM(M5:M".($row_cnt-1).")");
     $sheet->setCellValue("N".$row_cnt, "=SUM(N5:N".($row_cnt-1).")");
     $sheet->setCellValue("P".$row_cnt, "=SUM(P5:P".($row_cnt-1).")");

     $sheet->getStyle("E".$row_cnt)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
     $sheet->getStyle("F".$row_cnt)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
     $sheet->getStyle("G".$row_cnt)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
     $sheet->getStyle("H".$row_cnt)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
     $sheet->getStyle("I".$row_cnt)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
     $sheet->getStyle("J".$row_cnt)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
     $sheet->getStyle("K".$row_cnt)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
     $sheet->getStyle("L".$row_cnt)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
     $sheet->getStyle("M".$row_cnt)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
     $sheet->getStyle("N".$row_cnt)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
     $sheet->getStyle("O".$row_cnt)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
     $sheet->getStyle("P".$row_cnt)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
     $sheet->getStyle("Q".$row_cnt)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);

     //平均行
     $row_cnt++;
     $sheet->setCellValue("E".$row_cnt, "=AVERAGE(E5:E".($row_cnt-2).")");
     $sheet->setCellValue("F".$row_cnt, "=AVERAGE(F5:F".($row_cnt-2).")");
     $sheet->setCellValue("G".$row_cnt, "=AVERAGE(G5:G".($row_cnt-2).")");
     $sheet->setCellValue("H".$row_cnt, "=AVERAGE(H5:H".($row_cnt-2).")");
     $sheet->setCellValue("I".$row_cnt, "=AVERAGE(I5:I".($row_cnt-2).")");
     $sheet->setCellValue("J".$row_cnt, "=AVERAGE(J5:J".($row_cnt-2).")");
     $sheet->setCellValue("K".$row_cnt, "=AVERAGE(K5:K".($row_cnt-2).")");
     $sheet->setCellValue("L".$row_cnt, "=AVERAGE(L5:L".($row_cnt-2).")");
     $sheet->setCellValue("M".$row_cnt, "=AVERAGE(M5:M".($row_cnt-2).")");
     $sheet->setCellValue("N".$row_cnt, "=AVERAGE(N5:N".($row_cnt-2).")");
     $sheet->setCellValue("P".$row_cnt, "=AVERAGE(P5:P".($row_cnt-2).")");

     $sheet->getStyle("E".$row_cnt)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
     $sheet->getStyle("F".$row_cnt)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
     $sheet->getStyle("G".$row_cnt)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
     $sheet->getStyle("H".$row_cnt)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
     $sheet->getStyle("I".$row_cnt)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
     $sheet->getStyle("J".$row_cnt)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
     $sheet->getStyle("K".$row_cnt)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
     $sheet->getStyle("L".$row_cnt)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
     $sheet->getStyle("M".$row_cnt)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
     $sheet->getStyle("N".$row_cnt)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
     $sheet->getStyle("O".$row_cnt)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
     $sheet->getStyle("P".$row_cnt)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
     $sheet->getStyle("Q".$row_cnt)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);

     /* 羅線 */
     /*
     $sheet->getStyle("B4:".$cols[$col_cnt].$row_cnt)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
     $sheet->getStyle("B4:".$cols[$col_cnt].$row_cnt)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
     $sheet->getStyle("B4:".$cols[$col_cnt].$row_cnt)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
     $sheet->getStyle("B4:".$cols[$col_cnt].$row_cnt)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);

     $sheet->getStyle("B4:".$cols[$col_cnt]."4")->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
     $sheet->getStyle("B4:B".$row_cnt)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
     $sheet->getStyle($cols[$col_cnt]."4:".$cols[$col_cnt].$row_cnt)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
     $sheet->getStyle("B".$row_cnt.":".$cols[$col_cnt].$row_cnt)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_DOUBLE);
      */
    // PHPの日時出力
    $time = time();                 // 現在日時(Unix Timestamp)
    $sheet->setCellValue("S2", '出力日：'.date('Y/m/d'));
    $sheet->setCellValue("C2", $sheet_name."分売上一覧");

    /* 円表記
     --------------------------------------------------------*/
    $objPHPExcel->setActiveSheetIndex(1);
    $sheet = $objPHPExcel->getActiveSheet();

    // Rename sheet
    // シート名をつける
    $sheet->setTitle( $sheet_name."_円表記" );
    // デフォルトのフォント
    $sheet->getDefaultStyle()->getFont()->setName('ＭＳ Ｐゴシック');
    // デフォルトのフォントサイズ
    $sheet->getDefaultStyle()->getFont()->setSize(11);
    // 列番
    $row_cnt = 5;

    for($i=0;$i < count($data);$i++){

    	$sheet->setCellValue("B".$row_cnt, $i+1);
    	$sheet->setCellValue("C".$row_cnt, date("Y-m-d",strtotime($data[$i]['wedding_dt'])));
    	$sheet->setCellValue("D".$row_cnt, $data[$i]['grmls_kj'].$data[$i]['grmfs_kj']);
    	$sheet->setCellValue("E".$row_cnt, $data[$i]['total']);
    	$sheet->setCellValue("F".$row_cnt, $data[$i]['credit_domestic_pay_amount']+$data[$i]['credit_aboard_pay_amount']);
    	$sheet->setCellValue("G".$row_cnt, $data[$i]['service_fee']);
    	$sheet->setCellValue("H".$row_cnt, $data[$i]['hi_total']);
    	$sheet->setCellValue("I".$row_cnt, $data[$i]['rw_total']);
    	$sheet->setCellValue("J".$row_cnt, $data[$i]['hawaii_tax']);
    	$sheet->setCellValue("K".$row_cnt, $data[$i]['remittance_hawaii_tax']);
    	$sheet->setCellValue("L".$row_cnt, $data[$i]['rw_discount']);
    	$sheet->setCellValue("M".$row_cnt, $data[$i]['total_discount']);
    	$sheet->setCellValue("N".$row_cnt, $data[$i]['rw_sum']);
    	$sheet->setCellValue("O".$row_cnt, $data[$i]['rw_total_rate']/100);
    	$sheet->setCellValue("P".$row_cnt, $data[$i]['gross_total']);
    	$sheet->setCellValue("Q".$row_cnt, $data[$i]['gross_total_rate']/100);
    	$sheet->setCellValue("R".$row_cnt, $data[$i]['sales_rate']);
    	$sheet->setCellValue("S".$row_cnt, $data[$i]['cost_rate']);

    	$sheet->getStyle("E".$row_cnt)->getNumberFormat()->setFormatCode('"\"#,##0');
    	$sheet->getStyle("F".$row_cnt)->getNumberFormat()->setFormatCode('"\"#,##0');
    	$sheet->getStyle("G".$row_cnt)->getNumberFormat()->setFormatCode('"\"#,##0');
    	$sheet->getStyle("H".$row_cnt)->getNumberFormat()->setFormatCode('"\"#,##0');
    	$sheet->getStyle("I".$row_cnt)->getNumberFormat()->setFormatCode('"\"#,##0');
    	$sheet->getStyle("J".$row_cnt)->getNumberFormat()->setFormatCode('"\"#,##0');
    	$sheet->getStyle("K".$row_cnt)->getNumberFormat()->setFormatCode('"\"#,##0');
    	$sheet->getStyle("L".$row_cnt)->getNumberFormat()->setFormatCode('"\"#,##0');
    	$sheet->getStyle("M".$row_cnt)->getNumberFormat()->setFormatCode('"\"#,##0');
    	$sheet->getStyle("N".$row_cnt)->getNumberFormat()->setFormatCode('"\"#,##0');
    	$sheet->getStyle("O".$row_cnt)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
    	$sheet->getStyle("P".$row_cnt)->getNumberFormat()->setFormatCode('"\"#,##0');
    	$sheet->getStyle("Q".$row_cnt)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);

    	$row_cnt++;
    	$sheet->insertNewRowBefore( $row_cnt, 1 );
    }

    $sheet->removeRow($row_cnt);
    $sheet->removeRow($row_cnt);

    //合計行
    $sheet->setCellValue("E".$row_cnt, "=SUM(E5:E".($row_cnt-1).")");
    $sheet->setCellValue("F".$row_cnt, "=SUM(F5:F".($row_cnt-1).")");
    $sheet->setCellValue("G".$row_cnt, "=SUM(G5:G".($row_cnt-1).")");
    $sheet->setCellValue("H".$row_cnt, "=SUM(H5:H".($row_cnt-1).")");
    $sheet->setCellValue("I".$row_cnt, "=SUM(I5:I".($row_cnt-1).")");
    $sheet->setCellValue("J".$row_cnt, "=SUM(J5:J".($row_cnt-1).")");
    $sheet->setCellValue("K".$row_cnt, "=SUM(K5:K".($row_cnt-1).")");
    $sheet->setCellValue("L".$row_cnt, "=SUM(L5:L".($row_cnt-1).")");
    $sheet->setCellValue("M".$row_cnt, "=SUM(M5:M".($row_cnt-1).")");
    $sheet->setCellValue("N".$row_cnt, "=SUM(N5:N".($row_cnt-1).")");
    $sheet->setCellValue("P".$row_cnt, "=SUM(P5:P".($row_cnt-1).")");

    $sheet->getStyle("E".$row_cnt)->getNumberFormat()->setFormatCode('"\"#,##0');
    $sheet->getStyle("F".$row_cnt)->getNumberFormat()->setFormatCode('"\"#,##0');
    $sheet->getStyle("G".$row_cnt)->getNumberFormat()->setFormatCode('"\"#,##0');
    $sheet->getStyle("H".$row_cnt)->getNumberFormat()->setFormatCode('"\"#,##0');
    $sheet->getStyle("I".$row_cnt)->getNumberFormat()->setFormatCode('"\"#,##0');
    $sheet->getStyle("J".$row_cnt)->getNumberFormat()->setFormatCode('"\"#,##0');
    $sheet->getStyle("K".$row_cnt)->getNumberFormat()->setFormatCode('"\"#,##0');
    $sheet->getStyle("L".$row_cnt)->getNumberFormat()->setFormatCode('"\"#,##0');
    $sheet->getStyle("M".$row_cnt)->getNumberFormat()->setFormatCode('"\"#,##0');
    $sheet->getStyle("N".$row_cnt)->getNumberFormat()->setFormatCode('"\"#,##0');
    $sheet->getStyle("O".$row_cnt)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
    $sheet->getStyle("P".$row_cnt)->getNumberFormat()->setFormatCode('"\"#,##0');
    $sheet->getStyle("Q".$row_cnt)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);

    //平均行
    $row_cnt++;
    $sheet->setCellValue("E".$row_cnt, "=AVERAGE(E5:E".($row_cnt-2).")");
    $sheet->setCellValue("F".$row_cnt, "=AVERAGE(F5:F".($row_cnt-2).")");
    $sheet->setCellValue("G".$row_cnt, "=AVERAGE(G5:G".($row_cnt-2).")");
    $sheet->setCellValue("H".$row_cnt, "=AVERAGE(H5:H".($row_cnt-2).")");
    $sheet->setCellValue("I".$row_cnt, "=AVERAGE(I5:I".($row_cnt-2).")");
    $sheet->setCellValue("J".$row_cnt, "=AVERAGE(J5:J".($row_cnt-2).")");
    $sheet->setCellValue("K".$row_cnt, "=AVERAGE(K5:K".($row_cnt-2).")");
    $sheet->setCellValue("L".$row_cnt, "=AVERAGE(L5:L".($row_cnt-2).")");
    $sheet->setCellValue("M".$row_cnt, "=AVERAGE(M5:M".($row_cnt-2).")");
    $sheet->setCellValue("N".$row_cnt, "=AVERAGE(N5:N".($row_cnt-2).")");
    $sheet->setCellValue("P".$row_cnt, "=AVERAGE(P5:P".($row_cnt-2).")");

    $sheet->getStyle("E".$row_cnt)->getNumberFormat()->setFormatCode('"\"#,##0');
    $sheet->getStyle("F".$row_cnt)->getNumberFormat()->setFormatCode('"\"#,##0');
    $sheet->getStyle("G".$row_cnt)->getNumberFormat()->setFormatCode('"\"#,##0');
    $sheet->getStyle("H".$row_cnt)->getNumberFormat()->setFormatCode('"\"#,##0');
    $sheet->getStyle("I".$row_cnt)->getNumberFormat()->setFormatCode('"\"#,##0');
    $sheet->getStyle("J".$row_cnt)->getNumberFormat()->setFormatCode('"\"#,##0');
    $sheet->getStyle("K".$row_cnt)->getNumberFormat()->setFormatCode('"\"#,##0');
    $sheet->getStyle("L".$row_cnt)->getNumberFormat()->setFormatCode('"\"#,##0');
    $sheet->getStyle("M".$row_cnt)->getNumberFormat()->setFormatCode('"\"#,##0');
    $sheet->getStyle("N".$row_cnt)->getNumberFormat()->setFormatCode('"\"#,##0');
    $sheet->getStyle("O".$row_cnt)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
    $sheet->getStyle("P".$row_cnt)->getNumberFormat()->setFormatCode('"\"#,##0');
    $sheet->getStyle("Q".$row_cnt)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);

    // PHPの日時出力
    $time = time();                 // 現在日時(Unix Timestamp)
    $sheet->setCellValue("S2", '出力日：'.date('Y/m/d'));
    $sheet->setCellValue("C2", $sheet_name."分売上一覧");

// Excelファイルの保存
// 保存ファイルフルパス
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