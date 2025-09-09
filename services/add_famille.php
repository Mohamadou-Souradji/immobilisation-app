<?php
require_once('../config/dbconnect.php');

$message = '';
$nom_famille = '';
$type_immobilisation = '';
$duree_vie = '';
$coefficient = '';
$taux = '';
$id_amortissement = '';
$compte = '';

if (isset($_POST['submit'])) {
    $nom_famille = $_POST['nom_famille'];
    $type_immobilisation = $_POST['type_immobilisation'];
    $duree_vie = $_POST['duree_vie'];
    $coefficient = $_POST['coefficient'];
    $taux = $_POST['taux'];
    $id_amortissement = $_POST['id_amortissement'];
    $compte = $_POST['compte'];

    // Vérifier si le nom de la famille existe déjà
    $query = $db->prepare('SELECT COUNT(*) FROM famille_immobilisation WHERE nom_famille = :nom_famille');
    $query->execute(['nom_famille' => $nom_famille]);
    $count = $query->fetchColumn();

    if ($count > 0) {
        // Le nom de la famille existe déjà
        $message = ' Le nom de la famille saisi existe déjà. ';
    } else {
        // Insérer les données dans la table famille_immobilisation
        $query = $db->prepare('INSERT INTO famille_immobilisation (nom_famille, type_immobilisation, duree_vie, coefficient, taux, id_amortissement, compte) VALUES (:nom_famille, :type_immobilisation, :duree_vie, :coefficient, :taux, :id_amortissement, :compte)');
        $query->execute([
            'nom_famille' => $nom_famille,
            'type_immobilisation' => $type_immobilisation,
            'duree_vie' => $duree_vie,
            'coefficient' => $coefficient,
            'taux' => $taux,
            'id_amortissement' => $id_amortissement,
            'compte' => $compte,
        ]);

        header('Location: famille_immobilisation.php');
        exit();
    }
}

$query_amortissements = $db->query("SELECT * FROM amortissement");
$amortissements = $query_amortissements->fetchAll(PDO::FETCH_OBJ);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Ajouter une famille d'immobilisation</title>
    <link href="../css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
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
                            <div class="card-header">
                                <h3 class="text-center font-weight-light my-3" style="color: black;">Ajouter une famille d'immobilisation</h3>
                            </div>
                            <div class="card-body">
                                <?php if ($message): ?>
                                    <div class="alert alert-danger" role="alert">
                                    <i class="fa-solid fa-circle-exclamation"></i> <?= $message; ?>
                                    </div>
                                <?php endif; ?>
                                <form action="add_famille.php" method="POST">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="nom_famille" name="nom_famille" required>
                                        <label for="nom_famille">Nom de la famille</label>
                                    </div>
                                    <div class="form-floating mb-3">
                                        <select class="form-control" id="type_immobilisation" name="type_immobilisation" required>
                                            <option value="" selected disabled>Selectionner un type</option>
                                            <option value="corporelle">corporelle</option>
                                            <option value="incorporelle">incorporelle</option>
                                            <option value="finance">finance</option>
                                        </select>  
                                        <label for="type_immobilisation">Type d'immobilisation</label>
                                    </div>
                                    <div class="form-floating mb-3">
                                        <input type="number" class="form-control" id="duree_vie" name="duree_vie" required>
                                        <label for="duree_vie">Durée de vie</label>
                                    </div>
                                    <div class="form-floating mb-3">
                                                        <input type="number" class="form-control" id="coefficient" name="coefficient" step="0.01" min="0" value="<?= htmlspecialchars($coefficient) ?>">
                                                        <label for="coefficient">Coefficient</label>
                                                    </div>

                                    <div class="form-floating mb-3">
                                        <input type="number" class="form-control" id="taux" name="taux" required>
                                        <label for="taux">Taux</label>
                                    </div>
                                    <div class="form-floating mb-3">
                                        <select class="form-control" id="id_amortissement" name="id_amortissement" required>
                                            <?php foreach($amortissements as $amortissement): ?>
                                                <option value="<?= $amortissement->id_amortissement ?>"><?= $amortissement->type ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <label for="id_amortissement">Type d'amortissement</label>
                                    </div>
                                    <div class="form-floating mb-3">
                                        <input type="number" class="form-control" id="compte" name="compte" required>
                                        <label for="compte">Compte</label>
                                    </div>
                                    <div class="mt-4 mb-0">
                                        <div class="d-grid">
                                            <button type="submit" name="submit" class="btn btn-primary">Ajouter</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="card-footer text-center py-3">
                                <div class="small"><a href="famille_immobilisation.php">Retourner à la liste des familles</a></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        <footer class="py-4 bg-light mt-auto">
            <div class="container-fluid px-4">
                <div class="d-flex align-items-center justify-content-center text-center small">
                    <div class="text-muted">Copyright &copy; Gestion des immobilisations 2024</div>
                </div>
            </div>
        </footer>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="../js/scripts.js"></script>
</body>
</html>
