<?php include '../auth.php'; ?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analyse SWOT/TOWS - Secteur Non Marchand</title>
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

        .btn-secondary {
            background: #ecf0f1;
            color: #2c3e50;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
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
            content: "▸";
            color: var(--accent-color);
            position: absolute;
            left: 0;
            top: 8px;
        }

        #towsSection {
            display: none;
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
        }

        @media print {
            .modal-header, .btn {
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
    <!-- Section SWOT -->
    <div class="container" id="swotSection">
        <div class="header">
            <h1>Analyse SWOT</h1>
            <p>Outil d'analyse stratégique pour associations et organisations du secteur non marchand</p>
        </div>

        <div class="ngo-suggestions">
            <div class="suggestion-title">💡 Conseils pour les associations :</div>
            <div class="suggestion-text">
                Pensez à vos ressources humaines (bénévoles, salariés), votre mission sociale, vos partenariats, votre financement, votre ancrage territorial, et l'évolution du contexte réglementaire.
            </div>
        </div>

        <div class="controls">
            <button class="btn btn-primary" onclick="exportSWOT()">📊 Exporter l'analyse</button>
            <button class="btn btn-secondary" onclick="clearAll()">🗑️ Tout effacer</button>
            <button class="btn btn-secondary" onclick="saveToLocal()">💾 Sauvegarder</button>
            <button class="btn btn-secondary" onclick="loadFromLocal()">📁 Charger</button>
        </div>

        <div class="swot-grid">
            <!-- FORCES -->
            <div class="swot-quadrant strengths">
                <div class="quadrant-title">
                    <span class="quadrant-icon">💪</span>
                    Forces (Strengths)
                </div>
                <div class="ngo-suggestions">
                    <div class="suggestion-title">Exemples pour associations :</div>
                    <div class="suggestion-text">Équipe engagée, expertise métier, réseau de partenaires, légitimité auprès des bénéficiaires, ancrage local</div>
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
                    <span class="quadrant-icon">⚠️</span>
                    Faiblesses (Weaknesses)
                </div>
                <div class="ngo-suggestions">
                    <div class="suggestion-title">Exemples pour associations :</div>
                    <div class="suggestion-text">Ressources financières limitées, dépendance aux subventions, manque de visibilité, outils numériques obsolètes</div>
                </div>
                <div class="input-section">
                    <div class="input-group">
                        <input type="text" class="item-input" id="weaknesses-input" placeholder="Ajoutez une faiblesse...">
                        <button class="add-btn" onclick="addItem('weaknesses')">Ajouter</button>
                    </div>
                </div>
                <ul class="items-list" id="weaknesses-list"></ul>
            </div>

            <!-- OPPORTUNITÉS -->
            <div class="swot-quadrant opportunities">
                <div class="quadrant-title">
                    <span class="quadrant-icon">🚀</span>
                    Opportunités (Opportunities)
                </div>
                <div class="ngo-suggestions">
                    <div class="suggestion-title">Exemples pour associations :</div>
                    <div class="suggestion-text">Nouveaux financements européens, digitalisation, partenariats public-privé, évolution des besoins sociaux</div>
                </div>
                <div class="input-section">
                    <div class="input-group">
                        <input type="text" class="item-input" id="opportunities-input" placeholder="Ajoutez une opportunité...">
                        <button class="add-btn" onclick="addItem('opportunities')">Ajouter</button>
                    </div>
                </div>
                <ul class="items-list" id="opportunities-list"></ul>
            </div>

            <!-- MENACES -->
            <div class="swot-quadrant threats">
                <div class="quadrant-title">
                    <span class="quadrant-icon">⚡</span>
                    Menaces (Threats)
                </div>
                <div class="ngo-suggestions">
                    <div class="suggestion-title">Exemples pour associations :</div>
                    <div class="suggestion-text">Réduction des subventions publiques, concurrence d'autres acteurs, complexification administrative, crise économique</div>
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
            <div class="export-title">📈 Prêt pour l'analyse stratégique ?</div>
            <p style="color: #7f8c8d; margin-bottom: 20px;">
                Une fois votre SWOT complétée, passez à l'analyse TOWS pour définir vos stratégies d'action.
            </p>
            <button class="btn btn-primary" onclick="goToTOWS()" style="margin-top: 10px;">
                🎯 Passer à l'analyse TOWS
            </button>
        </div>
    </div>

    <!-- Section TOWS -->
    <div class="container" id="towsSection">
        <div class="header">
            <h1>Analyse TOWS</h1>
            <p>Matrice stratégique : Transformez votre SWOT en plans d'action concrets</p>
            <button class="btn btn-secondary" onclick="backToSWOT()" style="margin-top: 15px;">
                ← Retour au SWOT
            </button>
        </div>

        <div class="ngo-suggestions">
            <div class="suggestion-title">💡 Méthode TOWS pour associations :</div>
            <div class="suggestion-text">
                Croisez vos éléments SWOT pour identifier 4 types de stratégies : Maxi-Maxi (Forces+Opportunités), Mini-Maxi (Faiblesses+Opportunités), Maxi-Mini (Forces+Menaces), Mini-Mini (Faiblesses+Menaces).
            </div>
        </div>

        <div class="controls">
            <button class="btn btn-primary" onclick="exportTOWS()">📊 Exporter TOWS</button>
            <button class="btn btn-secondary" onclick="clearTOWS()">🗑️ Effacer TOWS</button>
            <button class="btn btn-secondary" onclick="generateTOWSSuggestions()">💡 Suggestions automatiques</button>
        </div>

        <div class="swot-grid">
            <!-- SO: Forces + Opportunités -->
            <div class="swot-quadrant strengths">
                <div class="quadrant-title">
                    <span class="quadrant-icon">🚀</span>
                    Stratégies SO (Forces + Opportunités)
                </div>
                <div class="ngo-suggestions">
                    <div class="suggestion-title">Stratégie Maxi-Maxi :</div>
                    <div class="suggestion-text">Comment utiliser vos forces pour saisir les opportunités ? (Ex: Exploiter votre expertise pour capter de nouveaux financements)</div>
                </div>
                <div class="tows-source">
                    <strong>Basé sur vos Forces :</strong> <span id="so-strengths">Ajoutez d'abord des éléments dans le SWOT</span><br>
                    <strong>Basé sur vos Opportunités :</strong> <span id="so-opportunities">Ajoutez d'abord des éléments dans le SWOT</span>
                </div>
                <div class="input-section">
                    <div class="input-group">
                        <input type="text" class="item-input" id="so-input" placeholder="Stratégie Forces + Opportunités...">
                        <button class="add-btn" onclick="addTOWSItem('so')">Ajouter</button>
                    </div>
                </div>
                <ul class="items-list" id="so-list"></ul>
            </div>

            <!-- WO: Faiblesses + Opportunités -->
            <div class="swot-quadrant opportunities">
                <div class="quadrant-title">
                    <span class="quadrant-icon">🔧</span>
                    Stratégies WO (Faiblesses + Opportunités)
                </div>
                <div class="ngo-suggestions">
                    <div class="suggestion-title">Stratégie Mini-Maxi :</div>
                    <div class="suggestion-text">Comment surmonter vos faiblesses pour saisir les opportunités ? (Ex: Former l'équipe au numérique pour accéder aux financements digitaux)</div>
                </div>
                <div class="tows-source">
                    <strong>Basé sur vos Faiblesses :</strong> <span id="wo-weaknesses">Ajoutez d'abord des éléments dans le SWOT</span><br>
                    <strong>Basé sur vos Opportunités :</strong> <span id="wo-opportunities">Ajoutez d'abord des éléments dans le SWOT</span>
                </div>
                <div class="input-section">
                    <div class="input-group">
                        <input type="text" class="item-input" id="wo-input" placeholder="Stratégie Faiblesses + Opportunités...">
                        <button class="add-btn" onclick="addTOWSItem('wo')">Ajouter</button>
                    </div>
                </div>
                <ul class="items-list" id="wo-list"></ul>
            </div>

            <!-- ST: Forces + Menaces -->
            <div class="swot-quadrant threats">
                <div class="quadrant-title">
                    <span class="quadrant-icon">🛡️</span>
                    Stratégies ST (Forces + Menaces)
                </div>
                <div class="ngo-suggestions">
                    <div class="suggestion-title">Stratégie Maxi-Mini :</div>
                    <div class="suggestion-text">Comment utiliser vos forces pour contrer les menaces ? (Ex: Diversifier vos financements grâce à votre réseau)</div>
                </div>
                <div class="tows-source">
                    <strong>Basé sur vos Forces :</strong> <span id="st-strengths">Ajoutez d'abord des éléments dans le SWOT</span><br>
                    <strong>Basé sur vos Menaces :</strong> <span id="st-threats">Ajoutez d'abord des éléments dans le SWOT</span>
                </div>
                <div class="input-section">
                    <div class="input-group">
                        <input type="text" class="item-input" id="st-input" placeholder="Stratégie Forces + Menaces...">
                        <button class="add-btn" onclick="addTOWSItem('st')">Ajouter</button>
                    </div>
                </div>
                <ul class="items-list" id="st-list"></ul>
            </div>

            <!-- WT: Faiblesses + Menaces -->
            <div class="swot-quadrant weaknesses">
                <div class="quadrant-title">
                    <span class="quadrant-icon">⚠️</span>
                    Stratégies WT (Faiblesses + Menaces)
                </div>
                <div class="ngo-suggestions">
                    <div class="suggestion-title">Stratégie Mini-Mini :</div>
                    <div class="suggestion-text">Comment minimiser vos faiblesses face aux menaces ? (Ex: Mutualiser les coûts avec d'autres associations)</div>
                </div>
                <div class="tows-source">
                    <strong>Basé sur vos Faiblesses :</strong> <span id="wt-weaknesses">Ajoutez d'abord des éléments dans le SWOT</span><br>
                    <strong>Basé sur vos Menaces :</strong> <span id="wt-threats">Ajoutez d'abord des éléments dans le SWOT</span>
                </div>
                <div class="input-section">
                    <div class="input-group">
                        <input type="text" class="item-input" id="wt-input" placeholder="Stratégie Faiblesses + Menaces...">
                        <button class="add-btn" onclick="addTOWSItem('wt')">Ajouter</button>
                    </div>
                </div>
                <ul class="items-list" id="wt-list"></ul>
            </div>
        </div>

        <div class="export-section">
            <div class="export-title">🎯 Plan d'action stratégique</div>
            <p style="color: #7f8c8d; margin-bottom: 20px;">
                Priorisez maintenant vos stratégies TOWS et définissez un plan d'action avec échéances et responsables.
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
                <button class="btn btn-primary" onclick="printSWOT()">🖨️ Imprimer</button>
                <button class="btn btn-primary" onclick="exportToPDF()">📄 Export PDF</button>
                <button class="btn btn-primary" onclick="exportToWord()">📝 Export Word</button>
                <button class="btn btn-secondary" onclick="copySWOT()">📋 Copier le texte</button>
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
                <button class="btn btn-primary" onclick="printTOWS()">🖨️ Imprimer</button>
                <button class="btn btn-primary" onclick="exportTOWSToPDF()">📄 Export PDF</button>
                <button class="btn btn-primary" onclick="exportTOWSToWord()">📝 Export Word</button>
                <button class="btn btn-secondary" onclick="copyTOWS()">📋 Copier le texte</button>
            </div>
            <div id="exportTOWSContent" class="export-content"></div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script>
        // Stockage des données SWOT et TOWS
        let swotData = {
            strengths: [],
            weaknesses: [],
            opportunities: [],
            threats: []
        };

        let towsData = {
            so: [], // Strengths + Opportunities
            wo: [], // Weaknesses + Opportunities  
            st: [], // Strengths + Threats
            wt: []  // Weaknesses + Threats
        };

        // ========== FONCTIONS SWOT ==========
        
        // Fonction pour ajouter un élément SWOT
        function addItem(category) {
            const input = document.getElementById(category + '-input');
            const text = input.value.trim();
            
            if (text === '') {
                alert('Veuillez saisir du texte avant d\'ajouter un élément.');
                return;
            }
            
            swotData[category].push(text);
            input.value = '';
            updateList(category);
            saveToLocal(); // Auto-sauvegarde
        }

        // Fonction pour supprimer un élément SWOT
        function removeItem(category, index) {
            swotData[category].splice(index, 1);
            updateList(category);
            saveToLocal(); // Auto-sauvegarde
        }

        // Fonction pour mettre à jour une liste SWOT
        function updateList(category) {
            const list = document.getElementById(category + '-list');
            list.innerHTML = '';
            
            swotData[category].forEach((item, index) => {
                const li = document.createElement('li');
                li.className = 'item';
                li.innerHTML = `
                    <span class="item-text">${item}</span>
                    <button class="delete-btn" onclick="removeItem('${category}', ${index})">✕</button>
                `;
                list.appendChild(li);
            });
        }

        // Fonction pour effacer tout le SWOT
        function clearAll() {
            if (confirm('Êtes-vous sûr de vouloir effacer toute l\'analyse SWOT ?')) {
                swotData = {
                    strengths: [],
                    weaknesses: [],
                    opportunities: [],
                    threats: []
                };
                
                Object.keys(swotData).forEach(category => {
                    updateList(category);
                });
                saveToLocal();
            }
        }

        // Fonction pour sauvegarder localement
        function saveToLocal() {
            try {
                const allData = { swot: swotData, tows: towsData };
                localStorage.setItem('swotAnalysis', JSON.stringify(allData));
            } catch (e) {
                console.error('Erreur sauvegarde:', e);
            }
        }

        // Fonction pour charger depuis le stockage local
        function loadFromLocal() {
            try {
                const saved = localStorage.getItem('swotAnalysis');
                if (saved) {
                    const allData = JSON.parse(saved);
                    if (allData.swot) {
                        swotData = allData.swot;
                        Object.keys(swotData).forEach(category => {
                            updateList(category);
                        });
                    }
                    if (allData.tows) {
                        towsData = allData.tows;
                        Object.keys(towsData).forEach(category => {
                            updateTOWSList(category);
                        });
                        updateTOWSSource();
                    }
                    alert('Analyse SWOT et TOWS chargée avec succès !');
                } else {
                    alert('Aucune sauvegarde trouvée.');
                }
            } catch (e) {
                alert('Erreur lors du chargement : ' + e.message);
            }
        }

        // ========== FONCTIONS TOWS ==========

        // Fonction pour passer au TOWS
        function goToTOWS() {
            const totalItems = Object.values(swotData).reduce((sum, arr) => sum + arr.length, 0);
            if (totalItems < 4) {
                alert('Veuillez d\'abord compléter votre analyse SWOT avec au moins un élément dans chaque quadrant.');
                return;
            }
            
            document.getElementById('swotSection').style.display = 'none';
            document.getElementById('towsSection').style.display = 'block';
            updateTOWSSource();
        }

        // Fonction pour revenir au SWOT
        function backToSWOT() {
            document.getElementById('swotSection').style.display = 'block';
            document.getElementById('towsSection').style.display = 'none';
        }

        // Fonction pour ajouter un élément TOWS
        function addTOWSItem(category) {
            const input = document.getElementById(category + '-input');
            const text = input.value.trim();
            
            if (text === '') {
                alert('Veuillez saisir du texte avant d\'ajouter une stratégie.');
                return;
            }
            
            towsData[category].push(text);
            input.value = '';
            updateTOWSList(category);
            saveToLocal();
        }

        // Fonction pour supprimer un élément TOWS
        function removeTOWSItem(category, index) {
            towsData[category].splice(index, 1);
            updateTOWSList(category);
            saveToLocal();
        }

        // Fonction pour mettre à jour une liste TOWS
        function updateTOWSList(category) {
            const list = document.getElementById(category + '-list');
            list.innerHTML = '';
            
            towsData[category].forEach((item, index) => {
                const li = document.createElement('li');
                li.className = 'item';
                li.innerHTML = `
                    <span class="item-text">${item}</span>
                    <button class="delete-btn" onclick="removeTOWSItem('${category}', ${index})">✕</button>
                `;
                list.appendChild(li);
            });
        }

        // Fonction pour mettre à jour les sources TOWS
        function updateTOWSSource() {
            // SO (Strengths + Opportunities)
            document.getElementById('so-strengths').textContent = 
                swotData.strengths.length > 0 ? swotData.strengths.slice(0, 2).join(', ') + '...' : 'Aucune force définie';
            document.getElementById('so-opportunities').textContent = 
                swotData.opportunities.length > 0 ? swotData.opportunities.slice(0, 2).join(', ') + '...' : 'Aucune opportunité définie';

            // WO (Weaknesses + Opportunities)
            document.getElementById('wo-weaknesses').textContent = 
                swotData.weaknesses.length > 0 ? swotData.weaknesses.slice(0, 2).join(', ') + '...' : 'Aucune faiblesse définie';
            document.getElementById('wo-opportunities').textContent = 
                swotData.opportunities.length > 0 ? swotData.opportunities.slice(0, 2).join(', ') + '...' : 'Aucune opportunité définie';

            // ST (Strengths + Threats)
            document.getElementById('st-strengths').textContent = 
                swotData.strengths.length > 0 ? swotData.strengths.slice(0, 2).join(', ') + '...' : 'Aucune force définie';
            document.getElementById('st-threats').textContent = 
                swotData.threats.length > 0 ? swotData.threats.slice(0, 2).join(', ') + '...' : 'Aucune menace définie';

            // WT (Weaknesses + Threats)
            document.getElementById('wt-weaknesses').textContent = 
                swotData.weaknesses.length > 0 ? swotData.weaknesses.slice(0, 2).join(', ') + '...' : 'Aucune faiblesse définie';
            document.getElementById('wt-threats').textContent = 
                swotData.threats.length > 0 ? swotData.threats.slice(0, 2).join(', ') + '...' : 'Aucune menace définie';
        }

        // Fonction pour effacer le TOWS
        function clearTOWS() {
            if (confirm('Êtes-vous sûr de vouloir effacer toute l\'analyse TOWS ?')) {
                towsData = { so: [], wo: [], st: [], wt: [] };
                Object.keys(towsData).forEach(category => {
                    updateTOWSList(category);
                });
                saveToLocal();
            }
        }

        // Fonction pour générer des suggestions TOWS
        function generateTOWSSuggestions() {
            const suggestions = {
                so: [
                    "Développer de nouveaux programmes en exploitant notre expertise reconnue",
                    "Élargir notre territoire d'action grâce à nos partenariats solides",
                    "Créer une offre de formation basée sur notre savoir-faire"
                ],
                wo: [
                    "Former l'équipe aux outils numériques pour accéder aux financements digitaux",
                    "Développer une stratégie de communication pour saisir les opportunités médiatiques",
                    "Créer des partenariats pour pallier nos manques de ressources"
                ],
                st: [
                    "Utiliser notre réseau pour diversifier nos sources de financement",
                    "Capitaliser sur notre réputation pour maintenir notre position face à la concurrence",
                    "Exploiter notre ancrage local pour résister aux changements réglementaires"
                ],
                wt: [
                    "Mutualiser les coûts avec d'autres associations pour réduire notre vulnérabilité",
                    "Développer des partenariats de secours en cas de réduction des subventions",
                    "Créer une réserve financière pour faire face aux crises"
                ]
            };

            let added = false;
            Object.keys(suggestions).forEach(category => {
                if (towsData[category].length < 2) {
                    const suggestion = suggestions[category][Math.floor(Math.random() * suggestions[category].length)];
                    towsData[category].push(`💡 ${suggestion}`);
                    updateTOWSList(category);
                    added = true;
                }
            });

            if (added) {
                alert('Suggestions ajoutées ! Personnalisez-les selon votre contexte.');
                saveToLocal();
            } else {
                alert('Vous avez déjà suffisamment de stratégies dans chaque quadrant.');
            }
        }

        // ========== FONCTIONS D'EXPORT SWOT ==========

        function exportSWOT() {
            const modal = document.getElementById('exportModal');
            const content = document.getElementById('exportContent');
            
            const categories = {
                strengths: { title: 'Forces (Strengths)', icon: '💪', color: '#27ae60' },
                weaknesses: { title: 'Faiblesses (Weaknesses)', icon: '⚠️', color: '#e74c3c' },
                opportunities: { title: 'Opportunités (Opportunities)', icon: '🚀', color: '#3498db' },
                threats: { title: 'Menaces (Threats)', icon: '⚡', color: '#f39c12' }
            };
            
            let html = `
                <div style="text-align: center; margin-bottom: 30px;">
                    <h1 style="color: #2c3e50; margin-bottom: 10px;">Analyse SWOT</h1>
                    <p style="color: #7f8c8d;">Secteur Non Marchand - ${new Date().toLocaleDateString('fr-FR')}</p>
                </div>
            `;
            
            Object.keys(categories).forEach(category => {
                const cat = categories[category];
                html += `
                    <div class="export-quadrant ${category}" style="--accent-color: ${cat.color};">
                        <h3>${cat.icon} ${cat.title}</h3>
                `;
                
                if (swotData[category].length > 0) {
                    html += '<ul class="export-list">';
                    swotData[category].forEach(item => {
                        html += `<li>${item}</li>`;
                    });
                    html += '</ul>';
                } else {
                    html += '<p style="color: #95a5a6; font-style: italic;">Aucun élément ajouté</p>';
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
                opportunities: 'OPPORTUNITÉS (OPPORTUNITIES)',
                threats: 'MENACES (THREATS)'
            };
            
            let text = `ANALYSE SWOT - SECTEUR NON MARCHAND\n`;
            text += `Date: ${new Date().toLocaleDateString('fr-FR')}\n\n`;
            
            Object.keys(categories).forEach(category => {
                text += `${categories[category]}\n`;
                text += '='.repeat(categories[category].length) + '\n';
                
                if (swotData[category].length > 0) {
                    swotData[category].forEach(item => {
                        text += `• ${item}\n`;
                    });
                } else {
                    text += 'Aucun élément ajouté\n';
                }
                text += '\n';
            });
            
            navigator.clipboard.writeText(text).then(() => {
                alert('Analyse SWOT copiée dans le presse-papier !');
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
                
                // Titre principal
                pdf.setFontSize(20);
                pdf.setTextColor(44, 62, 80);
                pdf.text('ANALYSE SWOT', pageWidth / 2, currentY, { align: 'center' });
                
                currentY += 10;
                pdf.setFontSize(12);
                pdf.setTextColor(127, 140, 141);
                pdf.text('Secteur Non Marchand', pageWidth / 2, currentY, { align: 'center' });
                pdf.text(`Date: ${new Date().toLocaleDateString('fr-FR')}`, pageWidth / 2, currentY + 5, { align: 'center' });
                
                currentY += 25;
                
                const categories = {
                    strengths: { title: 'FORCES (STRENGTHS)', color: [39, 174, 96] },
                    weaknesses: { title: 'FAIBLESSES (WEAKNESSES)', color: [231, 76, 60] },
                    opportunities: { title: 'OPPORTUNITÉS (OPPORTUNITIES)', color: [52, 152, 219] },
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
                            const lines = pdf.splitTextToSize(`• ${item}`, pageWidth - 2 * margin);
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
                        pdf.text('Aucun élément ajouté', margin, currentY);
                        currentY += lineHeight;
                    }
                    
                    currentY += 10;
                });
                
                const fileName = `SWOT_Analysis_${new Date().toISOString().split('T')[0]}.pdf`;
                pdf.save(fileName);
                alert('PDF généré avec succès !');
                
            } catch (error) {
                alert('Erreur lors de la génération du PDF.');
            }
        }

        function exportToWord() {
            try {
                const categories = {
                    strengths: 'FORCES (STRENGTHS)',
                    weaknesses: 'FAIBLESSES (WEAKNESSES)',
                    opportunities: 'OPPORTUNITÉS (OPPORTUNITIES)',
                    threats: 'MENACES (THREATS)'
                };
                
                let htmlContent = `
                    <html>
                    <head>
                        <meta charset='utf-8'>
                        <title>Analyse SWOT</title>
                        <style>
                            body { font-family: Arial, sans-serif; margin: 40px; line-height: 1.6; }
                            .header { text-align: center; margin-bottom: 40px; }
                            .title { font-size: 24px; font-weight: bold; color: #2c3e50; margin-bottom: 10px; }
                            .subtitle { font-size: 14px; color: #7f8c8d; }
                            .section { margin-bottom: 30px; }
                            .section-title { font-size: 16px; font-weight: bold; margin-bottom: 15px; padding-bottom: 5px; border-bottom: 2px solid; }
                            .strengths .section-title { color: #27ae60; border-color: #27ae60; }
                            .weaknesses .section-title { color: #e74c3c; border-color: #e74c3c; }
                            .opportunities .section-title { color: #3498db; border-color: #3498db; }
                            .threats .section-title { color: #f39c12; border-color: #f39c12; }
                            .item-list { margin: 0; padding-left: 20px; }
                            .item-list li { margin-bottom: 8px; }
                            .no-items { color: #95a5a6; font-style: italic; }
                        </style>
                    </head>
                    <body>
                        <div class="header">
                            <div class="title">ANALYSE SWOT</div>
                            <div class="subtitle">Secteur Non Marchand - ${new Date().toLocaleDateString('fr-FR')}</div>
                        </div>
                `;
                
                Object.keys(categories).forEach(category => {
                    htmlContent += `<div class="section ${category}">`;
                    htmlContent += `<div class="section-title">${categories[category]}</div>`;
                    
                    if (swotData[category].length > 0) {
                        htmlContent += '<ul class="item-list">';
                        swotData[category].forEach(item => {
                            htmlContent += `<li>${item}</li>`;
                        });
                        htmlContent += '</ul>';
                    } else {
                        htmlContent += '<div class="no-items">Aucun élément ajouté</div>';
                    }
                    
                    htmlContent += '</div>';
                });
                
                htmlContent += '</body></html>';
                
                const blob = new Blob([htmlContent], {
                    type: 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
                });
                
                const url = window.URL.createObjectURL(blob);
                const link = document.createElement('a');
                link.href = url;
                link.download = `SWOT_Analysis_${new Date().toISOString().split('T')[0]}.doc`;
                
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                window.URL.revokeObjectURL(url);
                
                alert('Document Word généré avec succès !');
                
            } catch (error) {
                alert('Erreur lors de la génération du document Word.');
            }
        }

        // ========== FONCTIONS D'EXPORT TOWS ==========

        function exportTOWS() {
            const modal = document.getElementById('exportTOWSModal');
            const content = document.getElementById('exportTOWSContent');
            
            const categories = {
                so: { title: 'Stratégies SO (Forces + Opportunités)', icon: '🚀', color: '#27ae60' },
                wo: { title: 'Stratégies WO (Faiblesses + Opportunités)', icon: '🔧', color: '#3498db' },
                st: { title: 'Stratégies ST (Forces + Menaces)', icon: '🛡️', color: '#f39c12' },
                wt: { title: 'Stratégies WT (Faiblesses + Menaces)', icon: '⚠️', color: '#e74c3c' }
            };
            
            let html = `
                <div style="text-align: center; margin-bottom: 30px;">
                    <h1 style="color: #2c3e50; margin-bottom: 10px;">Analyse TOWS</h1>
                    <p style="color: #7f8c8d;">Matrice Stratégique - ${new Date().toLocaleDateString('fr-FR')}</p>
                </div>
            `;
            
            Object.keys(categories).forEach(category => {
                const cat = categories[category];
                html += `
                    <div class="export-quadrant ${category}" style="--accent-color: ${cat.color};">
                        <h3>${cat.icon} ${cat.title}</h3>
                `;
                
                if (towsData[category].length > 0) {
                    html += '<ul class="export-list">';
                    towsData[category].forEach(item => {
                        html += `<li>${item}</li>`;
                    });
                    html += '</ul>';
                } else {
                    html += '<p style="color: #95a5a6; font-style: italic;">Aucune stratégie définie</p>';
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
                so: 'STRATÉGIES SO (FORCES + OPPORTUNITÉS)',
                wo: 'STRATÉGIES WO (FAIBLESSES + OPPORTUNITÉS)', 
                st: 'STRATÉGIES ST (FORCES + MENACES)',
                wt: 'STRATÉGIES WT (FAIBLESSES + MENACES)'
            };
            
            let text = `ANALYSE TOWS - MATRICE STRATÉGIQUE\n`;
            text += `Date: ${new Date().toLocaleDateString('fr-FR')}\n\n`;
            
            Object.keys(categories).forEach(category => {
                text += `${categories[category]}\n`;
                text += '='.repeat(categories[category].length) + '\n';
                
                if (towsData[category].length > 0) {
                    towsData[category].forEach(item => {
                        text += `• ${item}\n`;
                    });
                } else {
                    text += 'Aucune stratégie définie\n';
                }
                text += '\n';
            });
            
            navigator.clipboard.writeText(text).then(() => {
                alert('Analyse TOWS copiée dans le presse-papier !');
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
                pdf.text('Matrice Stratégique', pageWidth / 2, currentY, { align: 'center' });
                pdf.text(`Date: ${new Date().toLocaleDateString('fr-FR')}`, pageWidth / 2, currentY + 5, { align: 'center' });
                
                currentY += 25;
                
                const categories = {
                    so: { title: 'STRATÉGIES SO (FORCES + OPPORTUNITÉS)', color: [39, 174, 96] },
                    wo: { title: 'STRATÉGIES WO (FAIBLESSES + OPPORTUNITÉS)', color: [52, 152, 219] },
                    st: { title: 'STRATÉGIES ST (FORCES + MENACES)', color: [243, 156, 18] },
                    wt: { title: 'STRATÉGIES WT (FAIBLESSES + MENACES)', color: [231, 76, 60] }
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
                            const lines = pdf.splitTextToSize(`• ${item}`, pageWidth - 2 * margin);
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
                        pdf.text('Aucune stratégie définie', margin, currentY);
                        currentY += lineHeight;
                    }
                    
                    currentY += 10;
                });
                
                const fileName = `TOWS_Analysis_${new Date().toISOString().split('T')[0]}.pdf`;
                pdf.save(fileName);
                alert('PDF TOWS généré avec succès !');
                
            } catch (error) {
                alert('Erreur lors de la génération du PDF TOWS.');
            }
        }

        function exportTOWSToWord() {
            try {
                const categories = {
                    so: 'STRATÉGIES SO (FORCES + OPPORTUNITÉS)',
                    wo: 'STRATÉGIES WO (FAIBLESSES + OPPORTUNITÉS)',
                    st: 'STRATÉGIES ST (FORCES + MENACES)',
                    wt: 'STRATÉGIES WT (FAIBLESSES + MENACES)'
                };
                
                let htmlContent = `
                    <html>
                    <head>
                        <meta charset='utf-8'>
                        <title>Analyse TOWS</title>
                        <style>
                            body { font-family: Arial, sans-serif; margin: 40px; line-height: 1.6; }
                            .header { text-align: center; margin-bottom: 40px; }
                            .title { font-size: 24px; font-weight: bold; color: #2c3e50; margin-bottom: 10px; }
                            .subtitle { font-size: 14px; color: #7f8c8d; }
                            .section { margin-bottom: 30px; }
                            .section-title { font-size: 16px; font-weight: bold; margin-bottom: 15px; padding-bottom: 5px; border-bottom: 2px solid; }
                            .so .section-title { color: #27ae60; border-color: #27ae60; }
                            .wo .section-title { color: #3498db; border-color: #3498db; }
                            .st .section-title { color: #f39c12; border-color: #f39c12; }
                            .wt .section-title { color: #e74c3c; border-color: #e74c3c; }
                            .item-list { margin: 0; padding-left: 20px; }
                            .item-list li { margin-bottom: 8px; }
                            .no-items { color: #95a5a6; font-style: italic; }
                        </style>
                    </head>
                    <body>
                        <div class="header">
                            <div class="title">ANALYSE TOWS</div>
                            <div class="subtitle">Matrice Stratégique - ${new Date().toLocaleDateString('fr-FR')}</div>
                        </div>
                `;
                
                Object.keys(categories).forEach(category => {
                    htmlContent += `<div class="section ${category}">`;
                    htmlContent += `<div class="section-title">${categories[category]}</div>`;
                    
                    if (towsData[category].length > 0) {
                        htmlContent += '<ul class="item-list">';
                        towsData[category].forEach(item => {
                            htmlContent += `<li>${item}</li>`;
                        });
                        htmlContent += '</ul>';
                    } else {
                        htmlContent += '<div class="no-items">Aucune stratégie définie</div>';
                    }
                    
                    htmlContent += '</div>';
                });
                
                htmlContent += '</body></html>';
                
                const blob = new Blob([htmlContent], {
                    type: 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
                });
                
                const url = window.URL.createObjectURL(blob);
                const link = document.createElement('a');
                link.href = url;
                link.download = `TOWS_Analysis_${new Date().toISOString().split('T')[0]}.doc`;
                
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                window.URL.revokeObjectURL(url);
                
                alert('Document Word TOWS généré avec succès !');
                
            } catch (error) {
                alert('Erreur lors de la génération du document Word TOWS.');
            }
        }

        // ========== EVENT LISTENERS ==========

        // Event listeners pour les touches Entrée
        document.addEventListener('DOMContentLoaded', function() {
            // SWOT
            ['strengths', 'weaknesses', 'opportunities', 'threats'].forEach(category => {
                const input = document.getElementById(category + '-input');
                input.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        addItem(category);
                    }
                });
            });

            // TOWS
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

            // Chargement automatique
            const saved = localStorage.getItem('swotAnalysis');
            if (saved) {
                try {
                    const allData = JSON.parse(saved);
                    if (allData.swot) {
                        swotData = allData.swot;
                        Object.keys(swotData).forEach(category => {
                            updateList(category);
                        });
                    }
                    if (allData.tows) {
                        towsData = allData.tows;
                        Object.keys(towsData).forEach(category => {
                            updateTOWSList(category);
                        });
                    }
                    setTimeout(updateTOWSSource, 100);
                } catch (e) {
                    console.log('Erreur lors du chargement automatique');
                }
            }
        });

        // Fermer les modals en cliquant à l'extérieur
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