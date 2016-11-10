<ul class="operate">
  <li><a href="<?php echo $html->url('editCustomer').'/'.$data['CustomerMstView']['id'] ?>">編集</a></li>
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
	         //ステータスが成約以前の場合は挙式予定日を、それ以外は挙式日を表示する
	         if($data['CustomerMstView']['status_id'] < CS_CONTRACTED){
	         	echo $common->evalForShortDate($data['CustomerMstView']['wedding_planned_dt']);
	         }else{
	         	echo $common->evalForShortDate($data['CustomerMstView']['wedding_dt']);
	         }
	       ?>
	       </td>
	       <th>挙式会場：</th><td class="long"><?php echo $common->evalNbsp($data['CustomerMstView']['wedding_place']) ?></td>
	       <th>時間：</th><td class="short"><?php echo $common->evalNbsp($data['CustomerMstView']['wedding_time']) ?></td>
	       <th>レセプション会場：</th><td class="long"><?php echo $common->evalNbsp($data['CustomerMstView']['reception_place']) ?></td>
	       <th>時間：</th><td class="short"><?php echo $common->evalNbsp($data['CustomerMstView']['reception_time']) ?></td>
	    </tr>
	    <tr>
	       <th>問い合わせ日:</th><td><?php echo $common->evalNbspForShortDate($data['CustomerMstView']['first_contact_dt']) ?></td>
	       <th>新規接客日:</th><td><?php echo $common->evalNbspForShortDate($data['CustomerMstView']['first_visited_dt']) ?></td>
	       <th>見積発行日:</th>  <td><?php echo $common->evalNbspForShortDate($data['CustomerMstView']['estimate_issued_dt']) ?></td>
	       <th>仮約定日：</th><td><?php echo $common->evalNbspForShortDate($data['CustomerMstView']['contracting_dt']) ?></td>
	       <th>成約日：</th>  <td><?php echo $common->evalNbspForShortDate($contracted_dt) ?></td>
	       <th>請求書発行日：</th><td><?php echo $common->evalNbspForShortDate($invoice_issued_dt) ?></td>
           <th></th><td></td>
	    </tr>
	    <tr>
	       <th>導線1：</th><td><?php echo $leading1_list[$data['CustomerMstView']['leading1']] ?></td>
	       <th>導線2：</th><td><?php echo $leading2_list[$data['CustomerMstView']['leading2']] ?></td>
           <th>紹介者:</th><td colspan="3"><?php echo $data['CustomerMstView']['introducer'] ?></td>
	       <th></th><td></td>
	       <th></th><td></td>
           <th></th><td></td>
	    </tr>
	     <tr>
	       <th>登録日：</th>  <td><?php echo $common->evalNbspForShortDate($data['CustomerMstView']['reg_dt']) ?></td>
	       <th>登録者名：</th><td><?php echo $data['CustomerMstView']['reg_nm']?></td>
           <th>新規担当者:</th>  <td><?php echo $data['CustomerMstView']['first_contact_person_nm'] ?></td>
	       <th>プラン担当者：</th><td><?php echo $data['CustomerMstView']['process_person_nm'] ?></td>
           <th>更新日：</th>  <td><?php echo $common->evalNbspForShortDate($data['CustomerMstView']['upd_dt']) ?></td>
	       <th>更新者名：</th><td><?php echo $common->evalNbsp($data['CustomerMstView']['upd_nm']) ?></td>
           <th></th><td></td>
	    </tr>
	  </table>
	</fieldset>
  </td>
</tr>
<tr>
  <td style="padding:0px 0px 8px 0px;">
    <fieldset class="headerlegend">
      <legend class="legendtitle">顧客対応履歴</legend>

	  <table class="list" cellspacing="0" style="margin:10px;width:98%;">
	    <tr>
	       <th>日付</th>
	       <th>アクション</th>
	       <th>メモ</th>
	    </tr>
	    <?php
	     if(count($customer_process_data) == 0){
	         echo "<tr><td  colspan='3'>データはありません。</td></tr>";
	     }else{
	      for($i=0;$i < count($customer_process_data);$i++){
             $attr = $customer_process_data[$i]['CustomerProcessTrn'];
             echo "<tr>";
             echo "<td>".$common->evalNbspForShortDate($attr['action_dt'])."</td>";
             echo "<td>".$common->evalNbsp($attr['action_nm'])."</td>";
             echo "<td>".$common->evalNbsp($attr['note'])."</td>";
             echo "</tr>";
          }
	     }
	    ?>
	  </table>
	</fieldset>
  </td>
