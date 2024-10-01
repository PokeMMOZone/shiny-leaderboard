<?php
// Function to fetch the webpage content
function fetchWebpage($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    
    if ($response === false) {
        throw new Exception("Failed to fetch the webpage.");
    }
    
    return $response;
}

// Function to parse the HTML
function parseHTML($html) {
    $dom = new DOMDocument();
    libxml_use_internal_errors(true);
    $dom->loadHTML($html);
    libxml_clear_errors();
    
    return new DOMXPath($dom);
}

// Function to extract usernames and shiny counts
function extractUserData($xpath) {
    $members = [];
    $pattern = '/(?:@([A-Za-z0-9_]+)|([A-Za-z0-9]+))[^⭐]*\((\d+)\)/u';

    $nodes = $xpath->query('//p');

    foreach ($nodes as $node) {
        $text = $node->textContent;

        if (preg_match_all($pattern, $text, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $username = !empty($match[1]) ? trim($match[1]) : trim($match[2]);
                $shinyCount = intval($match[3]);

                $members[] = [
                    'username' => $username,
                    'count' => $shinyCount
                ];
            }
        }
    }
    return $members;
}

// Function to create JSON data
function createJSONData($members, $teamName, $teamCode, $url) {
    $totalShinies = array_sum(array_column($members, 'count'));

    return [
        "name" => $teamName,
        "code" => $teamCode,
        "url" => $url,
        "totalshinies" => $totalShinies,
        "members" => $members
    ];
}

// Function to save JSON data to a file
function saveJSONFile($data, $filePath) {
    $dir = dirname($filePath);
    
    // Create directory if it doesn't exist
    if (!is_dir($dir)) {
        if (!mkdir($dir, 0777, true)) {
            throw new Exception("Failed to create directories.");
        }
    }
    
    // Save the JSON data
    file_put_contents($filePath, json_encode($data, JSON_PRETTY_PRINT));
}

try {
    $url = 'https://forums.pokemmo.com/index.php?/topic/181924-lepa-shiny-showcase/';
    $teamName = 'LEGOWO PARTY';
    $teamCode = 'LEPA';
    
    // Fetch webpage content
    $html = fetchWebpage($url);
    
    // Parse HTML and extract user data
    $xpath = parseHTML($html);
    $members = extractUserData($xpath);
    
    // Create JSON data
    $jsonData = createJSONData($members, $teamName, $teamCode, $url);
    
    // Define the output file path
    $filePath = __DIR__ . '/../teams/lepa.json';
    
    // Save the JSON data to a file
    saveJSONFile($jsonData, $filePath);
    
    // Output the result in an HTML list format
    echo "<h1>{$teamName} ({$teamCode})</h1><ul>";
    foreach ($members as $member) {
        echo "<li><strong>{$member['username']}</strong>: {$member['shinies']} shinies</li>";
    }
    echo "</ul>";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
