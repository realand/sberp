<?
#####################################################################################
## Название:	orderLog
## назначение:	Логи заказов
##
#####################################################################################


class orderLog {

	function __construct()
	{
		$this->query = &CORE::$query;
	}

	public function addlog( $orderId, $name, $data=[] ) 
	{
		$dataToSave = [];
		$dataToSave["orderId"] = $orderId;
		$dataToSave["name"] = $name;
		if ( is_array($data) ) {
			$data = json_encode($data, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE );
		}
		$dataToSave["data"] = $data;
		$dataToSave["_REQUEST"] = json_encode($_REQUEST, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE );
		if ( !($orderId=$this->query->setdata("orders_log",$dataToSave)) ) {
			return false;
		}
		return true;
	}

}


