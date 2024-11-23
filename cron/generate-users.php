<?php
// Directory containing team JSON files
$teamsDir = __DIR__ . '/../teams';

// Directory to store the consolidated users JSON file
$usersDir = __DIR__ . '/../users';

// Create users directory if it doesn't exist
if (!is_dir($usersDir)) {
    if (!mkdir($usersDir, 0777, true)) {
        die("Failed to create users directory...");
    }
}

// Array to store all users
$allUsers = [];

// Read each JSON file in the teams directory
foreach (glob("$teamsDir/*.json") as $file) {
    $teamData = json_decode(file_get_contents($file), true);
    if ($teamData && isset($teamData['members'])) {
        foreach ($teamData['members'] as $member) {
            $allUsers[] = [
                'username' => $member['username'],
                'count' => $member['count'],
                'team' => [
                    'name' => $teamData['name'],
                    'code' => $teamData['code'],
                    'url' => $teamData['url']
                ]
            ];
        }
    }
}

// Prepare data for the users.json file
$usersData = [
    'users' => $allUsers
];

// Path to the users.json file
$usersFile = "$usersDir/users.json";

// Write data to users.json file
file_put_contents($usersFile, json_encode($usersData, JSON_PRETTY_PRINT));

// Display a message indicating success
echo "Users data successfully written to $usersFile\n";
?>
