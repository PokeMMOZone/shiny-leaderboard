<?php
function fetchWebpage_pory($url)
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

function parseHTML_pory($html)
{
    $dom = new DOMDocument();
    libxml_use_internal_errors(true);
    $dom->loadHTML($html);
    libxml_clear_errors();

    return new DOMXPath($dom);
}

function extractUserData_pory($xpath)
{
    // Extract the content of all <p> tags
    $pTags = $xpath->query("//p");
    $users = [];
    $usernamePattern = '/([a-zA-Z0-9]+(?:\s+[a-zA-Z0-9]+)*)\s*-\s*\((\d+)\)/';

    foreach ($pTags as $pTag) {
        $pContent = $pTag->textContent;

        // Match usernames and shiny counts
        if (preg_match_all($usernamePattern, $pContent, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $username = trim($match[1]);
                $imageCount = intval($match[2]);

                if (!isset($users[$username])) {
                    $users[$username] = [
                        'imageCount' => $imageCount,
                    ];
                }
            }
        }
    }

    return $users;
}

function createJSONData_pory($users, $url)
{
    $totalShinies = array_sum(array_column($users, 'imageCount'));
    $jsonData = [
        "name" => "Pory",
        "code" => "Pory",
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

function saveJSONFile_pory($data, $filePath)
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
    $url = "https://forums.pokemmo.com/index.php?/topic/184615-pory-ot-shiny-showcase/";
    $html = fetchWebpage_pory($url);
    $xpath = parseHTML_pory($html);
    $users = extractUserData_pory($xpath);
    $jsonData = createJSONData_pory($users, $url);
    saveJSONFile_pory($jsonData, __DIR__ . '/../teams/pory.json');

    echo "<h1>Team Pory OT Shiny Database</h1><ul>";
    foreach ($users as $username => $data) {
        echo "<li><strong>$username</strong>: {$data['imageCount']} shinies</li>";
    }
    echo "</ul>";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>