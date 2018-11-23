<?php
/**
 * ComptaCateg class
 */
class Tresorerie
{
	public $TData;
	public $TResult;
	public $TCompta;
	public $TComptaParents;
	public $TPayments;
	public $fk_bank;
	public $year;
	public $CAcompta;
	public $EXcompta;
	public $CHcompta1;
	public $CHcompta2;
	public $ttc;

	private $PDOdb;

	public function __construct($PDOdb, $fk_bank, $year) {
		$this->PDOdb = $PDOdb;
		$this->fk_bank = $fk_bank;
		$this->year = $year;
		$this->TResult = array();
		$this->CAcompta = TRESO_CA_CATEG_ID;
		$this->EXcompta = TRESO_EX_CATEG_ID;
		$this->CHcompta1 = TRESO_CH1_CATEG_ID;
		$this->CHcompta2 = TRESO_CH2_CATEG_ID;
	}

	public function fetch($TTC = false, $ref = null) {
		$this->ttc = $TTC;
		$object = new ComptaCateg($this->PDOdb);
		$this->TCompta = $object->fetchAll();
		$this->TComptaParents = $object->fetchParents();
		$payment = new Payment($this->PDOdb);
		$this->TPayments = $payment->fetchAllFor($this->fk_bank, $this->year, true, $ref);
		
		$this->generateCdr();
	}

	public function generateCdr(){
		$this->generateTData();
		foreach($this->TPayments as $year => $TYear) {
			foreach($TYear as $month => $TMonth) {
				foreach($TMonth as $day => $TPayments) {
					foreach($TPayments as $payment) {
						// Boucle sur chaque paiement
						$comptaCateg = new ComptaCateg($this->PDOdb);
						$comptaCateg->fetch($payment->fk_categcomptable);
						$parent = $comptaCateg->fk_parent;
						$price = $payment->getHt();
						$tva = $payment->getTva();
						$time = $payment->date_facture;
						if($this->ttc) {
							$price += $tva;
						}
						$this->TData['payments'][$payment->fk_categcomptable][$time]['object'] = $payment;
						$this->TData['payments'][$payment->fk_categcomptable][$time]['month'][$year][$month]['total'] = $price;
						
						$this->TData['category'][$payment->fk_categcomptable]['day'][$year][$month][$day]['total'] += $price;
						$this->TData['category'][$payment->fk_categcomptable]['month'][$year][$month]['total'] += $price;
						$this->TData['category'][$payment->fk_categcomptable]['year'][$year]['total'] += $price;
						if(!empty($parent)) {
							$this->TData['category'][$parent]['day'][$year][$month][$day]['total'] += $price;
							$this->TData['category'][$parent]['month'][$year][$month]['total'] += $price;
							$this->TData['category'][$parent]['year'][$year]['total'] += $price;
						}
						
					}
				}
			}
		}
	}

	public function generateTData(){
		foreach($this->TCompta as $compte) {
			for($i=1;$i<=12;$i++){
				$month = str_pad($i, 2, '0', STR_PAD_LEFT);
				for($j=1;$j<=31;$j++) {
					$day = str_pad($j, 2, '0', STR_PAD_LEFT);
					$this->TData['category'][$compte->rowid]['day'][$this->year][$month][$day]['total'] = 0;
				}
				$this->TData['category'][$compte->rowid]['month'][$this->year][$month]['total'] = 0;
			}
			$this->TData['category'][$compte->rowid]['year'][$this->year]['total'] = 0;
		}
	}
	
	public function printSummary() {
		$this->printCa();
		$this->printCh();
		$this->printMarge();
		
	}
	
