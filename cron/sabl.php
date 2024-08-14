<?php
// URL of the page to scrape
$url = "https://forums.pokemmo.com/index.php?/topic/180207-welcome%C2%A0to%C2%A0the%C2%A0sable%C2%A0shiny%C2%A0showcase/";

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
$usernamePattern = '/(?:<[^>]*>)*([A-Za-z0-9]+)(?:<\/[^>]*>)*\s*\((\d+)\)/';

// Initialize arrays
$users = [];

// Find all matches in the response
if (preg_match_all($usernamePattern, $htmlContent, $matches, PREG_SET_ORDER)) {
    foreach ($matches as $match) {
        $username = trim($match[1]);
        $imageCount = intval($match[2]);

        // Clean up any non-printable characters or whitespace around the username
        $username = preg_replace('/\s+/', '', $username);
        $username = preg_replace('/[^\x20-\x7E]/', '', $username); // Remove non-ASCII characters

        // Filter out invalid usernames and specific unwanted usernames
        if ($username !== 'u00a0' && preg_match('/^[A-Za-z0-9]+$/', $username) && $imageCount > 0) {
            // Check if the username starts with 't' and if there's already a username without 't'
            if ($username[0] === 't' && isset($users[substr($username, 1)])) {
                continue; // Skip this entry if a similar username without 't' exists
            }

            // Avoid duplication by ensuring unique usernames
            if (!isset($users[$username])) {
                $users[$username] = [
                    'imageCount' => $imageCount
                ];
            }
        }
    }
}

// Calculate total shinies
$totalShinies = array_sum(array_column($users, 'imageCount'));

// Prepare data for JSON file
$jsonData = [
    "name" => "Sable",
    "code" => "SABL",
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
$file = "$dir/sabl.json";
file_put_contents($file, json_encode($jsonData, JSON_PRETTY_PRINT));

// Display the results
echo "<h1>Team Sable OT Shiny Showcase</h1>";
echo "<ul>";
foreach ($users as $username => $data) {
    echo "<li><strong>$username</strong>: {$data['imageCount']} shinies</li>";
}
echo "</ul>";
?>
