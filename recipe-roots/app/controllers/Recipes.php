<?php

class Recipes {
	use Controller;

	private $profile;
	private $itemsPerPage = 6;

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
		$recipeModel = new Recipe();

		// List of all recipes for the authenticated user are controlled below
		if ( $id === '' ) {
			if ( $this->profile['user']['isAdmin'] && $this->profile['type'] !== PROFILE_TYPES['user'] ) {
				http_response_code( 403 );
				return $this->view( '403', [ 'message' => 'You can only browse your own recipes and create them on a user profile' ] );
			}

			$recipeParams = [];

			if ( isset( $_GET['filter'] ) && $_GET['filter'] !== '' ) {
				$recipeParams = [ 
					"title" => "%" . $_GET['filter'] . "%",
					"ingredients" => "%" . $_GET['filter'] . "%",
					"instructions" => "%" . $_GET['filter'] . "%"
				];
			}

			$recipeConditions = [ 'profileId' => $this->profile['id'] ];
			$dietaryType = ( $_GET['dietary'] ?? '' ) == 'none' ? null : $_GET['dietary'] ?? $this->profile['user']['dietaryType'];
			if ( $dietaryType ) {
				$recipeConditions = [ ...$recipeConditions, 'dietaryType' => $dietaryType ];
			}

			[ $currentPage, $totalPages, $recipes ] = getPaginationData(
				$recipeModel,
				$this->itemsPerPage,
				$recipeConditions,
				$recipeParams
			);

			$recipes = array_map(
				function ($recipe) {
					$commentModel = new Comment();
					$comments = $commentModel->findAll( [ 'recipeId' => $recipe['id'] ] );
					$numComments = count( $comments ) > 0 ? count( $comments ) : 1;
					$averageRating = array_reduce( $comments, fn( $carry, $comment ) => $carry + $comment['rating'], 0 ) / $numComments;
					return [ ...$recipe, "rating" => round( $averageRating, 0 ) ];
				},
				$recipes );

			return $this->view(
				'recipes/recipes',
				[ 
					'browse' => false,
					'recipes' => $recipes,
					'dietaryType' => $dietaryType,
					'currentPage' => $currentPage,
					'totalPages' => $totalPages,
				]
			);
		}

		if ( ! is_numeric( $id ) ) {
			http_response_code( 404 );
			return $this->view( '404' );
		}

		// Detailed recipe page are controlled below
		$recipe = $recipeModel->findById( $id, true );
		if ( ! $recipe ) {
			http_response_code( 404 );
			return $this->view( '404' );
		}

		if ( ! $recipe['public'] && $recipe['profileId'] !== $this->profile['id'] && ! $this->profile['user']['isAdmin'] ) {
			http_response_code( 403 );
			return $this->view( '403', [ 'message' => 'This recipe is private and cannot be accessed' ] );
		}

		$commentModel = new Comment();
		$comments = $commentModel->findAll( [ 'recipeId' => $recipe['id'] ], join: true );

		$cookbookModel = new Cookbook();
		$cookbooks = $cookbookModel->findAll( $this->profile['user']['isAdmin'] ? [] : [ 'profileId' => $this->profile['id'] ] );

		$cookbookJoinModel = new CookbookJoin();
		$join = $cookbookJoinModel->findAll( [ 'recipeId' => $recipe['id'] ] );
		$savedCookbooks = array_unique( array_map( fn( $j ) => $j['cookbookId'], $join ) );