	public function printCh() {
		// Compte 1
		$compte = $this->TCompta[$this->CHcompta1];
		$TInfo = new \stdClass();
		$TInfo->parent = false;
		$TInfo->code = $compte->code;
		$TInfo->id = $compte->rowid;
		$TInfo->label = $compte->label;
		$TInfo->cumul = $this->TData['category'][$this->CHcompta1]['year'][$this->year]['total'];
		$TInfo->plafond = $compte->plafond;
		$TInfo->reste = $TInfo->plafond + $TInfo->cumul;
		if(!empty($this->TData['category'][$this->CAcompta]['year'][$this->year]['total'])) {
			$tx = ($TInfo->cumul/$this->TData['category'][$this->CAcompta]['year'][$this->year]['total'])*100;
		}else{
			$tx = 100;
		}
		$TInfo->percent = abs(round($tx,2));
		$TAmountMonth = array();
		for($i=1;$i<=12;$i++) {
			$month = str_pad($i, 2, '0', STR_PAD_LEFT);
			$total = $this->TData['category'][$this->CHcompta1]['month'][$this->year][$month]['total'];
			if(!empty($total))
				$TAmountMonth[$i] = $total;
				else
					$TAmountMonth[$i] = 0;
		}
		$TInfo->TAmounts = $TAmountMonth;
		$this->printLigne($TInfo);
		if(!empty($this->TData['payments'][$compte->rowid])) {
			foreach($this->TData['payments'][$compte->rowid] as $TPayment) {
				$payment = $TPayment['object'];
				$TReglementInfo= new \stdClass();
				$TReglementInfo->parent = false;
				$TReglementInfo->label = $payment->label;
				$TReglementInfo->date = $payment->date_facture;
				$TReglementInfo->fk_codecompta = $compte->rowid;
				$TAmountMonth = array();
				for($i=1;$i<=12;$i++) {
					$month = str_pad($i, 2, '0', STR_PAD_LEFT);
					if(!empty($TPayment['month'][$this->year][$month]['total'])) {
						$total = $TPayment['month'][$this->year][$month]['total'];
						$TAmountMonth[$i] = $total;
					} else {
						$TAmountMonth[$i] = 0;
					}
				}
				$TReglementInfo->TAmounts = $TAmountMonth;
				$this->printLigneReglement($TReglementInfo);
			}
		}
		
		// Compte 2
		$compte = $this->TCompta[$this->CHcompta2];
		$TInfo = new \stdClass();
		$TInfo->parent = false;
		$TInfo->code = $compte->code;
		$TInfo->id = $compte->rowid;
		$TInfo->label = $compte->label;
		$TInfo->cumul = $this->TData['category'][$this->CHcompta2]['year'][$this->year]['total'];
		$TInfo->plafond = $compte->plafond;
		$TInfo->reste = $TInfo->plafond + $TInfo->cumul;
		if(!empty($this->TData['category'][$this->CAcompta]['year'][$this->year]['total'])) {
			$tx = ($TInfo->cumul/$this->TData['category'][$this->CAcompta]['year'][$this->year]['total'])*100;
		}else{
			$tx = 100;
		}
		$TInfo->percent = abs(round($tx,2));
		$TAmountMonth = array();
		for($i=1;$i<=12;$i++) {
			$month = str_pad($i, 2, '0', STR_PAD_LEFT);
			$total = $this->TData['category'][$this->CHcompta2]['month'][$this->year][$month]['total'];
			if(!empty($total))
				$TAmountMonth[$i] = $total;
				else
					$TAmountMonth[$i] = 0;
		}
		$TInfo->TAmounts = $TAmountMonth;
		$this->printLigne($TInfo);
		if(!empty($this->TData['payments'][$compte->rowid])) {
			foreach($this->TData['payments'][$compte->rowid] as $TPayment) {
				$payment = $TPayment['object'];
				$TReglementInfo= new \stdClass();
				$TReglementInfo->parent = false;
				$TReglementInfo->label = $payment->label;
				$TReglementInfo->date = $payment->date_facture;
				$TReglementInfo->fk_codecompta = $compte->rowid;
				$TAmountMonth = array();
				for($i=1;$i<=12;$i++) {
					$month = str_pad($i, 2, '0', STR_PAD_LEFT);
					if(!empty($TPayment['month'][$this->year][$month]['total'])) {
						$total = $TPayment['month'][$this->year][$month]['total'];
						$TAmountMonth[$i] = $total;
					} else {
						$TAmountMonth[$i] = 0;
					}
				}
				$TReglementInfo->TAmounts = $TAmountMonth;
				$this->printLigneReglement($TReglementInfo);
			}
		}
	}
	
