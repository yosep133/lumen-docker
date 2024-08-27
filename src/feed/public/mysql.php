<?php
$hostname	= "mysql";
$dbname		= "feedservice";
$username	= "user";
$password	= "secret";

try {

	$conn = new PDO( "mysql:host=$hostname;dbname=$dbname", $username, $password );

	// Configure PDO error mode
	$conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

	echo "Connected successfully";
}
catch( PDOException $e ) {

	echo "Failed to connect: " . $e->getMessage();
}

// Perform database operations

// Close the connection
$conn = null;