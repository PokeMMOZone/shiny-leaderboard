<?php
// URL of the page to scrape
$url = "https://forums.pokemmo.com/index.php?/topic/155763-made-goldenrod-mafia-ot-shiny-showcase/";

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

// Regular expression pattern to match usernames and counts
$usernamePattern = '/([a-zA-Z0-9]+)\s\((\d+)\)/';

// Loop through each <p> tag
foreach ($pTags as $pTag) {
    $pContent = $dom->saveHTML($pTag);
    
    // Check if the <p> tag contains a username
    if (preg_match($usernamePattern, $pContent, $usernameMatches)) {
        $username = trim($usernameMatches[1]);
        $imageCount = intval($usernameMatches[2]);

        // Exclude the username "Shinies"
        if ($username === "Shinies") {
            continue;
        }
        
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
    "name" => "GoldenrodMAFIA",
    "code" => "made",
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
$file = "$dir/made.json";
file_put_contents($file, json_encode($jsonData, JSON_PRETTY_PRINT));

// Display the results
echo "<h1>Team GolderodMAFIA OT Shiny Showcase</h1>";
echo "<ul>";
foreach ($users as $username => $data) {
    echo "<li><strong>$username</strong>: {$data['imageCount']} shinies</li>";
}
echo "</ul>";
?>
