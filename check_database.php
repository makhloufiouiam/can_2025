<?php
require_once 'config.php';

echo "<h2>Vérification de la structure de la base de données</h2>";

// Vérifier la table billets
try {
    $stmt = $pdo->query("DESCRIBE billets");
    $columns = $stmt->fetchAll();
    
    echo "<h3>Structure de la table 'billets' :</h3>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Colonne</th><th>Type</th><th>Null</th><th>Clé</th><th>Défaut</th><th>Extra</th></tr>";
    
    foreach ($columns as $column) {
        echo "<tr>";
        echo "<td>{$column['Field']}</td>";
        echo "<td>{$column['Type']}</td>";
        echo "<td>{$column['Null']}</td>";
        echo "<td>{$column['Key']}</td>";
        echo "<td>{$column['Default']}</td>";
        echo "<td>{$column['Extra']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Vérifier si la colonne user_id existe
    $user_id_exists = false;
    $id_user_exists = false;
    
    foreach ($columns as $column) {
        if ($column['Field'] === 'user_id') {
            $user_id_exists = true;
        }
        if ($column['Field'] === 'id_user') {
            $id_user_exists = true;
        }
    }
    
    echo "<h3>Problèmes détectés :</h3>";
    
    if (!$user_id_exists && !$id_user_exists) {
        echo "<p style='color: red;'>❌ Aucune colonne user_id ou id_user trouvée dans la table billets</p>";
        echo "<p>Ajout de la colonne user_id...</p>";
        
        $pdo->exec("ALTER TABLE billets ADD COLUMN user_id INT");
        echo "<p style='color: green;'>✅ Colonne user_id ajoutée</p>";
    } elseif ($id_user_exists && !$user_id_exists) {
        echo "<p style='color: orange;'>⚠️ Colonne id_user trouvée, renommage en user_id...</p>";
        $pdo->exec("ALTER TABLE billets CHANGE id_user user_id INT");
        echo "<p style='color: green;'>✅ Colonne renommée en user_id</p>";
    } else {
        echo "<p style='color: green;'>✅ Colonne user_id existe</p>";
    }
    
    // Vérifier la table users
    echo "<h3>Structure de la table 'users' :</h3>";
    $stmt = $pdo->query("DESCRIBE users");
    $user_columns = $stmt->fetchAll();
    
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Colonne</th><th>Type</th><th>Null</th><th>Clé</th><th>Défaut</th><th>Extra</th></tr>";
    
    foreach ($user_columns as $column) {
        echo "<tr>";
        echo "<td>{$column['Field']}</td>";
        echo "<td>{$column['Type']}</td>";
        echo "<td>{$column['Null']}</td>";
        echo "<td>{$column['Key']}</td>";
        echo "<td>{$column['Default']}</td>";
        echo "<td>{$column['Extra']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Vérifier si la colonne telephone existe
    $telephone_exists = false;
    foreach ($user_columns as $column) {
        if ($column['Field'] === 'telephone') {
            $telephone_exists = true;
            break;
        }
    }
    
    if (!$telephone_exists) {
        echo "<p style='color: red;'>❌ Colonne telephone manquante dans la table users</p>";
        echo "<p>Ajout de la colonne telephone...</p>";
        
        $pdo->exec("ALTER TABLE users ADD COLUMN telephone VARCHAR(20)");
        echo "<p style='color: green;'>✅ Colonne telephone ajoutée</p>";
    } else {
        echo "<p style='color: green;'>✅ Colonne telephone existe</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Erreur : " . $e->getMessage() . "</p>";
}

echo "<h3>Test de connexion à la base de données :</h3>";
try {
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
    $result = $stmt->fetch();
    echo "<p style='color: green;'>✅ Connexion réussie - {$result['count']} utilisateurs trouvés</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erreur de connexion : " . $e->getMessage() . "</p>";
}
?> 