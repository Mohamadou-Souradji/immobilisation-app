<?php
require_once('../config/dbconnect.php');
require_once('../vendor/autoload.php'); // Inclure Composer autoload si vous utilisez TCPDF via Composer

$pdf = new \TCPDF(); // Utilisation du nom complet de la classe sans 'use'

// Créer un nouveau document PDF
$pdf = new TCPDF();

// Ajouter une page
$pdf->AddPage();

// Définir le titre du document
$pdf->SetTitle('Liste des Immobilisations');

// Définir la police
$pdf->SetFont('helvetica', '', 12);

if (isset($_POST['id'])) {
    $id_immobilisation = $_POST['id'];

    // Récupérer les détails de l'immobilisation
    $query = $db->prepare("
        SELECT 
            i.id_immobilisation, 
            i.intitule, 
            i.valeur_acquision, 
            i.date_acquision, 
            f.type_immobilisation, 
            f.duree_vie, 
            f.coefficient, 
            f.taux,
            a.type 
        FROM 
            immobilisation i,
            famille_immobilisation f, 
            amortissement a 
        WHERE 
            i.id_famille = f.id_famille 
            AND a.id_amortissement=f.id_amortissement
            AND i.id_immobilisation = :id_immobilisation
    ");
    $query->execute(['id_immobilisation' => $id_immobilisation]);
    $immobilisation = $query->fetch(PDO::FETCH_OBJ);

    if ($immobilisation) {
        // Calculer le plan d'amortissement
        $plan_amortissement = [];
        $valeur_acquisition = $immobilisation->valeur_acquision;
        $date_acquisition = new DateTime($immobilisation->date_acquision);
        $duree_vie = $immobilisation->duree_vie;
        $taux = $immobilisation->taux;
        $type = $immobilisation->type;
        $coefficient = $immobilisation->coefficient;

        $jour_acquisition = (int) $date_acquisition->format('z') + 1;
        $prorata_temporaire = (360 - $jour_acquisition + 1) / 360;

        if ($type == 'Lineaire') {
            $taux_annuel = $taux / 100;
            $amortissement_premiere_annee = $valeur_acquisition * $taux_annuel * $prorata_temporaire;

            $valeur_debut_annee = $valeur_acquisition;
            $valeur_fin_annee = $valeur_debut_annee - $amortissement_premiere_annee;

            $plan_amortissement[] = [
                'annee' => $date_acquisition->format('Y'),
                'valeur_debut' => $valeur_debut_annee,
                'montant' => $amortissement_premiere_annee,
                'valeur_fin' => $valeur_fin_annee
            ];

            for ($i = 1; $i < $duree_vie; $i++) {
                $valeur_debut_annee = $valeur_fin_annee;
                $amortissement_annuel = $valeur_acquisition * $taux_annuel;
                $valeur_fin_annee = $valeur_debut_annee - $amortissement_annuel;
                $plan_amortissement[] = [
                    'annee' => $date_acquisition->format('Y') + $i,
                    'valeur_debut' => $valeur_debut_annee,
                    'montant' => $amortissement_annuel,
                    'valeur_fin' => $valeur_fin_annee
                ];
            }

            $valeur_debut_annee = $valeur_fin_annee;
            $amortissement_derniere_annee = $valeur_debut_annee;
            $valeur_fin_annee = 0;

            $plan_amortissement[] = [
                'annee' => $date_acquisition->format('Y') + $duree_vie,
                'valeur_debut' => $valeur_debut_annee,
                'montant' => $amortissement_derniere_annee,
                'valeur_fin' => $valeur_fin_annee
            ];
        } elseif ($type == 'Degressif') {
            $taux_degressif = $taux * $coefficient / 100;
            $valeur_residuelle = $valeur_acquisition;

            for ($i = 0; $i < $duree_vie; $i++) {
                $valeur_debut_annee = $valeur_residuelle;
                $amortissement_annuel = $valeur_residuelle * $taux_degressif;

                $taux_lineaire_restant = (1 / ($duree_vie - $i)) * 100;
                if ($taux_lineaire_restant > $taux_degressif * 100) {
                    $amortissement_annuel = $valeur_residuelle * ($taux / 100);
                }

                if ($i == $duree_vie - 1) {
                    $amortissement_annuel = $valeur_residuelle;
                    $valeur_fin_annee = 0;
                } else {
                    $valeur_fin_annee = $valeur_debut_annee - $amortissement_annuel;
                }

                $plan_amortissement[] = [
                    'annee' => $date_acquisition->format('Y') + $i,
                    'valeur_debut' => $valeur_debut_annee,
                    'montant' => $amortissement_annuel,
                    'valeur_fin' => $valeur_fin_annee
                ];

                $valeur_residuelle -= $amortissement_annuel;
            }
        }

        // Générer le contenu HTML pour le PDF
        $html = '<h1>Plan d\'amortissement</h1>';
        $html .= '<table border="1" cellpadding="5">';
        $html .= '<thead><tr>
                    <th>Année</th>
                    <th>Valeur début année</th>
                    <th>Montant Amorti</th>
                    <th>Valeur fin année</th>
                </tr></thead>';
        $html .= '<tbody>';
        foreach ($plan_amortissement as $annee) {
            $html .= '<tr>
                        <td>' . $annee['annee'] . '</td>
                        <td>' . number_format($annee['valeur_debut'], 2) . '</td>
                        <td>' . number_format($annee['montant'], 2) . '</td>
                        <td>' . number_format($annee['valeur_fin'], 2) . '</td>
                      </tr>';
        }
        $html .= '</tbody></table>';

        // Ajouter le contenu au document
        $pdf->writeHTML($html);

        // Fermer et générer le document PDF
        $pdf->Output('plan amortissement.pdf', 'D');
    }
}
?>
