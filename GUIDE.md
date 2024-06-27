# Instructions

## Steps to run:

1. `docker compose build`
2. `docker compose run worker composer install --no-scripts`
3. `docker compose exec -T mysql mariadb -ugamersx -psecret gamersx < PATH_TO_INIT_DB. Alternative:  docker compose exec -T mysql mariadb -ugamersx -psecret gamersx --host=mysql < PATH_TO_INIT_DB`
4. `docker compose run worker php bin/console doctrine:schema:update --force`
5. `docker compose run worker composer clear-cache`
6. `docker compose run vue yarn install && docker compose up -d`
7. `docker compose run vue yarn build`
8. `docker compose run worker php bin/console ca:cl`
9. `docker compose exec -T mysql mariadb -ugamersx -psecret gamersx < PATH_TO_INIT_DB. Alternative:  docker compose exec -T mysql mariadb -ugamersx -psecret gamersx --host=mysql < PATH_TO_DATA_DUMP`
10. `docker compose run worker php bin/console doctrine:schema:update --force`
11. `docker compose exec php bin/console app:cache-home-page-containers`