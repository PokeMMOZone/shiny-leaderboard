<?php
function fetchWebpage_exi($url)
{
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

function parseHTML_exi($html)
{
    $dom = new DOMDocument();
    libxml_use_internal_errors(true);
    $dom->loadHTML($html);
    libxml_clear_errors();

    return new DOMXPath($dom);
}

function extractUserData_exi($xpath) {
    $pTags = $xpath->query("//p");
    $users = [];
    
    // Regex to match usernames and shiny counts
    $usernamePattern = '/<strong>(.*?)\s\((\d+)\)<\/strong>/';

    foreach ($pTags as $pTag) {
        $pContent = $pTag->ownerDocument->saveHTML($pTag);

        if (preg_match_all($usernamePattern, $pContent, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $username = trim($match[1]);
                $shinyCount = intval($match[2]);

                // Remove "- Leader" specifically from "Omboss - Leader"
                if (strpos($username, ' - Leader') !== false) {
                    $username = str_replace(' - Leader', '', $username);
                }

                // Remove the specified string from usernames
                $username = str_replace("</strong></span></span><span style=\"color:#f39c12;\"><span style=\"font-family:'Lucida Sans Unicode', 'Lucida Grande', sans-serif;\"><strong>", '', $username);

                if (!isset($users[$username])) {
                    $users[$username] = [
                        'shinyCount' => $shinyCount
                    ];
                }
            }
        }
    }

    return $users;
}

function createJSONData_exi($users, $url)
{
    $totalShinies = array_sum(array_column($users, 'shinyCount'));
    $jsonData = [
        "name" => "The Exiles",
        "code" => "eXi",
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

function saveJSONFile_exi($data, $filePath)
{
    $dir = dirname($filePath);

    if (!is_dir($dir)) {
        if (!mkdir($dir, 0777, true)) {
            throw new Exception("Failed to create directories...");
        }
    }

    file_put_contents($filePath, json_encode($data, JSON_PRETTY_PRINT));
}

try {
    $url = "https://forums.pokemmo.com/index.php?/topic/184799-%E2%9C%A8-team-exi-shiny-showcase-%E2%9C%A8/";
    $html = fetchWebpage_exi($url);
    $xpath = parseHTML_exi($html);
    $users = extractUserData_exi($xpath);
    $jsonData = createJSONData_exi($users, $url);
    saveJSONFile_exi($jsonData, __DIR__ . '/../teams/exi.json');

    echo "<h1>The Exiles OT Shiny Database</h1><ul>";
    foreach ($users as $username => $data) {
        echo "<li><strong>$username</strong>: {$data['shinyCount']} shinies</li>";
    }
    echo "</ul>";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>