		$this->view(
			'recipes/recipe-detail',
			[ 
				'recipe' => $recipe,
				'comments' => $comments,
				'cookbooks' => $cookbooks,
				'savedCookbooks' => $savedCookbooks,
				'commentErrors' => $_SESSION['commentErrors'] ?? [],
				'recipeErrors' => $_SESSION['recipeErrors'] ?? [],
				'saveToCookbook' => $_SESSION['saveToCookbook'] ?? [],
				'profile' => $this->profile
			]
		);
		unset( $_SESSION['commentErrors'] );
		unset( $_SESSION['recipeErrors'] );
		unset( $_SESSION['saveToCookbook'] );
	}

	public function browse() {
		$recipeModel = new Recipe();

		$recipeParams = [];

		if ( isset( $_GET['filter'] ) && $_GET['filter'] !== '' ) {
			$recipeParams = [ 
				"title" => "%" . $_GET['filter'] . "%",
				"ingredients" => "%" . $_GET['filter'] . "%",
				"instructions" => "%" . $_GET['filter'] . "%"
			];
		}

		$recipeConditions = $this->profile['user']['isAdmin'] ? [] : [ 'public' => 1 ];
		$dietaryType = ( $_GET['dietary'] ?? '' ) == 'none' ? null : $_GET['dietary'] ?? $this->profile['user']['dietaryType'];
		if ( $dietaryType ) {
			$recipeConditions = [ ...$recipeConditions, 'dietaryType' => $dietaryType ];
		}

		[ $currentPage, $totalPages, $recipes ] = getPaginationData(
			$recipeModel,
			$this->itemsPerPage,
			$recipeConditions,
			$recipeParams
		);

		$recipes = array_map(
			function ($recipe) {
				$commentModel = new Comment();
				$comments = $commentModel->findAll( [ 'recipeId' => $recipe['id'] ] );
				$numComments = count( $comments ) > 0 ? count( $comments ) : 1;
				$averageRating = array_reduce( $comments, fn( $carry, $comment ) => $carry + $comment['rating'], 0 ) / $numComments;
				return [ ...$recipe, "rating" => round( $averageRating, 0 ) ];
			},
			$recipes );

		return $this->view(
			'recipes/recipes',
			[ 
				'browse' => true,
				'recipes' => $recipes,
				'dietaryType' => $dietaryType,
				'currentPage' => $currentPage,
				'totalPages' => $totalPages,
			]
		);
	}

	private function formatIngredients( $amounts, $units, $ingredients ) {
		$ingredientList = [];

		if ( ! $amounts || ! $units || ! $ingredients ) {
			return $ingredientList;
		}

		foreach ( $ingredients as $index => $ingredient ) {
			$ingredientList[] = [ 
				'ingredient' => $ingredient,
				'unit' => $units[ $index ],
				'amount' => $amounts[ $index ]
			];
		}

		return $ingredientList;
	}

	private function handleErrors( $errors, $action, $id = null ) {
		if ( count( $errors ) > 0 ) {
			$ingredientList = $this->formatIngredients( $_POST['amounts'] ?? null, $_POST['units'] ?? null, $_POST['ingredients'] ?? null );
			$_POST['ingredients'] = $ingredientList;

			if ( $id ) {
				$_POST['id'] = $id;
			}

			http_response_code( 400 );
			$this->view(
				'recipes/recipe-editor',
				[ 
					'action' => $action,
					'errors' => $errors,
					'data' => $_POST
				] );
			die;
		}
	}

	/**
	 * Controller for create recipe page
	 * @return void
	 */
	public function create() {
		if ( $this->profile['user']['isAdmin'] && $this->profile['type'] !== PROFILE_TYPES['user'] ) {
			http_response_code( 403 );
			return $this->view( '403', [ 'message' => 'You can only browse your own recipes and create them on a user profile' ] );
		}

		if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
			$recipeModel = new Recipe();

			$errors = $recipeModel->validate( array_merge( $_POST, $_FILES ) );
			$this->handleErrors( $errors, 'Create' );

			$newRecipe = [ 
				'profileId' => $this->profile['id'],
				'title' => $_POST['title'],
				'prepTime' => empty( $_POST['prepTime'] ) ? null : $_POST['prepTime'],
				'waitingTime' => empty( $_POST['waitingTime'] ) ? null : $_POST['waitingTime'],
				'servings' => empty( $_POST['servings'] ) ? null : $_POST['servings'],
				'public' => $_POST['public'] == 'yes' ? 1 : 0,
				'dietaryType' => $_POST['dietaryType'] == 'none' ? null : $_POST['dietaryType'],
				'instructions' => $_POST['instructions'],
			];

			if ( $_FILES['thumbnail']['error'] == UPLOAD_ERR_OK ) {
				$tmp_name = $_FILES['thumbnail']['tmp_name'];
				$name = basename( $_FILES['thumbnail']['name'] );
				$newRecipe['thumbnail'] = uploadFile( 'thumbnails', $tmp_name, $name );
			}

			$ingredientList = $this->formatIngredients( $_POST['amounts'], $_POST['units'], $_POST['ingredients'] );
			$newRecipe['ingredients'] = json_encode( $ingredientList );

			$recipe = $recipeModel->create( $newRecipe );
			redirect( 'recipes/' . $recipe['id'] );
		}

		$this->view( 'recipes/recipe-editor', [ 'action' => 'Create' ] );
	}

	/**
	 * Controller for edit recipe page
	 * @param string $id - id of the recipe provided in the URL
	 * @return void
	 */
	public function edit( string $id = '' ) {
		if ( ! $id ) {
			redirect( 'recipes' );
		}

		if ( ! is_numeric( $id ) ) {
			http_response_code( 404 );
			return $this->view( '404' );
		}

		$recipeModel = new Recipe();
		$recipe = $recipeModel->findById( $id, true );

		if ( ! $recipe ) {
			http_response_code( 404 );
			return $this->view( '404' );
		}

		if ( $recipe['profileId'] != $this->profile['id'] && ! $this->profile['user']['isAdmin'] ) {
			http_response_code( 403 );
			return $this->view( '403', [ 'message' => 'You do not have permissions to edit this recipe' ] );
		}

		if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
			$errors = $recipeModel->validate( array_merge( $_POST, $_FILES ) );
			$this->handleErrors( $errors, 'Edit', $id );

			$recipeData = [ 
				'title' => $_POST['title'],
				'prepTime' => empty( $_POST['prepTime'] ) ? null : $_POST['prepTime'],
				'waitingTime' => empty( $_POST['waitingTime'] ) ? null : $_POST['waitingTime'],
				'servings' => empty( $_POST['servings'] ) ? null : $_POST['servings'],
				'public' => $_POST['public'] == 'yes' ? 1 : 0,
				'dietaryType' => $_POST['dietaryType'] == 'none' ? null : $_POST['dietaryType'],
				'instructions' => $_POST['instructions'],
			];

			$ingredientList = $this->formatIngredients( $_POST['amounts'], $_POST['units'], $_POST['ingredients'] );
			$recipeData['ingredients'] = json_encode( $ingredientList );

			if ( $_FILES['thumbnail']['error'] == UPLOAD_ERR_OK ) {
				$tmp_name = $_FILES['thumbnail']['tmp_name'];
				$name = basename( $_FILES['thumbnail']['name'] );
				$recipeData['thumbnail'] = uploadFile( 'thumbnails', $tmp_name, $name );
			}

			if ( empty( $_FILES['thumbnail'] ) ) {
				$recipeData['thumbnail'] = '';
			}

			$success = $recipeModel->update( $id, $recipeData );
			if ( ! $success ) {
				http_response_code( 500 );
				$recipe['ingredients'] = json_decode( $recipe['ingredients'], true );
				$this->view(
					'recipes/recipe-editor',
					[ 
						'action' => 'Edit',
						'data' => $recipe,
						'errors' => [ 'Something went wrong updating the recipe and could not be saved' ]
					]
				);
			}

			redirect( "recipes/$id" );
		}

		$recipe['ingredients'] = json_decode( $recipe['ingredients'], true );
		$recipe['public'] = $recipe['public'] ? 'yes' : 'no';
		$this->view(
			'recipes/recipe-editor',
			[ 
				'action' => 'Edit',
				'data' => $recipe
			]
		);
	}

	public function delete() {
		if ( $_SERVER['REQUEST_METHOD'] == 'GET' ) {
			redirect( 'recipes' );
		}

		if ( empty( $_POST['recipeId'] ) || ! is_numeric( $_POST['recipeId'] ) ) {
			http_response_code( 400 );
			redirect( 'recipes' );
		}

		$recipeModel = new Recipe();
		$recipeId = $_POST['recipeId'];

		$recipe = $recipeModel->findById( $recipeId );
		if ( ! $recipe ) {
			http_response_code( 404 );
			redirect( '404' );
		}

		if ( $recipe['profileId'] !== $this->profile['id'] && ! $this->profile['user']['isAdmin'] ) {
			http_response_code( 403 );
			return $this->view( '403', [ 'message' => 'You cannot delete a recipe that you do not own' ] );
		}

		$success = $recipeModel->delete( $recipeId );

		if ( $success ) {
			redirect( 'recipes' );
		}

		http_response_code( 500 );
		$_SESSION['recipeErrors'] = [ 'Recipe could not be deleted' ];
		redirect( "recipes/$recipeId" );
	}

	public function comment( string $method = null ) {
		if ( $_SERVER['REQUEST_METHOD'] == 'GET' ) {
			redirect( 'recipes' );
		}

		switch ( strtolower( $method ) ) {
			case 'add': {
				$commentModel = new Comment();
				$errors = $commentModel->validate( $_POST );

				if ( count( $errors ) > 0 ) {
					http_response_code( 400 );

					if ( isset( $errors['recipeId'] ) ) {
						redirect( 'recipes' );
					}

					$_SESSION['commentErrors'] = $errors;
					redirect( 'recipes/' . $_POST['recipeId'] . '#comments' );
				}

				$recipeId = $_POST['recipeId'];
				$recipeModel = new Recipe();
				$recipe = $recipeModel->findById( $recipeId, true );

				if ( ! $recipe ) {
					http_response_code( 404 );
					redirect( '404' );
				}

				$newComment = $commentModel->createComment(
					[ ...$_POST, 'recipeId' => $recipe['id'], 'profileId' => $this->profile['id'] ],
					$this->profile,
					$recipe
				);
				redirect( "recipes/$recipeId#comment-" . $newComment['id'] );
				break;
			}
			case 'edit': {
				$commentModel = new Comment();
				$errors = $commentModel->hasProvidedId( $_POST );

				if ( count( $errors ) > 0 ) {
					http_response_code( 400 );

					if ( isset( $errors['recipeId'] ) ) {
						redirect( 'recipes' );
					}


					$_SESSION['commentErrors'] = $errors;
					redirect( 'recipes/' . $_POST['recipeId'] . '#comments' );
				}

				$recipeId = $_POST['recipeId'];
				$commentId = $_POST['commentId'];

				$errors = $commentModel->hasProvidedContent( $_POST );
				if ( count( $errors ) > 0 ) {
					http_response_code( 400 );
					$_SESSION['commentErrors'] = $errors;
					redirect( "recipes/$recipeId#comment-$commentId" );
				}

				$comment = $commentModel->findById( $commentId );
				if ( ! $comment ) {
					http_response_code( 404 );
					redirect( '404' );
				}

				if ( $comment['profileId'] !== $this->profile['id'] && ! $this->profile['user']['isAdmin'] ) {
					http_response_code( 403 );
					return $this->view( '403', [ 'message' => 'You cannot edit a comment that you did not make' ] );
				}

				$commentModel->update( $commentId, $_POST );
				redirect( "recipes/$recipeId#comment-$commentId" );
				break;
			}
			case 'delete': {
				$commentModel = new Comment();
				$errors = $commentModel->hasProvidedId( $_POST );

				if ( count( $errors ) > 0 ) {
					http_response_code( 400 );

					if ( isset( $errors['recipeId'] ) ) {
						redirect( 'recipes' );
					}

					$_SESSION['commentErrors'] = $errors;
					redirect( 'recipes/' . $_POST['recipeId'] . '#comments' );
				}

				$recipeId = $_POST['recipeId'];
				$commentId = $_POST['commentId'];

				$comment = $commentModel->findById( $commentId );
				if ( ! $comment ) {
					http_response_code( 404 );
					redirect( '404' );
				}

				if ( $comment['profileId'] !== $this->profile['id'] && ! $this->profile['user']['isAdmin'] ) {
					http_response_code( 403 );
					return $this->view( '403', [ 'message' => 'You cannot delete a comment that you did not make' ] );
				}

				$commentModel->delete( $commentId );
				redirect( "recipes/$recipeId" );
				break;
			}
			default: {
				redirect( 'recipes' );
			}
		}
	}

	// Update cookbooks
	public function updateCookbooks() {
		if ( $_SERVER['REQUEST_METHOD'] !== 'POST' ) {
			redirect( 'recipes' );
		}

		if ( empty( $_POST['recipeId'] ) || ! is_numeric( $_POST['recipeId'] ) ) {
			http_response_code( 400 );
			redirect( 'recipes' );
		}

		$recipeModel = new Recipe();
		$recipeId = $_POST['recipeId'];

		$recipe = $recipeModel->findById( $recipeId );
		if ( ! $recipe ) {
			http_response_code( 404 );
			redirect( '404' );
		}

		$cookbookModel = new Cookbook();
		$cookbooks = array_map(
			fn( $c ) => $cookbookModel->findOne(
				$this->profile['user']['isAdmin'] ? [ 'id' => $c ] : [ 'id' => $c, 'profileId' => $this->profile['id'] ]
			),
			$_POST['cookbooks'] ?? []
		);

		$cookbookJoinModel = new CookbookJoin();
		$joins = $cookbookJoinModel->findAll( [ 'recipeId' => $recipeId ] );

		$removeFromIds = array_diff(
			array_map( fn( $j ) => $j['cookbookId'], $joins ),
			array_map( fn( $c ) => $c['id'], $cookbooks )
		);

		foreach ( $removeFromIds as $cookbookId ) {
			$join = $cookbookJoinModel->findOne( [ 'cookbookId' => $cookbookId, 'recipeId' => $recipe['id'] ] );
			$cookbookJoinModel->delete( $join['id'] );
		}

		foreach ( $cookbooks as $cookbook ) {
			$join = $cookbookJoinModel->findOne( [ 'cookbookId' => $cookbook['id'], 'recipeId' => $recipe['id'] ] );
			if ( ! $join ) {
				$cookbookJoinModel->create( [ 'cookbookId' => $cookbook['id'], 'recipeId' => $recipe['id'] ] );
			}
		}

		$_SESSION['saveToCookbook'] = 'Successfully updated cookbook(s)';
		redirect( 'recipes/' . $recipe['id'] );
	}
}