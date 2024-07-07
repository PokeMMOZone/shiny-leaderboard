# shiny-leaderboard

A leaderboard for Shiny Hunter teams and players in PokeMMO.

## Overview

The PokeMMO Shiny OT Leaderboard is a community-driven project that ranks teams and individual players based on their shiny Pokémon achievements. Only OT (Original Trainer) shinies are counted, meaning these are shinies the players have caught themselves and still possess.

## Features

- **Team Leaderboard:** Ranks teams based on the total number of OT shinies.
- **Player Leaderboard:** Ranks individual players based on the total number of OT shinies.
- **Daily Updates:** The shiny counts are updated daily from publicly available information on PokeMMO.
- **Data Scraping:** JSON files are generated from web scrapers that read the team showcases.

## How It Works

1. **Data Collection:** The data is sourced from JSON files located in the `teams` and `users` directories. Each file contains information about a team's or player's name, URL, and total number of OT shinies.
2. **Data Scraping:** Web scrapers read the team showcases on PokeMMO to gather the necessary data. The `cron` folder contains the web scrapers that generate the JSON files.
3. **Daily Updates:** The data is updated on a daily basis to ensure the leaderboard reflects the most current and accurate information.

## Contribution

For support or to onboard a team, you can create a support ticket on the PokeMMO Zone discord (<http://discord.pokemmo.zone/>) or submit pull requests to our repository at <https://github.com/PokeMMOZone/shiny-leaderboard>.

## Contact

This project is brought to you by [PokeMMO Zone](https://pokemmo.zone/). For any questions or feedback, please reach out to us on our [discord server](http://discord.pokemmo.zone/).

## License

This project is licensed under the terms of the [LICENSE](./LICENSE) file.
