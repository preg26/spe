<div class="container-full mt50">
	<div class="navbar-left col-md-2 pt15" id="page-sidebar">
		<h4>Choix du compte</h4>
		<ul class="nav nav-pills nav-stacked">
	      <?php
	      	if(!empty($TComptes)) {
	      		foreach($TComptes as $c) {
	      			$c->signe();
	      			?>
			<li <?php if($id == $c->rowid) echo 'class="active"'; ?>>
				<a href="?action=view&id=<?php echo $c->rowid; ?>">
					<div class="col-md-12 nopad">
						<span class="glyphicon glyphicon-chevron-right ml010"></span> <?php echo $c->label; ?>
					</div>
					<div class="clear"></div>
				</a>
			</li>
	      			<?php
	      		}
	      	}
	      ?>
		</ul>
	</div>
	
	<div class="col-md-10 pt15" id="tresorerie">
	<?php
		if(!empty($id)) {
	?>
		<?php foreach($TFilterYears as $y): ?>
			<a class="col-md-1 text-center bloc-grey<?php if($year == $y) echo ' active'; ?>" href="?id=<?php echo $id; ?>&year=<?php echo $y; ?>"><?php echo $y ?></a>
		<?php endforeach; ?>
		<a class="col-md-1 pull-right bloc-grey text-center" href="#" onclick="javascript:window.print();" ><span class="glyphicon glyphicon-print"></span></a>
		<a class="col-md-1 pull-right bloc-grey text-center<?php if($ttc == false) echo ' active'; ?>" href="?id=<?php echo $id; ?>&year=<?php echo $year; ?>&ttc=0">HT</a>
		<a class="col-md-1 pull-right bloc-grey text-center<?php if($ttc == true) echo ' active'; ?>" href="?id=<?php echo $id; ?>&year=<?php echo $year; ?>&ttc=1">TTC</a>
		<div class="clear"></div>
	</div>
	<div class="clear"></div>
	<div class="col-md-12">
		<h2>
			<form>
			Compte de Résultat &nbsp;&nbsp;
			<input type="hidden" name="id" id="fk_bank" value="<?php echo $id; ?>" />
			<input type="hidden" name="action" value="search" />
			<input type="hidden" name="year" id="year" value="<?php echo $year; ?>" />
			<input type="hidden" name="ttc" id="ttc" value="<?php echo $ttc; ?>" />
			<input type="text" name="ref" id="ref" value="<?php echo $ref; ?>" placeholder="Tapez ici votre recherche" style="font-size:13px;padding:12px 4px 10px;min-width:250px;" />
		</form>
		</h2>
	</div>
	<div class="col-md-12" id="table-tresorerie">
		<div class="col-md-4 entete cell">
			&nbsp;
		</div>
		<div class="col-md-8 nopad">
			<div class="col-md-3 nopad">
				<div class="col-md-3 entete cumul cell">
					Cumul
				</div>
				<div class="col-md-3 entete percent cell">
					%
				</div>
				<div class="col-md-3 entete plafond cell">
					Plafond
				</div>
				<div class="col-md-3 entete reste cell">
					Reste
				</div>
			</div>
			<div class="col-md-9 nopad">
				<?php 
				 foreach($TMonthsShort as $month) {
					 ?>
				<div class="col-md-1 entete cell">
					<?php echo $month; ?>
				</div>
					<?php
				 }
				?>
			</div>
		</div>
		<div class="clear"></div>
		<?php
		
			$tresorerie->printCdr();
			
		} else {
			echo '<p>Veuillez choisir un compte</p>';
		}
	?>
	</div>
</div>