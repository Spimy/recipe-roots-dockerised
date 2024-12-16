<?php

class Notification extends Model {
	protected $senderId;
	protected $receiverId;
	protected $message;
	protected $link;
	protected $isRead;

	public function __construct() {
		$this->senderId = $this->foreignKey( new Profile, true );
		$this->receiverId = $this->foreignKey( new Profile, true );
		$this->message = $this->charField( 255 );
		$this->link = $this->charField( 255, true );
		$this->isRead = $this->booleanField();
		parent::__construct();
	}
}