<!DOCTYPE html>
<html lang="en">
	<head>
	    <meta charset="utf-8">
	    <meta http-equiv="X-UA-Compatible" content="IE=edge">
	    <meta name="viewport" content="width=device-width, initial-scale=1">
	    <meta name="description" content="<?php echo $page->description ?>">
	    <meta name="keywords" content="<?php echo $page->keywords ?>">
	    <meta name="author" content="SAINT-PATRICE Arnaud">
	    <link rel="icon" href="../../favicon.ico">
	
	    <title><?php echo $page->title ?></title>
	    <?php foreach($page->TCss as $css):?>
	    <link href="<?php echo $css; ?>" rel="stylesheet">
	    <?php endforeach; ?>
	    
	</head>
	<body>
