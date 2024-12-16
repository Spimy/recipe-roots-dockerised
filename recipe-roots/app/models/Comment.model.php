<?php

class Comment extends Model {
	protected $profileId;
	protected $recipeId;
	protected $content;
	protected $rating;

	public function __construct() {
		$this->profileId = $this->foreignKey( new Profile(), true );
		$this->recipeId = $this->foreignKey( new Recipe(), true );
		$this->content = $this->textField();
		$this->rating = $this->integerField();
		parent::__construct();
	}

	public function validate( $data ) {
		return array_merge( $this->hasProvidedRecipeId( $data ), $this->hasProvidedContent( $data ) );
	}

	public function hasProvidedRecipeId( $data ) {
		$errors = [];

		if ( empty( $data['recipeId'] ) ) {
			$errors['recipeId'] = 'No recipe id has been provided';
		}

		if ( ! is_numeric( $data['recipeId'] ) ) {
			$errors['recipeId'] = 'Invalid recipe id provided';
		}

		return $errors;
	}

	public function hasProvidedId( $data ) {
		$errors = [];

		if ( empty( $data['commentId'] ) ) {
			$errors['commentId'] = 'No comment id has been provided';
		}

		if ( ! is_numeric( $data['commentId'] ) ) {
			$errors['commentId'] = 'Invalid comment id provided';
		}

		return array_merge( $errors, $this->hasProvidedRecipeId( $data ) );
	}

	public function hasProvidedContent( $data ) {
		$errors = [];

		if ( empty( $data['content'] ) ) {
			$errors['content'] = 'Your comment has no content';
		}

		if ( empty( $data['rating'] ) ) {
			$errors['rating'] = 'A rating is required';
		}

		if ( ! is_numeric( $data['rating'] ) ) {
			$errors['rating'] = 'Invalid rating value';
		}

		return $errors;
	}

	// Apparently PHP cannot do method overloading...
	public function createComment( array $data, $commenter, $recipe ) {
		$comment = parent::create( $data );

		// Only create a notification if someone else commented
		if ( $commenter['id'] != $recipe['profile']['id'] ) {
			$notificationModel = new Notification();
			$notificationModel->create( [ 
				'senderId' => $commenter['id'],
				'receiverId' => $recipe['profile']['id'],
				'message' => "{bold}" . $commenter['username'] . '{/bold} left a comment and a rating on your recipe {bold}{link}' . $recipe['title'] . '{/link}{/bold}',
				'link' => ROOT . '/recipes/' . $recipe['id'] . '#comment-' . $comment['id']
			] );
		}

		return $comment;
	}
}