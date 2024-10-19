<?php
// URL of the page to scrape
$url = "https://forums.pokemmo.com/index.php?/topic/182823-team%C2%A0legends-ot-shiny-board/";

// List of usernames to exclude
$excludedUsers = [
    'xlirate',  // Add usernames you want to exclude here
    'MrGunn',
    'SadcuzBad',
    'Gibmister',
    'TrustMeXD',
    'Prepizza',
    'MyParentsFight',
    'leSnifferofShins',
    'bigboycharizard',
    'JacobRothschild',
    'TrainerMurn',
    'ReyadElric',
    'papitime',
    'Zerabot',
    'Tikksie',
    'Jujz',
    'thedynamight',
    'chippyeater',
    'LongConSIlver',
    'OKAYLOL'
];

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

// Find all div and p tags
$tags = $xpath->query("//div | //p");

// Initialize arrays
$users = [];

// Regular expression pattern to match usernames and counts
$usernamePattern = '/([a-zA-Z0-9]+)\s*\((\d+)\)/';

// Loop through each tag
foreach ($tags as $tag) {
    $tagContent = $dom->saveHTML($tag);
    
    // Check if the tag contains a username
    if (preg_match($usernamePattern, $tagContent, $usernameMatches)) {
        $username = trim($usernameMatches[1]);
        $imageCount = intval($usernameMatches[2]);
        
        // Exclude usernames in the exclusion list
        if (!in_array($username, $excludedUsers)) {
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
    "name" => "LËGEND",
    "code" => "LGÑD",
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
$file = "$dir/lgnd.json";
file_put_contents($file, json_encode($jsonData, JSON_PRETTY_PRINT));

// Display the results
echo "<h1>Team LGNDS OT Shiny Board</h1>";
echo "<ul>";
foreach ($users as $username => $data) {
    echo "<li><strong>$username</strong>: {$data['imageCount']} shinies</li>";
}
echo "</ul>";
?>
