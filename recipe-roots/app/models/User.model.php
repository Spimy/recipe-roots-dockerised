<?php

const DIETARY_TYPES = [ 'vegetarian', 'vegan', 'halal' ];

class User extends Model {
	protected $email;
	protected $password;
	protected $dietaryType;
	protected $verified;
	protected $isAdmin;

	public function __construct() {
		$this->email = $this->charField( 255, false, true );
		$this->password = $this->charField( 255, false );
		$this->dietaryType = $this->charField( 10, true, false );
		$this->verified = $this->booleanField( false );
		$this->isAdmin = $this->booleanField( false );
		parent::__construct();
	}

	public function validate( $data ) {
		$errors = [];

		if ( empty( $data['email'] ) ) {
			$errors['email'] = "Email is required";
		} else
			if ( ! filter_var( $data['email'], FILTER_VALIDATE_EMAIL ) ) {
				$errors['email'] = "Email is not valid";
			}

		if ( $this->findOne( [ 'email' => $data['email'] ] ) ) {
			$errors['email'] = 'This email is already in use';
		}

		if ( empty( $data['password'] ) ) {
			$errors['password'] = "Password is required";
		} else {
			if ( ! preg_match( '/^.*(?=.{8,})(?=.*[a-zA-Z])(?=.*\d)(?=.*[@!#$%&? "]).*$/', $data['password'] ) ) {
				$errors['password'] = 'Password must contain at least one letter, one digit and one character (@!#$%&?) and be at least 8 characters long';
			}
		}


		if ( $data['password'] != $data['confirmPassword'] ) {
			$errors['password'] = 'Passwords do not match';
		}

		return $errors;
	}

	public function verifyLogin( $data ) {
		$user = $this->findOne( [ 'email' => $data['email'] ] );
		if ( empty( $user ) ) {
			return [];
		}

		if ( ! password_verify( $data['password'], $user['password'] ) ) {
			return [];
		}

		unset( $user['password'] );
		return $user;
	}
}