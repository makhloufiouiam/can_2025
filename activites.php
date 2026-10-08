<?php
session_start();

// Données des stades
$stades = [
    [
        'id' => 1,
        'nom' => 'Stade Prince Moulay Abdellah',
        'ville' => 'Rabat',
        'image' => 'images/moulay_abdellah.jpg'
    ],
    [
        'id' => 2,
        'nom' => 'Stade Mohammed V',
        'ville' => 'Casablanca',
        'image' => 'images/mohammed_v.jpg'
    ],
    [
        'id' => 3,
        'nom' => 'Grand stade d\'Agadir',
        'ville' => 'Agadir',
        'image' => 'images/agadir.jpg'
    ],
    [
        'id' => 4,
        'nom' => 'Grand stade de Marrakech',
        'ville' => 'Marrakech',
        'image' => 'images/marrakech.jpg'
    ],
    [
        'id' => 5,
        'nom' => 'Stade Ibn Battouta',
        'ville' => 'Tanger',
        'image' => 'images/tanger.jpg'
    ]
];

// Données des activités
$activites = [
    'transport' => [
        // Rabat
        [
            'id' => 1,
            'titre' => 'Gare Rabat–Agdal (ONCF)',
            'description' => 'Liaisons vers Casablanca (Al Boraq - 1h, 2ème classe: 95 MAD, 1ère classe: 140 MAD), Fès (2h30, 2ème classe: 85 MAD), Tanger (2h15, 2ème classe: 120 MAD)',
            'prix' => 85,
            'image' => 'images/oncf.jpg',
            'stade_id' => 1,
            'ville' => 'Rabat',
            'lien' => 'https://www.oncf.ma/fr/gares/Rabat-Agdal'
        ],
        [
            'id' => 2,
            'titre' => 'Tramway Rabat–Salé',
            'description' => 'Station Agdal - Transport urbain rapide (Ticket unique: 6 MAD, Pass journée: 20 MAD)',
            'prix' => 6,
            'image' => 'images/tram.jpg',
            'stade_id' => 1,
            'ville' => 'Rabat',
            'lien' => 'https://www.tram-way.ma/'
        ],
        // Casablanca
        [
            'id' => 3,
            'titre' => 'Gare Casa-Voyageurs (ONCF)',
            'description' => 'Liaisons régionales et nationales (Al Boraq vers Rabat - 1h, 2ème classe: 95 MAD, 1ère classe: 140 MAD)',
            'prix' => 95,
            'image' => 'images/casa_voyageurs.jpg',
            'stade_id' => 2,
            'ville' => 'Casablanca',
            'lien' => 'https://www.oncf.ma/fr/gares/Casablanca-Voyageurs'
        ],
        [
            'id' => 4,
            'titre' => 'Tramway Casa Tram',
            'description' => 'Transport urbain moderne',
            'prix' => 6,
            'image' => 'images/casa_tram.jpg',
            'stade_id' => 2,
            'ville' => 'Casablanca',
            'lien' => 'https://www.casatramway.ma/'
        ],
        // Tanger
        [
            'id' => 5,
            'titre' => 'Gare Tanger-Ville (ONCF)',
            'description' => 'Liaisons nationales',
            'prix' => 0,
            'image' => 'images/tanger_gare.jpg',
            'stade_id' => 5,
            'ville' => 'Tanger',
            'lien' => 'https://www.oncf.ma/fr/gares/Tanger-Ville'
        ],
        // Agadir
        [
            'id' => 6,
            'titre' => 'Bus CTM/Supratours',
            'description' => 'Liaisons inter-villes',
            'prix' => 150,
            'image' => 'images/ctm.webp',
            'stade_id' => 3,
            'ville' => 'Agadir',
            'lien' => 'https://www.ctm.ma/'
        ],
        // Marrakech
        [
            'id' => 7,
            'titre' => 'Gare de Marrakech (ONCF)',
            'description' => 'Liaisons nationales',
            'prix' => 0,
            'image' => 'images/marrakech_gare.jpg',
            'stade_id' => 4,
            'ville' => 'Marrakech',
            'lien' => 'https://www.oncf.ma/fr/gares/Marrakech'
        ]
    ],
    'culture' => [
        // Rabat
        [
            'id' => 8,
            'titre' => 'Musée Mohammed VI d\'art moderne',
            'description' => 'Art moderne et contemporain marocain',
            'prix' => 50,
            'image' => 'images/musee_m6.jpg',
            'stade_id' => 1,
            'ville' => 'Rabat',
            'lien' => 'https://www.museemohammed6.ma/'
        ],
        [
            'id' => 9,
            'titre' => 'Kasbah des Oudayas',
            'description' => 'Site historique et vue panoramique',
            'prix' => 0,
            'image' => 'images/oudayas.jpg',
            'stade_id' => 1,
            'ville' => 'Rabat',
            'lien' => 'https://www.visitmorocco.com/fr/lieu/kasbah-des-oudayas'
        ],
        // Casablanca
        [
            'id' => 10,
            'titre' => 'Mosquée Hassan II',
            'description' => 'Plus grande mosquée du Maroc',
            'prix' => 130,
            'image' => 'images/hassan2.jpg',
            'stade_id' => 2,
            'ville' => 'Casablanca',
            'lien' => 'https://www.visitmorocco.com/fr/lieu/mosquee-hassan-ii'
        ],
        // Tanger
        [
            'id' => 11,
            'titre' => 'Musée de la Kasbah',
            'description' => 'Histoire et culture de Tanger',
            'prix' => 20,
            'image' => 'images/kasbah_tanger.jpg',
            'stade_id' => 5,
            'ville' => 'Tanger',
            'lien' => 'https://www.visitmorocco.com/fr/lieu/kasbah-de-tanger'
        ],
        // Agadir
        [
            'id' => 12,
            'titre' => 'Musée Amazigh',
            'description' => 'Patrimoine culturel amazigh',
            'prix' => 20,
            'image' => 'images/musee_amazigh.jpg',
            'stade_id' => 3,
            'ville' => 'Agadir',
            'lien' => 'https://www.visitmorocco.com/fr/lieu/musee-du-patrimoine-amazigh'
        ],
        // Marrakech
        [
            'id' => 13,
            'titre' => 'Jardin Majorelle & Musée YSL',
            'description' => 'Jardin botanique et musée de mode',
            'prix' => 100,
            'image' => 'images/majorelle.jpg',
            'stade_id' => 4,
            'ville' => 'Marrakech',
            'lien' => 'https://www.jardinmajorelle.com/'
        ]
    ],
    'divertissement' => [
        // Rabat
        [
            'id' => 14,
            'titre' => 'Marina Bouregreg',
            'description' => 'Promenade, restaurants, bateaux',
            'prix' => 0,
            'image' => 'images/marina_bouregreg.jpg',
            'stade_id' => 1,
            'ville' => 'Rabat',
            'lien' => 'https://www.bouregreg.com/'
        ],
        // Casablanca
        [
            'id' => 15,
            'titre' => 'Morocco Mall',
            'description' => 'Shopping et aquarium',
            'prix' => 0,
            'image' => 'images/morocco_mall.jpg',
            'stade_id' => 2,
            'ville' => 'Casablanca',
            'lien' => 'https://www.moroccomall.ma/'
        ],
        // Tanger
        [
            'id' => 16,
            'titre' => 'Mnar Park Aqua Leisure',
            'description' => 'Aqua park, karting, paintball',
            'prix' => 150,
            'image' => 'images/mnar_park.jpg',
            'stade_id' => 5,
            'ville' => 'Tanger',
            'lien' => 'https://www.mnarpark.com/'
        ],
        // Agadir
        [
            'id' => 17,
            'titre' => 'Crocoparc Agadir',
            'description' => 'Parc de crocodiles et jardin botanique',
            'prix' => 100,
            'image' => 'images/crocoparc.jpg',
            'stade_id' => 3,
            'ville' => 'Agadir',
            'lien' => 'http://www.crocoparc.com/'
        ],
        // Marrakech
        [
            'id' => 18,
            'titre' => 'Oasiria Parc Aquatique',
            'description' => 'Parc aquatique avec piscines et toboggans',
            'prix' => 200,
            'image' => 'images/oasiria.jpg',
            'stade_id' => 4,
            'ville' => 'Marrakech',
            'lien' => 'https://oasiria.com/'
        ]
    ]
];

