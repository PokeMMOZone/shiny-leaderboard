<?php
function fetchWebpage_rsng($url) {
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

function extractUserData_rsng($html) {
    $users = [];
    // A simple regex to find names followed by a count in parentheses
    $usernamePattern = '/([a-zA-Z0-9_]+)\s*\((\d+)\)/';
    
    if (preg_match_all($usernamePattern, $html, $matches, PREG_SET_ORDER)) {
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
    
    return $users;
}

function createJSONData_rsng($users, $url) {
    $totalShinies = array_sum(array_column($users, 'imageCount'));
    $jsonData = [
        "name" => "TeamRisingPH",
        "code" => "RSNG",
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

function saveJSONFile_rsng($data, $filePath) {
    $dir = dirname($filePath);
    
    if (!is_dir($dir)) {
        if (!mkdir($dir, 0777, true)) {
            throw new Exception("Failed to create directories...");
        }
    }
    
    file_put_contents($filePath, json_encode($data, JSON_PRETTY_PRINT));
}

try {
    $url = "https://forums.pokemmo.com/index.php?/clubs/page/179-shiny-database/";
    $html = fetchWebpage_rsng($url);
    $users = extractUserData_rsng($html);
    $jsonData = createJSONData_rsng($users, $url);
    saveJSONFile_rsng($jsonData, __DIR__ . '/../teams/rsng.json');
    
    echo "<h1>Team Rising PH Shiny Database</h1><ul>";
    foreach ($users as $username => $data) {
        echo "<li><strong>$username</strong>: {$data['imageCount']} shinies</li>";
    }
    echo "</ul>";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
