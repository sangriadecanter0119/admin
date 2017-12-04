<?php
//10分
set_time_limit(600);

// Excel出力用ライブラリ
App::import( 'Vendor', 'PHPExcel', array('file'=>'phpexcel' . DS . 'PHPExcel.php') );
//App::import( 'Vendor', 'PHPExcel_IOFactory', array('file'=>'phpexcel' . DS . 'PHPExcel' . DS . 'IOFactory.php') );
//App::import( 'Vendor', 'PHPExcel_Cell_AdvancedValueBinder', array('file'=>'phpexcel' . DS . 'PHPExcel' . DS . 'Cell' . DS . 'AdvancedValueBinder.php') );

// Excel95用ライブラリ
App::import( 'Vendor', 'PHPExcel_Writer_Excel2007', array('file'=>'phpexcel' . DS . 'PHPExcel' . DS . 'Writer' . DS . 'Excel2007.php') );
App::import( 'Vendor', 'PHPExcel_Reader_Excel2007', array('file'=>'phpexcel' . DS . 'PHPExcel' . DS . 'Reader' . DS . 'Excel2007.php') );

// Excel2007用ライブラリ
//App::import( 'Vendor', 'PHPExcel_Writer_Excel2007', array('file'=>'phpexcel' . DS . 'PHPExcel' . DS . 'Writer' . DS . 'Excel2007.php') );
//App::import( 'Vendor', 'PHPExcel_Reader_Excel2007', array('file'=>'phpexcel' . DS . 'PHPExcel' . DS . 'Reader' . DS . 'Excel2007.php') );

    $reader = new PHPExcel_Reader_Excel2007();
    //$reader = PHPExcel_IOFactory::createReader('Excel2007');
    $objPHPExcel = $reader->load(realpath( TMP ).DS . 'excels' . DS . $template_file);

    // Set active sheet index to the first sheet, so Excel opens this as the first sheet
    $objPHPExcel->setActiveSheetIndex( 0 );
    $customer_sheet = $objPHPExcel->getActiveSheet();

    // Rename sheet
    // シート名をつける
    //$sheet->setTitle( $sheet_name );
    // デフォルトのフォント
    $customer_sheet->getDefaultStyle()->getFont()->setName('ＭＳ Ｐゴシック');
    // デフォルトのフォントサイズ
    $customer_sheet->getDefaultStyle()->getFont()->setSize(13);
    // 列名
    $row_cnt = 1;
    $base_row = 1;

    // PHPの日時出力
    //$time = time();                 // 現在日時(Unix Timestamp)
    //$customer_sheet->setCellValue( 'A' . $row_cnt, PHPExcel_Shared_Date::PHPToExcel( $time ) );
    //$customer_sheet->getStyle( 'A' . $row_cnt)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDDSLASH);
    /* ロゴの出力 */
    $objDrawing = new PHPExcel_Worksheet_Drawing();
    $objDrawing->setPath('images/company_logo.png');
    $objDrawing->setHeight(30);
    $objDrawing->setCoordinates('M1');
    $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());

    $row_cnt_cs = 2;

    /* Basic */
    $row_cnt_cs = setBasicData($customer_sheet,$contract,$customer,$row_cnt_cs + 1,$common);

    /* Ceremony */
    $row_cnt_cs = setCeremonyData($customer_sheet,$contract,$row_cnt_cs + 2,$common);

    /* Reception */
    $row_cnt_cs = setReceptionData($customer_sheet,$contract,$row_cnt_cs  + 2,$common);

    /* CPL Personal */
    $row_cnt_cs = setPersonalDataForCustomerSheet($customer_sheet,$customer,$row_cnt_cs + 2,$common);

    /* Travel */
    $row_cnt_cs = setTravelDataForCustomerSheet($customer_sheet, $travel, $row_cnt_cs + 2, $common);

    /* Coordinator */
    if(count($coordinator) > 0){
      duplicateTable($customer_sheet,$coordinator,"CoordinatorMenuTrnView",$row_cnt_cs + 4,3);
      $row_cnt_cs = setCoordinatorDataForCustomerSheet($customer_sheet,$coordinator,$coordinator_time,$row_cnt_cs + 3,$common);
    }else{
      $customer_sheet->removeRow($row_cnt_cs + 2,7);
    }

    /* Ceremony Request */
    if(count($ceremony) > 0){
      $row_cnt_cs = setCeremonyRequestDataForCustomerSheet($customer_sheet,$ceremony,$ceremony_ring,$ceremony_flower,$ceremony_bride_made,$ceremony_groom_made,$ceremony_option,$row_cnt_cs + 2,$common);
    }else{
      $customer_sheet->removeRow($row_cnt_cs + 2,18);
    }

    /* Transportation Cpl */
    if(count($trans_cpl) > 0){
    	 duplicateTable($customer_sheet,$trans_cpl,"TransCplTrnView",$row_cnt_cs + 3,5);
    	 $row_cnt_cs = setTransCplDataForCustomerSheet($customer_sheet, $trans_cpl, $row_cnt_cs + 2, $common);
    }else{
    	$customer_sheet->removeRow($row_cnt_cs + 2,8);
    }

    /* Transportation Guest */
    if(count($trans_gst) > 0){
    	duplicateTable($customer_sheet,$trans_gst,"TransGuestTrnView",$row_cnt_cs + 3,5);
    	$row_cnt_cs = setTransGstDataForCustomerSheet($customer_sheet, $trans_gst, $row_cnt_cs + 2, $common);
    }else{
    	$customer_sheet->removeRow($row_cnt_cs + 2,8);
    }

    /* Hairmake CPL */
    if(count($hairmake_cpl) > 0){
    	duplicateTable($customer_sheet,$hairmake_cpl,"HairmakeCplMenuTrnView",$row_cnt_cs + 4,5);
    	$row_cnt_cs = setHairmakeCplDataForCustomerSheet($customer_sheet,$hairmake_cpl,$hairmake_cpl_time,$row_cnt_cs + 3,$common);
    }else{
    	$customer_sheet->removeRow($row_cnt_cs + 2,9);
    }

    /* Hairmake GUEST */
    if(count($hairmake_gst) > 0){
    	duplicateTable($customer_sheet,$hairmake_gst,"HairmakeGuestTrnView",$row_cnt_cs + 3,3);
    	$row_cnt_cs = setHairmakeGuestDataForCustomerSheet($customer_sheet,$hairmake_gst,$row_cnt_cs + 2,$common);
    }else{
      $customer_sheet->removeRow($row_cnt_cs + 2,6);
    }

    /* Photographer */
    if(count($photographer) > 0){
    	duplicateTable($customer_sheet,$photographer,"PhotographerMenuTrnView",$row_cnt_cs + 3,5);
    	$row_cnt_cs = setPhotographerDataForCustomerSheet($customer_sheet,$photographer,$photographer_time,$row_cnt_cs + 2,$common);
    }else{
       $customer_sheet->removeRow($row_cnt_cs + 2,8);
    }

    /* Album */
    if(count($album) > 0){
    	duplicateTable($customer_sheet,$album,"AlbumTrnView",$row_cnt_cs + 3,1);
    	$row_cnt_cs = setAlbumDataForCustomerSheet($customer_sheet,$album,$row_cnt_cs + 2,$common);
    }else{
        $customer_sheet->removeRow($row_cnt_cs + 2,4);
    }

    /* Videographer */
    if(count($videographer) > 0){
    	duplicateTable($customer_sheet,$videographer,"VideographerMenuTrnView",$row_cnt_cs + 3,5);
    	$row_cnt_cs = setVideographerDataForCustomerSheet($customer_sheet,$videographer,$videographer_time,$row_cnt_cs + 2,$common);
    }else{
       $customer_sheet->removeRow($row_cnt_cs + 2,8);
    }

    /* Flower */
    if(count($flower) > 0){
    	duplicateTable($customer_sheet,$flower,"FlowerTrnView",$row_cnt_cs + 3,11);
    	$row_cnt_cs = setFlowerDataForCustomerSheet($customer_sheet,$flower,$row_cnt_cs + 2,$common);
    }else{
       $customer_sheet->removeRow($row_cnt_cs + 2,14);
    }

     /* Reception */
    if(count($reception) > 0){
    	duplicateTable($customer_sheet,$reception,"ReceptionTrnView",$row_cnt_cs + 3,7);
    	$row_cnt_cs = setReceptionRequestDataForCustomerSheet($customer_sheet,$reception,$row_cnt_cs + 2,$common);
    }else{
       $customer_sheet->removeRow($row_cnt_cs + 2,10);
    }

     /* Reception Transportatioin */
    if(count($trans_recep) > 0){
    	duplicateTable($customer_sheet,$trans_recep,"TransRecepTrnView",$row_cnt_cs + 3,5);
    	$row_cnt_cs = setRecepTransDataForCustomerSheet($customer_sheet,$trans_recep,$row_cnt_cs + 2,$common);
    }else{
        $customer_sheet->removeRow($row_cnt_cs + 2,8);
    }

     /* Cake */
    if(count($cake) > 0){
    	duplicateTable($customer_sheet,$cake,"CakeTrnView",$row_cnt_cs + 3,3);
    	$row_cnt_cs = setCakeDataForCustomerSheet($customer_sheet,$cake,$row_cnt_cs + 2,$common);
    }else{
        $customer_sheet->removeRow($row_cnt_cs + 2,6);
    }

    /* Entertainment */
    if(count($entertainment) > 0){
    	duplicateTable($customer_sheet,$entertainment,"EntertainmentTrnView",$row_cnt + 3,1);
    	$row_cnt_cs = setEntertainmentDataForCustomerSheet($customer_sheet,$entertainment,$row_cnt_cs + 2,$common);
    }else{
        $customer_sheet->removeRow($row_cnt_cs + 2,4);
    }

    /* Av */
    if(count($av) > 0){
    	duplicateTable($customer_sheet,$av,"AvTrnView",$row_cnt_cs + 3,1);
    	$row_cnt_cs = setAvDataForCustomerSheet($customer_sheet,$av,$row_cnt_cs + 2,$common);
    }else{
        $customer_sheet->removeRow($row_cnt_cs + 2,4);
    }

    /* Linen */
    if(count($linen) > 0){
    	duplicateTable($customer_sheet,$linen,"LinenTrnView",$row_cnt_cs + 3,1);
    	$row_cnt_cs = setLinenDataForCustomerSheet($customer_sheet,$linen,$row_cnt_cs + 2,$common);
    }else{
        $customer_sheet->removeRow($row_cnt_cs + 2,4);
    }

    /* Wedding Item */
    if(count($paper) > 0){
    	duplicateTable($customer_sheet,$paper,"PaperTrnView",$row_cnt_cs + 3,1);
    	$row_cnt_cs = setPaperDataForCustomerSheet($customer_sheet,$paper,$row_cnt_cs + 2,$common);
    }else{
        $customer_sheet->removeRow($row_cnt_cs + 2,4);
    }

    /* Mc */
    if(count($mc) > 0){
    	duplicateTable($customer_sheet,$mc,"McTrnView",$row_cnt_cs + 3,1);
    	$row_cnt_cs = setMcDataForCustomerSheet($customer_sheet,$mc,$row_cnt_cs + 2,$common);
    }else{
        $customer_sheet->removeRow($row_cnt_cs + 2,4);
    }

    /* Minister */
    if(count($minister) > 0){
    	duplicateTable($customer_sheet,$minister,"MinisterTrnView",$row_cnt_cs + 3,1);
    	$row_cnt_cs = setMinisterDataForCustomerSheet($customer_sheet,$minister,$row_cnt_cs + 2,$common);
    }else{
        $customer_sheet->removeRow($row_cnt_cs + 2,4);
    }

    /* Party Option */
    if(count($party_option) > 0){
    	duplicateTable($customer_sheet,$party_option,"PartyOptionTrnView",$row_cnt_cs + 3,1);
    	$row_cnt_cs = setPartyOptionDataForCustomerSheet($customer_sheet,$party_option,$row_cnt_cs + 2,$common);
    }else{
        $customer_sheet->removeRow($row_cnt_cs + 2,4);
    }

     /* House Wedding */
    if(count($house_wedding) > 0){
    	duplicateTable($customer_sheet,$house_wedding,"HouseWeddingTrnView",$row_cnt_cs + 3,1);
    	$row_cnt_cs = setHouseWeddingDataForCustomerSheet($customer_sheet,$house_wedding,$row_cnt_cs + 2,$common);
    }else{
        $customer_sheet->removeRow($row_cnt_cs + 2,4);
    }

// Excelファイルの保存
// 保存ファイルフルパス
$uploadDir = realpath( TMP );
$uploadDir .= DS . 'excels' . DS;
$path = $uploadDir . $filename;

$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
//$objWriter = PHPExcel_IOFactory::createWriter( $objPHPExcel, 'Excel2007' );  //Excel2007用 ファイル名は.xlsxとする
$objWriter->save( $path );

// Excelファイルをクライアントに出力
Configure::write('debug', 0);       // debugコードを非表示
header("Content-disposition: attachment; filename={$filename}");
//header("Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; name={$filename}");
header('Content-Type: application/vnd.ms-excel');
header('Cache-Control: max-age=0');
$result = file_get_contents( $path );   // ダウンロードするデータの取得
print( $result );                       // 出力

/**
 *
 * Basicデータの作成
 * @param $sheet
 * @param $contract
 * @param $row_cnt
 * @param $common
 */
