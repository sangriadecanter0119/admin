<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
 <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
 <title>エラー</title>
 
<style type="text/css">
  body{ 
    background-color:#d8eaff;
    color:#333;  
  }
  
  div.error_container{   
     margin-top:30px;
     margin-left:40px;
     background-color:#fff; 
     width:500px;     
  }
  div.inner_container{
     margin:20px;
  }
  .error_container h1{
     color:#6295d6;
  }
  div.left{
     float:left;
     margin-right:20px;
  }
  div.right{
     float:left;
     padding-bottom:20px;
  }
</style>
</head>

<body>
<div class="error_container">   
<div class="inner_container">
  <div class="left"><?php echo $html->image('error.png',array("width"=>"48px","height"=>"48px")) ?></div>
  <div class="right">
       <h1>Oops, that's didn't work</h1>
       <div class="error_content"><?php echo $content_for_layout; ?></div>
  </div>
</div>
</div>
</body>
</html>
