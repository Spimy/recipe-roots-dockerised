<?php

class Admin {
	use Controller;

	private $profile;

	public function __construct() {
		if ( ! isAuthenticated() ) {
			handleUnauthenticated( $_GET['url'] );
		}
		$this->profile = $_SESSION['profile'];

		if ( ! $this->profile['user']['isAdmin'] ) {
			http_response_code( 403 );
			redirect( 'home' );
		}

		handleInvalidCsrfToken( $this );
	}


	public function index() {
		[ $currentPage, $totalPages, $users ] = getPaginationData( new User, 6 );
		$this->view(
			'admin/users',
			[ 
				'users' => $users,
				'currentPage' => $currentPage,
				'totalPages' => $totalPages,
				'message' => $_SESSION['accountDeleteSuccess'] ?? null
			]
		);
		unset( $_SESSION['accountDeleteSuccess'] );
	}

	public function edit( $type = null, $id = null ) {
		if ( ! $id || ! is_numeric( $id ) ) {
			redirect( 'admin' );
		}

		$userModel = new User();
		$user = $userModel->findById( $id );

		if ( ! $user ) {
			redirect( 'admin' );
		}

		switch ( $type ) {
			case 'account': {
				return $this->editUser( $user );
			}
			case 'profiles': {
				return $this->editProfile( $user );
			}
			default: {
				return redirect( 'admin' );
			}
		}
	}

	public function delete( $id ) {
		if ( $_SERVER['REQUEST_METHOD'] !== 'POST' ) {
			redirect( 'admin' );
		}

		if ( ! $id || ! is_numeric( $id ) ) {
			redirect( 'admin' );
		}

		$userModel = new User();
		$user = $userModel->findById( $id );

		if ( ! $user ) {
			redirect( 'admin' );
		}

		$currentPassword = $_POST['currentPassword'] ?? null;
		if ( ! $currentPassword ) {
			http_response_code( 400 );
			$_SESSION['accountDeleteError'] = [ 
				'status' => 403,
				'message' => 'Current password is required'
			];
			redirect( 'admin/edit/account/' . $user['id'] );
		}

		$admin = $userModel->findById( $this->profile['userId'] );
		if ( ! password_verify( $currentPassword, $admin['password'] ) ) {
			http_response_code( 403 );
			$_SESSION['accountDeleteError'] = [ 
				'status' => 403,
				'message' => 'The password you provided is incorrect'
			];
			redirect( 'admin/edit/account/' . $user['id'] );
		}

		// Also deletes profiles associated as they cascade once user is deleted
		$success = $userModel->delete( $user['id'] );
		if ( ! $success ) {
			http_response_code( 500 );
			$_SESSION['accountDeleteError'] = [ 
				'status' => 500,
				'message' => 'Something went wrong when deleting your account, please try again'
			];
			redirect( 'admin/edit/account/' . $user['id'] );
		}

		// Log the user out if the admin deleted their own account
		if ( $user['id'] === $this->profile['userId'] ) {
			redirect( 'signout?delete=success' );
		}

		$_SESSION['accountDeleteSuccess'] = 'Successfully deleted user';
		redirect( 'admin' );
	}

	public function ingredients() {
		[ $currentPage, $totalPages, $ingredients ] = getPaginationData( new Ingredient, 6, [ 'unlisted' => 0 ] );
		$this->view(
			'admin/ingredients',
			[ 
				'ingredients' => $ingredients,
				'currentPage' => $currentPage,
				'totalPages' => $totalPages,
				'message' => $_SESSION['produceDeleteMessage'] ?? null
			]
		);
		unset( $_SESSION['produceDeleteMessage'] );
	}

