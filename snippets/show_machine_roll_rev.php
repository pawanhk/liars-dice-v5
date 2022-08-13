<?php
session_start();
$m_array_new = $_SESSION['m_array_n'];
?>

<div class="container">
  <div class="row">
    <div class="col-sm">
      <h4> <?php echo $m_array_new[0] ?> </h4>
    </div>
    <div class="col-sm">
      <h4> <?php echo $m_array_new[1] ?> </h4>
    </div>
    <div class="col-sm">
     	<h4> <?php echo $m_array_new[2] ?> </h4>
    </div>
    <div class="col-sm">
      <h4> <?php echo $m_array_new[3] ?> </h4>
    </div>
    <div class="col-sm">
      <h4> <?php echo $m_array_new[4] ?> </h4>
    </div>
  </div>
</div>