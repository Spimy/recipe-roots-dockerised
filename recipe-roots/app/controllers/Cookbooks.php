<?php

class Cookbooks {
	use Controller;

	protected $profile;

	public function __construct() {
		if ( ! isAuthenticated() ) {
			handleUnauthenticated( $_GET['url'] );
		}
		$this->profile = $_SESSION['profile'];

		if ( $this->profile['type'] !== PROFILE_TYPES['user'] && ! $this->profile['user']['isAdmin'] ) {
			http_response_code( 403 );
			redirect( "settings/profiles?next=" . $_GET['url'] );
		}

		handleInvalidCsrfToken( $this );
	}

	public function index( string $id = '' ) {
		if ( $id !== '' ) {
			if ( ! is_numeric( $id ) ) {
				http_response_code( 404 );
				return $this->view( '404' );
			}

			$cookbookModel = new Cookbook();
			$cookbook = $cookbookModel->findById( $id, join: true );

			if ( ! $cookbook ) {
				http_response_code( 404 );
				return $this->view( '404' );
			}

			if ( ! $cookbook['public'] && $cookbook['profileId'] !== $this->profile['id'] && ! $this->profile['user']['isAdmin'] ) {
				http_response_code( 403 );
				return $this->view( '403', [ 'message' => 'This cookbook is private and cannot be accessed' ] );
			}

			[ $currentPage, $totalPages, $joins ] = getPaginationData(
				new CookbookJoin,
				6,
				[ 'cookbookId' => $cookbook['id'] ]
			);

			$commentModel = new Comment();
			$recipes = array_reduce(
				$joins,
				function ($c, $j) use ($commentModel) {
					$comments = $commentModel->findAll( [ 'recipeId' => $j['recipe']['id'] ] );
					$numComments = count( $comments ) > 0 ? count( $comments ) : 1;
					$averageRating = array_reduce( $comments, fn( $carry, $comment ) => $carry + $comment['rating'], 0 ) / $numComments;

					$c[] = [ ...$j['recipe'], "rating" => round( $averageRating, 0 ) ];
					return $c;
				},
				[]
			);

			return $this->view(
				'cookbooks/cookbook-detail',
				[ 
					'cookbook' => $cookbook,
					'recipes' => $recipes,
					'currentPage' => $currentPage,
					'totalPages' => $totalPages,
					'profile' => $this->profile
				]
			);
		}

		if ( $this->profile['user']['isAdmin'] && $this->profile['type'] !== PROFILE_TYPES['user'] ) {
			http_response_code( 403 );
			return $this->view( '403', [ 'message' => 'You can only browse your own cookbooks and create them on a user profile' ] );
		}

		$cookbookParams = [];
		if ( isset( $_GET['filter'] ) && $_GET['filter'] !== '' ) {
			$cookbookParams = [ 
				"title" => "%" . $_GET['filter'] . "%",
				"description" => "%" . $_GET['filter'] . "%"
			];
		}

		[ $currentPage, $totalPages, $cookbooks ] = getPaginationData(
			new Cookbook,
			6,
			[ 'profileId' => $this->profile['id'] ],
			$cookbookParams
		);
		$this->view(
			'cookbooks/cookbooks',
			[ 
				'currentPage' => $currentPage,
				'totalPages' => $totalPages,
				'cookbooks' => $this->getRating( $cookbooks ),
				'browse' => false,
				'message' => $_SESSION['cookbookDeleteMessage'] ?? null
			]
		);
		unset( $_SESSION['cookbookDeleteMessage'] );
	}

	public function browse() {
		$cookbookParams = [];
		if ( isset( $_GET['filter'] ) && $_GET['filter'] !== '' ) {
			$cookbookParams = [ 
				"title" => "%" . $_GET['filter'] . "%",
				"description" => "%" . $_GET['filter'] . "%"
			];
		}

		[ $currentPage, $totalPages, $cookbooks ] = getPaginationData(
			new Cookbook,
			6,
			$this->profile['user']['isAdmin'] ? [] : [ 'public' => 1 ],
			$cookbookParams
		);
		$this->view(
			'cookbooks/cookbooks',
			[ 
				'currentPage' => $currentPage,
				'totalPages' => $totalPages,
				'cookbooks' => $this->getRating( $cookbooks ),
				'browse' => true
			]
		);
	}

	public function create() {
		if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
			$cookbookModel = new Cookbook();
			$errors = $cookbookModel->validate( array_merge( $_POST, $_FILES ) );
			$this->handleErrors( $errors, 'Create' );

			$newCookbook = [ 
				'profileId' => $this->profile['id'],
				'title' => $_POST['title'],
				'description' => $_POST['description'],
				'public' => $_POST['public'] == 'yes' ? 1 : 0
			];

			if ( $_FILES['thumbnail']['error'] == UPLOAD_ERR_OK ) {
				$tmp_name = $_FILES['thumbnail']['tmp_name'];
				$name = basename( $_FILES['thumbnail']['name'] );
				$newCookbook['thumbnail'] = uploadFile( 'thumbnails', $tmp_name, $name );
			}

			$cookbook = $cookbookModel->create( $newCookbook );
			redirect( $_POST['from'] ?? 'cookbooks/' . $cookbook['id'] );
		}

