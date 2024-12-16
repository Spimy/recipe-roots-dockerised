<?php

class Settings {
	use Controller;

	private $profile;

	public function __construct() {
		if ( ! isAuthenticated() ) {
			handleUnauthenticated( $_GET['url'] );
		}
		$this->profile = $_SESSION['profile'];

		handleInvalidCsrfToken( $this );
	}

	// Handle user settings
	public function index() {
		$errors = [];

		if ( isset( $_SESSION['accountDeleteError'] ) ) {
			http_response_code( $_SESSION['accountDeleteError']['status'] );
			$errors['accountDeleteError'] = $_SESSION['accountDeleteError']['message'];
		}

		if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
			$currentPassword = $_POST['currentPassword'] ?? null;
			if ( ! $currentPassword ) {
				http_response_code( 400 );
				$errors['currentPassword'] = 'Current password is required';

				return $this->view(
					'settings/account',
					[ 
						'account' => $this->profile['user'],
						'errors' => $errors
					]
				);
			}

			$email = $_POST['email'] ?? null;
			if ( ! $email ) {
				http_response_code( 400 );
				$errors['email'] = 'Email is required';
			}

			$userModel = new User();
			$user = $userModel->findById( $this->profile['user']['id'] );

			if ( ! password_verify( $currentPassword, $user['password'] ) ) {
				http_response_code( 403 );
				$errors['currentPassword'] = 'The password you provided is incorrect';
				return $this->view(
					'settings/account',
					[ 
						'account' => $this->profile['user'],
						'errors' => $errors
					]
				);
			}

			if ( $email !== $user['email'] && count( $userModel->findOne( [ 'email' => $email ] ) ) > 0 ) {
				http_response_code( 400 );
				$errors['email'] = 'The email you provided is already in use by another account';
			}

			$newPassword = $_POST['newPassword'] ?? null;
			$confirmPassword = $_POST['confirmPassword'] ?? null;

			if ( $newPassword && $newPassword !== $confirmPassword ) {
				http_response_code( 400 );
				$errors['newPassword'] = 'The new passwords do not match';
			}

			if ( count( $errors ) > 0 ) {
				return $this->view(
					'settings/account',
					[ 
						'account' => $this->profile['user'],
						'errors' => $errors
					]
				);
			}

			$userDetails = [ 
				'email' => $email,
				'dietaryType' => $_POST['dietaryType'] == 'none' ? null : $_POST['dietaryType'] ?? null,
			];

			if ( $newPassword ) {
				$userDetails = [ ...$userDetails, 'password' => password_hash( $newPassword, PASSWORD_DEFAULT ) ];
			}

			$success = $userModel->update( $user['id'], $userDetails );
			if ( ! $success ) {
				http_response_code( 500 );
				return $this->view(
					'settings/account',
					[ 
						'account' => $this->profile['user'],
						'errors' => [ 'Something went wrong and could not update your information' ]
					]
				);
			}

			$profile = ( new Profile() )->findById( $this->profile['id'], join: true );
			$_SESSION['profile'] = $profile;

			return $this->view(
				'settings/account',
				[ 
					'account' => $profile['user'],
					'message' => 'Your information has been updated'
				]
			);
		}

