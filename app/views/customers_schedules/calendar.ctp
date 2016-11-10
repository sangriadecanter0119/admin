
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

 <title>顧客スケジュール</title>

<?php
   echo $html->css('default');
   echo $html->css('control');
   echo $html->css('application');
   echo $html->css('style');

   echo $html->css('jquery-ui-1.7.3.custom.css');
   echo $html->css('ui.all.css');
   echo $html->css('cupertino/theme.css');

   echo $html->css('fullcalendar/fullcalendar.css');
   echo $html->css('fullcalendar/fullcalendar.print.css',null,array('media' => 'print'));

   echo $html->script('jquery/jquery-1.3.2.js');
   echo $html->script('jquery/jquery-ui-1.7.3.custom.js');
   echo $html->script('fullcalendar/fullcalendar.js');
?>

 <script type='text/javascript'>

	$(document).ready(function() {

		$('#calendar').fullCalendar({

			<?php if (isset($openYear)) { ?>
            year: <?php echo $openYear.',';}?>
            <?php if (isset($oMonth)) { ?>
            month: <?php echo $openMonth.',';}?>
            <?php if (isset($oDay)) { ?>
            date: <?php echo $openDay.',';} ?>
			editable: true,
			//disableDragging:true,
			events: "<?php echo Dispatcher::baseUrl();?>/customersSchedules/feed?_=1268960593294&start=1306854000&end=1309359600",

			dayClick: function(date, allDay, jsEvent, view)
			{
			  $("#selectedDate").val($.fullCalendar.formatDate( date, "yyyy/MM/dd ddd"));

		      $("#event_dialog").show();
		      $("#event_dialog").load("<?php echo Dispatcher::baseUrl();?>/customersSchedules/add/"+allDay+"/"+$.fullCalendar.formatDate( date, "dd/MM/yyyy/HH/mm"));
		      $("#event_dialog").dialog('open');
		    },

		    eventClick: function(calEvent, jsEvent, view) {
			    //自動計算して取得したデータ(id=-1)は編集不可とする
			    if(calEvent.id > 0){
                  $("#event_dialog").show();
                  $("#event_dialog").load("<?php echo Dispatcher::baseUrl();?>/customersSchedules/edit/"+calEvent.id);
                  $("#event_dialog").dialog('open');
			    }
            },
            eventRender:function(event, element)  {
                //自動計算して取得したデータ(id=-1)は移動(編集)不可とする
                if(event.id < 1){element.draggable=false;}
            },
            eventDrop: function(event,dayDelta,minuteDelta,allDay,revertFunc) {
            	if (dayDelta>=0)
                {
              	  dayDelta = "+"+dayDelta;
            	}
            	if (minuteDelta>=0)
                {
            	  minuteDelta="+"+minuteDelta;
            	}
            	$.post("<?php echo Dispatcher::baseUrl();?>/customersSchedules/move/"+event.id+"/"+dayDelta+"/"+minuteDelta+"/"+allDay);
          	}
		});

		$("#event_dialog").dialog({
            autoOpen:false,
            modal:true,
            width:450,
            resizable:false,

            open: function(event, ui)
			{
				var title = $( "#event_dialog" ).dialog( "option", "title" );
				$( "#event_dialog" ).dialog( "option", "title", '予定作成&nbsp;&nbsp;&nbsp;'+$("#selectedDate").val() );
		    },

        	buttons: {
		    	'delete event': function() {
                        $("#DeleteForm").trigger('submit');
      	                $(this).dialog('close');
	                 },
		 	    'cancel': function() {
			          $(this).dialog('close');
		             },
				'save event': function() {

		                $("#CustomersSchedulesForm").trigger('submit');
	          	        $(this).dialog('close');
				}
			},
			close: function() {
			//	allFields.val('').removeClass('ui-state-error');
			}

        });

		$("#event_dialog").hide();

	});

</script>
<style type='text/css'>

	#calendar {
		width: 960px;
		margin: 0 auto;
		}

   body{font-size:70%;}
   	/*
	input.text { margin-bottom:12px; width:95%; padding: .4em; }
	fieldset { padding:0; border:0; margin-top:25px; }
	div#users-contain {  width: 600px; margin: 20px 0; }
	div#users-contain table { margin: 1em 0; border-collapse: collapse; width: 100%; }
	div#users-contain table td, div#users-contain table th { border: 1px solid #eee; padding: .6em 10px; text-align: left; }
	.ui-button { outline: 0; margin:0; padding: .4em 1em .5em; text-decoration:none;  !important; cursor:pointer; position: relative; text-align: center; }
	.ui-dialog .ui-state-highlight, .ui-dialog .ui-state-error { padding: .3em;  }
*/
</style>
</head>

