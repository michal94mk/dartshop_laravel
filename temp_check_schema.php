<?php

// Simple script to check database table structure

echo "Checking database schema...\n";

try {
    $pdo = new PDO(
        'mysql:host=localhost;dbname=dartshop_laravel', 
        'root', 
        '',
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    
    echo "Connected to database.\n";
    
    // Check brands table structure
    $stmt = $pdo->query("DESCRIBE brands");
    echo "Brands table structure:\n";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo $row['Field'] . " - " . $row['Type'] . "\n";
    }
    
    echo "\n";
    
    // Check categories table structure
    $stmt = $pdo->query("DESCRIBE categories");
    echo "Categories table structure:\n";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo $row['Field'] . " - " . $row['Type'] . "\n";
    }
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
} 