	public function printMarge() {
		$TInfo = new \stdClass();
		$TInfo->parent = false;
		$TInfo->label = 'Marge Commercial Brut';
		$totalCa = $this->TData['category'][$this->CAcompta]['year'][$this->year]['total'];
		$totalCh = $this->TData['category'][$this->CHcompta1]['year'][$this->year]['total'] + $this->TData['category'][$this->CHcompta2]['year'][$this->year]['total'];
		$TInfo->cumul = $totalCa + $totalCh;
		$TInfo->plafond = null;
		$TInfo->reste = null;
		if(!empty($this->TData['category'][$this->CAcompta]['year'][$this->year]['total'])) {
			$tx = ($TInfo->cumul/$this->TData['category'][$this->CAcompta]['year'][$this->year]['total'])*100;
		}else{
			$tx = 100;
		}
		$TInfo->percent = round($tx,2);
		if($TInfo->cumul >= 0) $TInfo->special = 'backpositif';
		else $TInfo->special = 'backnegatif';
		$TAmountMonth = array();
		for($i=1;$i<=12;$i++) {
			$month = str_pad($i, 2, '0', STR_PAD_LEFT);
			$totalCa = $this->TData['category'][$this->CAcompta]['month'][$this->year][$month]['total'];
			$totalCh = $this->TData['category'][$this->CHcompta1]['month'][$this->year][$month]['total'] + $this->TData['category'][$this->CHcompta2]['month'][$this->year][$month]['total'];
			$total = $totalCa + $totalCh;
			if(!empty($total))
				$TAmountMonth[$i] = $total;
			else
				$TAmountMonth[$i] = 0;
		}
		$TInfo->TAmounts = $TAmountMonth;
		$this->printLigne($TInfo);
	}
	
	public function printCa() {
		$compte = $this->TCompta[$this->CAcompta];
		$TInfo = new \stdClass();
		$TInfo->parent = false;
		$TInfo->code = $compte->code;
		$TInfo->id = $compte->rowid;
		$TInfo->label = $compte->label;
		$TInfo->percent = 100;
		$TInfo->cumul = $this->TData['category'][$this->CAcompta]['year'][$this->year]['total'];
		$TInfo->plafond = $compte->plafond;
		$TInfo->reste = $TInfo->plafond - $TInfo->cumul;
		$TAmountMonth = array();
		for($i=1;$i<=12;$i++) {
			$month = str_pad($i, 2, '0', STR_PAD_LEFT);
			if(!empty($this->TData['category'][$this->CAcompta]['month'][$this->year][$month]['total'])) {
				$total = $this->TData['category'][$this->CAcompta]['month'][$this->year][$month]['total'];
				$TAmountMonth[$i] = $total;
			} else {
				$TAmountMonth[$i] = 0;
			}
		}
		$TInfo->TAmounts = $TAmountMonth;
		$this->printLigne($TInfo);
		if(!empty($this->TData['payments'][$compte->rowid])) {
			foreach($this->TData['payments'][$compte->rowid] as $TPayment) {
				$payment = $TPayment['object'];
				$TReglementInfo= new \stdClass();
				$TReglementInfo->parent = false;
				$TReglementInfo->label = $payment->label;
				$TReglementInfo->date = $payment->date_facture;
				$TReglementInfo->fk_codecompta = $compte->rowid;
				$TAmountMonth = array();
				for($i=1;$i<=12;$i++) {
					$month = str_pad($i, 2, '0', STR_PAD_LEFT);
					if(!empty($TPayment['month'][$this->year][$month]['total'])) {
						$total = $TPayment['month'][$this->year][$month]['total'];
						$TAmountMonth[$i] = $total;
					} else {
						$TAmountMonth[$i] = 0;
					}
				}
				$TReglementInfo->TAmounts = $TAmountMonth;
				$this->printLigneReglement($TReglementInfo);
			}
		}
	}
	
