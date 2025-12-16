<?php
/**
 * API pour sauvegarder l'analyse SWOT/TOWS
 */
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../config/database.php';

// Vérifier l'authentification
if (!isset($_SESSION['participant_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Non authentifie']);
    exit;
}

// Récupérer les données POST
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Donnees invalides']);
    exit;
}

$participantId = $_SESSION['participant_id'];
$swotData = json_encode($input['swot'] ?? []);
$towsData = json_encode($input['tows'] ?? []);

try {
    $db = getDB();

    // Insérer ou mettre à jour l'analyse
    $stmt = $db->prepare("
        INSERT INTO analyses (participant_id, swot_data, tows_data, updated_at)
        VALUES (?, ?, ?, datetime('now'))
        ON CONFLICT(participant_id) DO UPDATE SET
            swot_data = excluded.swot_data,
            tows_data = excluded.tows_data,
            updated_at = datetime('now')
    ");
    $stmt->execute([$participantId, $swotData, $towsData]);

    echo json_encode(['success' => true, 'message' => 'Analyse sauvegardee']);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Erreur serveur: ' . $e->getMessage()]);
}
