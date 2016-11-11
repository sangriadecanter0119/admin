
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
 <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

 <title>基本情報</title>

<?php
   echo $html->css('default');
   echo $html->css('control');
   echo $html->css('application');
   //UIの選択
   //echo $html->css('jquery-ui-1.8.14.custom.lightness.css');
   echo $html->css('jquery-ui-1.8.23.custom.redmond.css');
   echo $html->script('jquery/jquery-1.5.1.min.js');
   echo $html->script('jquery/jquery-ui-1.8.14.custom.min.js');

   echo $html->script('jquery/jquery.maskedinput.js');
   echo $html->script('jquery/jquery.common.js');
   echo $html->script('common.js');

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
			<td class="<?php echo $menu_customer; ?>"><a href="#" onclick="return false;">顧客個別情報</a></td>
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
          <td><a class="<?php echo $sub_menu_bank; ?>"       href="<?php echo $html->url('/bankManagement') ?>">入金管理一覧</a></td>
          <?php
			   if($user['User']['user_kbn_id'] == UC_ADMIN){
			     echo "<td><a class='{$sub_menu_sales}'      href='{$html->url('/salesManagement')}'>売上管理一覧</a></td>";
                 echo "<td><a class='{$sub_menu_fund}'       href='{$html->url('/fundManagement')}'>資金管理一覧</a></td>";
	             echo "<td><a class='{$sub_menu_remittance}' href='{$html->url('/remittance')}'>送金一覧</a></td>";
                 echo "<td><a class='{$sub_menu_payment}'    href='{$html->url('/payment')}'>現地支払い一覧</a></td>";
                 echo "<td><a class='{$sub_menu_vendor_sales}' href='{$html->url('/vendorSales')}'>ベンダー売上一覧</a></td>";
			   }
		  ?>
     </tr>
    </table>

	<div class="clearer"></div>
   </div>

<?php echo $content_for_layout; ?>

</div>
</body>
</html>
