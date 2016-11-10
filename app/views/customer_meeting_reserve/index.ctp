<ul class="operate">
<li><a href="<?php echo $html->url('addCustomerSchedule') ?>">追加</a></li>
</ul>
<table class="content">
<tr>
  <td style="padding:0px 0px 8px 0px;">
    <fieldset class="headerlegend">
      <legend class="legendtitle">基本事項</legend>
   
	  <table class="viewheader">
	    <tr>
	       <th>顧客番号：</th><td class="short"><?php echo $data['CustomerMstView']['customer_cd'] ?></td>
	       <th>ステータス：</th><td class="short"><?php echo $common->evalNbsp($data['CustomerMstView']['status_nm'])?></td>	  
	       <th>挙式日：</th><td class="short">     
	        <?php 
	         //ステータスが成約以前の場合は挙式日は挙式予定日として顧客マスタに登録し、それ以外は契約テーブルに挙式日として登録する
	         if($data['CustomerMstView']['status_id'] < CS_CONTRACT)
	         {
	         	echo $common->evalForShortDate($data['CustomerMstView']['wedding_planned_dt']);
	         }
	         else 
	         {
	         	echo $common->evalForShortDate($data['CustomerMstView']['wedding_dt']);
	         }
	       ?> 	      
	       </td>
	       <th>挙式会場：</th><td class="long"><?php echo $common->evalNbsp($data['CustomerMstView']['wedding_place']) ?></td>
	       <th>時間：</th><td class="short"><?php echo $common->evalForTime($data['CustomerMstView']['wedding_time']) ?></td>
	       <th>レセプション会場：</th><td class="long"><?php echo $common->evalNbsp($data['CustomerMstView']['reception_place']) ?></td>
	       <th>時間：</th><td class="short"><?php echo $common->evalForTime($data['CustomerMstView']['reception_time']) ?></td>
	    </tr>	   
	  </table>
	</fieldset>
  </td>
</tr>
</table>
<table class="list" cellspacing="0">

	  <tr>
		<th><a href="">No</a></th>
		<th><a href="">来店日</a></th>
		<th><a href="">担当者</a></th>
		<th>タイトル</th>	
		<th>打ち合わせ内容</th>	
		<th>作成者</th>
		<th>作成日</th>
		<th>更新者</th>
		<th>更新日</th>		
	  </tr>

     <?php
       for($i=0;$i < count($customer_schedule);$i++)
       {
       	$atr = $customer_schedule[$i];
       	echo "<tr>".
       	     "<td><a href='".$html->url('editCustomerSchedule')."/".$atr['CustomerScheduleTrnView']['id']."'>".($i+1)."</a></td>".
       	     "<td>".date('Y/m/d H:i',strtotime($atr['CustomerScheduleTrnView']['start_dt']))."</td>".
       	     "<td>".$atr['CustomerScheduleTrnView']['username']."</td>".
       	     "<td>".$atr['CustomerScheduleTrnView']['title']."</td>".
       	     "<td>".$common->evalNbsp($atr['CustomerScheduleTrnView']['note'])."</td>".       	   
             "<td>".$common->evalNbsp($atr['CustomerScheduleTrnView']['reg_nm'])."</td>".
       	     "<td>".$common->evalNbspForShortDate($atr['CustomerScheduleTrnView']['reg_dt'])."</td>".
             "<td>".$common->evalNbsp($atr['CustomerScheduleTrnView']['upd_nm'])."</td>".
       	     "<td>".$common->evalNbspForShortDate($atr['CustomerScheduleTrnView']['upd_dt'])."</td>".
		   	 "</tr>";
       }     
     ?>   
</table>     

