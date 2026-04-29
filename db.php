<?php
declare(strict_types=1);




// for local testing
$host = 'localhost';
$dbName = 'city_events';
$user = 'root';
$pass = '';

$baseDsn = "mysql:host={$host}";
$dbDsn = "mysql:host={$host};dbname={$dbName}";

$pdoOptions = [
	PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
	PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
	PDO::ATTR_EMULATE_PREPARES => false,
];

try {

	$pdo = new PDO($dbDsn, $user, $pass, $pdoOptions);

	// Create users table
	$pdo->exec(
		'CREATE TABLE IF NOT EXISTS users (
			id INT AUTO_INCREMENT PRIMARY KEY,
			username VARCHAR(255) NOT NULL,
			password VARCHAR(255) NOT NULL
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
	);

	// Create events table
	$pdo->exec(
		'CREATE TABLE IF NOT EXISTS events (
			id INT AUTO_INCREMENT PRIMARY KEY,
			title VARCHAR(255) NOT NULL,
			description TEXT,
			category VARCHAR(255),
			location VARCHAR(255),
			event_date DATE,
			image VARCHAR(255)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
	);

	$userCountStmt = $pdo->query('SELECT COUNT(*) FROM users');
	$userCount = (int) $userCountStmt->fetchColumn();

	if ($userCount === 0) {
		$insertUserStmt = $pdo->prepare('INSERT INTO users (username, password) VALUES (:username, :password)');
		$insertUserStmt->execute([
			'username' => 'admin',
			'password' => 'admin',
		]);
	}
} catch (PDOException $e) {
	exit('Database setup error: ' . $e->getMessage());
}

