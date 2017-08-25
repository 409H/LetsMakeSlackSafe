![https://github.com/409H/LetsMakeSlackSafe/blob/master/img/banner.png?raw=true](https://github.com/409H/LetsMakeSlackSafe/blob/master/img/banner.png?raw=true)


## Installation
 * Clone the repository
 * Run `composer update`
 * Log into your Slack team
 * Go here and generate a legacy token for you: https://api.slack.com/custom-integrations/legacy-tokens
 * Run `cp config.ini.template config.ini` and put your legacy token into `app[token]`.
 * Run `php -f index.php`
 * Start chatting in Slack

### Commands

To set up some things, you need to know things that aren't publicly viewable. Below is a table of things you can run

| Command   	| Example Response           	    | Description                                          	|
|-----------	|----------------------------	    |------------------------------------------------------	|
| `+userid` 	| `COMMAND Userid: XXXXXXXX` 	    | Gives your slack user id for `admin[userid]` setting 	|
| `+update` 	| `COMMAND Updated domains: 2,141` 	| Updates the blacklist of domains provided by ESD   	|

## What does it do?

- [x] Disables use of people using Slackbot to remind channels.
- [x] Disables people from sending messages as anyone through the open legacy tokens api.
  * You can whitelist the `bot_ids` in `config.ini` that are allowed to post messages.
  * This will also reduce the attack vector of people creating apps and messaging everyone to look more legit.
- [ ] Periodically remind admin users who don't have 2fa enabled to enable it.
- [ ] Look at URLS in messages and see if they're in the [EtherScamDb](https://etherscamdb.info/) database.
- [ ] Archive deleted messages to an archive private channel for admins to look at.
- [ ] Deploy to Heroku button

## Author

* [https://twitter.com/sniko_](https://twitter.com/sniko_)
* [https://harrydenley.com/](https://harrydenley.com/)

Donations of ETH & ERC20 are accepted: 0xa4973cA595630F794413AAF290C4cf780987b142