function setBasicData($sheet,$contract,$customer,$row_cnt,$common){

	$sheet->setCellValue( 'L' . $row_cnt, $contract['ContractTrnView']['grmls_kj']." ".$contract['ContractTrnView']['grmfs_kj']."様");
    $sheet->setCellValue( 'V' . $row_cnt, $contract['ContractTrnView']['brdls_kj']." ".$contract['ContractTrnView']['brdfs_kj']."様");
    $row_cnt++;
    $sheet->setCellValue( 'I'  . $row_cnt, "Mr. ".$contract['ContractTrnView']['grmfs_rm']." ".$contract['ContractTrnView']['grmls_rm']);
    $sheet->setCellValue( 'S'  . $row_cnt, "Ms. ".$contract['ContractTrnView']['brdfs_rm']." ".$contract['ContractTrnView']['brdls_rm']);
    $sheet->setCellValue( 'A'  . $row_cnt, $common->evalForShortDate($contract['ContractTrnView']['wedding_dt']));
    $sheet->setCellValue( 'AC' . $row_cnt, $customer['CustomerMstView']['grm_cell_no']);

    /* 曜日の抽出 */
    $wedding_dt = $common->evalForShortDate($contract['ContractTrnView']['wedding_dt']);
    $arr = split("/",$wedding_dt);
    if(count($arr) == 3){
        $sheet->setCellValue( 'F' . $row_cnt, date("l", mktime(0, 0, 0, $arr[1], $arr[2], $arr[0])));
    }
	return $row_cnt;
}

/**
 *
 * Ceremonyデータの作成
 * @param $sheet
 * @param $contract
 * @param $row_cnt
 * @param $common
 */
function setCeremonyData($sheet,$contract,$row_cnt,$common){

	//$sheet->setCellValue( 'F'. $row_cnt,"phone number");
	$row_cnt += 2;
    $sheet->setCellValue( 'A'  . $row_cnt,$contract['ContractTrnView']['wedding_time']);
    $sheet->setCellValue( 'D'  . $row_cnt,$contract['ContractTrnView']['wedding_place']);
    $sheet->setCellValue( 'X'  . $row_cnt,$contract['ContractTrnView']['wedding_bg']);
    $sheet->setCellValue( 'Z'  . $row_cnt,$contract['ContractTrnView']['wedding_ad']);
    $sheet->setCellValue( 'AB' . $row_cnt,$contract['ContractTrnView']['wedding_ch']);
    $sheet->setCellValue( 'AD' . $row_cnt,$contract['ContractTrnView']['wedding_inf']);
    $sheet->setCellValue( 'AF' . $row_cnt,$contract['ContractTrnView']['wedding_gst_total']);
    return $row_cnt;
}

/**
 *
 * Receptionデータの作成
 * @param $sheet
 * @param $contract
 * @param $row_cnt
 * @param $common
 */
function setReceptionData($sheet,$contract,$row_cnt,$common){

	//$sheet->setCellValue( 'F'. $row_cnt,"phone number");
	$row_cnt += 2;
	$sheet->setCellValue( 'A'  . $row_cnt,$contract['ContractTrnView']['reception_time']);
    $sheet->setCellValue( 'D'  . $row_cnt,$contract['ContractTrnView']['reception_place']);
    $sheet->setCellValue( 'X'  . $row_cnt,$contract['ContractTrnView']['reception_bg']);
    $sheet->setCellValue( 'Z'  . $row_cnt,$contract['ContractTrnView']['reception_ad']);
    $sheet->setCellValue( 'AB' . $row_cnt,$contract['ContractTrnView']['reception_ch']);
    $sheet->setCellValue( 'AD' . $row_cnt,$contract['ContractTrnView']['reception_inf']);
    $sheet->setCellValue( 'AF' . $row_cnt,$contract['ContractTrnView']['reception_gst_total']);
    return $row_cnt;
}

/**
 *
 * Personalデータの作成
 * @param $sheet
 * @param $customer
 * @param $row_cnt
 * @param $common
 */
function setPersonalDataForCustomerSheet($sheet,$customer,$row_cnt,$common){

	$sheet->mergeCells('A'. $row_cnt.':G'.$row_cnt);

	$row_cnt += 2;
	/* メインに選択されている方の住所を表示 */
    if(GROOM == $customer['CustomerMstView']['prm_address_flg']){
       	 $sheet->setCellValue( 'A' . $row_cnt,$customer['CustomerMstView']['grm_address_rm']);
    }else{
    	 $sheet->setCellValue( 'A' . $row_cnt,$customer['CustomerMstView']['brd_address_rm']);
    }
    $sheet->setCellValue( 'U' . $row_cnt,$common->evalForShortDate($customer['CustomerMstView']['grmbirth_dt']));
    $sheet->setCellValue( 'Y' . $row_cnt,$common->evalForShortDate($customer['CustomerMstView']['brdbirth_dt']));
    //$sheet->setCellValue( 'AC' . $row_cnt,);
    $row_cnt += 2;
    $sheet->setCellValue( 'A'  . $row_cnt,$customer['CustomerMstView']['grm_email']);
    $sheet->setCellValue( 'L'  . $row_cnt,$customer['CustomerMstView']['brd_email']);
    $sheet->setCellValue( 'W'  . $row_cnt,$customer['CustomerMstView']['grm_cell_no']);
    $sheet->setCellValue( 'AC' . $row_cnt,$customer['CustomerMstView']['brd_cell_no']);

	return $row_cnt;
}

/**
 *
 * Travelデータの作成
 * @param $sheet
 * @param $travel
 * @param $row_cnt
 * @param $common
 */
function setTravelDataForCustomerSheet($sheet,$travel,$row_cnt,$common){

    $row_cnt += 2;
	$sheet->setCellValue( 'A' . $row_cnt,$common->evalForShortDate($travel[0]['TravelTrnView']['arrival_dt']));
    $sheet->setCellValue( 'E' . $row_cnt,$travel[0]['TravelTrnView']['arrival_time']);
    $sheet->setCellValue( 'H' . $row_cnt,$travel[0]['TravelTrnView']['arrival_flight_no']);
    $sheet->setCellValue( 'K' . $row_cnt,$common->evalForShortDate($travel[0]['TravelTrnView']['departure_dt']));
    $sheet->setCellValue( 'O' . $row_cnt,$travel[0]['TravelTrnView']['departure_time']);
    $sheet->setCellValue( 'R' . $row_cnt,$travel[0]['TravelTrnView']['departure_flight_no']);
    $sheet->setCellValue( 'U' . $row_cnt,$travel[0]['TravelTrnView']['wedding_day_hotel']);
    $sheet->mergeCells('AE'. $row_cnt.':AF'.$row_cnt);
    $sheet->setCellValue( 'AE' . $row_cnt,$travel[0]['TravelTrnView']['wedding_day_room_no']);
    $row_cnt += 2;

    for($time_index=0;$time_index < count($travel);$time_index++){

       $sheet->setCellValue( 'A' . $row_cnt,$travel[$time_index]['TravelTrnView']['no']);
       $sheet->setCellValue( 'B' . $row_cnt,$travel[$time_index]['TravelTrnView']['hotel_nm']);
       $sheet->setCellValue( 'O' . $row_cnt,$common->evalForShortDate($travel[$time_index]['TravelTrnView']['checkin_dt']));
       $sheet->setCellValue( 'S' . $row_cnt,$common->evalForShortDate($travel[$time_index]['TravelTrnView']['checkout_dt']));
       $sheet->setCellValue( 'W' . $row_cnt,$travel[$time_index]['TravelTrnView']['travel_dtl_note']);

       $row_cnt++;

       /* 次にデータが存在すれば行を追加する */
       if($time_index + 1 < count($travel)){
         $sheet->insertNewRowBefore($row_cnt, 1);
       }
    }
    return $row_cnt-1;
}

/**
 *
 * Coordinatorデータの作成
 * @param $sheet
 * @param $coordinator
 * @param $coordinator_time
 * @param $row_cnt
 * @param $common
 */
function setCoordinatorDataForCustomerSheet($sheet,$coordinator,$coordinator_time,$row_cnt,$common){

	$vendor_list = null;
	$head_id = -1;
  	$head_counter = 0;

    for($head_index=0;$head_index < count($coordinator);$head_index++){

        /* ヘッダテーブル作成 */
     	if($head_id != $coordinator[$head_index]['CoordinatorMenuTrnView']['id']){
    	   $head_id  = $coordinator[$head_index]['CoordinatorMenuTrnView']['id'];

    	   /* メインテーブル作成  */
    	   if($coordinator[$head_index]['CoordinatorMenuTrnView']['main_attend_kbn'] == CC_MAIN){
               $sheet->setCellValue( 'Q'  . $row_cnt,$common->evalForShortDate($coordinator[$head_index]['CoordinatorMenuTrnView']['briefing_dt']));
    	       $sheet->setCellValue( 'U'  . $row_cnt,$coordinator[$head_index]['CoordinatorMenuTrnView']['briefing_start_time']);
    	       $sheet->setCellValue( 'X'  . $row_cnt,$coordinator[$head_index]['CoordinatorMenuTrnView']['briefing_place']);
    	     //  $sheet->setCellValue( 'AD' . $row_cnt,$coordinator[$head_index]['CoordinatorMenuTrnView']['briefing_place']);
           }

           $row_cnt += 2;

           /* メニューテーブル作成 */
           $menu_id = -1;
           $menu_counter = 0;
           for($menu_index=0;$menu_index < count($coordinator);$menu_index++){

             //サブテーブルの外部キーとヘッダの主キーが同値
    	     if($head_id == $coordinator[$menu_index]['CoordinatorMenuTrnView']['id']){
    	       	if($menu_id != $coordinator[$menu_index]['CoordinatorMenuTrnView']['coordinator_menu_id']){
    	           $menu_id  = $coordinator[$menu_index]['CoordinatorMenuTrnView']['coordinator_menu_id'];

    	           //初回以外なら行追加
    	           if($menu_counter != 0){
    	           	 $sheet->insertNewRowBefore($row_cnt, 1);
    	           }
    	       	   $sheet->setCellValue( 'A' . $row_cnt,$menu_counter+1);
    	       	   $sheet->setCellValue( 'B' . $row_cnt,$coordinator[$menu_index]['CoordinatorMenuTrnView']['menu']);
    	       	   $sheet->setCellValue( 'I' . $row_cnt,$coordinator[$menu_index]['CoordinatorMenuTrnView']['coordinator_menu_note']);
    	       	   $row_cnt++;

    	           $menu_counter++;
    	       	}
    	     }
       }

       /* 時間テーブル作成 */
       $time_id = -1;
       $time_counter = 0;
       $row_cnt++;

       for($time_index=0;$time_index < count($coordinator_time);$time_index++){

           //サブテーブルの外部キーとヘッダの主キーが同値
    	   if($head_id == $coordinator_time[$time_index]['CoordinatorTimeTrnView']['id']){
    	    	if($time_id != $coordinator_time[$time_index]['CoordinatorTimeTrnView']['coordinator_time_id']){
    	           $time_id  = $coordinator_time[$time_index]['CoordinatorTimeTrnView']['coordinator_time_id'];

    	    	   //初回以外なら行追加
    	           if($time_counter != 0){
    	           	  $sheet->insertNewRowBefore($row_cnt, 1);
    	           }

    	           $sheet->setCellValue( 'A' . $row_cnt,$coordinator_time[$time_index]['CoordinatorTimeTrnView']['no']);
    	       	   $sheet->setCellValue( 'B' . $row_cnt,$coordinator_time[$time_index]['CoordinatorTimeTrnView']['start_time']);
    	       	   $sheet->setCellValue( 'E' . $row_cnt,$coordinator_time[$time_index]['CoordinatorTimeTrnView']['start_place']);
    	       	   $sheet->setCellValue( 'K' . $row_cnt,$coordinator_time[$time_index]['CoordinatorTimeTrnView']['end_time']);
    	       	   $sheet->setCellValue( 'N' . $row_cnt,$coordinator_time[$time_index]['CoordinatorTimeTrnView']['end_place']);
    	       	  // $sheet->setCellValue( 'T' . $row_cnt,$coordinator_time[$time_index]['CoordinatorTimeTrnView']['coordinator_menu_note']);
    	       	   $sheet->setCellValue( 'W' . $row_cnt,$coordinator_time[$time_index]['CoordinatorTimeTrnView']['coordinator_time_note']);
    	       	   $row_cnt++;

    	           $time_counter++;
                }
           }
       }
     $row_cnt +=1;
     $head_counter++;
    }
  }
  return  $row_cnt - 2;
}



/**
 *
 * CeremonyRequestデータの作成
 * @param unknown_type $sheet
 * @param unknown_type $ceremony
 * @param unknown_type $ceremony_ring
 * @param unknown_type $ceremony_flower
 * @param unknown_type $ceremony_bride_made
 * @param unknown_type $ceremony_groom_made
 * @param unknown_type $row_cnt
 * @param unknown_type $common
 */
