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

    /* 要約
     --------------------------------------------------------*/
    // Set active sheet index to the first sheet, so Excel opens this as the first sheet
    $objPHPExcel->setActiveSheetIndex( 0 );
    $sheet = $objPHPExcel->getActiveSheet();

    // Rename sheet
    // シート名をつける
    //$sheet->setTitle( $sheet_name."_ドル表記" );
    // デフォルトのフォント
    $sheet->getDefaultStyle()->getFont()->setName('ＭＳ Ｐゴシック');
    // デフォルトのフォントサイズ
    $sheet->getDefaultStyle()->getFont()->setSize(11);
    // 列番
    $row_cnt = 3;

    $sum_sales_num = 0;
	$sum_sales_customer= 0;
	$sum_sales = 0;
	$sum_cost = 0;

    for($i=0;$i < count($header);$i++){

        $atr = $header[$i][0];

    	$sheet->setCellValue("B".$row_cnt, $i+1);
    	$sheet->setCellValue("C".$row_cnt, $atr['goods_ctg_nm']);
    	$sheet->setCellValue("D".$row_cnt, $atr['vendor_nm']);
    	$sheet->setCellValue("E".$row_cnt, $atr['customer_num']);
    	$sheet->setCellValue("F".$row_cnt, $atr['sales_num']);
    	$sheet->setCellValue("G".$row_cnt, $atr['sales_price']);
    	$sheet->setCellValue("H".$row_cnt, $atr['sales_cost']);

    	$sum_sales_num      += $atr['sales_num'];
    	$sum_sales_customer += $atr['customer_num'];
	    $sum_sales += $atr['sales_price'];
	    $sum_cost  += $atr['sales_cost'];

        $row_cnt++;
     }
        $sheet->setCellValue("E".$row_cnt, $sum_sales_customer);
        $sheet->setCellValue("F".$row_cnt, $sum_sales_num);
    	$sheet->setCellValue("G".$row_cnt, $sum_sales);
    	$sheet->setCellValue("H".$row_cnt, $sum_cost);

    $time = time();
    $sheet->setCellValue("H1", '出力日：'.date('Y/m/d'));
    $sheet->setCellValue("C1", $start_date."～".$end_date);

    /* 詳細
     --------------------------------------------------------*/
    $objPHPExcel->setActiveSheetIndex(1);
    $sheet = $objPHPExcel->getActiveSheet();

    // Rename sheet
    // シート名をつける
    //$sheet->setTitle( $sheet_name."_円表記" );
    // デフォルトのフォント
    $sheet->getDefaultStyle()->getFont()->setName('ＭＳ Ｐゴシック');
    // デフォルトのフォントサイズ
    $sheet->getDefaultStyle()->getFont()->setSize(11);
    // 列番
    $row_cnt = 3;

    $sum_sales_num = 0;
	$sum_sales_customer= 0;
	$sum_sales = 0;
	$sum_cost = 0;

    for($i=0;$i < count($detail);$i++){

    	$atr = $detail[$i];

    	$sheet->setCellValue("B".$row_cnt, $i+1);
    	$sheet->setCellValue("C".$row_cnt, $atr[0]['goods_ctg_nm']);
    	$sheet->setCellValue("D".$row_cnt, $atr[0]['vendor_nm']);
    	$sheet->setCellValue("E".$row_cnt, $atr[0]['goods_cd']);
    	$sheet->setCellValue("F".$row_cnt, $atr[0]['goods_nm']);
    	$sheet->setCellValue("G".$row_cnt, $atr[0]['num']);
    	$sheet->setCellValue("H".$row_cnt, $atr[0]['sales_price']);
    	$sheet->setCellValue("I".$row_cnt, $atr[0]['sales_cost']);
    	$sheet->setCellValue("J".$row_cnt, $atr[0]['customer_nm']);
    	$sheet->setCellValue("K".$row_cnt, date("Y-m-d",strtotime($atr[0]['wedding_dt'])));

    	$sum_sales_num += $atr[0]['num'];
   	    $sum_sales += $atr[0]['sales_price'];
	    $sum_cost  += $atr[0]['sales_cost'];

    	$row_cnt++;
    }
        $sheet->setCellValue("G".$row_cnt, $sum_sales_num);
    	$sheet->setCellValue("H".$row_cnt, $sum_sales);
    	$sheet->setCellValue("I".$row_cnt, $sum_cost);

    $time = time();
    $sheet->setCellValue("K1", '出力日：'.date('Y/m/d'));
    $sheet->setCellValue("C1", $start_date."～".$end_date);

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