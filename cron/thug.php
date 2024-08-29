<?php
// Define the API URL
$apiUrl = 'http://localhost:3000/get-shiny-counts';

// Fetch the JSON data from the API
$jsonData = file_get_contents($apiUrl);
if ($jsonData === FALSE) {
    die("Failed to retrieve data from the API.");
}


// Decode the JSON data
$data = json_decode($jsonData, true);

// Check if decoding was successful
if ($data === null) {
    die("Failed to decode JSON data.");
}

// Define the path where the JSON file will be saved
$file = __DIR__ . '/../teams/thug.json';

// Save the JSON data to a file
if (file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT)) === FALSE) {
    die("Failed to save JSON data to file.");
}

// Display the data on the webpage
echo "<h1>Team Thug Shiny Showcase</h1>";
echo "<ul>";
foreach ($data['members'] as $member) {
    echo "<li><strong>{$member['username']}</strong>: {$member['count']} shinies</li>";
}
echo "</ul>";

echo "<p>Total shinies: {$data['totalshinies']}</p>";
?>