function setCeremonyRequestDataForCustomerSheet($sheet,$ceremony,$ceremony_ring,$ceremony_flower,$ceremony_bride_made,$ceremony_groom_made,$ceremony_option,$row_cnt,$common){

	$sheet->mergeCells('A'. $row_cnt.':G'.$row_cnt);

	$row_cnt += 2;
	$sheet->setCellValue( 'A'  . $row_cnt, $ceremony[0]['CeremonyTrn']['menu']);
    $sheet->setCellValue( 'S'  . $row_cnt, $ceremony[0]['CeremonyTrn']['ring_pollow']);
    $sheet->setCellValue( 'W'  . $row_cnt, $ceremony[0]['CeremonyTrn']['bouquet_toss_kbn']==0 ? "No":"Yes");
    $sheet->setCellValue( 'AA' . $row_cnt,$ceremony[0]['CeremonyTrn']['flower_shower_kbn']==0 ? "No":"Yes");
    $sheet->setCellValue( 'AE' . $row_cnt,$ceremony[0]['CeremonyTrn']['bubble_shower_kbn'] == 0 ? "No":"Yes");

    $row_cnt +=2;

    $sheet->setCellValue( 'A' . $row_cnt, $ceremony[0]['CeremonyTrn']['bride_escorted']);
    $sheet->setCellValue( 'F' . $row_cnt, $ceremony[0]['CeremonyTrn']['toasting_speech_nm']);
    $sheet->setCellValue( 'N' . $row_cnt, $ceremony[0]['CeremonyTrn']['lei_ceremony_place']);
    $sheet->setCellValue( 'U' . $row_cnt, $ceremony[0]['CeremonyTrn']['lei_ceremony_count']);
    $sheet->setCellValue( 'W' . $row_cnt, $ceremony[0]['CeremonyTrn']['rehearsal']);

    $row_cnt +=2;

    $sheet->setCellValue( 'A' . $row_cnt, $ceremony[0]['CeremonyTrn']['legal_wedding_kbn'] == 0 ? "No":"Yes");
    $sheet->setCellValue( 'C' . $row_cnt, $common->evalForShortDate($ceremony[0]['CeremonyTrn']['procedure_dt']));
    $sheet->setCellValue( 'H' . $row_cnt, $ceremony[0]['CeremonyTrn']['procedure_nm']);
    $sheet->setCellValue( 'P' . $row_cnt, $ceremony[0]['CeremonyTrn']['note']);
    $sheet->setCellValue( 'AF' . $row_cnt,"");

    $row_cnt +=2;
    if(count($ceremony_ring)==0){
    	  $sheet->setCellValue( 'F' . $row_cnt,'-');
    	  $row_cnt++;
    }else{
      for($i=0;$i < count($ceremony_ring);$i++){

          $sheet->setCellValue( 'F' . $row_cnt,$ceremony_ring[$i]['CeremonyRingTrn']['no']);
          $sheet->setCellValue( 'G' . $row_cnt,$ceremony_ring[$i]['CeremonyRingTrn']['ring_bg_nm']);
          $sheet->setCellValue( 'T' . $row_cnt,$ceremony_ring[$i]['CeremonyRingTrn']['age'] == 0 ? '' : $ceremony_ring[$i]['CeremonyRingTrn']['age']);
          $sheet->setCellValue( 'W' . $row_cnt,$ceremony_ring[$i]['CeremonyRingTrn']['note']);

          $row_cnt++;

          /* 次にデータが存在すれば行を追加する */
          if($i + 1 < count($ceremony_ring)){
            $sheet->insertNewRowBefore($row_cnt, 1);
          }
      }
    }

    $row_cnt++;
    if(count($ceremony_flower)==0){
    	  $sheet->setCellValue( 'F' . $row_cnt,'-');
    	  $row_cnt++;
    }else{
      for($i=0;$i < count($ceremony_flower);$i++){

          $sheet->setCellValue( 'F' . $row_cnt,$ceremony_flower[$i]['CeremonyFlowerTrn']['no']);
          $sheet->setCellValue( 'G' . $row_cnt,$ceremony_flower[$i]['CeremonyFlowerTrn']['flower_bg_nm']);
          $sheet->setCellValue( 'T' . $row_cnt,$ceremony_flower[$i]['CeremonyFlowerTrn']['age'] == 0 ? '' : $ceremony_flower[$i]['CeremonyFlowerTrn']['age']);
          $sheet->setCellValue( 'W' . $row_cnt,$ceremony_flower[$i]['CeremonyFlowerTrn']['note']);

          $row_cnt++;

          /* 次にデータが存在すれば行を追加する */
          if($i + 1 < count($ceremony_flower)){
            $sheet->insertNewRowBefore($row_cnt, 1);
          }
      }
    }

    $row_cnt++;
    if(count($ceremony_bride_made)==0){
    	  $sheet->setCellValue( 'F' . $row_cnt,'-');
    	  $row_cnt++;
    }else{
      for($i=0;$i < count($ceremony_bride_made);$i++){

          $sheet->setCellValue( 'F' . $row_cnt,$ceremony_bride_made[$i]['CeremonyBrideMadeTrn']['no']);
          $sheet->setCellValue( 'G' . $row_cnt,$ceremony_bride_made[$i]['CeremonyBrideMadeTrn']['bride_made_nm']);
          $sheet->setCellValue( 'T' . $row_cnt,$ceremony_bride_made[$i]['CeremonyBrideMadeTrn']['count']);
          $sheet->setCellValue( 'W' . $row_cnt,$ceremony_bride_made[$i]['CeremonyBrideMadeTrn']['note']);

          $row_cnt++;

          /* 次にデータが存在すれば行を追加する */
          if($i + 1 < count($ceremony_bride_made)){
            $sheet->insertNewRowBefore($row_cnt, 1);
          }
      }
    }

    $row_cnt++;
    if(count($ceremony_groom_made)==0){
    	  $sheet->setCellValue( 'F' . $row_cnt,'-');
    	  $row_cnt++;
    }else{
      for($i=0;$i < count($ceremony_groom_made);$i++){

          $sheet->setCellValue( 'F' . $row_cnt,$ceremony_groom_made[$i]['CeremonyGroomMadeTrn']['no']);
          $sheet->setCellValue( 'G' . $row_cnt,$ceremony_groom_made[$i]['CeremonyGroomMadeTrn']['groom_made_nm']);
          $sheet->setCellValue( 'T' . $row_cnt,$ceremony_groom_made[$i]['CeremonyGroomMadeTrn']['count']);
          $sheet->setCellValue( 'W' . $row_cnt,$ceremony_groom_made[$i]['CeremonyGroomMadeTrn']['note']);

          $row_cnt++;

          /* 次にデータが存在すれば行を追加する */
          if($i + 1 < count($ceremony_groom_made)){
            $sheet->insertNewRowBefore($row_cnt, 1);
          }
      }
    }

    if(count($ceremony_option) > 0){
       $row_cnt++;
       for($i=0;$i < count($ceremony_option);$i++){
       	   /* 初回以外のみ行追加 */
       	   if($i != 0){
       	   	 $sheet->insertNewRowBefore($row_cnt, 1);
       	   }
       	   $sheet->setCellValue( 'F' . $row_cnt,$ceremony_option[$i]['CeremonyOptionTrnView']['ceremony_option_no']);
           $sheet->setCellValue( 'G' . $row_cnt,$ceremony_option[$i]['CeremonyOptionTrnView']['ceremony_option_nm']);
           $sheet->setCellValue( 'T' . $row_cnt,$ceremony_option[$i]['CeremonyOptionTrnView']['ceremony_option_count']);
           $sheet->setCellValue( 'W' . $row_cnt,$ceremony_option[$i]['CeremonyOptionTrnView']['ceremony_option_note']);
           $row_cnt++;
       }
    }else{
    	  $sheet->removeRow($row_cnt,2);
    }
	return $row_cnt-1;
}



/**
 *
 * Transportation CPLデータの作成
 * @param unknown_type $sheet
 * @param unknown_type $trans_cpl
 * @param unknown_type $row_cnt
 * @param unknown_type $common
 */
function setTransCplDataForCustomerSheet($sheet,$trans_cpl,$row_cnt,$common){

	 if(count($trans_cpl) > 0)
	 {
	 	 $start_row = $row_cnt+1;
      	 $head_id = -1;
       	 $head_counter = 0;
       	 for($head_index=0;$head_index < count($trans_cpl);$head_index++){

       	    /* メインテーブル作成 */
       	 	if($head_id != $trans_cpl[$head_index]['TransCplTrnView']['id']){
      		   $head_id  = $trans_cpl[$head_index]['TransCplTrnView']['id'];

      		      $sheet->mergeCells('A'. $row_cnt.':G'.$row_cnt);

                  $row_cnt += 2;
                  /* サブテーブル作成 */
                  $sub_id = -1;
                  $sub_counter = 0;

                  for($sub_index=0;$sub_index < count($trans_cpl);$sub_index++){
                       //サブテーブルの外部キーとヘッダの主キーが同値
    	               if($head_id == $trans_cpl[$sub_index]['TransCplTrnView']['id']){
    	     	            if($sub_id != $trans_cpl[$sub_index]['TransCplTrnView']['trans_cpl_sub_id']){
    	       	               $sub_id  = $trans_cpl[$sub_index]['TransCplTrnView']['trans_cpl_sub_id'];

    	     	               /* 初回以外行追加 */
                      	       if($sub_counter != 0){
                      	      	 duplicateAnyTable($sheet, $start_row,5, $row_cnt+1);
                      	      	 $row_cnt += 2;
                      	       }

    	       	               $sheet->setCellValue( 'A' . $row_cnt,$trans_cpl[$sub_index]['TransCplTrnView']['menu']);
    	       	               $sheet->setCellValue( 'J' . $row_cnt,$trans_cpl[$sub_index]['TransCplTrnView']['vihicular_type']);
    	       	               $sheet->setCellValue( 'P' . $row_cnt,$trans_cpl[$sub_index]['TransCplTrnView']['working_start_time']);
    	       	               $sheet->setCellValue( 'S' . $row_cnt,$trans_cpl[$sub_index]['TransCplTrnView']['dep_place']);
    	       	               $sheet->setCellValue( 'X' . $row_cnt,$trans_cpl[$sub_index]['TransCplTrnView']['working_end_time']);
    	       	               $sheet->setCellValue( 'AA' . $row_cnt,$trans_cpl[$sub_index]['TransCplTrnView']['final_dest']);
    	       	               $sheet->setCellValue( 'AF'. $row_cnt,getFormattedWorkingTotalTime($trans_cpl[$sub_index]['TransCplTrnView']['working_total']));

    	    			       $row_cnt += 2;

    	    			         $sheet->setCellValue( 'A' . $row_cnt,$trans_cpl[$sub_index]['TransCplTrnView']['passenger_bg']);
    	    			         $sheet->setCellValue( 'C' . $row_cnt,$trans_cpl[$sub_index]['TransCplTrnView']['passenger_guest']);
    	    			         $sheet->setCellValue( 'E' . $row_cnt,$trans_cpl[$sub_index]['TransCplTrnView']['passenger_ph']);
    	    			         $sheet->setCellValue( 'G' . $row_cnt,$trans_cpl[$sub_index]['TransCplTrnView']['passenger_vh']);
    	    			         $sheet->setCellValue( 'I' . $row_cnt,$trans_cpl[$sub_index]['TransCplTrnView']['passenger_hm']);
    	    			         $sheet->setCellValue( 'K' . $row_cnt,$trans_cpl[$sub_index]['TransCplTrnView']['passenger_att']);
    	    			         $sheet->setCellValue( 'M' . $row_cnt,$trans_cpl[$sub_index]['TransCplTrnView']['total_passenger']);
                                 $sheet->setCellValue( 'P' . $row_cnt,$trans_cpl[$sub_index]['TransCplTrnView']['trans_cpl_sub_note']);

                               $row_cnt += 2;

    	        $dtl_id = -1;
                $dtl_counter =0;
           		for($dtl_index=0;$dtl_index < count($trans_cpl);$dtl_index++){
    	      		//詳細テーブルの外部キーとサブテーブルの主キーが同値
           	  	    if($sub_id == $trans_cpl[$dtl_index]['TransCplTrnView']['trans_cpl_sub_id']){
                 	  	 if($dtl_id != $trans_cpl[$dtl_index]['TransCplTrnView']['trans_cpl_dtl_id']){
                      	    $dtl_id  = $trans_cpl[$dtl_index]['TransCplTrnView']['trans_cpl_dtl_id'];
    	                       /* 初回以外行追加 */
                      	      if($dtl_counter != 0){
                      	      	$sheet->insertNewRowBefore($row_cnt, 1);
                      	      }
    	    		               $sheet->setCellValue( 'A' . $row_cnt,$trans_cpl[$dtl_index]['TransCplTrnView']['no']);
    	    		               $sheet->setCellValue( 'B' . $row_cnt,$trans_cpl[$dtl_index]['TransCplTrnView']['departure_time']);
    	    		               $sheet->setCellValue( 'F' . $row_cnt,$trans_cpl[$dtl_index]['TransCplTrnView']['departure_place']);
    	    		               $sheet->setCellValue( 'T' . $row_cnt,$trans_cpl[$dtl_index]['TransCplTrnView']['arrival_time']);
    	    		               $sheet->setCellValue( 'X' . $row_cnt,$trans_cpl[$dtl_index]['TransCplTrnView']['arrival_place']);

    	    		           $row_cnt++;
    	                       $dtl_counter++;
    	    	          }
           	            }  //サブIDと一致する詳細テーブル外部キー判定のIF文の締め
    	           }   //詳細テーブルのデータ数だけLOOPするFOR文の締め
    	       $sub_counter++;
                        }
   	                   }    //ヘッダIDと一致するサブテーブル外部キー判定のIF文の締め
                     }  //サブテーブルのデータ数だけLOOPするFOR文の締め
                 $row_cnt++;
                 $head_counter++;
                 } //一意のヘッダID判定のIF文の締め
              }    //trans_viewのデータ数だけLOOPするFOR文の締め
            }  //transデータ存在チェックIF文の締め
	return $row_cnt - 2;
}



/**
 *
 * Transportation Guestデータの作成
 * @param unknown_type $sheet
 * @param unknown_type $trans_gst
 * @param unknown_type $row_cnt
 * @param unknown_type $common
 */
