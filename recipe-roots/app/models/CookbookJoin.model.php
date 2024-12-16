<?php

class CookbookJoin extends Model {
	protected $cookbookId;
	protected $recipeId;

	public function __construct() {
		$this->cookbookId = $this->foreignKey( new Cookbook, true );
		$this->recipeId = $this->foreignKey( new Recipe, true );
		parent::__construct();
	}

	public function validate( $data ) {
		$errors = [];

		if ( empty( $data['cookbookId'] ) ) {
			$errors['cookbookId'] = 'Cookbook id is required';
		}

		if ( ! is_numeric( $data['cookbookId'] ) ) {
			$errors['cookbookId'] = 'Invalid cookbook id';
		}

		if ( empty( $data['recipeId'] ) ) {
			$errors['recipeId'] = 'Recipe id is required';
		}

		if ( ! is_numeric( $data['recipeId'] ) ) {
			$errors['recipeId'] = 'Invalid recipe id';
		}

		return $errors;
	}
}