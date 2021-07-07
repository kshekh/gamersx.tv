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

