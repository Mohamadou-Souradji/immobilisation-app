<?php
require_once('../config/dbconnect.php');

// Récupérer la liste des immobilisations avec des jointures pour obtenir les noms des fournisseurs, familles et emplacements
$query = $db->query("
SELECT i.id_immobilisation, i.intitule, i.valeur_acquision, i.date_acquision, i.date_actif, e.lieu, fa.nom_famille, fo.nom_fournisseur 
FROM immobilisation i, emplacement e, famille_immobilisation fa, compte_fournisseur fo 
WHERE e.id_emplacement = i.id_emplacement 
AND fa.id_famille = i.id_famille 
AND i.id_fournisseur = fo.id_fournisseur 
AND cedee = False;
");
$listeImmobilisations = $query->fetchAll(PDO::FETCH_OBJ);

// Définir l'en-tête pour le fichier CSV
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=immobilisations.csv');

// Créer un flux de sortie
$output = fopen('php://output', 'w');

// Écrire l'en-tête du fichier CSV
fputcsv($output, ['Code', 'Intitulé', 'Valeur', 'Date d\'acquisition', 'Fournisseur', 'Famille', 'Lieu']);

// Écrire les données dans le fichier CSV
foreach ($listeImmobilisations as $immobilisation) {
    fputcsv($output, [
        $immobilisation->id_immobilisation,
        $immobilisation->intitule,
        $immobilisation->valeur_acquision,
        $immobilisation->date_acquision,
        $immobilisation->nom_fournisseur,
        $immobilisation->nom_famille,
        $immobilisation->lieu
    ]);
}

// Fermer le flux de sortie
fclose($output);
exit();
?>
