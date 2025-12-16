<?php
/**
 * Configuration et initialisation de la base de données SQLite
 */

class Database {
    private static $instance = null;
    private $pdo;
    private $dbPath;

    private function __construct() {
        $this->dbPath = __DIR__ . '/../data/swot_analyzer.db';

        // Créer le dossier data s'il n'existe pas
        $dataDir = dirname($this->dbPath);
        if (!is_dir($dataDir)) {
            mkdir($dataDir, 0755, true);
        }

        try {
            $this->pdo = new PDO('sqlite:' . $this->dbPath);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $this->initTables();
        } catch (PDOException $e) {
            die("Erreur de connexion à la base de données: " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->pdo;
    }

    private function initTables() {
        // Table des sessions de formation
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS sessions (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name VARCHAR(255) NOT NULL,
                code VARCHAR(50) UNIQUE NOT NULL,
                description TEXT,
                formateur_password VARCHAR(255),
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                active INTEGER DEFAULT 1
            )
        ");

        // Table des participants
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS participants (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                session_id INTEGER NOT NULL,
                nom VARCHAR(100) NOT NULL,
                prenom VARCHAR(100) NOT NULL,
                organisation VARCHAR(255),
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (session_id) REFERENCES sessions(id),
                UNIQUE(session_id, nom, prenom)
            )
        ");

        // Table des analyses SWOT/TOWS
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS analyses (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                participant_id INTEGER NOT NULL UNIQUE,
                swot_data TEXT,
                tows_data TEXT,
                submitted INTEGER DEFAULT 0,
                submitted_at DATETIME,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (participant_id) REFERENCES participants(id)
            )
        ");

        // Créer un index pour améliorer les performances
        $this->pdo->exec("CREATE INDEX IF NOT EXISTS idx_participants_session ON participants(session_id)");
        $this->pdo->exec("CREATE INDEX IF NOT EXISTS idx_analyses_participant ON analyses(participant_id)");
    }
}

// Fonction helper pour obtenir la connexion
function getDB() {
    return Database::getInstance()->getConnection();
}
