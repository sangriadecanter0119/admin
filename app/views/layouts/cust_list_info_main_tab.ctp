
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
 <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<!-- <title>顧客情報</title> -->
 <title>.NET開発</title>
<?php
   echo $html->css('default');
   echo $html->css('control');
   echo $html->css('application');
 //UIの選択
   //echo $html->css('jquery-ui-1.8.14.custom.blitzer.css');

   //echo $html->script('jquery/jquery-1.5.1.min.js');
   //echo $html->script('jquery/jquery-ui-1.8.14.custom.min.js');

   echo $html->css('jquery-ui-1.8.23.custom.redmond.css');
   echo $html->script('jquery/jquery-1.7.2.min.js');
   echo $html->script('jquery/jquery-ui-1.8.14.custom.min.js');
   echo $html->script('jquery/jquery.common.js');
   echo $html->script('jquery/jquery.maskedinput.js');

   echo $scripts_for_layout;
?>
</head>

<body>
<div class="header">
	<div class="headertitle">
	     <a href="index.html"><?php echo $html->image('title.bmp') ?></a>
	</div>

	<div class="headerright">
		<a href="#"><?php echo $user['User']['username']; ?></a><a href="<?php echo $html->url('/systemManager') ?>">管理画面</a><a href="<?php echo $html->url('/users/logout') ?>">ログアウト</a>
	</div>

	<div class="control">
		<table cellspacing="0">
          <tr>
			<td class="<?php echo $menu_customers; ?>"><a href="<?php echo $html->url('/customersList') ?>">顧客一覧情報</a></td>
			<td class="<?php echo $menu_customer; ?>"><a href="#" onclick="return false">顧客個別情報</a></td>
			<td class="<?php echo $menu_fund; ?>"><a href="<?php echo $html->url('/bankManagement') ?>">資金管理</a></td>
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
          <!--  <td><a class="<?php echo $sub_menu_customers_company_contact; ?>" href="<?php echo $html->url('/customersCompanyContact') ?>" >問い合わせ状況一覧</a></td> -->
                <td><a class="<?php echo $sub_menu_customers_wedding_reserve; ?>" href="<?php echo $html->url('/customersWeddingReserve') ?>" >挙式予約状況一覧</a></td>
          <!--  <td><a class="<?php echo $sub_menu_customers_schedules; ?>"       href="<?php echo $html->url('/customersSchedules') ?>">顧客スケジュール</a></td>         -->
          <!--  <td><a class="<?php echo $sub_menu_to_do_schedules; ?>"           href="<?php echo $html->url('/toDoSchedules') ?>">業務スケジュール</a></td>             -->
                <td><a class="<?php echo $sub_menu_customers_contract_list; ?>"   href="<?php echo $html->url('/customersContractList') ?>">顧客挙式・約定一覧</a></td>
                <td><a class="<?php echo $sub_menu_attendant_state; ?>"           href="<?php echo $html->url('/attendantState') ?>">担当者別各月状況一覧</a></td>
	            <td><a class="<?php echo $sub_menu_customers_by_each_attendant_list; ?>"  href="<?php echo $html->url('/CustomersByEachAttendantList') ?>">担当者別顧客一覧</a></td>
	            <td><a class="<?php echo $sub_menu_wedding_reservations; ?>"      href="<?php echo $html->url('/weddingReservations') ?>" >挙式場予約状況</a></td>
         </tr>
    </table>
	<div class="clearer"></div>
  </div>

<?php echo $content_for_layout; ?>

</div>
</body>
</html>