	private function editUser( $user ) {
		$userModel = new User();

		if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
			$errors = [];

			$currentPassword = $_POST['currentPassword'] ?? null;
			if ( ! $currentPassword ) {
				http_response_code( 400 );
				$errors['currentPassword'] = 'Current password is required';

				return $this->view(
					'admin/edit-user',
					[ 
						'user' => $user,
						'errors' => $errors
					]
				);
			}

			$email = $_POST['email'] ?? null;
			if ( ! $email ) {
				http_response_code( 400 );
				$errors['email'] = 'Email is required';
			}

			$admin = $userModel->findById( $this->profile['userId'] );
			if ( ! password_verify( $currentPassword, $admin['password'] ) ) {
				http_response_code( 403 );
				$errors['currentPassword'] = 'The password you provided is incorrect';
				return $this->view(
					'admin/edit-user',
					[ 
						'user' => $user,
						'errors' => $errors
					]
				);
			}

			$newPassword = $_POST['newPassword'] ?? null;
			$confirmPassword = $_POST['confirmPassword'] ?? null;

			if ( $newPassword && $newPassword !== $confirmPassword ) {
				http_response_code( 400 );
				$errors['newPassword'] = 'The new passwords do not match';
			}

			if ( count( $errors ) > 0 ) {
				return $this->view(
					'admin/edit-user',
					[ 
						'user' => $user,
						'errors' => $errors
					]
				);
			}

			$userDetails = [ 
				'email' => $email,
				'dietaryType' => $_POST['dietaryType'] == 'none' ? null : $_POST['dietaryType'] ?? null,
				'isAdmin' => $_POST['admin'] == 'no' ? 0 : ( $_POST['admin'] == 'yes' ? 1 : 0 )
			];

			if ( $newPassword ) {
				$userDetails = [ ...$userDetails, 'password' => password_hash( $newPassword, PASSWORD_DEFAULT ) ];
			}

			$success = $userModel->update( $user['id'], $userDetails );
			if ( ! $success ) {
				http_response_code( 500 );
				return $this->view(
					'admin/edit-user',
					[ 
						'user' => $user,
						'errors' => [ 'Something went wrong and could not update the account' ]
					]
				);
			}

			// If the user that got updated is the admin's account then we need to update their current session
			if ( $user['id'] === $admin['id'] ) {
				$profile = ( new Profile() )->findById( $this->profile['id'], join: true );
				$_SESSION['profile'] = $profile;
			}

			$user = $userModel->findById( $user['id'] );
			$message = 'Updated user successfully';
		}

		if ( isset( $_SESSION['accountDeleteError'] ) ) {
			http_response_code( $_SESSION['accountDeleteError']['status'] );
			$errors['accountDeleteError'] = $_SESSION['accountDeleteError']['message'];
		}

		$this->view( 'admin/edit-user', [ 'user' => $user, 'message' => $message ?? null, 'errors' => $errors ?? null ] );
		unset( $_SESSION['accountDeleteError'] );
	}

	private function editProfile( $user ) {
		$profileModel = new Profile();
		$profiles = $profileModel->findAll( [ 'userId' => $user['id'] ] );

		if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
			$errors = [];

			$currentPassword = $_POST['currentPassword'] ?? null;
			if ( ! $currentPassword ) {
				http_response_code( 400 );
				$errors['currentPassword'] = 'Current password is required';

				return $this->view(
					'admin/edit-profile',
					[ 
						'profiles' => $profiles,
						'errors' => $errors
					]
				);
			}

			$userModel = new User();
			$admin = $userModel->findById( $this->profile['userId'] );
			if ( ! password_verify( $currentPassword, $admin['password'] ) ) {
				http_response_code( 403 );
				$errors['currentPassword'] = 'The password you provided is incorrect';
				return $this->view(
					'admin/edit-profile',
					[ 
						'profiles' => $profiles,
						'errors' => $errors
					]
				);
			}

			$errors = array_merge( $errors, $profileModel->validate( array_merge( $_POST, $_FILES ) ) );

			if ( empty( $_POST['profileId'] ) ) {
				$errors['profileId'] = 'No profile id has been provided';
			}

			if ( ! is_numeric( $_POST['profileId'] ) ) {
				$errors['profileId'] = 'Invalid or non-exising profile id provided';
			}

			$profileId = $_POST['profileId'];
			$profile = current( array_filter( $profiles, fn( $p ) => $p['id'] == $profileId ) ) ?? null;
			if ( ! $profile ) {
				$errors['profileId'] = 'Invalid or non-exising profile id provided';
			}

			if ( isset( $errors['usernameTaken'] ) ) {
				if ( $profileId == $profile['id'] ) {
					unset( $errors['usernameTaken'] );
				}
			}

			if ( count( $errors ) > 0 ) {
				http_response_code( 400 );
				return $this->view( 'settings/profile', [ 'profiles' => $profiles, 'errors' => $errors ] );
			}

			$profileDetails = [ 'username' => $_POST['username'] ];
			if ( $_FILES['avatar']['error'] == UPLOAD_ERR_OK ) {
				$tmp_name = $_FILES['avatar']['tmp_name'];
				$name = basename( $_FILES['avatar']['name'] );
				$profileDetails['avatar'] = uploadFile( 'avatars', $tmp_name, $name );
			}

			$success = $profileModel->update( $profileId, $profileDetails );
			if ( ! $success ) {
				http_response_code( 500 );
				return $this->view(
					'admin/edit-profile',
					[ 
						'profiles' => $profiles,
						'errors' => [ 'Something went wrong and could not update the profile' ]
					]
				);
			}

			// Update the session if the profile updated is the current active profile
			if ( $this->profile['id'] === $profileId ) {
				$_SESSION['profile'] = $profileModel->findById( $profileId, join: true );
			}

			$profiles = $profileModel->findAll( [ 'userId' => $user['id'] ] );
			$message = 'Updated profile successfully';
		}

		$this->view( 'admin/edit-profile', [ 'profiles' => $profiles, 'userId' => $user['id'], 'message' => $message ?? null ] );
	}
}