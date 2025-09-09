<?php
$host = "sql310.infinityfree.com"; // fourni par InfinityFree
$user = "if0_39894824";   // ton utilisateur MySQL
$pass = "";
$db   = "if0_39894824_gestion_immobilisation"; // ta base MySQL

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Connexion échouée : " . mysqli_connect_error());
}
?>
