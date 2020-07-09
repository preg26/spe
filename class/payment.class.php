<?php

/**
 * Payment class
 */
class Payment extends CommonObject
{
	public $element='payment';
	public $table_element='payment';
	public $TChamps = array(
	    'rowid'=>'number'
	    ,'label'=>'string'
	    ,'client'=>'string'
        ,'color'=>'string'
		,'datep'=>'date'
		,'date_facture'=>'date'
		,'mode'=>'number'
		,'tva'=>'number'
		,'provision'=>'number'
		,'amount'=>'float'
		,'fk_bank'=>'number'
		,'fk_categcomptable'=>'number'
		,'status'=>'number'
	);
	public $TTypeTrad = array(
		'0'=>'Prélèvement'
		,'1'=>'Virement Sepa'
		,'2'=>'Espèce'
		,'3'=>'Chèque'
	);
	public $TType = array(
		'0'=>'Prlv'
		,'1'=>'Vir'
		,'2'=>'Esp'
		,'3'=>'Chq'
	);
	
	var $label;
	var $client;
	var $datep;
	var $date_facture;
	var $amount;
	var $note;
	var $mode;
	var $tva;
	var $tva_tx;
	var $fk_bank;
	var $fk_categcomptable;
	var $categcomptable;
	var $status;
	var $provision;
	var $r;
	var $g;
	var $b;

	public function fetchForBank($fk_bank=null,$waiting=false,$status=null,$sort=null){
		$ret = array();
		$sql = "SELECT rowid";
		$sql .= " FROM " . MAIN_DB_PREFIX . $this->table_element;
		$sql .= " WHERE 1=1";
		
		// All payments for all banks case
		if(!empty($fk_bank)) {
		     $sql .= ' AND fk_bank = ' . $fk_bank;
		}
		if (!empty($status)) {
			$sql .= ' AND status = '.$status;
		}
		if($waiting) {
			$sql .= ' AND datep = \'1970-01-01\'';
		} else {
			$sql .= ' AND datep > \'1970-01-01 \'';
		}
		if(!empty($sort)) {
		    $order = 'ASC';
		    if ($sort == 'color') $order = 'DESC';
		    $sql .= ' ORDER BY '.$sort.' '.$order;
		}

		$req = $this->PDOdb->query($sql);
		while ($res = $req->fetch(PDO::FETCH_OBJ)) {
			$payment = new self($this->PDOdb);
			$payment->fetch($res->rowid);
			if (!$waiting) {
				$time = strtotime($payment->datep);
				$dateD = date('d',$time);
				$dateM = date('m',$time);
				$dateY = date('Y',$time);
				$ret[$dateY][$dateM][$dateD][] = $payment;
			}else{
				$ret[] = $payment;
			}
		}
		return $ret;
	}
	
	public function getHt() {
		return $this->amount * (1-$this->tva_tx);
	}
	
	public function getTva() {
		return $this->amount - $this->getHt();
	}

	public function move($date = null, $bank = null) {
		if(!empty($date)) {
			// Scheduled case
			$realdate = strtotime($date);
			$this->datep = date('Y-m-d',$realdate);
		} else {
			// Waiting case
			$this->datep = '1970-01-01';
		}
		if(!empty($bank)) {
		    $this->fk_bank = $bank;
		}
		return $this->update();
	}
	
	public function fetch($id) {
		$res = parent::fetch($id);
		if(!empty($this->tva)) $this->tva_tx = $this->tva/1000;
		if(!empty($this->color) && strlen($this->color) == 7) {
		    $split = str_split(substr($this->color,1), 2);
		    $this->r = hexdec($split[0]);
		    $this->g = hexdec($split[1]);
		    $this->b = hexdec($split[2]);
		}
		if(!empty($this->fk_categcomptable)) {
		    $categcomptable = new ComptaCateg($this->PDOdb);
		    $categcomptable->fetch($this->fk_categcomptable);
		    $this->categcomptable = $categcomptable;
		}
		return $res;
	}
	
	public function fetchAllFor($idaccount, $year, $tresorerie = false, $ref=null) {
		$ret = array();
		
		$sql = "SELECT rowid";
		$sql.= " FROM ".MAIN_DB_PREFIX.$this->table_element;
		if($tresorerie) {
			$sql.= " WHERE YEAR(datep) = '".$year."'";
		} else {
			$sql.= " WHERE YEAR(date_facture) = '".$year."'";
			$sql.= " AND status = 1";
		}
		$sql.= " AND fk_bank = ".$idaccount;
		$sql.= " AND fk_categcomptable <> 0";
		$sql.= " AND fk_categcomptable IS NOT NULL";
		if(!empty($ref)) {
			$sql.= " AND label LIKE '%".$ref."%'";
		}
		
		$req = $this->PDOdb->query($sql);
		while($res = $req->fetch(PDO::FETCH_OBJ)){
			$class = get_class($this);
			$object = new $class($this->PDOdb);
			$object->fetch($res->rowid);
			if($tresorerie) {
				$time = strtotime($object->date_facture);
			}else {
				$time = strtotime($object->datep);
			}
			$dateD = date('d',$time);
			$dateM = date('m',$time);
			$dateY = date('Y',$time);
			$ret[$dateY][$dateM][$dateD][] = $object;
		}
		return $ret;
	}
	
	public function show() {
		$ret = '';
		if((empty($this->date_facture) || $this->date_facture == '1970-01-01') || $this->fk_categcomptable == 0) {
			$ret = '<span class="glyphicon glyphicon-warning-sign colorred"></span> ';
		}
		$ret .= $this->getMode();
		$ret .= '<br />'.$this->label;
		if(!empty($this->date_facture) && $this->date_facture != '1970-01-01') {
		    $ret .= '<br /><b><u>Date:</u> '.$this->date_facture.'</b>';
		}else {
		    $ret .= '<br /><b><span class="glyphicon glyphicon-warning-sign colorred"></span> Aucune date</b>';
		}
		if(!empty($this->client)) {
		    $ret .= '<br /><br/><u>Client:</u> '.$this->client;
		}
		return $ret;
	}
	
	public function getMode() {
		return $this->TType[$this->mode];
	}
	
	public function getModeTrad() {
		return $this->TTypeTrad[$this->mode];
	}

	public function save() {
		$this->amount = save_price($this->amount);
		if(empty($this->status)) {
			$this->status = 0;
		} else if(strtoupper($this->status) == 'ON') {
			$this->status = 1;
		}
		if(empty($this->tva)) {
			$this->tva = 0;
		}
		if(empty($this->provision)) {
			$this->provision = 0;
		}
		if(empty($this->mode))
		    $this->mode = 0;
	    if(empty($this->color) || $this->color == '000000')
	        $this->color = null;
		if(empty($this->fk_categcomptable))
			$this->fk_categcomptable = 0;
		return parent::save();
	}
}