##Development
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

##Settings
The settings file is located in var/row_settings.json. It's validated according
to the JSON schema in schema.json.  There's a .dist file included in the root
directory to use as a starting point.
