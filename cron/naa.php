<?php
function fetchWebpage_naa($url) {
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

function parseHTML_naa($html) {
    $dom = new DOMDocument();
    libxml_use_internal_errors(true);
    $dom->loadHTML($html);
    libxml_clear_errors();
    
    return new DOMXPath($dom);
}

function extractUserData_naa($xpath) {
    $users = [];
    $usernamePatterns = [
        '/([a-zA-Z0-9_]+)(?:\s*-\s*[a-zA-Z]+\s*)?\((\d+)\)/', // Format 1: "Username - Rank (count)"
        '/([a-zA-Z0-9_]+)\s*\((\d+)\)/'                       // Format 2: "Username (count)"
    ];

    $elements = $xpath->query("//b/span | //p/span");

    foreach ($elements as $element) {
        $content = $element->textContent;
        foreach ($usernamePatterns as $pattern) {
            if (preg_match($pattern, $content, $matches)) {
                $username = trim($matches[1]);
                $imageCount = intval($matches[2]);

                if (!isset($users[$username])) {
                    $users[$username] = [
                        'imageCount' => $imageCount
                    ];
                }
                break; // Stop checking other patterns if one matches
            }
        }
    }

    return $users;
}

function createJSONData_naa($users, $url) {
    $totalShinies = array_sum(array_column($users, 'imageCount'));
    $jsonData = [
        "name" => "NoApesAllowed",
        "code" => "NAA",
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
    
    return $jsonData;
}

function saveJSONFile_naa($data, $filePath) {
    $dir = dirname($filePath);
    
    if (!is_dir($dir)) {
        if (!mkdir($dir, 0777, true)) {
            throw new Exception("Failed to create directories...");
        }
    }
    
    file_put_contents($filePath, json_encode($data, JSON_PRETTY_PRINT));
}

try {
    $url = "https://forums.pokemmo.com/index.php?/topic/180706-team-naa-no-apes-allowed-shiny-showcase/";
    $html = fetchWebpage_naa($url);
    $xpath = parseHTML_naa($html);
    $users = extractUserData_naa($xpath);
    $jsonData = createJSONData_naa($users, $url);
    saveJSONFile_naa($jsonData, __DIR__ . '/../teams/naa.json');
    
    echo "<h1>Team NoApesAllowed OT Shiny Database</h1><ul>";
    foreach ($users as $username => $data) {
        echo "<li><strong>$username</strong>: {$data['imageCount']} shinies</li>";
    }
    echo "</ul>";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
