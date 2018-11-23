<?php

/**
 *	Parent class of all other classes
 */
abstract class CommonObject
{	
	/**
	 * @var int The object identifier
	 */
	public $rowid=0;

	/**
	 * @var int The user who create it
	 */
	public $fk_user_creat;

	/**
	 * @var int The last user who modify it
	 */
	public $fk_user_modif;

	/**
	 * @var datetime The date of last modif
	 */
	public $tms;

	/**
	 * @var string 		Error string
	 * @deprecated		Use instead the array of error strings
	 * @see             errors
	 */
	public $error;

	/**
	 * @var string[]	Array of error strings
	 */
	public $errors=array();

	/**
	 * @var string		Key value used to track if data is coming from import wizard
	 */
	public $import_key;

	/**
	 * @var mixed		Contains data to manage extrafields
	 */
	public $array_options=array();

	/**
	 * @var int[]		Array of linked objects ids. Loaded by ->fetchObjectLinked
	 */
	public $linkedObjectsIds;

	/**
	 * @var mixed		Array of linked objects. Loaded by ->fetchObjectLinked
	 */
	public $linkedObjects;

	/**
	 * @var string		Name of primary key
	 */
	public $primary_key='rowid';

	/**
	 * @var string		Column name of the ref field.
	 */
	protected $table_ref_field = '';

	/**
	 * @var PDO		Database handler
	 */
	public $PDOdb;


	public function __construct($PDOdb) {
		$this->PDOdb = $PDOdb;
	}

	public function set_vars() {
		foreach($_REQUEST as $champs => $value) {
			if(array_key_exists($champs,$this->TChamps)) {
				if($this->TChamps[$champs] == 'date') $value = date('Y-m-d',strtotime($value));
				$this->{$champs} = $value;
			}
		}
	}

	public function save() {
		$result = -1;
		if(empty($this->rowid)) {
			$result = $this->create();
		} else {
			$result = $this->update();
		}
		return $result;
	}

	public function create() {

		$champs = '`'.implode('`,`',array_keys($this->TChamps)).'`';
		$sql = 'INSERT INTO '.MAIN_DB_PREFIX.$this->table_element.'('.$champs.')';
		$sql.= ' VALUES(';
		foreach($this->TChamps as $champs => $type) {
			$val = $this->{$champs};
			switch($type) {
				case 'date':
					$sql.='\''.$val.'\'';
					break;
				case 'number':
				case 'float':
					if(empty($val)) $val = 0;
					$sql.=$val;
					break;
				case 'text':
				default:
					$sql.='\''.addslashes($val).'\'';
					break;
			}
			$sql.=',';
		}
		$sql = substr($sql,0,-1);
		$sql.=')';
		
		$req = $this->PDOdb->query($sql);
		if($req) {
			return 1;
		}else{
			return -1;
		}

	}

	public function update() {
		$champs = '`'.implode('`,`',array_keys($this->TChamps)).'`';
		$sql = 'UPDATE '.MAIN_DB_PREFIX.$this->table_element;
		$sql.= ' SET ';
		foreach($this->TChamps as $champs => $type) {
			if($champs != $this->primary_key) {
				$sql .= '`'.$champs.'` = ';
				switch($type) {
					case 'date':
						$sql.='\''.$this->{$champs}.'\'';
						break;
					case 'number':
					case 'float':
						$sql.=$this->{$champs};
						break;
					case 'text':
					default:
						$sql.='\''.addslashes($this->{$champs}).'\'';
						break;
				}
				$sql.=',';
			}
		}
		$sql = substr($sql,0,-1);
		$sql.=' WHERE '.$this->primary_key.' = '.$this->{$this->primary_key};
		
		$req = $this->PDOdb->query($sql);
		if($req) {
			return 1;
		}else{
			return -1;
		}
	}

	public function delete() {
		$sql = 'DELETE FROM '.MAIN_DB_PREFIX.$this->table_element;
		$sql.=' WHERE '.$this->primary_key.' = '.$this->{$this->primary_key};

		$req = $this->PDOdb->query($sql);
		if($req) {
			return 1;
		}else{
			return -1;
		}
	}

	public function fetch($id) {
		$sql = "SELECT *";
		$sql.= " FROM ".MAIN_DB_PREFIX.$this->table_element;
		if(!empty($id)) {
			$sql.= " WHERE ".$this->primary_key.' = '.$id;
		}

		$req = $this->PDOdb->query($sql);
		if($res = $req->fetch(PDO::FETCH_OBJ)){
			$this->generate_vars($res);
			return 1;
		}else {
			return -1;
		}
	}

	public function fetchAll() {
		$ret = array();
		// Get user
		$sql = "SELECT rowid";
		$sql.= " FROM ".MAIN_DB_PREFIX.$this->table_element;

		$req = $this->PDOdb->query($sql);
		while($res = $req->fetch(PDO::FETCH_OBJ)){
			$class = get_class($this);
			$object = new $class($this->PDOdb);
			$object->fetch($res->rowid);
			$ret[$object->rowid] = $object;
		}
		return $ret;
	}

	private function generate_vars($array) {
		foreach($array as $elem => $value) {
			if($elem != 'PDOdb' && !is_array($value)) {
				$this->{$elem} = $value;
			}else if(is_array($value)) {
				$this->generate_vars($value);
			}
		}
	}

}
