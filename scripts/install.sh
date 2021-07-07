#! /bin/sh
cd /var/www/gamersx/codedeploy

# Copy our environment and link files
cp ../deploy/.env* .
ln -sf ../deploy/vendor .
ln -sf /var/symfony/themes public/images
ln -sf /var/symfony/items public/images
mkdir var
ln -sf /var/symfony/cache var
ln -sf /var/symfony/log var
ln -sf /var/symfony/data.db var

composer install