// Récupérer le prénom de l'utilisateur connecté (simulé)
$user_prenom = isset($_SESSION['user_prenom']) ? $_SESSION['user_prenom'] : null;
$user_info = isset($_SESSION['user_info']) ? $_SESSION['user_info'] : null;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Activités - CAN 2025</title>
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
            font-size: 2.5rem;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 8px rgba(0,0,0,0.5);
            animation: slideUp 1s ease;
        }
        
        .banner-subtitle {
            font-size: 1.2rem;
            max-width: 700px;
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
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }
        
        .section-title {
            text-align: center;
            color: var(--primary);
            margin-bottom: 3rem;
            font-size: 2.2rem;
            position: relative;
            display: inline-block;
            left: 50%;
            transform: translateX(-50%);
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
        
        .stadium-selector {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            margin-bottom: 30px;
            font-size: 16px;
            font-family: 'Poppins', sans-serif;
        }
        
        .activities-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 25px;
            margin-top: 30px;
        }
        
        .activity-card {
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 6px 15px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            position: relative;
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        
        .activity-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 12px 25px rgba(0,0,0,0.15);
        }
        
        .activity-img {
            width: 100%;
            height: 160px;
            object-fit: cover;
            transition: transform 0.5s;
        }
        
        .activity-card:hover .activity-img {
            transform: scale(1.05);
        }
        
        .activity-details {
            padding: 20px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }
        
        .activity-title {
            font-weight: 600;
            margin-bottom: 10px;
            font-size: 1.1rem;
            color: var(--dark);
        }
        
        .activity-info {
            color: var(--gray);
            font-size: 0.9rem;
            margin-bottom: 8px;
        }
        
        .activity-price {
            font-weight: bold;
            color: #d4af37;
            margin: 10px 0;
        }
        
        .btn {
            display: inline-block;
            background: var(--primary);
            color: var(--white);
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            text-align: center;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
            font-family: 'Poppins', sans-serif;
        }
        
        .btn:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(183, 28, 28, 0.3);
        }
        
        .btn-block {
            display: block;
            width: 100%;
            margin-top: auto;
        }
        
        .btn-large {
            padding: 12px 30px;
            font-size: 1.1rem;
        }
        
        .text-center {
            text-align: center;
        }
        
        .mt-30 {
            margin-top: 30px;
        }
        
        /* Footer */
        footer {
            background: var(--secondary);
            color: var(--white);
            padding: 4rem 0 0;
            margin-top: 50px;
        }
        
        .footer-container {
            max-width: 1200px;
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
                    <a href="equipes.php" class="nav-link">ÉQUIPES</a>
                    <a href="stades.php" class="nav-link">STADES</a>
                    <a href="activites.php" class="nav-link active">ACTIVITES</a>
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
        <img src="images/can1.jpg" alt="Activités CAN 2025" class="banner-img">
        <div class="banner-content">
            <h1 class="banner-title">Activités pour les visiteurs</h1>
            <p class="banner-subtitle">Découvrez toutes les activités disponibles autour des stades marocains</p>
        </div>
    </div>
    
    <div class="main-content">
        <h2 class="section-title">ACTIVITÉS DISPONIBLES</h2>
        
        <select class="stadium-selector" id="stadium-select">
            <option value="">-- Tous les stades --</option>
            <?php foreach ($stades as $stade): ?>
                <option value="<?= $stade['id'] ?>"><?= $stade['nom'] ?> (<?= $stade['ville'] ?>)</option>
            <?php endforeach; ?>
        </select>
        
        <div class="section" id="transport">
            <h2 class="section-title">Transport</h2>
            <div class="activities-grid">
                <?php foreach ($activites['transport'] as $activity): ?>
                    <div class="activity-card" data-stadium="<?= $activity['stade_id'] ?>" data-ville="<?= $activity['ville'] ?>">
                        <img src="<?= $activity['image'] ?>" alt="<?= $activity['titre'] ?>" class="activity-img">
                        <div class="activity-details">
                            <h3 class="activity-title"><?= $activity['titre'] ?></h3>
                            <p class="activity-info"><?= $activity['ville'] ?></p>
                            <p class="activity-info"><?= substr($activity['description'], 0, 100) ?>...</p>
                            <div class="activity-price"><?= $activity['prix'] > 0 ? 'À partir de ' . $activity['prix'] . ' MAD' : 'Gratuit' ?></div>
                            <a href="<?= $activity['lien'] ?>" target="_blank" class="btn btn-block">En savoir plus</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <div class="section" id="culture">
            <h2 class="section-title">Activités Culturelles</h2>
            <div class="activities-grid">
                <?php foreach ($activites['culture'] as $activity): ?>
                    <div class="activity-card" data-stadium="<?= $activity['stade_id'] ?>" data-ville="<?= $activity['ville'] ?>">
                        <img src="<?= $activity['image'] ?>" alt="<?= $activity['titre'] ?>" class="activity-img">
                        <div class="activity-details">
                            <h3 class="activity-title"><?= $activity['titre'] ?></h3>
                            <p class="activity-info"><?= $activity['ville'] ?></p>
                            <p class="activity-info"><?= substr($activity['description'], 0, 100) ?>...</p>
                            <div class="activity-price"><?= $activity['prix'] > 0 ? 'À partir de ' . $activity['prix'] . ' MAD' : 'Gratuit' ?></div>
                            <a href="<?= $activity['lien'] ?>" target="_blank" class="btn btn-block">En savoir plus</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <div class="section" id="divertissement">
            <h2 class="section-title">Divertissement</h2>
            <div class="activities-grid">
                <?php foreach ($activites['divertissement'] as $activity): ?>
                    <div class="activity-card" data-stadium="<?= $activity['stade_id'] ?>" data-ville="<?= $activity['ville'] ?>">
                        <img src="<?= $activity['image'] ?>" alt="<?= $activity['titre'] ?>" class="activity-img">
                        <div class="activity-details">
                            <h3 class="activity-title"><?= $activity['titre'] ?></h3>
                            <p class="activity-info"><?= $activity['ville'] ?></p>
                            <p class="activity-info"><?= substr($activity['description'], 0, 100) ?>...</p>
                            <div class="activity-price"><?= $activity['prix'] > 0 ? 'À partir de ' . $activity['prix'] . ' MAD' : 'Gratuit' ?></div>
                            <a href="<?= $activity['lien'] ?>" target="_blank" class="btn btn-block">En savoir plus</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    
    <footer>
        <div class="footer-container">
            <div class="footer-about">
                <div class="footer-logo">
                    <span class="logo" style="font-size: small; font-family: 'Courier New', monospace; font-weight: bold; font-style: italic;">
                        Football ticket
                    </span>
                    <h2>CAN 2025</h2>
                </div>
                <p>Votre plateforme officielle pour l'achat de billets de la Coupe d'Afrique des Nations 2025. Vivez l'émotion du football africain.</p>
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
                    <li><a href="panier.php"><i class="fas fa-chevron-right"></i> Mes Commandes</a></li>
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
            <p>Copyright © 2024 CAN 2025 TICKETING. Tous droits réservés.</p>
        </div>
    </footer>

    <script>
        // Filtrage par stade
        document.getElementById('stadium-select').addEventListener('change', function() {
            const selectedStadium = this.value;
            const allActivities = document.querySelectorAll('.activity-card');
            
            allActivities.forEach(activity => {
                const activityStadium = activity.getAttribute('data-stadium');
                const matchesStadium = !selectedStadium || activityStadium === selectedStadium;
                
                if (matchesStadium) {
                    activity.style.display = 'block';
                } else {
                    activity.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>