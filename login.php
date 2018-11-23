<?php
	require_once 'includes/preload.php';
	
	$action = GETPOST('action');
	$login = GETPOST('login');
	$password = GETPOST('password');
	$view = '';
	
	switch($action) {
		case 'connexion':
			$result = $controller->login($login,$password);
			if($result) {
				header("Location:./");
			}
			break;
		default:
			$view = 'fiche';
			break;
	}
	
	// Cas vue interface pour login
	if($view == 'fiche') {
		include 'tpl/header.tpl.php';
		include 'tpl/connexion.tpl.php';
		include 'tpl/footer.tpl.php';
	}