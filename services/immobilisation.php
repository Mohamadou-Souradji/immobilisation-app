<?php
require_once('../config/dbconnect.php');
// Vérifier si l'ID d'immobilisation à supprimer est passé en paramètre GET
if(isset($_GET['id_immobilisation'])){
    // Supprimer l'immobilisation correspondante dans la base de données
    $query = $db->prepare('DELETE FROM immobilisation WHERE id_immobilisation=:id_immobilisation');
    $query->execute(['id_immobilisation' => $_GET['id_immobilisation']]);
    
    // Rediriger vers la liste des immobilisations après la suppression
    header('Location: immobilisation.php');
    exit; // Assurez-vous de terminer le script après la redirection
}
// Récupérer la liste des immobilisations avec des jointures pour obtenir les noms des fournisseurs, familles et emplacements
$query = $db->query("
SELECT i.id_immobilisation, i.intitule, i.valeur_acquision, i.date_acquision, i.date_actif,e.lieu,fa.nom_famille,fo.nom_fournisseur 
FROM immobilisation i, emplacement e , famille_immobilisation fa,compte_fournisseur fo 
 WHERE e.id_emplacement=i.id_emplacement 
and fa.id_famille=i.id_famille and i.id_fournisseur=fo.id_fournisseur and cedee = False;
");
$listeImmobilisations = $query->fetchAll(PDO::FETCH_OBJ);

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Immobilisations</title>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/print-js/1.6.0/print.min.js" integrity="sha512-3Ke5B/D+mql1CrUK2N2F/EOcp4i5cXeb5WEx6yIPsgelSKMdJ0P4ATf35aPIGbdywOYq1c9/IVuFOzC6Tp0MPA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js" integrity="sha512-Z7Xw8z5MzF93ByC9/WyPo4tRrUvW4CBMJ8I7azDhElOi2WUbZPse4VXslI85cDlSLD4+MX2Fj8EytA5loqJ33Q==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <link href="../css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
        <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
        <link href="../css/styles.css" rel="stylesheet" />
        <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    </head>
    <body class="sb-nav-fixed">
        <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
            <!-- Navbar Brand--> 
            <a class="navbar-brand ps-3" href="accueil.php"><img src="../img/Logo-Gamma.jpg" alt="" width="200" height="55"></a>
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
    <a href="add_immobilisation.php" class="btn btn-success">Ajouter <i class="fa-solid fa-plus"></i></a>
    <a href="export_excell.php" class="btn btn-primary ms-2">Exporter en excel <i class="fa-solid fa-file-excel"></i></a>
    <a href="generate_pdf.php" class="btn btn-danger ms-2">Télécharger PDF <i class="fa-solid fa-file-pdf"></i></a>

</div>

                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-table me-1"></i>
                            Tableau des Immobilisations
                        </div>
                        <div class="card-body">
                                <table class="table table-success table-striped table-bordered " id="datatablesSimple">
                                    <thead >
                                        <tr >
                                        <th>Code</th>
                                        <th>Intitulé</th>
                                        <th >Valeur </th>
                                        <th>Date d'acquisition</th>
                                        <th>Fournisseur</th>
                                        <th>Famille</th>
                                        <th>lieu</th>                                   
                                        <th>Action</th>                                   
                                        </tr>
                                    </thead>
                                    <tfoot >
                                    <tr>
                                        <th>Code</th>
                                        <th>Intitulé</th>
                                        <th >Valeur </th>
                                        <th>Date d'acquisition</th>
                                        <th>Fournisseur</th>
                                        <th>Famille</th>
                                        <th>lieu</th>
                                        <th>Action</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
                                    <?php foreach($listeImmobilisations as $immobilisation): ?>
                                        <tr>
                                            <th><?= $immobilisation->id_immobilisation; ?></th>
                                            <td><?=$immobilisation->intitule; ?></</td>
                                            <td width=><?=$immobilisation->valeur_acquision; ?></</td>
                                            <td><?=$immobilisation->date_acquision; ?></</td>
                                            <td><?=$immobilisation->nom_fournisseur; ?></</td>
                                            <td><?=$immobilisation->nom_famille; ?></</td>
                                            <td><?=$immobilisation->lieu; ?></</td>
                                            <td>
                                            <a href="modifier_immobilisation.php?id=<?= $immobilisation->id_immobilisation; ?>" class="btn btn-info "><i class="fa-solid fa-pen-to-square"></i></a>
                                            <a href="plan_amortissement.php?id=<?= $immobilisation->id_immobilisation; ?>" class="btn btn-primary">Details</a>

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
        <script>
        function downloadPDF() {
            window.location.href = 'generate_pdf.php'; // Changez ceci en fonction du chemin vers votre script PHP
        }

        function printPDF() {
            var win = window.open('generate_pdf.php', '_blank');
            win.print();
        }
</script>

    </body>
</html>
