<?php

class Dashboard {
	use Controller;

	private $profile;

	public function __construct() {
		if ( ! isAuthenticated() ) {
			handleUnauthenticated( $_GET['url'] );
		}
		$this->profile = $_SESSION['profile'];

		if ( $this->profile['type'] !== PROFILE_TYPES['farmer'] && ! $this->profile['user']['isAdmin'] ) {
			http_response_code( 403 );
			redirect( "settings/profiles?next=" . $_GET['url'] );
		}

		handleInvalidCsrfToken( $this );
	}

	public function index() {
		if ( $this->profile['user']['isAdmin'] && $this->profile['type'] !== PROFILE_TYPES['farmer'] ) {
			http_response_code( 403 );
			return $this->view( '403', [ 'message' => 'You can only view dashboard or add produce on a farmer profile' ] );
		}

		$purchaseModel = new Purchase();
		$sales = $purchaseModel->findAll( [ 'farmerId' => $this->profile['id'] ], join: true );
		$groupedSales = $purchaseModel->groupSalesByDate( $sales );
		$dataPoints = $purchaseModel->createDataPoints( $groupedSales );

		$itemsPerPage = 6;
		$ingredientModel = new Ingredient();
		$ingredientConditions = [ 'farmerId' => $this->profile['id'], 'unlisted' => 0 ];

		[ $currentPage, $totalPages, $ingredients ] = getPaginationData(
			$ingredientModel,
			$itemsPerPage,
			$ingredientConditions
		);

		$this->view(
			'farmer/dashboard',
			[ 
				'dataPoints' => $dataPoints,
				'ingredients' => $ingredients,
				'currentPage' => $currentPage,
				'totalPages' => $totalPages,
				'message' => $_SESSION['produceDeleteMessage'] ?? null
			]
		);
		unset( $_SESSION['produceDeleteMessage'] );
	}

	public function produce( string $method = null, int $id = null ) {
		if ( ! $method ) {
			redirect( 'dashboard' );
		}

		switch ( $method ) {
			case 'add': {
				$this->addProduce();
				break;
			}
			case 'edit': {
				$this->editProduce( $id );
				break;
			}
			case 'delete': {
				$this->deleteProduce();
				break;
			}
			default:
				redirect( 'dashboard' );
		}
	}

	protected function addProduce() {
		if ( $this->profile['user']['isAdmin'] && $this->profile['type'] !== PROFILE_TYPES['farmer'] ) {
			http_response_code( 403 );
			return $this->view( '403', [ 'message' => 'You can only view dashboard or add produce on a farmer profile' ] );
		}

		if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
			$ingredientModel = new Ingredient();
			$errors = $ingredientModel->validate( array_merge( $_POST, $_FILES ) );

			if ( count( $errors ) > 0 ) {
				http_response_code( 400 );
				return $this->view( 'farmer/produce-editor', [ 'action' => 'Add', 'errors' => $errors ] );
			}

			$newIngredient = [ 
				'farmerId' => $this->profile['id'],
				'ingredient' => $_POST['ingredient'],
				'price' => number_format( $_POST['price'], 2, '.', '' ),
				'unit' => $_POST['unit'],
			];

			if ( $_FILES['thumbnail']['error'] == UPLOAD_ERR_OK ) {
				$tmp_name = $_FILES['thumbnail']['tmp_name'];
				$name = basename( $_FILES['thumbnail']['name'] );
				$newIngredient['thumbnail'] = uploadFile( 'thumbnails', $tmp_name, $name );
			}

			$ingredient = $ingredientModel->create( $newIngredient );
			$_SESSION['produceAddMessage'] = 'Successfully added new produce';
			redirect( 'dashboard/produce/edit' . $ingredient['id'] );
		}
		$this->view( 'farmer/produce-editor', [ 'action' => 'Add' ] );
	}

	protected function editProduce( int $id ) {
		if ( ! $id || ! is_numeric( $id ) ) {
			http_response_code( 400 );
			redirect( 'dashboard' );
		}

		$ingredientModel = new Ingredient();
		$ingredient = $ingredientModel->findById( $id, true );

		if ( ! $ingredient ) {
			http_response_code( 404 );
			return $this->view( '404' );
		}

		if ( $ingredient['farmerId'] != $this->profile['id'] && ! $this->profile['user']['isAdmin'] ) {
			http_response_code( 403 );
			return $this->view( '403', [ 'message' => 'You do not have permissions to edit this produce', 'data' => $ingredient ] );
		}

		if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
			$errors = $ingredientModel->validate( array_merge( $_POST, $_FILES ) );

			if ( count( $errors ) > 0 ) {
				http_response_code( 400 );
				return $this->view( 'farmer/produce-editor', [ 'action' => 'Edit', 'errors' => $errors, 'data' => $ingredient ] );
			}

			$ingredientData = [ 
				'ingredient' => $_POST['ingredient'],
				'price' => number_format( $_POST['price'], 2, '.', '' ),
				'unit' => $_POST['unit'],
			];

			if ( $_FILES['thumbnail']['error'] == UPLOAD_ERR_OK ) {
				$tmp_name = $_FILES['thumbnail']['tmp_name'];
				$name = basename( $_FILES['thumbnail']['name'] );
				$ingredientData['thumbnail'] = uploadFile( 'thumbnails', $tmp_name, $name );
			}

			$success = $ingredientModel->update( $id, $ingredientData );
			if ( ! $success ) {
				http_response_code( 500 );
				$_SESSION['recipeErrors'] = [ 'Something went wrong updating the recipe and could not be saved' ];
			}

			$ingredient = $ingredientModel->findById( $id, true );
			$message = 'Successfully edited your produce';
		}

		$message ??= $_SESSION['produceAddMessage'] ?? null;
		$this->view( 'farmer/produce-editor', [ 'action' => 'Edit', 'data' => $ingredient, 'message' => $message ] );
		unset( $_SESSION['produceAddMessage'] );
	}

	protected function deleteProduce() {
		if ( $_SERVER['REQUEST_METHOD'] !== 'POST' ) {
			redirect( 'dashboard' );
		}

		$ingredientId = $_POST['ingredientId'] ?? null;
		if ( ! $ingredientId || ! is_numeric( $ingredientId ) ) {
			http_response_code( 400 );
			redirect( 'dashboard' );
		}

		$ingredientModel = new Ingredient();
		$ingredient = $ingredientModel->findById( $ingredientId, true );

		if ( ! $ingredient ) {
			http_response_code( 404 );
			return $this->view( '404' );
		}

		if ( $ingredient['farmerId'] != $this->profile['id'] && ! $this->profile['user']['isAdmin'] ) {
			http_response_code( 403 );
			return $this->view( '403', [ 'message' => 'You do not have permissions to delete this produce' ] );
		}

		$ingredientModel->update( $ingredientId, [ 'unlisted' => 1 ] );
		$_SESSION['produceDeleteMessage'] = 'Successfully deleted produce';

		if ( isset( $_GET['from'] ) ) {
			redirect( $_GET['from'] );
		}

		redirect( 'dashboard' );
	}
}