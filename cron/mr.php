<?php
function fetchAPIData_mr($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    
    if ($response === false) {
        throw new Exception("Failed to fetch the API data.");
    }
    
    return json_decode($response, true);
}

function extractUserData_mr($apiData) {
    $users = [];
    $usernamePattern = '/([a-zA-Z0-9_]+)\s*\((\d+)\)/';
    
    foreach ($apiData as $entry) {
        if (preg_match($usernamePattern, $entry, $matches)) {
            $username = trim($matches[1]);
            $imageCount = intval($matches[2]);
            
            if (!isset($users[$username])) {
                $users[$username] = ['imageCount' => $imageCount];
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
        "url" => "https://www.pokemmotools.net/mr", // Updated URL
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
    $url = "https://www.pokemmotools.net/api/mr";
    $apiData = fetchAPIData_mr($url);
    $users = extractUserData_mr($apiData);
    $jsonData = createJSONData_mr($users, $url);
    saveJSONFile_mr($jsonData, __DIR__ . '/../teams/mr.json');
    
    echo "<h1>Team MR OT Shiny Database</h1><ul>";
    foreach ($users as $username => $data) {
        echo "<li><strong>$username</strong>: {$data['imageCount']} shinies</li>";
    }
    echo "</ul><h1>Total Shinies: {$jsonData['totalshinies']}</h1>";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
