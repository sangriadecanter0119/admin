
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
 <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

 <!-- <title>顧客管理システム</title> -->
 <title>.NET開発</title>
<?php
   echo $html->css('default');
   echo $html->css('control');
   echo $html->css('application');
   echo $html->css('style');
   
   echo $html->script('library/jquery.js');
   echo $html->script('library/ui.core.js');
   echo $html->script('library/ui.draggable.js');
   echo $html->script('customer.js');
   echo $html->script('postcode.js');
?>
</head>

<body onload="document.forms['login'].elements['userid'].focus()">
<div class="header">

	<div class="headertitle">
		  <a href="index.html"><?php echo $html->image('title.bmp') ?></a>
	</div>

	<div class="clearer"></div>
</div>

<form id="login" class="login" method="post" name="User" action="login">

	<h1>ログイン</h1>
		<table cellspacing="0">
		  <tr><th>ユーザー名</th><td><input type="text"   id="userid"  name="data[User][username]" class="logininput" value="" /></td></tr>
  		  <tr><th>パスワード</th><td><input type="password"             name="data[User][password]" class="logininput" value="" /></td></tr>
	    </table>

	<div class="loginsubmit"><input type="submit" value="　ログイン　" /></div>      

<?php
  if($session->check('Message.auth'))
  {
    echo "<span style='color:red'>".$session->flash('auth')."</span>";
  }  
?>
</form>


</body>
</html>
