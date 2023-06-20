<?
#####################################################################################
## Название:	Query
## назначение:	Простой класс для работы с базой
##
#####################################################################################


class Query
{
	var $source = null;
	var $c_last_create = 0;
	function __construct($l_connect_data = "",$l_pre_param = "")
	{
		global $config;
		
		$this->c_last_create = time();


		if (isset($l_pre_param["source"]))
			$this->source = $l_pre_param["source"];
		else
		{
			if ($l_connect_data != "")
			{
				$this->source = mysqli_connect(
					$l_connect_data["dbip"],
					$l_connect_data["dbuser"],
					$l_connect_data["dbpass"],
					$l_connect_data["dbname"]
					);

				mysqli_query($this->source, "SET character_set_results = '".$l_connect_data["dbcodepage"]."', character_set_client = '".$l_connect_data["dbcodepage"]."', character_set_connection = '".$l_connect_data["dbcodepage"]."', character_set_database = '".$l_connect_data["dbcodepage"]."', character_set_server = '".$l_connect_data["dbcodepage"]."'");
				mysqli_query($this->source, "SET GLOBAL sql_mode='ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION';");//set GLOBAL sql_mode='ONLY_FULL_GROUP_BY,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION'


			}
			else
			{
				$this->source = mysqli_connect(
					$config["dbip"],
					$config["dbuser"],
					$config["dbpass"],
					$config["dbname"]
				);
				
				mysqli_query($this->source,"SET character_set_results = '".$config["dbcodepage"]."', character_set_client = '".$config["dbcodepage"]."', character_set_connection = '".$config["dbcodepage"]."', character_set_database = '".$config["dbcodepage"]."', character_set_server = '".$config["dbcodepage"]."'");
			}
			
		}

		$config["db_link"] = $this->source;
			
		
		

	}//end_ func_ init

	function destroy()
	{
		mysqli_close($this->source);
	}
	function exec($query)
	{


		global $engine;
		
		$this->last_query = $query;
		if (isset($engine->logs_save) && $engine->logs_save == true)
		{
			$l_save_log = false;
			//Проверяем на delete
			$l_new_query = str_ireplace("delete","select * ",$query);
			if ($l_new_query != $query)
			{
				$this->result = mysqli_query($this->source,$l_new_query);
				$l_text_log = $this->getall();
				
				ob_start();
				echo "<pre>";print_r($l_text_log);echo "</pre>";
				$l_where_data = ob_get_contents(); 
				ob_end_clean();
				
				$l_save_log = true;
			}
			//Проверяем на delete

			
			//Проверяем на drop
			$l_new_query = str_ireplace("drop","",$query);
			if ($l_new_query != $query) $l_save_log = true;
			//Проверяем на drop
			
			//Проверяем на update
			$l_new_query = str_ireplace("update","",$query);
			if ($l_new_query != $query) $l_save_log = true;
			//Проверяем на update
			
			//Проверяем на insert
			$l_new_query = str_ireplace("insert","",$query);
			if ($l_new_query != $query) $l_save_log = true;
			//Проверяем на insert
			
			
			//Пишем лог
			if ($l_save_log == true)
			{
				$l_text = "-----------------------------------------------------------------\n";
				$l_text .= "Дата: ".date("d-m-Y H:i:s")."\n";
				$l_text .= "Логин: ".$engine->user["login"]."\n";
				$l_text .= $query."\n";
				if (isset($l_where_data)) $l_text .= $l_where_data."\n";
				
				file_put_contents(ROOT_DIR."/engine/logs/".date("d-m-Y").".txt",$l_text,FILE_APPEND);

			}
			//Пишем лог
		}
		
		
		try {
			$this->result = mysqli_query($this->source,$query);
		} 
		catch (Exception $e) {
			$this->result = false;
		}

		$this->last_query_id = mysqli_insert_id($this->source);

		return $this->result;
   	}
	function get($name)
	{
	
		if ($this->parts==0) return false;
		return $this->parts[$name];
	}


