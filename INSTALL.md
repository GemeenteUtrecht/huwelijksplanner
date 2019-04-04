# INSTALLATION

This repository is optimized for docker and includes a docker-compose file, the preferred way of getting a local instance up and running would therefore be docker or docker desktop. You can get Docker Desktop for windows [here](https://docs.docker.com/docker-for-windows/) (or [here](https://docs.docker.com/docker-for-mac/) for mac users). 

Now we just need a local copy of the code base, you can just download it [here](https://github.com/GemeenteUtrecht/trouwen/archive/master.zip) but the preferred way would be trough a git client (like [Source tree](https://www.sourcetreeapp.com/) or [Git Kraken](https://www.gitkraken.com/). Or if you prefer command line you can create a new folder and gitclone the project into it (you might want to replace my_branche with your name):

```CLI
$ git clone https://github.com/GemeenteUtrecht/trouwen.git -b my_branche
```

After that we only need to spin up the local containers, you can skip the pull but that would force a local build and take considerably longer.

```CLI
$ docker-compose pull # Download the latest versions of the pre-built images
$ docker-compose up -d # Running in detached mode
```

## Using this component in your application

### Including the container
Normally speaking we assume that that your application runs in one or more docker containers. In that case you can easily add this component by including it in your docker compose as follows (replace db with the DB container that you are running). Once the container is running in your network you can start your integration (see Using the container as a rest service).

```YAML
  # The actual application container
  trouwen_component:
    # In production, you may want to use a managed database service
    image: huwelijksplanner/trouwen-php:latest
    environment:
      - MYSQL_ROOT_PASSWORD=${MYSQL_ROOT_PASSWORD}
      - MYSQL_DATABASE=${MYSQL_DATABASE}
      - MYSQL_USER=${MYSQL_USER} 
      - MYSQL_PASSWORD=${MYSQL_PASSWORD}
      - DOCTRINE_DATABASE_PREFIX=trouwen_
      - ENV=dev
    depends_on:
      - db
```      

Don't forget to inlcude the comoponent as a dependency on the container(s) that need to use it.

```YAML
    depends_on:
      - trouwen_component
```      

#### Setting the environment to production
The component is set to development by default, if you want to use the component on production do not forget to set ENV=dev to ENV=prod

#### Using the container as a rest service
We provide a test implementation on trouwen.zaakonline.nl that can be used as a rest service to test your application. Or alternatively you can expose your own hosted containers to the internet as a rest service (do we do not recommend that). You can either build your own connection to the rest service or you can use the libary that is provided by Conduction.   

#### Accesing the container trough NLX
The test implementation is not currently available on nlx

### Manual installement of the container (on prod enveriments)
When you first fire up this container (or when you switch to a newer version) you want to make sure that the database that you are using is in order. Fortunately the container ships with doctrine making the whole process of updating the database rather clean and simple. Simply run the following command (this is done automatically when the env is not set to prod).

```CLI
$ docker-compose exec trouwen_component php bin/console doctrine:schema:update --force
``` 

### Autmatic installement of the container (on not prod environment )
There are a couple of commands run if the environment is not set to production 
```CLI
# Clearing the container cash, in al its variants

$ docker-compose exec trouwen_component php bin/console cache:clear --no-warmup 
$ docker-compose exec trouwen_component php bin/console doctrine:cache:clear-metadata 
$ docker-compose exec trouwen_component php bin/console doctrine:cache:clear-query
$ docker-compose exec trouwen_component php bin/console doctrine:cache:clear-result

# Updating the database to match the latest objects
$ docker-compose exec trouwen_component php bin/console doctrine:schema:update --force --no-interaction

# Loading example data
$ docker-compose exec trouwen_component php bin/console doctrine:fixtures:load --no-interaction 
``` 
Then the documantation is ALWAYS updated (production or not)
```CLI
$ docker-compose exec trouwen_component php bin/console api:swagger:export --output=/srv/api/public/schema/openapi.yaml --yaml --spec-version=3
``` 
