<?php
	require_once 'includes/preload.php';
	
	$controller->check_user();
	$page->name = 'compte';
	$page->title = 'Gestion des comptes en banque';
	
	$action = GETPOST('action');
	$id = GETPOST('id');
	
	$object = new Account($PDOdb);
	if(!empty($id)) $object->fetch($id);
	$TComptes = $object->fetchAll();
	
	switch($action) {
		case 'view':
			// TODO check
			break;
		case 'update':
			$object->set_vars();
			$res = $object->save();
			if($res) {
				header('Location:./compte.php');
			} else {
				echo 'Erreur, veuillez contacter l\'administrateur';
			}
			exit;
			break;
		case 'create':
	
			$object->set_vars();
			$res = $object->save();
			if($res) {
				header('Location:./compte.php');
			} else {
				echo 'Erreur, veuillez contacter l\'administrateur';
			}
			exit;
			break;
		case 'delete':
			$res = $object->delete();
			if($res) {
				header('Location:./compte.php');
			} else {
				echo 'Erreur, veuillez contacter l\'administrateur';
			}
			break;
		case 'new':
		case 'edit':
			break;
		default:
			$action = 'sumary';
			break;
	}
	
	include 'tpl/header.tpl.php';
	include 'tpl/menu.tpl.php';
	include 'tpl/compte.tpl.php';
	include 'tpl/footer.tpl.php';