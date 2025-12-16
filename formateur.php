<?php
/**
 * Interface Formateur - Visualisation des analyses des participants
 */
session_start();
require_once __DIR__ . '/config/database.php';

$db = getDB();
$error = '';
$isLoggedIn = isset($_SESSION['formateur_session_id']);

// Traitement de la connexion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
    $sessionCode = trim($_POST['session_code'] ?? '');
    $password = trim($_POST['password'] ?? '');

    try {
        $stmt = $db->prepare("SELECT id, name, formateur_password FROM sessions WHERE code = ? AND active = 1");
        $stmt->execute([$sessionCode]);
        $session = $stmt->fetch();

        if (!$session) {
            $error = "Code de session invalide.";
        } elseif ($session['formateur_password'] && $session['formateur_password'] !== $password) {
            $error = "Mot de passe incorrect.";
        } else {
            $_SESSION['formateur_session_id'] = $session['id'];
            $_SESSION['formateur_session_name'] = $session['name'];
            $_SESSION['formateur_session_code'] = $sessionCode;
            $isLoggedIn = true;
        }
    } catch (Exception $e) {
        $error = "Erreur de connexion.";
    }
}

// Déconnexion
if (isset($_GET['logout'])) {
    unset($_SESSION['formateur_session_id']);
    unset($_SESSION['formateur_session_name']);
    unset($_SESSION['formateur_session_code']);
    header('Location: formateur.php');
    exit;
}

// Récupérer les données si connecté
$participants = [];
$sessionName = '';
$sessionCode = '';

