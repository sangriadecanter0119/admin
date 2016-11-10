<?php

set_time_limit(600);

// Excel出力用ライブラリ
App::import( 'Vendor', 'PHPExcel', array('file'=>'phpexcel' . DS . 'PHPExcel.php') );
App::import( 'Vendor', 'PHPExcel_Writer_Excel2007', array('file'=>'phpexcel' . DS . 'PHPExcel' . DS . 'Writer' . DS . 'Excel2007.php') );
App::import( 'Vendor', 'PHPExcel_Reader_Excel2007', array('file'=>'phpexcel' . DS . 'PHPExcel' . DS . 'Reader' . DS . 'Excel2007.php') );

    //read template xls file
    $reader = new PHPExcel_Reader_Excel2007();
    $objPHPExcel = $reader->load(realpath( TMP ).DS . 'excels' . DS . $template_file);

    // Set active sheet index to the first sheet, so Excel opens this as the first sheet
    $objPHPExcel->setActiveSheetIndex( 0 );
    $sheet = $objPHPExcel->getActiveSheet();

    // デフォルトのフォント
    $sheet->getDefaultStyle()->getFont()->setName('ＭＳ Ｐゴシック');
    // デフォルトのフォントサイズ
    $sheet->getDefaultStyle()->getFont()->setSize(11);
    // 列番
    $row_cnt = 2;

    // PHPの日時出力
   	//$time = time();
   	//$sheet->setCellValue("C2", "出力日：".date('Y/m/d'));

    /* 成約候補一覧作成 */
    $row_cnt = setEstimateSheet($sheet,$estimate_data,$row_cnt,"【成約候補】");
    /* 当月成約一覧作成 */
    $row_cnt++;
    $row_cnt = setWeddingSheet($sheet,$contract_data,$row_cnt,"【当月成約】");    
    /* 当月挙式一覧作成 */
    $row_cnt++;
    $row_cnt =setWeddingSheet($sheet,$wedding_data,$row_cnt,"【当月挙式】");
    /* 翌月挙式一覧作成 */
    $row_cnt++;
    $row_cnt =setWeddingSheet($sheet,$next_wedding_data,$row_cnt,"【翌月挙式】");
    /* 製作対象一覧作成 */
    $row_cnt++;
    $row_cnt =setFutureWeddingSheet($sheet,$future_wedding_data,$row_cnt,"【制作対象】");

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

   //成約候補一覧作成
   function setEstimateSheet($sheet,$data,$row_cnt,$title){

   	$cols = array("","A","B","C","D","E","F","G","H","I","J","K","L","M","N");

    $sheet->setCellValue($cols[1].$row_cnt,$title);
    $row_cnt++;
    $counter = 1;
    $currentDate = "";
    for($i=0;$i < count($data);$i++){

      $atr = $data[$i]["CustomerMstView"];
      if($currentDate == "" || $currentDate != date('Y-m',strtotime($atr["estimate_issued_dt"]))){

         $sheet->setCellValue($cols[1].$row_cnt, "【".date('Y-m',strtotime($atr["estimate_issued_dt"]))."】");

         $currentDate = date('Y-m',strtotime($atr["estimate_issued_dt"]));
         $counter=1;
         $row_cnt++;
      }

      $sheet->setCellValue($cols[1].$row_cnt,$counter);
      $sheet->setCellValue($cols[2].$row_cnt,$atr['customer_cd']);

      if($atr['grmls_kj'] != "" || $atr['grmfs_kj'] != ""){

         $sheet->setCellValue($cols[3].$row_cnt,$atr['grmls_kj']." ".$atr['grmfs_kj']);

      }else if($atr['grmls_kn'] != "" || $atr['grmfs_kj'] != ""){

         $sheet->setCellValue($cols[3].$row_cnt,$atr['grmls_kn']." ".$atr['grmfs_kn']);

      }else if($atr['brdls_kj'] != "" || $atr['brdfs_kj'] != ""){

        $sheet->setCellValue($cols[3].$row_cnt,$atr['brdls_kj']." ".$atr['brdfs_kj']);

      }else{
         $sheet->setCellValue($cols[3].$row_cnt,$atr['brdls_kn']." ".$atr['brdfs_kn']);
      }

     if($atr['prm_address_flg']==0){
	   	$sheet->setCellValue($cols[4].$row_cnt, $atr['grm_zip_cd']);
	    $sheet->setCellValue($cols[5].$row_cnt, $atr['grm_pref'].$atr['grm_city'].$atr['grm_street'].$atr['grm_apart']);
	   }else{
	   	$sheet->setCellValue($cols[4].$row_cnt, $atr['brd_zip_cd']);
	    $sheet->setCellValue($cols[5].$row_cnt, $atr['brd_pref'].$atr['brd_city'].$atr['brd_street']. $atr['brd_apart']);
	   }

       if($atr['prm_phone_no_flg']==0){
       	 if($atr['grm_phone_no'] != ""){
	   	    $sheet->setCellValue($cols[6].$row_cnt, $atr['grm_phone_no']);
       	 }else{
       	 	$sheet->setCellValue($cols[6].$row_cnt, $atr['grm_cell_no']);
       	 }
	   }else{
	   	 if($atr['brd_phone_no'] != ""){
	   	    $sheet->setCellValue($cols[6].$row_cnt, $atr['brd_phone_no']);
       	 }else{
       	 	$sheet->setCellValue($cols[6].$row_cnt, $atr['brd_cell_no']);
       	 }
	   }

       if($atr['grm_email'] != ""){
	   	    $sheet->setCellValue($cols[7].$row_cnt, $atr['grm_email']);
       }else{
       	 	$sheet->setCellValue($cols[7].$row_cnt, $atr['brd_email']);
       }

      $sheet->setCellValue($cols[8].$row_cnt ,$atr["wedding_planned_dt"]);
      $sheet->setCellValue($cols[9].$row_cnt ,$atr['status_nm']);
      $sheet->setCellValue($cols[10].$row_cnt,$atr['first_contact_person_nm']);
      $sheet->setCellValue($cols[11].$row_cnt,$atr['process_person_nm']);
      $sheet->setCellValue($cols[12].$row_cnt,$atr['action_nm1']);
      $sheet->setCellValue($cols[13].$row_cnt,$atr['estimate_issued_dt']);
      $sheet->setCellValue($cols[14].$row_cnt,$atr['first_contact_dt']);

      /* 羅線 */
   	  //$sheet->getStyle($cols[1].$row_cnt)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
   	  //$sheet->getStyle($cols[1].$row_cnt)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
   	  //$sheet->getStyle($cols[1].$row_cnt)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
   	  //$sheet->getStyle($cols[1].$row_cnt)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

      //文字Alignment
   	  //$sheet->getStyle("B5:M".$row_cnt)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

      $counter++;
      $row_cnt++;
    }
    return $row_cnt;
   }

   //挙式一覧作成
   function setWeddingSheet($sheet,$data,$row_cnt,$title){

   	$cols = array("","A","B","C","D","E","F","G","H","I","J","K","L","M","N");

    $sheet->setCellValue($cols[1].$row_cnt,$title);
    $row_cnt++;
    $counter = 1;
    for($i=0;$i < count($data);$i++){

     $atr = $data[$i]['ContractTrnView'];

      $sheet->setCellValue($cols[1].$row_cnt,$counter);
      $sheet->setCellValue($cols[2].$row_cnt,$atr['customer_cd']);

      if($atr['grmls_kj'] != "" || $atr['grmfs_kj'] != ""){

         $sheet->setCellValue($cols[3].$row_cnt,$atr['grmls_kj']." ".$atr['grmfs_kj']);

      }else if($atr['grmls_kn'] != "" || $atr['grmfs_kj'] != ""){

         $sheet->setCellValue($cols[3].$row_cnt,$atr['grmls_kn']." ".$atr['grmfs_kn']);

      }else if($atr['brdls_kj'] != "" || $atr['brdfs_kj'] != ""){

        $sheet->setCellValue($cols[3].$row_cnt,$atr['brdls_kj']." ".$atr['brdfs_kj']);

      }else{
         $sheet->setCellValue($cols[3].$row_cnt,$atr['brdls_kn']." ".$atr['brdfs_kn']);
      }

     if($atr['prm_address_flg']==0){
	   	$sheet->setCellValue($cols[4].$row_cnt, $atr['grm_zip_cd']);
	    $sheet->setCellValue($cols[5].$row_cnt, $atr['grm_pref'].$atr['grm_city'].$atr['grm_street'].$atr['grm_apart']);
	   }else{
	   	$sheet->setCellValue($cols[4].$row_cnt, $atr['brd_zip_cd']);
	    $sheet->setCellValue($cols[5].$row_cnt, $atr['brd_pref'].$atr['brd_city'].$atr['brd_street']. $atr['brd_apart']);
	   }

       if($atr['prm_phone_no_flg']==0){
       	 if($atr['grm_phone_no'] != ""){
	   	    $sheet->setCellValue($cols[6].$row_cnt, $atr['grm_phone_no']);
       	 }else{
       	 	$sheet->setCellValue($cols[6].$row_cnt, $atr['grm_cell_no']);
       	 }
	   }else{
	   	 if($atr['brd_phone_no'] != ""){
	   	    $sheet->setCellValue($cols[6].$row_cnt, $atr['brd_phone_no']);
       	 }else{
       	 	$sheet->setCellValue($cols[6].$row_cnt, $atr['brd_cell_no']);
       	 }
	   }

       if($atr['grm_email'] != ""){
	   	    $sheet->setCellValue($cols[7].$row_cnt, $atr['grm_email']);
       }else{
       	 	$sheet->setCellValue($cols[7].$row_cnt, $atr['brd_email']);
       }

      $sheet->setCellValue($cols[8].$row_cnt ,$atr['wedding_dt']);
      $sheet->setCellValue($cols[9].$row_cnt ,$atr['status_nm']);
      $sheet->setCellValue($cols[10].$row_cnt,$atr['first_contact_person_nm']);
      $sheet->setCellValue($cols[11].$row_cnt,$atr['process_person_nm']);
      $sheet->setCellValue($cols[12].$row_cnt,$atr['latest_action']);
      $sheet->setCellValue($cols[13].$row_cnt,$atr['estimate_issued_dt']);
      $sheet->setCellValue($cols[14].$row_cnt,$atr['first_contact_dt']);

      /* 羅線 */
   	  //$sheet->getStyle($cols[1].$row_cnt)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
   	  //$sheet->getStyle($cols[1].$row_cnt)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
   	  //$sheet->getStyle($cols[1].$row_cnt)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
   	  //$sheet->getStyle($cols[1].$row_cnt)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

      //文字Alignment
   	  //$sheet->getStyle("B5:M".$row_cnt)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

      $counter++;
      $row_cnt++;
    }
    return $row_cnt;
   }
   
   //製作対象一覧作成
   function setFutureWeddingSheet($sheet,$data,$row_cnt,$title){

   	$cols = array("","A","B","C","D","E","F","G","H","I","J","K","L","M","N");

    $sheet->setCellValue($cols[1].$row_cnt,$title);
    $row_cnt++;
    $counter = 1;
    $currentDate = "";
    for($i=0;$i < count($data);$i++){

     $atr = $data[$i]['ContractTrnView'];

     if($currentDate == "" || $currentDate != date('Y-m',strtotime($atr["wedding_dt"]))){

         $sheet->setCellValue($cols[1].$row_cnt, "【".date('Y-m',strtotime($atr["wedding_dt"]))."】");

         $currentDate = date('Y-m',strtotime($atr["wedding_dt"]));
         $counter=1;
         $row_cnt++;
      }
      
      $sheet->setCellValue($cols[1].$row_cnt,$counter);
      $sheet->setCellValue($cols[2].$row_cnt,$atr['customer_cd']);

      if($atr['grmls_kj'] != "" || $atr['grmfs_kj'] != ""){

         $sheet->setCellValue($cols[3].$row_cnt,$atr['grmls_kj']." ".$atr['grmfs_kj']);

      }else if($atr['grmls_kn'] != "" || $atr['grmfs_kj'] != ""){

         $sheet->setCellValue($cols[3].$row_cnt,$atr['grmls_kn']." ".$atr['grmfs_kn']);

      }else if($atr['brdls_kj'] != "" || $atr['brdfs_kj'] != ""){

        $sheet->setCellValue($cols[3].$row_cnt,$atr['brdls_kj']." ".$atr['brdfs_kj']);

      }else{
         $sheet->setCellValue($cols[3].$row_cnt,$atr['brdls_kn']." ".$atr['brdfs_kn']);
      }

     if($atr['prm_address_flg']==0){
	   	$sheet->setCellValue($cols[4].$row_cnt, $atr['grm_zip_cd']);
	    $sheet->setCellValue($cols[5].$row_cnt, $atr['grm_pref'].$atr['grm_city'].$atr['grm_street'].$atr['grm_apart']);
	   }else{
	   	$sheet->setCellValue($cols[4].$row_cnt, $atr['brd_zip_cd']);
	    $sheet->setCellValue($cols[5].$row_cnt, $atr['brd_pref'].$atr['brd_city'].$atr['brd_street']. $atr['brd_apart']);
	   }

       if($atr['prm_phone_no_flg']==0){
       	 if($atr['grm_phone_no'] != ""){
	   	    $sheet->setCellValue($cols[6].$row_cnt, $atr['grm_phone_no']);
       	 }else{
       	 	$sheet->setCellValue($cols[6].$row_cnt, $atr['grm_cell_no']);
       	 }
	   }else{
	   	 if($atr['brd_phone_no'] != ""){
	   	    $sheet->setCellValue($cols[6].$row_cnt, $atr['brd_phone_no']);
       	 }else{
       	 	$sheet->setCellValue($cols[6].$row_cnt, $atr['brd_cell_no']);
       	 }
	   }

       if($atr['grm_email'] != ""){
	   	    $sheet->setCellValue($cols[7].$row_cnt, $atr['grm_email']);
       }else{
       	 	$sheet->setCellValue($cols[7].$row_cnt, $atr['brd_email']);
       }

      $sheet->setCellValue($cols[8].$row_cnt ,$atr['wedding_dt']);
      $sheet->setCellValue($cols[9].$row_cnt ,$atr['status_nm']);
      $sheet->setCellValue($cols[10].$row_cnt,$atr['first_contact_person_nm']);
      $sheet->setCellValue($cols[11].$row_cnt,$atr['process_person_nm']);
      $sheet->setCellValue($cols[12].$row_cnt,$atr['latest_action']);
      $sheet->setCellValue($cols[13].$row_cnt,$atr['estimate_issued_dt']);
      $sheet->setCellValue($cols[14].$row_cnt,$atr['first_contact_dt']);

      /* 羅線 */
   	  //$sheet->getStyle($cols[1].$row_cnt)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
   	  //$sheet->getStyle($cols[1].$row_cnt)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
   	  //$sheet->getStyle($cols[1].$row_cnt)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
   	  //$sheet->getStyle($cols[1].$row_cnt)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

      //文字Alignment
   	  //$sheet->getStyle("B5:M".$row_cnt)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

      $counter++;
      $row_cnt++;
    }
    return $row_cnt;
   }
?>