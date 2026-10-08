<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: auth.php');
    exit();
}

if (!isset($_POST['id_billet'])) {
    header('Location: panier.php');
    exit();
}

$id_billet = intval($_POST['id_billet']);
$user_id = $_SESSION['user_id'];

// Vérifier que le billet appartient à l'utilisateur et est en panier
$stmt = $pdo->prepare("SELECT * FROM billets WHERE id_billet = ? AND user_id = ? AND etat != 'payé'");
$stmt->execute([$id_billet, $user_id]);
$billet = $stmt->fetch();

if (!$billet) {
    header('Location: panier.php');
    exit();
}

// Mettre à jour l'état du billet à "payé"
$stmt = $pdo->prepare("UPDATE billets SET etat = 'payé' WHERE id_billet = ?");
$stmt->execute([$id_billet]);

header('Location: panier.php');
exit();