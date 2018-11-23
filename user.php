<?php
	require_once 'includes/preload.php';
	
	$controller->check_user();
	$page->name = 'user';
	$page->title = 'Gestion des utilisateurs';
	
	$action = GETPOST('action');
	$id = GETPOST('id');
	
	$object = new User($PDOdb);
	if(!empty($id)) $object->fetch($id);
	$TObject = $object->fetchAll();
	
	switch($action) {
		case 'view':
			// TODO check
			break;
		case 'update':
			$ex_pass = $object->pass_crypted;
			$object->set_vars();
			if(empty($object->pass_crypted)) $object->pass_crypted = $ex_pass;
			$res = $object->save();
			if($res) {
				header('Location:./user.php');
			} else {
				echo 'Erreur, veuillez contacter l\'administrateur';
			}
			exit;
			break;
		case 'create':
			$object->set_vars();
			$res = $object->save();
			if($res) {
				header('Location:./user.php');
			} else {
				echo 'Erreur, veuillez contacter l\'administrateur';
			}
			exit;
			break;
		case 'delete':
			$res = $object->delete();
			if($res) {
				header('Location:./user.php');
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
	
	// Interface pour login
	include 'tpl/header.tpl.php';
	include 'tpl/menu.tpl.php';
	include 'tpl/user.tpl.php';
	include 'tpl/footer.tpl.php';