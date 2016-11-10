<?php 
/*
App::import('Vendor','tcpdf');  
$tcpdf = new TCPDF(); 
$textfont = 'freesans'; // 見た目もいいし、きれいで、「dejavusans」よりスリムです。 
//日本語フォントの指定についてはこの記事の末尾を参照してください。
 
$tcpdf->SetAuthor("KBS Homes & Properties at http://kbs-properties.com"); 
$tcpdf->SetAutoPageBreak( false ); 
$tcpdf->setHeaderFont(array($textfont,'',40)); 
$tcpdf->headercolor = array(150,0,0); 
$tcpdf->headertext = 'KBS Homes & Properties'; 
$tcpdf->footertext = 'Copyright Â© %d KBS Homes & Properties. All rights reserved.'; 
 
// ページを追加（最近のバージョンのtcpdfで必要になった）
$tcpdf->AddPage(); 
 
// ページの内容の位置を決めプリントする。
// 例：
$tcpdf->SetTextColor(0, 0, 0); 
$tcpdf->SetFont($textfont,'B',20); 
$tcpdf->Cell(0,14, "Hello World", 0,1,'L'); 
// ... 
// など。
// TCPDF の例を参照。
 
echo $tcpdf->Output('filename.pdf', 'D'); 
*/

// set document information
$tcpdf->SetCreator(PDF_CREATOR);
$tcpdf->SetAuthor("Nicola Asuni");
$tcpdf->SetTitle("TCPDF Example 002");
$tcpdf->SetSubject("TCPDF Tutorial");
$tcpdf->SetKeywords("TCPDF, PDF, example, test, guide");

// remove default header/footer
$tcpdf->setPrintHeader(false);
$tcpdf->setPrintFooter(false);

//set margins
$tcpdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);

//set auto page breaks
$tcpdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

//set image scale factor
$tcpdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

//set some language-dependent strings
//$tcpdf->setLanguageArray($l);

//initialize document
$tcpdf->AliasNbPages();

// add a page
$tcpdf->AddPage();

// ---------------------------------------------------------

// set font
$tcpdf->SetFont("dejavusans", "BI", 20);
//$tcpdf->SetFont("ipag", "BI", 20);

// print a line using Cell()
$tcpdf->Cell(0,10,"Example 002",1,1,'C');
$tcpdf->Cell(0,10,"test",1,1,'C');

// ---------------------------------------------------------

//Close and output PDF document
$tcpdf->Output("example_002.pdf", "I");
 
?>