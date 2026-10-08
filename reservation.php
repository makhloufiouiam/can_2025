<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once 'config.php';
require 'lib/PHPMailer/src/PHPMailer.php';
require 'lib/PHPMailer/src/SMTP.php';
require 'lib/PHPMailer/src/Exception.php';
require 'lib/fpdf182/fpdf.php';

// Vérifier la connexion
if (!isset($_SESSION['user_id'])) {
    header('Location: auth.php');
    exit();
}

// Récupérer l'ID du match
if (!isset($_GET['match'])) {
    header('Location: billets.php');
    exit();
}
$id_match = intval($_GET['match']);

// Récupérer les infos du match
$sql = "SELECT m.*, s.nom_stade, s.ville, s.capacite
        FROM matchs m
        JOIN stades s ON m.id_stade = s.id_stade
        WHERE m.id_match = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id_match]);
$match = $stmt->fetch();
if (!$match) {
    echo "<p>Match introuvable.</p>";
    exit();
}

// Catégories et prix dynamiques
$categories = [
    'VIP' => rand(1000, 1500),
    'Standard' => rand(500, 1000),
    'Economique' => rand(100, 500)
];
// Ajustement du prix selon la proximité du match
$days_to_match = (strtotime($match['date_match']) - time()) / 86400;
foreach ($categories as $cat => &$prix) {
    if ($days_to_match < 7) $prix = intval($prix * 1.5);
    elseif ($days_to_match < 14) $prix = intval($prix * 1.2);
}
unset($prix);

// Contrôle de la disponibilité (total)
$sql = "SELECT SUM(nombre_billets) as total_vendus FROM billets WHERE id_match = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id_match]);
$vendus = $stmt->fetch()['total_vendus'] ?? 0;
$places_dispo = $match['capacite'] - $vendus;

