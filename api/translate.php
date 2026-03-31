<?php
require_once '../config/db.php';

// Allow CORS (since we're calling from index.php)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// Handle OPTIONS (Preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

// Ensure Cache Table exists: Standard Hostinger Migration
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS translations_cache (
        id INT AUTO_INCREMENT PRIMARY KEY,
        source_text TEXT NOT NULL,
        target_lang VARCHAR(10) NOT NULL,
        translated_text TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY `unique_trans` (source_text(255), target_lang)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
} catch (Exception $e) {
    // Silently handle if table already exists or permissions issue
}

// Get Params
$text = $_GET['q'] ?? '';
$targetLang = $_GET['lang'] ?? 'en';

if (empty($text) || $targetLang === 'en') {
    echo json_encode(['translatedText' => $text]);
    exit;
}

// 1. Check Server-Side Cache: MySQL
try {
    $stmt = $pdo->prepare("SELECT translated_text FROM translations_cache WHERE source_text = ? AND target_lang = ?");
    $stmt->execute([$text, $targetLang]);
    $cached = $stmt->fetch();

    if ($cached) {
        echo json_encode(['translatedText' => $cached['translated_text'], 'source' => 'cache']);
        exit;
    }
} catch (Exception $e) {
    // 
}

// 2. Optimized Multi-Engine Translator (Rotating Mirrors for extended limits)
$translated = null;

// Primary Mirrors for LibreTranslate (increases uptime)
$mirrors = [
    "https://libretranslate.de/translate",
    "https://translate.argosopentech.com/translate",
    "https://text-translation.p.rapidapi.com/translate" // Placeholder if needed
];

foreach ($mirrors as $mirror) {
    if ($translated) break;
    
    try {
        $ch = curl_init($mirror);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
            'q' => $text,
            'source' => 'en',
            'target' => $targetLang,
            'format' => 'text'
        ]));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_TIMEOUT, 6);
        $response = curl_exec($ch);
        $data = json_decode($response, true);
        
        if (isset($data['translatedText']) && !empty($data['translatedText'])) {
            $translated = $data['translatedText'];
        }
    } catch (Exception $e) {
        continue;
    }
}

// 3. Ultimate Engine: Google Translate (Unofficial High-Limit)
if (!$translated) {
    try {
        $url = "https://translate.googleapis.com/translate_a/single?client=gtx&sl=en&tl=" . $targetLang . "&dt=t&q=" . urlencode($text);
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 8);
        $response = curl_exec($ch);
        $data = json_decode($response, true);
        
        if (isset($data[0][0][0])) {
            $candidate = $data[0][0][0];
            
            // STRICT VALIDATION: Ensure we didn't receive a system error
            if (!empty($candidate) && stripos($candidate, 'MYMEMORY') === false && stripos($candidate, 'error') === false) {
                $translated = trim($candidate);
            }
        }
    } catch (Exception $e) {
        // 
    }
}

// 4. Save to Cache ONLY if we got a valid translation (Strict Audit)
// This ensures that even if we hit a limit, we don't save the error message
if ($translated && stripos($translated, 'MYMEMORY WARNING') === false && stripos($translated, 'limit reached') === false) {
    try {
        $stmt = $pdo->prepare("INSERT IGNORE INTO translations_cache (source_text, target_lang, translated_text) VALUES (?, ?, ?)");
        $stmt->execute([$text, $targetLang, $translated]);
    } catch (Exception $e) {
        // 
    }
}

echo json_encode([
    'translatedText' => $translated ?: $text, // Ultimate Fallback to original English
    'source' => $translated ? 'api' : 'original'
]);
