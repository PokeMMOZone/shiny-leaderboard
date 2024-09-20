<?php
// URL of the page to scrape
$url = "https://forums.pokemmo.com/index.php?/topic/177621-ndgo-shiny-museum/";

// Initialize cURL session
$ch = curl_init();

// Set the URL and other necessary options
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Follow redirects if any

// Execute the request
$response = curl_exec($ch);

// Close cURL session
curl_close($ch);

// Check if the response was successful
if ($response === false) {
    die("Failed to fetch the webpage.");
}

// Convert HTML entities to their corresponding characters
$response = html_entity_decode($response);

// Replace non-breaking spaces with regular spaces
$response = str_replace("\xC2\xA0", " ", $response);

// Load the HTML response into a DOMDocument
$dom = new DOMDocument();
libxml_use_internal_errors(true); // Suppress HTML parsing errors
$dom->loadHTML($response);
libxml_clear_errors();

// Get the entire HTML content as a string
$htmlContent = $dom->saveHTML();

// Regular expression pattern to match usernames and counts
$usernamePattern = '/>([A-Za-z0-9]+):\s*\((\d+)\)</';

// Initialize arrays
$users = [];

// Find all matches in the response
if (preg_match_all($usernamePattern, $htmlContent, $matches, PREG_SET_ORDER)) {
    foreach ($matches as $match) {
        $username = trim($match[1]);
        $imageCount = intval($match[2]);

        // Avoid duplication by ensuring unique usernames
        if (!isset($users[$username])) {
            $users[$username] = [
                'imageCount' => $imageCount
            ];
        }
    }
}

// Calculate total shinies
$totalShinies = array_sum(array_column($users, 'imageCount'));

// Prepare data for JSON file
$jsonData = [
    "name" => "IndigoPlateau",
    "code" => "NDGO",
    "url" => $url,
    "totalshinies" => $totalShinies,
    "members" => []
];

foreach ($users as $username => $data) {
    $jsonData["members"][] = [
        "username" => $username,
        "count" => $data['imageCount']
    ];
}

// Create directory if it doesn't exist
$dir = __DIR__ . '/../teams';
if (!is_dir($dir)) {
    if (!mkdir($dir, 0777, true)) {
        die("Failed to create directories...");
    }
}

// Write data to JSON file
$file = "$dir/ndgo.json";
file_put_contents($file, json_encode($jsonData, JSON_PRETTY_PRINT));

// Display the results
echo "<h1>Team IndigoPlateau OT Shiny Museum</h1>";
echo "<ul>";
foreach ($users as $username => $data) {
    echo "<li><strong>$username</strong>: {$data['imageCount']} shinies</li>";
}
echo "</ul>";
?>