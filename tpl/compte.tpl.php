
<div class="container-full mt50">
	<div class="navbar-left col-md-2 pt15" id="page-sidebar">
		<h4>Mes comptes</h4>
		<ul class="nav nav-pills nav-stacked">
	      <?php
	      	if(!empty($TComptes)) {
	      		foreach($TComptes as $compte) {
	      			$compte->signe();
	      			?>
	      <li>
	      	<a href="?action=edit&id=<?php echo $compte->rowid; ?>">
	      		<div class="col-md-8 nopad">
	      			<span class="glyphicon glyphicon-th"></span> <?php echo $compte->label; ?>
	      		</div>
				<div class="col-md-4 nopad text-right">
	      			<span class="badge <?php echo $compte->trans_signe; ?>"><?php echo $compte->amount; ?>€</span>
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
	      		<span class="glyphicon glyphicon-plus"></span> Nouveau compte
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
				<h4>Gestion de vos comptes bancaires</h4>
				<p>Pour gérer un de vos comptes en banque, veuillez séléctionner celui-ci dans le menu situé sur la gauche de votre écran.</p>
			<?php
				} elseif($action == 'new') {
					?>
				<h4>Création d'un compte</h4>
				<form method="post" action="">
					<div class="row">
						<div class="col-md-1 text-right">
							<label for="ref">Référence</label>
						</div>
						<div class="col-md-11 full-input">
							<input type="text" name="ref" id="ref" />
						</div>
					</div>
					<div class="row">
						<div class="col-md-1 text-right">
							<label for="label">Label</label>
						</div>
						<div class="col-md-11 full-input">
							<input type="text" name="label" id="label" />
						</div>
					</div>
					<div class="row">
						<div class="col-md-1 text-right">
							<label for="comment">Commentaire</label>
						</div>
						<div class="col-md-11 full-input">
							<textarea type="text" name="comment" id="comment" rows="8"></textarea>
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
				<h4>Création d'un compte</h4>
				<form method="post" action="">
					<div class="row">
						<div class="col-md-1 text-right">
							<label for="ref">Référence</label>
						</div>
						<div class="col-md-11 full-input">
							<input type="text" name="ref" id="ref" value="<?php echo $object->ref; ?>" />
						</div>
					</div>
					<div class="row">
						<div class="col-md-1 text-right">
							<label for="label">Label</label>
						</div>
						<div class="col-md-11 full-input">
							<input type="text" name="label" id="label" value="<?php echo $object->label; ?>" />
						</div>
					</div>
					<div class="row">
						<div class="col-md-1 text-right">
							<label for="comment">Commentaire</label>
						</div>
						<div class="col-md-11 full-input">
							<textarea type="text" name="comment" id="comment" rows="8"> <?php echo $object->comment; ?></textarea>
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