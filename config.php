<?php
  $dbuser = getenv('DBUSER');
  $dbpass = getenv('DBPASS');
  $dbname = 'subliteinternships';
  require_once('pass.php');

  $GLOBALS = array_merge($GLOBALS, array(
    'dbname' => $dbname,
    'dburi' => "mongodb://$dbuser:$dbpass@ds051980.mongolab.com:51980/$dbname",
    'domain' => "54.172.31.140"
  ));
?>