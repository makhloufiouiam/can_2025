<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: auth.php');
    exit();
}



$user_id = $_SESSION['user_id'];

// Récupérer tous les billets en panier pour l'utilisateur
$sql = "SELECT SUM(prix) as total, SUM(nombre_billets) as nb_billets FROM billets WHERE user_id = ? AND etat != 'payé'";
$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);
$panier = $stmt->fetch();
$total = $panier['total'] ?? 0;
$nb_billets = $panier['nb_billets'] ?? 0;

// On peut aussi afficher un message si le panier est vide
if ($nb_billets == 0) {
    echo '<div class="container"><div class="error">Votre panier est vide.</div></div>';
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Paiement du panier</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { font-family: 'Roboto', sans-serif; background: #f5f5f5; }
        .container { max-width: 600px; margin: 40px auto; background: #fff; border-radius: 10px; box-shadow: 0 2px 8px #0001; padding: 32px; }
        h2 { color: #b71c1c; text-align: center; margin-bottom: 24px; }
        .success { background: #d4edda; color: #155724; padding: 12px; border-radius: 4px; margin-bottom: 18px; text-align: center; }
        .error { background: #f8d7da; color: #721c24; padding: 12px; border-radius: 4px; margin-bottom: 18px; text-align: center; }
        .form-group { margin-bottom: 18px; }
        .btn { background: #b71c1c; color: #fff; border: none; padding: 10px 24px; border-radius: 4px; font-size: 1rem; cursor: pointer; margin: 8px 0; text-decoration: none; display: inline-block; }
        .btn:hover { background: #a31515; }
        .btn-secondary { background: #6c757d; }
        .btn-secondary:hover { background: #5a6268; }
    </style>
</head>
<body>
<div class="container">
    <h2>Paiement du panier</h2>
    <div class="match-info" style="text-align:center; margin-bottom:24px;">
        <strong>Nombre total de billets : <?php echo $nb_billets; ?></strong><br>
        <span>Total à payer : <?php echo $total; ?> DH</span>
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
        <input type="hidden" name="total" value="<?php echo $total; ?>">
        <input type="hidden" name="nb_billets" value="<?php echo $nb_billets; ?>">
        <div id="paypal-button-container"></div>
        <button type="button" id="btn-test" class="btn btn-secondary">
            <i class="fas fa-check-circle"></i> Compléter (Test)
        </button>
    </form>
    <script src="https://www.paypal.com/sdk/js?client-id=AXoBBj3PkcvIdwy9AKsks3wtMdK16AUv54btD7jEMwLqieySxIYuWbJla0EqRFOMg3L0B_QaAeI9CeAB&currency=EUR"></script>
    <script>
    // À adapter pour le paiement du panier (appel AJAX à un endpoint spécifique)
    document.getElementById('btn-test').onclick = function() {
        const btn = this;
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Traitement...';
        btn.disabled = true;
        var formData = new FormData(document.getElementById('paypal-form'));
        fetch('api/billets/test_reservation.php?panier=1', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const container = document.querySelector('.container');
                const successHTML = `
                    <div class="success">
                        <h3>🎉 Paiement du panier confirmé !</h3>
                        <p>Vos billets ont été générés avec succès.</p>
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
                    amount: { value: '<?php echo $total; ?>' }
                }]
            });
        },
        onApprove: function(data, actions) {
            return actions.order.capture().then(function(details) {
                var formData = new FormData(document.getElementById('paypal-form'));
                formData.append('payer', 1);
                formData.append('paypal_order_id', data.orderID);
                fetch('payer_panier.php', {
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
</body>
</html> 