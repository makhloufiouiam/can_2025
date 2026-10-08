<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: auth.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$user_info = $_SESSION['user_info'] ?? null;

// Si on veut payer un billet spécifique
if (isset($_GET['id_billet'])) {
    $id_billet = intval($_GET['id_billet']);
    $stmt = $pdo->prepare("SELECT b.*, m.equipe1, m.equipe2, m.date_match, m.heure_match, s.nom_stade, s.ville FROM billets b JOIN matchs m ON b.id_match = m.id_match JOIN stades s ON m.id_stade = s.id_stade WHERE b.id_billet = ? AND b.user_id = ?");
    $stmt->execute([$id_billet, $user_id]);
    $billet = $stmt->fetch();
    if ($billet && $billet['etat'] !== 'payé') {
       
        ?>
        <!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <title>Paiement du billet</title>
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
                
                body { 
                    font-family: 'Poppins', sans-serif; 
                    background: var(--light); 
                    color: var(--dark);
                }
                
                .container { 
                    max-width: 600px; 
                    margin: 40px auto; 
                    background: var(--white); 
                    border-radius: 10px; 
                    box-shadow: 0 2px 8px rgba(0,0,0,0.1); 
                    padding: 32px; 
                }
                
                h2 { 
                    color: var(--primary); 
                    text-align: center; 
                    margin-bottom: 24px; 
                }
                
                .form-group { 
                    margin-bottom: 18px; 
                }
                
                .form-group label {
                    display: block;
                    margin-bottom: 5px;
                    font-weight: 500;
                }
                
                .form-group input,
                .form-group select {
                    width: 100%;
                    padding: 10px;
                    border: 1px solid #ddd;
                    border-radius: 4px;
                    font-family: 'Poppins', sans-serif;
                }
                
                .btn { 
                    background: var(--primary); 
                    color: var(--white); 
                    border: none; 
                    padding: 10px 24px; 
                    border-radius: 4px; 
                    font-size: 1rem; 
                    cursor: pointer; 
                    margin: 8px 0; 
                    text-decoration: none; 
                    display: inline-block; 
                    transition: all 0.3s;
                }
                
                .btn:hover { 
                    background: var(--primary-dark); 
                    transform: translateY(-2px);
                }
                
                .btn-secondary { 
                    background: var(--gray); 
                }
                
                .btn-secondary:hover { 
                    background: #777; 
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
            
            <div class="container">
                <h2>Paiement du billet</h2>
                <div class="match-info" style="text-align:center; margin-bottom:24px;">
                    <strong><?= htmlspecialchars($billet['equipe1']) ?> vs <?= htmlspecialchars($billet['equipe2']) ?></strong><br>
                    <?= htmlspecialchars($billet['date_match']) ?> à <?= htmlspecialchars($billet['heure_match']) ?><br>
                    <?= htmlspecialchars($billet['nom_stade']) ?>, <?= htmlspecialchars($billet['ville']) ?><br>
                    <span>Catégorie : <?= htmlspecialchars($billet['categorie']) ?> | Prix : <?= htmlspecialchars($billet['prix']) ?> DH</span>
                </div>
                <form id="paypal-form" method="post" onsubmit="return false;">
                    <div class="form-group">
                        <label for="cin">CIN</label>
                        <input type="text" name="cin" id="cin" required>
                    </div>
                    <div class="form-group">
                        <label for="mode_paiement">Mode de paiement</label>
                        <select name="mode_paiement" id="mode_paiement" required>
                            <option value="paypal">PayPal</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="numero_compte">Numéro de compte bancaire</label>
                        <input type="text" name="numero_compte" id="numero_compte">
                    </div>
                    <input type="hidden" name="id_billet" value="<?= $billet['id_billet'] ?>">
                    <input type="hidden" name="prix" value="<?= $billet['prix'] ?>">
                    <input type="hidden" name="categorie" value="<?= htmlspecialchars($billet['categorie']) ?>">
                    <input type="hidden" name="nombre_billets" value="<?= $billet['nombre_billets'] ?>">
                    <div id="paypal-button-container"></div>
                    <button type="button" id="btn-test" class="btn btn-secondary">
                        <i class="fas fa-check-circle"></i> Compléter (Test)
                    </button>
                </form>
                <script src="https://www.paypal.com/sdk/js?client-id=AXoBBj3PkcvIdwy9AKsks3wtMdK16AUv54btD7jEMwLqieySxIYuWbJla0EqRFOMg3L0B_QaAeI9CeAB&currency=EUR"></script>
                <script>
                document.getElementById('btn-test').onclick = function() {
                    const btn = this;
                    const originalText = btn.innerHTML;
                    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Traitement...';
                    btn.disabled = true;
                    var formData = new FormData(document.getElementById('paypal-form'));
                    fetch('api/billets/test_reservation.php?id_billet=<?= $billet['id_billet'] ?>', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const container = document.querySelector('.container');
                            const successHTML = `
                                <div class="success">
                                    <h3>🎉 Paiement confirmé !</h3>
                                    <p>Votre billet a été généré avec succès.</p>
                                </div>
                                <div style="text-align: center; margin: 20px 0;">
                                    <a href="${data.pdf_url}" class="btn" style="background: #dc3545; margin: 10px;" target="_blank">
                                        📄 Télécharger le PDF
                                    </a>
                                    <br><br>
                                    <a href="billets.php" class="btn" style="background: #6c757d;">
                                        ← Retour aux matchs
                                    </a>
                                </div>
                            `;
                            container.innerHTML = successHTML;
                        } else {
                            alert('Erreur: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Une erreur est survenue lors du paiement.');
                        btn.innerHTML = originalText;
                        btn.disabled = false;
                    });
                };
                paypal.Buttons({
                    createOrder: function(data, actions) {
                        return actions.order.create({
                            purchase_units: [{
                                amount: { value: '<?= $billet['prix'] ?>' }
                            }]
                        });
                    },
                    onApprove: function(data, actions) {
                        return actions.order.capture().then(function(details) {
                            var formData = new FormData(document.getElementById('paypal-form'));
                            formData.append('payer', 1);
                            formData.append('paypal_order_id', data.orderID);
                            fetch('payer_billet.php', {
                                method: 'POST',
                                body: formData
                            })
                            .then(response => response.text())
                            .then(data => {
                                if (data === 'success') {
                                    window.location.href = 'billets.php';
                                } else {
                                    alert('Erreur lors du paiement.');
                                }
                            });
                        });
                    }
                }).render('#paypal-button-container');
                </script>
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
        <?php
        exit();
    }
}


