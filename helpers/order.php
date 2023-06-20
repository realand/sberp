<?
#####################################################################################
## Название:	Order
## назначение:	Простой класс для работы с заказами
##
#####################################################################################


class Order {

	public function __construct()
	{
		$this->query = &CORE::$query;
		$this->orderLog   = &CORE::$orderLog;
	}

	//Создать заказ
	public function makeOrder( $input, &$output ) 
	{
		if ( !isset($input["data"]) ) {
			$output = _error("Параметр дата не определён");
			return false;
		}
		$data = $input["data"];

		//Проверяем заказы на дубликаты
		$orderNumber = $data["orderNumber"];
		$where = " and orderNumber='".$orderNumber."'";
		$this->query->getdata("orders","",$where);
		$rowsAll = $this->query->getall();
		if ( is_array($rowsAll) && count($rowsAll) ) {
			$output = _error("Номер заказа [$orderNumber] уже существует");
			return false;
		}

		if ( ($orderId=$this->query->setdata("orders",$data))===false ){
			$output = _error("Ошибка при добавлении заказа");
			return false;
		}

		//Если заказ успешно добавлен, пишем в логи "Новый заказ"
		$this->orderLog->addlog($orderId, "Новый зака № ".$orderId." в ".IntToDate(), $input );

		$output = _success(["orderId"=>$orderId]);
		return true;
	}

	//Изменить данные заказа
	public function setOrderData( $input, &$output ) {

		if ( !isset($input["data"]) ) {
			$output = _error("Параметр data не определён");
			$this->orderLog->addlog( $orderId, "setOrderData. Ошибка при изменении данных заказа. Параметр data не определён" );
			return false;
		}

		if ( isset($input["orderNumber"]) ) {
			$orderFilter = ["orderNumber"=>$input["orderNumber"]];
		}elseif ( isset($input["orderId"]) ) {
			$orderFilter = ["orderId"=>$input["orderId"]];
		}else
		{
			$output = _error("orderId или orderNumber не определён");
			$this->orderLog->addlog( $orderId, "setOrderData. Ошибка при изменении данных заказа. orderNumber или orderId не определён" );
			return false;
		}

		//Проверяем на существование "заказа"
		if ( !$this->getOrder( $orderFilter, $orderResult ) ) {
			$output = _error($orderResult["error"]);
			$this->orderLog->addlog( $orderId, "setOrderData. Ошибка. ".$orderResult["error"] );
			return false;
		}
		$order = $orderResult["result"];
		$data = $input["data"];
		//Обновляем данные заказа
		$orderId = $order["id"];

		//Пишем в логи, что данные заказа изменяются
		$this->orderLog->addlog($orderId, "setOrderData. Изменение данных заказа", $data );

		if ( ($this->query->setdata("orders",$data," and id='".$orderId."'"))===false ){
			$output = _error("Ошибка при изменении данных заказа");
			$this->orderLog->addlog( $orderId, "setOrderData. Ошибка при изменении данных заказа" );
			return false;
		}

		$this->orderLog->addlog( $orderId, "setOrderData. Данные успешно изменены" );
		$output = _success( array_merge($order,$data) );
		return true;
	}

	public function getOrder( $input, &$output ) {

		$where = "";
		if ( isset($input["orderNumber"]) ) {
			$where = " and orderNumber='".$input["orderNumber"]."'";
		}elseif ( isset($input["orderId"]) ) {
			$where = " and id='".$input["orderId"]."'";
		} else {
			$output = _error("orderNumber или orderId не определён");
			return false;
		}
		if ( !isset($input["orderNumber"]) ) {
		}
		$orderId = $input["orderId"];

		$data = $this->query->getdata("orders","",$where);
		$rowsAll = $this->query->getall();

		if ( !is_array($rowsAll) || !count($rowsAll) ) {
			$output = _error("Заказ не найден");
			return false;
		}

		$output = _success( $rowsAll[0] );
		return true;
	}

}




