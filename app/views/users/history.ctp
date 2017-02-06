
  <script type="text/javascript">
     $(function(){
         $("#user_names").change(function(){
           $("#UserHistoryTrnUserName").val($(this).val());
           $("#UserHistoryForm").submit();
         });
     });
  </script>

    <ul class="operate">
     <li><a href="<?php echo $html->url('/systemManager/userMaster') ?>">一覧に戻る</a></li>
    </ul>

     <!--  ページネーション  -->
     <?php
     echo $paginator->counter(array('format' => '%count%件中%start% ~ %end%件表示中 '));
     echo $paginator->numbers (
	     array (
	   	         'before' => $paginator->hasPrev() ? $paginator->first('<<').' | ' : '',
		         'after' => $paginator->hasNext() ? ' | '.$paginator->last('>>') : '',
	           )
      );
    ?>

    <!-- ページネーションする時にフィルタ条件を引き継ぐためにパラメータを追加 -->
    <?php  $paginator->options(array('url' => array('user_name' => $user_id))); ?>

     <!-- フィルター用の条件を保持   -->
    <div style="display:none;">
    <?php echo $form->create(null); ?>
	<?php echo $form->text('UserHistoryTrn.user_name',array('value' => $user_name)); ?>
    <?php echo $form->end(); ?>
    </div>

   <div style="overflow:auto; width:100%; height:100%; padding:0px 0px 15px 0px;" >
    <table class="filterlist" cellspacing="0">
        <tr>
		<td>
		 <select id='user_names'>
		    <option value='-1' selected>ALL</option>
		   <?php
		  	for($i=0;$i < count($user_names);$i++)
		    {
		      $atr = $user_names[$i]['users'];
		  	  if($atr['id'] == $user_id){
		  	  	 echo "<option value='".$atr['id']."' selected>{$atr['username']}</option>";
		  	  }else{
		         echo "<option value='".$atr['id']."'>{$atr['username']}</option>";
		  	  }
		    }
		   ?>
		 </select>
		</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		</tr>

        <tr>
		  <th>ユーザ名</th>
		  <th>ユーザ表示名</th>
		  <th>ログイン日時</th>
		</tr>

		<?php
		  	for($i=0;$i < count($data);$i++){

		  	$atr = $data[$i]['LoginHistoryTrn'];
		  	echo "<tr>".
		  	         "<td>".$common->evalNbsp($atr['username'])."</td>".
		  	         "<td>".$common->evalNbsp($atr['display_nm'])."</td>".
	                 "<td>".$common->evalNbspForLongDate($atr['login_dt'])."</td>".
		  	      "</tr>";
		  }
		?>
     </table>
   </div>