	public function printExceptionnel() {
		$compte = $this->TComptaParents[$this->EXcompta];
		$TInfo = new \stdClass();
		$TInfo->parent = true;
		$TInfo->code = null;
		$TInfo->id = $compte->rowid;
		$TInfo->label = '<span class="glyphicon glyphicon-star-empty"></span>&nbsp;Comptes Exceptionnels';
		$TInfo->cumul = $this->TData['category'][$this->EXcompta]['year'][$this->year]['total'];
		$TInfo->plafond = $compte->plafond;
		$TInfo->reste = $TInfo->plafond + $TInfo->cumul;
		$TInfo->special = 'backExceptionnel';
		if(!empty($this->TData['category'][$this->CAcompta]['year'][$this->year]['total'])) {
			$tx = ($TInfo->cumul/$this->TData['category'][$this->CAcompta]['year'][$this->year]['total'])*100;
		}else{
			$tx = 100;
		}
		$TInfo->percent = abs(round($tx,2));
		$TAmountMonth = array();
		for($i=1;$i<=12;$i++) {
			$month = str_pad($i, 2, '0', STR_PAD_LEFT);
			if(!empty($this->TData['category'][$this->EXcompta]['month'][$this->year][$month]['total'])) {
				$total = $this->TData['category'][$this->EXcompta]['month'][$this->year][$month]['total'];
				$TAmountMonth[$i] = $total;
			} else {
				$TAmountMonth[$i] = 0;
			}
		}
		$TInfo->TAmounts = $TAmountMonth;
		$this->printLigne($TInfo);
		
		if(!empty($compte->TChilds)) {
			foreach($compte->TChilds as $compte) {
				$TInfo = new \stdClass();
				$TInfo->parent = false;
				$TInfo->code = $compte->code;
				$TInfo->id = $compte->rowid;
				$TInfo->label = $compte->label;
				$TInfo->cumul = $this->TData['category'][$compte->rowid]['year'][$this->year]['total'];
				$TInfo->plafond = $compte->plafond;
				$TInfo->reste = $TInfo->plafond + $TInfo->cumul;
				if(!empty($this->TData['category'][$this->CAcompta]['year'][$this->year]['total'])) {
					$tx = ($TInfo->cumul/$this->TData['category'][$this->CAcompta]['year'][$this->year]['total'])*100;
				}else{
					$tx = 100;
				}
				$TInfo->percent = abs(round($tx,2));
				$TAmountMonth = array();
				for($i=1;$i<=12;$i++) {
					$month = str_pad($i, 2, '0', STR_PAD_LEFT);
					if(!empty($this->TData['category'][$compte->rowid]['month'][$this->year][$month]['total'])) {
						$total = $this->TData['category'][$compte->rowid]['month'][$this->year][$month]['total'];
						$TAmountMonth[$i] = $total;
					} else {
						$TAmountMonth[$i] = 0;
					}
				}
				$TInfo->TAmounts = $TAmountMonth;
				$this->printLigne($TInfo);
				if(!empty($this->TData['payments'][$compte->rowid])) {
					foreach($this->TData['payments'][$compte->rowid] as $TPayment) {
						$payment = $TPayment['object'];
						$TReglementInfo= new \stdClass();
						$TReglementInfo->parent = false;
						$TReglementInfo->label = $payment->label;
						$TReglementInfo->date = $payment->date_facture;
						$TReglementInfo->fk_codecompta = $compte->rowid;
						$TAmountMonth = array();
						for($i=1;$i<=12;$i++) {
							$month = str_pad($i, 2, '0', STR_PAD_LEFT);
							if(!empty($TPayment['month'][$this->year][$month]['total'])) {
								$total = $TPayment['month'][$this->year][$month]['total'];
								$TAmountMonth[$i] = $total;
							} else {
								$TAmountMonth[$i] = 0;
							}
						}
						$TReglementInfo->TAmounts = $TAmountMonth;
						$this->printLigneReglement($TReglementInfo);
					}
				}
			}
		}
	}
	
