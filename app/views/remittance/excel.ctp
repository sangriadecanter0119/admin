<?php
// Excel出力用ライブラリ
App::import( 'Vendor', 'PHPExcel', array('file'=>'phpexcel' . DS . 'PHPExcel.php') );
//App::import( 'Vendor', 'PHPExcel_IOFactory', array('file'=>'phpexcel' . DS . 'PHPExcel' . DS . 'IOFactory.php') );
//App::import( 'Vendor', 'PHPExcel_Cell_AdvancedValueBinder', array('file'=>'phpexcel' . DS . 'PHPExcel' . DS . 'Cell' . DS . 'AdvancedValueBinder.php') );

// Excel95用ライブラリ
App::import( 'Vendor', 'PHPExcel_Writer_Excel2007', array('file'=>'phpexcel' . DS . 'PHPExcel' . DS . 'Writer' . DS . 'Excel2007.php') );
App::import( 'Vendor', 'PHPExcel_Reader_Excel2007', array('file'=>'phpexcel' . DS . 'PHPExcel' . DS . 'Reader' . DS . 'Excel2007.php') );

$cols = array("A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z");

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
    // 列番
    $row_cnt = 5;
    // 行番
    $col_cnt = 2;
    $wedding_date = array();
    /* 送金リスト一覧の作成 */
    for($i=0;$i < count($data);$i++)
    {
      $atr = $data[$i]['RemittanceTrnView'];
	  $sheet->setCellValue("B".$row_cnt ,($i+1));
	  $sheet->setCellValue("C".$row_cnt,$common->evalForShortDate($atr['wedding_dt']));
	  $sheet->setCellValue("D".$row_cnt,$atr['grmls_kj']." ".$atr['grmfs_kj']);
	  $sheet->setCellValue("E".$row_cnt,$atr['vendor_total_cost']);
	  $sheet->setCellValue("F".$row_cnt,$atr['aw_total_cost']);
	  $sheet->setCellValue("G".$row_cnt,$atr['total_tax']);
	  $sheet->setCellValue("H".$row_cnt,$atr['remittance_total']);

	  $sheet->getStyle("B".$row_cnt.":H".$row_cnt)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_DASHED);
	  $wedding_date[$atr['customer_id']] = $atr['wedding_dt'];
	  $row_cnt++;
    }

    /* フッター作成  */
    $sheet->setCellValue("D".$row_cnt, "合計");
    $sheet->setCellValue("E".$row_cnt, "=SUM(E5:E".($row_cnt-1).")");
    $sheet->setCellValue("F".$row_cnt, "=SUM(F5:F".($row_cnt-1).")");
    $sheet->setCellValue("G".$row_cnt, "=SUM(G5:G".($row_cnt-1).")");
    $sheet->setCellValue("H".$row_cnt, "=SUM(H5:H".($row_cnt-1).")");

    /* 羅線 */
    $sheet->getStyle("B4:H4")->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
    $sheet->getStyle("B".$row_cnt.":H".$row_cnt)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
    $sheet->getStyle("B4:B".$row_cnt)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
    $sheet->getStyle("H4:H".$row_cnt)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
    $sheet->getStyle("B".$row_cnt.":H".$row_cnt)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_DOUBLE);

    //タイトル
    $sheet->setCellValue("C2", substr($data[0]['RemittanceTrnView']['wedding_dt'],0,7)."挙式分");
    // PHPの日時出力
    $time = time();                 // 現在日時(Unix Timestamp)
    $sheet->setCellValue("H2", "出力日：".date('Y/m/d'));

    //明細用顧客分シートの作成
    createAllSheet($objPHPExcel,$estimate_data);

    /* 送金リスト明細作成 */
    $estimate_id = -1;
    $row_cnt = 5;
    $internal_total_price = 0;
    $internal_total_foreign_price =0;
    for($i=0;$i < count($estimate_data);$i++){

    	$attr = $estimate_data[$i]["EstimateDtlTrnView"];
    	if($estimate_id != $attr["estimate_id"]){

            $sheet = $objPHPExcel->getSheetByName($attr["grmls_kj"]."様");
            $estimate_id = $attr["estimate_id"];
            $row_cnt = 5;
            //送金為替レート
            $sheet->setCellValue("R2",$attr['remittance_exchange_rate']);
            //タイトル
            $sheet->setCellValue("B2",$attr["grmls_kj"]."様     挙式日：".substr($wedding_date[$attr["customer_id"]],0,10));
            //初期化
            $internal_total_price = 0;
            $internal_total_foreign_price =0;
    	}
      //明細行の作成
      $ret = calculateLine($sheet,$attr,$row_cnt,$payment_kbn_list);
      $internal_total_price += $ret['internal_price'];
      $internal_total_foreign_price += $ret['internal_foreign_price'];
      $row_cnt++;

      //全データの最後か各顧客データの最後なら明細の合計計算をする
      if(($i == count($estimate_data)-1) ||
         ($estimate_data[$i+1]["EstimateDtlTrnView"]["estimate_id"] != $estimate_id)){
         	calculateTotal($sheet,$row_cnt,$estimate_data[$i]["EstimateDtlTrnView"],$internal_total_price,$internal_total_foreign_price);
      }else{
      	$sheet->insertNewRowBefore( $row_cnt, 1 );
      }
    }


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
 *
 * 明細シート雛形を顧客分作成する
 * @param unknown_type $excel
 * @param unknown_type $estimate_data
 */
