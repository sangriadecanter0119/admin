<?php
//====================================================+
// File name   :
// Begin       :
// Last Update :
// Description :
// Author      :
//
// 参考URL      : http://www.monzen.org/Refdoc/tcpdf/
//====================================================+

/**
 *  @since
 *  @author
 *
 */

App::import( 'Vendor', 'TCPDF', array('file'=>'phpexcel' . DS . 'PHPExcel' . DS . 'Shared'. DS . 'PDF'. DS . 'tcpdf.php') );

/**
 * TCPDF インスタンス化
 * 引数１：用紙方向(L=横, P=縦)
 * 引数２：単位(mm, cm, pt=ポイント, in=インチ)
 * 引数３：用紙の大きさ(An, Bn...)
 */
$obj = new TCPDF('P', 'mm', 'A4');
//デフォルトだと空のページヘッダーが線となって表示されるので消す
$obj->setPrintHeader( false );
$obj->setPrintFooter( false );
//set auto page breaks
//$obj->SetAutoPageBreak(false, PDF_MARGIN_BOTTOM);


/**
 * 日本語フォントを指定
 * 引数１：フォント名
 * 引数２：スタイル(空文字=標準, B=太字, I=斜体, U=下線付, D=取消線付)
 * 引数３：サイズ
 */
$obj->SetFont('arialunicid0-japanese', '', 8);

//書き出し対象のページを準備
$obj->AddPage();

$groom_nm="";
$bride_nm="";
if($estimate_header[0]['EstimateTrnView']['grmls_kj'].$estimate_header[0]['EstimateTrnView']['grmfs_kj'] != null){
	$groom_nm = $estimate_header[0]['EstimateTrnView']['grmls_kj'].$estimate_header[0]['EstimateTrnView']['grmfs_kj'].'様  ';
}
if($estimate_header[0]['EstimateTrnView']['brdls_kj'].$estimate_header[0]['EstimateTrnView']['brdfs_kj'] != null){
	$bride_nm = $estimate_header[0]['EstimateTrnView']['brdls_kj'].$estimate_header[0]['EstimateTrnView']['brdfs_kj'].'様  ';
}

/* 判子 */
$img_x = 185;
$img_y = 34;
$img_w = 15;
$img_h = 15;
$obj->Image("./images/hanko.png",$img_x,$img_y,$img_w,$img_h);

$tax_rate = $estimate_header[0]['EstimateTrnView']['hawaii_tax_rate'] * 100;
$tax      = round($estimate_header[0]['EstimateTrnView']['yen_tax']);
$service  = round($estimate_header[0]['EstimateTrnView']['service_yen_fee']);
$service_rate  = $estimate_header[0]['EstimateTrnView']['service_rate'] * 100;
$subtotal = $estimate_header[0]['EstimateTrnView']['yen'];
$discountA = round($estimate_header[0]['EstimateTrnView']['discount_yen']);
$discount_rate = $estimate_header[0]['EstimateTrnView']['discount_rate'] * 100;
$discountB = $estimate_header[0]['EstimateTrnView']['discount'];
$total    = $estimate_header[0]['EstimateTrnView']['total_yen'];
$is_discounted = ($discountA <= 0 && $discountB <= 0) ? false : true;

/* ヘッダ */
$html = '<div>
            <table border="0" bgcolor="#CCFFCC" color="black">
              <tr><td rowspan="2" align="left"><font size="14"><strong>   INVOICE</strong></font></td><td></td></tr>
              <tr><td align="right">' . date('Y/m/j') .'</td></tr>
            </table>
        </div>';

