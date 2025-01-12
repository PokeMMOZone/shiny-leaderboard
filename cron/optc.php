<?php
// URL of the page to scrape
$url = "https://forums.pokemmo.com/index.php?/topic/169239-optic-shiny-showcase/";

// Initialize cURL session
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

// Check if the response was successful
if ($response === false) {
    die("Failed to fetch the webpage.");
}

// Load the HTML into DOMDocument
$dom = new DOMDocument();
libxml_use_internal_errors(true); // Suppress parsing warnings
$dom->loadHTML($response);
libxml_clear_errors();

// Extract all <p> tags
$paragraphs = $dom->getElementsByTagName('p');

// Initialize arrays to hold parsed user data
$users = [];

// Iterate over paragraphs to find usernames and shiny counts
foreach ($paragraphs as $paragraph) {
    $text = trim($paragraph->textContent);
    // Regex to match "Username - Count"
    if (preg_match('/([\w\-]+)\s*-\s*(\d+)/i', $text, $matches)) {
        $username = $matches[1];
        $count = intval($matches[2]);

        // Aggregate data
        if (!isset($users[$username])) {
            $users[$username] = $count;
        } else {
            $users[$username] += $count;
        }
    }
}

// Calculate total shinies
$totalShinies = array_sum($users);

// Prepare data for JSON file
$jsonData = [
    "name" => "OPTIC",
    "code" => "OpTc",
    "url" => $url,
    "totalshinies" => $totalShinies,
    "members" => []
];

foreach ($users as $username => $count) {
    $jsonData["members"][] = [
        "username" => $username,
        "count" => $count
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
$file = "$dir/optc.json";
file_put_contents($file, json_encode($jsonData, JSON_PRETTY_PRINT));

// Display results
echo "<h1>Team OPTIC Shiny Showcase</h1>";
echo "<ul>";
foreach ($users as $username => $count) {
    echo "<li><strong>$username</strong>: $count shinies</li>";
}
echo "</ul>";
?>
