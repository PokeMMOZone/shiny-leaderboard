<?php
function fetchWebpage_pkem($url) {
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

function parseHTML_pkem($html) {
    $dom = new DOMDocument();
    libxml_use_internal_errors(true);
    $dom->loadHTML($html);
    libxml_clear_errors();
    
    return new DOMXPath($dom);
}

function extractUserData_pkem($xpath) {
    $pTags = $xpath->query("//p");
    $users = [];
    
    // Adjusted pattern for 'username (count)' format
    $usernamePattern = '/([a-zA-Z0-9]+)\s*\((\d+)\)/';

    foreach ($pTags as $pTag) {
        $pContent = $pTag->textContent;

        if (preg_match($usernamePattern, $pContent, $usernameMatches)) {
            $username = trim($usernameMatches[1]);
            $shinyCount = intval($usernameMatches[2]);

            if (!isset($users[$username])) {
                $users[$username] = [
                    'shinyCount' => $shinyCount
                ];
            }
        }
    }

    return $users;
}

function createJSONData_pkem($users, $url) {
    $totalShinies = array_sum(array_column($users, 'shinyCount'));
    $jsonData = [
        "name" => "PokéMafia",
        "code" => "PkéM",
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

    return $jsonData;
}

function saveJSONFile_pkem($data, $filePath) {
    $dir = dirname($filePath);
    
    if (!is_dir($dir)) {
        if (!mkdir($dir, 0777, true)) {
            throw new Exception("Failed to create directories...");
        }
    }
    
    file_put_contents($filePath, json_encode($data, JSON_PRETTY_PRINT));
}

try {
    $url = "https://forums.pokemmo.com/index.php?/topic/180683-pok%C3%A9mafia-pk%C3%A9m-team-ot-shiny-showcase/";
    $html = fetchWebpage_pkem($url);
    $xpath = parseHTML_pkem($html);
    $users = extractUserData_pkem($xpath);
    $jsonData = createJSONData_pkem($users, $url);
    saveJSONFile_pkem($jsonData, __DIR__ . '/../teams/pkem.json');
    
    echo "<h1>PokéMafia OT Shiny Database</h1><ul>";
    foreach ($users as $username => $data) {
        echo "<li><strong>$username</strong>: {$data['shinyCount']} shinies</li>";
    }
    echo "</ul>";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