</tr>
<tr>
  <td>
    <table class="view" cellspacing="0">
       <tr><th>【新郎】</th><td>&nbsp;</td><th>【新婦】</th><td>&nbsp;</td></tr>
	   <tr>
	       <th>名前<?php if($data['CustomerMstView']['prm_lastname_flg'] == 0){ echo "<span class='primary'>【代表】</span>"; } ?></th><td><?php echo $common->evalNbsp($data['CustomerMstView']['grmls_kj']).' '.$common->evalNbsp($data['CustomerMstView']['grmfs_kj']) ?></td>
	       <th>名前<?php if($data['CustomerMstView']['prm_lastname_flg'] == 1){ echo "<span class='primary'>【代表】</span>"; } ?></th><td><?php echo $common->evalNbsp($data['CustomerMstView']['brdls_kj']).' '.$common->evalNbsp($data['CustomerMstView']['brdfs_kj']) ?></td>
	   </tr>
	   <tr>
	       <th>カナ</th><td><?php echo $common->evalNbsp($data['CustomerMstView']['grmls_kn']).' '.$common->evalNbsp($data['CustomerMstView']['grmfs_kn']) ?></td>
	       <th>カナ</th><td><?php echo $common->evalNbsp($data['CustomerMstView']['brdls_kn']).' '.$common->evalNbsp($data['CustomerMstView']['brdfs_kn']) ?></td>
	   </tr>
       <tr>
           <th>ローマ字</th><td><?php echo $common->evalNbsp($data['CustomerMstView']['grmls_rm']).' '.$common->evalNbsp($data['CustomerMstView']['grmfs_rm']) ?></td>
           <th>ローマ字</th><td><?php echo $common->evalNbsp($data['CustomerMstView']['brdls_rm']).' '.$common->evalNbsp($data['CustomerMstView']['brdfs_rm']) ?></td>
       </tr>
       <tr>
           <th>誕生日</th><td><?php echo $common->evalNbspForShortDate($data['CustomerMstView']['grmbirth_dt']) ?></td>
           <th>誕生日</th><td><?php echo $common->evalNbspForShortDate($data['CustomerMstView']['brdbirth_dt']) ?></td>
       </tr>
       <tr>
           <th>郵便番号</th><td><?php echo $common->evalNbsp($data['CustomerMstView']['grm_zip_cd']) ?></td>
           <th>郵便番号</th><td><?php echo $common->evalNbsp($data['CustomerMstView']['brd_zip_cd']) ?></td>
       </tr>
	   <tr>
	       <th>住所<?php if($data['CustomerMstView']['prm_address_flg'] == 0){ echo "<span class='primary'>【代表】</span>"; } ?></th>
	       <td><?php echo $common->evalNbsp($data['CustomerMstView']['grm_address']) ?></td>
	       <th>住所<?php if($data['CustomerMstView']['prm_address_flg'] == 1){ echo "<span class='primary'>【代表】</span>"; } ?></th>
	       <td><?php echo $common->evalNbsp($data['CustomerMstView']['brd_address']) ?></td>
	   </tr>
	   <tr>
	       <th>住所（ローマ字）</th><td><?php echo $common->evalNbsp($data['CustomerMstView']['grm_address_rm']) ?></td>
	       <th>住所（ローマ字）</th><td><?php echo $common->evalNbsp($data['CustomerMstView']['brd_address_rm']) ?></td>
	   </tr>
	   <tr>
	      <th>電話番号<?php if($data['CustomerMstView']['prm_phone_no_flg'] == 0){ echo "<span class='primary'>【代表】</span>"; } ?></th>
	      <td><?php echo $common->evalNbsp($data['CustomerMstView']['grm_phone_no'])?></td>
	      <th>電話番号<?php if($data['CustomerMstView']['prm_phone_no_flg'] == 1){ echo "<span class='primary'>【代表】</span>"; } ?></th>
	      <td><?php echo $common->evalNbsp($data['CustomerMstView']['brd_phone_no'])?></td>
	   </tr>
	   <tr>
	       <th>携帯電話番号<?php if($data['CustomerMstView']['prm_phone_no_flg'] == 0){ echo "<span class='primary'>【代表】</span>"; } ?></th>
	       <td><?php echo $common->evalNbsp($data['CustomerMstView']['grm_cell_no']) ?></td>
	       <th>携帯電話番号<?php if($data['CustomerMstView']['prm_phone_no_flg'] == 1){ echo "<span class='primary'>【代表】</span>"; } ?></th>
	       <td><?php echo $common->evalNbsp($data['CustomerMstView']['brd_cell_no']) ?></td>
	   </tr>
	   <tr>
	       <th>E-MAILアドレス<?php if($data['CustomerMstView']['prm_email_flg'] == 0){ echo "<span class='primary'>【代表】</span>"; } ?></th>
	       <td><?php echo $common->evalNbsp($data['CustomerMstView']['grm_email'])?></td>
	       <th>E-MAILアドレス<?php if($data['CustomerMstView']['prm_email_flg'] == 1){ echo "<span class='primary'>【代表】</span>"; } ?></th>
	       <td><?php echo $common->evalNbsp($data['CustomerMstView']['brd_email'])?></td>
	   </tr>
       <tr>
           <th>携帯メールアドレス</th><td><?php echo $common->evalNbsp($data['CustomerMstView']['grm_phone_mail'])?></td>
           <th>携帯メールアドレス</th><td><?php echo $common->evalNbsp($data['CustomerMstView']['brd_phone_mail'])?></td>
       </tr>
	   <tr>
	       <th>備考</th><td colspan="3"><?php echo $common->evalNbsp(nl2br($data['CustomerMstView']['note']))?></td>
	   </tr>
    </table>
  </td>
</tr>
</table>

