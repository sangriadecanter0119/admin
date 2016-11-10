<?php
set_time_limit(300);
// Excel出力用ライブラリ
App::import( 'Vendor', 'PHPExcel', array('file'=>'phpexcel' . DS . 'PHPExcel.php') );
App::import( 'Vendor', 'PHPExcel_Writer_Excel2007', array('file'=>'phpexcel' . DS . 'PHPExcel' . DS . 'Writer' . DS . 'Excel2007.php') );
App::import( 'Vendor', 'PHPExcel_Reader_Excel2007', array('file'=>'phpexcel' . DS . 'PHPExcel' . DS . 'Reader' . DS . 'Excel2007.php') );

    /* 指定の種類
     *   sheet->setCellValue('B1', '=A2+A3');
     *   sheet->setCellValue('A1:A5', value);
     *   sheet->setCellValueByColumnAndRow(col#, row#, value);
     */

    // Create new PHPExcel object
    $objPHPExcel = new PHPExcel();

    // Set active sheet index to the first sheet, so Excel opens this as the first sheet
    $objPHPExcel->setActiveSheetIndex( 0 );
    $sheet = $objPHPExcel->getActiveSheet();

    // シート名をつける
    $sheet->setTitle( $sheet_name );
    // デフォルトのフォント
    $sheet->getDefaultStyle()->getFont()->setName('ＭＳ Ｐゴシック');
    // デフォルトのフォントサイズ
    $sheet->getDefaultStyle()->getFont()->setSize(11);
    // 列名
    $row_cnt = 1;

    /* 項目 */
    $sheet->setCellValue( 'A' . $row_cnt, "No");
    $sheet->setCellValue( 'B' . $row_cnt, "顧客番号");
    $sheet->setCellValue( 'C' . $row_cnt, "新郎名");
    $sheet->setCellValue( 'D' . $row_cnt, "新婦名");
    $sheet->setCellValue( 'E' . $row_cnt, "郵便番号");
    $sheet->setCellValue( 'F' . $row_cnt, "住所");
    $sheet->setCellValue( 'G' . $row_cnt, "電話番号");
    $sheet->setCellValue( 'H' . $row_cnt, "メールアドレス");
    $sheet->setCellValue( 'I' . $row_cnt, "挙式日");
    $sheet->setCellValue( 'J' . $row_cnt, "ステータス");
    $sheet->setCellValue( 'K' . $row_cnt, "新規担当者");
    $sheet->setCellValue( 'L' . $row_cnt, "プラン担当者");
    $sheet->setCellValue( 'M' . $row_cnt, "最新アクション");
    $sheet->setCellValue( 'N' . $row_cnt, "初回見積提出日");
	$sheet->setCellValue( 'O' . $row_cnt, "問い合わせ日");

    $row_cnt++;

    /* データ */
    for($i=0;$i < count($customers);$i++){

      $atr = $customers[$i]['CustomerMstView'];

      $sheet->setCellValue( 'A' . $row_cnt, $i+1);
      $sheet->setCellValue( 'B' . $row_cnt, $atr['customer_cd']);

      if($atr['grmls_kj'] != "" || $atr['grmfs_kj'] != ""){
         $sheet->setCellValue( 'C' . $row_cnt, $atr['grmls_kj']." ".$atr['grmfs_kj']);
      }else{
         $sheet->setCellValue( 'C' . $row_cnt, $atr['grmls_kn']." ".$atr['grmfs_kn']);
      }

	  if($atr['brdls_kj'] != "" || $atr['brdfs_kj'] != ""){
	     $sheet->setCellValue( 'D' . $row_cnt, $atr['brdls_kj']." ".$atr['brdfs_kj']);
	  }else{
	     $sheet->setCellValue( 'D' . $row_cnt, $atr['brdls_kn']." ".$atr['brdfs_kn']);
	  }

      if($atr['prm_address_flg'] == 0){
		 $sheet->setCellValue( 'E' . $row_cnt, $atr['grm_zip_cd']);
		 $sheet->setCellValue( 'F' . $row_cnt, $atr['grm_address']);
      }else{
         $sheet->setCellValue( 'E' . $row_cnt, $atr['brd_zip_cd']);
		 $sheet->setCellValue( 'F' . $row_cnt, $atr['brd_address']);
      }

      if($atr['prm_phone_no_flg'] == 0){
         $sheet->setCellValue( 'G' . $row_cnt, $atr['grm_phone_no']);
	  }else{
	     $sheet->setCellValue( 'G' . $row_cnt, $atr['brd_phone_no']);
      }

      if($atr['prm_email_flg'] == 0){
         $sheet->setCellValue( 'H' . $row_cnt, $atr['grm_email']);
 	  }else{
      	 $sheet->setCellValue( 'H' . $row_cnt, $atr['brd_email']);
      }

      if($atr['status_id']>=CS_CONTRACTED){
         $sheet->setCellValue( 'I' . $row_cnt, $common->evalNbspForShortDate($atr['wedding_dt']));
	  }else{
	     $sheet->setCellValue( 'I' . $row_cnt, $common->evalNbspForShortDate($atr['wedding_planned_dt']));
      }

      $sheet->setCellValue( 'J' . $row_cnt, $atr['status_nm']);
      $sheet->setCellValue( 'K' . $row_cnt, $atr['first_contact_person_nm']);
      $sheet->setCellValue( 'L' . $row_cnt, $atr['process_person_nm']);
      $sheet->setCellValue( 'M' . $row_cnt, $atr['action_nm1']);
      $sheet->setCellValue( 'N' . $row_cnt, $common->evalNbspForShortDate($atr['estimate_issued_dt']));
	  $sheet->setCellValue( 'O' . $row_cnt, $common->evalNbspForShortDate($atr['first_contact_dt']));

	  $row_cnt++;
    }

// Excelファイルの保存
$uploadDir = realpath("./files");
$path = $uploadDir . DS . $filename;

$objWriter = new PHPExcel_Writer_Excel2007( $objPHPExcel );
$objWriter->save( $path );

// Excelファイルをクライアントに出力
Configure::write('debug', 0);       // debugコードを非表示
header("Content-disposition: attachment; filename={$filename}");
header("Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; name={$filename}");

$result = file_get_contents( $path );   // ダウンロードするデータの取得
print( $result );
?>