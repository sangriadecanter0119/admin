<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

 <!-- <title>顧客スケジュール</title> -->
      <title>.NET開発</title>
<?php
   echo $html->css('default');
   echo $html->css('control');
   echo $html->css('application');

   echo $html->css('jquery-ui-1.7.3.custom.css');
   echo $html->css('ui.all.css');
   echo $html->css('cupertino/theme.css');

   echo $html->css('fullcalendar/fullcalendar.css');
   echo $html->css('fullcalendar/fullcalendar.print.css',null,array('media' => 'print'));

   echo $html->script('jquery/jquery-1.3.2.js');
   echo $html->script('jquery/jquery-ui-1.7.3.custom.js');
   echo $html->script('fullcalendar/fullcalendar.min.js');
?>

 <script type='text/javascript'>
    function getFormatDate(dt)
	{
	   var year  = dt.getFullYear();
	   var　month = dt.getMonth()+1;
       if(month <10){ month = "0"+month;}
       var date  =  dt.getDate();
       if(date < 10){ date = "0"+date;}

     return year+month+date;
	}

	$(document).ready(function() {

		var date = new Date();
		var d = date.getDate();
		var m = date.getMonth();
		var y = date.getFullYear();
		var selected_date = new Date();



		//FullCalendarの定義
		var calendar = $('#calendar').fullCalendar({
			 header: {
				left: 'prev,next today',
				center: 'title',
				right:'' //'month,agendaWeek,agendaDay'
			 },
			 selectable: true,
		 	 selectHelper: true,

			 // イベント作成
			 dayClick: function(date, allDay, jsEvent, view)
			               {
			 	                selected_date = date;
			 	                $("#selected-date span").text($.fullCalendar.formatDate(date, 'MM月dd日'));
			 	                $("#comment").text("");
			 	                $("#event_id").val(-1);

			 	               $("#dialog").dialog('open');
                           },
             // イベント編集
             eventClick: function(calEvent, jsEvent, view)
                          {
                        	   selected_date = date;
                               $("#selected-date span").text($.fullCalendar.formatDate(calEvent.start, 'MM月dd日'));
                        	   $("#comment").text(calEvent.title);
                        	   $("#event_id").val(calEvent.id);

                        	   $("#dialog").dialog('open');
                        	   alert("編集ID"+calEvent.id);
                          },
			 editable: true,
			 eventDrop: function(event) {	// Make sure to read the plugin's documentation

				 var dt = new Date(event.start);   // event.start is the new date where you dragged and dropped the event post.
				 var newdate = getFormatDate(dt)

				 $.post("http://localhost/workspace/admin/customersSchedules/moveCalEvent/id:"+event.id+"/date:"+newdate, function(data){});
				 } ,

			 events: "http://localhost/workspace/admin/customersSchedules/getCalEvent"
		});

		//モーダル入力ダイアログBOXの定義
		$("#dialog").dialog({
            autoOpen:false,
            modal:true,

        	buttons: {
		        'キャンセル': function() {
			        $(this).dialog('close');
		             },

		   	    '削除': function() {
		          	$('#calendar').fullCalendar('removeEvents',$("#event_id").val());
					$.get("http://localhost/workspace/admin/customersSchedules/deleteCalEvent/"+$("#event_id").val(),function(data){
			                     // alert(data);
			              });
		          	$(this).dialog('close');
	              	 },

				'作成': function() {

                     //新規作成
	                 if($("#event_id").val() == -1)
	                 {
	                	alert("新規");
	                    var json = [{
			                  id:  $("#event_id").val(),
				            title: $("#comment").val(),
		 		            start: selected_date,
				              end: selected_date
		                     }]

	                    var event_dt = getFormatDate(json[0].start);

		               $('#calendar').fullCalendar('addEventSource',json );
                      //   $('#calendar').fullCalendar('renderEvent',json );

		               $.get("http://localhost/workspace/admin/customersSchedules/createCalEvent/"+json[0].title+"/"+event_dt,function(data){

	                        var arr = $('#calendar').fullCalendar('clientEvents',-1);
	                        if(arr != null)
	                        {
	                           arr[0].id = data;
	                           $('#calendar').fullCalendar('updateEvents',arr[0]);
	                        }
	                     });
	                 }
	                 //更新
	                 else
	                 {
	                	 alert("更新:"+$("#event_id").val());

	                   var arr = $('#calendar').fullCalendar('clientEvents',$("#event_id").val());

	                   alert(arr);

	                   arr[0].title =$("#comment").val();

                       alert(arr[0].id +":"+arr[0].title);

			           $('#calendar').fullCalendar('updateEvents',arr[0]);

			           var event_dt = getFormatDate(arr[0].start);

			           $.get("http://localhost/workspace/admin/customersSchedules/updateCalEvent/"+arr[0].id+"/"+arr[0].title+"/"+event_dt);
	                 }
	          	     $(this).dialog('close');
				}
			},
			close: function() {
			//	allFields.val('').removeClass('ui-state-error');
			}
        });

	});

