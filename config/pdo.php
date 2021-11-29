<?php 

// $host = "multidigitos.culbrckcaj13.us-west-2.rds.amazonaws.com";
$host = "db-multidigitos-aurora-instance-1.culbrckcaj13.us-west-2.rds.amazonaws.com";
$dbname = "cuotta";
$username = "lcruz";
$password = "Soporte1*";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
} catch (PDOException $e) {
    die("Fail" . $e->getMessage());
}
