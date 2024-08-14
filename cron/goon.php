<?php
// URL of the page to scrape
$url = "https://forums.pokemmo.com/index.php?/clubs/page/158-the-gooncase%E2%84%A2/";

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

// Find all <p> tags with text content matching the pattern
$pTags = $xpath->query("//p");

// Initialize arrays
$users = [];

// Regular expression pattern to match usernames and counts
$usernamePattern = '/^\s*@?([\w\-\(\)\s]+)\s*[-–—]+\s*(\d+)\s*$/u';

foreach ($pTags as $pTag) {
    $pContent = trim($pTag->textContent);

    // Check if the <p> tag contains a username and shiny count
    if (preg_match($usernamePattern, $pContent, $matches)) {
        $username = trim($matches[1]);
        $shinyCount = intval($matches[2]);

        if (!isset($users[$username])) {
            $users[$username] = [
                'shinyCount' => $shinyCount
            ];
        } else {
            // If user already exists, add the count (in case of multiple entries)
            $users[$username]['shinyCount'] += $shinyCount;
        }
    } else {
        // Debug output for unmatched <p> tag content
        echo "Unmatched: $pContent\n";
    }
}

// Calculate total shinies
$totalShinies = array_sum(array_column($users, 'shinyCount'));

// Prepare data for JSON file
$jsonData = [
    "name" => "Goón",
    "code" => "Goón",
    "url" => $url,
    "totalshinies" => $totalShinies,
    "members" => []
];

foreach ($users as $username => $data) {
    $jsonData["members"][] = [
        "username" => $username,
        "count" => $data['shinyCount']
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
$file = "$dir/goon.json";
file_put_contents($file, json_encode($jsonData, JSON_PRETTY_PRINT));

// Display the results
echo "<h1>Team The Gooncase™ Shiny Showcase</h1>";
echo "<ul>";
foreach ($users as $username => $data) {
    echo "<li><strong>$username</strong>: {$data['shinyCount']} shinies</li>";
}
echo "</ul>";
?>
