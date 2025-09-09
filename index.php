<?php
require_once('config/dbconnect.php');

// Vérifier si le formulaire a été soumis
$message = '';
if(isset($_POST['submit'])){
    // Vérifier si les champs sont renseignés
    if(!empty($_POST['login']) && !empty($_POST['password'])){
        // Récupérer les données du formulaire
        $login = $_POST['login'];
        $password = $_POST['password'];
        
        // Requête SQL pour vérifier l'existence de l'utilisateur
        $query = $db->prepare("SELECT * FROM utilisateur WHERE login=:login AND password=:password");
        $query->execute(['login' => $login, 'password' => $password]);
        
        // Vérifier si l'utilisateur existe dans la base de données
        if($query->rowCount() > 0){
            // Utilisateur authentifié, redirection vers la page d'accueil
            header('Location: services/accueil.php');
            exit; // Arrêter l'exécution du script après la redirection
        } else {
            // Utilisateur non trouvé, afficher un message d'erreur
            $message = "Identifiants incorrects. Veuillez réessayer.";
        }
    } else {
        // Champs non renseignés, afficher un message d'erreur
        $message = "Veuillez remplir tous les champs.";
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
        <title>Login</title>
        <link href="css/styles.css" rel="stylesheet" />
        <link href="css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
        <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
        <style>
            .card-header img {
                width: 100%;
                height: auto;
            }
            .card-body {
                padding: 2rem;
            }
        </style>
    </head>
    <body >
        <div id="layoutAuthentication">
            <div id="layoutAuthentication_content">
                <main>
                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="col-lg-6"> <!-- Augmentation de la largeur de la colonne -->
                                <div class="card shadow-lg border-0 rounded-lg mt-5">
                                    <div class="card-header">
                                        <img src="img/Logo-Gamma.jpg" alt="Logo Gamma">
                                    </div>
                                    <div class="card-body">
                                        <form action="" method="post">
                                            <div class="form-floating mb-3">
                                                <input class="form-control" name="login" id="inputEmail" type="email" placeholder="name@example.com" />
                                                <label for="inputEmail">Adresse Email</label>
                                            </div>
                                            <div class="form-floating mb-3">
                                                <input class="form-control" name="password" id="inputPassword" type="password" placeholder="Password" />
                                                <label for="inputPassword">Mot de passe</label>
                                            </div>
                                            <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                                                <button type="submit" name="submit" class="btn btn-success">Se connecter</button>
                                            </div>
                                        </form>
                                    </div>
                                    <?php if ($message): ?>
                                        <div class="card-footer text-danger text-center">
                                            <?= htmlspecialchars($message) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="../js/scripts.js"></script>
    </body>
</html>
