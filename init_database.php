<?php

require_once 'vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$dbHost = $_ENV['DB_HOST'];
$dbUsername = $_ENV['DB_USERNAME'];
$dbPassword = $_ENV['DB_PASSWORD'];
$dbDatabase = $_ENV['DB_DATABASE'];

try {
    // connexion pour la supprimer
    $pdo = new \PDO("mysql:host=$dbHost", $dbUsername, $dbPassword);
    $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

    $pdo->exec("DROP DATABASE IF EXISTS $dbDatabase");

    $pdo->exec("CREATE DATABASE IF NOT EXISTS $dbDatabase");

    // reconnexion à la base de données créée
    $pdo = new \PDO("mysql:host=$dbHost;dbname=$dbDatabase", $dbUsername, $dbPassword);
    $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

    $sqlFile = 'db_sql/ntierprojet_database.sql';
    if (file_exists($sqlFile)) {
        $sql = file_get_contents($sqlFile);
        $pdo->exec($sql);
        echo "La base de données a été importée avec succès.\n";
    } else {
        echo "Erreur: Le fichier SQL ($sqlFile) est introuvable.\n";
        exit(1);
    }
} catch (\PDOException $e) {
    echo "Erreur: " . $e->getMessage() . "\n";
    exit(1);
}
