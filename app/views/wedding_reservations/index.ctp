<?php
$this->addScript($javascript->codeBlock( <<<JSPROG
$(function(){
   $("input:submit").button();
});
JSPROG
)) ?>

<form action="" method="get" style="margin-bottom:10px;">

  <select name="year">
    <?php
      for( $i=date('Y', time() ); $i<date('Y', time() ) + 5; $i++ ) {
        if( isset( $year ) && $year == $i )
          printf( "<option value='%d' selected>%d</option>", $i, $i );
        else
          printf( "<option value='%d'>%d</option>", $i, $i );
      }
    ?>
  </select><span style="margin-left:3px;">年</span>

  <select name="month">
    <?php
      for( $i=1; $i<=12; $i++ ) {
        if( isset( $month ) && $month == $i )
          printf( "<option value='%d' selected>%d</option>", $i, $i );
        else
          printf( "<option value='%d'>%d</option>", $i, $i );
      }
      ?>
  </select><span style="margin-left:3px;">月</span>

  <select name="week">
    <?php
      for( $i=1; $i<=5; $i++ ) {
        if( isset( $week ) && $week == $i )
          printf( "<option value='%d' selected>%d</option>", $i, $i );
        else
          printf( "<option value='%d'>%d</option>", $i, $i );
      }
      ?>
  </select><span style="margin-left:3px;">週</span>
  <input type="submit" name="submit" class="inputbutton" value="Cache" style="margin-left:5px;">
  <input type="submit" name="submit" class="inputbutton" value="取得" style="margin-left:5px;">
</form>
<?php
  if( isset( $data ) ) {
  echo "<table class='list' cellspacing='0'>";
  echo $html->tableHeaders( $data['header1st'] );
  echo $html->tableCells(
      $data['header2nd']
  );
  echo $html->tableCells(
      $data['body']
  );
  echo "</table>";
}
?>