/* サブヘッダ */
$html .= '<div>
            <table border="0" cellspacing="0" cellpadding="0">
               <tr>
                  <td width="250" colspan="2" align="left">
                                      <font size="14">'.$groom_nm.'</font>&nbsp;&nbsp;
                                      <font size="14">'.$bride_nm.'</font>
                 </td>
                 <td width="140"></td>
                 <td width="130" colspan="3" align="right"><img src="./images/title.png" border="0" width="109px" height="17px" /></td>
              </tr>
              <tr>
                     <td width="250" colspan="2">&nbsp;</td>
                     <td width="270" colspan="4" align="right">Tel 03-3746-0004 Fax 03-3746-0047</td>
              </tr>
              <tr>
                     <td width="250" colspan="2" align="left">下記の通りご請求申し上げます。</td>
                     <td width="140">&nbsp;</td>
                     <td width="130" colspan="3" align="right">info@realweddings.jp</td>
              </tr>
              <tr>
                    <td width="390" colspan="3">&nbsp;</td>
                    <td width="130" colspan="3" align="right">〒106-0032</td>
              </tr>
              <tr>
                    <td width="100" align="right" style="border-bottom:1px double black"><font size="10">ご請求金額</font></td>';

              if($is_discounted){
              	$html .='<td width="290" colspan="2" align="center" style="border-bottom:1px dash black"><font size="14">￥'.number_format((($subtotal + $tax + $service)-$discountA-$discountB)- $credit_amount).'  (税込)</font></td>';
              }else{
	            $html .='<td width="290" colspan="2" align="center" style="border-bottom:1px dash black"><font size="14">￥'.number_format(($subtotal + $tax + $service) - $credit_amount).'  (税込)</font></td>';
              }

        $html .='<td width="130" colspan="3" align="right">東京都港区六本木7-15-10 5Ｆ</td>
              </tr>
           </table>
        </div>
        <div align="left">内訳</div>';

/* 明細 */
$html.= '<div><table border="1" cellspacing="0" cellpadding="2">
	        <tr align="center">
	        <th width="100">項目</th>
		    <th width="310">内容</th>
		    <th width="50">単価</th>
		    <th width="25">個数</th>
		    <th width="55">計</th>
	      </tr>';

     /* 同じ商品コードは１つの注文に束ねる  */
    for($main=0;$main < count($estimate_dtl);$main++)
    {
    	for($sub=$main+1;$sub < count($estimate_dtl);$sub++)
        {
        	//自分自身以外の見積
        	if($estimate_dtl[$main]['EstimateDtlTrnView']['id'] != $estimate_dtl[$sub]['EstimateDtlTrnView']['id'] ){
        	   //同じ商品コード
        	   if($estimate_dtl[$main]['EstimateDtlTrnView']['goods_cd'] == $estimate_dtl[$sub]['EstimateDtlTrnView']['goods_cd'] ){

        	   	//商品カテゴリがTransportation(Guest)又はReceptionTransportationの場合のみ集約
        	   	if($estimate_dtl[$main]['EstimateDtlTrnView']['goods_ctg_id'] == GC_TRANS_GST ||
        	   	   $estimate_dtl[$main]['EstimateDtlTrnView']['goods_ctg_id'] == GC_RECEPTION_TRANS ){
        	       //個数を追加
        	       $estimate_dtl[$main]['EstimateDtlTrnView']['num'] += $estimate_dtl[$sub]['EstimateDtlTrnView']['num'];
        	       //一致した商品の片方は見積対象外とする
        	       $estimate_dtl[$sub]['EstimateDtlTrnView']['del_kbn'] = true;
        	  	}
        	  }
           }
        }
    }

    // データ
    for($i=0;$i < count($estimate_dtl);$i++)
    {
       if($estimate_dtl[$i]['EstimateDtlTrnView']['del_kbn']==false){
         $html .= '<tr><td>'                    . $estimate_dtl[$i]['EstimateDtlTrnView']['goods_ctg_nm']            . '</td>'.
                      '<td>'                    . '【'. $estimate_dtl[$i]['EstimateDtlTrnView']['goods_kbn_nm'].'】'.'<br />&nbsp;&nbsp;'.str_replace ("\n", "<br />&nbsp;&nbsp;", $estimate_dtl[$i]['EstimateDtlTrnView']['sales_goods_nm']).'</td>'.
                      '<td align="right">￥'    . number_format($estimate_dtl[$i]['EstimateDtlTrnView']['yen_price']). '</td>'.
                      '<td align="right">'      . (int)$estimate_dtl[$i]['EstimateDtlTrnView']['num']                 . '</td>'.
                      '<td align="right">￥' . number_format($estimate_dtl[$i]['EstimateDtlTrnView']['yen_price'] * (int)$estimate_dtl[$i]['EstimateDtlTrnView']['num']) . '</td></tr>';
      }
    }

