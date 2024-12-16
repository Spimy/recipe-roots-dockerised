<?php

switch ( $_SERVER['SERVER_NAME'] ) {
	case 'localhost':
		/** database config **/
		define( 'DBNAME', 'RecipeRoots' );
		define( 'DBHOST', 'mariadb' );
		define( 'DBUSER', 'root' );
		define( 'DBPASS', 'root' );
		define( 'DBDRIVER', '' );

		define( 'DOMAIN', 'localhost' );
		define( 'ROOT', 'http://' . DOMAIN . '/recipe-roots/public' );
		break;

	default:
		/** database config **/
		define( 'DBNAME', 'RecipeRoots' );
		define( 'DBHOST', 'mariadb' );
		define( 'DBUSER', 'root' );
		define( 'DBPASS', 'root' );
		define( 'DBDRIVER', '' );

		define( 'DOMAIN', 'recipe-roots.spimy.dev' );
		define( 'ROOT', 'https://' . DOMAIN );
		break;
}

// const works here and is recommended since they are defined at compile time
const APP_NAME = "Recipe Roots";
const APP_DESC = "Your Kitchen Assistant";
const DEBUG = true;
