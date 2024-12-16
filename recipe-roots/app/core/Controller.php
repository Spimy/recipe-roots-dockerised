<?php

trait Controller {
	public function view( string $view, array $data = [] ) {
		$filename = "../app/views/{$view}.view.php";

		if ( ! file_exists( $filename ) ) {
			http_response_code( 404 );
			$filename = "../app/views/404.view.php";
		}

		if ( ! empty( $data ) ) {
			extract( $data );
		}

		require $filename;
	}
}