<?php
require_once('../config/dbconnect.php');

// Récupérer la liste des fournisseurs
$query = $db->query("SELECT id_fournisseur, nom_fournisseur FROM compte_fournisseur");
$listeFournisseur = $query->fetchAll(PDO::FETCH_OBJ);

// Récupérer la liste des familles
$query = $db->query("SELECT id_famille, nom_famille, type_immobilisation, duree_vie, compte, taux, coefficient FROM famille_immobilisation");
$listeFamille = $query->fetchAll(PDO::FETCH_OBJ);

// Récupérer la liste des emplacements
$query = $db->query("SELECT id_emplacement, lieu FROM emplacement");
$listeEmplacement = $query->fetchAll(PDO::FETCH_OBJ);

// Récupérer les informations de l'immobilisation à modifier
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $query = $db->prepare("SELECT * FROM immobilisation WHERE id_immobilisation = ?");
    $query->execute([$id]);
    $immobilisation = $query->fetch(PDO::FETCH_OBJ);
    if (!$immobilisation) {
        // Rediriger si l'immobilisation n'existe pas
        header('Location: immobilisation.php');
        exit();
    }
} else {
    // Rediriger si l'ID n'est pas fourni
    header('Location: immobilisation.php');
    exit();
}

// Traitement du formulaire de modification
// Traitement du formulaire de modification
if (isset($_POST['submit'])) {
    if (!empty($_POST['id_immobilisation']) && !empty($_POST['intitule']) && !empty($_POST['valeur_acquision']) && !empty($_POST['date_acquision']) 
        && !empty($_POST['date_actif']) && !empty($_POST['id_fournisseur']) && !empty($_POST['id_famille']) 
        && !empty($_POST['id_emplacement'])) {
        
        $id_immobilisation = $_POST['id_immobilisation'];
        $intitule = $_POST['intitule'];
        $valeur_acquision = $_POST['valeur_acquision'];
        $date_acquision = $_POST['date_acquision'];
        $date_actif = $_POST['date_actif'];
        $id_fournisseur = $_POST['id_fournisseur'];
        $id_famille = $_POST['id_famille'];
        $id_emplacement = $_POST['id_emplacement'];

        // Vérifier si le nouveau nom d'immobilisation existe déjà pour une autre immobilisation
        $query = $db->prepare("SELECT id_immobilisation FROM immobilisation WHERE intitule = ? AND id_immobilisation != ?");
        $query->execute([$intitule, $id]);
        $existingImmobilisation = $query->fetch(PDO::FETCH_OBJ);

        if ($existingImmobilisation) {
            // Nom d'immobilisation déjà utilisé pour une autre immobilisation
            $message = "Le nom d'immobilisation '$intitule' existe déjà pour une autre immobilisation.";
        } else {
            // Mettre à jour l'immobilisation dans la base de données
            $query = $db->prepare("UPDATE immobilisation SET id_immobilisation=:id_immobilisation, intitule = :intitule, valeur_acquision = :valeur_acquision, date_acquision = :date_acquision, date_actif = :date_actif, id_fournisseur = :id_fournisseur, id_famille = :id_famille, id_emplacement = :id_emplacement WHERE id_immobilisation = :id");
            $query->execute([
                'id' => $id,
                'id_immobilisation' => $id_immobilisation,
                'intitule' => $intitule,
                'valeur_acquision' => $valeur_acquision,
                'date_acquision' => $date_acquision,
                'date_actif' => $date_actif,
                'id_fournisseur' => $id_fournisseur,
                'id_famille' => $id_famille,
                'id_emplacement' => $id_emplacement
            ]);

            // Rediriger vers la liste des immobilisations après la mise à jour
            header('Location: immobilisation.php');
            exit();
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
    <title>Modifier Immobilisation</title>
    <link href="../css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link href="../css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>
<body class="sb-nav-fixed">
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <!-- Navbar Brand--> 
        <a class="navbar-brand ps-3" href="accueil.php"><img src="../img/Logo-Gamma.jpg" alt="" width=200px height=55px ></a>
        <!-- Sidebar Toggle-->
        <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars"></i></button>
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
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-8">
                            <div class="card shadow-lg border-0 rounded-lg mt-5">
                                <div class="card-header"><h3 class="text-center font-weight-light my-3">Modifier une immobilisation</h3></div>
                                <div class="card-body">
                                <?php if (isset($message)): ?>
                                        <div class="alert alert-danger" role="alert">
                                            <?= $message ?>
                                        </div>
                                    <?php endif; ?>
                                    <form action="" method="post">
                                         <div class="form-floating mb-3">
                                            <input type="number" name="id_immobilisation" class="form-control" id="inputId" placeholder="Code" value="<?= htmlspecialchars($immobilisation->id_immobilisation); ?>">
                                            <label for="inputId" class="form-label">Code</label>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input type="text" name="intitule" class="form-control" id="inputIntitule" placeholder="Intitulé" value="<?= htmlspecialchars($immobilisation->intitule); ?>">
                                            <label for="inputIntitule" class="form-label">Intitulé</label>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input type="number" name="valeur_acquision" class="form-control" id="inputValeurAcquision" placeholder="Valeur d'acquisition" value="<?= htmlspecialchars($immobilisation->valeur_acquision); ?>">
                                            <label for="inputValeurAcquision" class="form-label">Valeur d'acquisition</label>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input type="date" name="date_acquision" class="form-control" id="inputDateAcquision" placeholder="Date d'acquisition" value="<?= htmlspecialchars($immobilisation->date_acquision); ?>">
                                            <label for="inputDateAcquision" class="form-label">Date d'acquisition</label>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input type="date" name="date_actif" class="form-control" id="inputDateActif" placeholder="Date d'entrée en actif" value="<?= htmlspecialchars($immobilisation->date_actif); ?>">
                                            <label for="inputDateActif" class="form-label">Date d'entrée en actif</label>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <select name="id_fournisseur" class="form-control" id="inputFournisseur">
                                                <?php foreach ($listeFournisseur as $fournisseur): ?>
                                                    <option value="<?= $fournisseur->id_fournisseur ?>" <?= $immobilisation->id_fournisseur == $fournisseur->id_fournisseur ? 'selected' : ''; ?>><?= htmlspecialchars($fournisseur->nom_fournisseur); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            <label for="inputFournisseur" class="form-label">Fournisseur</label>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <select name="id_famille" class="form-control" id="inputFamille">
                                                <?php foreach ($listeFamille as $famille): ?>
                                                    <option value="<?= $famille->id_famille ?>" <?= $immobilisation->id_famille == $famille->id_famille ? 'selected' : ''; ?>><?= htmlspecialchars($famille->nom_famille); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            <label for="inputFamille" class="form-label">Famille</label>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <select name="id_emplacement" class="form-control" id="inputEmplacement">
                                                <?php foreach ($listeEmplacement as $emplacement): ?>
                                                    <option value="<?= $emplacement->id_emplacement ?>" <?= $immobilisation->id_emplacement == $emplacement->id_emplacement ? 'selected' : ''; ?>><?= htmlspecialchars($emplacement->lieu); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            <label for="inputEmplacement" class="form-label">Emplacement</label>
                                        </div>
                                        <div class="mt-4 mb-0">
                                            <div class="d-grid"><button type="submit" name="submit" class="btn btn-primary btn-block">Enregistrer les modifications</button></div>
                                        </div>
                                    </form>
                                </div>
                                <div class="card-footer text-center py-3">
                                    <div class="small"><a href="immobilisation.php">Retourner à la liste</a></div>
                                </div>
                            </div>
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
                            <a href="#">Terms & Conditions</a>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
    <script src="../js/scripts.js"></script>
</body>
</html>
