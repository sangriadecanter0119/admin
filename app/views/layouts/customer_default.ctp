
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
 <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

 <title>基本情報</title>

<?php
   echo $html->css('default');
   echo $html->css('control');
   echo $html->css('application');
   echo $html->css('style');

   //echo $html->css('jquery-ui-1.8.14.custom.lightness.css');
 //  echo $html->css('jquery-ui-1.8.14.custom.blitzer.css');
  // echo $html->script('jquery/jquery-1.5.1.min.js');
 //  echo $html->script('jquery/jquery-ui-1.8.14.custom.min.js');

  echo $html->script('jquery/jquery-1.4.4.min.js');
  echo $html->css('prettyPhoto.css');
  echo $html->script('jquery/jquery.prettyPhoto.js');

  echo $html->css('prettyPopin.css');
  echo $html->script('jquery/jquery.prettyPopin.js');


  echo $html->css('uploadify/uploadify.css');
  echo $html->css('uploadify/custom.uploadify.css');
  echo $html->script('jquery/uploadify/swfobject.js');
  echo $html->script('jquery/uploadify/jquery.uploadify.v2.1.4.min.js');

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

	<h1><?php echo $sub_title; ?>&nbsp;&nbsp;<?php echo "【".$broom.$html->image('heart.png').$bride."】" ?></h1>

	<table class="customertype" cellspacing="0">
      <tr>
	   	  <td><a class="<?php echo $sub_menuA; ?>" href="<?php echo $html->url('/customerInfo') ?>">基本情報</a></td>
   <!--   <td><a class="<?php echo $sub_menuB; ?>" href="<?php echo $html->url('/customerMeetingReserve') ?>">来店状況</a></td>          -->
          <td><a class="<?php echo $sub_menuC; ?>" href="<?php echo $html->url('/customerWeddingReserve') ?>">お打ち合わせ状況</a></td>
   <!--   <td><a class="<?php echo $sub_menuD; ?>" href="<?php echo $html->url('/customerCompanyContact') ?>">問い合わせ状況</a></td>    -->
   <!--   <td><a class="<?php echo $sub_menuE; ?>" href="<?php echo $html->url('/customerSchedule') ?>">スケジュール</a></td>            -->
		  <td><a class="<?php echo $sub_menuF; ?>" href="<?php echo $html->url('/customerEstimate') ?>">見積もり</a></td>
     </tr>
    </table>

	<div class="clearer"></div>
   </div>

<?php echo $content_for_layout; ?>

</div>
</body>
</html>
