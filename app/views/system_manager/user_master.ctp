    <ul class="operate">
     <li><a href="<?php echo $html->url('/systemManager') ?>">戻る</a></li>
     <?php
        /* ユーザー追加は管理者のみ */
        if(UC_ADMIN == $user['User']['user_kbn_id']){
          echo "<li><a href='{$html->url('/users/addUser')}'>追加</a></li>";
        }
     ?>
    </ul>

     <table class="list" cellspacing="0">

		<tr>
		<th>名前</th>
		<th>表示名</th>
		<th>ユーザー区分</th>
		<th>E-MAIL</th>
		<th>備考</th>
		<th>最終ログイン日時</th>
		<th>登録者</th>
		<th>登録日</th>
		<th>更新者</th>
		<th>更新日</th>
		</tr>

		<?php
		  for($i=0;$i < count($data);$i++)
		  {
		  	$atr = $data[$i]['UserView'];

		    if(UC_ADMIN == $user['User']['user_kbn_id'] || $user['User']['id'] == $atr['id']){
               echo "<tr><td><a href='".$html->url('/users/editUser')."/{$atr['id']}'>{$atr['username']}</a></td>";
            }else{
               echo "<tr><td>{$atr['username']}</td>";
            }

		       echo    "<td>{$atr['display_nm']}</td>".
		               "<td>{$atr['user_kbn_nm']}</td>".
		  	           "<td>{$atr['email']}</td>".
		  	           "<td>".$common->evalNbsp($atr['note'])."</td>".
		  	           "<td>".$common->evalNbsp($atr['last_login_dt'])."</td>".
		  	           "<td>{$atr['reg_nm']}</td>".
		  	           "<td>".date('Y/m/d',strtotime($atr['reg_dt']))."</td>".
		  	           "<td>".$common->evalNbsp($atr['upd_nm'])."</td>".
		  	           "<td>".$common->evalNbspForLongDate($atr['upd_dt'])."</td>
		  	       </tr>";
		  }
		?>
    </table>

