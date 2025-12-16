<?php
/**
 * API pour charger l'analyse SWOT/TOWS d'un participant
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

$participantId = $_SESSION['participant_id'];

try {
    $db = getDB();

    $stmt = $db->prepare("
        SELECT swot_data, tows_data, submitted, submitted_at, updated_at
        FROM analyses
        WHERE participant_id = ?
    ");
    $stmt->execute([$participantId]);
    $analysis = $stmt->fetch();

    if ($analysis) {
        echo json_encode([
            'success' => true,
            'data' => [
                'swot' => json_decode($analysis['swot_data'], true) ?? [],
                'tows' => json_decode($analysis['tows_data'], true) ?? [],
                'submitted' => (bool)$analysis['submitted'],
                'submitted_at' => $analysis['submitted_at'],
                'updated_at' => $analysis['updated_at']
            ]
        ]);
    } else {
        echo json_encode([
            'success' => true,
            'data' => [
                'swot' => ['strengths' => [], 'weaknesses' => [], 'opportunities' => [], 'threats' => []],
                'tows' => ['so' => [], 'wo' => [], 'st' => [], 'wt' => []],
                'submitted' => false
            ]
        ]);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Erreur serveur: ' . $e->getMessage()]);
}
