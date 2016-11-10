<?php 
/*
    echo $form->create('CustomersSchedules', array('target'=> '_parent') ); 
    echo $form->input('id',array('type'=>'hidden','value'=>$event['CustomerScheduleTrn']['id']));
    echo $form->input('title' , array('value'=>$event['CustomerScheduleTrn']['title']));
    echo 'When: ' .$displayTime; ?>
    <a href="<?php echo Dispatcher::baseUrl();?>/CustomersSchedules/delete/<?php echo $event['CustomerScheduleTrn']['id'];?>" onClick="return confirm('Do you really want to delete this event?');">Delete</a> 
    <?php echo $form->end(array('label'=>'Save' )); 
    //Below is just a cancel button. See previous post for the back() function ?>
    <input class="nicebutton" type="button" value="Cancel" onClick="back();">
*/

 echo "<form id='DeleteForm' method='post' action='{$html->url('delete')}/{$event['CustomerScheduleTrnView']['id']}'></form>";

 echo "<form id='CustomersSchedulesForm' method='post' action='{$html->url('edit')}'  >     
      <label for='CustomersSchedulesCustomerName'>{$event['CustomerScheduleTrnView']['grmls_kj']}様</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;   
      <input type='hidden' name='data[CustomerScheduleTrn][id]' value='{$event['CustomerScheduleTrnView']['id']}' id='CustomersSchedulesId' />";
  echo  "<br/><br/>";
  
  echo  "<label for='User'>担当者   </label><br/>";
  echo   "   <select id='CustomersSchedulesUserId' name='data[CustomerScheduleTrn][attend_id]' >";
          for($i=0;$i < count($user_list);$i++)
          {
           $atr = $user_list[$i];
           if($atr['User']['id'] == $event['CustomerScheduleTrnView']['attend_id'])
           {
           	echo      "<option value='{$atr['User']['id']}' selected>{$atr['User']['username']}</option>";   
           }
           else 
           {
           	echo      "<option value='{$atr['User']['id']}' >{$atr['User']['username']}</option>";   
           }  
          }                               
  echo  "</select>";
  echo  "<br/><br/>";
  echo  "<label for='StartTime'>予定時間   </label><br/>";
  echo  "<select id='StartHour' name='data[Tmp][start_hour]' >";
          for($i=0;$i < 24;$i++)
          {            
          	if($i==date('G',strtotime($event['CustomerScheduleTrnView']['start_dt'])))
          	{
  echo      "<option value='{$i}' selected>{$i}</option>";         		
          	}
          	else 
          	{
  echo      "<option value='{$i}' >{$i}</option>";           		
          	}  
          }                               
  echo  "</select> 時 ";
  echo  "<select id='StartMin' name='data[Tmp][start_min]' >";
          for($i=0;$i <= 59;$i++)
          {            
             if($i==date('i',strtotime($event['CustomerScheduleTrnView']['start_dt'])))
          	{
  echo      "<option value='{$i}' selected>{$i}</option>";         		
          	}
          	else 
          	{
  echo      "<option value='{$i}' >{$i}</option>";           		
          	}     
          }                               
  echo  "</select> 分 ";
  
  echo  "&nbsp;～&nbsp;";  
 
  echo   "<select id='EndHour' name='data[Tmp][end_hour]' >";
          for($i=0;$i < 24;$i++)
          {            
            if($i==date('G',strtotime($event['CustomerScheduleTrnView']['end_dt'])))
          	{
  echo      "<option value='{$i}' selected>{$i}</option>";         		
          	}
          	else 
          	{
  echo      "<option value='{$i}' >{$i}</option>";           		
          	}  
          }                               
  echo  "</select> 時 ";
  echo  "<select id='EndMin' name='data[Tmp][end_min]' >";
          for($i=0;$i <= 59;$i++)
          {            
            if($i==date('i',strtotime($event['CustomerScheduleTrnView']['end_dt'])))
          	{
  echo      "<option value='{$i}' selected>{$i}</option>";         		
          	}
          	else 
          	{
  echo      "<option value='{$i}' >{$i}</option>";           		
          	}  
          }                               
  echo  "</select> 分 <br/><br/>";
  echo  "<div class='input text'>
         <label for='CustomersSchedulesTitle'>タイトル</label><br/>
         <input name='data[CustomerScheduleTrn][title]' type='text' value='{$event['CustomerScheduleTrnView']['title']}' id='CustomersSchedulesTitle' />
         </div><br/>";     
  echo  "<label for='CustomersSchedulesTitle'>内容</label><br/>
         <textarea name='data[CustomerScheduleTrn][note]' class='inputcomment' rows='5'>{$event['CustomerScheduleTrnView']['note']}</textarea>";
  echo  "<input type='hidden' name='data[CustomerScheduleTrn][start_dt]' value='{$event['CustomerScheduleTrnView']['start_dt']}' id='CustomersSchedulesStartDt' />
         <input type='hidden' name='data[CustomerScheduleTrn][end_dt]'   value='{$event['CustomerScheduleTrnView']['end_dt']}' id='CustomersSchedulesEndDt' />   
         <input type='hidden' value='{$displayTime}' id='displayTime' />   
     </form>";         
?>
 
