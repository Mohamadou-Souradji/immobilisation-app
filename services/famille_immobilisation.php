<?php
require_once('../config/dbconnect.php');

// Vérifier si l'ID de la famille d'immobilisation à supprimer est passé en paramètre GET
if(isset($_GET['id_famille'])){
    // Supprimer la famille d'immobilisation correspondante dans la base de données
    $query = $db->prepare('DELETE FROM famille_immobilisation WHERE id_famille=:id_famille');
    $query->execute(['id_famille' => $_GET['id_famille']]);
    
    // Rediriger vers la liste des familles d'immobilisations après la suppression
    header('Location: famille_immobilisation.php');
    exit; // Assurez-vous de terminer le script après la redirection
}
// Récupérer la liste des familles d'immobilisations avec les détails d'amortissement
$query = $db->query("SELECT f.id_famille, f.nom_famille, f.type_immobilisation, f.duree_vie, f.coefficient, f.taux, a.type, f.compte FROM famille_immobilisation f JOIN amortissement a ON f.id_amortissement = a.id_amortissement");
$listefamille = $query->fetchAll(PDO::FETCH_OBJ);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Familles des immobilisations</title>
    <link href="../css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link href="../css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>
<body class="sb-nav-fixed">
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <!-- Navbar Brand--> 
        <a class="navbar-brand ps-3" href="accueil.php"><img src="../img/Logo-Gamma.jpg" alt="" width=200px height=55px ></a>
        <!-- Sidebar Toggle-->
        <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle"><i class="fas fa-bars"></i></button>
         <!-- Navbar Search-->
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
                <div class="d-flex align-items-center justify-content-center mt-4 mb-4">
                    <button type="submit" name="submit" class="btn btn-success"><a href="add_famille.php" class="btn btn-success">Ajouter <i class="fa-solid fa-plus  "></i></a></button>
                </div>
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-table me-1"></i>
                        Tableau des Familles
                    </div>
                    <div class="card-body">
                        <table class="table table-success table-striped table-bordered " id="datatablesSimple">
                            <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Type</th>
                                    <th>Durée de vie</th>
                                    <th>Coefficient</th>
                                    <th>Taux</th>
                                    <th>Amortissement</th>
                                    <th>Compte</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th>Nom</th>
                                    <th>Type</th>
                                    <th>Durée de vie</th>
                                    <th>Coefficient</th>
                                    <th>Taux</th>
                                    <th>Amortissement</th>
                                    <th>Compte</th>
                                    <th>Action</th>
                                </tr>
                            </tfoot>
                            <tbody>
                                <?php foreach($listefamille as $famille): ?>
                                    <tr>
                                        <td><?= $famille->nom_famille; ?></td>
                                        <td><?= $famille->type_immobilisation; ?></td>
                                        <td><?= $famille->duree_vie; ?></td>
                                        <td><?= $famille->coefficient; ?></td>
                                        <td><?= $famille->taux; ?></td>
                                        <td><?= $famille->type; ?></td>
                                        <td><?= $famille->compte; ?></td>                                 
                                            <td>
                                            <a href="modifier_famille.php?id=<?= $famille->id_famille; ?>" class="btn btn-info"><i class="fa-solid fa-pen-to-square"></i></a>
                                        </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                </main>
                <footer class="py-4 bg-light mt-auto">
                    <div class="container-fluid px-4">
                        <div class=" align-items-center text-center small">
                            <div class="text-muted">Copyright &copy; Gestion des immobilisations 2024</div>
                            
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
