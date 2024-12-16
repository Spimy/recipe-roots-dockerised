<?php

class Signout {
	use Controller;

	public function index() {
		session_unset();
		session_destroy();

		setcookie( 'profile', '', [ 
			'expires' => -1,
			'path' => '/',
			'domain' => DOMAIN,
			'secure' => true,
			'httponly' => true
		] );

		redirect( '' . isset( $_GET['delete'] ) ? '?delete=' . $_GET['delete'] : '' );
	}
}