	public function printResultat() {
		$this->printLigne();
		$total = 0;
		$totalCa = $this->TData['category'][$this->CAcompta]['year'][$this->year]['total'];
		foreach($this->TComptaParents as $compte) {
			if($compte->rowid != $this->CAcompta) {
				$total += $this->TData['category'][$compte->rowid]['year'][$this->year]['total'];
			}
		}
		$resultat = $totalCa + $total;
		if(!empty($totalCa)) {
			$tx = ($total/$totalCa)*100;
		}else{
			$tx = 100;
		}
		$percent = abs(round($tx,2));
		if(!empty($totalCa)) {
			$tx2 = ($resultat/$totalCa)*100;
		}else{
			$tx2 = 100;
		}
		$percent2 = round($tx2,2);
		?>
		<div class="col-md-12 backExceptionnel">
			<div class="col-md-4 compte cell">
				Total des charges de l'entreprise
			</div>
			<div class="col-md-8 nopad months">
				<div class="col-md-3 nopad">
					<div class="col-md-3 cumul cell">
					<?php echo view_price(abs($total)); ?>
					</div>
					<div class="col-md-3 percent cell">
					<?php echo $percent.'%'; ?>
					</div>
					<div class="col-md-3 plafond cell">
					&nbsp;
					</div>
					<div class="col-md-3 reste cell">
					&nbsp;
					</div>
				</div>
				<div class="col-md-9 nopad">
					<?php
					for($i=1;$i<=12;$i++) {
						?>
					<div class="col-md-1 cell">
						&nbsp;
					</div>
						<?php
					 }
					?>
				</div>
			</div>
		</div>
		<div class="col-md-12 parentCateg">
			<div class="col-md-4 compte cell">
				Total des produits de vente
			</div>
			<div class="col-md-8 nopad months">
				<div class="col-md-3 nopad">
					<div class="col-md-3 cumul cell">
					<?php echo view_price($totalCa); ?>
					</div>
					<div class="col-md-3 percent cell">
					&nbsp;
					</div>
					<div class="col-md-3 plafond cell">
					&nbsp;
					</div>
					<div class="col-md-3 reste cell">
					&nbsp;
					</div>
				</div>
				<div class="col-md-9 nopad">
					<?php
					for($i=1;$i<=12;$i++) {
						?>
					<div class="col-md-1 cell">
						&nbsp;
					</div>
						<?php
					 }
					?>
				</div>
			</div>
		</div>
		<div class="col-md-12 parentCateg">
			<div class="col-md-4 compte cell">
				Résultat estimatif
			</div>
			<div class="col-md-8 nopad months">
				<div class="col-md-3 nopad">
					<div class="col-md-3 cumul cell">
					<?php echo view_price($resultat); ?>
					</div>
					<div class="col-md-3 percent cell">
					<?php echo $percent2.'%'; ?>
					</div>
					<div class="col-md-3 plafond cell">
					&nbsp;
					</div>
					<div class="col-md-3 reste cell">
					&nbsp;
					</div>
				</div>
				<div class="col-md-9 nopad">
					<?php
					for($i=1;$i<=12;$i++) {
						?>
					<div class="col-md-1 cell">
						&nbsp;
					</div>
						<?php
					 }
					?>
				</div>
			</div>
		</div>
		<?php
	}
	
