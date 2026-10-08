<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once '../../config.php';
require_once __DIR__ . '/../../twilio-php/src/Twilio/autoload.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => "Non connecté."]);
    exit;
}

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
if (!$user) {
    echo json_encode(['success' => false, 'message' => "Utilisateur introuvable."]);
    exit;
}
if (empty($user['telephone'])) {
    echo json_encode(['success' => false, 'message' => "Numéro de téléphone manquant."]);
    exit;
}

$whatsapp_message = $_POST['message'] ?? 'Test message WhatsApp via Twilio !';

$clean_phone = preg_replace('/[^0-9]/', '', $user['telephone']);

try {
    $twilio = new Twilio\Rest\Client($twilio_sid, $twilio_token);
    $to = 'whatsapp:+' . $clean_phone; 
    $message = $twilio->messages->create(
        $to,
        [
            'from' => $twilio_number,
            'body' => $whatsapp_message
        ]
    );
    echo json_encode([
        'success' => true,
        'message' => 'Message WhatsApp envoyé avec succès.'
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => "Erreur Twilio: " . $e->getMessage()
    ]);
}