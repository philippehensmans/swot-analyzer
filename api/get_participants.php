<?php
/**
 * API pour récupérer la liste des participants d'une session (formateur)
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

$sessionId = $_SESSION['formateur_session_id'];

try {
    $db = getDB();

    $stmt = $db->prepare("
        SELECT
            p.id,
            p.nom,
            p.prenom,
            p.organisation,
            p.created_at,
            a.submitted,
            a.submitted_at,
            a.updated_at,
            CASE WHEN a.id IS NOT NULL THEN 1 ELSE 0 END as has_analysis
        FROM participants p
        LEFT JOIN analyses a ON p.id = a.participant_id
        WHERE p.session_id = ?
        ORDER BY p.nom, p.prenom
    ");
    $stmt->execute([$sessionId]);
    $participants = $stmt->fetchAll();

    echo json_encode(['success' => true, 'participants' => $participants]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Erreur serveur: ' . $e->getMessage()]);
}
