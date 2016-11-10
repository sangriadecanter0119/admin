<?php
set_time_limit(300);
// Excel出力用ライブラリ
App::import( 'Vendor', 'PHPExcel', array('file'=>'phpexcel' . DS . 'PHPExcel.php') );
//App::import( 'Vendor', 'PHPExcel_IOFactory', array('file'=>'phpexcel' . DS . 'PHPExcel' . DS . 'IOFactory.php') );
//App::import( 'Vendor', 'PHPExcel_Cell_AdvancedValueBinder', array('file'=>'phpexcel' . DS . 'PHPExcel' . DS . 'Cell' . DS . 'AdvancedValueBinder.php') );

// Excel95用ライブラリ
App::import( 'Vendor', 'PHPExcel_Writer_Excel2007', array('file'=>'phpexcel' . DS . 'PHPExcel' . DS . 'Writer' . DS . 'Excel2007.php') );
App::import( 'Vendor', 'PHPExcel_Reader_Excel2007', array('file'=>'phpexcel' . DS . 'PHPExcel' . DS . 'Reader' . DS . 'Excel2007.php') );

    /* 指定の種類
     *   sheet->setCellValue('B1', '=A2+A3');
     *   sheet->setCellValue('A1:A5', value);
     *   sheet->setCellValueByColumnAndRow(col#, row#, value);
     */

    // Create new PHPExcel object
    //$objPHPExcel = new PHPExcel();
    //read template xls file
    $reader = new PHPExcel_Reader_Excel2007();
    $objPHPExcel = $reader->load(realpath( TMP ).DS . 'excels' . DS . $template_file);

    // Set active sheet index to the first sheet, so Excel opens this as the first sheet
    $objPHPExcel->setActiveSheetIndex( 0 );
    $sheet = $objPHPExcel->getActiveSheet();

    // Rename sheet
    // シート名をつける
    $sheet->setTitle( $sheet_name );
    // デフォルトのフォント
    $sheet->getDefaultStyle()->getFont()->setName('ＭＳ Ｐゴシック');
    // デフォルトのフォントサイズ
    $sheet->getDefaultStyle()->getFont()->setSize(11);
    // 列名
    $row_cnt = 1;

     // PHPの日時出力
    $time = time();                 // 現在日時(Unix Timestamp)
    $sheet->setCellValue( 'A' . $row_cnt, PHPExcel_Shared_Date::PHPToExcel( $time ) );
    $sheet->getStyle( 'A' . $row_cnt)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDDSLASH);

    $row_cnt++;
    $row_cnt++;

    // データ
    for($i=0;$i < count($estimate_dtl);$i++)
    {
       $sheet->insertNewRowBefore($row_cnt, 1);

	   $sheet->setCellValue( 'A' . $row_cnt, $estimate_dtl[$i]['EstimateDtlTrnView']['goods_kbn_nm']);
	   $sheet->setCellValue( 'B' . $row_cnt, $estimate_dtl[$i]['EstimateDtlTrnView']['sales_goods_nm']);
	   $sheet->setCellValue( 'C' . $row_cnt, $estimate_dtl[$i]['EstimateDtlTrnView']['num']);
	   $sheet->setCellValue( 'D' . $row_cnt, $estimate_dtl[$i]['EstimateDtlTrnView']['sales_exchange_rate']);
	   $sheet->setCellValue( 'E' . $row_cnt, $estimate_dtl[$i]['EstimateDtlTrnView']['cost_exchange_rate']);

	   /* 円ベース */
	   //単価
	   $sheet->setCellValue( 'F' . $row_cnt, $estimate_dtl[$i]['EstimateDtlTrnView']['yen_price']);
	   //原価
	   $sheet->setCellValue( 'G' . $row_cnt, $estimate_dtl[$i]['EstimateDtlTrnView']['yen_cost']);
	   //総代価
	   $sheet->setCellValue( 'H' . $row_cnt, "=F".$row_cnt."* C".$row_cnt);
	   //総原価
	   $sheet->setCellValue( 'I' . $row_cnt, "=G".$row_cnt."* C".$row_cnt);
	   //利益
	   $sheet->setCellValue( 'J' . $row_cnt, "=H".$row_cnt."- I".$row_cnt);
	   //利益率
	   $sheet->setCellValue( 'K' . $row_cnt, "=J".$row_cnt." / H".$row_cnt);
	   //現地取り分
	   $sheet->setCellValue( 'L' . $row_cnt, "=J".$row_cnt." * X".$row_cnt);
	   //EMPRESS
	   $sheet->setCellValue( 'M' . $row_cnt, "=J".$row_cnt." * Y".$row_cnt);
	   //国内支払
	   if($estimate_dtl[$i]['EstimateDtlTrnView']['payment_kbn_id']==PC_DOMESTIC_DIRECT_PAY ||
	      $estimate_dtl[$i]['EstimateDtlTrnView']['payment_kbn_id']==PC_DOMESTIC_CREDIT_PAY){
	    $sheet->setCellValue( 'N' . $row_cnt, "=H".$row_cnt);
	   }else{
	   	$sheet->setCellValue( 'N' . $row_cnt, 0);
	   }
	   /* ドルベース */
	   //単価
	   $sheet->setCellValue( 'O' . $row_cnt, $estimate_dtl[$i]['EstimateDtlTrnView']['dollar_price']);
	   //原価
	   $sheet->setCellValue( 'P' . $row_cnt, $estimate_dtl[$i]['EstimateDtlTrnView']['dollar_cost']);
	   //総代価
	   $sheet->setCellValue( 'Q' . $row_cnt, "=O".$row_cnt."* C".$row_cnt);
	   //総原価
	   $sheet->setCellValue( 'R' . $row_cnt, "=P".$row_cnt."* C".$row_cnt);
	   //利益
	   $sheet->setCellValue( 'S' . $row_cnt, "=Q".$row_cnt."- R".$row_cnt);
	   //利益率
	   $sheet->setCellValue( 'T' . $row_cnt, "=S".$row_cnt."/ Q".$row_cnt);
	   //現地取り分
	   $sheet->setCellValue( 'U' . $row_cnt, "=S".$row_cnt."* X".$row_cnt);
	   //EMPRESS
	   $sheet->setCellValue( 'V' . $row_cnt, "=S".$row_cnt."* Y".$row_cnt);

	   /* SHARE */
	   $sheet->setCellValue( 'X' . $row_cnt, $estimate_dtl[$i]['EstimateDtlTrnView']['aw_share']);
	   $sheet->setCellValue( 'Y' . $row_cnt, $estimate_dtl[$i]['EstimateDtlTrnView']['rw_share']);
       //国内支払
	    if($estimate_dtl[$i]['EstimateDtlTrnView']['payment_kbn_id']==PC_DOMESTIC_DIRECT_PAY ||
	       $estimate_dtl[$i]['EstimateDtlTrnView']['payment_kbn_id']==PC_DOMESTIC_CREDIT_PAY){
	       $sheet->setCellValue( 'W' . $row_cnt, "=Q".$row_cnt);
	   }else{
	   	   $sheet->setCellValue( 'W' . $row_cnt, 0);
	   }
	   $row_cnt++;
    }
       //Alignment
       $sheet->getStyle('C3:Y'.$row_cnt)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
       $sheet->getStyle('A3:B'.$row_cnt)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

       $sheet->removeRow($row_cnt, 1);

       //ハワイ州税
       $sheet->setCellValue( 'C' . ($row_cnt+1), $estimate_dtl[0]['EstimateDtlTrnView']['hawaii_tax_rate']);
       //アレンジメント率
       $sheet->setCellValue( 'C' . ($row_cnt+2), $estimate_dtl[0]['EstimateDtlTrnView']['service_rate']);
       //割引率
       $sheet->setCellValue( 'C' . ($row_cnt+4), $estimate_dtl[0]['EstimateDtlTrnView']['discount_rate']);
       //割引率AWシェア
       $sheet->setCellValue( 'X' . ($row_cnt+4), $estimate_dtl[0]['EstimateDtlTrnView']['discount_aw_share']);
       //割引率RWシェア
       $sheet->setCellValue( 'Y' . ($row_cnt+4), $estimate_dtl[0]['EstimateDtlTrnView']['discount_rw_share']);
       //割引額
       $sheet->setCellValue( 'C' . ($row_cnt+5), $estimate_dtl[0]['EstimateDtlTrnView']['discount']);
       //割引額為替レート
       $sheet->setCellValue( 'E' . ($row_cnt+5), $estimate_dtl[0]['EstimateDtlTrnView']['discount_exchange_rate']);

       //総代価
       $sheet->setCellValue( 'H' . $row_cnt, '=SUM(H3:H' . ($row_cnt-1) . ')');
       //総原価
       $sheet->setCellValue( 'I' . $row_cnt, '=SUM(I3:I' . ($row_cnt-1) . ')');
       //現地取り分
       $sheet->setCellValue( 'L' . $row_cnt, '=SUM(L3:L' . ($row_cnt-1) . ')');
       //EMPRESS
       $sheet->setCellValue( 'M' . $row_cnt, '=SUM(M3:M' . ($row_cnt-1) . ')');
       //国内支払
       $sheet->setCellValue( 'N' . $row_cnt, '=SUM(N3:N' . ($row_cnt-1) . ')');

       //総代価
       $sheet->setCellValue( 'Q' . $row_cnt, '=SUM(Q3:Q' . ($row_cnt-1) . ')');
       //総原価
       $sheet->setCellValue( 'R' . $row_cnt, '=SUM(R3:R' . ($row_cnt-1) . ')');
       //現地取り分
       $sheet->setCellValue( 'U' . $row_cnt, '=SUM(U3:U' . ($row_cnt-1) . ')');
       //EMPRESS
       $sheet->setCellValue( 'V' . $row_cnt, '=SUM(V3:V' . ($row_cnt-1) . ')');
       //国内支払
       $sheet->setCellValue( 'W' . $row_cnt, '=SUM(W3:W' . ($row_cnt-1) . ')');

// Excelファイルの保存
// 保存ファイルフルパス
//$uploadDir = realpath( TMP );
$uploadDir = realpath("./files");
//$uploadDir .= DS . 'excels' . DS;
$path = $uploadDir . DS . $filename;

//$uploadDir = realpath("./files");
//$path = $uploadDir . DS .$filename;

$objWriter = new PHPExcel_Writer_Excel2007( $objPHPExcel );
//Excel2007用 ファイル名は.xlsxとする
//$objWriter = PHPExcel_IOFactory::createWriter( $objPHPExcel, 'Excel2007' );
$objWriter->save( $path );

// Excelファイルをクライアントに出力
Configure::write('debug', 0);       // debugコードを非表示
header("Content-disposition: attachment; filename={$filename}");
header("Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; name={$filename}");

$result = file_get_contents( $path );   // ダウンロードするデータの取得
print( $result );                       // 出力
//print($filename);

?>