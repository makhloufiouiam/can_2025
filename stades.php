<?php
session_start();
require_once 'config.php';

// Récupérer tous les stades depuis la base de données
$sql = "SELECT * FROM stades";
$stades = $pdo->query($sql)->fetchAll();

// Récupérer les matchs à venir pour chaque stade
foreach ($stades as &$stade) {
    $sql = "SELECT date_match, heure_match, equipe1, equipe2 
            FROM matchs 
            WHERE id_stade = ? AND date_match >= CURDATE() 
            ORDER BY date_match ASC 
            LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$stade['id_stade']]);
    $stade['prochain_match'] = $stmt->fetch();
}
unset($stade); // Détruire la référence

$user_info = $_SESSION['user_info'] ?? null;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carte des Stades - CAN 2026</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
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
        
        /* Header - Style comme la page d'accueil */
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
        
        /* Contenu principal */
        .main-content {
            display: flex;
            max-width: 1400px;
            margin: 20px auto;
            padding: 0 20px;
            gap: 20px;
        }
        
        .map-container {
            flex: 2;
            height: calc(100vh - 120px);
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }
        
        #map {
            height: 100%;
            width: 100%;
        }
        
        .stades-list {
            flex: 1;
            max-height: calc(100vh - 120px);
            overflow-y: auto;
            padding-right: 10px;
        }
        
        .stade-card {
            background: #fff;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            cursor: pointer;
            transition: transform 0.3s;
        }
        
        .stade-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        
        .stade-card.active {
            border-left: 4px solid var(--primary);
        }
        
        .stade-img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 4px;
            margin-bottom: 10px;
        }
        
        .stade-name {
            font-weight: 700;
            margin-bottom: 5px;
            color: var(--primary);
        }
        
        .stade-info {
            color: var(--gray);
            font-size: 0.9rem;
            margin-bottom: 5px;
        }
        
        .match-status {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: 500;
            margin-top: 5px;
        }
        
        .no-match {
            background: #f0f0f0;
            color: #666;
        }
        
        .match-planned {
            background: #d4edda;
            color: #155724;
        }
        
        .search-container {
            margin-bottom: 20px;
            position: relative;
        }
        
        #search-input {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }
        
        .search-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray);
        }
        
        .leaflet-popup-content {
            min-width: 250px;
        }
        
        .popup-stade-img {
            width: 100%;
            height: 120px;
            object-fit: cover;
            margin-bottom: 10px;
        }
        
        /* Footer - Style comme la page d'accueil */
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
            
            .main-content {
                flex-direction: column;
                padding: 0 10px;
            }
            
            .map-container, .stades-list {
                width: 100%;
            }
            
            .map-container {
                height: 400px;
            }
            
            .stades-list {
                max-height: none;
            }
        }
        
        @media (max-width: 576px) {
            .banner {
                height: 250px;
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
                    <a href="stades.php" class="nav-link active">STADES</a>
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
   
    <div class="main-content">
        <div class="stades-list">
            <div class="search-container">
                <input type="text" id="search-input" placeholder="Rechercher un stade...">
                <span class="search-icon"><i class="fas fa-search"></i></span>
            </div>
            
            <?php foreach ($stades as $stade): ?>
                <div class="stade-card" 
                     data-stade-id="<?= $stade['id_stade'] ?>" 
                     data-lat="<?= $stade['latitude'] ?>" 
                     data-lng="<?= $stade['longitude'] ?>">
                    <?php if (!empty($stade['image_stade'])): ?>
                        <img src="<?= $stade['image_stade'] ?>" alt="<?= htmlspecialchars($stade['nom_stade']) ?>" class="stade-img">
                    <?php endif; ?>
                    <div class="stade-name"><?= htmlspecialchars($stade['nom_stade']) ?></div>
                    <div class="stade-info"><?= htmlspecialchars($stade['ville']) ?></div>
                    <div class="stade-info">Capacité: <?= number_format($stade['capacite'], 0, ',', ' ') ?> places</div>
                    
                    <?php if (!empty($stade['prochain_match'])): ?>
                        <div class="match-status match-planned">
                            Prochain match: <?= htmlspecialchars($stade['prochain_match']['date_match']) ?> à <?= htmlspecialchars($stade['prochain_match']['heure_match']) ?>
                            <br><?= htmlspecialchars($stade['prochain_match']['equipe1']) ?> vs <?= htmlspecialchars($stade['prochain_match']['equipe2']) ?>
                        </div>
                    <?php else: ?>
                        <div class="match-status no-match">Aucun match prévu</div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="map-container">
            <div id="map"></div>
        </div>
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

    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script>
        // Initialiser la carte
        const map = L.map('map').setView([31.7917, -7.0926], 6); // Centrer sur le Maroc
        
        // Ajouter la couche OpenStreetMap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);
        
        // Créer un groupe de marqueurs
        const markers = L.layerGroup().addTo(map);
        
        // Ajouter les marqueurs pour chaque stade
        const stadesData = [
            <?php foreach ($stades as $stade): ?>
            {
                id: <?= $stade['id_stade'] ?>,
                name: "<?= addslashes($stade['nom_stade']) ?>",
                ville: "<?= addslashes($stade['ville']) ?>",
                capacite: <?= $stade['capacite'] ?>,
                image: "<?= $stade['image_stade'] ?>",
                lat: <?= $stade['latitude'] ?>,
                lng: <?= $stade['longitude'] ?>,
                match: <?= !empty($stade['prochain_match']) ? 
                    json_encode([
                        'date' => $stade['prochain_match']['date_match'],
                        'heure' => $stade['prochain_match']['heure_match'],
                        'equipes' => $stade['prochain_match']['equipe1'] . ' vs ' . $stade['prochain_match']['equipe2']
                    ]) : 'null' ?>
            },
            <?php endforeach; ?>
        ];
        
        // Ajouter les marqueurs à la carte
        stadesData.forEach(stade => {
            const marker = L.marker([stade.lat, stade.lng]).addTo(markers);
            
            let popupContent = `
                <div style="text-align:center;">
                    <h3 style="margin:0 0 10px 0;color:#b71c1c;">${stade.name}</h3>
            `;
            
            if (stade.image) {
                popupContent += `<img src="${stade.image}" class="popup-stade-img" alt="${stade.name}">`;
            }
            
            popupContent += `
                <p><strong>Ville:</strong> ${stade.ville}</p>
                <p><strong>Capacité:</strong> ${stade.capacite.toLocaleString()} places</p>
            `;
            
            if (stade.match) {
                popupContent += `
                    <div style="background:#d4edda;padding:8px;border-radius:4px;margin-top:10px;">
                        <p style="margin:0;"><strong>Prochain match:</strong></p>
                        <p style="margin:5px 0 0 0;">${stade.match.date} à ${stade.match.heure}</p>
                        <p style="margin:5px 0 0 0;">${stade.match.equipes}</p>
                    </div>
                `;
            } else {
                popupContent += `
                    <div style="background:#f0f0f0;padding:8px;border-radius:4px;margin-top:10px;">
                        <p style="margin:0;">Aucun match prévu</p>
                    </div>
                `;
            }
            
            popupContent += `
                <div style="margin-top:15px;">
                    <a href="https://www.google.com/maps/dir/?api=1&destination=${stade.lat},${stade.lng}" 
                       target="_blank" 
                       style="background:#b71c1c;color:white;padding:8px 12px;border-radius:4px;text-decoration:none;">
                        Itinéraire
                    </a>
                </div>
            `;
            
            marker.bindPopup(popupContent);
            
            // Associer le marqueur à la carte stade
            marker.stadeId = stade.id;
        });
        
        // Gestion de la recherche
        document.getElementById('search-input').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const stadeCards = document.querySelectorAll('.stade-card');
            
            stadeCards.forEach(card => {
                const stadeName = card.querySelector('.stade-name').textContent.toLowerCase();
                const stadeVille = card.querySelector('.stade-info').textContent.toLowerCase();
                
                if (stadeName.includes(searchTerm) || stadeVille.includes(searchTerm)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
        
        // Interaction entre les cartes stade et les marqueurs
        document.querySelectorAll('.stade-card').forEach(card => {
            card.addEventListener('click', function() {
                const stadeId = this.getAttribute('data-stade-id');
                const lat = parseFloat(this.getAttribute('data-lat'));
                const lng = parseFloat(this.getAttribute('data-lng'));
                
                // Centrer la carte sur ce stade
                map.setView([lat, lng], 14);
                
                // Ouvrir le popup du marqueur correspondant
                markers.eachLayer(marker => {
                    if (marker.stadeId == stadeId) {
                        marker.openPopup();
                    }
                });
                
                // Mettre en évidence la carte stade
                document.querySelectorAll('.stade-card').forEach(c => {
                    c.classList.remove('active');
                });
                this.classList.add('active');
            });
        });
    </script>
</body>
</html>