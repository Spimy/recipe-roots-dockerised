<?php

class Ingredients {
	use Controller;

	private $profile;
	private $itemsPerPage = 6;
	private $taxPercentage = 0.06;

	public function __construct() {
		if ( ! isAuthenticated() ) {
			handleUnauthenticated( $_GET['url'] );
		}
		$this->profile = $_SESSION['profile'];

		if ( $this->profile['type'] !== PROFILE_TYPES['user'] ) {
			http_response_code( 403 );
			redirect( "settings/profiles?next=" . $_GET['url'] );
		}

		handleInvalidCsrfToken( $this );
	}

	public function index() {
		$ingredientParams = [];

		if ( isset( $_GET['filter'] ) && $_GET['filter'] !== '' ) {
			$ingredientParams = [ 'ingredient' => "%" . $_GET['filter'] . "%" ];
		}

		$itemsPerPage = 6;
		$ingredientModel = new Ingredient();

		[ $currentPage, $totalPages, $ingredients ] = getPaginationData(
			$ingredientModel,
			$itemsPerPage,
			[ 'unlisted' => 0 ],
			$ingredientParams
		);

		$this->view(
			'ingredients',
			[ 
				'ingredients' => $ingredients,
				'currentPage' => $currentPage,
				'totalPages' => $totalPages,
				'errors' => $_SESSION['cartErrors'] ?? []
			]
		);
		unset( $_SESSION['cartErrors'] );
	}

	public function cart() {
		if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
			$cart = json_decode( $_COOKIE['cart'] ?? '[]', true );
			$errors = [];

			if ( empty( $_POST['ingredientId'] ) ) {
				http_response_code( 400 );
				$errors[] = 'Ingredient id is required';
			}

			if ( ( empty( $_POST['amount'] ) || ! is_numeric( $_POST['amount'] ) ) && (int) $_POST['amount'] !== 0 ) {
				http_response_code( 400 );
				$errors[] = 'Amount is required and must be numeric';
			}

			if ( (int) $_POST['amount'] < 0 || (int) $_POST['amount'] > 99 ) {
				http_response_code( 400 );
				$errors[] = 'Amount must be between 0-99';
			}

			if ( count( $errors ) > 0 ) {
				$_SESSION['cartErrors'] = $errors;
				unset( $_GET['url'] );
				redirect( $_POST['from'] ?? 'ingredients' . http_build_query( $_GET ?? [] ) );
			}

			if ( (int) $_POST['amount'] === 0 ) {
				unset( $cart[ $_POST['ingredientId'] ] );
			} else {
				$cart[ $_POST['ingredientId'] ] = round( (int) $_POST['amount'], 0 );
			}

			setcookie( 'cart', count( $cart ) > 0 ? json_encode( $cart ) : '' );
			unset( $_GET['url'] );
			redirect( $_POST['from'] ?? 'ingredients' . '?' . http_build_query( $_GET ?? [] ) );
		}

		$populatedCart = $this->getPopulatedCart();
		[ $subtotal, $tax, $total ] = $this->getPricingData( $populatedCart );

		return $this->view(
			'cart',
			[ 
				'cart' => $populatedCart,
				'pricing' => [ 
					'subtotal' => $subtotal,
					'tax' => $tax,
					'total' => $total
				]
			]
		);
	}

	public function checkout() {
		if ( $_SERVER['REQUEST_METHOD'] === 'GET' ) {
			redirect( 'ingredients/cart' );
		}

		$populatedCart = $this->getPopulatedCart();
		$purchases = [];
		$purchaseModel = new Purchase();

		foreach ( $populatedCart as $ingredient ) {
			$purchases[] = $purchaseModel->create( [ 
				'farmerId' => $ingredient['farmerId'],
				'ingredientId' => $ingredient['id'],
				'amount' => $ingredient['amount']
			] );
		}

		$invoiceModel = new Invoice();
		$invoice = $invoiceModel->create( [ 
			'invoiceId' => 'rr_' . $this->profile['id'] . '_' . time(),
			'profileId' => $this->profile['id'],
			'purchaseIds' => json_encode( array_map( fn( $p ) => $p['id'], $purchases ) )
		] );

		setcookie( 'cart', '' );
		redirect( 'ingredients/invoices/' . $invoice['invoiceId'] );
	}

	public function invoices( string $invoiceId = null ) {
		$invoiceModel = new Invoice();

		if ( $invoiceId ) {
			$invoice = $invoiceModel->findOne( [ 'invoiceId' => $invoiceId, 'profileId' => $this->profile['id'] ], join: true );

			// Return 404 so that people cannot crawl for valid invoices if it is owned by someone else
			if ( ! $invoice ) {
				http_response_code( 404 );
				return $this->view( '404' );
			}

			$purchaseModel = new Purchase();
			$invoice['purchases'] = array_map( fn( $id ) => $purchaseModel->findById( $id, true ), json_decode( $invoice['purchaseIds'] ) );
			$invoice['purchases'] = array_filter( $invoice['purchases'], fn( $i ) => $i !== null );

			$subtotal = number_format( array_reduce( $invoice['purchases'], fn( $c, $i ) => $c + $i['amount'] * $i['ingredient']['price'], 0 ), 2 );
			$tax = $subtotal * $this->taxPercentage;
			$total = number_format( $subtotal + $tax, 2 );
			return $this->view(
				'invoices/invoice-detail',
				[ 
					'invoice' => $invoice,
					'pricing' => [ 
						'subtotal' => $subtotal,
						'tax' => $tax,
						'total' => $total
					]
				]
			);
		}

		$invoiceConditions = [ 'profileId' => $this->profile['id'] ];
		[ $currentPage, $totalPages, $invoices ] = getPaginationData(
			$invoiceModel,
			$this->itemsPerPage,
			$invoiceConditions
		);
		$this->view( 'invoices/invoices', [ 'invoices' => $invoices, 'currentPage' => $currentPage, 'totalPages' => $totalPages ] );
	}

	protected function getPopulatedCart() {
		$cart = json_decode( $_COOKIE['cart'] ?? '[]', true );
		$populatedCart = [];

		$ingredientModel = new Ingredient();
		foreach ( $cart as $ingredientId => $amount ) {
			$ingredient = $ingredientModel->findById( $ingredientId, join: true );

			if ( ! $ingredient ) {
				continue;
			}

			$populatedCart[ $ingredientId ] = array_merge( $ingredient, [ 'amount' => $amount ] );
		}

		return $populatedCart;
	}

	protected function getPricingData( array $populatedCart ) {
		$subtotal = number_format( array_reduce( $populatedCart, fn( $c, $i ) => $c + $i['amount'] * $i['price'], 0 ), 2 );
		$tax = number_format( $subtotal * $this->taxPercentage, 2 );
		$total = number_format( $subtotal + $tax, 2 );
		return [ $subtotal, $tax, $total ];
	}
}