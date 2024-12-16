<?php

class Ingredient extends Model {
	protected $farmerId;
	protected $ingredient;
	protected $price;
	protected $unit;
	protected $thumbnail;
	protected $unlisted;

	public function __construct() {
		$this->farmerId = $this->foreignKey( new Profile, true );
		$this->ingredient = $this->charField( 40 );
		$this->price = $this->decimalField( 5, 2 );
		$this->unit = $this->charField( 6 );
		$this->thumbnail = $this->charField( 255, true );
		$this->unlisted = $this->booleanField();
		parent::__construct();
	}

	public function validate( $data ) {
		$errors = [];

		if ( empty( $data['ingredient'] ) ) {
			$errors['ingredient'] = 'Ingredient name is required';
		}

		if ( empty( $data['price'] ) ) {
			$errors['price'] = 'Unit price is required';
		} else {
			if ( ! is_numeric( $data['price'] ) ) {
				$errors['price'] = 'Unit price should be a number';
			} else {
				if ( $data['price'] >= 1000 ) {
					$errors['price'] = 'Unit price is too high';
				}
			}
		}

		if ( empty( $data['unit'] ) ) {
			$errors['unit'] = 'Unit is required';
		} else {
			if ( ! in_array( $data['unit'], INGREDIENT_UNITS ) ) {
				$errors['unit'] = 'Unit must be one of: ' . implode( ', ', INGREDIENT_UNITS );
			}
		}

		$errors = array_merge( $errors, validateUpload( $data['thumbnail'] ) );
		return $errors;
	}
}