<?php
	require_once 'includes/preload.php';
	
	$controller->check_user();
	$page->name = 'comptabilite';
	$page->title = 'Gestion des catégories comptables';
	
	$action = GETPOST('action');
	$id = GETPOST('id');
	$is_parent = GETPOST('is_parent');
	
	$object = new ComptaCateg($PDOdb);
	if(!empty($id)) $object->fetch($id);
	$TComptes = $object->fetchParents();
	$TNotAllowed = array(TRESO_CA_CATEG_ID, TRESO_EX_CATEG_ID, TRESO_CH1_CATEG_ID, TRESO_CH2_CATEG_ID);
	$deleteRight = (!in_array($id, $TNotAllowed));
	
	switch($action) {
		case 'view':
			// TODO check
			break;
		case 'update':
			$object->set_vars();
			$res = $object->save();
			if($res) {
				header('Location:./comptabilite.php');
			} else {
				echo 'Erreur, veuillez contacter l\'administrateur';
			}
			exit;
			break;
		case 'create':
	
			$object->set_vars();
			$res = $object->save();
			if($res) {
				header('Location:./comptabilite.php');
			} else {
				echo 'Erreur, veuillez contacter l\'administrateur';
			}
			exit;
			break;
		case 'delete':
			$res = $object->delete();
			if($res) {
				header('Location:./comptabilite.php');
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
	include 'tpl/comptabilite.tpl.php';
	include 'tpl/footer.tpl.php';