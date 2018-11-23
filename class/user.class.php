<?php

/**
 * User class
 */
class User extends CommonObject
{
	public $element='user';
	public $table_element='user';
	public $TChamps = array(
				'rowid'=>'number'
				,'login'=>'string'
				,'firstname'=>'string'
				,'lastname'=>'string'
				,'email'=>'string'
				,'admin'=>'number'
				,'pass_crypted'=>'string'
			);
	
	var $rowid;
	var $email;
	var $skype;
	var $lastname;
	var $firstname;
	var $job;
	var $address;
	var $zip;
	var $town;
	var $country;
	var $office_phone;
	var $office_fax;
	var $user_mobile;
	var $admin;
	var $login;
	
	//! Encrypted password in database (always defined)
	var $pass_crypted;
	
	var $datec;
	var $datem;
	var $datelastlogin;
	var $datepreviouslogin;
	var $statut;
	
	public function login($login, $password) {
		if(!empty($login) && !empty($password)) {
			$sql = "SELECT ".$this->primary_key;
			$sql.= " FROM ".MAIN_DB_PREFIX.$this->table_element;
			$sql.= ' WHERE login = "'.$login.'"';
			$sql.= ' AND pass_crypted = "'.$password.'"';
			
			$req = $this->PDOdb->query($sql);
			if($res = $req->fetch(PDO::FETCH_OBJ)) {
				$this->fetch($res->rowid);
			}
		}
	}
	
	public function update() {
		$this->pass_crypted = sha1($this->pass_crypted);
		return parent::update();
	}
	
	public function create() {
		$this->pass_crypted = sha1($this->pass_crypted);
		return parent::create();
	}
	
}