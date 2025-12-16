<?php
/**
 * API pour récupérer l'analyse d'un participant (formateur)
 */
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../config/database.php';

// Vérifier l'authentification formateur
if (!isset($_SESSION['formateur_session_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Non authentifie']);
    exit;
}

$participantId = $_GET['participant_id'] ?? null;

if (!$participantId) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'ID participant manquant']);
    exit;
}

$sessionId = $_SESSION['formateur_session_id'];

try {
    $db = getDB();

    // Vérifier que le participant appartient à la session du formateur
    $stmt = $db->prepare("
        SELECT p.id, p.nom, p.prenom, p.organisation,
               a.swot_data, a.tows_data, a.submitted, a.submitted_at, a.updated_at
        FROM participants p
        LEFT JOIN analyses a ON p.id = a.participant_id
        WHERE p.id = ? AND p.session_id = ?
    ");
    $stmt->execute([$participantId, $sessionId]);
    $result = $stmt->fetch();

    if (!$result) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Participant non trouve']);
        exit;
    }

    echo json_encode([
        'success' => true,
        'participant' => [
            'id' => $result['id'],
            'nom' => $result['nom'],
            'prenom' => $result['prenom'],
            'organisation' => $result['organisation']
        ],
        'analysis' => [
            'swot' => json_decode($result['swot_data'], true) ?? [],
            'tows' => json_decode($result['tows_data'], true) ?? [],
            'submitted' => (bool)$result['submitted'],
            'submitted_at' => $result['submitted_at'],
            'updated_at' => $result['updated_at']
        ]
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Erreur serveur: ' . $e->getMessage()]);
}
