<?php 
/*
    echo $form->create('CustomersSchedules', array('target'=> '_parent'));
    echo $form->input('title' , array('label' => 'Event title'));
    echo '<br/>At: ' . $displayTime;
    echo $form->input('start_dt', array('type'=>'hidden','value'=>$event['CustomerScheduleTrn']['start']));
    echo $form->input('end_dt', array('type'=>'hidden','value'=>$event['CustomerScheduleTrn']['end']));
    echo $form->input('allday', array('type'=>'hidden','value'=>$event['CustomerScheduleTrn']['allday']));
    echo  $form->end(array('label'=>'Save' ,'name' => 'save')); 
*/  
      
  echo  "<form id='CustomersSchedulesForm' method='post' action='{$html->url('add')}' >";     
  echo  "<div class='input text'>";
  echo  "<label for='Customer'>顧客   </label><br/>";
  echo   "   <select id='CustomersSchedulesCustomerId' name='data[CustomerScheduleTrn][customer_id]' >";
          for($i=0;$i < count($customer_list);$i++)
          {
           $atr = $customer_list[$i]; 
  echo      "<option value='{$atr['CustomerMst']['id']}' >{$atr['CustomerMst']['grmls_kj']} {$atr['CustomerMst']['grmfs_kj']}</option>";   
          }                               
  echo  "</select>";
  echo  "<br/><br/>";
  echo  "<label for='User'>担当者   </label><br/>";
  echo   "   <select id='CustomersSchedulesUserId' name='data[CustomerScheduleTrn][attend_id]' >";
          for($i=0;$i < count($user_list);$i++)
          {
           $atr = $user_list[$i]; 
  echo      "<option value='{$atr['User']['id']}' >{$atr['User']['username']}</option>";   
          }                               
  echo  "</select>";
  
  echo  "<br/><br/>";
  
  echo  "<label for='StartTime'>予定時間   </label><br/>";
  echo  "<select id='StartHour' name='data[Tmp][start_hour]' >";
          for($i=1;$i <= 24;$i++)
          {            
  echo      "<option value='{$i}' >{$i}</option>";   
          }                               
  echo  "</select> 時 ";
  echo  "<select id='StartMin' name='data[Tmp][start_min]' >";
          for($i=0;$i <= 59;$i++)
          {            
  echo      "<option value='{$i}' >{$i}</option>";   
          }                               
  echo  "</select> 分 ";
  
  echo  "&nbsp;～&nbsp;";

  echo   "<select id='EndHour' name='data[Tmp][end_hour]' >";
          for($i=1;$i <= 24;$i++)
          {            
  echo      "<option value='{$i}' >{$i}</option>";   
          }                               
  echo  "</select> 時 ";
  echo  "<select id='EndMin' name='data[Tmp][end_min]' >";
          for($i=0;$i <= 59;$i++)
          {            
  echo      "<option value='{$i}' >{$i}</option>";   
          }                               
  echo  "</select>分<br/><br/>";
  echo  " <label for='CustomersSchedulesTitle'>タイトル</label><br/>
          <input name='data[CustomerScheduleTrn][title]' type='text' class='inputvalue' id='CustomersSchedulesTitle' /><br/><br/>";     
  echo  " <label for='CustomersSchedulesTitle'>内容</label><br/>
          <textarea name='data[CustomerScheduleTrn][note]' class='inputcomment' rows='5'></textarea>";
  echo "</div>
      <br/>        
        <input type='hidden' name='data[CustomerScheduleTrn][start_dt]' value='{$event['CustomerScheduleTrn']['start']}' id='CustomersSchedulesStartDt' />
        <input type='hidden' name='data[CustomerScheduleTrn][end_dt]'   value='{$event['CustomerScheduleTrn']['end']}' id='CustomersSchedulesEndDt' />
        <input type='hidden' name='data[CustomerScheduleTrn][allday]'   value='{$event['CustomerScheduleTrn']['allday']}' id='CustomersSchedulesAllday' />
        <input type='hidden' value='{$displayTime}' id='displayTime' />
   
   </form>"
  
?>