	function getrow()
	{
		if ($this->parts==0) return false;
		return $this->parts;
	}

	function next()
	{
		global $engine;

		if ($this->result === false) return false;
		
		
		$this->parts = mysqli_fetch_array($this->result,MYSQLI_ASSOC);
		
		if ($this->parts === false)
		{
			if (mysqli_errno() > 0)
			{
				echo mysqli_errno()." ".mysqli_error()."<br/>";
				echo "<pre>";print_r($this->last_query);echo "</pre>";
				echo "class - ".$engine->last_module_name."<br/>";
				
			}
		}
//echo "<pre>";print_r(mysqli_error());echo "</pre>";
		
		return $this->parts;
	}
	function rows()
	{
	//	if ($this->result["num_rows"] != 0) return mysqli_num_rows( $this->result ); else return 0;
		if (isset($this->result->num_rows)) return $this->result->num_rows; else return 0;
	}


	function getall_old($l_column_name_temp = "")
	{	
	
		$l_column_name = str_replace("[]","",$l_column_name_temp);
		if ($l_column_name_temp > $l_column_name) $l_mass = true; else $l_mass = false;
		$l_result = array();
		while( $this->next())
		{
			if ($l_column_name != "")
			{
				if ($l_mass == true)
					$l_result[$this->parts[$l_column_name]][] = $this->parts;
				else
					$l_result[$this->parts[$l_column_name]] = $this->parts;
			}
			else $l_result[] = $this->parts;
		}

		return $l_result;
	}//end_ function_ getallfromcurrentrows

	function getall($l_columns_names = "")
	{	
		$l_result = array();

		if ($l_columns_names != "")
		{
			$l_columns_names = explode(",",$l_columns_names);
		//	$l_columns_names = array_reverse($l_columns_names);

			$l_eval_text = "";
			foreach ($l_columns_names as $l_column_name)
			{
				$l_column_name = explode("[]",$l_column_name);
				if (count($l_column_name) > 1) $l_eval_text .= "[\$this->parts[\"".$l_column_name[0]."\"]][]"; else $l_eval_text .= "[\$this->parts[\"".$l_column_name[0]."\"]]";
			}
		
			$l_eval_text = "\$l_result".$l_eval_text." = \$this->parts;";
			while( $this->next())@eval($l_eval_text);
		}
		else
			while( $this->next()) $l_result[] = $this->parts;


		return $l_result;
	}//end_ function_ getallfromcurrentrows


	function getdata( $e_table, $e_columns = "",$e_where = "")
	{
		global $config;
		global $engine;
		
		if ($e_columns != "") $e_columns = "`".str_replace(",","`,`",$e_columns)."`"; else $e_columns = "*";
		
		$e_table = ReplaceNoSecure($e_table);
		
		$l_query = "SELECT ".$e_columns." FROM `".$e_table."` WHERE (1=1) ".$e_where;
		
		$l_result = $this->exec( $l_query );
		
		$engine->mquery->usedtables[$e_table] = $e_table;
		
		return $l_result;
	}
	
	function create($l_tb_name)
	{
		global $engine;

		$this->exec("CREATE TABLE `".$l_tb_name."` (`id` int NOT NULL AUTO_INCREMENT,`_create` int NOT NULL,`_sort` int NOT NULL ,PRIMARY KEY (`id`)) ENGINE=InnoDB");
	//	$this->exec("SHOW COLUMNS FROM `".$l_tb_name."`");	
		$this->exec("DESCRIBE `".$l_tb_name."`");

		return $this->getall('Field');
		//Проверяем есть ли таблица и создаем шаблон солонок
	}

