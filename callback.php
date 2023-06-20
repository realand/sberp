<?

include("include.php");


//{merchant-url}?mdOrder={mdOrder}&orderNumber={orderNumber}&operation={operation}&status={status}&callbackCreationDate={callbackCreationDate}

$vars = [];
$vars["mdOrder"] 		= ["Яндекс orderID","yandexOrderNumber"];
$vars["orderNumber"] 		= ["наш orderID","orderNumber"];
$vars["operation"] 		= ["Операция","operation"];//approved - операция удержания (холдирования) суммы;
				//declinedByTimeout - операция отклонения заказа по истечении его времени жизни;
				//deposited - операция завершения;
				//reversed - операция отмены;
				//refunded - операция возврата.
$vars["status"] 		= ["Статус(1=Успешно)","status"];
$vars["callbackCreationDate"]	= ["Время создания запроса уведомления обратного вызова","callbackCreationDate"];


$dataToSave = [];
foreach( $vars as $varName=>$varData ) {
	if ( isset($_REQUEST[$varName]) ) {
		$newVarName = $varData[1];
		$varValue = $_REQUEST[$varName];
		$dataToSave[$newVarName] = $varValue;
	}else {
		echo "Параметр [".$varData[1]."] не определён"; exit;
	}
}


$orderNumber = $dataToSave["orderNumber"];
$orderInstance = new Order();
if ( !$orderInstance->setOrderData( ["orderNumber"=>$orderNumber,"data"=>$dataToSave], $result ) ) {
	echo "Ошибка. ".$result["error"];
	exit;
}else {
	if ( $result["result"]["status"] ) {
		echo "status=".$result["result"]["status"]."; <font color=blue>Деньги успешно проведены</font><br>";
		echo "Отобразить сообщение об успешнй оплате<br>";
	} else {
		echo "status=0; <font color=red>Ошибка проведения денег</font><br>";
		echo "Отобразить сообщение о возникновении ошибки<br>";
	}
	echo "Данные успешно обновлены<br>";
	echo "<pre>";
	print_r($result);
	echo "</pre>";
}



?>