# alterations
The design philosophy of the conduction framework is objects first. In the concepts op making alterations to the Trouwplanner application that means that for normal CRUD api calls we would expect to either add a object or alter an object. The rest of the application should then follow the new object.

## Adding or altering an object
Objects are called entities in Symfony slang and stored in the [api/src/Entity](https://github.com/ConductionNL/zaakonline/tree/master/api/src/Entity) folder. A typical Symfony object would look something like this:


The main things to note here are *@ApiResource* which is an annotation telling the API Platform that this entity should be available as a CRUD endpoint and the *@ORM\Entity*  annotation which tells doctrine that this entity should be persisted to a database. Read more about APP Platform annotations [here](https://api-platform.com/docs/core/getting-started/) and more about doctrine annotations [here](https://symfony.com/doc/current/doctrine.html), it also doesn't hurt to take a look at Symfony validation constraints [here](https://symfony.com/doc/current/validation.html#constraints).

As long as all the object properties are public we don't actually need getters and setters, wich makes for quick development but is an inherent security risk.

We rely on API Platform to turn this object into an CRUD API endpoint and in Doctrine to map it to an database.

Before publishing our enity to the API (wish is done by the @ApiResource at the top), we might want to check is the enity is in good order.
That we can do with a neat litle doctrine tool that checks if al entities and their relatations are in order. Just run the follwing command and check the responce

``` CLI
$ docker-compose exec sauron_php  php bin/console doctrine:schema:validate
```

Even if all the entities check out where proberly going to get an messege that the database is out of date, that of cource becoude we hevnt updated it yet. We can do so with the follwoing command

``` CLI
$ docker-compose exec sauron_php php bin/console doctrine:schema:update --force
```

if we encounter error's (becouse of working on a old dev infiroment we might want to drop the database, and the reload the fixures (example data for dev purposes)

``` CLI
$ docker-compose exec php php bin/console doctrine:schema:drop --force
```

Loading fixtures
If we ever deside that we need to roll the data base back to its first form with example fixtures (for example becouse you rebuild the database) we can do so eassaly with the load fixtures command.

``` CLI
$ docker-compose exec php php bin/console doctrine:fixtures:load
```

Now everything should check out, so we can continue to the last two steps of our changes to the api, first we need to clear the cash so user wont be oconfronted with old code. 

```
$ docker-compose exec php php bin/console cache:clear --no-warmup --env=prod
$ docker-compose exec php php bin/console doctrine:cache:clear-metadata 
$ docker-compose exec php php bin/console doctrine:cache:clear-query  
$ docker-compose exec php php bin/console doctrine:cache:clear-result
```


Then we need to inform the users about al our cool new options by regenereting our redoc documunantions, furtunatly api-platform does this for us so we only need to make an singele command call

```
$ docker-compose exec sauron_php php bin/console api:swagger:export --output=/srv/api/public/schema/openapi.yaml --yaml --spec-version=3
```

Which will render the open API version 3 specs to a specific yaml file located in the public scheme folder (which is actually a NLX spec but we will use it here for now) so that they can be picked up by our redoc template. 

You also might consider updating te postman collection, to help developers find thier way in the api

### Adding bussnes logic

### Using composer

```
$ docker-compose exec php composer require sumup/sumup-ecom-php-sdk
```

## Dashboard
API Platform Admin is a tool to automatically create a fancy (Material Design) and fully featured administration interface for any API supporting the [Hydra Core Vocabulary](http://www.hydra-cg.com/), including but not limited to all APIs created using the [API Platform framework](https://api-platform.com/).

The generated administration is a 100% standalone Single-Page-Application with no coupling to the server part, according to the API-first paradigm.

API Platform Admin parses the Hydra documentation then uses the awesome [React Admin](https://marmelab.com/react-admin/) library (and [React](https://facebook.github.io/react/)) to expose a nice, responsive, management interface (Create-Retrieve-Update-Delete) for all available resources.

The standard zaakonline installation comes with a pre-configured version of the Admin dashboard in its own container, the source of which is synced to docker to the admin/ folder of your installation and accessible trough [https://localhost:444/](https://localhost:444/).

The nature of the React Admin systems mean that you don’t actually have to update the dashboard when new functionality is added (as far as [CRUD] https://en.wikipedia.org/wiki/Create,_read,_update_and_delete) is concerned, it simply reads the hydra docs (see API / Redoc specs about updating those). You do however have to add specific pages for specific end points, containing business logic and of course a bit of additional styling would not hurt ;)


## Frontend
Trouwplanner also has an awesome client generator able to scaffold fully working React/Redux and [Vue.js](https://vuejs.org/) Progressive Web Apps that you can easily tune and customize. The generator also supports [React Native](https://facebook.github.io/react-native/) if you prefer to leverage all capabilities of mobile devices.

How does this work? We ship the Trouwplanner installation with an standard React frontend in the client container whenever we update our code we can automatically create actions, components, reducers an routes. You can do so with the following command


```
$ docker-compose exec client generate-api-platform-client
```

Docker syncs the client container with the client folder in our installation, so if we open that up and look in src/actions we can now see that appropriate actions have been added to our scaffolding. 

To get all these controllers and routing working on our site we will still need register the reducers and the routes in the client/src/index.js file:

``` js
import React from 'react';
import ReactDOM from 'react-dom';
import { createStore, combineReducers, applyMiddleware } from 'redux';
import { Provider } from 'react-redux';
import thunk from 'redux-thunk';
import { reducer as form } from 'redux-form';
import { Route, Switch } from 'react-router-dom';
import createBrowserHistory from 'history/createBrowserHistory';
import {
  ConnectedRouter,
  connectRouter,
  routerMiddleware
} from 'connected-react-router';
import 'bootstrap/dist/css/bootstrap.css';
import 'font-awesome/css/font-awesome.css';
import * as serviceWorker from './serviceWorker';

// Replace "book" with the name of the resource type
import yourentity from './reducers/yourentity/';
import yourentityRoutes from './routes/yourentity';

const history = createBrowserHistory();
const store = createStore(
  combineReducers({
    router: connectRouter(history),
    form,
    user,
    yourentity
    /* Replace yourentity with the name of the resource type */
  }),
  applyMiddleware(routerMiddleware(history), thunk)
);

ReactDOM.render(
  <Provider store={store}>
    <ConnectedRouter history={history}>
      <Switch>
      	{ userRoutes }  	
        {yourentityRoutes}
        /* Replace yourentity with the name of the resource type and/or add routes. Dont forget to place them between {} and this isn't a list so no , necessary  */
        <Route render={() => <h1>Not Found</h1>} />
      </Switch>
    </ConnectedRouter>
  </Provider>,
  document.getElementById('root')
);

// If you want your app to work offline and load faster, you can change
// unregister() to register() below. Note this comes with some pitfalls.
// Learn more about service workers: http://bit.ly/CRA-PWA
serviceWorker.unregister();
```

The code has been generated, and is ready to be executed! 

Go to [https://localhost/user/](https://localhost/user/) to start using your app. That's all!

###Read more about autogenerating scaffolding her:

- [React] (https://api-platform.com/docs/client-generator/react/)
- [React Native](https://api-platform.com/docs/client-generator/react-native/)
- [Vue](https://api-platform.com/docs/client-generator/vuejs/)

## Adding business logic


*Back to main:* [go back](https://github.com/ConductionNL/zaakonline)
