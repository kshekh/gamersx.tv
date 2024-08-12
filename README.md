## Development

Using docker-compose, you can run containers for the app, vue, and the web server.

Build the images to install necessary packages before running
```
docker-compose build
```

Install PHP packages using the composer installation in the worker container
```
docker-compose run worker composer install
```

Install packages so the image can run the yarn server
```
docker-compose run vue yarn install
```

Migrate DB Dump
```
docker-compose exec -T mysql mysql -ugamersx -psecret gamersx < PATH_TO_DUMP
```

Update Schema
```
docker-compose run worker php bin/console doctrine:schema:update --force
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

## Deployment

The app is deployed using Amazon CodeDeploy via a BitBucket Pipeline. There are
three environments on the server - "prod", "demo", and "dev".  Each is deployed
to the EC2 instance by using codedeploy to push the latest code, and the
install.sh script in the repo to copy to the right directory and link permanent
files like .env files and the vendor and images directories.  Each BitBucket
Deployment has a DEPLOYMENT_GROUP variable that matches up to an AWS Deployment
Group.

Every push to main is deployed to the dev server automatically.  To push to the
demo and prod environments, tag a release with a version number, e.g.
'release-v1.0'.

If you don't want to deploy to dev automatically, include [skip ci] in your
commit's message.

### Automated Temporary Environment Pipeline
When pull requests with the Staging branch are created, the pipeline creates a temporary environment based off of the merging branch for QA. Once that pull request is merged or declined, the temporary environment is destroyed.

#### Pull Request Open
- This action is configured in the bitbucket-pipelines.yml. it's built in to the bitbucket pipelines configuration.
- The pipeline installs Terraform and downloads the code that describes our development resources in AWS.
- The pipeline then uses Terraform to launch a temporary QA environment in the cloud. It uses the pull request id number as a naming convention. For example, if your pull request is number 59, an environment built from your merging branch will be automatically launched to a subdomian at `pr-59.gamersx.tv`

#### Pull Request Close
- Pull request close actions cannot be configured to directly trigger a pipeline, but we can use BitBucket Manage Webhooks to get around this.
- BitBucket Manage Webhook actions are configured to send a payload to a webhook we manage in Zapier. This payload includes the pull request id
- Zapier then authenticates with BitBucket API to get a temporary token.
- Once the Zapier zap has a token it uses the pipelines endpoint in the BitBucket API to trigger a custom pipeline to destroy the temporary environment
- The destroy pipeline is a custom pipeline that is configured along with the create pipeline. It just needs to be triggered manually because BitBucket Pipelines do not support pull request close actions. But we can trigger one manually with BitBucket API
- Once the custom destroy pipeline is triggered with the `BITBUCKET_PR_ID` variable manually set from the API request, it uses the same Terraform code to destroy the environment.

ToDo:
1. Find a way to stop the destroy pipeline from running until the create pipeline is complete. Since it takes around 5 minutes for the pipeline to finish, if someone were to create and merge a pull request in a hurry it could cause weird, undesirable effects when Terraform tries to destroy infrastructure while it's being created.
2. This Zapier webhook is not secured. Anyone who knows the id of a pull request could use Postman to trigger an unwanted environment destruction. It doesn't leak the token and it doesn't create anything. And it doesn't destroy anything other than exisiting development environments. It's just worth noting that if we decide to use this to do more sophisticated and complex things, we want to pay close attention to security.


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

