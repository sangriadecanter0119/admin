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
    // 列番
    $row_cnt = 3;
    // 行番
    $col_cnt = 4;

    $row_cnt++;

     /* ヘッダ作成  */
      $estimate_id = -1;
      $customer_pos = array();

      for($i=0;$i < count($data);$i++){
      	/* 重複して顧客が取得されているのでユニークな顧客名のみを抽出してヘッダとする */
       	if(in_array($data[$i]["EstimateDtlTrnView"]['customer_id'],$customer_pos)==false){
      	   $sheet->setCellValueByColumnAndRow($col_cnt, $row_cnt, $data[$i]["EstimateDtlTrnView"]['grmls_kj']."様 ".$common->evalNbspForDayOnly($data[$i]["EstimateDtlTrnView"]['wedding_dt']));
           $estimate_id = $data[$i]["EstimateDtlTrnView"]['estimate_id'];
           //顧客IDを保持
           $customer_pos[]=$data[$i]["EstimateDtlTrnView"]['customer_id'];
           $col_cnt++;
      	}
      }
      $sheet->setCellValueByColumnAndRow($col_cnt, $row_cnt, "合計");

     $row_cnt++;

     //顧客毎の支払合計値を集計するための準備
     $customer_sales = array();
     for($index=0;$index < count($customer_pos);$index++){
     	$customer_sales[$customer_pos[$index]] = 0;
     }

     /* コンテンツ作成 */
      $goods_ctg_id = -1;
      $goods_id = -1;
      $total_of_goods_price = 0;
      for($i=0;$i < count($data_by_category);$i++){

      	/* 仲介業者経由から手配した時のみ対象とする */
      	if($data_by_category[$i]["EstimateDtlTrnView"]['payment_kbn_id'] == PC_INDIRECT_ABOARD_PAY){

          /* 商品カテゴリ、商品ID順にソートしてあるので商品IDが前回と変われば新規の行に移る */
      	  if($data_by_category[$i]["EstimateDtlTrnView"]['goods_id'] != $goods_id){

      	  /* 新しい商品カテゴリに入ったらカテゴリ名を設定する */
      	  if($data_by_category[$i]["EstimateDtlTrnView"]['goods_ctg_id'] != $goods_ctg_id){
      	  	if($row_cnt != 5){
      	        $sheet->getStyle("B".$row_cnt.":".$cols[$col_cnt].$row_cnt)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_DASHED);
      	        $row_cnt++;
      	  	}
      	  	$sheet->setCellValue("B".$row_cnt, $data_by_category[$i]["EstimateDtlTrnView"]['goods_ctg_nm']);
      	    $goods_ctg_id = $data_by_category[$i]["EstimateDtlTrnView"]['goods_ctg_id'];
      	  }

      	  $goods_id = $data_by_category[$i]["EstimateDtlTrnView"]['goods_id'];
      	  $sheet->setCellValue("C".$row_cnt, $data_by_category[$i]["EstimateDtlTrnView"]['goods_nm']);
          $sheet->setCellValue("D".$row_cnt, $data_by_category[$i]["EstimateDtlTrnView"]['vendor_nm']);
          /* ヘッダの顧客名順に配列に顧客IDが設定されているので同一顧客同一商品があれば金額を設定する */
      	  $col_cnt = 4;
          for($j=0;$j < count($customer_pos);$j++){

             /* 新たに注文明細データを調べる */
          	 for($sub_index=0;$sub_index < count($data_by_category);$sub_index++){

          	 	   if($customer_pos[$j] == $data_by_category[$sub_index]["EstimateDtlTrnView"]['customer_id'] &&
          	 	      $goods_id         == $data_by_category[$sub_index]["EstimateDtlTrnView"]['goods_id']){

                     // $total_cost = $data_by_category[$sub_index]["EstimateDtlTrnView"]['sales_cost'] * $data_by_category[$sub_index]["EstimateDtlTrnView"]['num'];
                      $total_cost = $data_by_category[$sub_index]["EstimateDtlTrnView"]['total_sales_cost'];

                      $sheet->setCellValueByColumnAndRow($col_cnt, $row_cnt, $total_cost);
        	          //顧客別の合計値を抽出するために金額を加算する
        	          $customer_sales[$customer_pos[$j]] += $total_cost;
        	          $total_of_goods_price += $total_cost;
        	          break;
        	      }
             }
             $col_cnt++;
          }
            $sheet->setCellValueByColumnAndRow($col_cnt, $row_cnt, $total_of_goods_price);
            $total_of_goods_price = 0;
            $row_cnt++;
      	}
      }
     }

    /* フッター作成  */
     $sheet->setCellValue("C".$row_cnt, "合計");

     $total = 0;
     $col_cnt = 4;
     for($j=0;$j < count($customer_pos);$j++){
     	$sheet->setCellValueByColumnAndRow($col_cnt, $row_cnt, $customer_sales[$customer_pos[$j]]);
        $total += $customer_sales[$customer_pos[$j]];
        $col_cnt++;
     }
     $sheet->setCellValueByColumnAndRow($col_cnt, $row_cnt, $total);
     /* 羅線 */
     $sheet->getStyle("B4:".$cols[$col_cnt].$row_cnt)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
     $sheet->getStyle("B4:".$cols[$col_cnt].$row_cnt)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
     $sheet->getStyle("B4:".$cols[$col_cnt].$row_cnt)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
     $sheet->getStyle("B4:".$cols[$col_cnt].$row_cnt)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);

     $sheet->getStyle("B4:".$cols[$col_cnt]."4")->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
     $sheet->getStyle("B4:B".$row_cnt)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
     $sheet->getStyle($cols[$col_cnt]."4:".$cols[$col_cnt].$row_cnt)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
     $sheet->getStyle("B".$row_cnt.":".$cols[$col_cnt].$row_cnt)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_DOUBLE);

    // PHPの日時出力
    $time = time();                 // 現在日時(Unix Timestamp)
    $sheet->setCellValueByColumnAndRow($col_cnt, "2", date('Y/m/d'));
   // $sheet->getStyle( $col_cnt . "1")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDDSLASH);
       //Alignment
       //$sheet->getStyle('C3:Y'.$row_cnt)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
       //$sheet->getStyle('A3:B'.$row_cnt)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

       //$sheet->removeRow($row_cnt, 1);



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


?>