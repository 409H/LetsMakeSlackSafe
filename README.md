Go here and generate a legacy token for you: https://api.slack.com/custom-integrations/legacy-tokens

## What does it do?

* Disables use of people using Slackbot to remind other people other than themselves.
* Disables people from creating their own legacy tokens and sending messages as bots to channels and DMs.
  * You can whitelist the `bot_ids` in `config.ini` that are allowed to post messages.
  * This will also reduce the attack vector of people creating apps and messaging everyone to look more legit. See:
  