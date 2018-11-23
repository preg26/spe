
<div class="container-full mt50">
	<div class="navbar-left col-md-2 pt15" id="page-sidebar">
		<h4>Mes comptes</h4>
		<ul class="nav nav-pills nav-stacked">
		  <li>
			<a href="?action=new&is_parent=1">
				<span class="glyphicon glyphicon-plus"></span> Nouveau compte mère
				<div class="clear"></div>
			</a>
		  </li>
		  <li>
			<a href="?action=new">
				<span class="glyphicon glyphicon-plus"></span> Nouveau compte fille
				<div class="clear"></div>
			</a>
		  </li>
		  <?php
			if(!empty($TComptes)) {
				foreach($TComptes as $compte) {
					?>
		  <li>
			<a href="?action=edit&id=<?php echo $compte->rowid; ?>">
				<div class="col-md-12 nopad">
					<span class="glyphicon glyphicon-th"></span> <?php echo $compte->show(); ?>
				</div>
				<div class="clear"></div>
			</a>
			<ul class="nav nav-pills nav-stacked" style="margin-left:20px;">
			<?php
				if(!empty($compte->TChilds)) {
					foreach($compte->TChilds as $child) {
			?>
				<li>
					<a href="?action=edit&id=<?php echo $child->rowid; ?>">
						<div class="col-md-12 nopad">
							<span class="glyphicon glyphicon-th"></span> <?php echo $child->show(); ?>
						</div>
						<div class="clear"></div>
					</a>
				</li>
			<?php
				}
			}
			?>
			</ul>
		  </li>
					<?php
				}
			}
		  ?>
		</ul>
	</div>
	<div class="col-md-10 pt15">
		<div class="col-md-12">
			<?php
				if($action == 'sumary') {
			?>
				<h4>Gestion de vos catégories comptables</h4>
				<p>Pour gérer une de vos catégories, veuillez séléctionner celle-ci dans le menu situé sur la gauche de votre écran.</p>
			<?php
				} elseif($action == 'new') {
					?>
				<h4>Création d'un compte</h4>
				<form method="post" action="">
					<div class="row">
						<div class="col-md-2 text-right">
							<label for="ref">Code comptable</label>
						</div>
						<div class="col-md-10 full-input">
							<input type="text" name="code" id="code" />
						</div>
					</div>
					<div class="row">
						<div class="col-md-2 text-right">
							<label for="label">Label</label>
						</div>
						<div class="col-md-10 full-input">
							<input type="text" name="label" id="label" />
						</div>
					</div>
					<div class="row">
						<div class="col-md-2 text-right">
							<label for="plafond">Plafond</label>
						</div>
						<div class="col-md-10 full-input">
							<input type="text" name="plafond" id="plafond" />
						</div>
					</div>
					<div class="row">
						<?php
						if(!$is_parent) { 
						?>
						<div class="col-md-2 text-right">
							<label for="parent">Parent</label>
						</div>
						<div class="col-md-10 full-input">
							<select id="fk_parent" name="fk_parent">
								<?php
									foreach($TComptes as $categ) {
										echo '<option value="'.$categ->rowid.'">'.$categ->show().'</option>';
									}
								?>
							</select>
						</div>
						<?php
						} else {
							echo '<input type="hidden" name="fk_parent" value="" />';
						}
						?>
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
						<div class="col-md-2 text-right">
							<label for="code">Code comptable</label>
						</div>
						<div class="col-md-10 full-input">
							<input type="text" name="code" id="code" value="<?php echo $object->code; ?>" />
						</div>
					</div>
					<div class="row">
						<div class="col-md-2 text-right">
							<label for="label">Label</label>
						</div>
						<div class="col-md-10 full-input">
							<input type="text" name="label" id="label" value="<?php echo $object->label; ?>" />
						</div>
					</div>
					<div class="row">
						<div class="col-md-2 text-right">
							<label for="plafond">Plafond</label>
						</div>
						<div class="col-md-10 full-input">
							<input type="text" name="plafond" id="plafond" value="<?php echo $object->plafond; ?>" />
						</div>
					</div>
					<div class="row">
						<div class="col-md-2 text-right">
							<label for="parent">Parent</label>
						</div>
						<div class="col-md-10 full-input">
							<select id="fk_parent" name="fk_parent">
								<option value="" <?php if($object->fk_parent == 0) echo 'selected="selected"'; ?>>&nbsp;</option>
								<?php
									foreach($TComptes as $categ) {
										echo '<option value="'.$categ->rowid.'" ';
										if($categ->rowid == $object->fk_parent) echo 'selected="selected"';
										echo '>'.$categ->show().'</option>';
									}
								?>
							</select>
						</div>
					</div>
					<div class="row pt15">
						<div class="col-md-12 text-right">
							<input type="hidden" name="action" value="update" />
							<input type="submit" name="envoyer" value="Valider" class="btn btn-primary"/>
							<input type="reset" name="reset" value="Annuler" class="btn btn-secondary"/>
							<?php if($deleteRight): ?>
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