$sql = "SELECT b.*, m.equipe1, m.equipe2, m.date_match, m.heure_match, s.nom_stade, s.ville, s.image_stade
        FROM billets b
        JOIN matchs m ON b.id_match = m.id_match
        JOIN stades s ON m.id_stade = s.id_stade
        WHERE b.user_id = ?
        ORDER BY b.date_reservation DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);
$billets = $stmt->fetchAll();

// Calculer le total du panier et le nombre d'articles
$total_panier = 0;
$nombre_articles = 0;
foreach ($billets as $billet) {
    if ($billet['etat'] !== 'payé') {
        $total_panier += $billet['prix'];
        $nombre_articles += $billet['nombre_billets'];
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Panier | Football Tickets</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--light);
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
        
        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .header h1 {
            font-size: 2rem;
            color: var(--primary);
            font-weight: 600;
        }
        
        .cart-summary {
            background: var(--white);
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .summary-item {
            display: flex;
            align-items: center;
        }
        
        .summary-item i {
            font-size: 1.5rem;
            margin-right: 1rem;
            color: var(--primary);
        }
        
        .summary-text {
            font-size: 0.9rem;
            color: var(--gray);
        }
        
        .summary-value {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--dark);
        }
        
        .btn {
            display: inline-block;
            padding: 0.8rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            text-align: center;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
            font-size: 1rem;
        }
        
        .btn-primary {
            background-color: var(--primary);
            color: var(--white);
        }
        
        .btn-primary:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(183, 28, 28, 0.2);
        }
        
        .btn-danger {
            background-color: #dc3545;
            color: var(--white);
        }
        
        .btn-danger:hover {
            background-color: #c82333;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(220, 53, 69, 0.2);
        }
        
        .btn-outline {
            background-color: transparent;
            border: 1px solid var(--primary);
            color: var(--primary);
        }
        
        .btn-outline:hover {
            background-color: var(--primary);
            color: var(--white);
        }
        
        .ticket-card {
            background: var(--white);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            margin-bottom: 1.5rem;
            transition: transform 0.3s ease;
        }
        
        .ticket-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
        }
        
        .ticket-header {
            display: flex;
            align-items: center;
            padding: 1rem 1.5rem;
            background-color: var(--primary);
            color: var(--white);
        }
        
        .ticket-status {
            display: inline-block;
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
            margin-left: auto;
        }
        
        .status-pending {
            background-color: #ffc107;
            color: var(--dark);
        }
        
        .status-paid {
            background-color: #28a745;
            color: var(--white);
        }
        
        .ticket-body {
            display: flex;
            padding: 1.5rem;
        }
        
        .ticket-image {
            width: 150px;
            height: 150px;
            border-radius: 8px;
            object-fit: cover;
            margin-right: 1.5rem;
        }
        
        .ticket-details {
            flex: 1;
        }
        
        .match-teams {
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--dark);
        }
        
        .match-vs {
            color: var(--gray);
            font-weight: 400;
        }
        
        .match-info {
            display: flex;
            flex-wrap: wrap;
            gap: 1.5rem;
            margin-bottom: 1rem;
        }
        
        .info-item {
            display: flex;
            align-items: center;
        }
        
        .info-item i {
            margin-right: 0.5rem;
            color: var(--primary);
        }
        
        .ticket-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid #eee;
        }
        
        .ticket-price {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
        }
        
        .ticket-category {
            background-color: #fef0f1;
            color: var(--primary);
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 500;
        }
        
        .ticket-actions {
            display: flex;
            gap: 1rem;
        }
        
        .empty-cart {
            text-align: center;
            padding: 4rem 0;
        }
        
        .empty-cart i {
            font-size: 5rem;
            color: #e0e0e0;
            margin-bottom: 1.5rem;
        }
        
        .empty-cart h3 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            color: var(--dark);
        }
        
        .empty-cart p {
            color: var(--gray);
            margin-bottom: 1.5rem;
        }
        
        .checkout-section {
            background: var(--white);
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            margin-top: 2rem;
            text-align: right;
        }
        
        .total-amount {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 1.5rem;
        }
        
        /* Footer */
        footer {
            background-color: var(--secondary);
            color: var(--white);
            padding: 3rem 0;
            margin-top: 3rem;
        }
        
        .footer-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2rem;
        }
        
        .footer-logo {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
        }
        
        .footer-logo img {
            height: 40px;
            margin-right: 10px;
        }
        
        .footer-logo h2 {
            font-size: 1.2rem;
            font-weight: 600;
        }
        
        .footer-about p {
            margin-bottom: 1rem;
            font-size: 0.9rem;
            opacity: 0.8;
        }
        
        .social-links {
            display: flex;
            gap: 1rem;
        }
        
        .social-links a {
            color: var(--white);
            font-size: 1.2rem;
            transition: opacity 0.3s;
        }
        
        .social-links a:hover {
            opacity: 0.8;
        }
        
        .footer-links h3 {
            font-size: 1.1rem;
            margin-bottom: 1.5rem;
            position: relative;
            padding-bottom: 0.5rem;
        }
        
        .footer-links h3::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 40px;
            height: 2px;
            background-color: var(--white);
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
            font-size: 0.9rem;
            opacity: 0.8;
            transition: opacity 0.3s;
        }
        
        .footer-links a:hover {
            opacity: 1;
        }
        
        .footer-contact p {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
            font-size: 0.9rem;
            opacity: 0.8;
        }
        
        .footer-contact i {
            margin-right: 0.8rem;
            font-size: 1rem;
        }
        
        .copyright {
            text-align: center;
            padding-top: 2rem;
            margin-top: 2rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            font-size: 0.8rem;
            opacity: 0.7;
        }
        
        @media (max-width: 768px) {
            .ticket-body {
                flex-direction: column;
            }
            
            .ticket-image {
                width: 100%;
                height: 200px;
                margin-right: 0;
                margin-bottom: 1rem;
            }
            
            .cart-summary {
                flex-direction: column;
                gap: 1.5rem;
                align-items: flex-start;
            }
            
            .ticket-actions {
                flex-direction: column;
                width: 100%;
            }
            
            .btn {
                width: 100%;
            }
            
            .match-info {
                flex-direction: column;
                gap: 0.5rem;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-container">
            <div class="header-nav">
                <span class="logo" style="font-size: small; font-family: 'Courier New', monospace; font-weight: bold; font-style: italic;">
                    Football Ticket
                </span>
                <nav class="nav-links">
                    <a href="index.php" class="nav-link">ACCUEIL</a>
                    <a href="billets.php" class="nav-link">Matchs</a>
                    <a href="equipes.php" class="nav-link">ÉQUIPES</a>
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

    <div class="container">
        <div class="header">
            <h1><i class="fas fa-shopping-cart"></i> Mon Panier</h1>
            <a href="billets.php" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Retour aux matchs</a>
        </div>
        
        <?php if (count($billets) > 0): ?>
            <div class="cart-summary">
                <div class="summary-item">
                    <i class="fas fa-ticket-alt"></i>
                    <div>
                        <div class="summary-text">Billets dans le panier</div>
                        <div class="summary-value"><?= $nombre_articles ?></div>
                    </div>
                </div>
                <div class="summary-item">
                    <i class="fas fa-receipt"></i>
                    <div>
                        <div class="summary-text">Billets payés</div>
                        <div class="summary-value"><?= count(array_filter($billets, fn($b) => $b['etat'] === 'payé')) ?></div>
                    </div>
                </div>
                <div class="summary-item">
                    <i class="fas fa-wallet"></i>
                    <div>
                        <div class="summary-text">Total panier</div>
                        <div class="summary-value"><?= $total_panier ?> DH</div>
                    </div>
                </div>
            </div>
            
            <?php foreach ($billets as $billet): ?>
                <div class="ticket-card">
                    <div class="ticket-header">
                        <div>
                            <i class="far fa-calendar-alt"></i>
                            <?= date('d M Y', strtotime($billet['date_match'])) ?> • 
                            <?= htmlspecialchars($billet['heure_match']) ?>
                        </div>
                        <span class="ticket-status <?= $billet['etat'] === 'payé' ? 'status-paid' : 'status-pending' ?>">
                            <?= $billet['etat'] === 'payé' ? 'Payé' : 'En panier' ?>
                        </span>
                    </div>
                    
                    <div class="ticket-body">
                        <?php if (!empty($billet['image_stade'])): ?>
                            <img src="<?= htmlspecialchars($billet['image_stade']) ?>" alt="<?= htmlspecialchars($billet['nom_stade']) ?>" class="ticket-image">
                        <?php else: ?>
                            <img src="https://via.placeholder.com/150?text=Stade" alt="Stade" class="ticket-image">
                        <?php endif; ?>
                        
                        <div class="ticket-details">
                            <div class="match-teams">
                                <?= htmlspecialchars($billet['equipe1']) ?> 
                                <span class="match-vs">vs</span> 
                                <?= htmlspecialchars($billet['equipe2']) ?>
                            </div>
                            
                            <div class="match-info">
                                <div class="info-item">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <?= htmlspecialchars($billet['nom_stade']) ?>, <?= htmlspecialchars($billet['ville']) ?>
                                </div>
                                <div class="info-item">
                                    <i class="far fa-clock"></i>
                                    <?= htmlspecialchars($billet['heure_match']) ?>
                                </div>
                                
                                <?php if ($billet['nombre_billets'] > 1): ?>
                                    <div class="info-item">
                                        <i class="fas fa-ticket-alt"></i>
                                        Quantité: <?= $billet['nombre_billets'] ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="ticket-meta">
                                <div>
                                    <span class="ticket-price"><?= htmlspecialchars($billet['prix']) ?> DH</span>
                                    <span class="ticket-category"><?= htmlspecialchars($billet['categorie']) ?></span>
                                </div>
                                
                                <div class="ticket-actions">
                                    <?php if ($billet['etat'] !== 'payé'): ?>
                                        <form method="get" action="panier.php" style="display:inline;">
                                            <input type="hidden" name="id_billet" value="<?= $billet['id_billet'] ?>">
                                            <button type="submit" class="btn">Acheter maintenant</button>
                                        </form>
                                    <?php else: ?>
                                        <span>-</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            
            <?php if ($nombre_articles > 0): ?>
                <div class="checkout-section">
                    <div class="total-amount">
                        Total à payer: <?= $total_panier ?> DH
                    </div>
                    <form method="post" action="payer_billet.php">
                        <button type="submit" class="btn btn-primary" style="padding: 1rem 2rem; font-size: 1.1rem;">
                            <i class="fas fa-credit-card"></i> Payer tout le panier
                        </button>
                    </form>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="empty-cart">
                <i class="fas fa-shopping-cart"></i>
                <h3>Votre panier est vide</h3>
                <p>Parcourez les matchs disponibles et ajoutez des billets à votre panier.</p>
                <a href="billets.php" class="btn btn-primary">Voir les matchs</a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <footer>
        <div class="footer-container">
            <div class="footer-about">
                <div class="footer-logo">
                    <span class="logo" style="font-size: small; font-family: 'Courier New', monospace; font-weight: bold; font-style: italic;">
                        Football ticket
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

    <script>
    // Animation pour les boutons
    document.querySelectorAll('.btn').forEach(btn => {
        btn.addEventListener('click', function() {
            this.classList.add('btn-clicked');
            setTimeout(() => {
                this.classList.remove('btn-clicked');
            }, 300);
        });
    });
    </script>
</body>
</html>