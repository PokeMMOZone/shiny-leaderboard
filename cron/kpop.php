<?php
// URL of the page to scrape
$url = "https://forums.pokemmo.com/index.php?/topic/165706-team-kpop-shiny-showcase-2024-%E2%99%AA-%E2%99%AC-%E2%98%86/";

// Initialize cURL session
$ch = curl_init();

// Set the URL and other necessary options
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

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

// Regular expression patterns
$nameAndCountPattern = '/([\w]+)\s*\((\d+)\)/';

// Loop through each <p> tag
foreach ($pTags as $pTag) {
    // Extract and clean text from the <p> tag
    $pContent = strip_tags($dom->saveHTML($pTag));
    $pContent = html_entity_decode($pContent, ENT_QUOTES | ENT_HTML5);

    // Check for name and shiny count in cleaned text
    if (preg_match($nameAndCountPattern, $pContent, $matches)) {
        $name = trim($matches[1]); // Extract name
        $shinyCount = intval($matches[2]); // Extract shiny count

        if (!isset($users[$name])) {
            $users[$name] = [
                'shinyCount' => $shinyCount
            ];
        } else {
            // Add to existing count
            $users[$name]['shinyCount'] += $shinyCount;
        }
    }
}

// Calculate total shinies
$totalShinies = array_sum(array_column($users, 'shinyCount'));

// Prepare data for JSON
$jsonData = [
    "name" => "KPOP",
    "code" => "KPOP",
    "url" => $url,
    "totalshinies" => $totalShinies,
    "members" => []
];

foreach ($users as $name => $data) {
    $jsonData["members"][] = [
        "username" => $name,
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
$file = "$dir/kpop.json";
file_put_contents($file, json_encode($jsonData, JSON_PRETTY_PRINT));

// Display results
echo "<h1>Team KPOP OT Shiny Showcase</h1>";
echo "<ul>";
foreach ($users as $name => $data) {
    echo "<li><strong>$name</strong>: {$data['shinyCount']} shinies</li>";
}
echo "</ul>";
?>
