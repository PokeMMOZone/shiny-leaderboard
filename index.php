<?php
function getTeamsData($dir) {
    $teams = [];
    foreach (glob($dir . '/*.json') as $file) {
        $json = file_get_contents($file);
        $data = json_decode($json, true);
        $data['members_count'] = count($data['members']); // Count the number of members
        $teams[] = $data;
    }
    return $teams;
}

function sortTeamsByShinies($teams) {
    usort($teams, function ($a, $b) {
        return $b['totalshinies'] - $a['totalshinies'];
    });
    return $teams;
}

$teams = getTeamsData('teams');
$sortedTeams = sortTeamsByShinies($teams);

// Initialize ranking and handle ties
$rankedTeams = [];
$rank = 1;
$prevShinies = null;
foreach ($sortedTeams as $index => $team) {
    if ($prevShinies !== null && $team['totalshinies'] < $prevShinies) {
        $rank = $index + 1;
    }
    $team['rank'] = $rank;
    $rankedTeams[] = $team;
    $prevShinies = $team['totalshinies'];
}

$totalTeams = count($teams);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PokeMMO Shiny OT Team Leaderboard</title>
    <link rel="icon" type="image/png" href="icon.png">
    <?php include 'styles-scripts.php'; ?>
</head>
<body>
    <?php include 'nav.php'; ?>
    <div class="container">
        <h1>PokeMMO Shiny OT Team Leaderboard</h1>
        <p>Total number of teams: <?php echo $totalTeams; ?></p>
        <table id="leaderboard" class="display">
            <thead>
                <tr>
                    <th>Rank</th>
                    <th>Team Tag</th>
                    <th>Team Name</th>
                    <th>Members</th>
                    <th>Total Shinies</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rankedTeams as $team) : ?>
                    <tr>
                        <td><?php echo $team['rank']; ?></td>
                        <td><?php echo htmlspecialchars($team['code']); ?></td>
                        <td><a href="<?php echo htmlspecialchars($team['url']); ?>" target="_blank"><?php echo htmlspecialchars($team['name']); ?></a></td>
                        <td><?php echo $team['members_count']; ?></td> <!-- Display the number of members -->
                        <td><?php echo $team['totalshinies']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php include 'footer.php'; ?>
</body>
</html>
