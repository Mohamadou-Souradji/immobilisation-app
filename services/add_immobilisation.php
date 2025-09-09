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

// Initialisation des valeurs du formulaire
$id = '';
$intitule = '';
$valeur_acquision = '';
$date_acquision = '';
$date_actif = '';
$id_fournisseur = '';
$id_famille = '';
$id_emplacement = '';

// Traitement du formulaire d'ajout
$message = '';
if (isset($_POST['submit'])) {
    if (!empty($_POST['id']) && !empty($_POST['intitule']) && !empty($_POST['valeur_acquision']) && !empty($_POST['date_acquision']) 
        && !empty($_POST['date_actif']) && !empty($_POST['id_fournisseur']) && !empty($_POST['id_famille']) 
        && isset($_POST['id_emplacement'])) { // Utiliser isset pour vérifier l'existence de id_emplacement
        $id = $_POST['id'];
        $intitule = $_POST['intitule'];
        $valeur_acquision = $_POST['valeur_acquision'];
        $date_acquision = $_POST['date_acquision'];
        $date_actif = $_POST['date_actif'];
        $id_fournisseur = $_POST['id_fournisseur'];
        $id_famille = $_POST['id_famille'];
        $id_emplacement = $_POST['id_emplacement'];

        // Vérifier si l'id_immobilisation ou l'intitule existe déjà
        $query = $db->prepare("SELECT COUNT(*) FROM immobilisation WHERE id_immobilisation = :id OR intitule = :intitule");
        $query->execute(['id' => $id, 'intitule' => $intitule]);
        $exists = $query->fetchColumn();

        if ($exists) {
            $message = 'Cette immobilisation existe déjà. Veuillez en choisir un autre.';
        } else {
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

            // Rediriger vers la liste des immobilisations
            header('Location: immobilisation.php');
            exit();
        }
    } else {
        $message = 'Veuillez remplir tous les champs.';
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
    <title>Ajouter Immobilisation</title>
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
                                <div class="card-header"><h3 class="text-center font-weight-light my-3" >Ajouter une immobilisation</h3></div>
                                <div class="card-body">
                                    <?php if (!empty($message)): ?>
                                        <div class="alert alert-danger" role="alert">
                                            <?= htmlspecialchars($message); ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <form action="" method="post">
                                    <div class="form-floating mb-3">
                                            <select name="id_famille" class="form-select" id="inputFamille" onchange="updateFamilleInfo()">
                                                <option value="" disabled>Sélectionner une famille</option>
                                                <?php foreach ($listeFamille as $famille): ?>
                                                    <option value="<?= $famille->id_famille ?>"
                                                            data-type="<?= htmlspecialchars($famille->type_immobilisation) ?>"
                                                            data-duree="<?= htmlspecialchars($famille->duree_vie) ?>"
                                                            data-compte="<?= htmlspecialchars($famille->compte) ?>"
                                                            data-taux="<?= htmlspecialchars($famille->taux) ?>"
                                                            data-coefficient="<?= htmlspecialchars($famille->coefficient) ?>">
                                                        <?= htmlspecialchars($famille->nom_famille) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <label for="inputFamille" class="form-label">Famille</label>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input type="number" name="id" class="form-control" id="inputId" placeholder="Code" readonly>
                                            <label for="inputId" class="form-label">Code</label>
                                        </div>                                       
                                            <div class="form-floating mb-3">
                                                <input type="text" name="intitule" class="form-control <?= isset($errors['intitule']) ? 'is-invalid' : '' ?>" id="inputIntitule" placeholder="Intitulé" autofocus>
                                                <label for="inputIntitule" class="form-label">Intitulé</label>
                                                <?php if (isset($errors['intitule'])): ?>
                                                    <div class="invalid-feedback"><?= $errors['intitule'] ?></div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="form-floating mb-3">
                                                <input type="number" name="valeur_acquision" class="form-control <?= isset($errors['valeur_acquision']) ? 'is-invalid' : '' ?>" id="inputValeurAcquisition" placeholder="Valeur d'acquisition">
                                                <label for="inputValeurAcquisition" class="form-label">Valeur d'acquisition</label>
                                                <?php if (isset($errors['valeur_acquision'])): ?>
                                                    <div class="invalid-feedback"><?= $errors['valeur_acquision'] ?></div>
                                                <?php endif; ?>
                                            </div>
                                        <div class="form-floating mb-3">
                                            <input type="date" name="date_acquision" class="form-control" id="inputDateAcquisition" placeholder="Date d'acquisition">
                                            <label for="inputDateAcquisition" class="form-label">Date d'acquisition</label>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input type="date" name="date_actif" class="form-control" id="inputDateActif" placeholder="Date de mise en service">
                                            <label for="inputDateActif" class="form-label">Date de mise en service</label>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <select name="id_fournisseur" class="form-select" id="inputFournisseur">
                                                <option value=""selected disabled>Sélectionner un fournisseur</option>
                                                <?php foreach ($listeFournisseur as $fournisseur): ?>
                                                    <option value="<?= $fournisseur->id_fournisseur ?>"><?= $fournisseur->nom_fournisseur ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            <label for="inputFournisseur" class="form-label">Fournisseur</label>
                                        </div>
                                      
                                        <div class="form-floating mb-3">
                                            <select name="id_emplacement" class="form-select" id="inputEmplacement">
                                                <option value=" "selected disabled>Sélectionner un emplacement</option>
                                                <?php foreach ($listeEmplacement as $emplacement): ?>
                                                    <option value="<?= $emplacement->id_emplacement ?>"><?= $emplacement->lieu ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            <label for="inputEmplacement" class="form-label">Emplacement</label>
                                        </div>

                                        <!-- Afficher les informations de la famille sélectionnée -->
                                        <div id="familleInfo" style="display: none;">
                                            <div class="form-floating mb-3">
                                                <input type="text" class="form-control" id="familleType" placeholder="Type d'immobilisation" readonly>
                                                <label for="familleType" class="form-label">Type d'immobilisation</label>
                                            </div>
                                            <div class="form-floating mb-3">
                                                <input type="text" class="form-control" id="familleDuree" placeholder="Durée de vie" readonly>
                                                <label for="familleDuree" class="form-label">Durée de vie</label>
                                            </div>
                                            <div class="form-floating mb-3">
                                                <input type="text" class="form-control" id="familleCompte" placeholder="Compte" readonly>
                                                <label for="familleCompte" class="form-label">Compte</label>
                                            </div>
                                            <div class="form-floating mb-3">
                                                <input type="text" class="form-control" id="familleTaux" placeholder="Taux" readonly>
                                                <label for="familleTaux" class="form-label">Taux</label>
                                            </div>
                                            <div class="form-floating mb-3">
                                                <input type="text" class="form-control" id="familleCoefficient" placeholder="Coefficient" readonly>
                                                <label for="familleCoefficient" class="form-label">Coefficient</label>
                                            </div>
                                        </div>

                                        <div class="mt-4 mb-0">
                                            <div class="d-grid"><button type="submit" name="submit" class="btn btn-primary btn-block">Ajouter</button></div>

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
                        <div class=" align-items-center text-center small">
                            <div class="text-muted">Copyright &copy; Gestion des immobilisations 2024 </div>
                            
                        </div>
                    </div>
                </footer>
        </div>
    </div>
    <script>
        function updateFamilleInfo() {
            const familleSelect = document.getElementById('inputFamille');
            const selectedOption = familleSelect.options[familleSelect.selectedIndex];

            const type = selectedOption.getAttribute('data-type');
            const duree = selectedOption.getAttribute('data-duree');
            const compte = selectedOption.getAttribute('data-compte');
            const taux = selectedOption.getAttribute('data-taux');
            const coefficient = selectedOption.getAttribute('data-coefficient');

            if (type && duree && compte && taux && coefficient) {
                document.getElementById('familleType').value = type;
                document.getElementById('familleDuree').value = duree;
                document.getElementById('familleCompte').value = compte;
                document.getElementById('familleTaux').value = taux;
                document.getElementById('familleCoefficient').value = coefficient;
                document.getElementById('familleInfo').style.display = 'block';
            } else {
                document.getElementById('familleInfo').style.display = 'none';
            }

            // Génération automatique du code d'immobilisation
            const compteNum = selectedOption.getAttribute('data-compte');
            const uniqueNum = Math.floor(Math.random() * 100); // Vous pouvez ajuster ceci selon vos besoins
            const codeImmobilisation = compteNum + uniqueNum;
            document.getElementById('inputId').value = codeImmobilisation;
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="../js/scripts.js"></script>
</body>
</html>
