<?php
function fetchWebpage_fbas($url) {
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

function parseHTML_fbas($html) {
    $dom = new DOMDocument();
    libxml_use_internal_errors(true);
    $dom->loadHTML($html);
    libxml_clear_errors();
    
    return new DOMXPath($dom);
}

function extractUserData_fbas($xpath) {
    $pTags = $xpath->query("//p");
    $users = [];
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

function createJSONData_fbas($users, $url) {
    $totalShinies = array_sum(array_column($users, 'imageCount'));
    $jsonData = [
        "name" => "ForgedByAshes",
        "code" => "FBAS",
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

function saveJSONFile_fbas($data, $filePath) {
    $dir = dirname($filePath);
    
    if (!is_dir($dir)) {
        if (!mkdir($dir, 0777, true)) {
            throw new Exception("Failed to create directories...");
        }
    }
    
    file_put_contents($filePath, json_encode($data, JSON_PRETTY_PRINT));
}

try {
    $url = "https://forums.pokemmo.com/index.php?/clubs/page/165-shiny-showcase/";
    $html = fetchWebpage_fbas($url);
    $xpath = parseHTML_fbas($html);
    $users = extractUserData_fbas($xpath);
    $jsonData = createJSONData_fbas($users, $url);
    saveJSONFile_fbas($jsonData, __DIR__ . '/../teams/fbas.json');
    
    echo "<h1>Team FBAS Shiny Showcase</h1><ul>";
    foreach ($users as $username => $data) {
        echo "<li><strong>$username</strong>: {$data['imageCount']} shinies</li>";
    }
    echo "</ul>";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
