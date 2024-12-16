<?php

class App {
	private $controller = 'Home';
	private $method = 'index';

	private function splitURL() {
		$url = $_GET['url'] ?? 'home';
		$url = explode( "/", trim( $url, "/" ) );
		return $url;
	}

	public function loadController() {
		$url = $this->splitURL();
		$this->controller = ucfirst( $url[0] );
		unset( $url[0] );

		$filename = "../app/controllers/{$this->controller}.php";

		if ( ! file_exists( $filename ) ) {
			http_response_code( 404 );
			$this->controller = '_404';
			$filename = "../app/controllers/{$this->controller}.php";
		}

		require $filename;
		$controller = new $this->controller();

		if ( ! empty( $url[1] ) ) {
			if ( method_exists( $controller, $url[1] ) ) {
				$this->method = $url[1];
				unset( $url[1] );
			}
		}

		call_user_func_array( [ $controller, $this->method ], $url );
	}

	public function __construct() {
		// show( $this->splitURL() );
	}
}