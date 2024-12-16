<?php

class ResetToken extends Model {
	protected $userId;
	protected $token;
	protected $expiresAt;

	public function __construct() {
		$this->userId = $this->foreignKey( new User, true );
		$this->token = $this->charField( 255, false, true );
		$this->expiresAt = $this->dateTimeField( false );
		parent::__construct();
	}
}