function createAllSheet($excel,$estimate_data){

    $estimate_id = -1;
    for($i=0;$i < count($estimate_data);$i++){

    	$attr = $estimate_data[$i]["EstimateDtlTrnView"];
    	if($estimate_id != $attr["estimate_id"]){

            $tmp_sheet = $excel->getSheetByName("sub_template");
            $new_sheet = $tmp_sheet->copy();
            $new_sheet->setTitle($attr["grmls_kj"]."様");
            // 第2パラメータが null(省略時含む)の場合は最後尾に追加される
            $excel->addSheet($new_sheet, null);

            $estimate_id = $attr["estimate_id"];
    	}
    }
    /* テンプレートシートの削除 */
    $delSheet = $excel->getSheetByName("sub_template");
    $delIndex = $excel->getIndex($delSheet);
    $excel->removeSheetByIndex($delIndex);
}

/**
 *
 * 明細行の計算
 * @param $sheet
 * @param $estimate_data
 * @param $row_cnt
 * @param $payment_kbn_list
 */
function calculateLine($sheet,$estimate_data,$row_cnt,$payment_kbn_list){

	     //価格計算等の準備
         $rate      = $estimate_data['sales_exchange_rate'];
         $cost_rate = $estimate_data['cost_exchange_rate'];
         $num       = $estimate_data['num'];
         $aw_rate   = $estimate_data['aw_share'];
         $rw_rate   = $estimate_data['rw_share'];
         $foreign_unit_price=0;
         $foreign_amount_price=0;
         $foreign_unit_cost=0;
         $foreign_cost=0;
         $foreign_net=0;
         $foreign_profit_rate=0;
         $foreign_aw_share=0;
         $foreign_rw_share=0;

         $unit_price=0;
         $amount_price=0;
         $unit_cost=0;
         $cost=0;
         $net=0;
         $profit_rate=0;
         $aw_share=0;
         $rw_share=0;

         //ドルベース
         if($estimate_data['currency_kbn']==0)
         {
         	$foreign_unit_price   = $estimate_data['sales_price'];
            $foreign_amount_price = $foreign_unit_price * $num;
            $foreign_unit_cost    = $estimate_data['sales_cost'];
            $foreign_cost = $foreign_unit_cost * $num;
            $foreign_net  = $foreign_amount_price - $foreign_cost;
            $foreign_aw_share = $foreign_net * $aw_rate;
            $foreign_rw_share = $foreign_net * $rw_rate;
            if($foreign_amount_price != 0){
            	$foreign_profit_rate = round($foreign_net / $foreign_amount_price * 100);
            }

            $unit_price   = round($estimate_data['sales_price'] * $rate);
            $amount_price = $unit_price * $num;
            $unit_cost    = round($estimate_data['sales_cost'] * $cost_rate);
            $cost = $unit_cost * $num;
            $net = $amount_price - $cost;
            $aw_share = $net * $aw_rate;
            $rw_share = $net * $rw_rate;
            if($amount_price != 0){
            	$profit_rate = round($net / $amount_price * 100);
            }
         }
         //円ベース
         else
         {
            $unit_price   = round($estimate_data['sales_price']);
            $amount_price = $unit_price * $num;
            $unit_cost    = round($estimate_data['sales_cost']);
            $cost = $unit_cost * $num;
            $net = $amount_price - $cost;
            $aw_share = $net * $aw_rate;
            $rw_share = $net * $rw_rate;
            if($amount_price != 0){
            	$profit_rate = round($net / $amount_price * 100);
            }

            $foreign_unit_price   = round($estimate_data['sales_price'] / $rate,2);
            $foreign_amount_price = $foreign_unit_price * $num;
            $foreign_unit_cost    = round($estimate_data['sales_cost'] / $cost_rate,2);
            $foreign_cost = $foreign_unit_cost * $num;
            $foreign_net = $foreign_amount_price - $foreign_cost;
            $foreign_aw_share = $foreign_net * $aw_rate;
            $foreign_rw_share = $foreign_net * $rw_rate;
            if($foreign_amount_price != 0){
            	$foreign_profit_rate = round($foreign_net / $foreign_amount_price * 100);
            }
         }

      //支配区分
      for($payment_kbn_index=0;$payment_kbn_index < count($payment_kbn_list);$payment_kbn_index++){

           if($estimate_data['payment_kbn_id'] == $payment_kbn_list[$payment_kbn_index]['PaymentKbnMst']['id']){
           	   $sheet->setCellValue("B".$row_cnt,$payment_kbn_list[$payment_kbn_index]['PaymentKbnMst']['payment_kbn_nm']);
           }
      }

	  $sheet->setCellValue("C".$row_cnt,$estimate_data['goods_kbn_nm']);
	  $sheet->setCellValue("D".$row_cnt,$estimate_data['sales_goods_nm']);
	  $sheet->setCellValue("E".$row_cnt,$estimate_data['num']);
	  //総代価 (外貨)
	  $sheet->setCellValue("F".$row_cnt,$foreign_amount_price);
	  //総代価 (円貨)
	  $sheet->setCellValue("G".$row_cnt,$amount_price);
	  //総原価 (外貨)
	  if($estimate_data['payment_kbn_id'] == PC_CREDIT_ABOARD_PAY){
   	          $sheet->setCellValue("H".$row_cnt,"0.00");
      }else if($estimate_data['payment_kbn_id'] == PC_DOMESTIC_DIRECT_PAY ||
    	        $estimate_data['payment_kbn_id'] == PC_DOMESTIC_CREDIT_PAY){
   	          $sheet->setCellValue("H".$row_cnt,"0.00");
   	  }else{
   	   	      $sheet->setCellValue("H".$row_cnt,$foreign_cost);
   	  }

   	  // 総原価 (円貨)・利益(円貨)
      if($estimate_data['payment_kbn_id'] == PC_CREDIT_ABOARD_PAY){
      	      //背景色
      	      $sheet->getStyle("B".$row_cnt.":R".$row_cnt)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
              $sheet->getStyle("B".$row_cnt.":R".$row_cnt)->getFill()->getStartColor()->setRGB('C0C0C0');

      	      $sheet->setCellValue("I".$row_cnt,"0.00");
   	          $sheet->setCellValue("J".$row_cnt,sprintf("=G%d - %d",$row_cnt,$cost));
      }else{
          	  //背景色
      	      $sheet->getStyle("B".$row_cnt.":R".$row_cnt)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_NONE);
      	      $sheet->getStyle("B".$row_cnt.":R".$row_cnt)->getFill()->getStartColor()->setRGB();
   	  	      $sheet->setCellValue("I".$row_cnt,$cost);
   	  	      $sheet->setCellValue("J".$row_cnt,sprintf("=G%d - I%d",$row_cnt,$row_cnt));
   	  }


   	  //利益率(円貨)
	  $sheet->setCellValue("K".$row_cnt,sprintf("=IF(G%d=0,0,J%d / G%d)",$row_cnt,$row_cnt,$row_cnt));
   	  // awシェア (円貨)
      if($estimate_data['payment_kbn_id'] == PC_DOMESTIC_DIRECT_PAY ||
         $estimate_data['payment_kbn_id'] == PC_DOMESTIC_CREDIT_PAY){
   	          $sheet->setCellValue("L".$row_cnt,"0.00");
   	  }else{
   	  	      $sheet->setCellValue("L".$row_cnt,sprintf("=J%d * O%d",$row_cnt,$row_cnt));
   	  }

      // awシェア (外貨)
      if($estimate_data['payment_kbn_id'] == PC_DOMESTIC_DIRECT_PAY ||
         $estimate_data['payment_kbn_id'] == PC_DOMESTIC_CREDIT_PAY){
         	   //背景色
      	      $sheet->getStyle("B".$row_cnt.":R".$row_cnt)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
              $sheet->getStyle("B".$row_cnt.":R".$row_cnt)->getFill()->getStartColor()->setRGB('C0C0C0');
   	          $sheet->setCellValue("M".$row_cnt,"0.00");
   	  }else{
   	     	if($estimate_data['remittance_exchange_rate'] == ""  ||
   	      	   $estimate_data['remittance_exchange_rate'] == "0" ||
   	      	   $estimate_data['remittance_exchange_rate'] == "0.00"){
               $sheet->setCellValue("M".$row_cnt,"0.00");
            }else{
               $sheet->setCellValue("M".$row_cnt,sprintf("=L%d / R2",$row_cnt));
            }
            //背景色
            $sheet->getStyle("B".$row_cnt.":R".$row_cnt)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_NONE);
            $sheet->getStyle("B".$row_cnt.":R".$row_cnt)->getFill()->getStartColor()->setRGB();
      }

      // rwシェア(円貨)
      $sheet->setCellValue("N".$row_cnt,sprintf("=J%d * P%d",$row_cnt,$row_cnt));
      // awレート
      $sheet->setCellValue("O".$row_cnt,$estimate_data['aw_share']);
      // rwレート
      $sheet->setCellValue("P".$row_cnt,$estimate_data['rw_share']);
      // 販売為替レート
      $sheet->setCellValue("Q".$row_cnt,$estimate_data['sales_exchange_rate']);
      // 原価為替レート
      $sheet->setCellValue("R".$row_cnt,$estimate_data['cost_exchange_rate']);

      //国内払い用の商品の場合は代価を返す
      if($estimate_data['payment_kbn_id'] == PC_DOMESTIC_DIRECT_PAY ||
         $estimate_data['payment_kbn_id'] == PC_DOMESTIC_CREDIT_PAY ){
          return array('internal_price'=>$amount_price ,'internal_foreign_price'=>$foreign_amount_price);
      }else{
      	  return array('internal_price'=>0 ,'internal_foreign_price'=>0);
      }
}