	//задать переменные в нултокены
	function setdata($l_tb_name,$l_data,$l_where = "",$l_check_columns = false, $l_tpl_columns = array())
	{
		$l_create = $this->c_last_create;
		$this->c_last_create++;
		
		/*
		if ($l_check_columns == true && count($l_tpl_columns) == 0)
		{
			$this->exec("DESCRIBE `".$l_tb_name."`");
			$l_tpl_columns = $this->getall('Field');
			
			if (count($l_tpl_columns) == 0) $l_tpl_columns = $this->create($l_tb_name);
		}
		*/
		$l_create_table = false;
		if ($l_check_columns == true && count($l_tpl_columns) == 0)
		{

			try {

				$this->exec("DESCRIBE `".$l_tb_name."`");
				
				$l_tpl_columns = $this->getall('Field');
			} 
			catch (Exception $e) {
				$l_tpl_columns = $this->create($l_tb_name);
				$l_create_table = true;
			}

			
		}
		
	
		$l_add_columns = "";
		$l_update_array = array();
		

	//	if (!isset($l_data["_create"]) || $l_data["_create"] == "") $l_data["_create"] = $l_create ;
	//	if (!isset($l_data["_sort"]) || $l_data["_sort"] == "") $l_data["_sort"] = $l_create ;
		if (!isset($l_data["_create"]) && !isset($l_data["id"])  && $l_where == "") $l_data["_create"] = $l_create ;
		if (!isset($l_data["_sort"]) && !isset($l_data["id"]) && $l_where == "") $l_data["_sort"] = $l_create ;
		$l_data["_modify"] = array("type" => "INT","value" => $l_create);
		
		foreach ($l_data as $l_column_name => $l_item)
		{
			$l_type = "TEXT";
			
			if (is_array($l_item))
			{
				if (isset($l_item["type"])) $l_type = $l_item["type"];
				$l_value = $l_item["value"];
			}
			else
				$l_value = $l_item;
			
			$l_value = str_replace ("'","\'",$l_value);

		//	if ($l_column_name == '_hash_1' || $l_column_name == '_hash_2' || $l_column_name == '_create' || $l_column_name == '_sort')
			if ($l_column_name == '_create' || $l_column_name == '_sort')
				$l_update_array[] = $l_column_name."=".$l_value;
			else
			$l_update_array[] = "`".$l_column_name."`='".$l_value."'";
			

			if ($l_check_columns == true && !isset($l_tpl_columns[$l_column_name]))
			{

				$l_tpl_columns[$l_column_name] = "";
				if ($l_add_columns != "") $l_add_columns .= ",";
				$l_add_columns .= " ADD `".$l_column_name."` ".$l_type." NOT NULL";
			}
		}
		

		
		if ($l_add_columns != "")
		{

			$this->exec("ALTER TABLE `".$l_tb_name."` ".$l_add_columns);
		}
		$l_update_str = implode(",",$l_update_array);
		
		$l_str = "";
		foreach ($l_data as $l_key => $l_val)
		{
			
			if (is_array($l_val)) $l_val = $l_val["value"];

			$l_data[$l_key] = str_replace ("'","\'",$l_val);
			if ($l_str != "") $l_str .=",";
			$l_str .= "`".$l_key."` = '".str_replace ("'","\'",$l_val)."'";
		}
			
		$l_vars = "`".implode("`,`",array_keys($l_data))."`";
		$l_vars_data = "'".implode("','",$l_data)."'";
		
		
		
		if ($l_where != "")
			$l_query = "UPDATE `".$l_tb_name."` SET ".$l_str." WHERE (1=1) ".$l_where;
		else
			$l_query = "INSERT INTO `".$l_tb_name."` SET ".$l_str." ON DUPLICATE KEY UPDATE ".$l_str;
		
		if ($this->exec($l_query) === false && (int)$this->sql_error == 0) {
			$this->setdata($l_tb_name,$l_data,$l_where,true);
			$this->sql_error = 1;
		} else 
			$this->sql_error = 0;
	
	
		//$this->last_query_id
		//return $l_tpl_columns;
		return $this->last_query_id;		
	}//end_ function_ setdata
	
	
	function remove( $e_table,$e_id="",$l_where = "")
	{
		global $engine;



		if ($e_id != "") $l_where = " and id = ".$e_id." ".$l_where;
		if ($l_where != "") $l_where = "WHERE (1=1) ". $l_where;
		
	
		
		if ($l_where != "") return $this->exec("delete from `".$e_table."` ".$l_where); else return false;
	}//end_ function_ remove
	
	
	