$html .= '<tr><td colspan="4" align="right">小計</td><td align="right">￥' . number_format($subtotal) . '</td></tr>' .
         '<tr><td colspan="4" align="right">ハワイ州税</td><td align="right">￥'. number_format($tax) .'</td></tr>' .
         '<tr><td colspan="4" align="right">'.$estimate_dtl[0]['EstimateDtlTrnView']['service_rate_nm'].'</td><td align="right">￥'. number_format($service) .'</td></tr>';

          //割引有の請求書フォーマットで計算
          if($is_discounted){

          	$html .=  '<tr><td colspan="4" align="right">小計</td><td align="right">￥'. number_format($subtotal + $tax + $service) .'</td></tr>' ;
          	//割引率が適用されている
          	if($discountA > 0){
          		$html .= '<tr><td colspan="4" align="right">'.$estimate_dtl[0]['EstimateDtlTrnView']['discount_rate_nm'] .'['. $discount_rate .'%]</td><td align="right">(￥' .number_format($discountA) . ')</td></tr>';
          	}
          	//割引額が適用されている
          	if($discountB > 0){
          		$html .='<tr><td colspan="4" align="right">'.$estimate_dtl[0]['EstimateDtlTrnView']['discount_nm'].'</td><td align="right">(￥' .number_format($discountB) . ')</td></tr>' ;
          	}
          	$html .='<tr><td colspan="4" align="right">合計</td><td align="right">￥' . number_format(($subtotal + $tax + $service)-$discountA-$discountB) . '</td></tr>' ;
          	if($credit_amount > 0){
          		$html .= '<tr><td colspan="4" align="right">ご入金金額</td><td align="right">(￥' . number_format($credit_amount) . ')</td></tr>'.
          		         '<tr><td colspan="4" align="right">合計</td><td align="right">￥' . number_format((($subtotal + $tax + $service)-$discountA-$discountB)- $credit_amount) . '</td></tr>' ;
          	}

          //割引無の請求書フォーマットで計算
          }else{

          	$html .= '<tr><td colspan="4" align="right">合計</td><td align="right">￥'. number_format($subtotal + $tax + $service) .'</td></tr>' ;
          	if($credit_amount > 0){
          		$html .= '<tr><td colspan="4" align="right">ご入金金額</td><td align="right">(￥' . number_format($credit_amount) . ')</td></tr>'.
            	         '<tr><td colspan="4" align="right">ご請求金額合計</td><td align="right">￥' . number_format(($subtotal + $tax + $service) - $credit_amount) . '</td></tr>';
          	}
          }

$html .= '</table></div>';

/* 補足説明 */
$html .= '<div style="border:1px solid black;">
               <table border="0" color="black">
                 <tr align="center"><td>&nbsp;</td></tr>
                 <tr align="center"><td><font size="12">'.substr($invoice_deadline,0,4).'年'.substr($invoice_deadline,4,2).'月'.substr($invoice_deadline,6,2).'日までに下記口座へお振り込みください。</font></td></tr>
                 <tr align="center"><td><font size="12">三井住友銀行　六本木支店　普通7348176　エンプレス株式会社</font></td></tr>
                 <tr align="center"><td><font size="12">＊恐れ入りますが、お振込み手数料はお客様でご負担下さい。</font></td></tr>
               </table>
          </div>';

/* フッター */
$html .= '<div bgcolor="#CCFFCC" color="black" align="center">
              www.realweddings.jp
          </div>';


$obj->writeHTML($html, true, 0, true, 0);

//改行
//$obj->Ln();

/**
 * 引数１：ファイル名
 * 引数２：出力先(I=ブラウザ, D=ダウンロード, F=ファイル保存, S=文字列として出力)
 */
$out = $obj->Output($filename, "D");
?>