function setTransGstDataForCustomerSheet($sheet,$trans_gst,$row_cnt,$common){

	 if(count($trans_gst) > 0)
	 {
	 	 $start_row = $row_cnt+1;
	 	 $start_sub_row = $row_cnt+4;
      	 $head_id = -1;
       	 $head_counter = 0;
       	 for($head_index=0;$head_index < count($trans_gst);$head_index++){

       	    /* メインテーブル作成 */
       	 	if($head_id != $trans_gst[$head_index]['TransGuestTrnView']['id']){
      		   $head_id  = $trans_gst[$head_index]['TransGuestTrnView']['id'];

      		      $sheet->mergeCells('A'. $row_cnt.':G'.$row_cnt);

                  $row_cnt += 2;
                  /* サブテーブル作成 */
                  $sub_id = -1;
                  $sub_counter = 0;

                  for($sub_index=0;$sub_index < count($trans_gst);$sub_index++){
                       //サブテーブルの外部キーとヘッダの主キーが同値
    	               if($head_id == $trans_gst[$sub_index]['TransGuestTrnView']['id']){
    	     	            if($sub_id != $trans_gst[$sub_index]['TransGuestTrnView']['trans_guest_sub_id']){
    	       	               $sub_id  = $trans_gst[$sub_index]['TransGuestTrnView']['trans_guest_sub_id'];

    	     	               /* 初回以外行追加 */
                      	       if($sub_counter != 0){
                      	      	 duplicateAnyTable($sheet, $start_row,5, $row_cnt+1);
                      	      	 $row_cnt += 2;
                      	       }
    	       	               $sheet->setCellValue( 'A' . $row_cnt,$trans_gst[$sub_index]['TransGuestTrnView']['menu']);
    	       	               $sheet->setCellValue( 'V' . $row_cnt,$trans_gst[$sub_index]['TransGuestTrnView']['vihicular_type']);
    	       	               $sheet->setCellValue( 'AB' . $row_cnt,$trans_gst[$sub_index]['TransGuestTrnView']['working_start_time']."～".$trans_gst[$sub_index]['TransGuestTrnView']['working_end_time']);
    	       	               $sheet->setCellValue( 'AF'. $row_cnt,getFormattedWorkingTotalTime($trans_gst[$sub_index]['TransGuestTrnView']['working_total']));

                               $row_cnt += 2;
    	        $dtl_id = -1;
                $dtl_counter =0;
           		for($dtl_index=0;$dtl_index < count($trans_gst);$dtl_index++){
    	      		//詳細テーブルの外部キーとサブテーブルの主キーが同値
           	  	    if($sub_id == $trans_gst[$dtl_index]['TransGuestTrnView']['trans_guest_sub_id']){
                 	  	 if($dtl_id != $trans_gst[$dtl_index]['TransGuestTrnView']['trans_guest_dtl_id']){
                      	    $dtl_id  = $trans_gst[$dtl_index]['TransGuestTrnView']['trans_guest_dtl_id'];
    	                       /* 初回以外行追加 */
                      	      if($dtl_counter != 0){
                      	      	duplicateAnyTable($sheet, $start_sub_row,2, $row_cnt,"EVEN","LOW");
                      	      	$sheet->removeRow($row_cnt + 3,1);
                      	      	$sheet->getRowDimension($row_cnt + 3)->setRowHeight("14.25");
                      	      }
    	    		               $sheet->setCellValue( 'A' . $row_cnt,$trans_gst[$dtl_index]['TransGuestTrnView']['no']);
    	    		               $sheet->setCellValue( 'B' . $row_cnt,$trans_gst[$dtl_index]['TransGuestTrnView']['departure_time']);
    	    		               $sheet->setCellValue( 'E' . $row_cnt,$trans_gst[$dtl_index]['TransGuestTrnView']['departure_place']);
    	    		               $sheet->setCellValue( 'L' . $row_cnt,$trans_gst[$dtl_index]['TransGuestTrnView']['total_departure_passenger']);
    	    		               $sheet->setCellValue( 'O' . $row_cnt,$trans_gst[$dtl_index]['TransGuestTrnView']['representative_nm']);
    	    		               $sheet->setCellValue( 'V' . $row_cnt,$trans_gst[$dtl_index]['TransGuestTrnView']['arrival_time']);
    	    		               $sheet->setCellValue( 'Y' . $row_cnt,$trans_gst[$dtl_index]['TransGuestTrnView']['arrival_place']);
    	    		               $sheet->setCellValue( 'AF'. $row_cnt,$trans_gst[$dtl_index]['TransGuestTrnView']['total_arrival_passenger']);
    	    		           $row_cnt+=2;
    	    		               $sheet->setCellValue( 'B' . $row_cnt,$trans_gst[$dtl_index]['TransGuestTrnView']['trans_guest_dtl_note']);
    	    		           $row_cnt++;
    	                       $dtl_counter++;
    	    	          }
           	            }  //サブIDと一致する詳細テーブル外部キー判定のIF文の締め
    	           }   //詳細テーブルのデータ数だけLOOPするFOR文の締め
    	       $sub_counter++;
                        }
   	                   }    //ヘッダIDと一致するサブテーブル外部キー判定のIF文の締め
                     }  //サブテーブルのデータ数だけLOOPするFOR文の締め
                 $row_cnt++;
                 $head_counter++;
                 } //一意のヘッダID判定のIF文の締め
              }    //trans_viewのデータ数だけLOOPするFOR文の締め
            }  //transデータ存在チェックIF文の締め
	return $row_cnt - 2;

}


/**
 *
 * HairmakeCplデータの作成
 * @param $sheet
 * @param $hairmake_cpl
 * @param $row_cnt
 */
function setHairmakeCplDataForCustomerSheet($sheet,$hairmake_cpl,$hairmake_cpl_time,$row_cnt,$common){

        $head_id = -1;
        $head_counter = 0;

    	for($head_index=0;$head_index < count($hairmake_cpl);$head_index++){
    	  /* ヘッダテーブル作成 */
       	  if($head_id != $hairmake_cpl[$head_index]['HairmakeCplMenuTrnView']['id']){
             $head_id  = $hairmake_cpl[$head_index]['HairmakeCplMenuTrnView']['id'];

            $sheet->mergeCells('A'. $row_cnt.':G'.$row_cnt);

    	    /* メイン  */
            if($hairmake_cpl[$head_index]['HairmakeCplMenuTrnView']['main_attend_kbn'] == HC_MAIN){
    	      $sheet->setCellValue( 'Q' . $row_cnt,$common->evalForShortDate($hairmake_cpl[$head_index]['HairmakeCplMenuTrnView']['rehasal_dt']));
    	      $sheet->setCellValue( 'U' . $row_cnt,$hairmake_cpl[$head_index]['HairmakeCplMenuTrnView']['rehasal_start_time']);
    	      $sheet->setCellValue( 'X' . $row_cnt,$hairmake_cpl[$head_index]['HairmakeCplMenuTrnView']['rehasal_place']);
            }

            $row_cnt += 2;
            /* メニューテーブル作成 */
            $menu_id = -1;
            $menu_counter = 0;
            for($menu_index=0;$menu_index < count($hairmake_cpl);$menu_index++){

              //サブテーブルの外部キーとヘッダの主キーが同値
    	      if($head_id == $hairmake_cpl[$menu_index]['HairmakeCplMenuTrnView']['id']){
    	    	if($menu_id != $hairmake_cpl[$menu_index]['HairmakeCplMenuTrnView']['hairmake_cpl_menu_id']){
    	           $menu_id  = $hairmake_cpl[$menu_index]['HairmakeCplMenuTrnView']['hairmake_cpl_menu_id'];

    	           /* ２行目以降なら行を追加する */
    	           if($menu_counter != 0){
    	           	 $row_cnt++;
    	           	 $sheet->insertNewRowBefore($row_cnt, 1);
    	           }
    	    	   $sheet->setCellValue( 'A' . $row_cnt ,$menu_counter+1);
    	           $sheet->setCellValue( 'B' . $row_cnt ,$hairmake_cpl[$menu_index]['HairmakeCplMenuTrnView']['menu']);
    	           $menu_counter++;
    	        }
    	      }
            }
            $row_cnt += 2;
            $sheet->setCellValue( 'A' . $row_cnt ,$hairmake_cpl[$head_index]['HairmakeCplMenuTrnView']['working_start_time']);
    	    $sheet->setCellValue( 'D' . $row_cnt ,$hairmake_cpl[$head_index]['HairmakeCplMenuTrnView']['working_start_place']);
    	    $sheet->setCellValue( 'P' . $row_cnt ,$hairmake_cpl[$head_index]['HairmakeCplMenuTrnView']['working_end_time']);
    	    $sheet->setCellValue( 'S' . $row_cnt ,$hairmake_cpl[$head_index]['HairmakeCplMenuTrnView']['working_end_place']);
    	    $sheet->setCellValue( 'AF'. $row_cnt,getFormattedWorkingTotalTime($hairmake_cpl[$head_index]['HairmakeCplMenuTrnView']['working_total']));

           /* 時間テーブル作成 */
           $row_cnt += 2;
           $time_id = -1;
           $time_counter = 0;
           for($time_index=0;$time_index < count($hairmake_cpl_time);$time_index++){

              //サブテーブルの外部キーとヘッダの主キーが同値
    	      if($head_id == $hairmake_cpl_time[$time_index]['HairmakeCplTimeTrnView']['id']){
    	          if($time_id != $hairmake_cpl_time[$time_index]['HairmakeCplTimeTrnView']['hairmake_cpl_time_id']){
    	       	     $time_id  = $hairmake_cpl_time[$time_index]['HairmakeCplTimeTrnView']['hairmake_cpl_time_id'];

    	            /* ２行目以降なら行を追加する */
    	             if($time_counter != 0){
    	           	   $row_cnt++;
    	           	   $sheet->insertNewRowBefore($row_cnt, 1);
    	             }
    	       	     $sheet->setCellValue( 'A' . $row_cnt ,$hairmake_cpl_time[$time_index]['HairmakeCplTimeTrnView']['hairmake_cpl_time_no']);
    	             $sheet->setCellValue( 'B' . $row_cnt ,$hairmake_cpl_time[$time_index]['HairmakeCplTimeTrnView']['make_start_time']);
    	             $sheet->setCellValue( 'E' . $row_cnt ,$hairmake_cpl_time[$time_index]['HairmakeCplTimeTrnView']['make_start_place']);
    	             $sheet->setCellValue( 'M' . $row_cnt ,$hairmake_cpl_time[$time_index]['HairmakeCplTimeTrnView']['hairmake_cpl_time_note']);

    	       	     $time_counter++;
    	          }
    	     }
           }
           $row_cnt += 2;
           $head_counter++;
       	  }
        }
	return $row_cnt - 2;
}


/**
 *
 * HairmakeGuestデータの作成
 * @param unknown_type $sheet
 * @param unknown_type $hairmake_gst
 * @param unknown_type $row_cnt
 * @param unknown_type $common
 */
function setHairmakeGuestDataForCustomerSheet($sheet,$hairmake_gst,$row_cnt,$common){

	 $start_row = $row_cnt + 1;
	 $head_id = -1;
     $head_counter = 0;
     for($head_index=0;$head_index < count($hairmake_gst);$head_index++){

         /* メインテーブル作成 */
       	if($head_id != $hairmake_gst[$head_index]['HairmakeGuestTrnView']['id']){
           $head_id  = $hairmake_gst[$head_index]['HairmakeGuestTrnView']['id'];

             $sheet->mergeCells('A'. $row_cnt.':G'.$row_cnt);

    	     $row_cnt +=2;
             $sub_id = -1;
             $sub_counter = 0;

             for($sub_index=0;$sub_index < count($hairmake_gst);$sub_index++){
                //サブテーブルの外部キーとヘッダの主キーが同値
    	         if($head_id == $hairmake_gst[$sub_index]['HairmakeGuestTrnView']['id']){
    	     	     if($sub_id != $hairmake_gst[$sub_index]['HairmakeGuestTrnView']['hairmake_guest_sub_id']){
    	       	        $sub_id  = $hairmake_gst[$sub_index]['HairmakeGuestTrnView']['hairmake_guest_sub_id'];

    	     	        /* ２行目以降なら行を追加する */
    	                if($sub_counter != 0){
    	           	        $row_cnt +=2;
    	           	        duplicateAnyTable($sheet, $start_row, 3, $row_cnt);
    	           	        $row_cnt ++;
    	                }
    	       	        $sheet->setCellValue( 'A' . $row_cnt,$sub_counter+1);
    	       	        $sheet->setCellValue( 'B' . $row_cnt,$hairmake_gst[$sub_index]['HairmakeGuestTrnView']['menu']);

    	       			$row_cnt +=2;
    	        		$dtl_id = -1;
                		$dtl_counter =0;
           				for($dtl_index=0;$dtl_index < count($hairmake_gst);$dtl_index++){
    	      				//詳細テーブルの外部キーとサブテーブルの主キーが同値
           	  	    		if($sub_id == $hairmake_gst[$dtl_index]['HairmakeGuestTrnView']['hairmake_guest_sub_id']){
                 	  	 		if($dtl_id != $hairmake_gst[$dtl_index]['HairmakeGuestTrnView']['hairmake_guest_dtl_id']){
                      	    		$dtl_id  = $hairmake_gst[$dtl_index]['HairmakeGuestTrnView']['hairmake_guest_dtl_id'];

                 	  	 		    /* ２行目以降なら行を追加する */
    	                            if($dtl_counter != 0){
    	           	                  $row_cnt++;
    	           	                  $sheet->insertNewRowBefore($row_cnt, 1);
    	                            }
                      	     		$sheet->setCellValue( 'A' . $row_cnt,$hairmake_gst[$dtl_index]['HairmakeGuestTrnView']['make_start_time']);
                      	     		$sheet->setCellValue( 'D' . $row_cnt,$hairmake_gst[$dtl_index]['HairmakeGuestTrnView']['make_end_time']);
                      	     		$sheet->setCellValue( 'G' . $row_cnt,$hairmake_gst[$dtl_index]['HairmakeGuestTrnView']['make_start_place']);
                      	     		$sheet->setCellValue( 'M' . $row_cnt,$hairmake_gst[$dtl_index]['HairmakeGuestTrnView']['guest_nm']);
                      	     		$sheet->setCellValue( 'U' . $row_cnt,$hairmake_gst[$dtl_index]['HairmakeGuestTrnView']['attend_nm']);
                      	     		$sheet->setCellValue( 'AA'. $row_cnt,$hairmake_gst[$dtl_index]['HairmakeGuestTrnView']['hairmake_guest_dtl_note']);

    	                     		$dtl_counter++;
    	    	        		 }
           	        		}  //サブIDと一致する詳細テーブル外部キー判定のIF文の締め
    	       			}   //詳細テーブルのデータ数だけLOOPするFOR文の締め
    	       		$sub_counter++;
                   }
   	            }    //ヘッダIDと一致するサブテーブル外部キー判定のIF文の締め
           }  //サブテーブルのデータ数だけLOOPするFOR文の締め
             $row_cnt += 2;
             $head_counter++;
       } //一意のヘッダID判定のIF文の締め
     }
    return $row_cnt - 2;
}


