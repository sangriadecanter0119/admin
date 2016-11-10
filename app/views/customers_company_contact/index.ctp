<?php 
$url = $html->url("index");
$this->addScript($javascript->codeBlock( <<<JSPROG
$(function(){
    //表示件数の変更
   $("#limit").change(function(){  
     location.href = "$url" + "/" + $("#limit").val();
   });
   //表示件数にマッチした件数表示にする
   $("#limit option").each(function(){   
     if($(this).val() == $page_limit)
     {
       $(this).attr("selected","selected");
     }     
   });
});
JSPROG
)) ?>

<ul class="operate"></ul>

	<table class="list" cellspacing="0">
	    <tr>
		<th>問い合わせ番号</th>
		<th>顧客名</th>
		<th>手配区分</th>
		<th><a href="">依頼日</a></th>
		<th>担当</th>      
		<th>予約項目</th>
		<th>ベンダー</th>
        <th>内容区分</th>
		<th>内容</th>
		<th>依頼事項</th>
		<th>返答事項</th>
		<th>メール件名</th>
		<th><a href="">返答日</a></th>
		<th>備考</th>
	    </tr>
<?php
  $header_id= -1; 
  for($i=0;$i < count($data);$i++)
  {
  	  $atr = $data[$i]['ContactTrnView'];
  	  
  	  if($header_id != $atr['id'])
  	  {
    echo  "<tr>".	  	
	  	  "<td><a href='".$html->url('/customer_company_contact/editContact/').$atr['id']."/".$atr['customer_id']."'>{$atr['contact_no']}</a>".	  	  
	  	  "</td>".
          "<td><a href='".$html->url('/customers_list/goToCustomerInfo/').$atr['customer_id']."'>{$atr['grmls_kj']}&nbsp;{$atr['grmfs_kj']}</a></td>".
          "<td>".$common->evalNbsp($atr['contact_kbn_nm'])."</td>".
		  "<td>".$common->evalNbspForShortDate($atr['reg_dt'])."</td>".
    	  "<td>".$common->evalNbsp($atr['sender_nm'])."</td>".
		  "<td>".$common->evalNbsp($atr['goods_ctg_nm'])."</td>".
		  "<td>".$common->evalNbsp($atr['vendor_nm'])."</td>".
		  "<td>".$common->evalNbsp($atr['content_kbn_nm'])."</td>".
		  "<td>".$common->evalNbsp($atr['content'])."</td>".
    	  "<td>".$common->evalNbsp($atr['question_kbn_nm'])."</td>".
          "<td>".$common->evalNbsp($atr['answer_kbn_nm'])."</td>".
          "<td>".$common->evalNbsp($atr['title'])."</td>".
		  "<td>".$common->evalNbspForShortDate($atr['answer_dt'])."</td>".
		  "<td>".$common->evalNbsp($atr['note'])."</td>".
          "</tr>";
        $header_id = $atr['id'];
  	  }
  }
?>	   
	</table>	
	<div class="pagination">
      <?php
        echo $paginator->prev('前ページ');
        echo $paginator->numbers();
        echo $paginator->next('次ページ');
      ?>
                  表示件数:
              <select id="limit" name="limit">
                 <option value="10">10</option>
                 <option value="20">20</option>
                 <option value="30">30</option>
                 <option value="40">40</option>
                 <option value="50">50</option>
                 <option value="60">60</option>
                 <option value="70">70</option>
                 <option value="80">80</option>
                 <option value="90">90</option>
                 <option value="100">100</option>
              </select>
     </div>         


	

