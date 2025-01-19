<?php
function fetchWebpage_roo($url) {
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

function parseHTML_roo($html) {
    $dom = new DOMDocument();
    libxml_use_internal_errors(true);
    $dom->loadHTML($html);
    libxml_clear_errors();
    
    return new DOMXPath($dom);
}

function extractUserData_roo($xpath) {
    $userNodes = $xpath->query("//p[contains(@style, 'text-align:center;')]//span[contains(@style, 'color:#7ba889;')]");

    $users = [];

    foreach ($userNodes as $node) {
        // Extract the username and shiny count
        $text = $node->textContent;
        if (preg_match('/(.+?)\s\((\d+)\)/', $text, $matches)) {
            $username = trim($matches[1]);
            $shinyCount = intval($matches[2]);

            $users[$username] = [
                'totalCount' => $shinyCount,
            ];
        }
    }

    return $users;
}

function createJSONData_roo($users, $url) {
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

function saveJSONFile_roo($data, $filePath) {
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
    $html = fetchWebpage_roo($url);
    $xpath = parseHTML_roo($html);
    $users = extractUserData_roo($xpath);
    $jsonData = createJSONData_roo($users, $url);
    saveJSONFile_roo($jsonData, __DIR__ . '/../teams/roo.json');
    
    echo "<h1>Team Roo OT Shiny Database</h1><ul>";
    foreach ($users as $username => $data) {
        echo "<li><strong>$username</strong>: {$data['totalCount']} shinies</li>";
    }
    echo "</ul>";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
