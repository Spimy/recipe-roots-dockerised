<?php

class Session extends Model {
	protected $sessionId;
	protected $profileId;

	public function __construct() {
		$this->sessionId = $this->charField();
		$this->profileId = $this->foreignKey( new Profile, true );
		parent::__construct();
	}
}