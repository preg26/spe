<?php
	session_start();
	/**
	 * Fichier autoload
	 * 
	 * Charge toutes les class du dossier
	 */

	function __autoload($class)
	{
		require_once('class/'. strtolower($class) . '.class.php');
	}
	
	function GETPOST($key) {
		$ret = null;
		if(isset($_REQUEST[$key])) {
			$ret = $_REQUEST[$key];
		}
		return $ret;
	}
	
	/*
	 * View Price
	 * This function convert float number to text number with seperate
	 * Float	$number		Ex : 8125364.12
	 * 
	 * return String	$number	Ex : 8 125 364.12
	 */
	function view_price($number, $virgule = true) {
		// au cas où on refais sur 2 decimales
		$number = save_price($number);
		// Séparateur php des miliers
		if($virgule) {
			$number = number_format($number, 2, '.', ' ');
		} else {
			$number = number_format($number, 0, '', ' ');
		}
		return $number;
	}
	
	function save_price($number) {
		$number = str_replace(',', '.', $number);
		$number = ((floor($number) == round($number, 2)) ? $number: round($number, 2));
		return $number;
	}
