<?php
// Read the JSON data
$file = __DIR__ . '/../teams/thug.json';
if (!file_exists($file)) {
    die("JSON file not found.");
}

$jsonData = json_decode(file_get_contents($file), true);

// Display the results
echo "<h1>Team Thug Shiny Showcase</h1>";
echo "<ul>";
foreach ($jsonData['members'] as $member) {
    echo "<li><strong>{$member['username']}</strong>: {$member['count']} shinies</li>";
}
echo "</ul>";

// Optionally display total shinies
echo "<p>Total shinies: {$jsonData['totalshinies']}</p>";
?>
