<?php

    $host = "localhost";
    $username = "root";
    $password = "";
    $db = "bookstore";


    try {
        $pdo = new PDO("mysql:host=$host;dbname=$db", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        // echo "Successful";
    } catch (PDOException $e) {
        die("Connection Failed: " . $e->getMessage());
    }