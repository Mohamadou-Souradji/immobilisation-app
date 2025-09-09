<?php
require_once('../config/dbconnect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_immobilisation = $_POST['id_immobilisation'];
    $date_cession = $_POST['date_cession'];
    $prix_vente = $_POST['prix_vente'];
    $autres_infos = $_POST['autres_infos'];

    // Insérer la cession dans la table cession
    $query = $db->prepare("INSERT INTO cession (id_immobilisation, date_cession, prix_vente, autres_infos) VALUES (:id_immobilisation, :date_cession, :prix_vente, :autres_infos)");
    $query->execute([
        'id_immobilisation' => $id_immobilisation,
        'date_cession' => $date_cession,
        'prix_vente' => $prix_vente,
        'autres_infos' => $autres_infos
    ]);

    // Mettre à jour l'immobilisation pour indiquer qu'elle a été cédée
    $query = $db->prepare("UPDATE immobilisation SET cedee = TRUE WHERE id_immobilisation = :id_immobilisation");
    $query->execute(['id_immobilisation' => $id_immobilisation]);

    // Redirection vers la liste des cessions
    header("Location: cessionlist.php");
    exit;
}
?>