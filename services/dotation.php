<?php
require_once('../config/dbconnect.php');

// Récupérer toutes les immobilisations
$query = $db->prepare("
    SELECT 
        i.id_immobilisation, 
        i.valeur_acquision, 
        i.date_acquision, 
        f.duree_vie, 
        f.coefficient, 
        f.taux, 
        a.type 
    FROM 
        immobilisation i
        JOIN famille_immobilisation f ON i.id_famille = f.id_famille
        JOIN amortissement a ON f.id_amortissement = a.id_amortissement
");
$query->execute();
$immobilisations = $query->fetchAll(PDO::FETCH_OBJ);

foreach($immobilisations as $immobilisation) {
    $dotations = [];
    $date_acquisition = new DateTime($immobilisation->date_acquision);
    $jour_acquisition = (int) $date_acquisition->format('z') + 1; // Jour de l'année (1-365)
    $prorata_temporaire = (360 - $jour_acquisition + 1) / 360;

    if ($immobilisation->type == 'Lineaire') {
        $taux_annuel = $immobilisation->taux / 100;
        $amortissement_premiere_annee = $immobilisation->valeur_acquision * $taux_annuel * $prorata_temporaire;

        // Valeurs pour la première année
        $valeur_debut_annee = $immobilisation->valeur_acquision;
        $valeur_fin_annee = $valeur_debut_annee - $amortissement_premiere_annee;

        // Ajouter l'amortissement de la première année
        $dotations[] = [
            'id_immobilisation' => $immobilisation->id_immobilisation,
            'annee' => (int) $date_acquisition->format('Y'),
            'montant' => $amortissement_premiere_annee,
            'debut_annee' => $valeur_debut_annee,
            'fin_annee' => $valeur_fin_annee
        ];

        // Ajouter les amortissements des années pleines
        for ($i = 1; $i < $immobilisation->duree_vie ; $i++) {
            $valeur_debut_annee = $valeur_fin_annee;
            $amortissement_annuel = $immobilisation->valeur_acquision * $taux_annuel;
            $valeur_fin_annee = $valeur_debut_annee - $amortissement_annuel;

            $dotations[] = [
                'id_immobilisation' => $immobilisation->id_immobilisation,
                'annee' => (int) $date_acquisition->format('Y') + $i,
                'montant' => $amortissement_annuel,
                'debut_annee' => $valeur_debut_annee,
                'fin_annee' => $valeur_fin_annee
            ];
        }

        // Calcul du prorata temporis pour la dernière année
        $valeur_debut_annee = $valeur_fin_annee;
        $amortissement_derniere_annee = $valeur_debut_annee;
        $valeur_fin_annee = 0;

        // Ajouter l'amortissement de la dernière année
        $dotations[] = [
            'id_immobilisation' => $immobilisation->id_immobilisation,
            'annee' => (int) $date_acquisition->format('Y') + $immobilisation->duree_vie,
            'montant' => $amortissement_derniere_annee,
            'debut_annee' => $valeur_debut_annee,
            'fin_annee' => $valeur_fin_annee
        ];

    } elseif ($immobilisation->type == 'Degressif') {
        $taux_degressif = $immobilisation->taux * $immobilisation->coefficient / 100;
        $valeur_residuelle = $immobilisation->valeur_acquision;

        // Ajouter les amortissements dégressifs
        for ($i = 0; $i < $immobilisation->duree_vie; $i++) {
            $valeur_debut_annee = $valeur_residuelle;
            $amortissement_annuel = $valeur_residuelle * $taux_degressif;

            // Vérifier si le taux linéaire restant est supérieur au taux dégressif
            $taux_lineaire_restant = (1 / ($immobilisation->duree_vie - $i)) * 100;
            if ($taux_lineaire_restant > $taux_degressif * 100) {
                $amortissement_annuel = $valeur_residuelle * ($immobilisation->taux / 100);
            }

            // Si c'est la dernière année, ajuster l'amortissement pour que la valeur finale soit 0
            if ($i == $immobilisation->duree_vie - 1) {
                $amortissement_annuel = $valeur_residuelle;
                $valeur_fin_annee = 0;
            } else {
                $valeur_fin_annee = $valeur_debut_annee - $amortissement_annuel;
            }

            $dotations[] = [
                'id_immobilisation' => $immobilisation->id_immobilisation,
                'annee' => (int) $date_acquisition->format('Y') + $i,
                'montant' => $amortissement_annuel,
                'debut_annee' => $valeur_debut_annee,
                'fin_annee' => $valeur_fin_annee
            ];
            $valeur_residuelle -= $amortissement_annuel;
        }
    }

    // Insérer toutes les dotations calculées dans la base de données
    foreach ($dotations as $dotation) {
        // Vérifier si la dotation existe déjà
        $check_query = $db->prepare("
            SELECT COUNT(*) FROM dotation 
            WHERE id_immobilisation = :id_immobilisation 
            AND annee = :annee
        ");
        $check_query->execute([
            'id_immobilisation' => $dotation['id_immobilisation'],
            'annee' => $dotation['annee']
        ]);
        $count = $check_query->fetchColumn();

        // Si la dotation n'existe pas, l'insérer
        if ($count == 0) {
            $query = $db->prepare("
                INSERT INTO dotation (id_immobilisation, annee, montant, debut_annee, fin_annee)
                VALUES (:id_immobilisation, :annee, :montant, :debut_annee, :fin_annee)
            ");
            $query->execute([
                'id_immobilisation' => $dotation['id_immobilisation'],
                'annee' => $dotation['annee'],
                'montant' => $dotation['montant'],
                'debut_annee' => $dotation['debut_annee'],
                'fin_annee' => $dotation['fin_annee']
            ]);
        }
    }
}

$query = $db->query("
SELECT annee,SUM(montant) as montant_total from dotation d ,immobilisation i WHERE d.id_immobilisation=i.id_immobilisation and cedee=false GROUP by annee

");
$dotation_annuel = $query->fetchAll(PDO::FETCH_OBJ);

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
        <a class="navbar-brand ps-3" href="accueil.php"><img src="../img/Logo-Gamma.jpg" alt="" width=200px height=55px></a>
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
                            Factures
                        </a>
                        <a class="nav-link" href="dotation.php">
                            <div class="sb-nav-link-icon"><i class="fa-brands fa-docker"></i></div>
                            Dotations
                        </a>
                        <a class="nav-link" href="cessionlist.php">
                            <div class="sb-nav-link-icon"><i class="fa-solid fa-eraser"></i></div>
                            Cessions
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
                <div class="container-fluid px-4">
                    <h1 class="mt-4">Plan d'amortissement</h1>
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-table me-1"></i>
                            Détails des dotations
                        </div>
                        <div class="card-body">
                        <table class="table table-success table-striped table-bordered " id="datatablesSimple">
                            <thead>
                                    <tr>
                                        <th>Année</th>
                                        <th>Montant a la dotation</th>
                                        <th>Action</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($dotation_annuel as $dotation): ?>
                                        <tr>
                                            <td><?= $dotation->annee; ?></td>
                                            <td><?= $dotation->montant_total; ?></td>
                                           <td>
                                           <a href="plan_dotation.php?annee=<?= $dotation->annee; ?>" class="btn btn-primary">voir</a>

                                           </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
            <footer class="py-4 bg-light mt-auto">
                <div class="container-fluid px-4">
                    <div class="d-flex align-items-center justify-content-between small">
                        <div class="text-muted">Copyright &copy; Your Website 2024</div>
                        <div>
                            <a href="#">Privacy Policy</a>
                            &middot;
                            <a href="#">Terms &amp; Conditions</a>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="../js/scripts.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
        <script src="../assets/demo/chart-area-demo.js"></script>
        <script src="../assets/demo/chart-bar-demo.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
        <script src="../js/datatables-simple-demo.js"></script>
</body>
</html>
