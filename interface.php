<?php
	require_once 'includes/preload.php';
		
	$action = GETPOST('action');
	$rowid = GETPOST('rowid');
	$date = GETPOST('new_date');
	$bank = GETPOST('bank');
	
	$payment = new Payment($PDOdb);
    $payment->fetch($rowid);
	
	switch($action) {
		case 'move':
			$payment->move($date,$bank);
			break;
		default:
			break;
	}
	