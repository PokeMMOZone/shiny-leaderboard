<?php
function fetchWebpage_mush($url) {
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

function parseHTML_mush($html) {
    $dom = new DOMDocument();
    libxml_use_internal_errors(true);
    $dom->loadHTML($html);
    libxml_clear_errors();
    
    return new DOMXPath($dom);
}

function extractUserData_mush($xpath) {
    $pTags = $xpath->query("//p[@style='text-align:center;']");
    $users = [];
    
    foreach ($pTags as $pTag) {
        $spans = $pTag->getElementsByTagName('span');
        $username = null;
        $imageCount = 0;
        
        foreach ($spans as $span) {
            if ($span->getAttribute('style') === 'font-size:20px;') {
                $username = preg_replace('/^\s+|\s+$/u', '', $span->textContent);
            } elseif ($span->getAttribute('style') === 'color:#e67e22;') {
                $nestedUsername = preg_replace('/^\s+|\s+$/u', '', $span->textContent);
                if (!empty($nestedUsername)) {
                    $username = $nestedUsername;
                }
            } elseif ($span->getAttribute('style') === 'font-size:9px;') {
                $imageCount = intval(trim($span->textContent, '()'));
            }
        }
        
        $aTags = $pTag->getElementsByTagName('a');
        foreach ($aTags as $aTag) {
            $aUsername = preg_replace('/^\s+|\s+$/u', '', $aTag->textContent);
            if (!empty($aUsername)) {
                $username = $aUsername;
            }
        }
        
        if ($username && $imageCount) {
            $username = preg_replace('/^\s+|\s+$/u', '', $username);
            if (!isset($users[$username])) {
                $users[$username] = [
                    'imageCount' => $imageCount
                ];
            }
        }
    }
    
    return $users;
}

function createJSONData_mush($users, $url) {
    $totalShinies = array_sum(array_column($users, 'imageCount'));
    $jsonData = [
        "name" => "Shroom",
        "code" => "MÜSH",
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

function saveJSONFile_mush($data, $filePath) {
    $dir = dirname($filePath);
    
    if (!is_dir($dir)) {
        if (!mkdir($dir, 0777, true)) {
            throw new Exception("Failed to create directories...");
        }
    }
    
    file_put_contents($filePath, json_encode($data, JSON_PRETTY_PRINT));
}

try {
    $url = "https://forums.pokemmo.com/index.php?/topic/186128-m%C3%BCshteamshroom-ot-shiny-showcase/";
    $html = fetchWebpage_mush($url);
    $xpath = parseHTML_mush($html);
    $users = extractUserData_mush($xpath);
    $jsonData = createJSONData_mush($users, $url);
    saveJSONFile_mush($jsonData, __DIR__ . '/../teams/mush.json');
    
    echo "<h1>Team Mushteamshroom OT Shiny Showcase</h1><ul>";
    foreach ($users as $username => $data) {
        $username = preg_replace('/^\s+|\s+$/u', '', $username); // Extra trim to ensure no leading/trailing spaces
        echo "<li><strong>$username</strong>: {$data['imageCount']} shinies</li>";
    }
    echo "</ul>";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
