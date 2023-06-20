<?
	include("include.php");


	//Список товаров
	$products = [];
	$products["Товар 1"] = ["productPrice"=>1200,"productId"=>"10"];
	$products["Товар 2"] = ["productPrice"=>1300,"productId"=>"11"];
	$products["Товар 3"] = ["productPrice"=>1400,"productId"=>"12"];

	//№ заказа
	$orderNumber = md5(microtime().rand(1,100000));

	//Если форма на оплату отправлена пользователем(методом POST)
	if ( count($_POST)>0 ) {

		$productName = $_POST["product"];

		//Проверяем существует ли продукт
		if ( isset($products[$productName]) ) {

			//Заполняем массив для отправки данных в сбер
			$orderData = $products[$productName];
			$orderData["productName"] = $productName;
			$orderData["productQuantity"] = intval($_REQUEST["quantity"]);
			$orderData["orderNumber"] = $_REQUEST["orderNumber"];

			//Простой кдасс для работы с заказами
			$orderInstance = Core::$order;
			$orderLogInstance = Core::$orderLog;

			//Создаём новый заказ в базе
			if ( $orderInstance->makeOrder( ["data"=>$orderData], $output ) ) {
				//Если заказ создался успешно
				echo "Заказ создан успешно<br>";
				echo "<pre>";
				print_r($output);
				echo "</pre>";

				//ИД заказа в системе нашего процессинга
				$orderId = $output["result"]["orderId"];
		        
				//Простой класс для работы с платёжкой сбера
				$paymentInstance = new sberPayment();
		        
				echo "Попытка произвести оплату<br>";
				$orderLogInstance->addlog($orderId, "Попытка произвести оплату в ".IntToDate(), $input );
		        
				//Запрос на оплату в сбербанк
				if ( $paymentResult = $paymentInstance->doRegisterOrder( $orderData, $output ) ) {
					$url = $output["result"]["formUrl"];

					//Обновляем данные заказа(в таблицу orders)
					$orderInstance->setOrderData( [
							"orderId"=>$orderId,
							"data"=>[ "yandexOrderNumber"=>$output["result"]["orderId"] ]
						], $result );
		        
					$orderLogInstance->addlog( $orderId, "Транзакция была выполнена", $output );
					echo "Транзакция была выполнена<br>";
					echo "<pre>";
					print_r($output);
					echo "</pre>";
					echo "Перенаправление на страницу оплаты [".$output["result"]["formUrl"]."] произойдёт через 4 секунды<br>";

					echo "<script> setTimeout( function(){ document.location.href='".$output["result"]["formUrl"]."' }, 4000 ) </script>";
					exit;
				}else {
					$orderLogInstance->addlog( $orderId, "Ошибка при проведении танзакции", $output );
					echo "Ошибка при проведении транзакции<br>";
					echo "<pre>";
					print_r($output);exit;
				}
			} else {
				//Если произошла какая то ошибка
				echo $output["error"]."<br>";
			}

		}else {
			echo "Внутренная ошибка. Товар не найден.";
			exit;
		}

	}


?>

<script src="js/jquery.min.js"></script>

<style>
form { margin:0 auto; border:1px solid gray; display:inline-flex; flex-direction:column; padding:40px; }
form label { display:flex; align-items:center; margin-top:10px; margin-bottom:10px;  }
form label span { width:300px; }
form input,
form select { width:100%; height:30px; }
</style>

<script>

function allPriceRecalc()
{
	let productName  = $("[name=product]").val();
	let productPrice = $("[name=product] option:selected").attr("data-price");
	let productCount = $("[name=quantity]").val();
	$("[name=amount]").val( productPrice*productCount );
}

$( function(){
	$("input,select").on("change", allPriceRecalc );
	$("input,select").on("keydown", allPriceRecalc );
	allPriceRecalc();
} );
</script>

<form action="" method="POST">

	<input name="orderNumber" placeholder='' type='text' value="<?=$orderNumber?>">

	<label>
		<span>ФИО</span>
		<input name="fio" placeholder='' type='text' value="Иван Петров">
	</label>

	<label>
		<span>Товар</span>
		<select name="product">
			<? foreach( $products as $pName=>$data ) { 
				$price = $data["productPrice"];
			?>
			<option value="<?=$pName?>" data-price="<?=$price?>"><?=$pName?> (<?=$price?>)</option>
			<? } ?>
		</select>
	</label>

	<label>
		<span>Количество товара</span>
		<select name="quantity">
			<? for( $i=1; $i<=10; $i++ ) { ?>
			<option value="<?=$i?>"><?=$i?></option>
			<? } ?>
		</select>
	</label>

	<label>
		<span>Оплачиваемая сумма</span>
		<input name="amount" value="0" disabled>
	</label>

	<label>
		<span></span>
		<input type='submit' value="Отправить">
	</label>


</form>

<?




