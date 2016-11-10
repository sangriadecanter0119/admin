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
    $sheet->setTitle( $sheet_name );
    // デフォルトのフォント
    $sheet->getDefaultStyle()->getFont()->setName('ＭＳ Ｐゴシック');
    // デフォルトのフォントサイズ
    $sheet->getDefaultStyle()->getFont()->setSize(11);
    // 列番
    $row_cnt = 5;

    for($i=0;$i < count($data);$i++){

    	$sheet->setCellValue("B".$row_cnt, $i+1);

    	$sheet->setCellValue("C".$row_cnt, date("Y-m-d",strtotime($data[$i]['CreditTrnView']['credit_dt'])));
    	$sheet->setCellValue("D".$row_cnt, $data[$i]['CreditTrnView']['customer_cd']);
    	$sheet->setCellValue("E".$row_cnt, $data[$i]['CreditTrnView']['grmls_kj'].$data[$i]['CreditTrnView']['grmfs_kj']);
    	$sheet->setCellValue("F".$row_cnt, $data[$i]['CreditTrnView']['credit_customer_nm']);
    	$sheet->setCellValue("G".$row_cnt, $data[$i]['CreditTrnView']['amount']);
    	$sheet->setCellValue("H".$row_cnt, $data[$i]['CreditTrnView']['credit_type_nm']);
    	$sheet->setCellValue("I".$row_cnt, $data[$i]['CreditTrnView']['status_nm']);
    	$sheet->setCellValue("J".$row_cnt, $data[$i]['CreditTrnView']['reg_nm']);
    	$sheet->setCellValue("K".$row_cnt, date("Y-m-d",strtotime($data[$i]['CreditTrnView']['reg_dt'])));
    	$sheet->setCellValue("L".$row_cnt, $data[$i]['CreditTrnView']['upd_nm']);
    	$sheet->setCellValue("M".$row_cnt, empty($data[$i]['CreditTrnView']['upd_dt']) ? "": date("Y-m-d",strtotime($data[$i]['CreditTrnView']['upd_dt'])));

    	$sheet->getStyle("G".$row_cnt)->getNumberFormat()->setFormatCode('"\"#,##0');
        $row_cnt++;
        $sheet->insertNewRowBefore( $row_cnt, 1 );
     }
    $sheet->removeRow( $row_cnt);

    // PHPの日時出力
    $sheet->setCellValue("L2", '出力日：'.date('Y/m/d'));
    $sheet->setCellValue("C2", date('Y-m',strtotime($data[0]['CreditTrnView']['credit_dt']))."分入金一覧");

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