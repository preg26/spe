<?php
	require_once 'includes/autoload.php';
	
	// Global prefix database
	define('MAIN_DB_PREFIX', 'spe_');
	define('TRESO_CA_CATEG_ID', 4);
	define('TRESO_EX_CATEG_ID', 8);
	define('TRESO_CH1_CATEG_ID', 6);
	define('TRESO_CH2_CATEG_ID', 7);
	
	// BDD et formatage utf8
	$PDOdb = SPDO::getInstance();
	$PDOdb->query("SET NAMES utf8");
	
	// Instanciations diverses
	$page = new Page;
	$user = new User($PDOdb);
	
	$controller = new Controller($user);
	
	$TMonths = array(1=>'janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre');
	$TMonthsShort = array(1=>'Jan.', 'Fév.', 'Mars', 'Avril', 'Mai', 'Juin', 'Juil.', 'Août', 'Sept.', 'Oct.', 'Nov.', 'Déc.');
	$TDays = array(1=>'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche');
	
	// Liste js
	$page->TJavascript[] = 'js/jquery.min.js';
	$page->TJavascript[] = 'js/jquery-ui/jquery-ui.min.js';
	$page->TJavascript[] = 'includes/bootstrap/js/bootstrap.js';
	$page->TJavascript[] = 'js/select2.min.js';
	
	// Liste css
	$page->TCss[] = 'includes/bootstrap/css/bootstrap.css';
	$page->TCss[] = 'js/jquery-ui/jquery-ui.min.css';
	$page->TCss[] = 'css/style.css';
	$page->TCss[] = 'css/select2.css';
	
	