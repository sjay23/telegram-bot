TelegramBotApiBundle
===================
A symfony wrapper bundle for  [Telegram Bot API](https://core.telegram.org/bots/api).

## Configure the bundle

This bundle was designed to just work out of the box. The only thing you have to configure in order to get this bundle up and running is your bot [token](https://core.telegram.org/bots#botfather).

```yaml
# config/packages/telegram.yaml

telegram_bot_api:
    # Proxy (optional) :
    #proxy: 'socks5h://127.0.0.1:5090' # this is example you have to change this
    #async_requests: false

    # Development section:
    development:
        # implement in next version
        # Telegram user_id of developers accounts
        developers_id: [1234567, 87654321]
        # If this mode is enabled, the robot only responds to the developers
        maintenance:
            text: "The robot is being repaired! Please come back later."
  
    # Bots:
    bots:
        # The bot name
        first:
            # Your bot token: (required)
            token: 123456789:ABCD1234****4321CBA
            maintenance: false
        second:
            # Your bot token: (required)
            token: 123456789:ABCD1234****4321CBA
            maintenance: false
    
    # The default bot returned when you call getBot()
    default: 'second' 
```

## Webhook
In order to receive updates via a Webhook, You first need to tell your webhook URL to Telegram. You can use setWebhook method to specify a url and receive incoming updates via an outgoing webhook or use this commands:.

for get information about webhook of bot:
```bash
    $ php bin/console telegram:bot:webhook:info
```

for set webhook url for the bot:
```bash
    $ php bin/console telegram:bot:webhook:set
```

for delete webhook of the bot:
```bash
    $ php bin/console telegram:bot:webhook:delete
```
