<?php

/**
 * Account class
 */
class Account extends CommonObject
{
	public $element='account';
	public $table_element='account';
	public $TChamps = array(
				'rowid'=>'number'
				,'label'=>'string'
				,'ref'=>'string'
				,'amount'=>'float'
				,'comment'=>'text'
			);
	
	var $label;
	var $ref;
	var $bank_name;
	var $code_banque;
	var $code_guichet;
	var $number;
	var $cle_rib;
	var $bic;
	var $iban_prefix;
	var $country_iban;
	var $cle_iban;
	var $status;
	var $url;
	var $account_number;
	var $min_allowed;
	var $min_desired;
	var $amount;
	var $amount_before;
	var $comment;
	var $signe;
	var $trans_signe;
	
	public function __construct($PDOdb) {
		$this->status = 0;
		parent::__construct($PDOdb);
	}
	
	public function signe() {
		if($this->amount == 0) {
			$this->signe = 0;
			$this->trans_signe = 'null';
			return 0;
		} elseif($this->amount > 0) {
			$this->signe = 1;
			$this->trans_signe = 'positif';
			return 1;
		} else {
			$this->signe = 2;
			$this->trans_signe = 'negatif';
			return 2;
		}
	}
	
	public function fetch($id, $year = null) {
		$res = parent::fetch($id);
		$this->fetch_last_total($year);
		$this->fetch_total();
		$this->fetch_current_total();
		return $res;
	}
	
	public function fetch_current_total() {
		$total = 0;
		$date = time();
		$sql = 'SELECT SUM(amount) as total';
		$sql.=' FROM '.MAIN_DB_PREFIX.'payment';
		$sql.=' WHERE fk_bank = '.$this->{$this->primary_key};
		$sql.=' AND datep <= "'.(date('Y',$date)).'-'.(date('m',$date)).'-'.(date('d',$date)).'"';
		$sql.=' AND datep > "1970-01-01"';
		$req = $this->PDOdb->query($sql);
		if($res = $req->fetch(PDO::FETCH_OBJ)){
			$total = $res->total;
		}
		$this->amount_day = (float) $total;
	}
	
	public function fetch_total() {
		$total = 0;
		$sql = 'SELECT SUM(amount) as total';
		$sql.=' FROM '.MAIN_DB_PREFIX.'payment';
		$sql.=' WHERE fk_bank = '.$this->{$this->primary_key};
		$sql.=' AND datep > "1970-01-01"';
		$req = $this->PDOdb->query($sql);
		if($res = $req->fetch(PDO::FETCH_OBJ)){
			$total = $res->total;
		}
		$this->amount = (float) $total;
	}
	
	public function fetch_last_total($year = null) {
		if(!empty($year)) {
			$total = 0;
			$sql = 'SELECT SUM(amount) as total';
			$sql.=' FROM '.MAIN_DB_PREFIX.'payment';
			$sql.=' WHERE fk_bank = '.$this->{$this->primary_key};
			$sql.=' AND datep < "'.($year).'-01-01"';
			$sql.=' AND datep > "1970-01-01"';
			$req = $this->PDOdb->query($sql);
			if($res = $req->fetch(PDO::FETCH_OBJ)){
				$total = $res->total;
			}
			$this->amount_before = (float) $total;
		}
	}
	
	public static function calcul_totaux($compte, $TData) {
		$res = array();
		if(!empty($TData)) {
			foreach($TData as $year => $TMonths) {
				$res[$year]['current'] = 0;
				
				if(!empty($TMonths)) {
					foreach($TMonths as $month => $TDays) {
						$res[$year][$month]['current'] = 0;
								
						if(!empty($TDays)) {
							foreach($TDays as $day => $TPayments) {
								$res[$year][$month][$day]['current'] = 0;
								
								if(!empty($TPayments)) {
									foreach($TPayments as $payment) {
										// Test si payé
										if($payment->rowid > 0) {
											$res[$year][$month][$day]['current'] += $payment->amount;
											$res[$year][$month]['current'] += $payment->amount;
											$res[$year]['current'] += $payment->amount;
										}
									}
								}
							}
						}
					}
				}
			}
		}
		return $res;
	}
}