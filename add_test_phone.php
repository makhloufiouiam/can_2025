<?php
require_once 'config.php';

echo "<h2>Ajout d'un numéro de téléphone de test</h2>";

try {
    // Récupérer le premier utilisateur
    $stmt = $pdo->query("SELECT user_id, nom, prenom FROM users LIMIT 1");
    $user = $stmt->fetch();
    
    if ($user) {
        echo "<p>Utilisateur trouvé : {$user['prenom']} {$user['nom']} (ID: {$user['user_id']})</p>";
        
        // Vérifier si l'utilisateur a déjà un numéro de téléphone
        $stmt = $pdo->prepare("SELECT telephone FROM users WHERE user_id = ?");
        $stmt->execute([$user['user_id']]);
        $current_phone = $stmt->fetchColumn();
        
        if ($current_phone) {
            echo "<p style='color: green;'>✅ L'utilisateur a déjà un numéro de téléphone : $current_phone</p>";
        } else {
            // Ajouter un numéro de téléphone de test
            $test_phone = "212600000000"; // Format international pour le Maroc
            $stmt = $pdo->prepare("UPDATE users SET telephone = ? WHERE user_id = ?");
            $stmt->execute([$test_phone, $user['user_id']]);
            
            echo "<p style='color: green;'>✅ Numéro de téléphone de test ajouté : $test_phone</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ Aucun utilisateur trouvé dans la base de données</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Erreur : " . $e->getMessage() . "</p>";
}

echo "<p><a href='reservation.php?match=1'>← Retour à la réservation</a></p>";
?> 