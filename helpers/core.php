<?
#####################################################################################
## Ќазвание:	ядро
## назначение:	
##
#####################################################################################


class Core {
	public static $query = "";
	public static $order = "";
	public static $orderLog = "";
}

Core::$query = new query();
Core::$order = new order();
Core::$orderLog = new orderLog();

