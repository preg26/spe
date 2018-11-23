<?php
	require_once 'includes/preload.php';
		
	$action = GETPOST('action');
	$rowid = GETPOST('rowid');
	$date = GETPOST('new_date');
	
	$payment = new Payment($PDOdb);
    $payment->fetch($rowid);
	
	switch($action) {
		case 'move':
			$payment->move($date);
			break;
		default:
			break;
	}
	