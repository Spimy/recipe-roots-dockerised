<?php

class Invoice extends Model {
	protected $invoiceId;
	protected $profileId;
	protected $purchaseIds;

	public function __construct() {
		$this->invoiceId = $this->charField( 32 );
		$this->profileId = $this->foreignKey( new Profile, true );
		$this->purchaseIds = $this->jsonField();
		parent::__construct();
	}
}