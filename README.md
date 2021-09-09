## Development

Using docker-compose, you can run containers for the app, vue, and the web server.

Build the images to install necessary packages before running
```
docker-compose build
```
Install PHP packages using composer
```
docker-compose run app composer install
```
Install packages so the image can run the yarn server
```
docker-compose run vue yarn install
```
Dependencies installed. You can run the project anytime with
```
docker-compose up -d
```

Now that your docker containers are up and running, you can set up the database
and a user via the shell in the "app" container running PHP.
```
docker-compose exec app bash
```

Using this shell you can access the Symfony console to create / update your
database, create users, etc.
```
bin/console doctrine:database:create
bin/console doctrine:schema:update --force
bin/console fos:user:create --super-admin
```


You'll also need to create an .env.local file with your Twitch and YouTube
credentials.  You can either copy the whole .env file and edit the lines you
want, or just put your credentials in the local file.

### Troubleshooting
If you have trouble with "Out of Memory" issues when dealing with composer or
the cache-clearing it does after install, try increasing the PHP memory limit by
using the memory_limit variable during that step.
```
docker-compose run app php -d memory_limit=-1 /usr/bin/composer install
docker-compose run app php -d memory_limit=-1 bin/console cache:clear
```

If you're getting 404s on the "bundle" items, a broken composer install may have
also stopped the assets install process.  Try running this command on the php
containers to copy bundled assets in Symfony.
```
bin/console assets:install --symlink
```

## Settings

You can reach the settings admin via the /admin/ route. You must have a logon to
continue.

## Theme Images

You can replace items on the "game" or "streamer" pages using themes. The best
size for theme images in pixels, width x height:

* Banner - 728x90 (a "leaderboard" ad)
* Custom Art - streamer profiles are 300x300, box art 225x300
* Embed Background - 700x400

## Containers and Terminology
The content of each page is highly customizable. Here are some of the items that
can be customized:

### Home Row
The front page is made up of Home Rows. A Home Row can have a Layout, which
determines how the Home Row Items within are laid out and displayed. Current
layouts include:
  * Classic - Standard rows of embeds, scrollable by clicking arrows on either side.
  * Custom Background - a row of embeds at the bottom, with the "Custom Art" uploaded for each item showing full-height behind the row.
  * Numbered - Like the Standard row, but with a set of descending numbers next to each embed on the left.

### Topics
Topics are a "query" on one of our API services. A Topic includes a service
(Twitch or YouTube) and a query term (Overwatch, Pokimane, etc.)

### Home Row Items
Each home row has "Items", which declare a topic and settings about how those
search results' embeds are displayed, such as whether to show a given number of
embeds, whether to show art from the service, whether to show the embed when the
topic is offline. An item may resolve to zero or more Containers.

### Container
The root of the app's content. A container is one piece of embedded content,
configured via the home row, the theme, etc.

### Theme
A theme allows you to add custom art for a given Topic. When a "GamersX Link" is
clicked for a topic, the page will use the given Theme artwork as frames,
banners, etc.

