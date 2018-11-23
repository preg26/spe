<?php
	require_once 'includes/preload.php';
	
	$controller->check_user();
	$view = '';
	
	$result = $controller->logout($login,$password);
	header('Location:./');
