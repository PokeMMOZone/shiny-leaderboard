<?php
// URL of the page to scrape
// $url = "https://forums.pokemmo.com/index.php?/topic/179197-testing-ignore/";  // For testing
$url = "https://forums.pokemmo.com/index.php?/topic/159659-pory-ot-shiny-showcase/";

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
$possibleUsernameSpans = $xpath->query("//span[@style='font-size:16px;']");
$shinyCountSpans = $xpath->query("//span[@style='color:#ffffff;']");

// Initialize arrays
$users = [];

// Regular expression pattern to match usernames and counts
$usernamePattern = '>([a-zA-Z]+)<>';
$shinyCountPattern = '/<span style="color:#ffffff;">(\d+)<\/span>/';

$usernameSpans = [];

// Loop through each tag to extract only usernames
foreach ($possibleUsernameSpans as $possibleUsernameSpan) {
    $usernameContent = $dom->saveHTML($possibleUsernameSpan);
    // Check if the tag contains a username
    if (preg_match($usernamePattern, $usernameContent, $usernameMatches)) {
        $username = trim($usernameMatches[1]);
        // Add it to th array
        $usernameSpans[] = $possibleUsernameSpan;
    }
}

for($i = 0; $i < count($usernameSpans); $i++) {
    $usernameContent = $dom->saveHTML($usernameSpans[$i]);
    if (preg_match($usernamePattern, $usernameContent, $usernameMatches)) {
        $username = trim($usernameMatches[1]);
        debug_to_console($username);
        $imageCount = 0;
        if($shinyCountSpans[$i] != null){
            $shinyCountContent = $dom->saveHTML($shinyCountSpans[$i]);
            if (preg_match($shinyCountPattern, $shinyCountContent, $shinyCountMatches)) {
                $imageCount = intval($shinyCountMatches[1]);
            }
        }
        debug_to_console($imageCount);
        if (!isset($users[$username])) {
            $users[$username] = [
                'imageCount' => $imageCount
            ];
        } else {
            // If user already exists, add the count (in case of multiple entries)
            $users[$username]['imageCount'] += $imageCount;
        }
    }
}

// Calculate total shinies
$totalShinies = array_sum(array_column($users, 'imageCount'));

// Prepare data for JSON file
$jsonData = [
    "name" => "Porygon",
    "code" => "Pory",
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
if (!is_dir($dir) && !mkdir($dir, 0777, true)) {
    die("Failed to create directories...");
}

// Write data to JSON file
$file = "$dir/pory.json";
file_put_contents($file, json_encode($jsonData, JSON_PRETTY_PRINT));

// Display the results
echo "<h1>Team Pory OT Shiny Showcase</h1>";
echo "<ul>";
foreach ($users as $username => $data) {
    echo "<li><strong>$username</strong>: {$data['imageCount']} shinies</li>";
}
echo "</ul>";
?>
