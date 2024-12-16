<?php

function show( $output ) {
	echo "<pre>";
	print_r( $output );
	echo "</pre>";
}

function escape( $str ) {
	return htmlspecialchars( $str );
}

function redirect( $path ) {
	header( "Location: " . ROOT . "/" . $path );
	die();
}

function isAuthenticated() {
	return isset( $_SESSION['profile'] );
}

function handleUnauthenticated( string $next ) {
	http_response_code( 401 );
	$_SESSION['require_auth'] = 'You need to be signed in first';
	redirect( "signin?next=$next" );
}

function validateUpload( $file ) {
	$errors = [];

	if ( $file['error'] !== UPLOAD_ERR_NO_FILE ) {
		if ( $file['error'] === UPLOAD_ERR_INI_SIZE ) {
			$errors['file'] = 'File is too large, it should not exceed ' . ini_get( 'upload_max_filesize' ) . 'B';
			return $errors;
		}

		$fileTmpPath = $file['tmp_name'];
		$fileName = $file['name'];

		$allowedExtensions = [ 'jpg', 'jpeg', 'png', 'gif' ];
		$allowedMimeTypes = [ 'image/jpeg', 'image/png', 'image/gif' ];

		$fileExtension = strtolower( pathinfo( $fileName, PATHINFO_EXTENSION ) );
		if ( ! in_array( $fileExtension, $allowedExtensions ) ) {
			$errors['ext'] = 'Invalid file extension. Allowed extensions are: ' . implode( ', ', $allowedExtensions );
		}

		$fileMimeType = mime_content_type( $fileTmpPath );
		if ( ! in_array( $fileMimeType, $allowedMimeTypes ) ) {
			$errors['mime'] = 'Invalid MIME type. Allowed types are: ' . implode( ', ', $allowedMimeTypes );
		}

		$imageSize = @getimagesize( $fileTmpPath );
		if ( $imageSize === false ) {
			$errors['img'] = "The file is not a valid image";
		}
	}

	return $errors;
}

function uploadFile( string $folder, string $tempfile, string $filename ) {
	if ( ! is_dir( '../public/uploads' ) ) {
		mkdir( '../public/uploads' );
	}

	$folderpath = "../public/uploads/$folder";
	if ( ! is_dir( $folderpath ) ) {
		mkdir( $folderpath );
	}

	$name = pathinfo( $filename, PATHINFO_FILENAME );
	$extension = pathinfo( $filename, PATHINFO_EXTENSION );

	$increment = 0;
	$filename = "$name.$extension";
	while ( is_file( "$folderpath/$filename" ) ) {
		$increment++;
		$filename = "$name ($increment).$extension";
	}

	move_uploaded_file( $tempfile, "$folderpath/$filename" );
	return ROOT . "/uploads/$folder/$filename";
}

function extractTitleLetters( string $title ) {
	$titleWords = explode( ' ', $title );
	$firstLetters = '';

	for ( $i = 0; $i < count( $titleWords ); $i++ ) {
		if ( $i == 2 ) {
			break;
		}
		$firstLetters .= strtoupper( $titleWords[ $i ][0] );
	}

	return $firstLetters;
}

function convertToHoursMins( $mins ) {
	if ( $mins < 1 ) {
		return;
	}

	$hours = floor( $mins / 60 );
	$minutes = $mins % 60;

	$format = $hours > 0 ? '%01d hr %02d min' : '%02d min';
	return $hours > 0 ? sprintf( $format, $hours, $minutes ) : sprintf( $format, $minutes );
}

function getPaginationData( Model $model, int $itemsPerPage, array $conditions = [], array $contains = [] ) {
	$currentPage = isset( $_GET['page'] ) && is_numeric( $_GET['page'] ) ? (int) $_GET['page'] : 1;
	$offset = ( $currentPage - 1 ) * $itemsPerPage;

	$totalData = count( $model->findAll( $conditions, contain: $contains ) );
	$totalPages = ceil( $totalData / $itemsPerPage );
	$totalPages = $totalPages == 0 ? 1 : $totalPages;

	if ( $currentPage > $totalPages ) {
		redirect( $_GET['url'] . "?page=$totalPages" );
	}

	$pageData = $model->findAll(
		data: $conditions,
		contain: $contains,
		join: true,
		limit: $itemsPerPage,
		offset: $offset
	);

	return [ $currentPage, $totalPages, $pageData ];
}

function getPaginatorPages( $currentPage, $totalPages ) {
	// Handle edge cases where total pages are less than or equal to 3
	if ( $totalPages <= 3 ) {
		return range( 1, $totalPages );
	}

	// Calculate the range of pages to display
	if ( $currentPage == 1 ) {
		// First page: show 1, 2, 3
		return [ 1, 2, 3 ];
	} elseif ( $currentPage == $totalPages ) {
		// Last page: show last three pages
		return [ $totalPages - 2, $totalPages - 1, $totalPages ];
	} else {
		// Middle pages
		return [ $currentPage - 1, $currentPage, $currentPage + 1 ];
	}
}

function injectCsrfToken() {
	$token = escape( $_SESSION['csrfToken'] );
	echo <<<HTML
		<input type="hidden" name="csrfToken" value="{$token}" />
	HTML;
}

function hasValidCsrfToken() {
	return isset( $_POST['csrfToken'] ) && hash_equals( $_POST['csrfToken'], $_SESSION['csrfToken'] );
}

function handleInvalidCsrfToken( object $controller ) {
	if ( $_SERVER['REQUEST_METHOD'] !== 'POST' ) {
		return;
	}

	if ( hasValidCsrfToken() ) {
		return;
	}

	http_response_code( 403 );
	/** @var Controller $controller */
	$controller->view( '403', [ 'message' => 'No valid CSRF token provided.' ] );
	die;
}