/**
 *
 * Photographerデータの作成
 * @param $sheet
 * @param $hairmake_cpl
 * @param $row_cnt
 */
function setPhotographerDataForCustomerSheet($sheet,$photographer,$photographer_time,$row_cnt,$common){

	$head_id = -1;
   	$head_counter = 0;
   	for($head_index=0;$head_index < count($photographer);$head_index++){

       if($head_id != $photographer[$head_index]['PhotographerMenuTrnView']['id']){
          $head_id  = $photographer[$head_index]['PhotographerMenuTrnView']['id'];

              $sheet->mergeCells('A'. $row_cnt.':G'.$row_cnt);
              $row_cnt += 2;

              $sheet->setCellValue( 'A'  . $row_cnt,$photographer[$head_index]['PhotographerMenuTrnView']['working_start_time']."～".$photographer[$head_index]['PhotographerMenuTrnView']['working_end_time']);
    	      $sheet->setCellValue( 'F'  . $row_cnt,getFormattedWorkingTotalTime($photographer[$head_index]['PhotographerMenuTrnView']['working_total_time']));
    	      $sheet->setCellValue( 'I'  . $row_cnt,$photographer[$head_index]['PhotographerMenuTrnView']['first_meeting_place']);
    	      $sheet->setCellValue( 'Q'  . $row_cnt,$common->evalForShortDate($photographer[$head_index]['PhotographerMenuTrnView']['delivery_dt'])."  ".$photographer[$head_index]['PhotographerMenuTrnView']['delivery_time']);
    	      $sheet->setCellValue( 'U'  . $row_cnt,$photographer[$head_index]['PhotographerMenuTrnView']['delivery_place']);
         	  $sheet->setCellValue( 'AC' . $row_cnt,$photographer[$head_index]['PhotographerMenuTrnView']['reciever_nm']);
         	  $row_cnt += 2;

    	     /* メニューテーブル作成 */
         	 $menu_id = -1;
         	 $menu_counter = 0;
         	 for($menu_index=0;$menu_index < count($photographer);$menu_index++){

               //サブテーブルの外部キーとヘッダの主キーが同値
    	       if($head_id == $photographer[$menu_index]['PhotographerMenuTrnView']['id']){
    	          	if($menu_id != $photographer[$menu_index]['PhotographerMenuTrnView']['photographer_menu_id']){
    	               $menu_id  = $photographer[$menu_index]['PhotographerMenuTrnView']['photographer_menu_id'];

    	          	   /* ２行目以降なら行を追加する */
    	               if($menu_counter != 0){
    	           	      $row_cnt++;
    	           	      $sheet->insertNewRowBefore($row_cnt, 1);
    	               }
    	       	            $sheet->setCellValue( 'A' . $row_cnt,$menu_counter + 1);
    	       	            $sheet->setCellValue( 'B' . $row_cnt,$photographer[$menu_index]['PhotographerMenuTrnView']['menu']);
    	       	            $sheet->setCellValue( 'Q' . $row_cnt,$photographer[$menu_index]['PhotographerMenuTrnView']['photographer_menu_note']);
    	       	            $menu_counter++;
    	            }
    	       }
             }

            /* 時間テーブル作成 */
            $time_id = -1;
            $time_counter = 0;
            $row_cnt += 2;
            for($time_index=0;$time_index < count($photographer_time);$time_index++){

               //サブテーブルの外部キーとヘッダの主キーが同値
    	       if($head_id == $photographer_time[$time_index]['PhotographerTimeTrnView']['id']){
    	          	if($time_id != $photographer_time[$time_index]['PhotographerTimeTrnView']['photographer_time_id']){
    	    	       $time_id  = $photographer_time[$time_index]['PhotographerTimeTrnView']['photographer_time_id'];

    	          	   /* ２行目以降なら行を追加する */
    	               if($time_counter != 0){
    	           	      $row_cnt++;
    	           	      $sheet->insertNewRowBefore($row_cnt, 1);
    	               }
    	               $sheet->setCellValue( 'A' . $row_cnt,$photographer_time[$time_index]['PhotographerTimeTrnView']['photographer_time_no']);
    	               $sheet->setCellValue( 'B' . $row_cnt,$photographer_time[$time_index]['PhotographerTimeTrnView']['shooting_time']);
    	               $sheet->setCellValue( 'F' . $row_cnt,$photographer_time[$time_index]['PhotographerTimeTrnView']['shooting_place']);
    	               $sheet->setCellValue( 'Q' . $row_cnt,$photographer_time[$time_index]['PhotographerTimeTrnView']['photographer_time_note']);
    	               $time_counter++;
                    }
               }
            }
       $row_cnt += 2;
       $head_counter++;
      }
    }
	return $row_cnt - 2;
}



/**
 *
 * Albumデータの作成
 * @param unknown_type $sheet
 * @param unknown_type $album
 * @param unknown_type $row_cnt
 * @param unknown_type $common
 */
function setAlbumDataForCustomerSheet($sheet,$album,$row_cnt,$common){

    $head_id = -1;
    $head_counter = 0;
    for($head_index=0;$head_index < count($album);$head_index++){

    	 if($head_id != $album[$head_index]['AlbumTrnView']['id']){
    	 	$head_id  = $album[$head_index]['AlbumTrnView']['id'];

    	 	$sheet->mergeCells('A'. $row_cnt.':G'.$row_cnt);
            $row_cnt += 2;

           $dtl_id = -1;
           $dtl_counter =0;
    	   for($dtl_index=0;$dtl_index < count($album);$dtl_index++){
     		   //ヘッダテーブルの外部キーとサブテーブルの主キーが同値
     	       if($head_id == $album[$dtl_index]['AlbumTrnView']['id']){
              	  	 if($dtl_id != $album[$dtl_index]['AlbumTrnView']['album_dtl_id']){
                   	    $dtl_id  = $album[$dtl_index]['AlbumTrnView']['album_dtl_id'];

              	  	  /* ２行目以降なら行を追加する */
    	               if($dtl_counter != 0){
    	           	      $row_cnt++;
    	           	      $sheet->insertNewRowBefore($row_cnt, 1);
    	               }
    	               $sheet->setCellValue( 'A' . $row_cnt,$album[$dtl_index]['AlbumTrnView']['type']);
                       $sheet->setCellValue( 'M' . $row_cnt,$album[$dtl_index]['AlbumTrnView']['delivery_term']);
                       $sheet->setCellValue( 'Q' . $row_cnt,$album[$dtl_index]['AlbumTrnView']['album_note']);
        		       $dtl_counter++;
               	  	 }
       	       }
     	    }
            $row_cnt += 2;
     	    $head_counter++;
   		}
   	}
   	return $row_cnt - 2;
}



/**
 *
 * Photographerデータの作成
 * @param $sheet
 * @param $hairmake_cpl
 * @param $row_cnt
 */
function setVideographerDataForCustomerSheet($sheet,$videographer,$videographer_time,$row_cnt,$common){

	$head_id = -1;
   	$head_counter = 0;
   	for($head_index=0;$head_index < count($videographer);$head_index++){

       if($head_id != $videographer[$head_index]['VideographerMenuTrnView']['id']){
          $head_id  = $videographer[$head_index]['VideographerMenuTrnView']['id'];

              $sheet->mergeCells('A'. $row_cnt.':G'.$row_cnt);
              $row_cnt += 2;

              $sheet->setCellValue( 'A'  . $row_cnt,$videographer[$head_index]['VideographerMenuTrnView']['working_start_time']."～".$videographer[$head_index]['VideographerMenuTrnView']['working_end_time']);
    	      $sheet->setCellValue( 'F'  . $row_cnt,getFormattedWorkingTotalTime($videographer[$head_index]['VideographerMenuTrnView']['working_total_time']));
    	      $sheet->setCellValue( 'I'  . $row_cnt,$videographer[$head_index]['VideographerMenuTrnView']['first_meeting_place']);
    	      $sheet->setCellValue( 'Q'  . $row_cnt,$common->evalForShortDate($videographer[$head_index]['VideographerMenuTrnView']['delivery_dt'])."  ".$videographer[$head_index]['VideographerMenuTrnView']['delivery_time']);
    	      $sheet->setCellValue( 'U'  . $row_cnt,$videographer[$head_index]['VideographerMenuTrnView']['delivery_place']);
         	  $sheet->setCellValue( 'AC' . $row_cnt,$videographer[$head_index]['VideographerMenuTrnView']['reciever_nm']);
         	  $row_cnt += 2;

    	     /* メニューテーブル作成 */
         	 $menu_id = -1;
         	 $menu_counter = 0;
         	 for($menu_index=0;$menu_index < count($videographer);$menu_index++){

               //サブテーブルの外部キーとヘッダの主キーが同値
    	       if($head_id == $videographer[$menu_index]['VideographerMenuTrnView']['id']){
    	          	if($menu_id != $videographer[$menu_index]['VideographerMenuTrnView']['videographer_menu_id']){
    	               $menu_id  = $videographer[$menu_index]['VideographerMenuTrnView']['videographer_menu_id'];

    	          	   /* ２行目以降なら行を追加する */
    	               if($menu_counter != 0){
    	           	      $row_cnt++;
    	           	      $sheet->insertNewRowBefore($row_cnt, 1);
    	               }
    	       	            $sheet->setCellValue( 'A' . $row_cnt,$menu_counter + 1);
    	       	            $sheet->setCellValue( 'B' . $row_cnt,$videographer[$menu_index]['VideographerMenuTrnView']['menu']);
    	       	            $sheet->setCellValue( 'Q' . $row_cnt,$videographer[$menu_index]['VideographerMenuTrnView']['videographer_menu_note']);
    	       	            $menu_counter++;
    	            }
    	       }
             }

            /* 時間テーブル作成 */
            $time_id = -1;
            $time_counter = 0;
            $row_cnt += 2;
            for($time_index=0;$time_index < count($videographer_time);$time_index++){

               //サブテーブルの外部キーとヘッダの主キーが同値
    	       if($head_id == $videographer_time[$time_index]['VideographerTimeTrnView']['id']){
    	          	if($time_id != $videographer_time[$time_index]['VideographerTimeTrnView']['videographer_time_id']){
    	    	       $time_id  = $videographer_time[$time_index]['VideographerTimeTrnView']['videographer_time_id'];

    	          	   /* ２行目以降なら行を追加する */
    	               if($time_counter != 0){
    	           	      $row_cnt++;
    	           	      $sheet->insertNewRowBefore($row_cnt, 1);
    	               }
    	               $sheet->setCellValue( 'A' . $row_cnt,$videographer_time[$time_index]['VideographerTimeTrnView']['videographer_time_no']);
    	               $sheet->setCellValue( 'B' . $row_cnt,$videographer_time[$time_index]['VideographerTimeTrnView']['shooting_time']);
    	               $sheet->setCellValue( 'F' . $row_cnt,$videographer_time[$time_index]['VideographerTimeTrnView']['shooting_place']);
    	               $sheet->setCellValue( 'Q' . $row_cnt,$videographer_time[$time_index]['VideographerTimeTrnView']['videographer_time_note']);
    	               $time_counter++;
                    }
               }
            }
       $row_cnt += 2;
       $head_counter++;
      }
    }
	return $row_cnt - 2;
}



/**
 *
 * Flowerデータの作成
 * @param $sheet
 * @param $flower
 * @param $row_cnt
 * @param $common
 */
