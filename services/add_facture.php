<?php
require_once('../config/dbconnect.php');

// Récupérer les fournisseurs
$queryFournisseurs = $db->prepare("SELECT id_fournisseur, nom_fournisseur FROM compte_fournisseur");
$queryFournisseurs->execute();
$fournisseurs = $queryFournisseurs->fetchAll(PDO::FETCH_OBJ);

// Récupérer les immobilisations
$queryImmobilisations = $db->prepare("SELECT id_immobilisation, intitule, valeur_acquision FROM immobilisation");
$queryImmobilisations->execute();
$immobilisations = $queryImmobilisations->fetchAll(PDO::FETCH_OBJ);

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $valeur = $_POST['valeur'];
    $TVA = $_POST['TVA'];
    $marque = $_POST['marque'];
    $serie = $_POST['serie'];
    $date_facture = $_POST['date_facture'];
    $id_immobilisation = $_POST['id_immobilisation'];
    $id_fournisseur = $_POST['id_fournisseur'];

    // Vérifier si une facture existe déjà pour cette immobilisation
    $queryCheck = $db->prepare('SELECT COUNT(*) FROM facture WHERE id_immobilisation = ?');
    $queryCheck->execute([$id_immobilisation]);
    $exists = $queryCheck->fetchColumn();

    if ($exists) {
        $error = 'Cette immobilisation a déjà une facture. Veuillez choisir une autre immobilisation.';
    } else {
        $query = $db->prepare('INSERT INTO facture (valeur, TVA, marque, serie, date_facture, id_immobilisation, id_fournisseur) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $query->execute([$valeur, $TVA, $marque, $serie, $date_facture, $id_immobilisation, $id_fournisseur]);

        // Rediriger vers la liste des factures après l'ajout
        header('Location: facturelist.php');
        exit;
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
    <title>Ajouter Facture</title>
    <link href="../css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link href="../css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
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
                        <div class="card-header"><h3 class="text-center font-weight-light my-3">Ajouter une Facture</h3></div>
                        <div class="card-body">
                            <?php if ($error): ?>
                                <div class="alert alert-danger"><?= $error ?></div>
                            <?php endif; ?>
                            <form method="POST" action="add_facture.php">
                                <div class="mb-3">
                                    <label for="immobilisation" class="form-label">Immobilisation</label>
                                    <select id="immobilisation" name="id_immobilisation" class="form-select" required onchange="updateValeur()">
                                        <option value="">Sélectionnez une immobilisation</option>
                                        <?php foreach($immobilisations as $immobilisation): ?>
                                            <option value="<?= $immobilisation->id_immobilisation ?>" data-valeur="<?= $immobilisation->valeur_acquision ?>">
                                                <?= $immobilisation->intitule ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="fournisseur" class="form-label">Fournisseur</label>
                                    <select id="fournisseur" name="id_fournisseur" class="form-select" required>
                                        <option value="" selected disabled>Sélectionnez un fournisseur</option>
                                        <?php foreach($fournisseurs as $fournisseur): ?>
                                            <option value="<?= $fournisseur->id_fournisseur ?>"><?= $fournisseur->nom_fournisseur ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="valeur" class="form-label">Valeur d'Acquisition</label>
                                    <input type="text" id="valeur" name="valeur" class="form-control" readonly>
                                </div>
                                <div class="mb-3">
                                    <label for="TVA" class="form-label">TVA</label>
                                    <input type="text" id="TVA" name="TVA" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label for="marque" class="form-label">Marque</label>
                                    <input type="text" id="marque" name="marque" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label for="serie" class="form-label">Numéro de Série</label>
                                    <input type="text" id="serie" name="serie" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label for="date_facture" class="form-label">Date de la Facture</label>
                                    <input type="date" id="date_facture" name="date_facture" class="form-control" required>
                                </div>
                                <button type="submit" class="btn btn-primary">Ajouter</button>
                            </form>
                        </div>
                        <div class="card-footer text-center py-3">
                            <div class="small"><a href="facturelist.php">Retourner à la liste des factures</a></div>
                        </div>
                    </div>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="../js/scripts.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
    <script src="../assets/demo/chart-area-demo.js"></script>
    <script src="../assets/demo/chart-bar-demo.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
    <script src="../js/datatables-simple-demo.js"></script>
    <script>
        function updateValeur() {
            var immobilisationSelect = document.getElementById('immobilisation');
            var valeurInput = document.getElementById('valeur');
            var selectedOption = immobilisationSelect.options[immobilisationSelect.selectedIndex];
            valeurInput.value = selectedOption.getAttribute('data-valeur');
        }
    </script>
</body>
</html>