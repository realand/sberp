<?
#####################################################################################
## Название:	Функции хелперы
## назначение:	
##
#####################################################################################


function _error($error,$data="")
{
	$arr = ["action" => "error", "error" => $error];
	if ( $data ) {
		$arr["errorDetail"] = $data;
	}
	return $arr;
}

function _error_j($error)
{
	$data = ["action" => "error", "error" => $error];
	$data = json_encode($data, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE );
	return $data;
}

function _success($data=null)
{
    if (!$data && !is_array($data)) {
        return ["action" => "success"];
    }
    return ["action" => "success", "result" => $data];
}

function _success_j($data=null)
{
	if (!$data) {
		return json_encode(["action" => "success"], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE );
	}

	$data = ["action" => "success", "result" => $data];
	$data = json_encode($data, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE );
	return $data;
}


function IntToDate($e_int="", $e_space = "-")
{
    if (!trim($e_int)) {
	$e_int = time();
    }
    return date("d" . $e_space . "m" . $e_space . "Y G:i", $e_int);
}


function ReplaceNoSecure( $variable )
{
	$prev_length = strlen($variable);
	$variable = str_replace("<?", "", $variable);
	$variable = str_replace("?>", "", $variable);
	//$variable = str_replace("select", "", $variable);
	$variable = str_replace("union", "", $variable);
	$variable = str_replace("update ", "", $variable);
	$variable = str_replace("insert ", "", $variable);
	$variable = str_replace("system", "", $variable);
	$variable = str_replace("./", "", $variable);
	$variable = str_replace("../", "", $variable);
	//$variable = str_replace("'", "", $variable);
	//$variable = str_replace("\"", "", $variable);
	return $variable;
}




