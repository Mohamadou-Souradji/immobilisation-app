<?php
require_once('../config/dbconnect.php');

$message = '';
if (isset($_POST['submit'])) {
    $file = $_FILES['file']['tmp_name'];

    if (($handle = fopen($file, 'r')) !== FALSE) {
        while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {  // Remplacer ',' par ';' pour le délimiteur
            // Vérifier que chaque colonne existe avant de l'utiliser
            $id = isset($data[0]) ? $data[0] : '';
            $intitule = isset($data[1]) ? $data[1] : '';
            $valeur_acquision = isset($data[2]) ? $data[2] : '';
            $date_acquision = isset($data[3]) ? $data[3] : '';
            $date_actif = isset($data[4]) ? $data[4] : '';
            $id_fournisseur = isset($data[5]) ? $data[5] : '';
            $id_famille = isset($data[6]) ? $data[6] : '';
            $id_emplacement = isset($data[7]) ? $data[7] : '';

            if (!empty($id) && !empty($intitule) && !empty($valeur_acquision) && !empty($date_acquision) && !empty($date_actif) && !empty($id_fournisseur) && !empty($id_famille) && isset($id_emplacement)) {
                $query = $db->prepare("INSERT INTO immobilisation (id_immobilisation, intitule, valeur_acquision, date_acquision, date_actif, id_fournisseur, id_famille, id_emplacement) 
                                       VALUES (:id, :intitule, :valeur_acquision, :date_acquision, :date_actif, :id_fournisseur, :id_famille, :id_emplacement)");
                $query->execute([
                    'id' => $id,
                    'intitule' => $intitule,
                    'valeur_acquision' => $valeur_acquision,
                    'date_acquision' => $date_acquision,
                    'date_actif' => $date_actif,
                    'id_fournisseur' => $id_fournisseur,
                    'id_famille' => $id_famille,
                    'id_emplacement' => $id_emplacement
                ]);
            } else {
                $message .= 'Erreur: Veuillez vérifier les champs pour la ligne avec l\'ID ' . $id . '<br>';
            }
        }
        fclose($handle);
        $message .= 'Importation terminée avec succès.';
    } else {
        $message = 'Erreur lors de l\'ouverture du fichier CSV.';
    }
} else {
    $message = 'Aucun fichier n\'a été téléchargé.';
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
    <title>Importation des immobilisations</title>
    <link href="../css/bootstrap.min.css" rel="stylesheet" />
    <link href="../css/styles.css" rel="stylesheet" />
</head>
<body class="sb-nav-fixed">
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <a class="navbar-brand ps-3" href="accueil.php"><img src="../img/Logo-Gamma.jpg" alt="" width="200" height="55"></a>
        <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars"></i></button>
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
                                <div class="card-header"><h3 class="text-center font-weight-light my-3">Importation des immobilisations</h3></div>
                                <div class="card-body">
                                    <?php if (!empty($message)): ?>
                                        <div class="alert alert-danger" role="alert">
                                            <?= htmlspecialchars($message); ?>
                                        </div>
                                    <?php endif; ?>
                                    <form action="" method="post" enctype="multipart/form-data">
                                        <div class="form-floating mb-3">
                                            <input type="file" name="file" class="form-control" id="inputFile" required>
                                            <label for="inputFile" class="form-label">Choisir un fichier CSV</label>
                                        </div>
                                        <div class="mt-4 mb-0">
                                            <div class="d-grid"><button type="submit" name="submit" class="btn btn-primary btn-block">Importer</button></div>
                                        </div>
                                    </form>
                                </div>
                                <div class="card-footer text-center py-3">
                                    <div class="small"><a href="immobilisation.php">Retourner à la liste des immobilisations</a></div>
                                </div>
                            </div>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="../js/scripts.js"></script>
</body>
</html>
