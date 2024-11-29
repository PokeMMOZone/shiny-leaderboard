<?php
function fetchWebpage_br($url) {
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

function parseHTML_br($html) {
    $dom = new DOMDocument();
    libxml_use_internal_errors(true);
    $dom->loadHTML($html);
    libxml_clear_errors();
    
    return new DOMXPath($dom);
}

function extractUserData_br($xpath) {
    $pTags = $xpath->query("//p");
    $users = [];
    
    // Adjusted regex to account for names with styles and counts in parentheses
    $usernamePattern = '/([a-zA-Z0-9_]+)\s*\((\d+)\)/';
    
    foreach ($pTags as $pTag) {
        $pContent = $pTag->textContent;
        
        if (preg_match($usernamePattern, $pContent, $usernameMatches)) {
            $username = trim($usernameMatches[1]);
            $imageCount = intval($usernameMatches[2]);
            
            if (!isset($users[$username])) {
                $users[$username] = [
                    'imageCount' => $imageCount
                ];
            }
        }
    }
    
    return $users;
}

function createJSONData_br($users, $url) {
    $totalShinies = array_sum(array_column($users, 'imageCount'));
    $jsonData = [
        "name" => "TeamBrilliant",
        "code" => "ßr",
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

function saveJSONFile_br($data, $filePath) {
    $dir = dirname($filePath);
    
    if (!is_dir($dir)) {
        if (!mkdir($dir, 0777, true)) {
            throw new Exception("Failed to create directories...");
        }
    }
    
    file_put_contents($filePath, json_encode($data, JSON_PRETTY_PRINT));
}

try {
    $url = "https://forums.pokemmo.com/index.php?/clubs/page/204-gem-cave%E2%84%A2/";
    $html = fetchWebpage_br($url);
    $xpath = parseHTML_br($html);
    $users = extractUserData_br($xpath);
    $jsonData = createJSONData_br($users, $url);
    saveJSONFile_br($jsonData, __DIR__ . '/../teams/br.json');
    
    echo "<h1>Team Brilliant OT Shiny Database</h1><ul>";
    foreach ($users as $username => $data) {
        echo "<li><strong>$username</strong>: {$data['imageCount']} shinies</li>";
    }
    echo "</ul>";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
