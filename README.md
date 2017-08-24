![https://github.com/409H/LetsMakeSlackSafe/blob/master/img/banner.png?raw=true](https://github.com/409H/LetsMakeSlackSafe/blob/master/img/banner.png?raw=true)


## Installation
 * Log into your Slack team
 * Go here and generate a legacy token for you: https://api.slack.com/custom-integrations/legacy-tokens
 * Run `cp config.ini.template config.ini` and put your legacy token into `app[token]`.
 * Run `php -f index.php`

## What does it do?

* Disables use of people using Slackbot to remind channels.
* Disables people from sending messages as anyone through the open legacy tokens api.
  * You can whitelist the `bot_ids` in `config.ini` that are allowed to post messages.
  * This will also reduce the attack vector of people creating apps and messaging everyone to look more legit.

## No more of these messages phishing your users!
![https://github.com/409H/LetsMakeSlackSafe/blob/master/img/no-more-app-phishing.PNG?raw=true](https://github.com/409H/LetsMakeSlackSafe/blob/master/img/no-more-app-phishing.PNG?raw=true)

## Roadmap

* Periodically remind admin users who don't have 2fa enabled to enable it.
* Look at URLS in messages and see if they're in the [EtherScamDb](https://etherscamdb.info/) database.
* Archive deleted messages to an archive private channel for admins to look at.