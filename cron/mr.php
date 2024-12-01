<?php
function fetchWebpage_mr($url) {
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

function parseHTML_mr($html) {
    $dom = new DOMDocument();
    libxml_use_internal_errors(true);
    $dom->loadHTML($html);
    libxml_clear_errors();
    
    return new DOMXPath($dom);
}

function extractUserData_mr($xpath) {
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

function createJSONData_mr($users, $url) {
    $totalShinies = array_sum(array_column($users, 'imageCount'));
    $jsonData = [
        "name" => "Mr",
        "code" => "Mr",
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

function saveJSONFile_mr($data, $filePath) {
    $dir = dirname($filePath);
    
    if (!is_dir($dir)) {
        if (!mkdir($dir, 0777, true)) {
            throw new Exception("Failed to create directories...");
        }
    }
    
    file_put_contents($filePath, json_encode($data, JSON_PRETTY_PRINT));
}

try {

    $url = "https://www.pokemmotools.net/mr";
    $html = fetchWebpage_mr($url);
    $xpath = parseHTML_mr($html);
    $users = extractUserData_mr($xpath);
    $jsonData = createJSONData_mr($users, $url);
    saveJSONFile($jsonData, __DIR__ . '/../teams/mr.json');

    
    echo "<h1>Team MR OT Shiny Database</h1><ul>";
    foreach ($users as $username => $data) {
        echo "<li><strong>$username</strong>: {$data['imageCount']} shinies</li>";

    }
    echo "</ul><h1>Total Shinies: {$jsonData['totalshinies'] }</h1>";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
