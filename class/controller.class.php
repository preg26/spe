<?php

class Controller
{
	
	// Publics var
	public $user;


	/**
	 *	Constructor
	 *
	 * @param	$db		DataBase	Dolibarr Database Object
	 * @param	$conf	array()		Array with all Dolibarr configs
	 * @param	$user	User		Dolibarr User Object
	 */
	function __construct($user) {
		$this->user = $user;
	}

     /**
	 *	Function login
	 * @param	$page	Page	Object
	 *
	 * @return	TODO
	 */
    function login($login,$password)
    {
    	// TODO
    	$crypted_pass = sha1($password);
		$this->user->login($login,$crypted_pass);
		if(!empty($this->user->rowid)) {
			$_SESSION['logged'] = $this->user->rowid;
			return 1;
		} else {
			return -1;
		}
		
    }
	
	function logout() {
		session_destroy();
		unset($_SESSION);
	}
	
	function check_user() {
		if(!empty($_SESSION['logged'])) {
			$this->user->fetch($_SESSION['logged']);
		} else {
			header('Location:./login.php');
		}
	}
}