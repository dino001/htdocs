<?php

const IS_DEBUG = false;

session_start();
date_default_timezone_set("America/Los_Angeles");

//this should be set in PHP.ini
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING & ~E_DEPRECATED);

//Mysql setting
const MYSQL_SERVERNAME = 'localhost';
const MYSQL_USERNAME = 'root';
const MYSQL_PASSWORD = 'gamenow';
const MYSQL_DATABASE = 'quickdict_db';

require_once("db.class.php");
require_once("word.class.php");
require_once("setting.php");
require_once("function.php");

$db = new DBWrapper();

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">