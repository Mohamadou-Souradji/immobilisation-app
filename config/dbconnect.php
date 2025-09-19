<?php
$host = "dpg-d301smfdiees738sr6h0-a";      // ex: dpg-xxxxx.frankfurt.render.com
$port = "5432";
$dbname = "immobilisation_db";    // ex: immobilisation_db
$user = "immobilisation_db_user";             // ex: immobilisation_user
$password = "lp2KNKsCXlpJyWjhIiQfbTeokTmFgtPW";

// Connexion PostgreSQL
$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password sslmode=require");

if (!$conn) {
    die("Erreur de connexion à PostgreSQL : " . pg_last_error());
}
?>
