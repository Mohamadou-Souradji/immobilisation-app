<?php
require __DIR__ . '/../vendor/autoload.php'; // Chemin relatif vers le fichier autoload.php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Récupérer la liste des immobilisations avec des jointures pour obtenir les noms des fournisseurs, familles et emplacements
require_once('../config/dbconnect.php');

$query = $db->query("
SELECT i.id_immobilisation, i.intitule, i.valeur_acquision, i.date_acquision, i.date_actif, e.lieu, fa.nom_famille, fo.nom_fournisseur 
FROM immobilisation i, emplacement e, famille_immobilisation fa, compte_fournisseur fo 
WHERE e.id_emplacement = i.id_emplacement 
AND fa.id_famille = i.id_famille 
AND i.id_fournisseur = fo.id_fournisseur 
AND cedee = False;
");
$listeImmobilisations = $query->fetchAll(PDO::FETCH_OBJ);

// Créer un nouveau fichier Excel
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Définir l'en-tête du fichier Excel
$sheet->setCellValue('A1', 'Code');
$sheet->setCellValue('B1', 'Intitulé');
$sheet->setCellValue('C1', 'Valeur');
$sheet->setCellValue('D1', 'Date d\'acquisition');
$sheet->setCellValue('E1', 'Fournisseur');
$sheet->setCellValue('F1', 'Famille');
$sheet->setCellValue('G1', 'Lieu');

// Écrire les données dans le fichier Excel
$row = 2;
foreach ($listeImmobilisations as $immobilisation) {
    $sheet->setCellValue('A' . $row, $immobilisation->id_immobilisation);
    $sheet->setCellValue('B' . $row, $immobilisation->intitule);
    $sheet->setCellValue('C' . $row, $immobilisation->valeur_acquision);
    $sheet->setCellValue('D' . $row, $immobilisation->date_acquision);
    $sheet->setCellValue('E' . $row, $immobilisation->nom_fournisseur);
    $sheet->setCellValue('F' . $row, $immobilisation->nom_famille);
    $sheet->setCellValue('G' . $row, $immobilisation->lieu);
    $row++;
}

// Définir l'en-tête pour le fichier Excel
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="immobilisations.xlsx"');
header('Cache-Control: max-age=0');

// Créer un écrivain pour le fichier Excel et l'enregistrer dans le flux de sortie
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit();
?>