if ($isLoggedIn) {
    $sessionId = $_SESSION['formateur_session_id'];
    $sessionName = $_SESSION['formateur_session_name'];
    $sessionCode = $_SESSION['formateur_session_code'];

    try {
        $stmt = $db->prepare("
            SELECT
                p.id,
                p.nom,
                p.prenom,
                p.organisation,
                p.created_at,
                a.swot_data,
                a.tows_data,
                a.submitted,
                a.submitted_at,
                a.updated_at
            FROM participants p
            LEFT JOIN analyses a ON p.id = a.participant_id
            WHERE p.session_id = ?
            ORDER BY p.nom, p.prenom
        ");
        $stmt->execute([$sessionId]);
        $participants = $stmt->fetchAll();
    } catch (Exception $e) {
        $error = "Erreur lors du chargement des participants.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formateur - Analyse SWOT/TOWS</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .login-container {
            max-width: 400px;
            margin: 100px auto;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
            padding: 40px;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
        }

        .header-bar {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            padding: 20px 30px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .header-info h1 {
            color: #2c3e50;
            font-size: 1.8em;
            margin-bottom: 5px;
        }

        .header-info p {
            color: #7f8c8d;
            font-size: 14px;
        }

        .header-info .session-code {
            background: #ecf0f1;
            padding: 5px 15px;
            border-radius: 20px;
            font-family: monospace;
            font-size: 16px;
            color: #2c3e50;
            display: inline-block;
            margin-top: 5px;
        }

        .header-actions {
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background: linear-gradient(45deg, #3498db, #2980b9);
            color: white;
        }

        .btn-secondary {
            background: #ecf0f1;
            color: #2c3e50;
        }

        .btn-success {
            background: linear-gradient(45deg, #27ae60, #2ecc71);
            color: white;
        }

        .btn-danger {
            background: linear-gradient(45deg, #e74c3c, #c0392b);
            color: white;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .stats-bar {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .stat-card .number {
            font-size: 2.5em;
            font-weight: 700;
            color: #2c3e50;
        }

        .stat-card .label {
            color: #7f8c8d;
            font-size: 14px;
            margin-top: 5px;
        }

        .stat-card.submitted .number { color: #27ae60; }
        .stat-card.pending .number { color: #f39c12; }
        .stat-card.empty .number { color: #e74c3c; }

        .main-content {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            border-bottom: 2px solid #ecf0f1;
            padding-bottom: 10px;
        }

        .tab {
            padding: 10px 20px;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            color: #7f8c8d;
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .tab:hover {
            background: #ecf0f1;
        }

        .tab.active {
            background: linear-gradient(45deg, #3498db, #2980b9);
            color: white;
        }

        .participants-table {
            width: 100%;
            border-collapse: collapse;
        }

        .participants-table th,
        .participants-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ecf0f1;
        }

        .participants-table th {
            background: #f8f9fa;
            font-weight: 600;
            color: #2c3e50;
        }

        .participants-table tr:hover {
            background: #f8f9fa;
        }

        .status-badge {
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-submitted {
            background: #d4edda;
            color: #155724;
        }

        .status-draft {
            background: #fff3cd;
            color: #856404;
        }

        .status-empty {
            background: #f8d7da;
            color: #721c24;
        }

        .action-buttons {
            display: flex;
            gap: 5px;
        }

        .action-btn {
            padding: 6px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 12px;
            transition: all 0.3s ease;
        }

        .action-btn.view {
            background: #3498db;
            color: white;
        }

        .action-btn.compare {
            background: #9b59b6;
            color: white;
        }

        .checkbox-cell {
            width: 40px;
        }

        .checkbox-cell input {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }

        /* Modal pour affichage détaillé */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.8);
            overflow-y: auto;
        }

        .modal-content {
            background-color: white;
            margin: 20px auto;
            padding: 30px;
            border-radius: 15px;
            width: 95%;
            max-width: 1200px;
            position: relative;
        }

        .modal-content.fullscreen {
            width: 100%;
            max-width: none;
            height: 100vh;
            margin: 0;
            border-radius: 0;
            overflow-y: auto;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 2px solid #ecf0f1;
        }

        .modal-header h2 {
            color: #2c3e50;
            font-size: 1.8em;
        }

        .modal-actions {
            display: flex;
            gap: 10px;
        }

        .close-btn {
            background: #e74c3c;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 25px;
            cursor: pointer;
            font-weight: 600;
        }

        .fullscreen-btn {
            background: #3498db;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 25px;
            cursor: pointer;
            font-weight: 600;
        }

        /* Grille SWOT dans le modal */
        .swot-display {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }

        .swot-quadrant-display {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 20px;
            border-left: 5px solid var(--color);
        }

        .swot-quadrant-display.strengths { --color: #27ae60; }
        .swot-quadrant-display.weaknesses { --color: #e74c3c; }
        .swot-quadrant-display.opportunities { --color: #3498db; }
        .swot-quadrant-display.threats { --color: #f39c12; }

        .swot-quadrant-display h3 {
            color: var(--color);
            margin-bottom: 15px;
            font-size: 1.2em;
        }

        .swot-quadrant-display ul {
            list-style: none;
            padding: 0;
        }

        .swot-quadrant-display li {
            padding: 8px 0;
            border-bottom: 1px solid #ecf0f1;
            color: #2c3e50;
        }

        .swot-quadrant-display li:last-child {
            border-bottom: none;
        }

        .empty-message {
            color: #95a5a6;
            font-style: italic;
        }

        /* Comparaison */
        .comparison-grid {
            display: grid;
            gap: 20px;
        }

        .comparison-participant {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .comparison-participant h3 {
            color: #2c3e50;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #ecf0f1;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            color: #2c3e50;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #ecf0f1;
            border-radius: 10px;
            font-size: 16px;
        }

        .form-group input:focus {
            outline: none;
            border-color: #3498db;
        }

        .error {
            background: #fee;
            border: 1px solid #e74c3c;
            color: #c0392b;
            padding: 12px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: center;
        }

        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .login-header h1 {
            color: #2c3e50;
            font-size: 1.8em;
            margin-bottom: 10px;
        }

        .login-header p {
            color: #7f8c8d;
        }

        @media (max-width: 768px) {
            .stats-bar {
                grid-template-columns: repeat(2, 1fr);
            }

            .swot-display {
                grid-template-columns: 1fr;
            }

            .header-bar {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
        }

        @media print {
            .header-bar, .stats-bar, .tabs, .action-buttons, .modal-actions {
                display: none !important;
            }

            body {
                background: white;
                padding: 0;
            }

            .modal {
                position: static;
                background: none;
            }

            .modal-content {
                margin: 0;
                padding: 20px;
                box-shadow: none;
            }
        }
    </style>
</head>
<body>
<?php if (!$isLoggedIn): ?>
    <!-- Formulaire de connexion -->
    <div class="login-container">
        <div class="login-header">
            <h1>Espace Formateur</h1>
            <p>Connectez-vous pour voir les analyses des participants</p>
        </div>

        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST">
            <input type="hidden" name="action" value="login">

            <div class="form-group">
                <label for="session_code">Code de session</label>
                <input type="text" id="session_code" name="session_code"
                       placeholder="Entrez le code de la session" required>
            </div>

            <div class="form-group">
                <label for="password">Mot de passe formateur (optionnel)</label>
                <input type="password" id="password" name="password"
                       placeholder="Mot de passe si defini">
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%;">
                Acceder a la session
            </button>
        </form>

        <div style="text-align: center; margin-top: 20px;">
            <a href="index.php" style="color: #3498db;">Retour a l'accueil</a>
            &nbsp;|&nbsp;
            <a href="admin_sessions.php" style="color: #3498db;">Creer une session</a>
        </div>
    </div>

<?php else: ?>
    <!-- Interface formateur -->
    <div class="container">
        <div class="header-bar">
            <div class="header-info">
                <h1>Tableau de bord Formateur</h1>
                <p>Session: <?= htmlspecialchars($sessionName) ?></p>
                <span class="session-code"><?= htmlspecialchars($sessionCode) ?></span>
            </div>
            <div class="header-actions">
                <button class="btn btn-success" onclick="compareSelected()">Comparer la selection</button>
                <a href="?logout=1" class="btn btn-secondary">Deconnexion</a>
            </div>
        </div>

        <?php
        $totalParticipants = count($participants);
        $submittedCount = 0;
        $draftCount = 0;
        $emptyCount = 0;

        foreach ($participants as $p) {
            if ($p['submitted']) {
                $submittedCount++;
            } elseif ($p['swot_data']) {
                $draftCount++;
            } else {
                $emptyCount++;
            }
        }
        ?>

        <div class="stats-bar">
            <div class="stat-card">
                <div class="number"><?= $totalParticipants ?></div>
                <div class="label">Total participants</div>
            </div>
            <div class="stat-card submitted">
                <div class="number"><?= $submittedCount ?></div>
                <div class="label">Soumis</div>
            </div>
            <div class="stat-card pending">
                <div class="number"><?= $draftCount ?></div>
                <div class="label">En cours</div>
            </div>
            <div class="stat-card empty">
                <div class="number"><?= $emptyCount ?></div>
                <div class="label">Non commences</div>
            </div>
        </div>

        <div class="main-content">
            <div class="tabs">
                <button class="tab active" onclick="showTab('list')">Liste des participants</button>
                <button class="tab" onclick="showTab('comparison')">Comparaison</button>
            </div>

            <div id="tab-list">
                <table class="participants-table">
                    <thead>
                        <tr>
                            <th class="checkbox-cell">
                                <input type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                            </th>
                            <th>Participant</th>
                            <th>Organisation</th>
                            <th>Statut</th>
                            <th>Derniere modification</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($participants as $p): ?>
                        <tr>
                            <td class="checkbox-cell">
                                <input type="checkbox" class="participant-checkbox"
                                       value="<?= $p['id'] ?>"
                                       data-name="<?= htmlspecialchars($p['prenom'] . ' ' . $p['nom']) ?>">
                            </td>
                            <td>
                                <strong><?= htmlspecialchars($p['prenom'] . ' ' . $p['nom']) ?></strong>
                            </td>
                            <td><?= htmlspecialchars($p['organisation'] ?? '-') ?></td>
                            <td>
                                <?php if ($p['submitted']): ?>
                                    <span class="status-badge status-submitted">Soumis</span>
                                <?php elseif ($p['swot_data']): ?>
                                    <span class="status-badge status-draft">En cours</span>
                                <?php else: ?>
                                    <span class="status-badge status-empty">Non commence</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($p['updated_at']): ?>
                                    <?= date('d/m/Y H:i', strtotime($p['updated_at'])) ?>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <?php if ($p['swot_data']): ?>
                                    <button class="action-btn view" data-participant="<?= htmlspecialchars(json_encode([
                                        'id' => $p['id'],
                                        'nom' => $p['nom'],
                                        'prenom' => $p['prenom'],
                                        'organisation' => $p['organisation'],
                                        'swot' => json_decode($p['swot_data'], true),
                                        'tows' => json_decode($p['tows_data'], true)
                                    ], JSON_HEX_APOS | JSON_HEX_QUOT), ENT_QUOTES, 'UTF-8') ?>" onclick="viewAnalysisFromButton(this)">
                                        Voir
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <?php if (empty($participants)): ?>
                <p style="text-align: center; color: #7f8c8d; padding: 40px;">
                    Aucun participant n'a encore rejoint cette session.
                </p>
                <?php endif; ?>
            </div>

            <div id="tab-comparison" style="display: none;">
                <p style="color: #7f8c8d; margin-bottom: 20px;">
                    Selectionnez des participants dans la liste et cliquez sur "Comparer la selection"
                    pour voir leurs analyses cote a cote.
                </p>
                <div id="comparison-content"></div>
            </div>
        </div>
    </div>

    <!-- Modal pour voir une analyse -->
    <div id="viewModal" class="modal">
        <div class="modal-content" id="modalContent">
            <div class="modal-header">
                <h2 id="modalTitle">Analyse de...</h2>
                <div class="modal-actions">
                    <button class="fullscreen-btn" onclick="toggleFullscreen()">Plein ecran</button>
                    <button class="btn btn-primary" onclick="printAnalysis()">Imprimer</button>
                    <button class="close-btn" onclick="closeModal()">Fermer</button>
                </div>
            </div>
            <div id="modalBody"></div>
        </div>
    </div>

    <!-- Données des participants en JSON pour JavaScript -->
    <script>
        const participantsData = <?= json_encode(array_map(function($p) {
            return [
                'id' => $p['id'],
                'nom' => $p['nom'],
                'prenom' => $p['prenom'],
                'organisation' => $p['organisation'],
                'swot' => json_decode($p['swot_data'], true) ?? [],
                'tows' => json_decode($p['tows_data'], true) ?? []
            ];
        }, $participants)) ?>;
    </script>

    <script>
        function showTab(tabName) {
            document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
            document.getElementById('tab-list').style.display = 'none';
            document.getElementById('tab-comparison').style.display = 'none';

            event.target.classList.add('active');
            document.getElementById('tab-' + tabName).style.display = 'block';
        }

        function toggleSelectAll() {
            const checked = document.getElementById('selectAll').checked;
            document.querySelectorAll('.participant-checkbox').forEach(cb => {
                cb.checked = checked;
            });
        }

        function viewAnalysisFromButton(button) {
            try {
                const jsonData = button.getAttribute('data-participant');
                const data = JSON.parse(jsonData);
                viewAnalysis(data);
            } catch (e) {
                console.error('Erreur parsing JSON:', e);
                alert('Erreur lors du chargement de l\'analyse.');
            }
        }

        function viewAnalysis(data) {
            const modal = document.getElementById('viewModal');
            const title = document.getElementById('modalTitle');
            const body = document.getElementById('modalBody');

            title.textContent = `Analyse de ${data.prenom} ${data.nom}`;

            let html = generateAnalysisHTML(data);
            body.innerHTML = html;

            modal.style.display = 'block';
        }

        function generateAnalysisHTML(data) {
            const swotCategories = {
                strengths: 'Forces',
                weaknesses: 'Faiblesses',
                opportunities: 'Opportunites',
                threats: 'Menaces'
            };

            const towsCategories = {
                so: 'Strategies SO (Forces + Opportunites)',
                wo: 'Strategies WO (Faiblesses + Opportunites)',
                st: 'Strategies ST (Forces + Menaces)',
                wt: 'Strategies WT (Faiblesses + Menaces)'
            };

            let html = '<h3 style="color: #2c3e50; margin-bottom: 20px;">Analyse SWOT</h3>';
            html += '<div class="swot-display">';

            for (const [key, label] of Object.entries(swotCategories)) {
                const items = data.swot?.[key] || [];
                html += `
                    <div class="swot-quadrant-display ${key}">
                        <h3>${label}</h3>
                        ${items.length > 0
                            ? '<ul>' + items.map(i => `<li>${escapeHtml(i)}</li>`).join('') + '</ul>'
                            : '<p class="empty-message">Aucun element</p>'
                        }
                    </div>
                `;
            }

            html += '</div>';

            // TOWS
            const hasTows = data.tows && Object.values(data.tows).some(arr => arr && arr.length > 0);

            if (hasTows) {
                html += '<h3 style="color: #2c3e50; margin: 30px 0 20px;">Analyse TOWS</h3>';
                html += '<div class="swot-display">';

                for (const [key, label] of Object.entries(towsCategories)) {
                    const items = data.tows?.[key] || [];
                    const colorClass = key === 'so' ? 'strengths' : key === 'wo' ? 'opportunities' : key === 'st' ? 'threats' : 'weaknesses';
                    html += `
                        <div class="swot-quadrant-display ${colorClass}">
                            <h3>${label}</h3>
                            ${items.length > 0
                                ? '<ul>' + items.map(i => `<li>${escapeHtml(i)}</li>`).join('') + '</ul>'
                                : '<p class="empty-message">Aucune strategie</p>'
                            }
                        </div>
                    `;
                }

                html += '</div>';
            }

            return html;
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function closeModal() {
            document.getElementById('viewModal').style.display = 'none';
            document.getElementById('modalContent').classList.remove('fullscreen');
        }

        function toggleFullscreen() {
            document.getElementById('modalContent').classList.toggle('fullscreen');
        }

        function printAnalysis() {
            window.print();
        }

        function compareSelected() {
            const selected = [];
            document.querySelectorAll('.participant-checkbox:checked').forEach(cb => {
                const participant = participantsData.find(p => p.id == cb.value);
                if (participant) {
                    selected.push(participant);
                }
            });

            if (selected.length < 2) {
                alert('Veuillez selectionner au moins 2 participants pour comparer.');
                return;
            }

            // Afficher l'onglet comparaison
            document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.tab')[1].classList.add('active');
            document.getElementById('tab-list').style.display = 'none';
            document.getElementById('tab-comparison').style.display = 'block';

            // Générer le contenu de comparaison
            let html = '';

            selected.forEach(p => {
                html += `
                    <div class="comparison-participant">
                        <h3>${escapeHtml(p.prenom)} ${escapeHtml(p.nom)}
                            ${p.organisation ? `<span style="font-weight: normal; color: #7f8c8d;"> - ${escapeHtml(p.organisation)}</span>` : ''}
                        </h3>
                        ${generateAnalysisHTML(p)}
                    </div>
                `;
            });

            document.getElementById('comparison-content').innerHTML = html;
        }

        // Fermer le modal en cliquant à l'extérieur
        window.onclick = function(event) {
            const modal = document.getElementById('viewModal');
            if (event.target === modal) {
                closeModal();
            }
        }

        // Touche Escape pour fermer
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal();
            }
        });
    </script>
<?php endif; ?>
</body>
</html>
