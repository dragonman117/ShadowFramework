<?php
/*
 * Model Base Class
 *
 *      This is the core database functions that each model can build off of
 *      It is not meant to be a stand alone database interaction, but should be inherited by a
 *      child class to define module specific interactions.
 */
require_once('settings.php');

class Model{
    //Table must be defined by the child class
	var $table;
	var $database;
	var $host;
	var $username;
	var $password;
	var $link = 0; // Current Connection results
	
	//Errors
	var $errors;
	
	function __construct(){
        global $SETTINGS;
        $this->database = $SETTINGS['database'];
        $this->host = $SETTINGS['host'];
        $this->username = $SETTINGS['username'];
        $this->password = $SETTINGS['password'];
	}

    //Set Table
    //This function is not advised it exists to providee a way to  use the model class as a stand alone class.
    function set_table($table){
        $this->table = $table;
    }
	
	//Connect to DB
	function connect(){
		if(is_int($this->link)){
			$this->link = new mysqli($this->host, $this->username, $this->password, $this->database);
			if($this->link->connect_errno){
				$this->errors = 'Database Connection Error: '.$this->link->connect_error;
			}
		}
	}

    //Last Link
    function last_link(){
        return mysqli_insert_id($this->link);
    }
    
    //Function Query
    //Will return an associated array or array of associated arrays both get/getall
    function query($string, $force_array = false){
        $this->connect();
        $res = $this->link->query($string);
        if($res){
            $array = array();
            //var_dump($res);
            if(is_bool($res)){
                return true;
            }
            $rows = $res->num_rows;
            if($rows > 1 or $force_array){
                return $res->fetch_all($resulttype = MYSQLI_ASSOC);
            }else{
                return $res->fetch_assoc();
            }
        }else{
            return false;
        }
    }
    
    //Count
    function count($and = ''){
        $sql = "SELECT COUNT(*) AS SUM FROM `{$this->table}` {$and}";
        $res = $this->query($sql);
        return $res['SUM'];
    }
    
    //Pagination
    function page_count($page=1, $and=''){
        $ppc = $GLOBALS['ipp'];
        $results = array();
        $articles = $this->count($and);
        //Get the maximum number of pages
        $pages = ceil($articles/$ppc);
        //pages array
        if($pages > 10 && $page > 6){
            $results['start'] = $page-5;
            $results['end'] = $page+5;
        }else{
            $results['start'] = 1;
            $results['end'] = $pages;
        }
        $results['total'] = $pages;
        return $results;
    }
    
    function get_page($page, $and = '',  $field='id', $sort = 'DESC', $extra = ''){
        $ppc = $GLOBALS['ipp'];
        $start = $ppc * ($page-1);
        $and = " {$and} ORDER BY `{$field}` {$sort} {$extra} LIMIT {$start}, {$ppc}";
        return $this->get_all($and);
    }
	
    //Get all
    function get_all($extra = ''){
        $string = "SELECT * FROM `".$this->table."`".$extra;
        return $this->query($string, true);
    }
    
    //Get
    function get($value, $key = 'id', $extra=""){
        $string = "SELECT * FROM `".$this->table."` WHERE `{$key}` = '{$value}' {$extra}";
        return $this->query($string);
    }

    //Special query
    function special($and){
        $string = "SELECT * FROM `{$this->table}` {$and}";
        return $this->query($string);
    }
    
    //Add Edit
    //Takes a key value array to add to db
    function ae($array, $id_str = 'id', $and = ''){
        $this->connect();
        if(array_key_exists($id_str, $array)){
            $type = 'UPDATE `'.$this->table.'` SET ';
            $id = $array[$id_str];
            unset($array[$id_str]);
        }else{
            $type = 'INSERT INTO `'.$this->table.'` ';
        }
        $string = $type;
        $keys = '';
        $values = '';
        $update = '';
        $num = count($array);
        foreach($array as $key=>$value){
            $keys .= " `{$key}`";
            $value = mysqli_real_escape_string($this->link, $value);
            $values .= ' "'.$value.'"';
            $update .= ' `'.$key.'` = "'.$value.'"';
            if($num > 1 ){
                $keys .=",";
                $values .=",";
                $update .= ",";
            }
            $num -= 1;
        }
        if(isset($id)){
            $string .= $update." WHERE `{$id_str}` =".$id;
        }else{
            $string .= '('.$keys.' ) VALUES ('.$values.')';
        }
        $string .= $and;
        $this->query($string);
        return true;
    }
    
    //Delete
    function delete($value, $key = 'id', $and = ''){
        $string = "DELETE FROM `".$this->table."` WHERE `".$key.'` = "'.$value.'" '.$and ;
        return $this->query($string);
    }
}
?>