		$this->view( 'settings/account', [ 'account' => $this->profile['user'], 'errors' => $errors ] );
		unset( $_SESSION['accountDeleteError'] );
	}

	// Handle profile settings
	public function profiles( string $method = '' ) {
		$profileModel = new Profile();
		$profiles = $profileModel->findAll( [ 'userId' => $this->profile['user']['id'] ], join: true );

		if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
			$errors = [];

			$currentPassword = $_POST['currentPassword'] ?? null;
			if ( ! $currentPassword ) {
				http_response_code( 400 );
				$errors['currentPassword'] = 'Current password is required';

				return $this->view(
					'settings/account',
					[ 
						'account' => $this->profile['user'],
						'errors' => $errors
					]
				);
			}

			$userModel = new User();
			$user = $userModel->findById( $this->profile['user']['id'] );

			if ( ! password_verify( $currentPassword, $user['password'] ) ) {
				http_response_code( 403 );
				$errors['currentPassword'] = 'The password you provided is incorrect';
				return $this->view(
					'settings/account',
					[ 
						'account' => $this->profile['user'],
						'errors' => $errors
					]
				);
			}

			$errors = array_merge( $errors, $profileModel->validate( array_merge( $_POST, $_FILES ) ) );

			switch ( strtolower( $method ) ) {
				case 'update': {
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

					$_SESSION['profileMessage'] = 'Successfully updated your profile';
					$profileModel->update( $profileId, $profileDetails );

					// Update the session if the profile updated is the current active profile
					if ( $this->profile['id'] == $profileId ) {
						$_SESSION['profile'] = $profileModel->findById( $profileId, join: true );
					}

					redirect( 'settings/profiles' );
					break;
				}
				case 'create': {
					if ( count( $errors ) > 0 ) {
						http_response_code( 400 );
						return $this->view( 'settings/profile', [ 'profiles' => $profiles, 'errors' => $errors ] );
					}

					$profileDetails = [ 
						'username' => $_POST['username'],
						'type' => $this->profile['type'] === PROFILE_TYPES['user'] ? PROFILE_TYPES['farmer'] : PROFILE_TYPES['user']
					];

					if ( $_FILES['avatar']['error'] == UPLOAD_ERR_OK ) {
						$tmp_name = $_FILES['avatar']['tmp_name'];
						$name = basename( $_FILES['avatar']['name'] );
						$profileDetails['avatar'] = uploadFile( 'avatars', $tmp_name, $name );
					}

					$_SESSION['profileMessage'] = 'Successfully created your profile';
					$profileModel->create( array_merge( $profileDetails, [ 'userId' => $user['id'] ] ) );
					redirect( 'settings/profiles' );
					break;
				}
				default: {
					redirect( 'settings/profiles' );
				}
			}
		}

		if ( $method === 'switch' ) {
			$swapProfile = current( array_filter( $profiles, fn( $p ) => $p['id'] != $this->profile['id'] ) ) ?? null;
			if ( $swapProfile ) {
				if ( isset( $_COOKIE['profile'] ) ) {
					$sessionModel = new Session();
					$session = $sessionModel->findOne( [ 'sessionId' => $_COOKIE['profile'] ] );
					$sessionModel->update( $session['id'], [ 'profileId' => $swapProfile['id'] ] );
				}

				$_SESSION['profile'] = $swapProfile;
				if ( ! isset( $_GET['next'] ) ) {
					$_SESSION['profileMessage'] = 'You have been swapped to your ' . ( $swapProfile['type'] === PROFILE_TYPES['user'] ? 'User' : 'Farmer' ) . ' profile';
				}
			}

			redirect( isset( $_GET['next'] ) ? $_GET['next'] : 'settings/profiles' );
		} else {
			$this->view( 'settings/profile', [ 'profiles' => $profiles, 'message' => $_SESSION['profileMessage'] ?? null ] );
			unset( $_SESSION['profileMessage'] );
		}

	}

	// Handle delete account
	public function delete() {
		if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
			$errors = [];

			$currentPassword = $_POST['currentPassword'] ?? null;
			if ( ! $currentPassword ) {
				http_response_code( 400 );
				$_SESSION['accountDeleteError'] = [ 
					'status' => 403,
					'message' => 'Current password is required'
				];
				redirect( 'settings' );
			}

			$userModel = new User();
			$user = $userModel->findById( $this->profile['user']['id'] );

			if ( ! password_verify( $currentPassword, $user['password'] ) ) {
				http_response_code( 403 );
				$_SESSION['accountDeleteError'] = [ 
					'status' => 403,
					'message' => 'The password you provided is incorrect'
				];
				redirect( 'settings' );
			}

			// Also deletes profiles associated as they cascade once user is deleted
			$success = $userModel->delete( $user['id'] );
			if ( ! $success ) {
				http_response_code( 500 );
				$_SESSION['accountDeleteError'] = [ 
					'status' => 500,
					'message' => 'Something went wrong when deleting your account, please try again'
				];
				redirect( 'settings' );
			}

			// Sign the user out after the account is deleted
			redirect( 'signout?delete=success' );
		} else {
			redirect( 'settings' );
		}
	}
}