<body>
<div class="header">

	<div class="headertitle">
		 <a href="index.html"><?php echo $html->image('title.bmp') ?></a>
	</div>

	<div class="headerright">
		<a href=""><?php echo $user['User']['username']; ?></a><a href="<?php echo $html->url('/SystemManager') ?>">管理画面</a><a href="<?php echo $html->url('/users/logout') ?>">ログアウト</a>
	</div>

	<div class="control">
		<table cellspacing="0">
		  <tr>
			<td class="<?php echo $menu_customers; ?>"><a href="<?php echo $html->url('/customersList') ?>">顧客一覧情報</a></td>
			<td class="<?php echo $menu_customer; ?>"> <a href="#" onclick="return false">顧客個別情報</a></td>
			<?php
			   if($user['User']['user_kbn_id'] == UC_ADMIN){
			     echo "<td class='{$menu_fund}'><a href='{$html->url('/fundManagement')}'>資金管理</a></td>";
			   }
			?>
		  </tr>
		</table>
	</div>
</div>

<div class="container">
   <div class="contentcontrol">
	  <h1><?php echo $sub_title; ?></h1>

	  <table class="customertype" cellspacing="0">
		 <tr>
		        <td><a class="<?php echo $sub_menu_customers_list; ?>" href="<?php echo $html->url('/customersList') ?>" >顧客一覧</a></td>
                <td><a class="<?php echo $sub_menu_customers_company_contact; ?>" href="<?php echo $html->url('/customersCompanyContact') ?>" >問い合わせ状況一覧</a></td>
                <!-- <td><a class="<?php echo $sub_menu_customers_wedding_reserve; ?>" href="<?php echo $html->url('/customersWeddingsReserve') ?>" >挙式予約状況一覧</a></td> -->
                <td><a class="<?php echo $sub_menu_customers_schedules; ?>" href="<?php echo $html->url('/customersSchedules') ?>">顧客スケジュール</a></td>
                <!-- <td><a class="<?php echo $sub_menu_to_do_schedules; ?>" href="<?php echo $html->url('/toDoSchedules') ?>">業務スケジュール</a></td> -->
                <td><a class="<?php echo $sub_menu_customers_contract_list; ?>" href="<?php echo $html->url('/customersContractList') ?>">顧客挙式・約定一覧</a></td>
	     </tr>
	  </table>
      <div class="clearer"></div>
    </div>

   <ul class="operate"></ul>

   <table>
      <tr>
        <td><div class="fc-event-skin-month-before-wedding" style="width:30px;height:10px"></div></td><td style="padding-left:3px;padding-right:10px">挙式一ヶ月前</td>
        <td><div class="fc-event-skin-home-departure-date" style="width:30px;height:10px"></div></td><td style="padding-left:3px;padding-right:10px">日本出発日</td>
        <td><div class="fc-event-skin-wedding-date" style="width:30px;height:10px"></div></td><td style="padding-left:3px;padding-right:10px">挙式日</td>
        <td><div class="fc-event-skin-abroad-departure-date" style="width:30px;height:10px"></div></td><td style="padding-left:3px;padding-right:10px">現地出発日</td>
        <td><div class="fc-event-skin" style="width:30px;height:10px"></div></td><td>その他</td>
      </tr>
   </table>

  <div id="event_dialog" title="予定作成"></div>
  <div class="content" id="calendar"></div>
  <input type="hidden" id="selectedDate" />
 <!--
  <div id="dialog" title="予定作成">
	<p id="selected-date">日時:<span></span></p>

	<form action="">
	   <table>
	      <tr><td>選択:</td><td> <select><option>MENUA</option>
		                                 <option>MENUB</option>
		                         </select></td>
		  </tr>
	      <tr><td>コメント:</td><td><textarea id="comment" cols="25"></textarea></td></tr>
	   </table>
	</form>
  </div>
  -->

</div>
</body>
</html>
