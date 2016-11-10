<?php 
//10分
set_time_limit(600);

// Excel出力用ライブラリ   
App::import( 'Vendor', 'PHPExcel', array('file'=>'phpexcel' . DS . 'PHPExcel.php') );   
App::import( 'Vendor', 'PHPExcel_IOFactory', array('file'=>'phpexcel' . DS . 'PHPExcel' . DS . 'IOFactory.php') );   
App::import( 'Vendor', 'PHPExcel_Cell_AdvancedValueBinder', array('file'=>'phpexcel' . DS . 'PHPExcel' . DS . 'Cell' . DS . 'AdvancedValueBinder.php') );   
  
// Excel2007用ライブラリ   
App::import( 'Vendor', 'PHPExcel_Writer_Excel2007', array('file'=>'phpexcel' . DS . 'PHPExcel' . DS . 'Writer' . DS . 'Excel2007.php') );   
App::import( 'Vendor', 'PHPExcel_Reader_Excel2007', array('file'=>'phpexcel' . DS . 'PHPExcel' . DS . 'Reader' . DS . 'Excel2007.php') ); 
  
    $reader = PHPExcel_IOFactory::createReader('Excel2007');
    $objPHPExcel = $reader->load(realpath( TMP ).DS . 'excels' . DS . $template_file); 

    $objPHPExcel->setActiveSheetIndex( 0 );   
    $sheet = $objPHPExcel->getActiveSheet();  

    // デフォルトのフォント   
    $sheet->getDefaultStyle()->getFont()->setName('ＭＳ Ｐゴシック');   
    // デフォルトのフォントサイズ   
    $sheet->getDefaultStyle()->getFont()->setSize(13);       
    // 列名   
    $row_cnt = 1;   
    $base_row = 1;
    
    // PHPの日時出力   
    //$time = time();                 // 現在日時(Unix Timestamp)   
    //$sheet->setCellValue( 'A' . $row_cnt, PHPExcel_Shared_Date::PHPToExcel( $time ) );   
    //$sheet->getStyle( 'A' . $row_cnt)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDDSLASH);   
     /* ロゴの出力 */
    $objDrawing = new PHPExcel_Worksheet_Drawing(); 
    $objDrawing->setPath('images/company_logo.png'); 
    $objDrawing->setHeight(30);
    $objDrawing->setCoordinates('M1');       
    $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());

// Excelファイルの保存
// 保存ファイルフルパス   
$uploadDir = realpath( TMP );   
$uploadDir .= DS . 'excels' . DS;   
$path = $uploadDir . $filename;     

$objWriter = PHPExcel_IOFactory::createWriter( $objPHPExcel, 'Excel2007' );  //Excel2007用 ファイル名は.xlsxとする
$objWriter->save( $path );   
  
// Excelファイルをクライアントに出力  
Configure::write('debug', 0);       // debugコードを非表示   
header("Content-disposition: attachment; filename={$filename}");   
header('Content-Type: application/vnd.ms-excel');
header('Cache-Control: max-age=0');
$result = file_get_contents( $path );   // ダウンロードするデータの取得   
print( $result );                       // 出力  
?>