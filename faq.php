<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAQ - PokeMMO Shiny OT Leaderboard</title>
    <link rel="icon" type="image/png" href="icon.png">
    <?php include 'styles-scripts.php'; ?>
</head>
<body>
    <?php include 'nav.php'; ?>
    <div class="container">
        <h1>Frequently Asked Questions</h1>
        <div class="faq-item">
            <h2>What is the PokeMMO Shiny OT Team Leaderboard?</h2>
            <p>The PokeMMO Shiny OT Team Leaderboard ranks teams based on the total number of shiny Pokémon caught by their members. Only OT (Original Trainer) shinies are counted, meaning these are shinies the players have caught themselves and still possess.</p>
        </div>
        <div class="faq-item">
            <h2>What is the PokeMMO Player OT Shiny Leaderboard?</h2>
            <p>The PokeMMO Player OT Shiny Leaderboard ranks individual players based on the total number of shiny Pokémon they have caught. Only OT shinies are counted.</p>
        </div>
        <div class="faq-item">
            <h2>How is the team ranking determined?</h2>
            <p>The team ranking is determined by the total number of OT shinies caught by each team. Teams with more shinies are ranked higher.</p>
        </div>
        <div class="faq-item">
            <h2>How is the player ranking determined?</h2>
            <p>The player ranking is determined by the total number of OT shinies caught by each player. Players with more shinies are ranked higher.</p>
        </div>
        <div class="faq-item">
            <h2>Where does the data come from?</h2>
            <p>The data is generated from web scrapers that read the team showcases.</p>
        </div>
        <div class="faq-item">
            <h2>How often are the leaderboards updated?</h2>
            <p>The shiny counts are updated on a daily basis from publicly available information on PokeMMO. Teams often have a shiny showcase, and the data is scraped from there.</p>
        </div>
        <div class="faq-item">
            <h2>How can I get support or onboard a team?</h2>
            <p>For support or to onboard a team, you can create a support ticket on the <a href="http://discord.pokemmo.zone/" target="_blank">PokeMMO Zone discord</a> or submit pull requests to <a href="https://github.com/PokeMMOZone/shiny-leaderboard" target="_blank">our repository</a>. To make it easier to add your team, we recommend using the format "username (shiny count)" on your team's showcase.</p>
        </div>
    </div>
    <?php include 'footer.php'; ?>
</body>
</html>
