    <ul class="operate">
     <li><a href="<?php echo $html->url('.') ?>">一覧に戻る</a></li>
    </ul>

    <form class="content" method="post" name="Goods action="<?php echo $html->url('addGoods') ?>" >

		<table class="form" cellspacing="0">		 
	  	   <tr>
             <th>セット商品名<span class="necessary">(必須)</span></th>                       
             <td>
                 <select id="folder_id" name="data[SetGoodsMst][set_goods_id]">
   			        <?php
   			        
   			           for($i=0;$i < count($set_goods_list);$i++)
   			           {
   			             $atr = $set_goods_list[$i]['GoodsMst'];   			        
   			             echo "<option value='{$atr['id']}'>{$atr['goods_nm']}</option>"; 	   			                 
   			           }  		
   			        ?>			             		
                 </select>             
             </td>
          </tr>        
          <tr>
             <th>商品名<span class="necessary">(必須)</span></th>                       
             <td>
                 <select id="folder_id" name="data[SetGoodsMst][goods_id]">
   			        <?php
   			        
   			           for($i=0;$i < count($goods_list);$i++)
   			           {
   			             $atr = $goods_list[$i]['GoodsMst'];   			        
   			             echo "<option value='{$atr['id']}'>{$atr['goods_nm']}</option>"; 	   			                 
   			           }  		
   			        ?>			             		
                 </select>             
             </td>
          </tr>                         	  
	    </table>

  
    <input type="hidden" name="data[SetGoodsMst][reg_nm]" value="<?php echo $user['User']['username']; ?>" >
    <input type="hidden" name="data[SetGoodsMst][reg_dt]" value="<?php echo date('Y-m-d H:i:s'); ?>">    
         
	<div class="submit">
		<input type="submit" value="追加" />     
	</div>
   </form>