function setFlowerDataForCustomerSheet($sheet,$flower,$row_cnt,$common){

   $head_id = -1;
   $head_counter = 0;

   for($head_index=0;$head_index < count($flower);$head_index++){

       /* ヘッダ作成 */
       if($head_id != $flower[$head_index]['FlowerTrnView']['id']){
          $head_id  = $flower[$head_index]['FlowerTrnView']['id'];

          $sheet->mergeCells('A'. $row_cnt.':G'.$row_cnt);
          $row_cnt++;

         //Florist Main
         $sub_id = -1;
         $sub_counter = 0;

         for($sub_index=0;$sub_index < count($flower);$sub_index++){

            //サブテーブルの外部キーとヘッダの主キーが同値
            if($head_id == $flower[$sub_index]['FlowerTrnView']['id']){
               //フラワー区分がMAIN
               if($sub_id != $flower[$sub_index]['FlowerTrnView']['flower_dtl_id'] && $flower[$sub_index]['FlowerTrnView']['flower_kbn'] == FC_MAIN){
    	          $sub_id  = $flower[$sub_index]['FlowerTrnView']['flower_dtl_id'];

    	          //初回のみヘッダ出力
    	          if($sub_counter == 0){
    	          	$row_cnt++;
    	     	    $sheet->setCellValue( 'A' . $row_cnt,$flower[$head_index]['FlowerTrnView']['main_florist_nm']);
    	     	    $sheet->setCellValue( 'I' . $row_cnt,$flower[$head_index]['FlowerTrnView']['main_delivery_term']);
    	     	    $sheet->setCellValue( 'M' . $row_cnt,$flower[$head_index]['FlowerTrnView']['main_delivery_place']);
    	     	    $row_cnt += 2;
    	          }
                  /* ２行目以降なら行を追加する */
    	          if($sub_counter != 0){
    	               $row_cnt++;
    	               $sheet->insertNewRowBefore($row_cnt, 1);
    	          }
                  $sheet->setCellValue( 'A' . $row_cnt,$flower[$sub_index]['FlowerTrnView']['flower_content'].$flower[$sub_index]['FlowerTrnView']['flower_type']);
    	     	 // $sheet->setCellValue( 'I' . $row_cnt,$flower[$sub_index]['FlowerTrnView']['flower_type']);
    	     	  $sheet->setCellValue( 'U' . $row_cnt,$flower[$sub_index]['FlowerTrnView']['num']);
    	     	  $sheet->setCellValue( 'X' . $row_cnt,$flower[$sub_index]['FlowerTrnView']['flower_dtl_note']);
    	          $sub_counter++;
               }
           }
         }

        if($sub_counter == 0){
        	$sheet->removeRow($row_cnt  ,4);
        }else{
        	 $row_cnt++;
        }

        //Florist Ceremony
        $sub_id = -1;
        $sub_counter = 0;

        for($sub_index=0;$sub_index < count($flower);$sub_index++){

           //サブテーブルの外部キーとヘッダの主キーが同値
    	   if($head_id == $flower[$sub_index]['FlowerTrnView']['id']){
    	     //フラワー区分がCEREMONY
    	     if($sub_id != $flower[$sub_index]['FlowerTrnView']['flower_dtl_id'] && $flower[$sub_index]['FlowerTrnView']['flower_kbn'] == FC_CEREMONY){
   	            $sub_id  = $flower[$sub_index]['FlowerTrnView']['flower_dtl_id'];

    	   	     //初回のみヘッダ出力
    	          if($sub_counter == 0){
    	          	$row_cnt++;
    	     	    $sheet->setCellValue( 'A' . $row_cnt,$flower[$head_index]['FlowerTrnView']['ceremony_florist_nm']);
    	     	    $sheet->setCellValue( 'I' . $row_cnt,$flower[$head_index]['FlowerTrnView']['ceremony_delivery_term']);
    	     	    $sheet->setCellValue( 'M' . $row_cnt,$flower[$head_index]['FlowerTrnView']['ceremony_delivery_place']);
    	     	    $row_cnt += 2;
    	          }
                  /* ２行目以降なら行を追加する */
    	          if($sub_counter != 0){
    	               $row_cnt++;
    	               $sheet->insertNewRowBefore($row_cnt, 1);
    	          }
                  $sheet->setCellValue( 'A' . $row_cnt,$flower[$sub_index]['FlowerTrnView']['flower_content'].$flower[$sub_index]['FlowerTrnView']['flower_type']);
    	     	 // $sheet->setCellValue( 'I' . $row_cnt,$flower[$sub_index]['FlowerTrnView']['flower_type']);
    	     	  $sheet->setCellValue( 'U' . $row_cnt,$flower[$sub_index]['FlowerTrnView']['num']);
    	     	  $sheet->setCellValue( 'X' . $row_cnt,$flower[$sub_index]['FlowerTrnView']['flower_dtl_note']);
    	          $sub_counter++;
            }
          }
        }

        if($sub_counter == 0){
        	$sheet->removeRow($row_cnt ,4);
        }else{
        	 $row_cnt++;
        }

        //Florist Reception
        $sub_id = -1;
        $sub_counter = 0;

        for($sub_index=0;$sub_index < count($flower);$sub_index++){

           //サブテーブルの外部キーとヘッダの主キーが同値
    	   if($head_id == $flower[$sub_index]['FlowerTrnView']['id']){
    	      //フラワー区分がRECEPTION
    	      if($sub_id != $flower[$sub_index]['FlowerTrnView']['flower_dtl_id'] && $flower[$sub_index]['FlowerTrnView']['flower_kbn'] == FC_RECEPTION){
    	         $sub_id  = $flower[$sub_index]['FlowerTrnView']['flower_dtl_id'];

    	         //初回のみヘッダ出力
    	          if($sub_counter == 0){
    	          	$row_cnt++;
    	     	    $sheet->setCellValue( 'A' . $row_cnt,$flower[$head_index]['FlowerTrnView']['reception_florist_nm']);
    	     	    $sheet->setCellValue( 'I' . $row_cnt,$flower[$head_index]['FlowerTrnView']['reception_delivery_term']);
    	     	    $sheet->setCellValue( 'M' . $row_cnt,$flower[$head_index]['FlowerTrnView']['reception_delivery_place']);
    	     	    $row_cnt += 2;
    	          }
                  /* ２行目以降なら行を追加する */
    	          if($sub_counter != 0){
    	               $row_cnt++;
    	               $sheet->insertNewRowBefore($row_cnt, 1);
    	          }
                  $sheet->setCellValue( 'A' . $row_cnt,$flower[$sub_index]['FlowerTrnView']['flower_content'].$flower[$sub_index]['FlowerTrnView']['flower_type']);
    	     	  //$sheet->setCellValue( 'I' . $row_cnt,$flower[$sub_index]['FlowerTrnView']['flower_type']);
    	     	  $sheet->setCellValue( 'U' . $row_cnt,$flower[$sub_index]['FlowerTrnView']['num']);
    	     	  $sheet->setCellValue( 'X' . $row_cnt,$flower[$sub_index]['FlowerTrnView']['flower_dtl_note']);
    	          $sub_counter++;
             }
           }
        }
        if($sub_counter == 0){
        	$sheet->removeRow($row_cnt ,4);
        	$row_cnt++;
        }else{
        	 $row_cnt += 2;
        }
      $head_counter++;
   	}
  }
  return $row_cnt - 2;
}


/**
 *
 * ReceptionRequestデータの作成
 * @param $sheet
 * @param $reception
 * @param $row_cnt
 * @param $common
 */
function setReceptionRequestDataForCustomerSheet($sheet,$reception,$row_cnt,$common){

    $head_id = -1;
    $head_counter = 0;

    for($head_index=0;$head_index < count($reception);$head_index++){

        /* メインテーブル作成 */
    	if($head_id != $reception[$head_index]['ReceptionTrnView']['id']){
           $head_id  = $reception[$head_index]['ReceptionTrnView']['id'];

           $sheet->mergeCells('A'. $row_cnt.':G'.$row_cnt);
           $row_cnt += 2;

           $sheet->mergeCells('A'. $row_cnt.':H'.$row_cnt);
    	   $sheet->mergeCells('I'. $row_cnt.':P'.$row_cnt);
    	   $sheet->mergeCells('Q'. $row_cnt.':X'.$row_cnt);
    	   $sheet->mergeCells('Y'. $row_cnt.':AH'.$row_cnt);

    	   $sheet->setCellValue( 'A' . $row_cnt,$reception[$head_index]['ReceptionTrnView']['cpl_trans_dep_place']);
           $sheet->setCellValue( 'I' . $row_cnt,$reception[$head_index]['ReceptionTrnView']['cpl_trans_arrival_place']);
           $sheet->setCellValue( 'Q' . $row_cnt,$reception[$head_index]['ReceptionTrnView']['guest_trans_dep_place']);
           $sheet->setCellValue( 'Y' . $row_cnt,$reception[$head_index]['ReceptionTrnView']['guest_trans_arrival_place']);
           $row_cnt += 2;

           $sheet->mergeCells('A'. $row_cnt.':F'.$row_cnt);
           $sheet->mergeCells('G'. $row_cnt.':L'.$row_cnt);
           $sheet->mergeCells('M'. $row_cnt.':O'.$row_cnt);
           $sheet->mergeCells('P'. $row_cnt.':R'.$row_cnt);
           $sheet->mergeCells('S'. $row_cnt.':V'.$row_cnt);
           $sheet->mergeCells('W'. $row_cnt.':Z'.$row_cnt);
           $sheet->mergeCells('AA'. $row_cnt.':AD'.$row_cnt);
           $sheet->mergeCells('AE'. $row_cnt.':AH'.$row_cnt);

           $sheet->setCellValue( 'A' . $row_cnt,$reception[$head_index]['ReceptionTrnView']['decoration_staff_nm']);
           $sheet->setCellValue( 'G' . $row_cnt,$reception[$head_index]['ReceptionTrnView']['mc_nm']);
           $sheet->setCellValue( 'M' . $row_cnt,$reception[$head_index]['ReceptionTrnView']['party_program_kbn'] == 0 ? "NO" : "YES");
           $sheet->setCellValue( 'P' . $row_cnt,$reception[$head_index]['ReceptionTrnView']['bouquet_toss_kbn'] == 0 ? "NO" : "YES");
           $sheet->setCellValue( 'S' . $row_cnt,$reception[$head_index]['ReceptionTrnView']['theme_color']);
           $sheet->setCellValue( 'W' . $row_cnt,$reception[$head_index]['ReceptionTrnView']['bar_type']);
           $sheet->setCellValue( 'AA' . $row_cnt,$reception[$head_index]['ReceptionTrnView']['champagne_payment']);
           $sheet->setCellValue( 'AE' . $row_cnt,$reception[$head_index]['ReceptionTrnView']['menu_payment']);

           $row_cnt += 2;
           $sheet->mergeCells('A'. $row_cnt.':F'.$row_cnt);
           $sheet->mergeCells('G'. $row_cnt.':I'.$row_cnt);
           $sheet->mergeCells('J'. $row_cnt.':L'.$row_cnt);
           $sheet->mergeCells('M'. $row_cnt.':O'.$row_cnt);
           $sheet->mergeCells('P'. $row_cnt.':R'.$row_cnt);
           $sheet->mergeCells('S'. $row_cnt.':U'.$row_cnt);
           $sheet->mergeCells('V'. $row_cnt.':AH'.$row_cnt);

           $sheet->setCellValue( 'A' . $row_cnt,$reception[$head_index]['ReceptionTrnView']['table_layout']);
           $sheet->setCellValue( 'G' . $row_cnt,$reception[$head_index]['ReceptionTrnView']['high_chair']);
           $sheet->setCellValue( 'J' . $row_cnt,$reception[$head_index]['ReceptionTrnView']['seating_order_kbn'] == 0 ? "NO" : "YES");
           $sheet->setCellValue( 'M' . $row_cnt,$reception[$head_index]['ReceptionTrnView']['name_card_kbn'] == 0 ? "NO" : "YES");
           $sheet->setCellValue( 'P' . $row_cnt,$reception[$head_index]['ReceptionTrnView']['menu_card_kbn'] == 0 ? "NO" : "YES");
           $sheet->setCellValue( 'S' . $row_cnt,$reception[$head_index]['ReceptionTrnView']['favor']);
           $sheet->setCellValue( 'V' . $row_cnt,$reception[$head_index]['ReceptionTrnView']['allergie']);

           $row_cnt += 2;
        /* サブテーブル作成 */
        $sub_id = -1;
        $sub_counter = 0;

        for($sub_index=0;$sub_index < count($reception);$sub_index++){

           //サブテーブルの外部キーとヘッダの主キーが同値
    	   if($head_id == $reception[$sub_index]['ReceptionTrnView']['id']){
    	     	if($sub_id != $reception[$sub_index]['ReceptionTrnView']['reception_menu_id']){
    	           $sub_id  = $reception[$sub_index]['ReceptionTrnView']['reception_menu_id'];

                   /* ２行目以降なら行を追加する */
    	           if($sub_counter != 0){
    	              $row_cnt++;
    	              $sheet->insertNewRowBefore($row_cnt, 1);
    	          }
    	          $sheet->mergeCells('A'. $row_cnt.':R'.$row_cnt);
    	          $sheet->mergeCells('S'. $row_cnt.':U'.$row_cnt);
    	          $sheet->mergeCells('V'. $row_cnt.':AH'.$row_cnt);

    	          $sheet->setCellValue( 'A' . $row_cnt,$reception[$sub_index]['ReceptionTrnView']['menu']);
    	          $sheet->setCellValue( 'S' . $row_cnt,$reception[$sub_index]['ReceptionTrnView']['num']);
    	          $sheet->setCellValue( 'V' . $row_cnt,$reception[$sub_index]['ReceptionTrnView']['reception_menu_note']);
                  $sub_counter++;
                }
           }
        }
       $row_cnt += 2;
       $head_counter++;
      }
   }
   return $row_cnt - 2;
}

/**
 *
 * Reception Transportationデータの作成
 * @param unknown_type $sheet
 * @param unknown_type $trans_recep
 * @param unknown_type $row_cnt
 * @param unknown_type $common
 */
