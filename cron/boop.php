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
    $pTags = $xpath->query("//p");
    $users = [];

    // Adjusted regex to capture the username and shiny count in this context
    $usernamePattern = '/<strong>(.*?)<\/strong>.*?\((\d+)\)/';

    foreach ($pTags as $pTag) {
        $pContent = $pTag->ownerDocument->saveHTML($pTag);

        if (preg_match_all($usernamePattern, $pContent, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $username = trim($match[1]);
                $imageCount = intval($match[2]);
                
                if (!isset($users[$username])) {
                    $users[$username] = [
                        'imageCount' => $imageCount
                    ];
                }
            }
        }
    }
    
    return $users;
}

function createJSONData($users, $url) {
    $totalShinies = array_sum(array_column($users, 'imageCount'));
    $jsonData = [
        "name" => "Boop",
        "code" => "Boop",
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
    $url = "https://forums.pokemmo.com/index.php?/clubs/page/183-shiny-showcase/";
    $html = fetchWebpage($url);
    $xpath = parseHTML($html);
    $users = extractUserData($xpath);
    $jsonData = createJSONData($users, $url);
    saveJSONFile($jsonData, __DIR__ . '/../teams/boop.json');
    
    echo "<h1>Team Boop Shiny Showcase</h1><ul>";
    foreach ($users as $username => $data) {
        echo "<li><strong>$username</strong>: {$data['imageCount']} shinies</li>";
    }
    echo "</ul>";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
