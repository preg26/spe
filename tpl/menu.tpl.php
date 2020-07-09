<div class="navbar navbar-default navbar-fixed-top" role="navigation" id="navigation">
	<div class="container-full">
		<div class="navbar-header">
		  <a class="navbar-brand nopad" href="#"><img alt="logo" src="img/logo.png" height="50px"></a>
		</div>
		<div class="navbar-collapse collapse">
			<ul class="nav navbar-nav">
				<li<?php if($page->name == 'index') echo ' class="active"'; ?>><a href="./<?php if(!empty($compte)) echo '?id='.$id; ?>">Accueil</a></li>
				<li<?php if($page->name == 'compte') echo ' class="active"'; ?>><a href="./compte.php">Comptes Bancaires</a></li>
				<?php if (!empty($global->conf->USE_COMPTA)): ?>
    				<li<?php if($page->name == 'comptabilite') echo ' class="active"'; ?>><a href="./comptabilite.php">Comptabilité</a></li>
    				<?php if($user->admin == 1) : ?>
    				<li<?php if($page->name == 'tresorerie') echo ' class="active"'; ?>><a href="./tresorerie.php">Trésorerie</a></li>
    				<?php endif; ?>
				<?php endif; ?>
				<li<?php if($page->name == 'user') echo ' class="active"'; ?>><a href="./user.php">Utilisateurs</a></li>
			</ul>
			<div class="navbar-collapse collapse navbar-right">
				<ul class="nav navbar-nav">
					<li><a href="./logout.php">Se déconnecter </a></li>
				</ul>
			</div>
		</div>
	</div>
</div>