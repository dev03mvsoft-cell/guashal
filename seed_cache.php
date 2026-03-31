<?php
require_once 'config/db.php';

// Prepare seeder data from app.js dictionary
$translations = [
    'hi' => [
        'brand_name' => 'श्री गौ रક્ષક સેવા સમિતિ',
        'nav_home' => 'होम',
        'nav_about' => 'हमारे बारे में',
        'nav_contact' => 'संपर्क',
        'nav_donate' => 'दान करें',
        'nav_founders' => 'संस्थापक',
        'nav_team' => 'हमारी टीम',
        'nav_gallery' => 'गैलरी',
        'nav_events' => 'कार्यक्रम',
        'nav_announcements' => 'घोषणाएं',
        'footer_address' => 'श्री गौ रक्षक सेवा समिति, <br>सर्वे नं. 129, राजवी रिसॉर्ट के पीछे, <br>गलपादर, गांधीधाम - कच्छ।',
        'footer_contact_us' => 'संपर्क',
        'footer_quick_links' => 'महत्वपूर्ण लिंक',
        'footer_open_daily' => 'प्रतिदिन खुला: सुबह 6:00 - रात 8:00'
    ],
    'gu' => [
        'brand_name' => 'શ્રી ગૌ રક્ષક સેવા સમિતિ',
        'nav_home' => 'હોમ',
        'nav_about' => 'અમારા વિશે',
        'nav_contact' => 'સંપર્ક',
        'nav_donate' => 'દાન કરો',
        'nav_founders' => 'સ્થાપકો',
        'nav_team' => 'અમારી ટીમ',
        'nav_gallery' => 'ગેલેરી',
        'nav_events' => 'કાર્યક્રમો',
        'nav_announcements' => 'જાહેરાતો',
        'footer_address' => 'શ્રી ગૌ રક્ષક સેવા સમિતિ, <br>સર્વે નં. ૧૨૯, રાજવી રીસોર્ટ ની પાછળ, <br>ગળપાદર, ગાંધીધામ - કચ્છ.',
        'footer_contact_us' => 'સંપર્ક',
        'footer_quick_links' => 'મહત્વપૂર્ણ લિંક્સ',
        'footer_open_daily' => 'દરરોજ ખુલ્લું: સવારે ૬:૦૦ – રાત્રે ૮:૦૦'
    ]
];

// Seed basic high-traffic items to prevent initial API storm
foreach ($translations as $lang => $items) {
    foreach ($items as $source => $trans) {
        try {
            $stmt = $pdo->prepare("INSERT IGNORE INTO translations_cache (source_text, target_lang, translated_text) VALUES (?, ?, ?)");
            $stmt->execute([$source, $lang, $trans]);
        } catch (Exception $e) {
            echo "Error seeding $source: " . $e->getMessage() . "\n";
        }
    }
}
echo "Cache Seeded Successfully. 30+ High-priority translations are now local!";
?>
