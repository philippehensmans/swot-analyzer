<?php
/**
 * Administration des sessions de formation
 */
session_start();
require_once __DIR__ . '/config/database.php';

$db = getDB();
$error = '';
$success = '';

// Générer un code unique
function generateSessionCode() {
    $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    $code = '';
    for ($i = 0; $i < 6; $i++) {
        $code .= $chars[random_int(0, strlen($chars) - 1)];
    }
    return $code;
}

// Traitement du formulaire de création
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'create') {
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if (empty($name)) {
            $error = "Le nom de la session est obligatoire.";
        } else {
            try {
                // Générer un code unique
                $code = generateSessionCode();

                // Vérifier que le code n'existe pas déjà
                $stmt = $db->prepare("SELECT id FROM sessions WHERE code = ?");
                $stmt->execute([$code]);
                while ($stmt->fetch()) {
                    $code = generateSessionCode();
                    $stmt->execute([$code]);
                }

                // Créer la session
                $stmt = $db->prepare("
                    INSERT INTO sessions (name, code, description, formateur_password)
                    VALUES (?, ?, ?, ?)
                ");
                $stmt->execute([$name, $code, $description, $password ?: null]);

                $success = "Session creee avec succes ! Code: <strong>$code</strong>";

            } catch (Exception $e) {
                $error = "Erreur lors de la creation: " . $e->getMessage();
            }
        }
    } elseif ($_POST['action'] === 'toggle') {
        $sessionId = (int)$_POST['session_id'];
        $active = (int)$_POST['active'];

        try {
            $stmt = $db->prepare("UPDATE sessions SET active = ? WHERE id = ?");
            $stmt->execute([$active, $sessionId]);
            $success = $active ? "Session activee." : "Session desactivee.";
        } catch (Exception $e) {
            $error = "Erreur lors de la modification.";
        }
    } elseif ($_POST['action'] === 'delete') {
        $sessionId = (int)$_POST['session_id'];

        try {
            // Supprimer les analyses des participants de cette session
            $stmt = $db->prepare("
                DELETE FROM analyses WHERE participant_id IN
                (SELECT id FROM participants WHERE session_id = ?)
            ");
            $stmt->execute([$sessionId]);

            // Supprimer les participants
            $stmt = $db->prepare("DELETE FROM participants WHERE session_id = ?");
            $stmt->execute([$sessionId]);

            // Supprimer la session
            $stmt = $db->prepare("DELETE FROM sessions WHERE id = ?");
            $stmt->execute([$sessionId]);

            $success = "Session supprimee.";
        } catch (Exception $e) {
            $error = "Erreur lors de la suppression.";
        }
    }
}

// Récupérer toutes les sessions
$sessions = [];
try {
    $stmt = $db->query("
        SELECT s.*,
               (SELECT COUNT(*) FROM participants WHERE session_id = s.id) as participant_count,
               (SELECT COUNT(*) FROM participants p
                JOIN analyses a ON p.id = a.participant_id
                WHERE p.session_id = s.id AND a.submitted = 1) as submitted_count
        FROM sessions s
        ORDER BY s.created_at DESC
    ");
    $sessions = $stmt->fetchAll();
} catch (Exception $e) {
    $error = "Erreur lors du chargement des sessions.";
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Sessions - SWOT Analyzer</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            padding: 25px 30px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .header h1 {
            color: #2c3e50;
            font-size: 1.8em;
        }

        .header-links a {
            color: #3498db;
            text-decoration: none;
            margin-left: 20px;
        }

        .header-links a:hover {
            text-decoration: underline;
        }

        .card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .card h2 {
            color: #2c3e50;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #ecf0f1;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            color: #2c3e50;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #ecf0f1;
            border-radius: 10px;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #3498db;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 80px;
        }

        .form-group small {
            color: #7f8c8d;
            font-size: 12px;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: linear-gradient(45deg, #3498db, #2980b9);
            color: white;
        }

        .btn-success {
            background: linear-gradient(45deg, #27ae60, #2ecc71);
            color: white;
        }

        .btn-danger {
            background: linear-gradient(45deg, #e74c3c, #c0392b);
            color: white;
        }

        .btn-secondary {
            background: #ecf0f1;
            color: #2c3e50;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 12px;
        }

        .alert {
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .alert-error {
            background: #fee;
            border: 1px solid #e74c3c;
            color: #c0392b;
        }

        .alert-success {
            background: #d4edda;
            border: 1px solid #27ae60;
            color: #155724;
        }

        .sessions-table {
            width: 100%;
            border-collapse: collapse;
        }

        .sessions-table th,
        .sessions-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ecf0f1;
        }

        .sessions-table th {
            background: #f8f9fa;
            font-weight: 600;
            color: #2c3e50;
        }

        .sessions-table tr:hover {
            background: #f8f9fa;
        }

        .session-code {
            font-family: monospace;
            font-size: 1.1em;
            background: #ecf0f1;
            padding: 5px 10px;
            border-radius: 5px;
            color: #2c3e50;
        }

        .status-active {
            color: #27ae60;
            font-weight: 600;
        }

        .status-inactive {
            color: #e74c3c;
            font-weight: 600;
        }

        .action-buttons {
            display: flex;
            gap: 5px;
        }

        .empty-state {
            text-align: center;
            color: #7f8c8d;
            padding: 40px;
        }

        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }

            .header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }

            .sessions-table {
                font-size: 14px;
            }

            .action-buttons {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Gestion des Sessions</h1>
            <div class="header-links">
                <a href="index.php">Accueil participant</a>
                <a href="formateur.php">Espace formateur</a>
            </div>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>

        <!-- Formulaire de création -->
        <div class="card">
            <h2>Creer une nouvelle session</h2>
            <form method="POST">
                <input type="hidden" name="action" value="create">

                <div class="form-row">
                    <div class="form-group">
                        <label for="name">Nom de la session *</label>
                        <input type="text" id="name" name="name"
                               placeholder="Ex: Formation SWOT - Janvier 2025" required>
                    </div>

                    <div class="form-group">
                        <label for="password">Mot de passe formateur</label>
                        <input type="password" id="password" name="password"
                               placeholder="Optionnel - pour proteger l'acces formateur">
                        <small>Laissez vide pour un acces sans mot de passe</small>
                    </div>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description"
                              placeholder="Description optionnelle de la session..."></textarea>
                </div>

                <button type="submit" class="btn btn-success">Creer la session</button>
            </form>
        </div>

        <!-- Liste des sessions -->
        <div class="card">
            <h2>Sessions existantes</h2>

            <?php if (empty($sessions)): ?>
                <div class="empty-state">
                    <p>Aucune session n'a encore ete creee.</p>
                </div>
            <?php else: ?>
                <table class="sessions-table">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Code</th>
                            <th>Participants</th>
                            <th>Soumis</th>
                            <th>Statut</th>
                            <th>Cree le</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sessions as $s): ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars($s['name']) ?></strong>
                                <?php if ($s['description']): ?>
                                    <br><small style="color: #7f8c8d;"><?= htmlspecialchars(substr($s['description'], 0, 50)) ?>...</small>
                                <?php endif; ?>
                            </td>
                            <td><span class="session-code"><?= htmlspecialchars($s['code']) ?></span></td>
                            <td><?= $s['participant_count'] ?></td>
                            <td><?= $s['submitted_count'] ?></td>
                            <td>
                                <?php if ($s['active']): ?>
                                    <span class="status-active">Active</span>
                                <?php else: ?>
                                    <span class="status-inactive">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td><?= date('d/m/Y', strtotime($s['created_at'])) ?></td>
                            <td>
                                <div class="action-buttons">
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="action" value="toggle">
                                        <input type="hidden" name="session_id" value="<?= $s['id'] ?>">
                                        <input type="hidden" name="active" value="<?= $s['active'] ? 0 : 1 ?>">
                                        <button type="submit" class="btn btn-sm btn-secondary">
                                            <?= $s['active'] ? 'Desactiver' : 'Activer' ?>
                                        </button>
                                    </form>

                                    <form method="POST" style="display: inline;"
                                          onsubmit="return confirm('Supprimer cette session et toutes ses donnees ?');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="session_id" value="<?= $s['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-danger">Supprimer</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <!-- Instructions -->
        <div class="card">
            <h2>Comment utiliser</h2>
            <ol style="color: #2c3e50; line-height: 1.8; padding-left: 20px;">
                <li><strong>Creez une session</strong> en remplissant le formulaire ci-dessus</li>
                <li><strong>Partagez le code</strong> avec vos participants (ex: ABC123)</li>
                <li>Les participants se connectent sur <strong>index.php</strong> avec le code</li>
                <li>Suivez les analyses en temps reel sur <strong>formateur.php</strong></li>
                <li>Projetez les resultats en mode plein ecran pour discussion collective</li>
            </ol>
        </div>
    </div>
</body>
</html>
