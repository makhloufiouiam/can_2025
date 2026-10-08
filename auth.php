<?php
session_start();
require_once 'config.php';

// Ajout des nouveaux champs dans la table users (à exécuter une fois en SQL)
/*
ALTER TABLE users ADD COLUMN (
    civilite VARCHAR(10),
    cin VARCHAR(20),
    date_naissance DATE,
    pays VARCHAR(50),
    ville VARCHAR(50),
    date_creation DATETIME
);
*/

// Gestion de la déconnexion
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: auth.php');
    exit();
}

// Gestion de l'inscription
if (isset($_POST['signup'])) {
    $civilite = $_POST['civilite'];
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $cin = $_POST['cin'];
    $date_naissance = $_POST['date_naissance'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $telephone = $_POST['telephone'];
    $pays = $_POST['pays'];
    $ville = $_POST['ville'];
    $role = 'client';
    $date_creation = date('Y-m-d H:i:s');

    try {
        $stmt = $pdo->prepare("INSERT INTO users (civilite, nom, prenom, cin, date_naissance, email, mot_de_passe, telephone, pays, ville, role, date_creation) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$civilite, $nom, $prenom, $cin, $date_naissance, $email, $password, $telephone, $pays, $ville, $role, $date_creation]);
        
        $_SESSION['user_id'] = $pdo->lastInsertId();
        $_SESSION['user_info'] = [
            'civilite' => $civilite,
            'nom' => $nom,
            'prenom' => $prenom,
            'email' => $email,
            'role' => $role
        ];
        
        header('Location: index.php');
        exit();
    } catch (PDOException $e) {
        $error = "Erreur lors de l'inscription : " . $e->getMessage();
    }
}

// Gestion de la connexion
if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['mot_de_passe'])) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['user_info'] = [
            'civilite' => $user['civilite'],
            'nom' => $user['nom'],
            'prenom' => $user['prenom'],
            'email' => $user['email'],
            'role' => $user['role'],
            'ville' => $user['ville'],
            'pays' => $user['pays']
        ];
        
        header('Location: index.php');
        exit();
    } else {
        $error = "L'adresse e-mail n'existe pas ou le mot de passe est incorrect.";
    }
}

// Redirection si déjà connecté
if (isset($_SESSION['user_id']) && !isset($_GET['signup'])) {
    header('Location: index.php');
    exit();
}

$show_signup = isset($_GET['signup']);

