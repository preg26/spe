
<div class="container-full mt50">
	<div class="navbar-left col-md-2 pt15" id="page-sidebar">
		<h4>Liste des utilisateurs</h4>
		<ul class="nav nav-pills nav-stacked">
	      <?php
	      	if(!empty($TObject)) {
	      		foreach($TObject as $user) {
	      			?>
	      <li>
	      	<a href="?action=edit&id=<?php echo $user->rowid; ?>">
	      		<div class="col-md-12 nopad">
	      			<span class="glyphicon glyphicon-user"></span> <?php echo $user->login.' ('.$user->firstname.' '.$user->lastname.')'; ?>
	      		</div>
	      		<div class="clear"></div>
	      	</a>
	      </li>
	      			<?php
	      		}
	      	}
	      ?>
	      <li>
	      	<a href="?action=new">
	      		<span class="glyphicon glyphicon-plus"></span> Nouvel utilisateur
	      		<div class="clear"></div>
	      	</a>
	      </li>
		</ul>
	</div>
	<div class="col-md-10 pt15">
		<div class="col-md-12">
			<?php
				if($action == 'sumary') {
			?>
				<h4>Gestion de vos utilisateurs</h4>
				<p>Pour gérer un de vos utilisateurs, veuillez séléctionner celui-ci dans le menu situé sur la gauche de votre écran.</p>
			<?php
				} elseif($action == 'new') {
					?>
				<h4>Création d'un utilisateur</h4>
				<form method="post" action="">
					<div class="row">
						<div class="col-md-1 text-right">
							<firstname for="login">Login</firstname>
						</div>
						<div class="col-md-11 full-input">
							<input type="text" name="login" id="login" />
						</div>
					</div>
					<div class="row">
						<div class="col-md-1 text-right">
							<firstname for="email">Email</firstname>
						</div>
						<div class="col-md-11 full-input">
							<input type="email" name="email" id="email" value="<?php echo $object->email; ?>" />
						</div>
					</div>
					<div class="row">
						<div class="col-md-1 text-right">
							<firstname for="firstname">Prénom</firstname>
						</div>
						<div class="col-md-11 full-input">
							<input type="text" name="firstname" id="firstname" />
						</div>
					</div>
					<div class="row">
						<div class="col-md-1 text-right">
							<firstname for="lastname">Nom</firstname>
						</div>
						<div class="col-md-11 full-input">
							<input type="text" name="lastname" id="lastname" />
						</div>
					</div>
					<div class="row">
						<div class="col-md-1 text-right">
							<firstname for="pass_crypted">Password</firstname>
						</div>
						<div class="col-md-11 full-input">
							<input type="password" name="pass_crypted" id="pass_crypted" value="" />
						</div>
					</div>
					<div class="row pt15">
						<div class="col-md-12 text-right">
							<input type="hidden" name="action" value="create" />
							<input type="submit" name="envoyer" value="Valider" class="btn btn-primary"/>
							<input type="reset" name="reset" value="Annuler" class="btn btn-secondary"/>
						</div>
					</div>
				</form>
					<?php
				} elseif($action == 'edit') {
					?>
				<h4>Création d'un utilisateur</h4>
				<form method="post" action="">
					<div class="row">
						<div class="col-md-1 text-right">
							<firstname for="login">Login</firstname>
						</div>
						<div class="col-md-11 full-input">
							<input type="text" name="login" id="login" value="<?php echo $object->login; ?>" />
						</div>
					</div>
					<div class="row">
						<div class="col-md-1 text-right">
							<firstname for="email">Email</firstname>
						</div>
						<div class="col-md-11 full-input">
							<input type="email" name="email" id="email" value="<?php echo $object->email; ?>" />
						</div>
					</div>
					<div class="row">
						<div class="col-md-1 text-right">
							<firstname for="firstname">Prénom</firstname>
						</div>
						<div class="col-md-11 full-input">
							<input type="text" name="firstname" id="firstname" value="<?php echo $object->firstname; ?>" />
						</div>
					</div>
					<div class="row">
						<div class="col-md-1 text-right">
							<firstname for="lastname">Nom</firstname>
						</div>
						<div class="col-md-11 full-input">
							<input type="text" name="lastname" id="lastname" value="<?php echo $object->lastname; ?>" />
						</div>
					</div>
					<div class="row">
						<div class="col-md-1 text-right">
							<firstname for="pass_crypted">Password</firstname>
						</div>
						<div class="col-md-11 full-input">
							<input type="password" name="pass_crypted" id="pass_crypted" value="" />
						</div>
					</div>
					<div class="row pt15">
						<div class="col-md-12 text-right">
							<input type="hidden" name="action" value="update" />
							<input type="submit" name="envoyer" value="Valider" class="btn btn-primary"/>
							<input type="reset" name="reset" value="Annuler" class="btn btn-secondary"/>
							<?php if($id!=1): ?>
							<a class="btn btn-danger" href="?action=delete&id=<?php echo $id; ?>">Supprimer</a>
							<?php endif; ?>
						</div>
					</div>
				</form>
					<?php
				}
			?>
		</div>
	</div>
	<div class="clear"></div>
</div>