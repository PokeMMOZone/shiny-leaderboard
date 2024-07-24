<?php
// URL of the page to scrape
$url = "https://forums.pokemmo.com/index.php?/topic/169239-optic-shiny-showcase/";

// Initialize cURL session
$ch = curl_init();

// Set the URL and other necessary options
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Execute the request
$response = curl_exec($ch);

// Close cURL session
curl_close($ch);

// Check if the response was successful
if ($response === false) {
    die("Failed to fetch the webpage.");
}

// Load the HTML response into a DOMDocument
$dom = new DOMDocument();
libxml_use_internal_errors(true); // Suppress HTML parsing errors
$dom->loadHTML($response);
libxml_clear_errors();

// Create a new XPath object
$xpath = new DOMXPath($dom);

// Find all <p> tags
$pTags = $xpath->query("//p");

// Initialize arrays
$users = [];

// Regular expression patterns to match usernames, shiny counts, and TOTAL OT counts
$usernamePattern = '/@(\w+)/';
$shinyCountPattern = '/<font color="#ffffff">(\d+)<\/font>/';
$totalOtPattern = '/TOTAL OT:\s*(\d+)/';

foreach ($pTags as $pTag) {
    $pContent = $dom->saveHTML($pTag);

    // Check if the <p> tag contains a username
    if (preg_match($usernamePattern, $pContent, $usernameMatches)) {
        $currentUsername = trim($usernameMatches[1]); // Remove @

        // Initialize user data if not already set
        if (!isset($users[$currentUsername])) {
            $users[$currentUsername] = [
                'imageCount' => 0,
                'totalOt' => 0
            ];
        }

        // Check if the <p> tag contains a shiny count
        if (preg_match($shinyCountPattern, $pContent, $shinyCountMatches)) {
            $imageCount = intval($shinyCountMatches[1]);
            $users[$currentUsername]['imageCount'] += $imageCount;
        }

        // Check if the <p> tag contains a TOTAL OT count
        if (preg_match($totalOtPattern, $pContent, $totalOtMatches)) {
            $totalOtCount = intval($totalOtMatches[1]);
            $users[$currentUsername]['totalOt'] = $totalOtCount;
        }
    }
}

// Calculate total shinies
$totalShinies = array_sum(array_column($users, 'imageCount'));

// Prepare data for JSON file
$jsonData = [
    "name" => "OPTIC",
    "code" => "OpTc",
    "url" => $url,
    "totalshinies" => $totalShinies,
    "members" => []
];

foreach ($users as $username => $data) {
    $jsonData["members"][] = [
        "username" => $username,
        "count" => $data['imageCount'],
        "totalOt" => $data['totalOt']
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
$file = "$dir/optic.json";
file_put_contents($file, json_encode($jsonData, JSON_PRETTY_PRINT));

// Display the results
echo "<h1>Team OPTIC Shiny Showcase</h1>";
echo "<ul>";
foreach ($users as $username => $data) {
    echo "<li><strong>$username</strong>: {$data['imageCount']} shinies, TOTAL OT: {$data['totalOt']}</li>";
}
echo "</ul>";
?>