	function load_data_from_base( $e_table, $e_id,$e_control_collection )
	{
		global $engine;

		$l_result = array();

		
		//получаем данные из списка
		$this->getdata( $e_table,""," and `id` = '".$e_id."'");
		//echo "<pre>";print_r($this->last_query);echo "</pre>";
		

		if ( $this->rows()==0 )
		{
			$engine->c_warnings[] = "mBaseModule, function load_data_from_base : Количество загружаемых данных=0";
			return $l_result;
		}
		$l_data = $this->next();


		foreach( $e_control_collection as $l_control )
		{
			$l_nameofcontrol  = $l_control["control_dbname"];
			if ( $l_control["control_load"])
			{
				if (isset($l_data[$l_nameofcontrol])) $l_value = $l_data[$l_nameofcontrol]; else $l_value = "";
				if ( isset($l_value) && $l_value!="" )
				{
					$l_result[$l_nameofcontrol] = $l_value;
				}
			}
		}//end_ foreach

		return $l_result;

	}//end_ function_ load_base
	function sort($l_table,$l_where,$l_sort,$l_orderby,$l_prev,$l_last)
	{
	
		global $engine;
		
		if ($l_prev > $l_last) 
			$l_limit = " limit ".$l_last.",".(abs($l_prev-$l_last)+1);
		else
			$l_limit = " limit ".$l_prev.",".($l_last-$l_prev+1);
		
		
		$l_query = "SELECT id,_sort FROM `".$l_table."` WHERE (1=1) ".$l_where.$l_orderby." ".$l_sort.$l_limit;
		
		$this->exec($l_query);
		$l_data = $this->getall();
		if ($l_prev > $l_last) $l_data = array_reverse($l_data);

		
		
		$l_result = array();
		for ($i=1;$i<count($l_data);$i++)
		{
			$l_result[$l_data[$i]["id"]] = $l_data[$i-1]["_sort"];
		}
		$l_result[$l_data[0]["id"]] = $l_data[count($l_data)-1]["_sort"];
		
		
		
		foreach ($l_result as $l_id => $l_sort_id)
			$this->exec("UPDATE `".$l_table."` SET _sort = ".$l_sort_id." WHERE id = ".$l_id);

		return "";
	}//end_ func_ sort
	function update_database( $e_table, $e_id,$e_control_collection,$l_data = array())
	{
	
		if ($e_id != "") $l_data["id"] = $e_id;
			
		foreach( $e_control_collection as $l_control )
		{
			$l_name 	= $l_control["control_dbname"];
			$l_instance	= &$l_control["instance"];
			$l_value	= $l_instance->getvalue();

			if ( !$l_control["control_save"] )
			{
				continue;
			}

			$l_data[$l_name] = $l_value;
		}//end_ foreach


		return $this->setdata($e_table,$l_data,"",true);
	}//end_ function_ update_value

	function check_cache($l_table)
	{
		global $engine;
		
		$l_tb_name = "cd_root_cache_[]";
		
		try {
			$this->exec("DESCRIBE `".$l_tb_name."`");
		} 
		catch (Exception $e) {
			$this->exec("CREATE TABLE `".$l_tb_name."` (`id` int NOT NULL AUTO_INCREMENT,`_create` int NOT NULL,`_sort` int NOT NULL,`cache_date` text NOT NULL,`tables` text NOT NULL ,PRIMARY KEY (`id`)) ENGINE=InnoDB");
		}

		$engine->mquery->exec("update `".$l_tb_name."` set cache_date =  '' where `tables` like '%|".$l_table."|%'");
	
	}
	
}//end_ class_














?>