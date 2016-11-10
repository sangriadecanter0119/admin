<?php
echo $html->script("jquery/jExpand.js",false);
echo $html->css("jExpand.css",false);

$this->addScript($javascript->codeBlock( <<<JSPROG
$(function(){
   $("#report").jExpand();
   $("input:submit").button();
});
JSPROG
))
?>

<ul class="operate">
<li><a href="<?php echo $html->url('export') ?>" >EXCEL出力</a></li>
</ul>

<form class="content" method="post" name="customer" action="">

	<table id="report" class="list" cellspacing="0">

		<tr>
		    <th></th>
		    <th><a href="">挙式日</a></th>
		    <th>場所・時間</th>
		    <th><a href="">新郎/新婦名</a></th>
		    <th>HOTEL</th>
		    <th>レセプション会場</th>
		    <th>Rec/H</th>
		    <th>Max Pax</th>
		    <th>Camera</th>
		    <th>Hair Make</th>
		    <th>RE H/M</th>
		    <th>Video</th>
		    <th>Flower</th>
		    <th>Attend</th>
		    <th>Briefing  DateTime</th>
		    <th>紹介者</th>
		    <th>Slideshow</th>
		    <th>ShortFilm</th>
		</tr>

		<tr>
		    <td><div class="arrow"></div></td>
		    <td nowrap>12/12/2011</td>
		    <td nowrap>CA-14:00</td>
		    <td nowrap><a href="customer.html">山田太郎/花子</a></td>
		    <td>ハレクラニ</td>
		    <td>Hau Tree Lanai</td>
		    <td>18:00</td>
		    <td>25</td>
		    <td>Jayson</td>
		    <td>Shinobu</td>
		    <td>12/12/2011</td>
		    <td>King</td>
		    <td>&nbsp;</td>
		    <td>Maiko</td>
		    <td>12/11/2011 14:00</td>
		    <td>Honda様</td>
		    <td>&nbsp;</td>
		    <td>&nbsp;</td>
        </tr>
        <tr>
            <td colspan="18">
                <ul class="operate">
                  <li>Visionari SS</li>
                  <li>
                     <select id="folder_id" name="folder_id">
   			            <option value="">-</option>
                        <option value="" selected="selected">OK</option>
                     </select>
                  </li>
                  <li>Visionari Dater</li>
                  <li>
                     <select id="folder_id" name="folder_id">
   			            <option value="">-</option>
                        <option value="" selected="selected">OK</option>
                     </select>
                  </li>
                  <li>写真</li>
                  <li>
                     <select id="folder_id" name="folder_id">
   			            <option value="">-</option>
                        <option value="" selected="selected">OK</option>
                     </select>
                  </li>
                  <li>アルバム</li>
                  <li>
                     <select id="folder_id" name="folder_id">
   			            <option value="">-</option>
                        <option value="" selected="selected">OK</option>
                     </select>
                  </li>
                  <li>アンケート</li>
                  <li>
                     <select id="folder_id" name="folder_id">
   			            <option value="">-</option>
                        <option value="" selected="selected">OK</option>
                     </select>
                  </li>
                  <li>HP</li>
                  <li>
                     <select id="folder_id" name="folder_id">
   			            <option value="">-</option>
                        <option value="" selected="selected">OK</option>
                     </select>
                  </li>
                  <li>Fel</li>
                  <li>
                     <select id="folder_id" name="folder_id">
   			            <option value="">-</option>
                        <option value="" selected="selected">OK</option>
                     </select>
                  </li>
                </ul>
            </td>
        </tr>

        <tr>
            <td><div class="arrow"></div></td>
		    <td>12/12/2012</td>
		    <td>CA-14:00</td>
		    <td><a href="customer.html">田中一郎/恵子</a></td>
		    <td>モダンホノルル</td>
		    <td>Hau Tree Lanai</td>
		    <td>18:00</td>
		    <td>25</td>
		    <td>Jayson</td>
		    <td>Shinobu</td>
		    <td>12/12/2011</td>
		    <td>King</td>
		    <td>&nbsp;</td>
		    <td>Maiko</td>
		    <td>12/11/2011 14:00</td>
		    <td>Honda様</td>
		    <td>&nbsp;</td>
		    <td>&nbsp;</td>
         </tr>
         <tr>
            <td colspan="18">
                <ul class="operate">
                  <li>Visionari SS</li>
                  <li>
                     <select id="folder_id" name="folder_id">
   			            <option value="">-</option>
                        <option value="" selected="selected">OK</option>
                     </select>
                  </li>
                  <li>Visionari Dater</li>
                  <li>
                     <select id="folder_id" name="folder_id">
   			            <option value="">-</option>
                        <option value="" selected="selected">OK</option>
                     </select>
                  </li>
                  <li>写真</li>
                  <li>
                     <select id="folder_id" name="folder_id">
   			            <option value="">-</option>
                        <option value="" selected="selected">OK</option>
                     </select>
                  </li>
                  <li>アルバム</li>
                  <li>
                     <select id="folder_id" name="folder_id">
   			            <option value="">-</option>
                        <option value="" selected="selected">OK</option>
                     </select>
                  </li>
                  <li>アンケート</li>
                  <li>
                     <select id="folder_id" name="folder_id">
   			            <option value="">-</option>
                        <option value="" selected="selected">OK</option>
                     </select>
                  </li>
                  <li>HP</li>
                  <li>
                     <select id="folder_id" name="folder_id">
   			            <option value="">-</option>
                        <option value="" selected="selected">OK</option>
                     </select>
                  </li>
                  <li>Fel</li>
                  <li>
                     <select id="folder_id" name="folder_id">
   			            <option value="">-</option>
                        <option value="" selected="selected">OK</option>
                     </select>
                  </li>
                </ul>
            </td>
        </tr>
	</table>

        <div class="submit">
	     <input type="submit" class="inputbutton" value="保存" />
	     <input type="hidden" name="customer_type" value="0" />
        </div>
</form>

