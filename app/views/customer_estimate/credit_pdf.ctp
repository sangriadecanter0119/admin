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


//ステータスが仮約定以前の場合は挙式予定日を、それ以外は挙式日を表示する
if($customer['CustomerMstView']['status_id'] < CS_CONTRACTING){
  $wedding_dt = $common->evalForShortDate($customer['CustomerMstView']['wedding_planned_dt']);
}else{
  $wedding_dt= $common->evalForShortDate($customer['CustomerMstView']['wedding_dt']);
}

if($customer['CustomerMstView']['status_id'] < CS_CONTRACTING){
  $wedding_place= $common->evalNbsp($customer['CustomerMstView']['wedding_planned_place']);
}else{
  $wedding_place= $common->evalNbsp($customer['CustomerMstView']['wedding_place']);
}

if($customer['CustomerMstView']['status_id'] < CS_CONTRACTING){
  $wedding_time= $common->evalNbsp($customer['CustomerMstView']['wedding_planned_time']);
}else{
  $wedding_time= $common->evalNbsp($customer['CustomerMstView']['wedding_time']);
}


/* ヘッダ */
$html = '<div>
            <table border="0" bgcolor="#CCFFCC" color="black">
              <tr><td rowspan="2" align="left"><font size="14"><strong>   INVOICE</strong></font></td><td></td></tr>
              <tr><td align="right">' . date('Y/m/j') .'</td></tr>
            </table>
        </div>';

$html .= '<br />';

/* サブヘッダ */
$html .= '<div>
            <table border="0" cellspacing="0" cellpadding="0">
               <tr>
                 <td width="250" colspan="2" align="left">&nbsp;</td>
                 <td width="140"></td>
                 <td width="130" colspan="3" align="right"><img src="./images/title.png" border="0" width="109px" height="17px" /></td>
              </tr>
              <tr>
                 <td width="250" colspan="2" align="left">
                                      <font size="14">'.$groom_nm.'</font>&nbsp;&nbsp;
                                      <font size="14">'.$bride_nm.'</font>
                 </td>
                 <td width="270" colspan="4" align="right">Tel 03-3746-0004 Fax 03-3746-0047</td>
              </tr>
              <tr>
                     <td width="250" colspan="2" align="left">&nbsp;</td>
                     <td width="140">&nbsp;</td>
                     <td width="130" colspan="3" align="right">info@realweddings.jp</td>
              </tr>
              <tr>
                    <td width="390" colspan="3">&nbsp;</td>
                    <td width="130" colspan="3" align="right">〒106-0032</td>
              </tr>
              <tr>
                    <td width="100" >&nbsp;</td>
                    <td width="290" colspan="2" >&nbsp;</td>
                    <td width="130" colspan="3" align="right">東京都港区六本木7-15-10 5Ｆ</td>
              </tr>';
$html.= '</table></div>';

/* サブヘッダ */
$html .= '<div>
            <table border="0" cellspacing="0" cellpadding="0">';
     
         if($common->hasValue($wedding_dt) and $common->hasValue($wedding_place)){
            $html.='<tr><td>&nbsp;</td><td>&nbsp;</td></tr>
                    <tr><td>&nbsp;</td><td>&nbsp;</td></tr>
                    <tr style="font-size:10"><td width="200" colspan="3">挙式日時：<span style="text-decoration:underline">'.$wedding_dt.' '.$wedding_time.'</span></td><td align="left" width="320" colspan="3">会場：<span style="text-decoration:underline">'.$wedding_place.'</span></td></tr>';

         }elseif($common->hasValue($wedding_dt)){
             $html.='<tr><td>&nbsp;</td><td>&nbsp;</td></tr>
                     <tr><td>&nbsp;</td><td>&nbsp;</td></tr>
                     <tr><td>挙式日時：<span style="text-decoration:underline">'.$wedding_dt.' '.$wedding_time.'</span></td><td align="left">会場：</td></tr>';

         }elseif($common->hasValue($wedding_place)){
             $html.='<tr><td>&nbsp;</td><td>&nbsp;</td></tr>
                     <tr><td>&nbsp;</td><td>&nbsp;</td></tr>
                     <tr><td>挙式日時：</td><td align="left">会場：<span style="text-decoration:underline">'.$wedding_place.'</span></td></tr>';
         }

$html.= '</table></div>';

$html .= '<br /><br /><br /><br />';

/* サブヘッダ */
$html .= '<div>
            <table border="0" cellspacing="0" cellpadding="0">
              <tr>
                  <td width="100">&nbsp;</td>
                  <td width="135">下記の通りご請求申し上げます。</td>
                  <td width="50">&nbsp;</td>
                  <td width="135">&nbsp;</td>
                  <td width="100">&nbsp;</td>
              </tr>
              <tr>
                  <td width="100">&nbsp;</td>
                  <td width="135">&nbsp;</td>
                  <td width="50">&nbsp;</td>
                  <td width="135">&nbsp;</td>
                  <td width="100">&nbsp;</td>
              </tr>
              <tr>
                  <td width="100">&nbsp;</td>
                  <td width="135" align="center" style="border-bottom:1px double black"><font size="10">ご請求金額</font></td>
                  <td width="50"  style="border-bottom:1px double black">&nbsp;</td>
                  <td width="135" style="border-bottom:1px double black"><font size="12">￥'. number_format($credit_amount).'</font></td>
                  <td width="100">&nbsp;</td>
              </tr>';
$html.= '</table></div>';

$html .= '<br /><br /><br /><br /><br />';

/* 補足説明 */
$html .= '<div style="border:1px solid black;">
               <table border="0" color="black">
                 <tr align="center"><td>&nbsp;</td></tr>
                 <tr align="center"><td><font size="12">'.substr($credit_deadline,0,4).'年'.substr($credit_deadline,4,2).'月'.substr($credit_deadline,6,2).'日までに下記口座へお振り込みください。</font></td></tr>
                 <tr align="center"><td><font size="12">三井住友銀行　六本木支店　普通7348176　エンプレス株式会社</font></td></tr>
                 <tr align="center"><td><font size="12">＊恐れ入りますが、お振込み手数料はお客様でご負担下さい。</font></td></tr>
               </table>
          </div>';

/* フッター 
$html .= '<div bgcolor="#CCFFCC" color="black" align="center">
              <font size="10">www.realweddings.jp</font>
          </div>';
*/

$html .= '<div>
            <table border="0" bgcolor="#CCFFCC" color="black">
              <tr><td align="center"><font size="10">www.realweddings.jp</font></td></tr>
            </table>
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