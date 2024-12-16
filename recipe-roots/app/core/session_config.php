<?php

date_default_timezone_set( 'Asia/Kuala_Lumpur' );
ini_set( 'session.use_only_cookies', 1 );
ini_set( 'session.use_strict_mode', 1 );

session_set_cookie_params( [ 
	'lifetime' => 0,					// Sessions are deleted when a browser is closed
	'path' => '/',            // Accessible across the entire domain
	'domain' => DOMAIN,				// Available to the domain only
	'secure' => true,         // Only sent over HTTPS
	'httponly' => true        // Inaccessible to JavaScript
] );

session_start();

session_regenerate_id( true );

// Enable CORS for the domain only so other websites cannot make AJAX request to the server
header( 'Access-Control-Allow-Origin: ' . ROOT );

// Generate a token to prevent CSRF attacks
$_SESSION['csrfToken'] ??= bin2hex( random_bytes( 32 ) );

// If the profile token is set in the cookie, we can assume they are signed in from the "remember me" option
$_SESSION['profile'] ??= isset( $_COOKIE['profile'] )
	? ( new Session() )->findOne( [ 'sessionId' => $_COOKIE['profile'] ], join: true )['profile'] ?? null
	: null;
