<?php
session_start();

require_once "config.php";
require_once "config.admin.php";
require_once "init.object.php";

echo 'Loading~~';
if ($schedule->schedule_daily_auto() ){
  echo 'ok la '.date("h:i:s");
}



?>