	public function printCdr() {
		global $TMonthsShort;
		if(!empty($this->TCompta)) {
			
			$this->printSummary();
			$TNotAllowed = array(TRESO_CA_CATEG_ID, TRESO_EX_CATEG_ID, TRESO_CH1_CATEG_ID, TRESO_CH2_CATEG_ID);
			foreach($this->TComptaParents as $compte) {
				if(!in_array($compte->rowid,$TNotAllowed)) {
					$TInfo = new \stdClass();
					$TInfo->parent = true;
					$TInfo->code = $compte->code;
					$TInfo->label = $compte->label;
					$TInfo->id = $compte->rowid;
					$TInfo->cumul = $this->TData['category'][$compte->rowid]['year'][$this->year]['total'];
					$TInfo->plafond = $compte->plafond;
					$TInfo->reste = $TInfo->plafond + $TInfo->cumul;
					if(!empty($this->TData['category'][$this->CAcompta]['year'][$this->year]['total'])) {
						$tx = ($TInfo->cumul/$this->TData['category'][$this->CAcompta]['year'][$this->year]['total'])*100;
					}else{
						$tx = 100;
					}
					$TInfo->percent = abs(round($tx,2));
					$TAmountMonth = array();
					for($i=1;$i<=12;$i++) {
						$month = str_pad($i, 2, '0', STR_PAD_LEFT);
						if(!empty($this->TData['category'][$compte->rowid]['month'][$this->year][$month]['total'])) {
							$total = $this->TData['category'][$compte->rowid]['month'][$this->year][$month]['total'];
							$TAmountMonth[$i] = $total;
						} else {
							$TAmountMonth[$i] = 0;
						}
					}
					$TInfo->TAmounts = $TAmountMonth;
					$this->printLigne($TInfo);
					
					foreach($compte->TChilds as $compte) {
						$TInfo = new \stdClass();
						$TInfo->parent = false;
						$TInfo->code = $compte->code;
						$TInfo->id = $compte->rowid;
						$TInfo->label = $compte->label;
						$TInfo->cumul = $this->TData['category'][$compte->rowid]['year'][$this->year]['total'];
						$TInfo->plafond = $compte->plafond;
						$TInfo->reste = $TInfo->plafond - $TInfo->cumul;
						if(!empty($this->TData['category'][$this->CAcompta]['year'][$this->year]['total'])) {
							$tx = ($TInfo->cumul/$this->TData['category'][$this->CAcompta]['year'][$this->year]['total'])*100;
						}else{
							$tx = 100;
						}
						$TInfo->percent = abs(round($tx,2));
						$TAmountMonth = array();
						for($i=1;$i<=12;$i++) {
							$month = str_pad($i, 2, '0', STR_PAD_LEFT);
							if(!empty($this->TData['category'][$compte->rowid]['month'][$this->year][$month]['total'])) {
								$total = $this->TData['category'][$compte->rowid]['month'][$this->year][$month]['total'];
								$TAmountMonth[$i] = $total;
							} else {
								$TAmountMonth[$i] = 0;
							}
						}
						$TInfo->TAmounts = $TAmountMonth;
						$this->printLigne($TInfo);
						if(!empty($this->TData['payments'][$compte->rowid])) {
							foreach($this->TData['payments'][$compte->rowid] as $TPayment) {
								$payment = $TPayment['object'];
								$TReglementInfo= new \stdClass();
								$TReglementInfo->parent = false;
								$TReglementInfo->label = $payment->label;
								$TReglementInfo->date = $payment->date_facture;
								$TReglementInfo->fk_codecompta = $compte->rowid;
								$TAmountMonth = array();
								for($i=1;$i<=12;$i++) {
									$month = str_pad($i, 2, '0', STR_PAD_LEFT);
									if(!empty($TPayment['month'][$this->year][$month]['total'])) {
										$total = $TPayment['month'][$this->year][$month]['total'];
										$TAmountMonth[$i] = $total;
									} else {
										$TAmountMonth[$i] = 0;
									}
								}
								$TReglementInfo->TAmounts = $TAmountMonth;
								$this->printLigneReglement($TReglementInfo);
							}
						}
					}
				}
			}
			
			$this->printExceptionnel();
			$this->printResultat();
		}
	}
	
