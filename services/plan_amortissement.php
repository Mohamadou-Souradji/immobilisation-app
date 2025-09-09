<?php
require_once('../config/dbconnect.php');

// Vérifier si l'ID de l'immobilisation est passé en paramètre GET
if(isset($_GET['id'])){
    $id_immobilisation = $_GET['id'];

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
        $type_amortissement = $immobilisation->type_immobilisation;
        $duree_vie = $immobilisation->duree_vie;
        $coefficient = $immobilisation->coefficient;
        $taux = $immobilisation->taux;
        $type = $immobilisation->type;

        // Calcul du prorata temporis pour la première année
        $jour_acquisition = (int) $date_acquisition->format('z') + 1; // Jour de l'année (1-365)
        $prorata_temporaire = (360 - $jour_acquisition + 1) / 360;

        if ($type == 'Lineaire') {
            $taux_annuel = $taux / 100;
            $amortissement_premiere_annee = $valeur_acquisition * $taux_annuel * $prorata_temporaire;

            // Valeurs pour la première année
            $valeur_debut_annee = $valeur_acquisition;
            $valeur_fin_annee = $valeur_debut_annee - $amortissement_premiere_annee;

            // Ajouter l'amortissement de la première année
            $plan_amortissement[] = [
                'annee' => $date_acquisition->format('Y'),
                'valeur_debut' => $valeur_debut_annee,
                'montant' => $amortissement_premiere_annee,
                'valeur_fin' => $valeur_fin_annee,
                'taux' => $taux
            ];

            // Ajouter les amortissements des années pleines
            for ($i = 1; $i < $duree_vie ; $i++) {
                $valeur_debut_annee = $valeur_fin_annee;
                $amortissement_annuel = $valeur_acquisition * $taux_annuel;
                $valeur_fin_annee = $valeur_debut_annee - $amortissement_annuel;
                $plan_amortissement[] = [
                    'annee' => $date_acquisition->format('Y') + $i,
                    'valeur_debut' => $valeur_debut_annee,
                    'montant' => $amortissement_annuel,
                    'valeur_fin' => $valeur_fin_annee,
                    'taux' => $taux
                ];
            }

            // Calcul du prorata temporis pour la dernière année
            $valeur_debut_annee = $valeur_fin_annee;
            $amortissement_derniere_annee = $valeur_debut_annee;
            $valeur_fin_annee = 0;

            // Ajouter l'amortissement de la dernière année
            $plan_amortissement[] = [
                'annee' => $date_acquisition->format('Y') + $duree_vie ,
                'valeur_debut' => $valeur_debut_annee,
                'montant' => $amortissement_annuel-$amortissement_premiere_annee,
                'valeur_fin' => $valeur_fin_annee,
                'taux' => $taux
            ];
        } elseif ($type == 'Degressif') {
            $taux_degressif = $taux * $coefficient / 100;
            $valeur_residuelle = $valeur_acquisition;

            // Ajouter les amortissements dégressifs
            for ($i = 0; $i < $duree_vie; $i++) {
                $valeur_debut_annee = $valeur_residuelle;
                $amortissement_annuel = $valeur_residuelle * $taux_degressif;

                // Vérifier si le taux linéaire restant est supérieur au taux dégressif
                $taux_lineaire_restant = (1 / ($duree_vie - $i)) * 100;
                if ($taux_lineaire_restant > $taux_degressif * 100) {
                    $amortissement_annuel = $valeur_residuelle * ($taux / 100);
                }

                // Si c'est la dernière année, ajuster l'amortissement pour que la valeur finale soit 0
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
                    'valeur_fin' => $valeur_fin_annee,
                    'taux' => $taux_degressif
                ];

                $valeur_residuelle -= $amortissement_annuel;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Plan d'amortissement</title>
    <link href="../css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link href="../css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>
<body class="sb-nav-fixed">
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <a class="navbar-brand ps-3" href="accueil.php"><img src="../img/Logo-Gamma.jpg" alt="" width=200px height=55px ></a>
        <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle"><i class="fas fa-bars"></i></button>
        <form class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0">
            <div class="input-group">
                <input class="form-control" type="text" placeholder="Search for..." aria-label="Search for..." aria-describedby="btnNavbarSearch" />
                <button class="btn btn-primary" id="btnNavbarSearch" type="button"><i class="fas fa-search"></i></button>
            </div>
        </form>
        <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="utilisateur.php">Utilisateurs</a></li>
                        <li><a class="dropdown-item" href="index.php">Deconnexion</a></li>
                        <li><hr class="dropdown-divider" /></li>
                        <li><a class="dropdown-item" href="#!"><i class="fa-solid fa-gear"></i> Parametre </a></li>
                    </ul>
                </li>
            </ul>
    </nav>
    <div id="layoutSidenav">
        <div id="layoutSidenav_nav">
        <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                <div class="sb-sidenav-menu">
                    <div class="nav">
                        
                        <a class="nav-link" href="accueil.php">
                        <div class="sb-nav-link-icon"><i class="fa-solid fa-house"></i></div>
                            Accueil
                        </a>
                        <div class="sb-sidenav-menu-heading">Codification</div>
                        <a class="nav-link" href="famille_immobilisation.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-columns"></i></div>
                            Familles
                        </a>
                        <a class="nav-link" href="fournisseur.php">
                            <div class="sb-nav-link-icon"><i class="fa-solid fa-cart-shopping"></i></div>
                            Fournisseurs
                        </a>
                        <a class="nav-link" href="emplacement.php">
                            <div class="sb-nav-link-icon"><i class="fa-solid fa-location-dot"></i></div>
                            Emplacements
                        </a>
                        <div class="sb-sidenav-menu-heading">Gestion</div>
                        <a class="nav-link" href="immobilisation.php">
                            <div class="sb-nav-link-icon"><i class="fa-solid fa-layer-group"></i></div>
                            Immobilisations
                        </a>                       
                        <a class="nav-link" href="facturelist.php">
                            <div class="sb-nav-link-icon"><i class="fa-solid fa-sheet-plastic"></i></div>
                            facture
                        </a>
                        <a class="nav-link" href="dotation.php">
                            <div class="sb-nav-link-icon"><i class="fa-brands fa-docker"></i></div>
                            Dotations
                        </a>
                        <a class="nav-link" href="cessionlist.php">
                            <div class="sb-nav-link-icon"><i class="fa-solid fa-eraser"></i></div>
                            cession
                        </a>                                
                                               
                    </div>
                </div>
                <div class="sb-sidenav-footer">
                    <a class="nav-link" href="index.php">
                        <div class="sb-nav-link-icon"><i class="fa-solid fa-right-left"></i> Déconnexion</div>
                    </a>
                </div>
            </nav>
        </div>
        <div id="layoutSidenav_content">
            <main>
            <div class="d-flex align-items-center justify-content-center mt-1 ">
                    <form action="telecharger_pdf.php" method="POST">
                            <input type="hidden" name="id" value="<?= $id_immobilisation ?>">
                            <button type="submit" class="btn btn-primary">Télécharger en PDF</button>
                        </form>
                        </div>
                <div class="container-fluid px-4">
                    <h1 class="mt-4">Plan d'amortissement</h1>
                    
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-table me-1"></i>
                            Détails de l'amortissement
                        </div>
                        
                        <div class="card-body">
                                <p><strong>Intitulé :</strong> <?= $immobilisation->intitule ?></p>
                                <p><strong>Valeur d'acquisition :</strong> <?= number_format($immobilisation->valeur_acquision)  ?> fcfa</p>
                                <p><strong>Date d'acquisition :</strong> <?= $immobilisation->date_acquision ?></p>
                                <p><strong>Type d'amortissement :</strong> <?= $immobilisation->type ?></p>
                                <p><strong>Taux:</strong> <?= $immobilisation->taux ?>%</p>
                                <p><strong>coefficient:</strong> <?= $immobilisation->coefficient ?></p>
                                <p><strong>Durée de vie :</strong> <?= $immobilisation->duree_vie ?> ans</p>
                            

                                <table class=" table table-info table-bordered border-primary">
                                    <thead  class="table-dark">
                                        <tr>
                                            <th>Année</th>
                                            <th >Valeur début année</th>
                                            <th>Montant Amorti</th>
                                            <th>Valeur fin année</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($plan_amortissement as $annee): ?>
                                            <tr>
                                                <td><?= $annee['annee']; ?></td>
                                                <td><?= number_format($annee['valeur_debut'], 2); ?></td>
                                                <td class="table-danger"><?= number_format($annee['montant'], 2); ?></td>
                                                <td><?= number_format($annee['valeur_fin'], 2); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                                <canvas id="amortissementChart" width="400" height="200"></canvas>
                            <!-- Ajouter ceci dans votre fichier HTML -->
                                                </div>
                    </div>
                </div>
            </main>
            <footer class="py-4 bg-light mt-auto">
                <div class="container-fluid px-4">
                    <div class="d-flex align-items-center justify-content-between small">
                        <div class="text-muted">Copyright &copy; Gestion des immobilisations 2024</div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="../js/scripts.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var ctx = document.getElementById('amortissementChart').getContext('2d');
            var amortissementChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: <?= json_encode(array_column($plan_amortissement, 'annee')); ?>,
                    datasets: [{
                        label: 'Valeur Début Année',
                        data: <?= json_encode(array_column($plan_amortissement, 'valeur_debut')); ?>,
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1,
                        fill: false
                    }, {
                        label: 'Valeur Fin Année',
                        data: <?= json_encode(array_column($plan_amortissement, 'valeur_fin')); ?>,
                        borderColor: 'rgba(153, 102, 255, 1)',
                        borderWidth: 1,
                        fill: false
                    }]
                },
                options: {
                    scales: {
                        x: {
                            beginAtZero: true
                        },
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>
