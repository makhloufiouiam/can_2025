<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once '../../config.php';
require '../../lib/fpdf182/fpdf.php';
require_once __DIR__ . '/../../twilio-php/src/Twilio/autoload.php';

header('Content-Type: application/json');

// Vérifier la connexion
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => "Non connecté."]);
    exit;
}

// Récupérer l'ID du match
$id_match = $_GET['matchs'] ?? $_GET['match'] ?? null;
if (!$id_match) {
    echo json_encode(['success' => false, 'message' => "Paramètre match manquant."]);
    exit;
}
$id_match = intval($id_match);

// Récupérer les infos du match
$stmt = $pdo->prepare("SELECT m.*, s.nom_stade, s.ville FROM matchs m JOIN stades s ON m.id_stade = s.id_stade WHERE m.id_match = ?");
$stmt->execute([$id_match]);
$match = $stmt->fetch();
if (!$match) {
    echo json_encode(['success' => false, 'message' => "Match introuvable."]);
    exit;
}

// Récupérer l'utilisateur avec son numéro de téléphone
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
if (!$user) {
    echo json_encode(['success' => false, 'message' => "Utilisateur introuvable."]);
    exit;
}

// Vérifier que l'utilisateur a un numéro de téléphone
if (empty($user['telephone'])) {
    echo json_encode(['success' => false, 'message' => "Numéro de téléphone manquant pour l'envoi WhatsApp."]);
    exit;
}

// Récupérer les infos du formulaire
$categorie = $_POST['categorie'] ?? 'Standard';
$prix = $_POST['prix'] ?? 0;

// Correction : récupération robuste du nombre de billets
if (isset($_POST['nombre_billets'])) {
    $nombre_billets = intval($_POST['nombre_billets']);
} elseif (isset($_GET['nombre_billets'])) {
    $nombre_billets = intval($_GET['nombre_billets']);
} else {
    $nombre_billets = 1;
}


$numero_place = rand(1, 500);
$numero_porte = rand(1, 10);

// Déterminer l'état du billet
$etat = isset($_POST['etat']) ? $_POST['etat'] : 'payé';

// Enregistrer le billet dans la base de données
try {
    $sql = "INSERT INTO billets (id_match, user_id, categorie, prix, etat, date_reservation, nombre_billets, numero_porte, numero_place)
            VALUES (?, ?, ?, ?, ?, NOW(), ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $id_match,
        $user_id,
        $categorie,
        $prix,
        $etat,
        $nombre_billets,
        $numero_porte,
        $numero_place
    ]);
    $id_billet = $pdo->lastInsertId();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => "Erreur lors de l'enregistrement du billet: " . $e->getMessage()]);
    exit;
}

// Générer le PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',16);
$pdf->Cell(0,10,'Billet - CAN 2026',0,1,'C');
$pdf->SetFont('Arial','',12);
$pdf->Ln(5);
$pdf->Cell(0,10,"Nom: {$user['prenom']} {$user['nom']}",0,1);
$pdf->Cell(0,10,"Téléphone: {$user['telephone']}",0,1);
$pdf->Cell(0,10,"Match: {$match['equipe1']} vs {$match['equipe2']}",0,1);
$pdf->Cell(0,10,"Date: {$match['date_match']} {$match['heure_match']}",0,1);
$pdf->Cell(0,10,"Stade: {$match['nom_stade']} ({$match['ville']})",0,1);
$pdf->Cell(0,10,"Porte: $numero_porte",0,1);
$pdf->Cell(0,10,"Place: $numero_place",0,1);
$pdf->Cell(0,10,"Catégorie: $categorie",0,1);
$pdf->Cell(0,10,"Nombre de billets: $nombre_billets",0,1);
$pdf->Cell(0,10,"Prix total: $prix DH",0,1);
$pdf->Ln(10);
$pdf->Cell(0,10,"Merci pour votre réservation !",0,1,'C');

// Sauvegarder le PDF temporairement
$pdf_dir = '../../tmp';
if (!is_dir($pdf_dir)) {
    mkdir($pdf_dir, 0777, true);
}
$pdf_filename = 'billet_' . uniqid() . '.pdf';
$pdf_path = $pdf_dir . '/' . $pdf_filename;
$pdf->Output('F', $pdf_path);

// Préparer le message WhatsApp
$whatsapp_message = "Bonjour {$user['prenom']},\n\n";
$whatsapp_message .= "📌 *Votre billet CAN 2026* 📌\n\n";
$whatsapp_message .= "⚽ *Match*: {$match['equipe1']} vs {$match['equipe2']}\n";
$whatsapp_message .= "📅 *Date*: {$match['date_match']} à {$match['heure_match']}\n";
$whatsapp_message .= "🏟️ *Stade*: {$match['nom_stade']} ({$match['ville']})\n";
$whatsapp_message .= "🚪 *Porte*: $numero_porte\n";
$whatsapp_message .= "💺 *Place*: $numero_place\n";
$whatsapp_message .= "🎫 *Catégorie*: $categorie\n";
$whatsapp_message .= "🎟️ *Nombre de billets*: $nombre_billets\n";
$whatsapp_message .= "💰 *Prix total*: $prix DH\n\n";
$whatsapp_message .= "Merci pour votre réservation !\n";
$whatsapp_message .= "Présentez ce message à l'entrée du stade.";

// Retourner la réponse avec le statut d'envoi WhatsApp et le PDF
echo json_encode([
    'success' => true,
    'message' => "Billet généré avec succès!",
    'pdf_url' => 'tmp/' . $pdf_filename,
    'whatsapp_message' => $whatsapp_message
]);

// Note: Le PDF temporaire devrait être nettoyé après un certain temps
// Vous pouvez ajouter un cron job ou un système de nettoyage
?>