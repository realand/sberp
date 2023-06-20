<?
#####################################################################################
## Название:	sberPayment
## назначение:	Простой класс для работы с оплатой "Сбербанка"
##
#####################################################################################


class sberPayment {

	public function doRegisterOrder( $input, &$output )
	{
		global $config;

		$vars = array();
		$order_id = $input["orderNumber"];
		
		$vars['userName'] = $config["sber.username"];
		$vars['password'] = $config["sber.password"];
		
		/* ID заказа в магазине */
		$vars['orderNumber'] = $order_id;
		
		/* Корзина */
		$items = array(
			array(
				//'positionId' => 1,
				'name' => $input["productName"],//'Название товара',
				'quantity' => array(
					'value' => $input["productQuantity"],    
					'measure' => 'шт'
				),
				'itemAmount' => $input["productQuantity"] * ($input["productPrice"] * 100),//В копейках
				'itemCode' => $input["productId"],
				'tax' => array(
					'taxType' => 0,
					'taxSum' => 0
				),
				'itemPrice' => $input["productPrice"] * 100,//В копейках
			)
		);
		
		$vars['orderBundle'] = json_encode(
			array(
				'cartItems' => array(
					'items' => $items
				)
			), 
			JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP  | JSON_UNESCAPED_UNICODE
		);
		
		/* Сумма заказа в копейках */
		$vars['amount'] = $input["productQuantity"] * $input["productPrice"] * 100;
		
		/* URL куда клиент вернется в случае успешной оплаты */
		$vars['returnUrl'] = $config["orderSuccessUrl"];
			
		/* URL куда клиент вернется в случае ошибки */
		$vars['failUrl'] = $config["orderErrorUrl"];
		
		/* Описание заказа, не более 24 символов, запрещены % + \r \n */
		$vars['description'] = 'Заказ №' . $order_id . ' в магазине '.$config["shopUrl"];

		$url = $config["orderUrl"].'?'.http_build_query($vars);
		$ch = curl_init( $url );
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_HEADER, false);
		$res = curl_exec($ch);
		$info = curl_getinfo($ch);
		curl_close($ch);
		if (!$res) {
			$output = _error( "Сбер сбросил соединение либо сервер сбера не работает. ", $info );
			return false;
		}


$res = '{"orderId":"70906e55-7114-41d6-8332-4609dc6590f4","formUrl":"https://3dsec.sberbank.ru/payment/merchants/test/payment_ru.html?mdOrder=70906e55-7114-41d6-8332-4609dc6590f4"}';

		$resArray = json_decode($res,true);

/*$resArray = [
	"errorCode"=>"0",
	"orderId"=>"70906e55-7114-41d6-8332-4609dc6590f4",
	"formUrl"=>"https://3dsec.sberbank.ru/payment/merchants/test/payment_ru.html?mdOrder=70906e55-7114-41d6-8332-4609dc6590f4"
];*/

		//Если ошибка
		if ( isset($resArray["errorCode"]) && $resArray["errorCode"] ) {
			$output = _error("Сбер вернул ошибку",$resArray);
			$output["request"] = $vars;
			return false;
		}

		$output = _success( $resArray );
		return true;
	}

}



?>