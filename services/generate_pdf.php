<?php
require_once('../config/dbconnect.php');
require_once('../vendor/autoload.php'); // Inclure Composer autoload si vous utilisez TCPDF via Composer

use TCPDF;

// Créer un nouveau document PDF
$pdf = new TCPDF();

// Ajouter une page
$pdf->AddPage();

// Définir le titre du document
$pdf->SetTitle('Liste des Immobilisations');

// Définir la police
$pdf->SetFont('helvetica', '', 12);

// Récupérer les immobilisations
$query = $db->query("
    SELECT i.id_immobilisation, i.intitule, i.valeur_acquision, i.date_acquision, i.date_actif, e.lieu, fa.nom_famille, fo.nom_fournisseur 
    FROM immobilisation i
    JOIN emplacement e ON e.id_emplacement = i.id_emplacement
    JOIN famille_immobilisation fa ON fa.id_famille = i.id_famille
    JOIN compte_fournisseur fo ON i.id_fournisseur = fo.id_fournisseur
    WHERE cedee = False
");
$listeImmobilisations = $query->fetchAll(PDO::FETCH_OBJ);

// Créer le tableau
$html = '<h1>Liste des Immobilisations</h1>';
$html .= '<table border="1" cellpadding="5">';
$html .= '<thead><tr>
    <th>Code</th>
    <th>Intitulé</th>
    <th>Valeur</th>
    <th>Date d\'acquisition</th>
    <th>Fournisseur</th>
    <th>Famille</th>
    <th>Lieu</th>
</tr></thead>';
$html .= '<tbody>';
foreach ($listeImmobilisations as $immobilisation) {
    $html .= '<tr>
        <td>' . $immobilisation->id_immobilisation . '</td>
        <td>' . $immobilisation->intitule . '</td>
        <td>' . $immobilisation->valeur_acquision . '</td>
        <td>' . $immobilisation->date_acquision . '</td>
        <td>' . $immobilisation->nom_fournisseur . '</td>
        <td>' . $immobilisation->nom_famille . '</td>
        <td>' . $immobilisation->lieu . '</td>
    </tr>';
}
$html .= '</tbody>';
$html .= '</table>';

// Ajouter le contenu au document
$pdf->writeHTML($html);

// Fermer et générer le document PDF
$pdf->Output('immobilisations.pdf', 'D');
?>
