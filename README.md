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
* Art Replacement - 300x400
* Embed Background - 700x400
