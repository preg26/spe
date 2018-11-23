<?php

/**
 * ComptaCateg class
 */
class ComptaCateg extends CommonObject
{
	public $element='comptacateg';
	public $table_element='comptacateg';
	public $TChamps = array(
				'rowid'=>'number'
				,'label'=>'string'
				,'code'=>'string'
				,'ordre'=>'number'
				,'plafond'=>'float'
				,'fk_parent'=>'number'
			);
	
	var $label;
	var $code_compta;
	var $plafond;
	var $ordre;
	var $fk_parent;
	var $parent;
	var $TChilds;
	
	public function fetch($id) {
		$res = parent::fetch($id);
		if(!empty($this->fk_parent)){
			$categParent = new self($this->PDOdb);
			$categParent->fetch($this->fk_parent);
			$this->parent = $categParent;
		}
		return $res;
	}

	public function fetchParents() {
		$ret = array();
		$sql = "SELECT rowid";
		$sql.= " FROM ".MAIN_DB_PREFIX.$this->table_element;
		$sql.= " WHERE fk_parent is NULL OR fk_parent=0";
		$sql.= " ORDER BY code";
		
		$req = $this->PDOdb->query($sql);
		while($res = $req->fetch(PDO::FETCH_OBJ)){
			$class = get_class($this);
			$object = new $class($this->PDOdb);
			$object->fetch($res->rowid);
			
			$TChilds = array();
			$sql2 = "SELECT rowid";
			$sql2.= " FROM ".MAIN_DB_PREFIX.$this->table_element;
			$sql2.= " WHERE fk_parent=".$res->rowid;
			$sql2.= " ORDER BY code";
			$req2 = $this->PDOdb->query($sql2);
			while($res2 = $req2->fetch(PDO::FETCH_OBJ)){
				$object2 = new $class($this->PDOdb);
				$object2->fetch($res2->rowid);
				$TChilds[$object2->rowid] = $object2;
			}
			$object->TChilds = $TChilds;
			$ret[$object->rowid] = $object;
		}
		return $ret;
	}
	
	public function show(){
		return $this->code.'-'.$this->label;
	}
	public function save(){
		if(empty($this->ordre)) $this->ordre = 1;
		if(empty($this->plafond)) $this->plafond = 0;
		if(empty($this->fk_parent) || $this->fk_parent == $this->rowid) $this->fk_parent = 0;
		return parent::save();
	}
	
	public function fetchAll() {
		$ret = array();
		// Get user
		$sql = "SELECT rowid";
		$sql.= " FROM ".MAIN_DB_PREFIX.$this->table_element;
		$sql.= " ORDER BY code ASC";
		
		$req = $this->PDOdb->query($sql);
		while($res = $req->fetch(PDO::FETCH_OBJ)){
			$class = get_class($this);
			$object = new $class($this->PDOdb);
			$object->fetch($res->rowid);
			$ret[$object->rowid] = $object;
		}
		return $ret;
	}
	
	public function delete() {
		$sql = "UPDATE ".MAIN_DB_PREFIX.$this->table_element;
		$sql.= " SET fk_parent = 0";
		$sql.= " WHERE fk_parent = ".$this->rowid;
		$req = $this->PDOdb->query($sql);
		
		$sql = "UPDATE ".MAIN_DB_PREFIX."payment";
		$sql.= " SET fk_categcomptable = 0";
		$sql.= " WHERE fk_categcomptable = ".$this->rowid;
		$req = $this->PDOdb->query($sql);
		return parent::delete();
	}
}