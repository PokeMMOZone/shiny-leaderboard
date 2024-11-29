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

// Search entire HTML content using a regular expression to capture username and shiny count
// Updated regex pattern to allow for any content between the username and shiny count
$usernamePattern = '/<strong>\s*([^<]+?)\s*-\s*(\d+)\s*(?:<br\s*\/?>|<\/span>)?\s*<\/strong>/i';

// Initialize arrays to hold the parsed user data
$users = [];

// Match all occurrences of the pattern in the response
if (preg_match_all($usernamePattern, $response, $matches, PREG_SET_ORDER)) {
    foreach ($matches as $match) {
        $currentUsername = trim($match[1]);
        $OTShinyCount = intval($match[2]);

        // Add the user and their shiny count to the $users array
        if (!isset($users[$currentUsername])) {
            $users[$currentUsername] = [
                'OTShinyCount' => $OTShinyCount
            ];
        } else {
            // If the user already exists, add the counts (in case of multiple entries)
            $users[$currentUsername]['OTShinyCount'] += $OTShinyCount;
        }
    }
}

// Calculate total shinies
$totalShinies = array_sum(array_column($users, 'OTShinyCount'));

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
        "count" => $data['OTShinyCount']
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
$file = "$dir/optc.json";  // Updated output file name
file_put_contents($file, json_encode($jsonData, JSON_PRETTY_PRINT));

// Display the results
echo "<h1>Team OPTIC Shiny Showcase</h1>";
echo "<ul>";
foreach ($users as $username => $data) {
    echo "<li><strong>$username</strong>: {$data['OTShinyCount']} shinies</li>";
}
echo "</ul>";
?>
