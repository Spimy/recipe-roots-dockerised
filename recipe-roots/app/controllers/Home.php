<?php

class Home {
	use Controller;

	public function index() {
		if ( isAuthenticated() ) {
			redirect( $_SESSION['profile']['type'] === PROFILE_TYPES['user'] ? 'recipes' : 'dashboard' );
		}

		$this->view( 'home' );
	}
}