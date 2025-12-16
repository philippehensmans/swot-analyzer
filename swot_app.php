<?php
/**
 * Application SWOT/TOWS - Version multi-utilisateurs
 */
session_start();

// Vérifier l'authentification
if (!isset($_SESSION['participant_id'])) {
    header('Location: index.php');
    exit;
}

$participantId = $_SESSION['participant_id'];
$participantNom = $_SESSION['participant_nom'];
$participantPrenom = $_SESSION['participant_prenom'];
$sessionName = $_SESSION['session_name'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analyse SWOT/TOWS - <?= htmlspecialchars($participantPrenom . ' ' . $participantNom) ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .user-bar {
            max-width: 1200px;
            margin: 0 auto 15px auto;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
            padding: 12px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            background: linear-gradient(45deg, #667eea, #764ba2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 16px;
        }

        .user-details {
            line-height: 1.3;
        }

        .user-name {
            font-weight: 600;
            color: #2c3e50;
        }

        .session-name {
            font-size: 12px;
            color: #7f8c8d;
        }

        .user-actions {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-draft {
            background: #fff3cd;
            color: #856404;
        }

        .status-submitted {
            background: #d4edda;
            color: #155724;
        }

        .btn-logout {
            padding: 8px 16px;
            background: #ecf0f1;
            color: #2c3e50;
            border: none;
            border-radius: 20px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-logout:hover {
            background: #dfe6e9;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            padding: 30px;
            backdrop-filter: blur(10px);
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
            position: relative;
        }

        .header h1 {
            color: #2c3e50;
            font-size: 2.5em;
            margin-bottom: 10px;
            background: linear-gradient(45deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .header p {
            color: #7f8c8d;
            font-size: 1.1em;
            font-weight: 300;
        }

        .controls {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .btn-primary {
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
        }

        .btn-success {
            background: linear-gradient(45deg, #27ae60, #2ecc71);
            color: white;
        }

        .btn-secondary {
            background: #ecf0f1;
            color: #2c3e50;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .swot-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }

        .swot-quadrant {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border: 3px solid transparent;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .swot-quadrant::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--accent-color);
        }

        .swot-quadrant:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        .swot-quadrant.strengths {
            --accent-color: #27ae60;
            --accent-color-dark: #219a52;
            --accent-rgb: 39, 174, 96;
        }

        .swot-quadrant.weaknesses {
            --accent-color: #e74c3c;
            --accent-color-dark: #c0392b;
            --accent-rgb: 231, 76, 60;
        }

        .swot-quadrant.opportunities {
            --accent-color: #3498db;
            --accent-color-dark: #2980b9;
            --accent-rgb: 52, 152, 219;
        }

        .swot-quadrant.threats {
            --accent-color: #f39c12;
            --accent-color-dark: #e67e22;
            --accent-rgb: 243, 156, 18;
        }

        .quadrant-title {
            font-size: 1.4em;
            font-weight: 700;
            margin-bottom: 20px;
            color: var(--accent-color);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .quadrant-icon {
            font-size: 1.2em;
        }

        .input-section {
            margin-bottom: 20px;
        }

        .input-group {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
        }

        .item-input {
            flex: 1;
            padding: 12px 15px;
            border: 2px solid #ecf0f1;
            border-radius: 10px;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .item-input:focus {
            outline: none;
            border-color: var(--accent-color);
            box-shadow: 0 0 0 3px rgba(var(--accent-rgb), 0.1);
        }

        .add-btn {
            background: var(--accent-color);
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .add-btn:hover {
            background: var(--accent-color-dark);
            transform: scale(1.05);
        }

        .items-list {
            list-style: none;
        }

        .item {
            background: #f8f9fa;
            padding: 12px 15px;
            margin-bottom: 8px;
            border-radius: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-left: 4px solid var(--accent-color);
            transition: all 0.3s ease;
        }

        .item:hover {
            background: #e9ecef;
            transform: translateX(5px);
        }

        .item-text {
            flex: 1;
            color: #2c3e50;
            font-weight: 500;
        }

        .delete-btn {
            background: #e74c3c;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 12px;
            transition: all 0.3s ease;
        }

        .delete-btn:hover {
            background: #c0392b;
            transform: scale(1.1);
        }

        .export-section {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .export-title {
            color: #2c3e50;
            font-size: 1.3em;
            font-weight: 700;
            margin-bottom: 15px;
        }

        .ngo-suggestions {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .suggestion-title {
            color: #856404;
            font-weight: 600;
            margin-bottom: 10px;
            font-size: 14px;
        }

        .suggestion-text {
            color: #856404;
            font-size: 13px;
            line-height: 1.4;
        }

        .tows-source {
            font-size: 12px;
            color: #7f8c8d;
            margin-bottom: 15px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px);
        }

        .modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 30px;
            border-radius: 15px;
            width: 90%;
            max-width: 800px;
            max-height: 80vh;
            overflow-y: auto;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .close {
            color: #aaa;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .close:hover {
            color: #2c3e50;
        }

        .export-content {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #2c3e50;
        }

        .export-quadrant {
            margin-bottom: 30px;
            page-break-inside: avoid;
        }

        .export-quadrant h3 {
            color: var(--accent-color);
            font-size: 1.2em;
            margin-bottom: 15px;
            padding-bottom: 5px;
            border-bottom: 2px solid var(--accent-color);
        }

        .export-list {
            list-style: none;
            padding-left: 0;
        }

        .export-list li {
            padding: 8px 0;
            border-bottom: 1px solid #ecf0f1;
            position: relative;
            padding-left: 20px;
        }

        .export-list li:before {
            content: ">";
            color: var(--accent-color);
            position: absolute;
            left: 0;
            top: 8px;
        }

        #towsSection {
            display: none;
        }

        .save-indicator {
            position: fixed;
            bottom: 20px;
            right: 20px;
            padding: 10px 20px;
            border-radius: 25px;
            font-size: 14px;
            font-weight: 500;
            opacity: 0;
            transition: opacity 0.3s ease;
            z-index: 1000;
        }

        .save-indicator.saving {
            background: #fff3cd;
            color: #856404;
            opacity: 1;
        }

        .save-indicator.saved {
            background: #d4edda;
            color: #155724;
            opacity: 1;
        }

        .save-indicator.error {
            background: #f8d7da;
            color: #721c24;
            opacity: 1;
        }

        @media (max-width: 768px) {
            .swot-grid {
                grid-template-columns: 1fr;
            }

            .container {
                padding: 20px;
            }

            .header h1 {
                font-size: 2em;
            }

            .controls {
                flex-direction: column;
                align-items: center;
            }

            .user-bar {
                flex-direction: column;
                gap: 10px;
                text-align: center;
            }
        }

        @media print {
            .modal-header, .btn, .user-bar {
                display: none;
            }

            .modal-content {
                box-shadow: none;
                margin: 0;
                padding: 20px;
                max-height: none;
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <!-- Barre utilisateur -->
    <div class="user-bar">
        <div class="user-info">
            <div class="user-avatar"><?= strtoupper(substr($participantPrenom, 0, 1) . substr($participantNom, 0, 1)) ?></div>
            <div class="user-details">
                <div class="user-name"><?= htmlspecialchars($participantPrenom . ' ' . $participantNom) ?></div>
                <div class="session-name"><?= htmlspecialchars($sessionName) ?></div>
            </div>
        </div>
        <div class="user-actions">
            <span class="status-badge status-draft" id="statusBadge">Brouillon</span>
            <button class="btn-logout" onclick="logout()">Deconnexion</button>
        </div>
    </div>

    <!-- Indicateur de sauvegarde -->
    <div class="save-indicator" id="saveIndicator"></div>

    <!-- Section SWOT -->
    <div class="container" id="swotSection">
        <div class="header">
            <h1>Analyse SWOT</h1>
            <p>Outil d'analyse strategique pour associations et organisations du secteur non marchand</p>
        </div>

        <div class="ngo-suggestions">
            <div class="suggestion-title">Conseils pour les associations :</div>
            <div class="suggestion-text">
                Pensez a vos ressources humaines (benevoles, salaries), votre mission sociale, vos partenariats, votre financement, votre ancrage territorial, et l'evolution du contexte reglementaire.
            </div>
        </div>

        <div class="controls">
            <button class="btn btn-primary" onclick="exportSWOT()">Exporter l'analyse</button>
            <button class="btn btn-secondary" onclick="clearAll()">Tout effacer</button>
            <button class="btn btn-success" onclick="submitAnalysis()">Soumettre mon analyse</button>
        </div>

        <div class="swot-grid">
            <!-- FORCES -->
            <div class="swot-quadrant strengths">
                <div class="quadrant-title">
                    <span class="quadrant-icon">Forces</span>
                    (Strengths)
                </div>
                <div class="ngo-suggestions">
                    <div class="suggestion-title">Exemples pour associations :</div>
                    <div class="suggestion-text">Equipe engagee, expertise metier, reseau de partenaires, legitimite aupres des beneficiaires, ancrage local</div>
                </div>
                <div class="input-section">
                    <div class="input-group">
                        <input type="text" class="item-input" id="strengths-input" placeholder="Ajoutez une force...">
                        <button class="add-btn" onclick="addItem('strengths')">Ajouter</button>
                    </div>
                </div>
                <ul class="items-list" id="strengths-list"></ul>
            </div>

            <!-- FAIBLESSES -->
            <div class="swot-quadrant weaknesses">
                <div class="quadrant-title">
                    <span class="quadrant-icon">Faiblesses</span>
                    (Weaknesses)
                </div>
                <div class="ngo-suggestions">
                    <div class="suggestion-title">Exemples pour associations :</div>
                    <div class="suggestion-text">Ressources financieres limitees, dependance aux subventions, manque de visibilite, outils numeriques obsoletes</div>
                </div>
                <div class="input-section">
                    <div class="input-group">
                        <input type="text" class="item-input" id="weaknesses-input" placeholder="Ajoutez une faiblesse...">
                        <button class="add-btn" onclick="addItem('weaknesses')">Ajouter</button>
                    </div>
                </div>
                <ul class="items-list" id="weaknesses-list"></ul>
            </div>

            <!-- OPPORTUNITES -->
            <div class="swot-quadrant opportunities">
                <div class="quadrant-title">
                    <span class="quadrant-icon">Opportunites</span>
                    (Opportunities)
                </div>
                <div class="ngo-suggestions">
                    <div class="suggestion-title">Exemples pour associations :</div>
                    <div class="suggestion-text">Nouveaux financements europeens, digitalisation, partenariats public-prive, evolution des besoins sociaux</div>
                </div>
                <div class="input-section">
                    <div class="input-group">
                        <input type="text" class="item-input" id="opportunities-input" placeholder="Ajoutez une opportunite...">
                        <button class="add-btn" onclick="addItem('opportunities')">Ajouter</button>
                    </div>
                </div>
                <ul class="items-list" id="opportunities-list"></ul>
            </div>

            <!-- MENACES -->
            <div class="swot-quadrant threats">
                <div class="quadrant-title">
                    <span class="quadrant-icon">Menaces</span>
                    (Threats)
                </div>
                <div class="ngo-suggestions">
                    <div class="suggestion-title">Exemples pour associations :</div>
                    <div class="suggestion-text">Reduction des subventions publiques, concurrence d'autres acteurs, complexification administrative, crise economique</div>
                </div>
                <div class="input-section">
                    <div class="input-group">
                        <input type="text" class="item-input" id="threats-input" placeholder="Ajoutez une menace...">
                        <button class="add-btn" onclick="addItem('threats')">Ajouter</button>
                    </div>
                </div>
                <ul class="items-list" id="threats-list"></ul>
            </div>
        </div>

        <div class="export-section">
            <div class="export-title">Pret pour l'analyse strategique ?</div>
            <p style="color: #7f8c8d; margin-bottom: 20px;">
                Une fois votre SWOT completee, passez a l'analyse TOWS pour definir vos strategies d'action.
            </p>
            <button class="btn btn-primary" onclick="goToTOWS()" style="margin-top: 10px;">
                Passer a l'analyse TOWS
            </button>
        </div>
    </div>

    <!-- Section TOWS -->
    <div class="container" id="towsSection">
        <div class="header">
            <h1>Analyse TOWS</h1>
            <p>Matrice strategique : Transformez votre SWOT en plans d'action concrets</p>
            <button class="btn btn-secondary" onclick="backToSWOT()" style="margin-top: 15px;">
                Retour au SWOT
            </button>
        </div>

        <div class="ngo-suggestions">
            <div class="suggestion-title">Methode TOWS pour associations :</div>
            <div class="suggestion-text">
                Croisez vos elements SWOT pour identifier 4 types de strategies : Maxi-Maxi (Forces+Opportunites), Mini-Maxi (Faiblesses+Opportunites), Maxi-Mini (Forces+Menaces), Mini-Mini (Faiblesses+Menaces).
            </div>
        </div>

        <div class="controls">
            <button class="btn btn-primary" onclick="exportTOWS()">Exporter TOWS</button>
            <button class="btn btn-secondary" onclick="clearTOWS()">Effacer TOWS</button>
            <button class="btn btn-secondary" onclick="generateTOWSSuggestions()">Suggestions automatiques</button>
            <button class="btn btn-success" onclick="submitAnalysis()">Soumettre mon analyse</button>
        </div>

        <div class="swot-grid">
            <!-- SO: Forces + Opportunites -->
            <div class="swot-quadrant strengths">
                <div class="quadrant-title">
                    <span class="quadrant-icon">Strategies SO</span>
                    (Forces + Opportunites)
                </div>
                <div class="ngo-suggestions">
                    <div class="suggestion-title">Strategie Maxi-Maxi :</div>
                    <div class="suggestion-text">Comment utiliser vos forces pour saisir les opportunites ?</div>
                </div>
                <div class="tows-source">
                    <strong>Base sur vos Forces :</strong> <span id="so-strengths">Ajoutez d'abord des elements dans le SWOT</span><br>
                    <strong>Base sur vos Opportunites :</strong> <span id="so-opportunities">Ajoutez d'abord des elements dans le SWOT</span>
                </div>
                <div class="input-section">
                    <div class="input-group">
                        <input type="text" class="item-input" id="so-input" placeholder="Strategie Forces + Opportunites...">
                        <button class="add-btn" onclick="addTOWSItem('so')">Ajouter</button>
                    </div>
                </div>
                <ul class="items-list" id="so-list"></ul>
            </div>

            <!-- WO: Faiblesses + Opportunites -->
            <div class="swot-quadrant opportunities">
                <div class="quadrant-title">
                    <span class="quadrant-icon">Strategies WO</span>
                    (Faiblesses + Opportunites)
                </div>
                <div class="ngo-suggestions">
                    <div class="suggestion-title">Strategie Mini-Maxi :</div>
                    <div class="suggestion-text">Comment surmonter vos faiblesses pour saisir les opportunites ?</div>
                </div>
                <div class="tows-source">
                    <strong>Base sur vos Faiblesses :</strong> <span id="wo-weaknesses">Ajoutez d'abord des elements dans le SWOT</span><br>
                    <strong>Base sur vos Opportunites :</strong> <span id="wo-opportunities">Ajoutez d'abord des elements dans le SWOT</span>
                </div>
                <div class="input-section">
                    <div class="input-group">
                        <input type="text" class="item-input" id="wo-input" placeholder="Strategie Faiblesses + Opportunites...">
                        <button class="add-btn" onclick="addTOWSItem('wo')">Ajouter</button>
                    </div>
                </div>
                <ul class="items-list" id="wo-list"></ul>
            </div>

            <!-- ST: Forces + Menaces -->
            <div class="swot-quadrant threats">
                <div class="quadrant-title">
                    <span class="quadrant-icon">Strategies ST</span>
                    (Forces + Menaces)
                </div>
                <div class="ngo-suggestions">
                    <div class="suggestion-title">Strategie Maxi-Mini :</div>
                    <div class="suggestion-text">Comment utiliser vos forces pour contrer les menaces ?</div>
                </div>
                <div class="tows-source">
                    <strong>Base sur vos Forces :</strong> <span id="st-strengths">Ajoutez d'abord des elements dans le SWOT</span><br>
                    <strong>Base sur vos Menaces :</strong> <span id="st-threats">Ajoutez d'abord des elements dans le SWOT</span>
                </div>
                <div class="input-section">
                    <div class="input-group">
                        <input type="text" class="item-input" id="st-input" placeholder="Strategie Forces + Menaces...">
                        <button class="add-btn" onclick="addTOWSItem('st')">Ajouter</button>
                    </div>
                </div>
                <ul class="items-list" id="st-list"></ul>
            </div>

            <!-- WT: Faiblesses + Menaces -->
            <div class="swot-quadrant weaknesses">
                <div class="quadrant-title">
                    <span class="quadrant-icon">Strategies WT</span>
                    (Faiblesses + Menaces)
                </div>
                <div class="ngo-suggestions">
                    <div class="suggestion-title">Strategie Mini-Mini :</div>
                    <div class="suggestion-text">Comment minimiser vos faiblesses face aux menaces ?</div>
                </div>
                <div class="tows-source">
                    <strong>Base sur vos Faiblesses :</strong> <span id="wt-weaknesses">Ajoutez d'abord des elements dans le SWOT</span><br>
                    <strong>Base sur vos Menaces :</strong> <span id="wt-threats">Ajoutez d'abord des elements dans le SWOT</span>
                </div>
                <div class="input-section">
                    <div class="input-group">
                        <input type="text" class="item-input" id="wt-input" placeholder="Strategie Faiblesses + Menaces...">
                        <button class="add-btn" onclick="addTOWSItem('wt')">Ajouter</button>
                    </div>
                </div>
                <ul class="items-list" id="wt-list"></ul>
            </div>
        </div>

        <div class="export-section">
            <div class="export-title">Plan d'action strategique</div>
            <p style="color: #7f8c8d; margin-bottom: 20px;">
                Priorisez maintenant vos strategies TOWS et definissez un plan d'action avec echeances et responsables.
            </p>
        </div>
    </div>

    <!-- Modal d'export SWOT -->
    <div id="exportModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Analyse SWOT - Export</h2>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>
            <div class="controls" style="margin-bottom: 20px;">
                <button class="btn btn-primary" onclick="printSWOT()">Imprimer</button>
                <button class="btn btn-primary" onclick="exportToPDF()">Export PDF</button>
                <button class="btn btn-secondary" onclick="copySWOT()">Copier le texte</button>
            </div>
            <div id="exportContent" class="export-content"></div>
        </div>
    </div>

    <!-- Modal d'export TOWS -->
    <div id="exportTOWSModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Analyse TOWS - Export</h2>
                <span class="close" onclick="closeTOWSModal()">&times;</span>
            </div>
            <div class="controls" style="margin-bottom: 20px;">
                <button class="btn btn-primary" onclick="printTOWS()">Imprimer</button>
                <button class="btn btn-primary" onclick="exportTOWSToPDF()">Export PDF</button>
                <button class="btn btn-secondary" onclick="copyTOWS()">Copier le texte</button>
            </div>
            <div id="exportTOWSContent" class="export-content"></div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script>
        // Donnees SWOT et TOWS
        let swotData = {
            strengths: [],
            weaknesses: [],
            opportunities: [],
            threats: []
        };

        let towsData = {
            so: [],
            wo: [],
            st: [],
            wt: []
        };

        let isSubmitted = false;
        let saveTimeout = null;

        // ========== FONCTIONS DE SAUVEGARDE SERVEUR ==========

        function showSaveIndicator(status) {
            const indicator = document.getElementById('saveIndicator');
            indicator.className = 'save-indicator ' + status;

            if (status === 'saving') {
                indicator.textContent = 'Sauvegarde en cours...';
            } else if (status === 'saved') {
                indicator.textContent = 'Sauvegarde OK';
                setTimeout(() => {
                    indicator.style.opacity = '0';
                }, 2000);
            } else if (status === 'error') {
                indicator.textContent = 'Erreur de sauvegarde';
                setTimeout(() => {
                    indicator.style.opacity = '0';
                }, 3000);
            }
        }

        function saveToServer() {
            // Debounce pour eviter trop de requetes
            if (saveTimeout) {
                clearTimeout(saveTimeout);
            }

            saveTimeout = setTimeout(() => {
                showSaveIndicator('saving');

                fetch('api/save.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        swot: swotData,
                        tows: towsData
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showSaveIndicator('saved');
                    } else {
                        showSaveIndicator('error');
                        console.error('Erreur sauvegarde:', data.error);
                    }
                })
                .catch(error => {
                    showSaveIndicator('error');
                    console.error('Erreur reseau:', error);
                });
            }, 500);
        }

        function loadFromServer() {
            fetch('api/load.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        swotData = data.data.swot;
                        towsData = data.data.tows;
                        isSubmitted = data.data.submitted;

                        // Mettre a jour l'interface
                        Object.keys(swotData).forEach(category => {
                            updateList(category);
                        });
                        Object.keys(towsData).forEach(category => {
                            updateTOWSList(category);
                        });
                        updateTOWSSource();
                        updateStatusBadge();
                    }
                })
                .catch(error => {
                    console.error('Erreur chargement:', error);
                });
        }

        function updateStatusBadge() {
            const badge = document.getElementById('statusBadge');
            if (isSubmitted) {
                badge.textContent = 'Soumis';
                badge.className = 'status-badge status-submitted';
            } else {
                badge.textContent = 'Brouillon';
                badge.className = 'status-badge status-draft';
            }
        }

        function submitAnalysis() {
            const totalSwot = Object.values(swotData).reduce((sum, arr) => sum + arr.length, 0);
            const totalTows = Object.values(towsData).reduce((sum, arr) => sum + arr.length, 0);

            if (totalSwot < 4) {
                alert('Veuillez completer au moins un element dans chaque quadrant SWOT avant de soumettre.');
                return;
            }

            if (!confirm('Voulez-vous soumettre votre analyse ? Vous pourrez toujours la modifier apres.')) {
                return;
            }

            fetch('api/submit.php', {
                method: 'POST'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    isSubmitted = true;
                    updateStatusBadge();
                    alert('Votre analyse a ete soumise avec succes !');
                } else {
                    alert('Erreur: ' + data.error);
                }
            })
            .catch(error => {
                alert('Erreur reseau lors de la soumission.');
                console.error(error);
            });
        }

        function logout() {
            window.location.href = 'logout.php';
        }

        // ========== FONCTIONS SWOT ==========

        function addItem(category) {
            const input = document.getElementById(category + '-input');
            const text = input.value.trim();

            if (text === '') {
                alert('Veuillez saisir du texte avant d\'ajouter un element.');
                return;
            }

            swotData[category].push(text);
            input.value = '';
            updateList(category);
            saveToServer();
        }

        function removeItem(category, index) {
            swotData[category].splice(index, 1);
            updateList(category);
            saveToServer();
        }

        function updateList(category) {
            const list = document.getElementById(category + '-list');
            list.innerHTML = '';

            swotData[category].forEach((item, index) => {
                const li = document.createElement('li');
                li.className = 'item';
                li.innerHTML = `
                    <span class="item-text">${escapeHtml(item)}</span>
                    <button class="delete-btn" onclick="removeItem('${category}', ${index})">X</button>
                `;
                list.appendChild(li);
            });
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function clearAll() {
            if (confirm('Etes-vous sur de vouloir effacer toute l\'analyse SWOT ?')) {
                swotData = {
                    strengths: [],
                    weaknesses: [],
                    opportunities: [],
                    threats: []
                };

                Object.keys(swotData).forEach(category => {
                    updateList(category);
                });
                saveToServer();
            }
        }

        // ========== FONCTIONS TOWS ==========

        function goToTOWS() {
            const totalItems = Object.values(swotData).reduce((sum, arr) => sum + arr.length, 0);
            if (totalItems < 4) {
                alert('Veuillez d\'abord completer votre analyse SWOT avec au moins un element dans chaque quadrant.');
                return;
            }

            document.getElementById('swotSection').style.display = 'none';
            document.getElementById('towsSection').style.display = 'block';
            updateTOWSSource();
        }

        function backToSWOT() {
            document.getElementById('swotSection').style.display = 'block';
            document.getElementById('towsSection').style.display = 'none';
        }

        function addTOWSItem(category) {
            const input = document.getElementById(category + '-input');
            const text = input.value.trim();

            if (text === '') {
                alert('Veuillez saisir du texte avant d\'ajouter une strategie.');
                return;
            }

            towsData[category].push(text);
            input.value = '';
            updateTOWSList(category);
            saveToServer();
        }

        function removeTOWSItem(category, index) {
            towsData[category].splice(index, 1);
            updateTOWSList(category);
            saveToServer();
        }

        function updateTOWSList(category) {
            const list = document.getElementById(category + '-list');
            list.innerHTML = '';

            towsData[category].forEach((item, index) => {
                const li = document.createElement('li');
                li.className = 'item';
                li.innerHTML = `
                    <span class="item-text">${escapeHtml(item)}</span>
                    <button class="delete-btn" onclick="removeTOWSItem('${category}', ${index})">X</button>
                `;
                list.appendChild(li);
            });
        }

        function updateTOWSSource() {
            document.getElementById('so-strengths').textContent =
                swotData.strengths.length > 0 ? swotData.strengths.slice(0, 2).join(', ') + '...' : 'Aucune force definie';
            document.getElementById('so-opportunities').textContent =
                swotData.opportunities.length > 0 ? swotData.opportunities.slice(0, 2).join(', ') + '...' : 'Aucune opportunite definie';

            document.getElementById('wo-weaknesses').textContent =
                swotData.weaknesses.length > 0 ? swotData.weaknesses.slice(0, 2).join(', ') + '...' : 'Aucune faiblesse definie';
            document.getElementById('wo-opportunities').textContent =
                swotData.opportunities.length > 0 ? swotData.opportunities.slice(0, 2).join(', ') + '...' : 'Aucune opportunite definie';

            document.getElementById('st-strengths').textContent =
                swotData.strengths.length > 0 ? swotData.strengths.slice(0, 2).join(', ') + '...' : 'Aucune force definie';
            document.getElementById('st-threats').textContent =
                swotData.threats.length > 0 ? swotData.threats.slice(0, 2).join(', ') + '...' : 'Aucune menace definie';

            document.getElementById('wt-weaknesses').textContent =
                swotData.weaknesses.length > 0 ? swotData.weaknesses.slice(0, 2).join(', ') + '...' : 'Aucune faiblesse definie';
            document.getElementById('wt-threats').textContent =
                swotData.threats.length > 0 ? swotData.threats.slice(0, 2).join(', ') + '...' : 'Aucune menace definie';
        }

        function clearTOWS() {
            if (confirm('Etes-vous sur de vouloir effacer toute l\'analyse TOWS ?')) {
                towsData = { so: [], wo: [], st: [], wt: [] };
                Object.keys(towsData).forEach(category => {
                    updateTOWSList(category);
                });
                saveToServer();
            }
        }

        function generateTOWSSuggestions() {
            const suggestions = {
                so: [
                    "Developper de nouveaux programmes en exploitant notre expertise reconnue",
                    "Elargir notre territoire d'action grace a nos partenariats solides",
                    "Creer une offre de formation basee sur notre savoir-faire"
                ],
                wo: [
                    "Former l'equipe aux outils numeriques pour acceder aux financements digitaux",
                    "Developper une strategie de communication pour saisir les opportunites mediatiques",
                    "Creer des partenariats pour pallier nos manques de ressources"
                ],
                st: [
                    "Utiliser notre reseau pour diversifier nos sources de financement",
                    "Capitaliser sur notre reputation pour maintenir notre position face a la concurrence",
                    "Exploiter notre ancrage local pour resister aux changements reglementaires"
                ],
                wt: [
                    "Mutualiser les couts avec d'autres associations pour reduire notre vulnerabilite",
                    "Developper des partenariats de secours en cas de reduction des subventions",
                    "Creer une reserve financiere pour faire face aux crises"
                ]
            };

            let added = false;
            Object.keys(suggestions).forEach(category => {
                if (towsData[category].length < 2) {
                    const suggestion = suggestions[category][Math.floor(Math.random() * suggestions[category].length)];
                    towsData[category].push(suggestion);
                    updateTOWSList(category);
                    added = true;
                }
            });

            if (added) {
                alert('Suggestions ajoutees ! Personnalisez-les selon votre contexte.');
                saveToServer();
            } else {
                alert('Vous avez deja suffisamment de strategies dans chaque quadrant.');
            }
        }

        // ========== FONCTIONS D'EXPORT ==========

        function exportSWOT() {
            const modal = document.getElementById('exportModal');
            const content = document.getElementById('exportContent');

            const categories = {
                strengths: { title: 'Forces (Strengths)', color: '#27ae60' },
                weaknesses: { title: 'Faiblesses (Weaknesses)', color: '#e74c3c' },
                opportunities: { title: 'Opportunites (Opportunities)', color: '#3498db' },
                threats: { title: 'Menaces (Threats)', color: '#f39c12' }
            };

            let html = `
                <div style="text-align: center; margin-bottom: 30px;">
                    <h1 style="color: #2c3e50; margin-bottom: 10px;">Analyse SWOT</h1>
                    <p style="color: #7f8c8d;">Par <?= htmlspecialchars($participantPrenom . ' ' . $participantNom) ?> - ${new Date().toLocaleDateString('fr-FR')}</p>
                </div>
            `;

            Object.keys(categories).forEach(category => {
                const cat = categories[category];
                html += `
                    <div class="export-quadrant ${category}" style="--accent-color: ${cat.color};">
                        <h3>${cat.title}</h3>
                `;

                if (swotData[category].length > 0) {
                    html += '<ul class="export-list">';
                    swotData[category].forEach(item => {
                        html += `<li>${escapeHtml(item)}</li>`;
                    });
                    html += '</ul>';
                } else {
                    html += '<p style="color: #95a5a6; font-style: italic;">Aucun element ajoute</p>';
                }

                html += '</div>';
            });

            content.innerHTML = html;
            modal.style.display = 'block';
        }

        function closeModal() {
            document.getElementById('exportModal').style.display = 'none';
        }

        function printSWOT() {
            window.print();
        }

        function copySWOT() {
            const categories = {
                strengths: 'FORCES (STRENGTHS)',
                weaknesses: 'FAIBLESSES (WEAKNESSES)',
                opportunities: 'OPPORTUNITES (OPPORTUNITIES)',
                threats: 'MENACES (THREATS)'
            };

            let text = `ANALYSE SWOT\n`;
            text += `Par: <?= htmlspecialchars($participantPrenom . ' ' . $participantNom) ?>\n`;
            text += `Date: ${new Date().toLocaleDateString('fr-FR')}\n\n`;

            Object.keys(categories).forEach(category => {
                text += `${categories[category]}\n`;
                text += '='.repeat(categories[category].length) + '\n';

                if (swotData[category].length > 0) {
                    swotData[category].forEach(item => {
                        text += `- ${item}\n`;
                    });
                } else {
                    text += 'Aucun element ajoute\n';
                }
                text += '\n';
            });

            navigator.clipboard.writeText(text).then(() => {
                alert('Analyse SWOT copiee dans le presse-papier !');
            }).catch(() => {
                alert('Erreur lors de la copie.');
            });
        }

        function exportToPDF() {
            try {
                const { jsPDF } = window.jspdf;
                const pdf = new jsPDF();

                const pageWidth = pdf.internal.pageSize.getWidth();
                const margin = 20;
                const lineHeight = 8;
                let currentY = 30;

                pdf.setFontSize(20);
                pdf.setTextColor(44, 62, 80);
                pdf.text('ANALYSE SWOT', pageWidth / 2, currentY, { align: 'center' });

                currentY += 10;
                pdf.setFontSize(12);
                pdf.setTextColor(127, 140, 141);
                pdf.text('Par: <?= htmlspecialchars($participantPrenom . ' ' . $participantNom) ?>', pageWidth / 2, currentY, { align: 'center' });
                pdf.text(`Date: ${new Date().toLocaleDateString('fr-FR')}`, pageWidth / 2, currentY + 5, { align: 'center' });

                currentY += 25;

                const categories = {
                    strengths: { title: 'FORCES (STRENGTHS)', color: [39, 174, 96] },
                    weaknesses: { title: 'FAIBLESSES (WEAKNESSES)', color: [231, 76, 60] },
                    opportunities: { title: 'OPPORTUNITES (OPPORTUNITIES)', color: [52, 152, 219] },
                    threats: { title: 'MENACES (THREATS)', color: [243, 156, 18] }
                };

                Object.keys(categories).forEach(category => {
                    const cat = categories[category];

                    if (currentY > 250) {
                        pdf.addPage();
                        currentY = 30;
                    }

                    pdf.setFontSize(14);
                    pdf.setTextColor(cat.color[0], cat.color[1], cat.color[2]);
                    pdf.text(cat.title, margin, currentY);

                    pdf.setDrawColor(cat.color[0], cat.color[1], cat.color[2]);
                    pdf.setLineWidth(0.5);
                    pdf.line(margin, currentY + 2, pageWidth - margin, currentY + 2);

                    currentY += 10;

                    pdf.setFontSize(10);
                    pdf.setTextColor(44, 62, 80);

                    if (swotData[category].length > 0) {
                        swotData[category].forEach(item => {
                            const lines = pdf.splitTextToSize(`- ${item}`, pageWidth - 2 * margin);
                            lines.forEach(line => {
                                if (currentY > 270) {
                                    pdf.addPage();
                                    currentY = 30;
                                }
                                pdf.text(line, margin, currentY);
                                currentY += lineHeight;
                            });
                        });
                    } else {
                        pdf.setTextColor(149, 165, 166);
                        pdf.text('Aucun element ajoute', margin, currentY);
                        currentY += lineHeight;
                    }

                    currentY += 10;
                });

                const fileName = `SWOT_<?= preg_replace('/[^a-zA-Z0-9]/', '_', $participantPrenom . '_' . $participantNom) ?>_${new Date().toISOString().split('T')[0]}.pdf`;
                pdf.save(fileName);
                alert('PDF genere avec succes !');

            } catch (error) {
                alert('Erreur lors de la generation du PDF.');
                console.error(error);
            }
        }

        function exportTOWS() {
            const modal = document.getElementById('exportTOWSModal');
            const content = document.getElementById('exportTOWSContent');

            const categories = {
                so: { title: 'Strategies SO (Forces + Opportunites)', color: '#27ae60' },
                wo: { title: 'Strategies WO (Faiblesses + Opportunites)', color: '#3498db' },
                st: { title: 'Strategies ST (Forces + Menaces)', color: '#f39c12' },
                wt: { title: 'Strategies WT (Faiblesses + Menaces)', color: '#e74c3c' }
            };

            let html = `
                <div style="text-align: center; margin-bottom: 30px;">
                    <h1 style="color: #2c3e50; margin-bottom: 10px;">Analyse TOWS</h1>
                    <p style="color: #7f8c8d;">Par <?= htmlspecialchars($participantPrenom . ' ' . $participantNom) ?> - ${new Date().toLocaleDateString('fr-FR')}</p>
                </div>
            `;

            Object.keys(categories).forEach(category => {
                const cat = categories[category];
                html += `
                    <div class="export-quadrant ${category}" style="--accent-color: ${cat.color};">
                        <h3>${cat.title}</h3>
                `;

                if (towsData[category].length > 0) {
                    html += '<ul class="export-list">';
                    towsData[category].forEach(item => {
                        html += `<li>${escapeHtml(item)}</li>`;
                    });
                    html += '</ul>';
                } else {
                    html += '<p style="color: #95a5a6; font-style: italic;">Aucune strategie definie</p>';
                }

                html += '</div>';
            });

            content.innerHTML = html;
            modal.style.display = 'block';
        }

        function closeTOWSModal() {
            document.getElementById('exportTOWSModal').style.display = 'none';
        }

        function printTOWS() {
            window.print();
        }

        function copyTOWS() {
            const categories = {
                so: 'STRATEGIES SO (FORCES + OPPORTUNITES)',
                wo: 'STRATEGIES WO (FAIBLESSES + OPPORTUNITES)',
                st: 'STRATEGIES ST (FORCES + MENACES)',
                wt: 'STRATEGIES WT (FAIBLESSES + MENACES)'
            };

            let text = `ANALYSE TOWS\n`;
            text += `Par: <?= htmlspecialchars($participantPrenom . ' ' . $participantNom) ?>\n`;
            text += `Date: ${new Date().toLocaleDateString('fr-FR')}\n\n`;

            Object.keys(categories).forEach(category => {
                text += `${categories[category]}\n`;
                text += '='.repeat(categories[category].length) + '\n';

                if (towsData[category].length > 0) {
                    towsData[category].forEach(item => {
                        text += `- ${item}\n`;
                    });
                } else {
                    text += 'Aucune strategie definie\n';
                }
                text += '\n';
            });

            navigator.clipboard.writeText(text).then(() => {
                alert('Analyse TOWS copiee dans le presse-papier !');
            }).catch(() => {
                alert('Erreur lors de la copie.');
            });
        }

        function exportTOWSToPDF() {
            try {
                const { jsPDF } = window.jspdf;
                const pdf = new jsPDF();

                const pageWidth = pdf.internal.pageSize.getWidth();
                const margin = 20;
                const lineHeight = 8;
                let currentY = 30;

                pdf.setFontSize(20);
                pdf.setTextColor(44, 62, 80);
                pdf.text('ANALYSE TOWS', pageWidth / 2, currentY, { align: 'center' });

                currentY += 10;
                pdf.setFontSize(12);
                pdf.setTextColor(127, 140, 141);
                pdf.text('Par: <?= htmlspecialchars($participantPrenom . ' ' . $participantNom) ?>', pageWidth / 2, currentY, { align: 'center' });
                pdf.text(`Date: ${new Date().toLocaleDateString('fr-FR')}`, pageWidth / 2, currentY + 5, { align: 'center' });

                currentY += 25;

                const categories = {
                    so: { title: 'STRATEGIES SO (FORCES + OPPORTUNITES)', color: [39, 174, 96] },
                    wo: { title: 'STRATEGIES WO (FAIBLESSES + OPPORTUNITES)', color: [52, 152, 219] },
                    st: { title: 'STRATEGIES ST (FORCES + MENACES)', color: [243, 156, 18] },
                    wt: { title: 'STRATEGIES WT (FAIBLESSES + MENACES)', color: [231, 76, 60] }
                };

                Object.keys(categories).forEach(category => {
                    const cat = categories[category];

                    if (currentY > 250) {
                        pdf.addPage();
                        currentY = 30;
                    }

                    pdf.setFontSize(14);
                    pdf.setTextColor(cat.color[0], cat.color[1], cat.color[2]);
                    pdf.text(cat.title, margin, currentY);

                    pdf.setDrawColor(cat.color[0], cat.color[1], cat.color[2]);
                    pdf.setLineWidth(0.5);
                    pdf.line(margin, currentY + 2, pageWidth - margin, currentY + 2);

                    currentY += 10;

                    pdf.setFontSize(10);
                    pdf.setTextColor(44, 62, 80);

                    if (towsData[category].length > 0) {
                        towsData[category].forEach(item => {
                            const lines = pdf.splitTextToSize(`- ${item}`, pageWidth - 2 * margin);
                            lines.forEach(line => {
                                if (currentY > 270) {
                                    pdf.addPage();
                                    currentY = 30;
                                }
                                pdf.text(line, margin, currentY);
                                currentY += lineHeight;
                            });
                        });
                    } else {
                        pdf.setTextColor(149, 165, 166);
                        pdf.text('Aucune strategie definie', margin, currentY);
                        currentY += lineHeight;
                    }

                    currentY += 10;
                });

                const fileName = `TOWS_<?= preg_replace('/[^a-zA-Z0-9]/', '_', $participantPrenom . '_' . $participantNom) ?>_${new Date().toISOString().split('T')[0]}.pdf`;
                pdf.save(fileName);
                alert('PDF TOWS genere avec succes !');

            } catch (error) {
                alert('Erreur lors de la generation du PDF TOWS.');
                console.error(error);
            }
        }

        // ========== EVENT LISTENERS ==========

        document.addEventListener('DOMContentLoaded', function() {
            // Charger les donnees depuis le serveur
            loadFromServer();

            // Event listeners pour les touches Entree - SWOT
            ['strengths', 'weaknesses', 'opportunities', 'threats'].forEach(category => {
                const input = document.getElementById(category + '-input');
                input.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        addItem(category);
                    }
                });
            });

            // Event listeners pour les touches Entree - TOWS
            ['so', 'wo', 'st', 'wt'].forEach(category => {
                const input = document.getElementById(category + '-input');
                if (input) {
                    input.addEventListener('keypress', function(e) {
                        if (e.key === 'Enter') {
                            addTOWSItem(category);
                        }
                    });
                }
            });
        });

        // Fermer les modals en cliquant a l'exterieur
        window.onclick = function(event) {
            const modal = document.getElementById('exportModal');
            const towsModal = document.getElementById('exportTOWSModal');
            if (event.target == modal) {
                closeModal();
            }
            if (event.target == towsModal) {
                closeTOWSModal();
            }
        }
    </script>
</body>
</html>
