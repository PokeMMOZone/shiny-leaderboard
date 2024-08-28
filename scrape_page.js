const puppeteer = require('puppeteer');
const fs = require('fs');
const sleep = ms => new Promise(res => setTimeout(res, ms));

(async () => {
    const browser = await puppeteer.launch();
    const page = await browser.newPage();
    await page.goto('https://www.pokemmotools.net/showcase');

    // Wait for the page to load completely
    await page.waitForSelector('body'); // Adjust the selector as needed

    await sleep(3000);
    
    // Extract data from <a> tags
    const users = {};
    const data = await page.evaluate(() => {
        const elements = Array.from(document.querySelectorAll('a, span')); // Select all <a> tags
        return elements.map(element => {
            console.log(element.textContent);
            const text = element.textContent.trim(); // Get the text content of the <a> tag
            const match = text.match(/(\w+)\s*\((\d+)\)/);
            return match ? { username: match[1].trim(), count: parseInt(match[2], 10) } : null;
        }).filter(item => item !== null);
    });

    // Process extracted data
    data.forEach(({ username, count }) => {
        if (!users[username]) {
            users[username] = { OTShinyCount: count };
        } else {
            users[username].OTShinyCount += count;
        }
    });

    // Debugging: Print final users and counts
    console.log('Extracted Users and Counts:', users);

    // Calculate total shinies
    const totalShinies = Object.values(users).reduce((sum, user) => sum + user.OTShinyCount, 0);

    // Prepare JSON data
    const jsonData = {
        name: "Thug",
        code: "Thug",
        url: "https://www.pokemmotools.net/showcase",
        totalshinies: totalShinies,
        members: Object.entries(users).map(([username, data]) => ({
            username,
            count: data.OTShinyCount
        }))
    };

    // Write data to JSON file
    fs.writeFileSync('teams/thug.json', JSON.stringify(jsonData, null, 2));

    // Close browser
    await browser.close();
})();