function setRecepTransDataForCustomerSheet($sheet,$trans_recep,$row_cnt,$common){

	 if(count($trans_recep) > 0)
	 {
	 	 $start_row = $row_cnt+1;
	 	 $start_sub_row = $row_cnt+4;
      	 $head_id = -1;
       	 $head_counter = 0;
       	 for($head_index=0;$head_index < count($trans_recep);$head_index++){

       	    /* メインテーブル作成 */
       	 	if($head_id != $trans_recep[$head_index]['TransRecepTrnView']['id']){
      		   $head_id  = $trans_recep[$head_index]['TransRecepTrnView']['id'];

      		      $sheet->mergeCells('A'. $row_cnt.':G'.$row_cnt);
                  $row_cnt += 2;
                  /* サブテーブル作成 */
                  $sub_id = -1;
                  $sub_counter = 0;

                  for($sub_index=0;$sub_index < count($trans_recep);$sub_index++){
                       //サブテーブルの外部キーとヘッダの主キーが同値
    	               if($head_id == $trans_recep[$sub_index]['TransRecepTrnView']['id']){
    	     	            if($sub_id != $trans_recep[$sub_index]['TransRecepTrnView']['trans_recep_sub_id']){
    	       	               $sub_id  = $trans_recep[$sub_index]['TransRecepTrnView']['trans_recep_sub_id'];

    	     	               /* 初回以外行追加 */
                      	       if($sub_counter != 0){
                      	      	 duplicateAnyTable($sheet, $start_row,5, $row_cnt+1);
                      	      	 $row_cnt += 2;
                      	       }
    	       	               $sheet->setCellValue( 'A' . $row_cnt,$trans_recep[$sub_index]['TransRecepTrnView']['menu']);
    	       	               $sheet->setCellValue( 'W' . $row_cnt,$trans_recep[$sub_index]['TransRecepTrnView']['working_start_time']."～".$trans_recep[$sub_index]['TransRecepTrnView']['working_end_time']);
    	       	               $sheet->setCellValue( 'AF'. $row_cnt,getFormattedWorkingTotalTime($trans_recep[$sub_index]['TransRecepTrnView']['working_total']));

                               $row_cnt += 2;
    	        $dtl_id = -1;
                $dtl_counter =0;
           		for($dtl_index=0;$dtl_index < count($trans_recep);$dtl_index++){
    	      		//詳細テーブルの外部キーとサブテーブルの主キーが同値
           	  	    if($sub_id == $trans_recep[$dtl_index]['TransRecepTrnView']['trans_recep_sub_id']){
                 	  	 if($dtl_id != $trans_recep[$dtl_index]['TransRecepTrnView']['trans_recep_dtl_id']){
                      	    $dtl_id  = $trans_recep[$dtl_index]['TransRecepTrnView']['trans_recep_dtl_id'];
    	                       /* 初回以外行追加 */
                      	      if($dtl_counter != 0){
                      	      	duplicateAnyTable($sheet, $start_sub_row,2, $row_cnt,"EVEN","LOW");
                      	      	$sheet->removeRow($row_cnt + 3,1);
                      	      	$sheet->getRowDimension($row_cnt + 3)->setRowHeight("14.25");
                      	      }
    	    		               $sheet->setCellValue( 'A' . $row_cnt,$trans_recep[$dtl_index]['TransRecepTrnView']['no']);
    	    		               $sheet->setCellValue( 'B' . $row_cnt,$trans_recep[$dtl_index]['TransRecepTrnView']['departure_time']);
    	    		               $sheet->setCellValue( 'E' . $row_cnt,$trans_recep[$dtl_index]['TransRecepTrnView']['departure_place']);
    	    		               $sheet->setCellValue( 'L' . $row_cnt,$trans_recep[$dtl_index]['TransRecepTrnView']['total_departure_passenger']);
    	    		               $sheet->setCellValue( 'O' . $row_cnt,$trans_recep[$dtl_index]['TransRecepTrnView']['representative_nm']);
    	    		               $sheet->setCellValue( 'V' . $row_cnt,$trans_recep[$dtl_index]['TransRecepTrnView']['arrival_time']);
    	    		               $sheet->setCellValue( 'Y' . $row_cnt,$trans_recep[$dtl_index]['TransRecepTrnView']['arrival_place']);
    	    		               $sheet->setCellValue( 'AF'. $row_cnt,$trans_recep[$dtl_index]['TransRecepTrnView']['total_arrival_passenger']);
    	    		           $row_cnt+=2;
    	    		               $sheet->setCellValue( 'B' . $row_cnt,$trans_recep[$dtl_index]['TransRecepTrnView']['trans_recep_dtl_note']);
    	    		           $row_cnt++;
    	                       $dtl_counter++;
    	    	          }
           	            }  //サブIDと一致する詳細テーブル外部キー判定のIF文の締め
    	           }   //詳細テーブルのデータ数だけLOOPするFOR文の締め
    	       $sub_counter++;
                        }
   	                   }    //ヘッダIDと一致するサブテーブル外部キー判定のIF文の締め
                     }  //サブテーブルのデータ数だけLOOPするFOR文の締め
                 $row_cnt++;
                 $head_counter++;
                 } //一意のヘッダID判定のIF文の締め
              }    //trans_viewのデータ数だけLOOPするFOR文の締め
            }  //transデータ存在チェックIF文の締め
	return $row_cnt - 2;

}

/**
 *
 * Cakeデータの作成
 * @param $sheet
 * @param $cake
 * @param $row_cnt
 * @param $common
 */
function setCakeDataForCustomerSheet($sheet,$cake,$row_cnt,$common){

	$start_sub_row = $row_cnt + 1;
    $head_id = -1;
    $head_counter = 0;

    for($head_index=0;$head_index < count($cake);$head_index++){

        /* メインテーブル作成 */
     	if($head_id != $cake[$head_index]['CakeTrnView']['id']){
     	   $head_id  = $cake[$head_index]['CakeTrnView']['id'];

           //$sheet->setCellValue( 'Y' . $row_cnt,$cake[$head_index]['CakeTrnView']['note']);
           $sheet->mergeCells('A'. $row_cnt.':G'.$row_cnt);
           $row_cnt += 2;
            /* サブテーブル作成 */
            $sub_id = -1;
            $sub_counter = 0;

            for($sub_index=0;$sub_index < count($cake);$sub_index++){

               //サブテーブルの外部キーとヘッダの主キーが同値
    	       if($head_id == $cake[$sub_index]['CakeTrnView']['id']){
    	         	if($sub_id != $cake[$sub_index]['CakeTrnView']['cake_menu_id']){
    	               $sub_id  = $cake[$sub_index]['CakeTrnView']['cake_menu_id'];

    	               /* ２行目以降なら行を追加する */
    	               if($sub_counter != 0){
    	                 $row_cnt++;
    	                 duplicateAnyTable($sheet,$start_sub_row,3,$row_cnt,"ODD","LOW");
    	                 $sheet->removeRow($row_cnt + 4,1);
    	                 $sheet->getRowDimension($row_cnt + 4)->setRowHeight(getDefaultMinRowHeight());
    	                 $row_cnt++;
    	               }

    	               $sheet->setCellValue( 'A' . $row_cnt,$cake[$sub_index]['CakeTrnView']['menu']);
    	               $sheet->setCellValue( 'M' . $row_cnt,$cake[$sub_index]['CakeTrnView']['size']);
    	               $sheet->setCellValue( 'Q' . $row_cnt,$cake[$sub_index]['CakeTrnView']['shaping']);
    	               $sheet->setCellValue( 'U' . $row_cnt,$cake[$sub_index]['CakeTrnView']['topping']);
    	               $sheet->setCellValue( 'AB'. $row_cnt,$cake[$sub_index]['CakeTrnView']['name_plate']);

    	               $row_cnt += 2;
    	               $sheet->setCellValue( 'A' . $row_cnt,$cake[$sub_index]['CakeTrnView']['flavor']);
    	               $sheet->setCellValue( 'E' . $row_cnt,$cake[$sub_index]['CakeTrnView']['filling']);
    	               $sheet->setCellValue( 'I' . $row_cnt,$cake[$sub_index]['CakeTrnView']['frosting']);
    	               $sheet->setCellValue( 'M' . $row_cnt,$cake[$sub_index]['CakeTrnView']['decoration']);
    	               $sheet->setCellValue( 'U' . $row_cnt,$cake[$sub_index]['CakeTrnView']['flower']);
    	               $sheet->setCellValue( 'AB' . $row_cnt,$cake[$sub_index]['CakeTrnView']['cake_menu_note']);
    	      	       $sub_counter++;
   	                }
    	       }
            }

          $row_cnt += 2;
          $head_counter++;
   	 	}
    }
	return $row_cnt -2 ;
}



/**
 *
 * Entertainmentデータの作成
 * @param $sheet
 * @param $entertainment
 * @param $row_cnt
 * @param $common
 */
function setEntertainmentDataForCustomerSheet($sheet,$entertainment,$row_cnt,$common){

   $head_id = -1;
   $head_counter = 0;

   for($head_index=0;$head_index < count($entertainment);$head_index++){

       /* メインテーブル作成 */
       if($head_id != $entertainment[$head_index]['EntertainmentTrnView']['id']){
      	  $head_id  = $entertainment[$head_index]['EntertainmentTrnView']['id'];

      	  $sheet->mergeCells('A'. $row_cnt.':G'.$row_cnt);
          $row_cnt += 2;

          /* サブテーブル作成 */
          $sub_id = -1;
          $sub_counter = 0;

          for($sub_index=0;$sub_index < count($entertainment);$sub_index++){

             //サブテーブルの外部キーとヘッダの主キーが同値
             if($head_id == $entertainment[$sub_index]['EntertainmentTrnView']['id']){
  	             	if($sub_id != $entertainment[$sub_index]['EntertainmentTrnView']['entertainment_menu_id']){
  	       	           $sub_id  = $entertainment[$sub_index]['EntertainmentTrnView']['entertainment_menu_id'];

    	                /* ２行目以降なら行を追加する */
    	               if($sub_counter != 0){
    	                 $row_cnt++;
    	                 $sheet->insertNewRowBefore($row_cnt, 2);

    	                $top = $row_cnt - 2;
    	                $bottom = $row_cnt - 1;
    	                $colOffset = 0; // コピー先 列のオフセット値
                        $rowOffset = 2; // コピー先 行のオフセット値
                        /* コピー開始 */
                        for($col=getColIndexByName("A");$col <= getColIndexByName("AH");$col++) {
                          for($row=$top;$row <= $bottom ;$row++) {
                            // セルを取得
                            $cell = $sheet->getCellByColumnAndRow($col, $row);
                            // セルスタイルを取得
                            $style = $sheet->getStyleByColumnAndRow($col, $row);
                            // 数値から列文字列に変換する (0,1) → A1
                            $offsetCell = PHPExcel_Cell::stringFromColumnIndex($col + $colOffset) . (string)($row + $rowOffset);
                            // セル値をコピー
                            $sheet->setCellValue($offsetCell, $cell->getValue());
                            // スタイルをコピー
                            $sheet->duplicateStyle($style, $offsetCell);
                         }
                       }

                      $current_offset_row = 1;
                      /* 行の幅調整 */
                      for($row=$top + $rowOffset;$row <= $bottom + $rowOffset ;$row++) {
                        if($current_offset_row % 2 != 0){
   	                       $sheet->getRowDimension($row)->setRowHeight(getDefaultMinRowHeight());
                        }
                         $current_offset_row++;
                       }
                      $row_cnt++;
    	            }

    	             $sheet->setCellValue( 'A'  . $row_cnt,$entertainment[$sub_index]['EntertainmentTrnView']['menu']);
    	             $sheet->setCellValue( 'I'  . $row_cnt,$entertainment[$sub_index]['EntertainmentTrnView']['working_start_time']);
    	             $sheet->setCellValue( 'L'  . $row_cnt,$entertainment[$sub_index]['EntertainmentTrnView']['working_end_time']);
    	             $sheet->setCellValue( 'O'  . $row_cnt,getFormattedWorkingTotalTime($entertainment[$sub_index]['EntertainmentTrnView']['working_total_time']));
    	             $sheet->setCellValue( 'R'  . $row_cnt,$entertainment[$sub_index]['EntertainmentTrnView']['start_place']);
    	             $sheet->setCellValue( 'AB' . $row_cnt,$entertainment[$sub_index]['EntertainmentTrnView']['entertainment_menu_note']);
                     $sub_counter++;
  	             }
             }
          }
       $row_cnt += 2;
       $head_counter++;
      }
   }
   return $row_cnt - 2;
}



/**
 *
 * Avデータの作成
 * @param $sheet
 * @param $av
 * @param $row_cnt
 * @param $common
 */
function setAvDataForCustomerSheet($sheet,$av,$row_cnt,$common){

   $head_id = -1;
   $head_counter = 0;

   for($head_index=0;$head_index < count($av);$head_index++){
     /* メインテーブル作成 */
     if($head_id != $av[$head_index]['AvTrnView']['id']){
        $head_id  = $av[$head_index]['AvTrnView']['id'];

       $sheet->mergeCells('A'. $row_cnt.':G'.$row_cnt);
       $row_cnt += 2;

       /* サブテーブル作成 */
       $sub_id = -1;
       $sub_counter = 0;

       for($sub_index=0;$sub_index < count($av);$sub_index++){

         //サブテーブルの外部キーとヘッダの主キーが同値
         if($head_id == $av[$sub_index]['AvTrnView']['id']){
           	if($sub_id != $av[$sub_index]['AvTrnView']['av_menu_id']){
               $sub_id  = $av[$sub_index]['AvTrnView']['av_menu_id'];

       	       /* ２行目以降なら行を追加する */
               if($sub_counter != 0){
                  $row_cnt++;
                  $sheet->insertNewRowBefore($row_cnt, 1);
               }

               $sheet->setCellValue( 'A'  . $row_cnt,$av[$sub_index]['AvTrnView']['menu']);
    	       $sheet->setCellValue( 'I'  . $row_cnt,$av[$sub_index]['AvTrnView']['setting_start_time']);
    	       $sheet->setCellValue( 'L'  . $row_cnt,$av[$sub_index]['AvTrnView']['setting_end_time']);
    	      // $sheet->setCellValue( 'O'  . $row_cnt,$av[$sub_index]['AvTrnView']['working_total_time']);
    	       $sheet->setCellValue( 'R'  . $row_cnt,$av[$sub_index]['AvTrnView']['setting_place']);
    	       $sheet->setCellValue( 'AB' . $row_cnt,$av[$sub_index]['AvTrnView']['av_menu_note']);
               $sub_counter++;
            }
         }
       }

       $row_cnt += 2;
       $head_counter++;
  	}
  }
  return $row_cnt - 2;
}


/**
 *
 * Linenデータの作成
 * @param $sheet
 * @param $linen
 * @param $row_cnt
 * @param $common
 */
function setLinenDataForCustomerSheet($sheet,$linen,$row_cnt,$common){

   $head_id = -1;
   $head_counter = 0;

   for($head_index=0;$head_index < count($linen);$head_index++){

       /* メインテーブル作成 */
       if($head_id != $linen[$head_index]['LinenTrnView']['id']){
          $head_id  = $linen[$head_index]['LinenTrnView']['id'];

          $sheet->mergeCells('A'. $row_cnt.':G'.$row_cnt);
          $row_cnt += 2;

    	  /* サブテーブル作成 */
          $sub_id = -1;
          $sub_counter = 0;

          for($sub_index=0;$sub_index < count($linen);$sub_index++){

            //サブテーブルの外部キーとヘッダの主キーが同値
    	    if($head_id == $linen[$sub_index]['LinenTrnView']['id']){

    	      	if($sub_id != $linen[$sub_index]['LinenTrnView']['linen_dtl_id']){
    	           $sub_id  = $linen[$sub_index]['LinenTrnView']['linen_dtl_id'];

    	          /* ２行目以降なら行を追加する */
                  if($sub_counter != 0){
                     $row_cnt++;
                     $sheet->insertNewRowBefore($row_cnt, 1);
                  }
                  $sheet->setCellValue( 'A' . $row_cnt,$linen[$sub_index]['LinenTrnView']['menu']);
                  //$sheet->setCellValue( 'G' . $row_cnt,$linen[$head_index]['LinenTrnView']['delivery_place']);
                  $sheet->setCellValue( 'M' . $row_cnt,$linen[$sub_index]['LinenTrnView']['color']);
                  $sheet->setCellValue( 'S' . $row_cnt,$linen[$sub_index]['LinenTrnView']['num']);
                  $sheet->setCellValue( 'V' . $row_cnt,$linen[$sub_index]['LinenTrnView']['linen_dtl_note']);
                  $sub_counter++;
    	        }
    	     }
           }
       $row_cnt += 2;
       $head_counter++;
     }
  }
  return $row_cnt - 2;
}


