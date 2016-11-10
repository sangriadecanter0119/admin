<?php

set_time_limit(600);

// Excel出力用ライブラリ
App::import( 'Vendor', 'PHPExcel', array('file'=>'phpexcel' . DS . 'PHPExcel.php') );
//App::import( 'Vendor', 'PHPExcel_IOFactory', array('file'=>'phpexcel' . DS . 'PHPExcel' . DS . 'IOFactory.php') );
//App::import( 'Vendor', 'PHPExcel_Cell_AdvancedValueBinder', array('file'=>'phpexcel' . DS . 'PHPExcel' . DS . 'Cell' . DS . 'AdvancedValueBinder.php') );
App::import( 'Vendor', 'PHPExcel_Writer_Excel2007', array('file'=>'phpexcel' . DS . 'PHPExcel' . DS . 'Writer' . DS . 'Excel2007.php') );
App::import( 'Vendor', 'PHPExcel_Reader_Excel2007', array('file'=>'phpexcel' . DS . 'PHPExcel' . DS . 'Reader' . DS . 'Excel2007.php') );

    //read template xls file
    $reader = new PHPExcel_Reader_Excel2007();
    $objPHPExcel = $reader->load(realpath( TMP ).DS . 'excels' . DS . $template_file);

    // Set active sheet index to the first sheet, so Excel opens this as the first sheet
    $objPHPExcel->setActiveSheetIndex( 0 );
    $sheet = $objPHPExcel->getActiveSheet();

    // Rename sheet
    // シート名をつける
   // $sheet->setTitle( $sheet_name );
    // デフォルトのフォント
    $sheet->getDefaultStyle()->getFont()->setName('ＭＳ Ｐゴシック');
    // デフォルトのフォントサイズ
    $sheet->getDefaultStyle()->getFont()->setSize(11);
    // 列番
    $row_cnt = 4;
    // 行番
    $col_cnt = 2;
    /* 挙式一覧作成 */
    setWeddingSheet($sheet,$wedding_data,$wedding_dt,$showing_month_count,$row_cnt,$col_cnt);

    $objPHPExcel->setActiveSheetIndex( 1 );
    $sheet = $objPHPExcel->getActiveSheet();
    /* 約定一覧作成 */
    setContractSheet($sheet,$contract_data,$wedding_dt,$showing_month_count,$row_cnt,$col_cnt);

   // Excelファイルの保存
   // 保存ファイルフルパス
   $uploadDir = realpath( TMP );
   $uploadDir .= DS . 'excels' . DS;
   $path = $uploadDir . $filename;

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

   /**
    * 挙式一覧作成
    * @param unknown $sheet
    * @param unknown $wedding_data
    * @param unknown $wedding_dt
    * @param unknown $showing_month_count
    * @param unknown $row_cnt
    * @param unknown $col_cnt
    */
   function setWeddingSheet($sheet,$wedding_data,$wedding_dt,$showing_month_count,$row_cnt,$col_cnt){

   	$cols = array("","A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z",
   	                 "AA","AB","AC","AD","AE","AF","AG","AH","AI","AJ","AK","AL","AM","AN","AO","AP","AQ","AR","AS","AT","AU","AV","AW");

   	for($i=0;$i < $showing_month_count ; $i++){
   		$sheet->setCellValue($cols[2+($i*4)].$row_cnt,date('Y-m',strtotime($wedding_dt." +".$i." month")));
   	}

   	for($i=0;;$i++)
   	{
   	if(count($wedding_data[0]) <= $i && count($wedding_data[1]) <= $i && count($wedding_data[2]) <= $i &&
   	count($wedding_data[3]) <= $i && count($wedding_data[4]) <= $i && count($wedding_data[5]) <= $i){break;}

   	$row_cnt++;
   	$customers = array();
   	for($j=0 ; $j < $showing_month_count ;$j++){
   	$customers[$j]['name'] = count($wedding_data[$j]) <= $i ? null : $wedding_data[$j][$i]['customer_nm'];
   	$customers[$j]['code'] = count($wedding_data[$j]) <= $i ? null : $wedding_data[$j][$i]['customer_cd'];
   	$customers[$j]['first_contact_person_nm'] = count($wedding_data[$j]) <= $i ? null : $wedding_data[$j][$i]['first_contact_person_nm'];
   	$customers[$j]['process_person_nm']       = count($wedding_data[$j]) <= $i ? null : $wedding_data[$j][$i]['process_person_nm'];
   	}

   	for($j=0 ; $j < $showing_month_count ;$j++){
   	$sheet->setCellValue($cols[2+($j*4)].$row_cnt,$customers[$j]['name']);
   	$sheet->setCellValue($cols[3+($j*4)].$row_cnt,$customers[$j]['code']);
   	$sheet->setCellValue($cols[4+($j*4)].$row_cnt,$customers[$j]['first_contact_person_nm']);
   	$sheet->setCellValue($cols[5+($j*4)].$row_cnt,$customers[$j]['process_person_nm']);

   	/* 羅線 */
   	$sheet->getStyle($cols[2+($j*4)].$row_cnt)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
   	$sheet->getStyle($cols[2+($j*4)].$row_cnt)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
   	$sheet->getStyle($cols[2+($j*4)].$row_cnt)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
   	$sheet->getStyle($cols[2+($j*4)].$row_cnt)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

   	$sheet->getStyle($cols[3+($j*4)].$row_cnt)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
   	$sheet->getStyle($cols[3+($j*4)].$row_cnt)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
   	$sheet->getStyle($cols[3+($j*4)].$row_cnt)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
   	$sheet->getStyle($cols[3+($j*4)].$row_cnt)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

   	$sheet->getStyle($cols[4+($j*4)].$row_cnt)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
   	$sheet->getStyle($cols[4+($j*4)].$row_cnt)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
   	$sheet->getStyle($cols[4+($j*4)].$row_cnt)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
   	$sheet->getStyle($cols[4+($j*4)].$row_cnt)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

   	$sheet->getStyle($cols[5+($j*4)].$row_cnt)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
   	$sheet->getStyle($cols[5+($j*4)].$row_cnt)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
   	$sheet->getStyle($cols[5+($j*4)].$row_cnt)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
   	$sheet->getStyle($cols[5+($j*4)].$row_cnt)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
   	}
   	}

   	//文字Alignment
   	$sheet->getStyle("B5:M".$row_cnt)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
   	// PHPの日時出力
   	$time = time();                 // 現在日時(Unix Timestamp)
   	$sheet->setCellValue("C2", "出力日：".date('Y/m/d'));
   }

   /**
    * 約定一覧作成
    * @param unknown $sheet
    * @param unknown $wedding_data
    * @param unknown $wedding_dt
    * @param unknown $showing_month_count
    * @param unknown $row_cnt
    * @param unknown $col_cnt
    */
   function setContractSheet($sheet,$contract_data,$wedding_dt,$showing_month_count,$row_cnt,$col_cnt){

   	$cols = array("","A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z",
   	                 "AA","AB","AC","AD","AE","AF","AG","AH","AI","AJ","AK","AL","AM","AN","AO","AP","AQ","AR","AS","AT","AU","AV","AW");

   	for($i=0;$i < $showing_month_count ; $i++){
   		$sheet->setCellValue($cols[2+($i*4)].$row_cnt,date('Y-m',strtotime($wedding_dt." +".$i." month")));
   	}

   	for($i=0;;$i++)
   	{
   	if(count($contract_data[0]) <= $i && count($contract_data[1]) <= $i && count($contract_data[2]) <= $i &&
   	count($contract_data[3]) <= $i && count($contract_data[4]) <= $i && count($contract_data[5]) <= $i){break;}

   	$row_cnt++;
   	$customers = array();
   	for($j=0 ; $j < $showing_month_count ;$j++){
   	$customers[$j]['name'] = count($contract_data[$j]) <= $i ? null : $contract_data[$j][$i]['customer_nm'];
   	$customers[$j]['code'] = count($contract_data[$j]) <= $i ? null : $contract_data[$j][$i]['customer_cd'];
   	$customers[$j]['first_contact_person_nm'] = count($contract_data[$j]) <= $i ? null : $contract_data[$j][$i]['first_contact_person_nm'];
   	$customers[$j]['process_person_nm']       = count($contract_data[$j]) <= $i ? null : $contract_data[$j][$i]['process_person_nm'];
   	}

   	for($j=0 ; $j < $showing_month_count ;$j++){
   	$sheet->setCellValue($cols[2+($j*4)].$row_cnt,$customers[$j]['name']);
   	$sheet->setCellValue($cols[3+($j*4)].$row_cnt,$customers[$j]['code']);
    $sheet->setCellValue($cols[4+($j*4)].$row_cnt,$customers[$j]['first_contact_person_nm']);
   	$sheet->setCellValue($cols[5+($j*4)].$row_cnt,$customers[$j]['process_person_nm']);

   	/* 羅線 */
   	$sheet->getStyle($cols[2+($j*4)].$row_cnt)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
   	$sheet->getStyle($cols[2+($j*4)].$row_cnt)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
   	$sheet->getStyle($cols[2+($j*4)].$row_cnt)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
   	$sheet->getStyle($cols[2+($j*4)].$row_cnt)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

   	$sheet->getStyle($cols[3+($j*4)].$row_cnt)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
   	$sheet->getStyle($cols[3+($j*4)].$row_cnt)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
   	$sheet->getStyle($cols[3+($j*4)].$row_cnt)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
   	$sheet->getStyle($cols[3+($j*4)].$row_cnt)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

   	$sheet->getStyle($cols[4+($j*4)].$row_cnt)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
   	$sheet->getStyle($cols[4+($j*4)].$row_cnt)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
   	$sheet->getStyle($cols[4+($j*4)].$row_cnt)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
   	$sheet->getStyle($cols[4+($j*4)].$row_cnt)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

   	$sheet->getStyle($cols[5+($j*4)].$row_cnt)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
   	$sheet->getStyle($cols[5+($j*4)].$row_cnt)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
   	$sheet->getStyle($cols[5+($j*4)].$row_cnt)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
   	$sheet->getStyle($cols[5+($j*4)].$row_cnt)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
   	}
   	}

   	//文字Alignment
   	$sheet->getStyle("B5:M".$row_cnt)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
   	// PHPの日時出力
   	$time = time();                 // 現在日時(Unix Timestamp)
   	$sheet->setCellValue("C2", "出力日：".date('Y/m/d'));
   }
?>