<?php
function getTeamsData($dir) {
    $teams = [];
    foreach (glob($dir . '/*.json') as $file) {
        $json = file_get_contents($file);
        $data = json_decode($json, true);
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
        <table>
            <thead>
                <tr>
                    <th>Rank</th>
                    <th>Team Name</th>
                    <th>Total Shinies</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sortedTeams as $index => $team) : ?>
                    <tr>
                        <td><?php echo $index + 1; ?></td>
                        <td><a href="<?php echo htmlspecialchars($team['url']); ?>" target="_blank"><?php echo htmlspecialchars($team['name']); ?></a></td>
                        <td><?php echo $team['totalshinies']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php include 'footer.php'; ?>
</body>
</html>