		$this->view( 'cookbooks/cookbook-editor', [ 'action' => 'Create' ] );
	}

	public function edit( $id = null ) {
		if ( ! $id ) {
			redirect( 'cookbooks' );
		}

		if ( ! is_numeric( $id ) ) {
			http_response_code( 404 );
			return $this->view( '404' );
		}

		$cookbookModel = new Cookbook();
		$cookbook = $cookbookModel->findById( $id, join: true );

		if ( ! $cookbook ) {
			http_response_code( 404 );
			return $this->view( '404' );
		}

		if ( ! $cookbook['public'] && $cookbook['profileId'] !== $this->profile['id'] && ! $this->profile['user']['isAdmin'] ) {
			http_response_code( 403 );
			return $this->view( '403', [ 'message' => 'You do not have permissions to edit this cookbook' ] );
		}

		if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
			$errors = $cookbookModel->validate( array_merge( $_POST, $_FILES ) );
			$this->handleErrors( $errors, 'Edit', $id );

			$cookbookData = [ 
				'title' => $_POST['title'],
				'description' => $_POST['description'],
				'public' => $_POST['public'] == 'yes' ? 1 : 0
			];

			if ( $_FILES['thumbnail']['error'] == UPLOAD_ERR_OK ) {
				$tmp_name = $_FILES['thumbnail']['tmp_name'];
				$name = basename( $_FILES['thumbnail']['name'] );
				$cookbookData['thumbnail'] = uploadFile( 'thumbnails', $tmp_name, $name );
			}

			$success = $cookbookModel->update( $id, $cookbookData );
			if ( ! $success ) {
				http_response_code( 500 );
				$this->view(
					'cookbooks/cookbook-editor',
					[ 
						'action' => 'Edit',
						'errors' => [ 'Something went wrong updating the cookbook and could not be saved' ],
						'data' => $_POST
					] );
			}

			redirect( "cookbooks/$id" );

		}

		$cookbook['public'] = $cookbook['public'] ? 'yes' : 'no';
		$this->view(
			'cookbooks/cookbook-editor',
			[ 
				'action' => 'Edit',
				'data' => $cookbook
			]
		);
	}

	public function delete() {
		if ( $_SERVER['REQUEST_METHOD'] !== 'POST' ) {
			redirect( 'cookbooks' );
		}

		$cookbookId = $_POST['cookbookId'] ?? null;
		if ( ! $cookbookId || ! is_numeric( $cookbookId ) ) {
			http_response_code( 400 );
			redirect( 'cookbooks' );
		}

		$cookbookModel = new Cookbook();
		$cookbook = $cookbookModel->findById( $cookbookId, true );

		if ( ! $cookbook ) {
			http_response_code( 404 );
			return $this->view( '404' );
		}

		if ( $cookbook['profileId'] != $this->profile['id'] && ! $this->profile['user']['isAdmin'] ) {
			http_response_code( 403 );
			return $this->view( '403', [ 'message' => 'You do not have permissions to delete this cookbook' ] );
		}

		$cookbookModel->delete( $cookbookId );
		$_SESSION['cookbookDeleteMessage'] = 'Successfully deleted cookbook';
		redirect( 'cookbooks' );
	}

	private function handleErrors( $errors, $action, $id = null ) {
		if ( count( $errors ) > 0 ) {
			if ( $id ) {
				$_POST['id'] = $id;
			}

			http_response_code( 400 );
			$this->view(
				'cookbooks/cookbook-editor',
				[ 
					'action' => $action,
					'errors' => $errors,
					'data' => $_POST
				] );
			die;
		}
	}

	private function getRating( array $cookbooks ) {
		$cookbookJoinModel = new CookbookJoin();
		$commentModel = new Comment();

		foreach ( $cookbooks as $index => $cookbook ) {
			$cookbooks[ $index ]['rating'] = 0;
			$numRatings = 0;
			$totalRating = 0;

			$joins = $cookbookJoinModel->findAll( [ 'cookbookId' => $cookbook['id'] ] );

			foreach ( $joins as $join ) {
				$comments = $commentModel->findAll( [ 'recipeId' => $join['recipeId'] ] );
				$numRatings += count( $comments );
				$totalRating += array_reduce( $comments, fn( $ca, $c ) => $ca + $c['rating'], 0 );
			}

			$cookbooks[ $index ]['rating'] = round( $totalRating / ( $numRatings > 0 ? $numRatings : 1 ) );
		}

		return $cookbooks;
	}
}