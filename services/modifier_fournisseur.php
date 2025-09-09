<?php
require_once('../config/dbconnect.php');

// Récupérer les informations du fournisseur à modifier
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $query = $db->prepare("SELECT * FROM compte_fournisseur WHERE id_fournisseur = ?");
    $query->execute([$id]);
    $fournisseur = $query->fetch(PDO::FETCH_OBJ);
    if (!$fournisseur) {
        // Rediriger si le fournisseur n'existe pas
        header('Location: fournisseur.php');
        exit();
    }
} else {
    // Rediriger si l'ID n'est pas fourni
    header('Location: fournisseur.php');
    exit();
}

// Traitement du formulaire de modification
if (isset($_POST['submit'])) {
    $compte = trim($_POST['compte']);
    $nom = trim($_POST['nom']);

    if (!empty($compte) && !empty($nom)) {
        // Vérifier si le compte ou le nom existent déjà dans la base de données
        $query = $db->prepare("SELECT * FROM compte_fournisseur WHERE id_fournisseur = ? AND id_fournisseur != ?");
        $query->execute([$compte, $id]);
        $compteExists = $query->fetch(PDO::FETCH_OBJ);

        $query = $db->prepare("SELECT * FROM compte_fournisseur WHERE nom_fournisseur = ? AND id_fournisseur != ?");
        $query->execute([$nom, $id]);
        $nomExists = $query->fetch(PDO::FETCH_OBJ);

        if ($compteExists) {
            $error = "Le compte existe déjà.";
        } elseif ($nomExists) {
            $error = "Le nom existe déjà.";
        } else {
            // Préparer et exécuter la requête de mise à jour
            $query = $db->prepare("UPDATE compte_fournisseur SET id_fournisseur = :compte, nom_fournisseur = :nom WHERE id_fournisseur = :id");
            $result = $query->execute([
                'id' => $id,
                'compte' => $compte,
                'nom' => $nom,
            ]);

            if ($result) {
                // Rediriger vers la liste des fournisseurs après succès
                header('Location: fournisseur.php');
                exit();
            } else {
                $error = "Erreur lors de la mise à jour du fournisseur. Veuillez réessayer.";
            }
        }
    } else {
        $error = "Tous les champs sont obligatoires.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Modifier fournisseur</title>
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
        <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars"></i></button>
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
                            Facture
                        </a>
                        <a class="nav-link" href="dotation.php">
                            <div class="sb-nav-link-icon"><i class="fa-brands fa-docker"></i></div>
                            Dotations
                        </a>
                        <a class="nav-link" href="cessionlist.php">
                            <div class="sb-nav-link-icon"><i class="fa-solid fa-eraser"></i></div>
                            Cession
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
                                <div class="card-header"><h3 class="text-center font-weight-light my-3 "  style="color: black;">Modifier un fournisseur</h3></div>
                                <div class="card-body">
                                    <?php if (isset($error)): ?>
                                        <div class="alert alert-danger">
                                            <?= htmlspecialchars($error) ?>
                                        </div>
                                    <?php endif; ?>
                                     <form action="modifier_fournisseur.php?id=<?= $id ?>" method="POST">
                                        <div class="form-floating mb-3">
                                            <input type="text" class="form-control" id="compte" name="compte" value="<?= htmlspecialchars($fournisseur->id_fournisseur); ?>" required>
                                            <label for="compte">Compte</label>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input type="text" class="form-control" id="nom" name="nom" value="<?= htmlspecialchars($fournisseur->nom_fournisseur); ?>" required>
                                            <label for="nom">Nom</label>
                                        </div>
                                        <div class="mt-4 mb-0">
                                            <div class="d-grid">
                                                <button type="submit" name="submit" class="btn btn-primary">Enregistrer les modifications</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="card-footer text-center py-3">
                                    <div class="small"><a href="fournisseur.php">Retourner à la liste des fournisseurs</a></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
            <footer class="py-4 bg-light mt-auto">
                <div class="container-fluid px-4">
                    <div class="d-flex align-items-center justify-content-between small">
                        <div class="text-muted">&copy; Gestion des immobilisations 2024</div>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
    <script src="../js/scripts.js"></script>
</body>
</html>
