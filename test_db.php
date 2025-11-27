<?php
// Test database connection
$host = '127.0.0.1';
$port = 3306;
$database = 'hospital_management_system';
$username = 'root';
$password = '';
$socket = '/opt/lampp/var/mysql/mysql.sock';

echo "Testing MySQL connection...\n\n";

// Test 1: TCP Connection
echo "Test 1: TCP Connection (127.0.0.1:3306)\n";
try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$database", $username, $password);
    echo "✅ TCP Connection successful!\n\n";
    $pdo = null;
} catch (PDOException $e) {
    echo "❌ TCP Connection failed: " . $e->getMessage() . "\n\n";
}

// Test 2: Socket Connection
echo "Test 2: Socket Connection ($socket)\n";
try {
    $pdo = new PDO("mysql:unix_socket=$socket;dbname=$database", $username, $password);
    echo "✅ Socket Connection successful!\n\n";
    $pdo = null;
} catch (PDOException $e) {
    echo "❌ Socket Connection failed: " . $e->getMessage() . "\n\n";
}

// Test 3: localhost
echo "Test 3: Using 'localhost' as host\n";
try {
    $pdo = new PDO("mysql:host=localhost;dbname=$database", $username, $password);
    echo "✅ Localhost Connection successful!\n\n";
    $pdo = null;
} catch (PDOException $e) {
    echo "❌ Localhost Connection failed: " . $e->getMessage() . "\n\n";
}
