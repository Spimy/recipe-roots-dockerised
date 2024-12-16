<?php

trait Database {
	private function connect() {
		$connectionString = "mysql:host=" . DBHOST;

		$connection = new PDO( $connectionString, DBUSER, DBPASS );
		$connection->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

		$tz = ( new DateTime( 'now', new DateTimeZone( 'Asia/Kuala_Lumpur' ) ) )->format( 'P' );
		$connection->exec( "SET time_zone='$tz';" );

		$connection->query( "CREATE DATABASE IF NOT EXISTS `" . DBNAME . "`" );
		$connection->query( "use " . DBNAME );

		return $connection;
	}

	/**
	 * Utility method for creating prepared statements when querying the database.
	 * @param string $query - the SQL query with placeholders
	 * @param array $data - the data to be inserted into the placeholders
	 * @return array
	 */
	public function query( string $query, array $data = [] ) {
		$connection = $this->connect();
		$statement = $connection->prepare( $query );
		$success = $statement->execute( $data );

		if ( $success ) {
			$result = $statement->fetchAll( PDO::FETCH_ASSOC );
			if ( is_array( $result ) && count( $result ) > 0 ) {
				return [ 
					'result' => $result,
					'connection' => $connection
				];
			}
		}

		return [ 
			'result' => [],
			'connection' => $connection
		];
	}
}