// Gestion du formulaire
$step = 'choix';
$selected_cat = '';
$prix_choisi = 0;
$success = $error = '';
if (isset($_GET['id_billet'])) {
    $id_billet = intval($_GET['id_billet']);
    $stmt = $pdo->prepare("SELECT * FROM billets WHERE id_billet = ? AND user_id = ?");
    $stmt->execute([$id_billet, $_SESSION['user_id']]);
    $billet = $stmt->fetch();
    if ($billet && $billet['etat'] === 'panier') {
        
        $selected_cat = $billet['categorie'];
        $prix_choisi = $billet['prix'];
        $nombre_billets = $billet['nombre_billets'];
        $step = 'paiement';
       
    } else {
        
        header('Location: panier.php');
        exit();
    }
}
if (isset($_POST['choisir_cat'])) {
    $selected_cat = $_POST['categorie'];
    $nombre_billets = isset($_POST['nombre_billets']) ? intval($_POST['nombre_billets']) : 1;
    $prix_unitaire = intval($categories[$selected_cat]);
    $prix_choisi = $prix_unitaire * $nombre_billets;
    $step = 'achat';
}
if (isset($_POST['ajouter_panier'])) {
    $selected_cat = $_POST['categorie'];
    $nombre_billets = isset($_POST['nombre_billets']) ? intval($_POST['nombre_billets']) : 1;
    $prix_unitaire = intval($categories[$selected_cat]);
    $prix_choisi = $prix_unitaire * $nombre_billets;
    $numero_place = rand(1, 500);
    $numero_porte = rand(1, 10);
    $etat = 'panier';

    $sql = "INSERT INTO billets (id_match, user_id, categorie, prix, nombre_billets, date_reservation, etat, numero_porte, numero_place)
            VALUES (?, ?, ?, ?, ?, NOW(), ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $id_match,
        $_SESSION['user_id'],
        $selected_cat,
        $prix_choisi,
        $nombre_billets,
        $etat,
        $numero_porte,
        $numero_place
    ]);
    header('Location: panier.php');
    exit();
}
if (isset($_POST['acheter'])) {
    $selected_cat = $_POST['categorie'];
    $nombre_billets = isset($_POST['nombre_billets']) ? intval($_POST['nombre_billets']) : 1;
    $prix_unitaire = intval($categories[$selected_cat]);
    $prix_choisi = $prix_unitaire * $nombre_billets;
    $step = 'paiement';
}
if (isset($_POST['payer']) || isset($_POST['paypal_order_id'])) {
    // Traitement du paiement classique ou PayPal
    $cin = $_POST['cin'] ?? '';
    $mode_paiement = $_POST['mode_paiement'] ?? 'paypal';
    $numero_compte = $_POST['numero_compte'] ?? '';
    $categorie = $_POST['categorie'];
    $prix = $_POST['prix'];
    $user_id = $_SESSION['user_id'];
    $etat = 'payé';
    $date_reservation = date('Y-m-d H:i:s');
    $nombre_billets = isset($_POST['nombre_billets']) ? intval($_POST['nombre_billets']) : 1;
    $prix_unitaire = isset($_POST['prix_unitaire']) ? intval($_POST['prix_unitaire']) : 0;
    $prix_choisi = $prix_unitaire * $nombre_billets;
    $numero_place = rand(1, 500);
    $numero_porte = rand(1, 10);

    // 1. Enregistrer le billet
    $sql = "INSERT INTO billets (id_match, user_id, categorie, prix, etat, date_reservation, nombre_billets, numero_porte, numero_place) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $id_match,
        $user_id,
        $categorie,
        $prix_choisi,
        $etat,
        $date_reservation,
        $nombre_billets,
        $numero_porte,
        $numero_place
    ]);
    $id_billet = $pdo->lastInsertId();

    // 2. Enregistrer le paiement
    $sql = "INSERT INTO paiement (id_billet, user_id, montant, mode_paiement, numero_compte, date_paiement, cin, paypal_order_id) VALUES (?, ?, ?, ?, ?, NOW(), ?, ?)";
    $paypal_order_id = $_POST['paypal_order_id'] ?? null;
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_billet, $user_id, $prix_choisi, $mode_paiement, $numero_compte, $cin, $paypal_order_id]);

    // Si c'est un appel AJAX (PayPal), on arrête ici
    if (isset($_POST['paypal_order_id'])) {
        echo 'success';
        exit;
    }
    $success = "Paiement effectué et billet réservé avec succès !";
    $step = 'fin';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Réservation - <?php echo htmlspecialchars($match['equipe1']) . ' vs ' . htmlspecialchars($match['equipe2']); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { font-family: 'Roboto', sans-serif; background: #f5f5f5; }
        .container { max-width: 600px; margin: 40px auto; background: #fff; border-radius: 10px; box-shadow: 0 2px 8px #0001; padding: 32px; }
        h2 { color: #b71c1c; text-align: center; margin-bottom: 24px; }
        .match-info { text-align: center; margin-bottom: 24px; }
        .cat-list { display: flex; flex-direction: column; gap: 18px; margin-bottom: 24px; }
        .cat-item { display: flex; align-items: center; justify-content: space-between; background: #f8f8f8; border-radius: 6px; padding: 14px 20px; }
        .btn { background: #b71c1c; color: #fff; border: none; padding: 10px 24px; border-radius: 4px; font-size: 1rem; cursor: pointer; margin: 8px 0; text-decoration: none; display: inline-block; }
        .btn:hover { background: #a31515; }
        .btn-secondary { background: #6c757d; }
        .btn-secondary:hover { background: #5a6268; }
        .success { background: #d4edda; color: #155724; padding: 12px; border-radius: 4px; margin-bottom: 18px; text-align: center; }
        .error { background: #f8d7da; color: #721c24; padding: 12px; border-radius: 4px; margin-bottom: 18px; text-align: center; }
        .form-group { margin-bottom: 18px; }
        label { display: block; margin-bottom: 6px; }
        input[type=text], input[type=number] { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; }
        .fa-spinner { animation: spin 1s linear infinite; }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Réserver un billet</h2>
    <div class="match-info">
        <strong><?php echo htmlspecialchars($match['equipe1']); ?> vs <?php echo htmlspecialchars($match['equipe2']); ?></strong><br>
        <?php echo htmlspecialchars($match['date_match']); ?> à <?php echo htmlspecialchars($match['heure_match']); ?><br>
        <?php echo htmlspecialchars($match['nom_stade']); ?>, <?php echo htmlspecialchars($match['ville']); ?><br>
        <span>Places disponibles : <?php echo $places_dispo; ?></span>
    </div>
    <?php if ($success): ?><div class="success"><?php echo $success; ?></div><?php endif; ?>
    <?php if ($error): ?><div class="error"><?php echo $error; ?></div><?php endif; ?>

    <?php if ($step === 'choix'): ?>
        <form method="post">
            <div class="cat-list">
                <?php foreach ($categories as $cat => $prix): ?>
                    <div class="cat-item">
                        <label><input type="radio" name="categorie" value="<?php echo $cat; ?>" data-prix="<?php echo $prix; ?>" required> <?php echo $cat; ?> (<?php echo $prix; ?> DH)</label>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="form-group">
                <label for="nombre_billets">Nombre de billets :</label>
                <input type="number" name="nombre_billets" id="nombre_billets" min="1" max="<?php echo $places_dispo; ?>" value="1" required>
            </div>
            <input type="hidden" name="prix_unitaire" id="prix_unitaire" value="">
            <button type="submit" name="choisir_cat" class="btn">Continuer</button>
        </form>
        <script>
        document.querySelectorAll('input[name="categorie"]').forEach(function(radio) {
            radio.addEventListener('change', function() {
                document.getElementById('prix_unitaire').value = this.getAttribute('data-prix');
            });
        });
        </script>
    <?php elseif ($step === 'achat'): ?>
        <form method="post">
            <input type="hidden" name="categorie" value="<?php echo htmlspecialchars($selected_cat); ?>">
            <input type="hidden" name="nombre_billets" value="<?php echo htmlspecialchars($nombre_billets); ?>">
            <input type="hidden" name="prix_unitaire" value="<?php echo $prix_unitaire; ?>">
            <div class="form-group">
                <label>Catégorie :</label>
                <strong><?php echo htmlspecialchars($selected_cat); ?></strong>
            </div>
            <div class="form-group">
                <label>Prix :</label>
                <strong><?php echo $prix_choisi; ?> DH</strong>
            </div>
            <input type="hidden" name="prix" value="<?php echo $prix_choisi; ?>">
            <button type="submit" name="ajouter_panier" class="btn">Ajouter au panier</button>
            <button type="submit" name="acheter" class="btn">Acheter maintenant</button>
        </form>
    <?php elseif ($step === 'fin'): ?>
        <div class="success">Votre paiement a été effectué et votre billet réservé avec succès !<br>
            <a href="billets.php">Retour aux matchs</a>
        </div>
    <?php elseif ($step === 'paiement'): ?>
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
            <input type="hidden" name="categorie" value="<?php echo htmlspecialchars($selected_cat); ?>">
            <input type="hidden" name="prix" value="<?php echo $prix_choisi; ?>">
            <input type="hidden" name="nombre_billets" value="<?php echo htmlspecialchars($nombre_billets); ?>">
            <div id="paypal-button-container"></div>
            <button type="button" id="btn-test" class="btn btn-secondary">
                <i class="fas fa-check-circle"></i> Compléter (Test)
            </button>
            <script>
            document.getElementById('btn-test').onclick = function() {
                // Afficher un indicateur de chargement
                const btn = this;
                const originalText = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Traitement...';
                btn.disabled = true;
                
                var formData = new FormData(document.getElementById('paypal-form'));
                formData.append('nombre_billets', '1'); // Ajouter le nombre de billets
                
                fetch('api/billets/test_reservation.php?match=<?php echo $id_match; ?>', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const container = document.querySelector('.container');
                        const successHTML = `
                            <div class="success">
                                <h3>🎉 Réservation confirmée !</h3>
                                <p>Votre billet a été généré avec succès.</p>
                            </div>
                            <div style="text-align: center; margin: 20px 0;">
                                <a href="#" id="btn-whatsapp" class="btn" style="background: #25D366; margin: 10px;">
                                    📱 Envoyer via WhatsApp
                                </a>
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

                        // Gestion du bouton WhatsApp
                        document.getElementById('btn-whatsapp').onclick = function(e) {
                            e.preventDefault();
                            this.disabled = true;
                            this.innerHTML = 'Envoi...';
                            fetch('api/billets/send_whatsapp.php', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                                body: 'message=' + encodeURIComponent(data.whatsapp_message)
                            })
                            .then(response => response.json())
                            .then(res => {
                                if (res.success) {
                                    alert('Message WhatsApp envoyé avec succès !');
                                } else {
                                    alert('Erreur : ' + res.message);
                                }
                                this.disabled = false;
                                this.innerHTML = '📱 Envoyer via WhatsApp';
                            })
                            .catch(() => {
                                alert('Erreur réseau.');
                                this.disabled = false;
                                this.innerHTML = '📱 Envoyer via WhatsApp';
                            });
                        };
                    } else {
                        alert('Erreur: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Une erreur est survenue lors de la réservation.');
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                });
            };
            </script>
        </form>
        <script src="https://www.paypal.com/sdk/js?client-id=AXoBBj3PkcvIdwy9AKsks3wtMdK16AUv54btD7jEMwLqieySxIYuWbJla0EqRFOMg3L0B_QaAeI9CeAB&currency=EUR"></script>
        <script>
        paypal.Buttons({
            createOrder: function(data, actions) {
                return actions.order.create({
                    purchase_units: [{
                        amount: { value: '<?php echo $prix_choisi; ?>' }
                    }]
                });
            },
            onApprove: function(data, actions) {
                return actions.order.capture().then(function(details) {
                    // Envoi des infos à reservation.php pour enregistrer le billet et le paiement
                    var formData = new FormData(document.getElementById('paypal-form'));
                    formData.append('payer', 1);
                    formData.append('paypal_order_id', data.orderID);
                    fetch('reservation.php?match=<?php echo $id_match; ?>', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.text())
                    .then(data => {
                        if (data === 'success') {
                            window.location.href = 'billets.php';
                        } else {
                            alert('Erreur lors de la réservation.');
                        }
                    });
                });
            }
        }).render('#paypal-button-container');
        </script>
    <?php endif; ?>
</div>

</body>
</html>