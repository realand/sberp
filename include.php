<?

header("Content-type: text/html; charset=utf-8"); 

//Файл настроек
include("config.php");

//Основные классы и хелперы
include("helpers/function.php");
include("helpers/query.php");
//Более высокоуровневые классы
include("helpers/order.php");
include("helpers/order_log.php");
include("helpers/payment.php");

//Ядро
include("helpers/core.php");