</script>
<style type='text/css'>

	#calendar {
		width: 900px;
		margin: 0 auto;
		}


	input.text { margin-bottom:12px; width:95%; padding: .4em; }
	fieldset { padding:0; border:0; margin-top:25px; }
	div#users-contain {  width: 350px; margin: 20px 0; }
	div#users-contain table { margin: 1em 0; border-collapse: collapse; width: 100%; }
	div#users-contain table td, div#users-contain table th { border: 1px solid #eee; padding: .6em 10px; text-align: left; }
	.ui-button { outline: 0; margin:0; padding: .4em 1em .5em; text-decoration:none;  !important; cursor:pointer; position: relative; text-align: center; }
	.ui-dialog .ui-state-highlight, .ui-dialog .ui-state-error { padding: .3em;  }

</style>
</head>

<body>
<div class="header">

	<div class="headertitle">
		 <a href="index.html"><?php echo $html->image('title.bmp') ?></a>
	</div>

	<div class="headerright">
		<a href="#"><?php echo $user['User']['username']; ?></a><a href="controlManager.html">管理画面</a><a href="<?php echo $html->url('/users/logout') ?>">ログアウト</a>
	</div>

	<div class="control">
		<table cellspacing="0">
		  <tr>
			<td class="<?php echo $menu_customers; ?>"><a href="<?php echo $html->url('/customersList') ?>">顧客一覧情報</a></td>
			<td class="<?php echo $menu_customer; ?>"><a href="#" onclick="return false">顧客個別情報</a></td>
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
		        <td><a class="<?php echo $sub_menu_customers_list; ?>"            href="<?php echo $html->url('/customersList') ?>" >顧客一覧</a></td>
                <td><a class="<?php echo $sub_menu_customers_company_contact; ?>" href="<?php echo $html->url('/customersCompanyContact') ?>" >問い合わせ状況一覧</a></td>
          <!-- <td><a class="<?php echo $sub_menu_customers_wedding_reserve; ?>"  href="<?php echo $html->url('/customersWeddingsReserve') ?>" >挙式予約状況一覧</a></td> -->
                <td><a class="<?php echo $sub_menu_customers_schedules; ?>"       href="<?php echo $html->url('/customersSchedules') ?>">顧客スケジュール</a></td>
          <!-- <td><a class="<?php echo $sub_menu_to_do_schedules; ?>"            href="<?php echo $html->url('/toDoSchedules') ?>">業務スケジュール</a></td> -->
                <td><a class="<?php echo $sub_menu_customers_contract_list; ?>"   href="<?php echo $html->url('/customersContractList') ?>">顧客挙式・約定一覧</a></td>
	     </tr>
	  </table>
	  <div class="clearer"></div>
    </div>

   <?php echo $content_for_layout; ?>

</div>
</body>
</html>
