<?php
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

function parseHTML($html) {
    $dom = new DOMDocument();
    libxml_use_internal_errors(true);
    $dom->loadHTML($html);
    libxml_clear_errors();
    
    return new DOMXPath($dom);
}

function extractUserData($xpath) {
    $userNodes = $xpath->query("//p[contains(@style, 'text-align:center;')]//a[@data-mentionid]");
    $countNodes = $xpath->query("//p[contains(@style, 'text-align:center;')]/strong[contains(text(), 'Total Count')]");

    $users = [];

    for ($i = 0; $i < $userNodes->length; $i++) {
        $username = ltrim($userNodes->item($i)->textContent, '@');
        $totalCount = intval(preg_replace('/[^0-9]/', '', $countNodes->item($i)->textContent));

        $users[$username] = [
            'totalCount' => $totalCount
        ];
    }
    
    return $users;
}

function createJSONData($users, $url) {
    $totalShinies = array_sum(array_column($users, 'totalCount'));
    $jsonData = [
        "name" => "ROO",
        "code" => "ROO",
        "url" => $url,
        "totalshinies" => $totalShinies,
        "members" => []
    ];
    
    foreach ($users as $username => $data) {
        $jsonData["members"][] = [
            "username" => $username,
            "count" => $data['totalCount']
        ];
    }
    
    return $jsonData;
}

function saveJSONFile($data, $filePath) {
    $dir = dirname($filePath);
    
    if (!is_dir($dir)) {
        if (!mkdir($dir, 0777, true)) {
            throw new Exception("Failed to create directories...");
        }
    }
    
    file_put_contents($filePath, json_encode($data, JSON_PRETTY_PRINT));
}

try {
    $url = "https://forums.pokemmo.com/index.php?/topic/177613-roo-shiny-showcase/";
    $html = fetchWebpage($url);
    $xpath = parseHTML($html);
    $users = extractUserData($xpath);
    $jsonData = createJSONData($users, $url);
    saveJSONFile($jsonData, __DIR__ . '/../teams/roo.json');
    
    echo "<h1>Team Roo OT Shiny Database</h1><ul>";
    foreach ($users as $username => $data) {
        echo "<li><strong>$username</strong>: {$data['totalCount']} shinies</li>";
    }
    echo "</ul>";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
