<?php

class Cookbook extends Model {
	protected $profileId;
	protected $title;
	protected $description;
	protected $public;
	protected $thumbnail;

	public function __construct() {
		$this->profileId = $this->foreignKey( new Profile, true );
		$this->title = $this->charField( 100 );
		$this->description = $this->textField();
		$this->public = $this->booleanField();
		$this->thumbnail = $this->charField( 255, true );
		parent::__construct();
	}

	public function validate( $data ) {
		$errors = [];

		if ( empty( $data['title'] ) ) {
			$errors['title'] = 'A title is required';
		}

		if ( empty( $data['description'] ) ) {
			$errors['description'] = 'A description is required';
		}

		return $errors;
	}
}