	public function printLigne($TInfo = null) {
		if(!empty($TInfo)) {
			?>
		<?php if($TInfo->parent == 0) { ?>
		
		<div class="col-md-12 sous-compte <?php if(!empty($TInfo->special)) echo $TInfo->special; ?>" reel="<?php if(!empty($TInfo->id)) echo $TInfo->id; ?>">
		
		<?php 
		} else { 
				$this->printLigne();
			?>
		<div class="col-md-12 sous-compte parentCateg <?php if(!empty($TInfo->special)) echo $TInfo->special; ?>">
		
		<?php } ?>
		
			<div class="col-md-4 compte cell">
				<?php if($TInfo->parent == 0): ?>
					<?php 
					if(!empty($TInfo->code)) {
						echo $TInfo->code.' &nbsp;&nbsp; '.$TInfo->label;
					} elseif(!empty($TInfo->label)) {
						echo '&nbsp;&nbsp; '.$TInfo->label;
					} else {
						echo '&nbsp;';
					}
					?>
						
				<?php else: ?>
				
					<?php 
					if(!empty($TInfo->label)) {
						echo '&nbsp;&nbsp; '.$TInfo->label;
					} else {
						echo '&nbsp;';
					}
					?>
					
				<?php endif; ?>
			</div>
			<div class="col-md-8 nopad months">
				<div class="col-md-3 nopad">
					<div class="col-md-3 cumul cell">
						<?php echo view_price($TInfo->cumul, false); ?>
					</div>
					<div class="col-md-3 percent cell">
						<?php echo $TInfo->percent.'%'; ?>
					</div>
					<div class="col-md-3 plafond cell">
						<?php if(!empty($TInfo->plafond)) echo view_price($TInfo->plafond, false); else echo '&nbsp;'; ?>
					</div>
					<div class="col-md-3 reste cell<?php if($TInfo->reste < 0) echo ' fontred'; ?>">
						<?php if(!empty($TInfo->reste)) echo view_price($TInfo->reste, false); else echo '&nbsp;'; ?>
					</div>
				</div>
				<div class="col-md-9 nopad">
					<?php 
					 for($i=1;$i<=12;$i++) {
						 ?>
					<div class="col-md-1 cell">
						<?php if(!empty($TInfo->TAmounts[$i])) echo view_price($TInfo->TAmounts[$i], false); else echo '&nbsp;'; ?>
					</div>
						<?php
					 }
					?>
				</div>
			</div>
		</div>
		<?php
		} else {
		?>
		<div class="col-md-12 empty">
			<div class="col-md-4 compte cell">
				&nbsp;
			</div>
			<div class="col-md-8 nopad months">
				<div class="col-md-3 nopad">
					<div class="col-md-3 cumul cell">
				&nbsp;
					</div>
					<div class="col-md-3 percent cell">
				&nbsp;
					</div>
					<div class="col-md-3 plafond cell">
				&nbsp;
					</div>
					<div class="col-md-3 reste cell">
				&nbsp;
					</div>
				</div>
				<div class="col-md-9 nopad">
					<?php 
					 for($i=1;$i<=12;$i++) {
						 ?>
					<div class="col-md-1 cell">
						&nbsp;
					</div>
						<?php
					 }
					?>
				</div>
			</div>
		</div>
			<?php
		}
	}
	
	public function printLigneReglement($TInfo = null) {
		if(!empty($TInfo)) {
			?>
		<div class="col-md-12 sous-compte payment compte<?php echo $TInfo->fk_codecompta; ?> backGreylight">
		
			<div class="col-md-4 compte cell">
				<?php echo '&nbsp;&nbsp;&nbsp;'.$TInfo->date.' '.$TInfo->label; ?>
			</div>
			<div class="col-md-8 nopad months">
				<div class="col-md-3 nopad">
					<div class="col-md-3 cumul cell">
						&nbsp;
					</div>
					<div class="col-md-3 percent cell">
						&nbsp;
					</div>
					<div class="col-md-3 plafond cell">
						&nbsp;
					</div>
					<div class="col-md-3 reste cell">
						&nbsp;
					</div>
				</div>
				<div class="col-md-9 nopad">
					<?php 
					 for($i=1;$i<=12;$i++) {
						 ?>
					<div class="col-md-1 cell">
						<?php if(!empty($TInfo->TAmounts[$i])) echo view_price($TInfo->TAmounts[$i]); else echo '&nbsp;'; ?>
					</div>
						<?php
					 }
					?>
				</div>
			</div>
		</div>
		<?php
		} else {
		?>
		<div class="col-md-12 empty">
			<div class="col-md-4 compte cell">
				&nbsp;
			</div>
			<div class="col-md-8 nopad months">
				<div class="col-md-3 nopad">
					<div class="col-md-3 cumul cell">
				&nbsp;
					</div>
					<div class="col-md-3 percent cell">
				&nbsp;
					</div>
					<div class="col-md-3 plafond cell">
				&nbsp;
					</div>
					<div class="col-md-3 reste cell">
				&nbsp;
					</div>
				</div>
				<div class="col-md-9 nopad">
					<?php 
					 for($i=1;$i<=12;$i++) {
						 ?>
					<div class="col-md-1 cell">
						&nbsp;
					</div>
						<?php
					 }
					?>
				</div>
			</div>
		</div>
			<?php
		}
	}
}