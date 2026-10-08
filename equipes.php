<?php
session_start();
require_once 'config.php';

// Récupérer le prénom de l'utilisateur connecté
$user_prenom = null;
if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT prenom FROM users WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    if ($user) {
        $user_prenom = $user['prenom'];
    }
}

$user_info = $_SESSION['user_info'] ?? null;

// Récupérer toutes les équipes
$equipes = [];
$sql = "SELECT * FROM equipes ORDER BY id_equipe";
$equipes = $pdo->query($sql)->fetchAll();

// Définition des groupes
$groupes = [
    'A' => [1, 4],
    'B' => [5, 8],
    'C' => [9, 12],
    'D' => [13, 16],
    'E' => [17, 20],
    'F' => [21, 24]
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Équipes - CAN 2025</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #b71c1c;
            --primary-dark: #9e1616;
            --secondary: #222;
            --light: #f5f5f5;
            --dark: #333;
            --gray: #888;
            --white: #fff;
        }
        
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: var(--light);
            color: var(--dark);
            line-height: 1.6;
        }
        
        /* Header */
        .header {
            background: var(--primary);
            color: var(--white);
            padding: 0 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 80px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        
        .header-container {
            width: 100%;
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .logo {
            height: 50px;
            transition: transform 0.3s;
        }
        
        .logo:hover {
            transform: scale(1.05);
        }
        
        .nav-links {
            display: flex;
            gap: 2rem;
        }
        
        .nav-link {
            color: var(--white);
            text-decoration: none;
            font-weight: 500;
            font-size: 1rem;
            transition: all 0.3s;
            position: relative;
            padding: 0.5rem 0;
        }
        
        .nav-link:hover {
            opacity: 0.9;
        }
        
        .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--white);
            transition: width 0.3s;
        }
        
        .nav-link:hover::after {
            width: 100%;
        }
        
        .header-actions {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }
        
        .action-link {
            color: var(--white);
            text-decoration: none;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: opacity 0.3s;
        }
        
        .action-link:hover {
            opacity: 0.8;
        }
        
        .user-menu {
            position: relative;
        }
        
        .user-btn {
            background: var(--white);
            color: var(--primary);
            padding: 0.6rem 1.2rem;
            border: none;
            border-radius: 30px;
            font-weight: 500;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s;
        }
        
        .user-btn:hover {
            background: #f0f0f0;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .user-dropdown {
            display: none;
            position: absolute;
            right: 0;
            top: 120%;
            background: var(--white);
            border-radius: 8px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
            min-width: 180px;
            z-index: 1000;
            overflow: hidden;
            animation: fadeIn 0.3s;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .user-dropdown a {
            display: block;
            padding: 0.8rem 1.2rem;
            color: var(--dark);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
        }
        
        .user-dropdown a:hover {
            background: #f5f5f5;
            color: var(--primary);
        }
        .cart-btn {
    position: relative;
    color: var(--white);
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: opacity 0.3s;
    padding: 0.5rem;
}

.cart-btn:hover {
    opacity: 0.8;
}
        
        /* Banner */
        .banner {
            position: relative;
            width: 100%;
            height: 400px;
            overflow: hidden;
        }
        
        .banner-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            opacity: 0.8;
        }
        
        .banner-content {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: var(--white);
            background: linear-gradient(to bottom, rgba(0,0,0,0.2), rgba(0,0,0,0.6));
            text-align: center;
            padding: 2rem;
        }
        
        .banner-title {
            font-size: 3rem;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 8px rgba(0,0,0,0.5);
            animation: slideUp 1s ease;
        }
        
        .banner-subtitle {
            font-size: 1.5rem;
            max-width: 800px;
            margin-bottom: 2rem;
            text-shadow: 1px 1px 4px rgba(0,0,0,0.5);
            animation: slideUp 1s ease 0.2s forwards;
            opacity: 0;
        }
        
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Main Content */
        .main-content {
            max-width: 1400px;
            margin: 4rem auto;
            padding: 0 2rem;
        }
        
        .section-title {
            text-align: center;
            color: var(--primary);
            margin-bottom: 1rem;
            font-size: 2.2rem;
            position: relative;
            display: block;
            width: 100%;
        }
        
        .section-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: var(--primary);
            border-radius: 2px;
        }
        
        .teams-intro {
            text-align: center;
            margin-bottom: 3rem;
            max-width: 100%;
        }
        
        .teams-intro p {
            text-align: center;
            max-width: 800px;
            margin: 20px auto;
            font-size: 1.1rem;
            color: var(--dark);
        }
        
        .team-group {
            margin-bottom: 3rem;
            background: var(--white);
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 6px 15px rgba(0,0,0,0.1);
        }
        
        .group-title {
            color: var(--primary);
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
            padding-bottom: 0.8rem;
            border-bottom: 2px solid var(--primary);
        }
        
        .teams-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1.5rem;
        }
        
        .teams-table th {
            background-color: var(--primary);
            color: var(--white);
            padding: 1rem;
            text-align: left;
            font-weight: 500;
        }
        
        .teams-table td {
            padding: 1rem;
            border-bottom: 1px solid #eee;
        }
        
        .teams-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .teams-table tr:hover {
            background-color: #f1f1f1;
        }
        
        .flag-img {
            width: 40px;
            height: 25px;
            object-fit: cover;
            margin-right: 10px;
            vertical-align: middle;
            border-radius: 2px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.2);
        }
        
        /* Footer */
        footer {
            background: var(--secondary);
            color: var(--white);
            padding: 4rem 0 0;
            margin-top: 6rem;
        }
        
        .footer-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 2rem;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 3rem;
        }
        
        .footer-logo {
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        
        .footer-logo img {
            height: 40px;
            margin-right: 1rem;
        }
        
        .footer-logo h2 {
            font-size: 1.3rem;
            font-weight: 600;
        }
        
        .footer-about p {
            margin-bottom: 1.5rem;
            font-size: 0.95rem;
            opacity: 0.8;
            line-height: 1.7;
        }
        
        .social-links {
            display: flex;
            gap: 1rem;
        }
        
        .social-links a {
            color: var(--white);
            font-size: 1.2rem;
            transition: all 0.3s;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255,255,255,0.1);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .social-links a:hover {
            background: var(--primary);
            transform: translateY(-3px);
        }
        
        .footer-section h3 {
            font-size: 1.2rem;
            margin-bottom: 1.5rem;
            position: relative;
            padding-bottom: 0.8rem;
        }
        
        .footer-section h3::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 50px;
            height: 3px;
            background: var(--primary);
        }
        
        .footer-links ul {
            list-style: none;
        }
        
        .footer-links li {
            margin-bottom: 0.8rem;
        }
        
        .footer-links a {
            color: var(--white);
            text-decoration: none;
            font-size: 0.95rem;
            opacity: 0.8;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .footer-links a:hover {
            opacity: 1;
            color: var(--primary);
            transform: translateX(5px);
        }
        
        .footer-links i {
            font-size: 0.8rem;
        }
        
        .footer-contact p {
            display: flex;
            align-items: flex-start;
            margin-bottom: 1.2rem;
            font-size: 0.95rem;
            opacity: 0.8;
            line-height: 1.6;
            gap: 1rem;
        }
        
        .footer-contact i {
            color: var(--primary);
            margin-top: 0.2rem;
        }
        
        .copyright {
            text-align: center;
            padding: 1.5rem 0;
            margin-top: 3rem;
            border-top: 1px solid rgba(255,255,255,0.1);
            font-size: 0.9rem;
            opacity: 0.7;
        }
        
        /* Responsive */
        @media (max-width: 992px) {
            .banner {
                height: 350px;
            }
            
            .banner-title {
                font-size: 2.5rem;
            }
            
            .banner-subtitle {
                font-size: 1.2rem;
            }
        }
        
        @media (max-width: 768px) {
            .header {
                height: auto;
                padding: 1rem;
                flex-direction: column;
                gap: 1rem;
            }
            
            .nav-links {
                gap: 1rem;
                flex-wrap: wrap;
                justify-content: center;
            }
            
            .header-actions {
                gap: 1rem;
                flex-wrap: wrap;
                justify-content: center;
            }
            
            .banner {
                height: 300px;
            }
            
            .banner-title {
                font-size: 2rem;
            }
            
            .section-title {
                font-size: 1.8rem;
            }
            
            .teams-table {
                display: block;
                overflow-x: auto;
            }
        }
        
        @media (max-width: 576px) {
            .banner {
                height: 250px;
            }
            
            .banner-title {
                font-size: 1.8rem;
            }
            
            .banner-subtitle {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-container">
            <div class="header-nav">
                <span class="logo" style="font-size: small; font-family: 'Courier New', monospace; font-weight: bold; font-style: italic;">
                    AFRIKA TICKET
                </span>
                <nav class="nav-links">
                    <a href="index.php" class="nav-link">ACCUEIL</a>
                    <a href="billets.php" class="nav-link">Matchs</a>
                    <a href="equipes.php" class="nav-link active">ÉQUIPES</a>
                    <a href="stades.php" class="nav-link">STADES</a>
                    <a href="activites.php" class="nav-link">ACTIVITES</a>
                </nav>
            </div>
            <div class="header-actions">
                <a href="panier.php" class="cart-btn action-link" style="margin-right: 15px;">
                    <i class="fas fa-shopping-cart"></i>
        
                 </a>
                <a href="#" class="action-link">
                    <i class="fas fa-phone"></i> +212 667 570 800
                </a>
                <?php if ($user_info): ?>
                    <div class="user-menu">
                        <button id="user-btn" class="user-btn">
                            <i class="fas fa-user"></i> <?php echo htmlspecialchars($user_info['prenom']); ?>
                        </button>
                        <div id="user-dropdown" class="user-dropdown">
                            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
                        </div>
                    </div>
                    <script>
                    const userBtn = document.getElementById('user-btn');
                    const userDropdown = document.getElementById('user-dropdown');
                    userBtn.onclick = function(e) {
                        e.stopPropagation();
                        userDropdown.style.display = userDropdown.style.display === 'block' ? 'none' : 'block';
                    };
                    document.addEventListener('click', function(e) {
                        if (!userBtn.contains(e.target)) userDropdown.style.display = 'none';
                    });
                    </script>
                <?php else: ?>
                    <a href="auth.php" class="action-link">
                        <i class="fas fa-lock"></i> S'authentifier
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </header>
    
    <div class="banner">
        <img src="images/can1.jpg" alt="CAN 2025" class="banner-img">
        <div class="banner-content">
            <h1 class="banner-title">Les Équipes de la CAN 2025</h1>
            <p class="banner-subtitle">Découvrez toutes les équipes participantes à la Coupe d'Afrique des Nations 2025</p>
        </div>
    </div>
    
    <div class="main-content">
        <div class="teams-intro">
            <h2 class="section-title">LES ÉQUIPES PARTICIPANTES</h2>
            <p>La Coupe d'Afrique des Nations 2025 réunit les meilleures sélections africaines. Découvrez ci-dessous les informations détaillées sur chaque équipe participant au tournoi.</p>
        </div>
        
        <?php foreach ($groupes as $lettre => $range): ?>
            <?php
            $min = $range[0];
            $max = $range[1];
            ?>
            <div class="team-group">
                <h3 class="group-title">GROUPE <?= $lettre ?></h3>
                <table class="teams-table">
                    <thead>
                        <tr>
                            <th>Équipe</th>
                            <th>Sélectionneur</th>
                            <th>Classement FIFA</th>
                            <th>Classement CAF</th>
                            <th>Titres CAN</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($equipes as $equipe): ?>
                            <?php if ($equipe['id_equipe'] >= $min && $equipe['id_equipe'] <= $max): ?>
                            <tr>
                                <td>
                                    <?php if (!empty($equipe['image'])): ?>
                                        <img src="<?= htmlspecialchars($equipe['image']) ?>" alt="<?= htmlspecialchars($equipe['nom']) ?>" class="flag-img">
                                    <?php endif; ?>
                                    <?= htmlspecialchars($equipe['nom']) ?>
                                </td>
                                <td><?= htmlspecialchars($equipe['selectionneur']) ?></td>
                                <td><?= htmlspecialchars($equipe['classement_fifa']) ?>ᵉ</td>
                                <td><?= htmlspecialchars($equipe['classement_caf']) ?>ᵉ</td>
                                <td><?= htmlspecialchars($equipe['nb_titres_can']) ?></td>
                            </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endforeach; ?>
    </div>
    
    <footer>
        <div class="footer-container">
            <div class="footer-about">
                <div class="footer-logo">
                    <span class="logo" style="font-size: small; font-family: 'Courier New', monospace; font-weight: bold; font-style: italic;">
                        AFRIKA TICKET
                    </span>
                    <h2>CAN 2025</h2>
                </div>
                <p>Votre plateforme officielle pour l'achat de billets de la Coupe d'Afrique des Nations 2026. Vivez l'émotion du football africain.</p>
                <div class="social-links">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-youtube"></i></a>
                </div>
            </div>
            
            <div class="footer-section">
                <h3>Liens rapides</h3>
                <ul class="footer-links">
                    <li><a href="index.php"><i class="fas fa-chevron-right"></i> Accueil</a></li>
                    <li><a href="billets.php"><i class="fas fa-chevron-right"></i> Matchs</a></li>
                    <li><a href="equipes.php"><i class="fas fa-chevron-right"></i> Équipes</a></li>
                    <li><a href="stades.php"><i class="fas fa-chevron-right"></i> Stades</a></li>
                    <li><a href="activites.php"><i class="fas fa-chevron-right"></i> Activités</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h3>Informations</h3>
                <ul class="footer-links">
                    <li><a href="panier.php"><i class="fas fa-chevron-right"></i>Mes Commandes</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h3>Contact</h3>
                <div class="footer-contact">
                    <p><i class="fas fa-map-marker-alt"></i> Complexe Mohamed Benjelloun, Avenue des sports, Casablanca</p>
                    <p><i class="fas fa-phone"></i> SAV: 0703177060</p>
                    <p><i class="fas fa-phone-alt"></i> Fixe: 05 22 99 38 00</p>
                    <p><i class="fas fa-envelope"></i> contact@can2025ticketing.com</p>
                </div>
            </div>
        </div>
        
        <div class="copyright">
            <p>Copyright © 2024 CAN 2026 TICKETING. Tous droits réservés.</p>
        </div>
    </footer>
</body>
</html>