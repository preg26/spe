<?php
require_once 'includes/preload.php';

$page->TJavascript[] = 'js/spe-tresorerie.js';

$controller->check_user();
$page->name = 'tresorerie';
$page->title = 'Compte de résultat';
if($user->admin != 1)  {
	header('Location:./');
}
$TFilterYears = array();
$end_year = (int) date("Y", strtotime("+1 year", time()));
for($i = 2016; $i <= $end_year; $i ++) {
	$TFilterYears[$i] = $i;
}

$action = GETPOST('action');
$id = GETPOST('id');
$ttc = GETPOST('ttc');
$year = (int) GETPOST('year');
$ref = GETPOST('ref');

if (empty($id)) {
	$id = 1;
}
if (empty($ttc)) {
	$ttc = 0;
}
if (empty($year)) {
	$year = date("Y", time());
}

$compte = new Account($PDOdb);
$compte->fetch($id, $year);
$TComptes = $compte->fetchAll();
$tresorerie = new Tresorerie($PDOdb,$id,$year);
$tresorerie->fetch($ttc,$ref);

$action = 'view';

if ($action == 'view') {
	include 'tpl/header.tpl.php';
	include 'tpl/menu.tpl.php';
	include 'tpl/tresorerie.tpl.php';
	include 'tpl/footer.tpl.php';
}