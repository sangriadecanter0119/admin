<?php
set_time_limit(300);
// Excel出力用ライブラリ
App::import( 'Vendor', 'PHPExcel', array('file'=>'phpexcel' . DS . 'PHPExcel.php') );
//App::import( 'Vendor', 'PHPExcel_IOFactory', array('file'=>'phpexcel' . DS . 'PHPExcel' . DS . 'IOFactory.php') );
//App::import( 'Vendor', 'PHPExcel_Cell_AdvancedValueBinder', array('file'=>'phpexcel' . DS . 'PHPExcel' . DS . 'Cell' . DS . 'AdvancedValueBinder.php') );

// Excel95用ライブラリ
//App::import( 'Vendor', 'PHPExcel_Writer_Excel5', array('file'=>'phpexcel' . DS . 'PHPExcel' . DS . 'Writer' . DS . 'Excel5.php') );
//App::import( 'Vendor', 'PHPExcel_Reader_Excel5', array('file'=>'phpexcel' . DS . 'PHPExcel' . DS . 'Reader' . DS . 'Excel5.php') );

App::import( 'Vendor', 'PHPExcel_Writer_Excel2007', array('file'=>'phpexcel' . DS . 'PHPExcel' . DS . 'Writer' . DS . 'Excel2007.php') );
App::import( 'Vendor', 'PHPExcel_Reader_Excel2007', array('file'=>'phpexcel' . DS . 'PHPExcel' . DS . 'Reader' . DS . 'Excel2007.php') );

    /* 指定の種類
     *   sheet->setCellValue('B1', '=A2+A3');
     *   sheet->setCellValue('A1:A5', value);
     *   sheet->setCellValueByColumnAndRow(col#, row#, value);
     */

    // Create new PHPExcel object
    $objPHPExcel = new PHPExcel();
     //$reader = new PHPExcel_Reader_Excel2007();
    //read template xls file
    //$reader = PHPExcel_IOFactory::createReader('Excel5');
    //$objPHPExcel = $reader->load(realpath( TMP ).DS . 'excels' . DS . $template_file);

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

    // データ
    for($i=0;$i < count($customers);$i++)
    {
       //苗字
       $last_name = null;
       if(!empty($customers[$i]['CustomerMstView']['grmls_kj'])){
       	  $last_name = $customers[$i]['CustomerMstView']['grmls_kj'];
       }else if(!empty($customers[$i]['CustomerMstView']['brdls_kj'])){
       	  $last_name = $customers[$i]['CustomerMstView']['brdls_kj'];
       }else if(!empty($customers[$i]['CustomerMstView']['grmls_kn'])){
       	  $last_name = $customers[$i]['CustomerMstView']['grmls_kn'];
       }else if(!empty($customers[$i]['CustomerMstView']['brdls_kn'])){
       	  $last_name = $customers[$i]['CustomerMstView']['brdls_kn'];
       }else if(!empty($customers[$i]['CustomerMstView']['grmls_rm'])){
       	  $last_name = $customers[$i]['CustomerMstView']['grmls_rm'];
       }else if(!empty($customers[$i]['CustomerMstView']['brdls_rm'])){
       	  $last_name = $customers[$i]['CustomerMstView']['brdls_rm'];
       }

       //新郎の名前
       $grm_first_name = null;
       if(!empty($customers[$i]['CustomerMstView']['grmfs_kj'])){
         	$grm_first_name = $customers[$i]['CustomerMstView']['grmfs_kj'];
       }else if(!empty($customers[$i]['CustomerMstView']['grmfs_kn'])){
         	$grm_first_name = $customers[$i]['CustomerMstView']['grmfs_kn'];
       }else if(!empty($customers[$i]['CustomerMstView']['grmfs_rm'])){
         	$grm_first_name = $customers[$i]['CustomerMstView']['grmfs_rm'];
       }

       //新婦の名前
       $brd_first_name = null;
       if(!empty($customers[$i]['CustomerMstView']['brdfs_kj'])){
       	$brd_first_name = $customers[$i]['CustomerMstView']['brdfs_kj'];
       }else if(!empty($customers[$i]['CustomerMstView']['brdfs_kn'])){
       	$brd_first_name = $customers[$i]['CustomerMstView']['brdfs_kn'];
       }else if(!empty($customers[$i]['CustomerMstView']['brdfs_rm'])){
       	$brd_first_name = $customers[$i]['CustomerMstView']['brdfs_rm'];
       }

	   $sheet->setCellValue( 'A' . $row_cnt, $last_name." ".$grm_first_name);
	   $sheet->setCellValue( 'B' . $row_cnt, $brd_first_name);

	   if($customers[$i]['CustomerMstView']['prm_address_flg']==0){
	   	$sheet->setCellValue( 'C' . $row_cnt, $customers[$i]['CustomerMstView']['grm_zip_cd']);
	    $sheet->setCellValue( 'D' . $row_cnt, $customers[$i]['CustomerMstView']['grm_pref'].$customers[$i]['CustomerMstView']['grm_city'].$customers[$i]['CustomerMstView']['grm_street']);
	    $sheet->setCellValue( 'E' . $row_cnt, $customers[$i]['CustomerMstView']['grm_apart']);
	   }else{
	   	$sheet->setCellValue( 'C' . $row_cnt, $customers[$i]['CustomerMstView']['brd_zip_cd']);
	    $sheet->setCellValue( 'D' . $row_cnt, $customers[$i]['CustomerMstView']['brd_pref'].$customers[$i]['CustomerMstView']['brd_city'].$customers[$i]['CustomerMstView']['brd_street']);
	    $sheet->setCellValue( 'E' . $row_cnt, $customers[$i]['CustomerMstView']['brd_apart']);
	   }

       if($customers[$i]['CustomerMstView']['prm_phone_no_flg']==0){
       	 if($customers[$i]['CustomerMstView']['grm_phone_no'] != ""){
	   	    $sheet->setCellValue( 'F' . $row_cnt, $customers[$i]['CustomerMstView']['grm_phone_no']);
       	 }else{
       	 	$sheet->setCellValue( 'F' . $row_cnt, $customers[$i]['CustomerMstView']['grm_cell_no']);
       	 }
	   }else{
	   	 if($customers[$i]['CustomerMstView']['brd_phone_no'] != ""){
	   	    $sheet->setCellValue( 'F' . $row_cnt, $customers[$i]['CustomerMstView']['brd_phone_no']);
       	 }else{
       	 	$sheet->setCellValue( 'F' . $row_cnt, $customers[$i]['CustomerMstView']['brd_cell_no']);
       	 }
	   }

	   $row_cnt++;
    }
       //Alignment
      // $sheet->getStyle('C3:Y'.$row_cnt)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
      // $sheet->getStyle('A3:B'.$row_cnt)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
// Excelファイルの保存
// 保存ファイルフルパス
//$uploadDir = realpath( TMP );
$uploadDir = realpath("./files");
//$uploadDir .= DS . 'excels' . DS;
$path = $uploadDir . DS . $filename;

//$uploadDir = realpath("./files");
//$path = $uploadDir . DS .$filename;

$objWriter = new PHPExcel_Writer_Excel2007( $objPHPExcel );
//$objWriter = new PHPExcel_Writer_Excel5( $objPHPExcel );
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