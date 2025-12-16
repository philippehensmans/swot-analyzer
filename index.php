<?php
/**
 * Page d'accueil - Sélection de la session et identification du participant
 */
session_start();
require_once __DIR__ . '/config/database.php';

$db = getDB();
$error = '';
$sessions = [];

// Récupérer les sessions actives
try {
    $stmt = $db->query("SELECT id, name, code, description FROM sessions WHERE active = 1 ORDER BY created_at DESC");
    $sessions = $stmt->fetchAll();
} catch (Exception $e) {
    $error = "Erreur lors du chargement des sessions.";
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sessionCode = trim($_POST['session_code'] ?? '');
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $organisation = trim($_POST['organisation'] ?? '');

    if (empty($sessionCode) || empty($nom) || empty($prenom)) {
        $error = "Veuillez remplir tous les champs obligatoires.";
    } else {
        try {
            // Vérifier que la session existe
            $stmt = $db->prepare("SELECT id, name FROM sessions WHERE code = ? AND active = 1");
            $stmt->execute([$sessionCode]);
            $session = $stmt->fetch();

            if (!$session) {
                $error = "Code de session invalide.";
            } else {
                // Créer ou récupérer le participant
                $stmt = $db->prepare("
                    INSERT OR IGNORE INTO participants (session_id, nom, prenom, organisation)
                    VALUES (?, ?, ?, ?)
                ");
                $stmt->execute([$session['id'], $nom, $prenom, $organisation]);

                // Récupérer l'ID du participant
                $stmt = $db->prepare("
                    SELECT id FROM participants
                    WHERE session_id = ? AND nom = ? AND prenom = ?
                ");
                $stmt->execute([$session['id'], $nom, $prenom]);
                $participant = $stmt->fetch();

                // Stocker les informations en session
                $_SESSION['participant_id'] = $participant['id'];
                $_SESSION['participant_nom'] = $nom;
                $_SESSION['participant_prenom'] = $prenom;
                $_SESSION['session_id'] = $session['id'];
                $_SESSION['session_name'] = $session['name'];

                // Rediriger vers l'application SWOT
                header('Location: swot_app.php');
                exit;
            }
        } catch (Exception $e) {
            $error = "Erreur lors de l'identification: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analyse SWOT/TOWS - Identification</title>
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
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
            padding: 40px;
            width: 100%;
            max-width: 500px;
            backdrop-filter: blur(10px);
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header h1 {
            color: #2c3e50;
            font-size: 2em;
            margin-bottom: 10px;
            background: linear-gradient(45deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .header p {
            color: #7f8c8d;
            font-size: 1em;
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

        .form-group label .required {
            color: #e74c3c;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #ecf0f1;
            border-radius: 10px;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .btn {
            width: 100%;
            padding: 15px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .btn-primary {
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
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

        .divider {
            display: flex;
            align-items: center;
            margin: 25px 0;
            color: #95a5a6;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #ecf0f1;
        }

        .divider span {
            padding: 0 15px;
            font-size: 14px;
        }

        .formateur-link {
            text-align: center;
            margin-top: 20px;
        }

        .formateur-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .formateur-link a:hover {
            color: #764ba2;
        }

        .session-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .session-info h3 {
            color: #2c3e50;
            font-size: 14px;
            margin-bottom: 5px;
        }

        .session-info p {
            color: #7f8c8d;
            font-size: 13px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Analyse SWOT/TOWS</h1>
            <p>Identifiez-vous pour commencer votre analyse</p>
        </div>

        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="session_code">Code de session <span class="required">*</span></label>
                <input type="text" id="session_code" name="session_code"
                       placeholder="Entrez le code fourni par le formateur"
                       value="<?= htmlspecialchars($_POST['session_code'] ?? '') ?>" required>
            </div>

            <div class="divider"><span>Vos informations</span></div>

            <div class="form-group">
                <label for="prenom">Prenom <span class="required">*</span></label>
                <input type="text" id="prenom" name="prenom"
                       placeholder="Votre prenom"
                       value="<?= htmlspecialchars($_POST['prenom'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label for="nom">Nom <span class="required">*</span></label>
                <input type="text" id="nom" name="nom"
                       placeholder="Votre nom"
                       value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label for="organisation">Organisation</label>
                <input type="text" id="organisation" name="organisation"
                       placeholder="Votre organisation (optionnel)"
                       value="<?= htmlspecialchars($_POST['organisation'] ?? '') ?>">
            </div>

            <button type="submit" class="btn btn-primary">Commencer l'analyse</button>
        </form>

        <div class="formateur-link">
            <a href="formateur.php">Acces formateur</a>
        </div>
    </div>
</body>
</html>