// Liste des villes et pays
$villes = ['Casablanca', 'Rabat', 'Marrakech', 'Fès', 'Tanger', 'Agadir', 'Oujda', 'Laâyoune', 'Dakhla'];
$pays_liste = ['Maroc', 'Algérie', 'Tunisie', 'Sénégal', 'Côte d\'Ivoire', 'Cameroun', 'Égypte', 'Autre'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Authentification - SONARGES</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #e74c3c;
            --light-color: #ecf0f1;
            --dark-color: #2c3e50;
            --gray-color: #95a5a6;
            --success-color: #27ae60;
        }
        
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f5f7fa;
            color: var(--dark-color);
            line-height: 1.6;
        }
        
        .auth-container {
            display: flex;
            min-height: 100vh;
        }
        
        .auth-form-section {
            flex: 1;
            padding: 2rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            background-color: white;
        }
        
        .auth-hero-section {
            flex: 1;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 4rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }
        
        .auth-hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('https://images.unsplash.com/photo-1540575467063-178a50c2df87?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80') no-repeat center center;
            background-size: cover;
            opacity: 0.2;
            z-index: 0;
        }
        
        .auth-hero-content {
            position: relative;
            z-index: 1;
            max-width: 500px;
        }
        
        .auth-logo {
            width: 300px;
            margin-bottom: 2rem;
        }
        
        .auth-title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: var(--primary-color);
        }
        
        .auth-subtitle {
            color: var(--gray-color);
            margin-bottom: 2rem;
            font-size: 1rem;
        }
        
        .auth-form {
            width: 100%;
            max-width: 400px;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-row {
            display: flex;
            gap: 1rem;
        }
        
        .form-row .form-group {
            flex: 1;
        }
        
        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--dark-color);
        }
        
        input, select {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }
        
        input:focus, select:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(44, 62, 80, 0.2);
        }
        
        .radio-group {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        
        .radio-option {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .btn {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            background-color: var(--secondary-color);
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.3s;
            width: 100%;
        }
        
        .btn:hover {
            background-color: #c0392b;
        }
        
        .auth-switch {
            text-align: center;
            margin-top: 1.5rem;
            color: var(--gray-color);
        }
        
        .auth-switch a {
            color: var(--secondary-color);
            text-decoration: none;
            font-weight: 500;
        }
        
        .auth-switch a:hover {
            text-decoration: underline;
        }
        
        .error-message {
            color: #e74c3c;
            background-color: #fde8e8;
            padding: 0.75rem 1rem;
            border-radius: 4px;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
        }
        
        .info-message {
            color: #2980b9;
            background-color: #e8f4fc;
            padding: 0.75rem 1rem;
            border-radius: 4px;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
        }
        
        .hero-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            line-height: 1.2;
        }
        
        .hero-text {
            font-size: 1.1rem;
            margin-bottom: 2rem;
            opacity: 0.9;
        }
        
        @media (max-width: 768px) {
            .auth-container {
                flex-direction: column;
            }
            
            .auth-hero-section {
                display: none;
            }
            
            .auth-form-section {
                padding: 2rem 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-form-section">
         <h1 class="logo" style="font-size: small; font-family: 'Courier New', monospace; font-weight: bold; font-style: italic;">
                    AFRIKA TICKET
    </h1>
            <h1 class="auth-title">Bienvenue !</h1>
            <p class="auth-subtitle">Connectez-vous à votre compte ou créez-en un nouveau</p>
            
            <?php if (isset($error)): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if (!$show_signup): ?>
                <!-- Formulaire de connexion -->
                <form class="auth-form" method="post">
                    <div class="form-group">
                        <label for="email">Adresse e-mail</label>
                        <input type="email" id="email" name="email" placeholder="Entrez votre adresse e-mail" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Mot de passe</label>
                        <input type="password" id="password" name="password" placeholder="Entrez votre mot de passe" required>
                    </div>
                    
                    <div class="info-message">
                        Nouvelle méthode de connexion : Nous enverrons un code à 6 chiffres par e-mail.
                    </div>
                    
                    <button type="submit" name="login" class="btn">Se connecter</button>
                    
                    <div class="auth-switch">
                        Vous n'avez pas de compte ? <a href="?signup=1">S'inscrire</a>
                    </div>
                </form>
            <?php else: ?>
                <!-- Formulaire d'inscription -->
                <form class="auth-form" method="post">
                    <div class="radio-group">
                        <div class="radio-option">
                            <input type="radio" id="homme" name="civilite" value="Homme" checked>
                            <label for="homme">Homme</label>
                        </div>
                        <div class="radio-option">
                            <input type="radio" id="femme" name="civilite" value="Femme">
                            <label for="femme">Femme</label>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="nom">Nom</label>
                            <input type="text" id="nom" name="nom" required>
                        </div>
                        <div class="form-group">
                            <label for="prenom">Prénom</label>
                            <input type="text" id="prenom" name="prenom" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="cin">CIN</label>
                            <input type="text" id="cin" name="cin">
                        </div>
                        <div class="form-group">
                            <label for="date_naissance">Date de naissance</label>
                            <input type="date" id="date_naissance" name="date_naissance">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="pays">Pays</label>
                            <select id="pays" name="pays" required>
                                <option value="">Sélectionnez un pays</option>
                                <?php foreach ($pays_liste as $pays): ?>
                                    <option value="<?= htmlspecialchars($pays) ?>"><?= htmlspecialchars($pays) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="ville">Ville</label>
                            <input type="text" id="ville" name="ville" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="telephone">Téléphone</label>
                        <input type="tel" id="telephone" name="telephone" placeholder="0650-123456" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Adresse e-mail</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Mot de passe</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    
                    <button type="submit" name="signup" class="btn">S'inscrire</button>
                    
                    <div class="auth-switch">
                        Vous avez déjà un compte ? <a href="auth.php">Se connecter</a>
                    </div>
                </form>
            <?php endif; ?>
        </div>
        
        <div class="auth-hero-section">
            <div class="auth-hero-content">
                <h2 class="hero-title">Rejoignez des milliers de clients qui nous font confiance</h2>
                <p class="hero-text">Le Système de Billetterie Informatisée de la SONARGES. Inscrivez-vous gratuitement pour vous lancer.</p>
            </div>
        </div>
    </div>
</body>
</html>