/**
 *
 * 合計計算
 * @param $new_sheet
 * @param $row_cnt
 * @param $estimate_data
 */
function calculateTotal($sheet,$row_cnt,$estimate_data,$internal_total_price,$internal_total_foreign_price){

	/* 羅線 */
    $sheet->getStyle("B5:B".($row_cnt-1))->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $sheet->getStyle("R5:R".($row_cnt-1))->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $sheet->getStyle("B".$row_cnt.":R".$row_cnt)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_DOUBLE);

    $row_cnt++;

    /* SUBTOTAL */
    /*
    $sheet->setCellValue("D".$row_cnt,"SUBTOTAL");
    $sheet->setCellValue("F".$row_cnt,sprintf("=SUM(F5:F%d)",$row_cnt-1));
    $sheet->setCellValue("G".$row_cnt,sprintf("=SUM(G5:G%d)",$row_cnt-1));
    $sheet->setCellValue("H".$row_cnt,sprintf("=SUM(H5:H%d)",$row_cnt-1));
    $sheet->setCellValue("I".$row_cnt,sprintf("=SUM(I5:I%d)",$row_cnt-1));
    $sheet->setCellValue("J".$row_cnt,sprintf("=SUM(J5:J%d)",$row_cnt-1));
    $sheet->setCellValue("K".$row_cnt,sprintf("=J%d / G%d"  ,$row_cnt,$row_cnt));
    $sheet->setCellValue("L".$row_cnt,sprintf("=SUM(L5:L%d)",$row_cnt-1));
    $sheet->setCellValue("M".$row_cnt,sprintf("=SUM(M5:M%d)",$row_cnt-1));
    $sheet->setCellValue("N".$row_cnt,sprintf("=SUM(N5:N%d)",$row_cnt-1));
    */

    $row_cnt++;

    /* ハワイ州税 */
    //$sheet->setCellValue("D".$row_cnt,"ハワイ州税");
    $sheet->setCellValue("E".$row_cnt,$estimate_data["hawaii_tax_rate"]);
    $sheet->setCellValue("F".$row_cnt,sprintf("=E%d * (F%d - %d)",$row_cnt,$row_cnt-1,$internal_total_foreign_price));
    $sheet->setCellValue("G".$row_cnt,sprintf("=E%d * (G%d - %d)",$row_cnt,$row_cnt-1,$internal_total_price));
    //$sheet->setCellValue("F".$row_cnt,sprintf("=E%d * F%d)",$row_cnt,$row_cnt-1));
    //$sheet->setCellValue("G".$row_cnt,sprintf("=E%d * G%d)",$row_cnt,$row_cnt-1));
  //  $sheet->getStyleByColumnAndRow(4, $row_cnt)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE);

    $row_cnt++;

    /* アレンジメント料 */
   // $sheet->setCellValue("D".$row_cnt,"アレンジメント料");
    $sheet->setCellValue("E".$row_cnt,$estimate_data["service_rate"]);
   // $sheet->setCellValue("F".$row_cnt,sprintf("=E%d * F%d)",$row_cnt,$row_cnt-2));
   // $sheet->setCellValue("G".$row_cnt,sprintf("=E%d * G%d)",$row_cnt,$row_cnt-2));
  //  $sheet->setCellValue("N".$row_cnt,sprintf("=G%d)",$row_cnt));
  //  $sheet->getStyleByColumnAndRow(4, $row_cnt)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE);

    $row_cnt++;

    /* 中計 */
    /*
    $sheet->setCellValue("D".$row_cnt,"SUBTOTAL");
    $sheet->setCellValue("F".$row_cnt,sprintf("=F%d + F%d + F%d",$row_cnt-3,$row_cnt-2,$row_cnt-1));
    $sheet->setCellValue("G".$row_cnt,sprintf("=G%d + G%d + G%d",$row_cnt-3,$row_cnt-2,$row_cnt-1));
    $sheet->setCellValue("H".$row_cnt,sprintf("=H%d",$row_cnt-3));
    $sheet->setCellValue("I".$row_cnt,sprintf("=I%d",$row_cnt-3));
    $sheet->setCellValue("J".$row_cnt,sprintf("=G%d - I%d",$row_cnt,$row_cnt));
    $sheet->setCellValue("K".$row_cnt,sprintf("=J%d / G%d",$row_cnt,$row_cnt));
    $sheet->setCellValue("L".$row_cnt,sprintf("=L%d",$row_cnt-3));
    $sheet->setCellValue("M".$row_cnt,sprintf("=M%d",$row_cnt-3));
    $sheet->setCellValue("N".$row_cnt,sprintf("=N%d + N%d",$row_cnt-3,$row_cnt-1));
    */

    $row_cnt++;

    /* 割引率 */
    $sheet->setCellValue("D".$row_cnt,$estimate_data['discount_rate_nm']);
    $sheet->setCellValue("E".$row_cnt,$estimate_data['discount_rate']);
    /*
    $sheet->setCellValue("F".$row_cnt,sprintf("=E%d * F%d)",$row_cnt,$row_cnt-1));
    $sheet->setCellValue("G".$row_cnt,sprintf("=E%d * G%d)",$row_cnt,$row_cnt-1));
    $sheet->setCellValue("L".$row_cnt,sprintf("=G%d * O%d)",$row_cnt,$row_cnt));
    $sheet->setCellValue("M".$row_cnt,sprintf("=F%d * O%d)",$row_cnt,$row_cnt));
    $sheet->setCellValue("N".$row_cnt,sprintf("=G%d * P%d)",$row_cnt,$row_cnt));
    */
    $sheet->setCellValue("O".$row_cnt,$estimate_data['discount_aw_share']);
    $sheet->setCellValue("P".$row_cnt,$estimate_data['discount_rw_share']);

    $row_cnt++;

    /* 割引額 */
    $sheet->setCellValue("D".$row_cnt,$estimate_data['discount_nm']);
    $sheet->setCellValue("E".$row_cnt,$estimate_data['discount']);
    /*
    $sheet->setCellValue("F".$row_cnt,sprintf("=E%d / R%d)",$row_cnt,$row_cnt));
    $sheet->setCellValue("G".$row_cnt,sprintf("=E%d)",$row_cnt));
    $sheet->setCellValue("L".$row_cnt,sprintf("=G%d * O%d)",$row_cnt,$row_cnt));
    $sheet->setCellValue("M".$row_cnt,sprintf("=F%d * O%d)",$row_cnt,$row_cnt));
    $sheet->setCellValue("N".$row_cnt,sprintf("=G%d * P%d)",$row_cnt,$row_cnt));
    */
    $sheet->setCellValue("O".$row_cnt,$estimate_data['discount_aw_share']);
    $sheet->setCellValue("P".$row_cnt,$estimate_data['discount_rw_share']);
 //   $sheet->setCellValue("Q".$row_cnt,"割引額為替レート");
    $sheet->setCellValue("R".$row_cnt,$estimate_data['discount_exchange_rate']);

    $row_cnt++;

    /* 総合計 */
    /*
    $sheet->setCellValue("D".$row_cnt,"TOTAL");
    $sheet->setCellValue("F".$row_cnt,sprintf("=F%d - F%d - F%d",$row_cnt-3,$row_cnt-2,$row_cnt-1));
    $sheet->setCellValue("G".$row_cnt,sprintf("=G%d - G%d - G%d",$row_cnt-3,$row_cnt-2,$row_cnt-1));
    $sheet->setCellValue("H".$row_cnt,sprintf("=H%d)",$row_cnt-3));
    $sheet->setCellValue("I".$row_cnt,sprintf("=I%d)",$row_cnt-3));
    $sheet->setCellValue("J".$row_cnt,sprintf("=G%d - I%d",$row_cnt,$row_cnt));
    $sheet->setCellValue("K".$row_cnt,sprintf("=J%d / G%d",$row_cnt,$row_cnt));
    $sheet->setCellValue("L".$row_cnt,sprintf("=L%d - L%d - L%d",$row_cnt-3,$row_cnt-2,$row_cnt-1));
    $sheet->setCellValue("M".$row_cnt,sprintf("=M%d - M%d - M%d",$row_cnt-3,$row_cnt-2,$row_cnt-1));
    $sheet->setCellValue("N".$row_cnt,sprintf("=N%d - N%d - N%d",$row_cnt-3,$row_cnt-2,$row_cnt-1));
    */

    $row_cnt+=2;

    /* PD手配料 */
   // $sheet->setCellValue("D".$row_cnt,"PD手配料");
  //  $sheet->setCellValue("F".$row_cnt,sprintf("=M%d",$row_cnt-2));

    $row_cnt++;

    /* 州税 */
   // $sheet->setCellValue("D".$row_cnt,"州税");
   // $sheet->setCellValue("F".$row_cnt,sprintf("=(F%d + F%d) * E%d",$row_cnt-1,$row_cnt+1,$row_cnt-8));

    $row_cnt++;

    /* 現地支払額 */
   // $sheet->setCellValue("D".$row_cnt,"現地支払い額");
   // $sheet->setCellValue("F".$row_cnt,sprintf("=H%d",$row_cnt-4));

    $row_cnt++;

    /* ＲＷからPDへの振り込み額 */
   // $sheet->setCellValue("D".$row_cnt,"ＲＷからPDへの振り込み額");
   // $sheet->setCellValue("F".$row_cnt,sprintf("=SUM(F%d:F%d)",$row_cnt-3,$row_cnt-1));

    //文字Alignment
   // $sheet->getStyle("D".($row_cnt-11).":D".$row_cnt)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
}
?>