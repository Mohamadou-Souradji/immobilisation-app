

<?php
require_once('../config/dbconnect.php');

// Récupérer les immobilisations non cédées
$query = $db->query("SELECT id_immobilisation, intitule, valeur_acquision FROM immobilisation WHERE cedee = FALSE");
$immobilisations = $query->fetchAll(PDO::FETCH_OBJ);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Cession</title>
    <link href="../css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link href="../css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <script>
        function updateValue() {
            var immobilisationSelect = document.getElementById('immobilisation');
            var valeurAcquisition = immobilisationSelect.options[immobilisationSelect.selectedIndex].getAttribute('data-valeur');
            document.getElementById('valeur_acquisition').value = valeurAcquisition;
        }
    </script>
</head>
<body class="sb-nav-fixed">
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <a class="navbar-brand ps-3" href="accueil.php"><img src="../img/Logo-Gamma.jpg" alt="" width="200" height="55"></a>
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
            <div class="container-fluid px-4">
                <div class="card shadow-lg border-0 rounded-lg mt-5">

                  <div class="card-header"><h3 class="text-center font-weight-light my-3" style="color: black;"> Ajouter une Cession</h3></div>
                    
                        <div class="card-body">
                    <form method="POST" action="add_cession.php">
                        <div class="mb-3">
                            <label for="immobilisation" class="form-label">Immobilisation</label>
                            <select id="immobilisation" name="id_immobilisation" class="form-select" onchange="updateValue()" required>
                                <option value=""selected disabled>Sélectionnez une immobilisation</option>
                                <?php foreach($immobilisations as $immobilisation): ?>
                                    <option value="<?= $immobilisation->id_immobilisation ?>" data-valeur="<?= $immobilisation->valeur_acquision ?>"><?= $immobilisation->intitule ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="valeur_acquisition" class="form-label">Valeur d'acquisition</label>
                            <input type="text" id="valeur_acquisition" class="form-control" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="date_cession" class="form-label">Date de Cession</label>
                            <input type="date" id="date_cession" name="date_cession" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="prix_vente" class="form-label">Prix de Vente</label>
                            <input type="number" id="prix_vente" name="prix_vente" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="autres_infos" class="form-label">Autres Informations</label>
                            <textarea id="autres_infos" name="autres_infos" class="form-control"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Céder</button>
                    </form>
                </div>
                <div>
                    </div>
            </main>
           
            <footer class="py-4 bg-light mt-auto">
                <div class="container-fluid px-4">
                    <div class="align-items-center text-center small">
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