/**
 *
 * Paperデータの作成
 * @param $sheet
 * @param $paper
 * @param $row_cnt
 * @param $common
 */
function setPaperDataForCustomerSheet($sheet,$paper,$row_cnt,$common){

    $head_id = -1;
    $head_counter = 0;

    for($head_index=0;$head_index < count($paper);$head_index++){

        /* メインテーブル作成 */
     	if($head_id != $paper[$head_index]['PaperTrnView']['id']){
    	   $head_id  = $paper[$head_index]['PaperTrnView']['id'];

    	   $sheet->mergeCells('A'. $row_cnt.':G'.$row_cnt);
    	   $row_cnt += 2;

    	   /* サブテーブル作成 */
           $sub_id = -1;
           $sub_counter = 0;

           for($sub_index=0;$sub_index < count($paper);$sub_index++){

              //サブテーブルの外部キーとヘッダの主キーが同値
    	      if($head_id == $paper[$sub_index]['PaperTrnView']['id']){
    	       	if($sub_id != $paper[$sub_index]['PaperTrnView']['paper_dtl_id']){
    	           $sub_id  = $paper[$sub_index]['PaperTrnView']['paper_dtl_id'];

    	           /* ２行目以降なら行を追加する */
                   if($sub_counter != 0){
                      $row_cnt++;
                      $sheet->insertNewRowBefore($row_cnt, 1);
                   }

                  $sheet->setCellValue( 'A' . $row_cnt,$paper[$sub_index]['PaperTrnView']['menu']);
                  $sheet->setCellValue( 'L' . $row_cnt,$paper[$sub_index]['PaperTrnView']['color']);
                  $sheet->setCellValue( 'R' . $row_cnt,$paper[$sub_index]['PaperTrnView']['num']);
                  $sheet->setCellValue( 'V' . $row_cnt,$paper[$sub_index]['PaperTrnView']['paper_dtl_note']);
                  $sub_counter++;
    	        }
    	    }
         }
     $row_cnt += 2;
     $head_counter++;
    }
  }
     return $row_cnt - 2;
}



/**
 *
 * Mcデータの作成
 * @param $sheet
 * @param $mc
 * @param $row_cnt
 * @param $common
 */
function setMcDataForCustomerSheet($sheet,$mc,$row_cnt,$common){

	for($index=0;$index < count($mc);$index++){

        $row_cnt += 2;
    	$sheet->setCellValue( 'A' . $row_cnt,$mc[$index]['McTrnView']['menu']);
        $sheet->setCellValue( 'I' . $row_cnt,$mc[$index]['McTrnView']['working_start_time']);
        $sheet->setCellValue( 'L' . $row_cnt,$mc[$index]['McTrnView']['working_end_time']);
        $sheet->setCellValue( 'O' . $row_cnt,getFormattedWorkingTotalTime($mc[$index]['McTrnView']['working_total']));
        $sheet->setCellValue( 'R' . $row_cnt,$mc[$index]['McTrnView']['start_place']);
        $sheet->setCellValue( 'AB' . $row_cnt,$mc[$index]['McTrnView']['rw_note']);

    	$row_cnt += 2;
    }
  return $row_cnt - 2;
}


/**
 *
 * Ministerデータの作成
 * @param $sheet
 * @param $mc
 * @param $row_cnt
 * @param $common
 */
function setMinisterDataForCustomerSheet($sheet,$minister,$row_cnt,$common){

	for($index=0;$index < count($minister);$index++){

        $row_cnt += 2;
    	$sheet->setCellValue( 'A' . $row_cnt,$minister[$index]['MinisterTrnView']['menu']);
        $sheet->setCellValue( 'I' . $row_cnt,$minister[$index]['MinisterTrnView']['working_start_time']);
        $sheet->setCellValue( 'L' . $row_cnt,$minister[$index]['MinisterTrnView']['working_end_time']);
        $sheet->setCellValue( 'O' . getFormattedWorkingTotalTime($row_cnt,$minister[$index]['MinisterTrnView']['working_total']));
        $sheet->setCellValue( 'R' . $row_cnt,$minister[$index]['MinisterTrnView']['start_place']);
        $sheet->setCellValue( 'AB' . $row_cnt,$minister[$index]['MinisterTrnView']['rw_note']);

    	$row_cnt += 2;
    }
  return $row_cnt - 2;
}



/**
 *
 * Party Optionデータの作成
 * @param $sheet
 * @param $party_option
 * @param $row_cnt
 * @param $common
 */
function setPartyOptionDataForCustomerSheet($sheet,$party_option,$row_cnt,$common){

    $head_id = -1;
    $head_counter = 0;

    for($head_index=0;$head_index < count($party_option);$head_index++){

        /* メインテーブル作成 */
     	if($head_id != $party_option[$head_index]['PartyOptionTrnView']['id']){
    	   $head_id  = $party_option[$head_index]['PartyOptionTrnView']['id'];

    	   $sheet->mergeCells('A'. $row_cnt.':G'.$row_cnt);
           $row_cnt += 2;

    	   /* サブテーブル作成 */
           $sub_id = -1;
           $sub_counter = 0;

           for($sub_index=0;$sub_index < count($party_option);$sub_index++){

             //サブテーブルの外部キーとヘッダの主キーが同値
    	     if($head_id == $party_option[$sub_index]['PartyOptionTrnView']['id']){

    	      	if($sub_id != $party_option[$sub_index]['PartyOptionTrnView']['party_option_dtl_id']){
    	           $sub_id  = $party_option[$sub_index]['PartyOptionTrnView']['party_option_dtl_id'];

    	           /* ２行目以降なら行を追加する */
                   if($sub_counter != 0){
                      $row_cnt++;
                      $sheet->insertNewRowBefore($row_cnt, 1);
                   }
                  $sheet->setCellValue( 'A' . $row_cnt,$party_option[$sub_index]['PartyOptionTrnView']['menu']);
                  $sheet->setCellValue( 'I' . $row_cnt,$party_option[$sub_index]['PartyOptionTrnView']['content']);
                  $sheet->setCellValue( 'X' . $row_cnt,$party_option[$sub_index]['PartyOptionTrnView']['num']);
                  $sheet->setCellValue( 'Z' . $row_cnt,$party_option[$sub_index]['PartyOptionTrnView']['party_option_note']);
                  $sub_counter++;
             }
           }
       }

      $row_cnt += 2;
      $head_counter++;
     }
   }
 return $row_cnt - 2;
}



/**
 *
 * House Weddingデータの作成
 * @param $sheet
 * @param $house_wedding
 * @param $row_cnt
 * @param $common
 */
function setHouseWeddingDataForCustomerSheet($sheet,$house_wedding,$row_cnt,$common){

   for($index=0;$index < count($house_wedding);$index++){

      $row_cnt += 2;
   	  $sheet->setCellValue( 'A' . $row_cnt,$house_wedding[$index]['HouseWeddingTrnView']['site']);
   	  $sheet->setCellValue( 'S' . $row_cnt,$house_wedding[$index]['HouseWeddingTrnView']['note']);

   	  $row_cnt += 2;
   }
  return $row_cnt - 2;
}


/**
 *
 * テーブルの複製(違う業者の場合のみ複製する）
 * @param $sheet
 * @param $table_obj
 * @param $table_name
 * @param $row_cnt
 * @param $table_rows_count
 */
function duplicateTable($sheet,$table_obj,$table_name,$row_cnt,$table_rows_count){

	$head_id = -1;
    $head_counter = 0;

    for($head_index=0;$head_index < count($table_obj);$head_index++){
     if($head_id != $table_obj[$head_index][$table_name]['id']){
     	$head_id  = $table_obj[$head_index][$table_name]['id'];
        $head_counter++;
      }
    }

    $top = $row_cnt;
    $bottom = $top + $table_rows_count;
    for($i=1; $i < $head_counter;$i++){

       $insert_row_pos = $top + ($table_rows_count + 2);
       /* 挿入分の行を追加する */
       for($j=0;$j < ($table_rows_count + 3);$j++){
    	 $sheet->insertNewRowBefore($insert_row_pos, 1);
       }

       $colOffset = 0; // コピー先 列のオフセット値
       $rowOffset = $table_rows_count + 3; // コピー先 行のオフセット値
       /* コピー開始 */
       for($col=getColIndexByName("A");$col <= getColIndexByName("AH");$col++) {
          for($row=$top;$row <= $bottom ;$row++) {
            // セルを取得
            $cell = $sheet->getCellByColumnAndRow($col, $row);
            // セルスタイルを取得
            $style = $sheet->getStyleByColumnAndRow($col, $row);
            // 数値から列文字列に変換する (0,1) → A1
            $offsetCell = PHPExcel_Cell::stringFromColumnIndex($col + $colOffset) . (string)($row + $rowOffset);
            // セル値をコピー
            $sheet->setCellValue($offsetCell, $cell->getValue());
            // スタイルをコピー
            $sheet->duplicateStyle($style, $offsetCell);
          }
      }

      /* 業者名のスタイルコピー */
    //  $sheet->setCellValue("I".($top -1 + $rowOffset), $sheet->getCell("I".($top-1))->getValue());
      $style = $sheet->getStyle("I".($top-1));
      $sheet->duplicateStyle($style, "I".($top -1 + $rowOffset));

      $current_offset_row = 1;
      /* 行の幅調整 */
      for($row=$top + $rowOffset;$row <= $bottom + $rowOffset ;$row++) {
        if($current_offset_row % 2 == 0){
   	      $sheet->getRowDimension($row)->setRowHeight(getDefaultRowHeight());
        }
        $current_offset_row++;
      }
      $top += $rowOffset;
      $bottom += $rowOffset;
    }
}

/**
 *
 * テーブルの複製(条件なしにテーブルを複製）
 * @param $sheet
 * @param $row_cnt
 * @param $table_rows_count
 * @param $insert_row_pos
 * @param $method_of_row_height   行の高さをデフォルト値にするタイミング
 */
function duplicateAnyTable($sheet,$row_cnt,$table_rows_count,$insert_row_pos,$method_of_row_height="EVEN",$row_height_mode="HIGHT"){

    $top = $row_cnt;
    $bottom = $top + $table_rows_count;

       /* 挿入分の行を追加する */
       for($j=0;$j < ($table_rows_count + 2);$j++){
    	 $sheet->insertNewRowBefore($insert_row_pos, 1);
       }

       /* コピー開始 */
       for($col=getColIndexByName("A");$col <= getColIndexByName("AH");$col++) {

       	 $colOffset = 0; // コピー先 列のオフセット値
         $rowOffset = 0; // コピー先 行のオフセット値
       	 for($row=$top;$row <= $bottom ;$row++) {
            // セルを取得
            $cell = $sheet->getCellByColumnAndRow($col, $row);
            // セルスタイルを取得
            $style = $sheet->getStyleByColumnAndRow($col, $row);
            // 数値から列文字列に変換する (0,1) → A1
            $offsetCell = PHPExcel_Cell::stringFromColumnIndex($col + $colOffset) . (string)($insert_row_pos + $rowOffset);
            // セル値をコピー
            $sheet->setCellValue($offsetCell, $cell->getValue());
            // スタイルをコピー
            $sheet->duplicateStyle($style, $offsetCell);
            $rowOffset++;
          }
      }

      $current_offset_row = 1;
      /* 行の幅調整 */
      for($row=$insert_row_pos;$row <= $insert_row_pos+$table_rows_count ;$row++) {

      	/* 偶数行の時に行高さを変更 */
      	if(strtoupper($method_of_row_height) == "EVEN"){
      		if($current_offset_row % 2 == 0){
      			if(strtoupper($row_height_mode) == "HIGHT" ){
      				$sheet->getRowDimension($row)->setRowHeight(getDefaultRowHeight());
      			}else{
      				$sheet->getRowDimension($row)->setRowHeight(getDefaultMinRowHeight());
      			}
            }
        /* 奇数行の時に行高さを変更 */
      	}else{
      		if($current_offset_row % 2 != 0){
   	          if(strtoupper($row_height_mode) == "HIGHT" ){
      				$sheet->getRowDimension($row)->setRowHeight(getDefaultRowHeight());
      		  }else{
      				$sheet->getRowDimension($row)->setRowHeight(getDefaultMinRowHeight());
      		  }
            }
      	}
        $current_offset_row++;
      }
}

/**
 *
 * カラム名から数値インデックスを取得する
 * @param unknown_type $col
 */
function getColIndexByName($col){
	 $cols = array("A"=>0,"B"=>1,"C"=>2,"D"=>3,"E"=>4,"F"=>5,"G"=>6,"H"=>7,"I"=>8,"J"=>9,"K"=>10,"L"=>11,"M"=>12,"N"=>13,"O"=>14,
                  "P"=>15,"Q"=>16,"R"=>17,"S"=>18,"T"=>19,"U"=>20,"V"=>21,"W"=>22,"X"=>23,"Y"=>24,"Z"=>25,"AA"=>26,"AB"=>27,"AC"=>28,
                  "AD"=>29,"AE"=>30,"AF"=>31,"AG"=>32,"AH"=>33,"AI"=>34);
	 return $cols[$col];
}

/**
 *
 * 形式済みの合計稼働時間を取得する
 * @param $working_total_time
 */
function getFormattedWorkingTotalTime($working_total_time){

  if($working_total_time == null || $working_total_time == 0){return "";}
  $hour = floor($working_total_time / 60) == '0' ? "" : (floor($working_total_time / 60)).'H';
  $time = $working_total_time % 60 == 0 ? "" : ($working_total_time % 60).'M';

  return $hour.$time;
}

function getDefaultRowHeight(){return 39.75;}
function getDefaultMinRowHeight(){return 14.25;}
?>