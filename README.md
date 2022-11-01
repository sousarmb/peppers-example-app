# Peppers application base directory structure and files
Peppers needs a fixed set of directories and files to work properly. These files store route and service descriptors as well as other information. Below find that information as well as a brief introduction to Peppers inner workings.
## The application base
The following directory structure and files are necessary to build and run a Peppers application:
|Path|Description & Functionality|
|--|--|
|webroot||
|┣━&nbsp;app|Files related to the application/business|
|┃&nbsp;┣━&nbsp;Contracts|Interfaces used by business classes|
|┃&nbsp;┣━&nbsp;Controllers|Controller classes|
|┃&nbsp;┃&nbsp;┗━&nbsp;Peppers|Peppers default controller directory|
|┃&nbsp;┃&nbsp;&nbsp;&nbsp;&nbsp;┗━&nbsp;DefaultController.php|Default controller for routes not found (404); modify to your needs|
|┃&nbsp;┣━&nbsp;Events|Event classes|
|┃&nbsp;┃&nbsp;┣━&nbsp;BuildRoutesCacheFileEvent.php|Event class to build the routes cache file, used internally by boot strategies|
|┃&nbsp;┃&nbsp;┣━&nbsp;Handlers|Event handler classes|
|┃&nbsp;┃&nbsp;┃&nbsp;┣━&nbsp;BuildRoutesCacheFileHandler.php|Event handler class that actually builds the routes cache file|
|┃&nbsp;┃&nbsp;┃&nbsp;┗━&nbsp;LogRequestHandler.php|Event handler class that logs information in the log file|
|┃&nbsp;┃&nbsp;┗━&nbsp;LogRequestEvent.php|Event class that holds one entry to be logged|
|┃&nbsp;┣━&nbsp;Helpers|Custom helper classes|
|┃&nbsp;┣━&nbsp;Models|Model classes|
|┃&nbsp;┃&nbsp;┗━&nbsp;Repositories|Model repository classes|
|┃&nbsp;┣━&nbsp;Services|Custom service classes necessary for the business|
|┃&nbsp;┗━&nbsp;Views|View (.phtml) files; this is the root directory for all views that are used in a Peppers application|
|┃&nbsp;&nbsp;&nbsp;&nbsp;┗━&nbsp;Peppers|Holds Peppers default views|
|┃&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;┣━&nbsp;DefaultController.phtml|Default controller HTML view; modify to your needs|
|┃&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;┗━&nbsp;DefaultException.phtml|Default exception HTML view; modify to your needs|
|┣━&nbsp;config|Application configuration files|
|┃&nbsp;┣━&nbsp;credentials.php|Credentials known to Peppers|
|┃&nbsp;┣━&nbsp;datasources.php|External data sources known to Peppers|
|┃&nbsp;┣━&nbsp;eventshandlers.php|Mappings of event to handler class|
|┃&nbsp;┣━&nbsp;routes.php|Routes file|
|┃&nbsp;┣━&nbsp;services.php|Service descriptors file|
|┃&nbsp;┗━&nbsp;strategies.php|Strategies file|
|┣━&nbsp;logs|Log files|
|┣━&nbsp;private|Files used with `LocalFile` instances are stored here|
|┣━&nbsp;public|Public directory; everything Peppers related starts here|
|┃&nbsp;┣━&nbsp;.htaccess|Apache file; modify to your needs|
|┃&nbsp;┗━&nbsp;index.php|Peppers applications start here; contrary to PSR-4, the `Settings` class is defined here, all Peppers default settings are set here. Modify with care!|
|┗━&nbsp;temp|Temporary files such as the routes cache file go here|
## Configuration files and their content
Brief description of the data structures needed to boot/run a Peppers application
### credentials.php
This file holds the list of credentials that are known to the Peppers application and are needed to access external data sources. Credentials are stored in a `CredentialStore` instance (which is then stored in the `ServiceLocator` as a singleton). This file contains an associative array as follows:
```
return [
    'default' => 'mysql.peppers',
    'mysql' => [
        'peppers' => [
            'user' => 'peppers',
            'password' => 'peppers'
        ]
    ]
];
```
The `default` key sets which credentials should be used by default when none are specifically requested to the `CredentialStore` and is the only mandatory key => value pair. The default value must correspond to the name of the 1st level key concatenated with a dot (.) and then the 2nd key name. 

In the example above the 1st level key indicates that the external data source is a MySQL database. The 2nd level key indicates the database name. The key names in the example were selected just for the purpose of making an analogy, you can use whatever name you want. The 3rd level is made up of the credentials. 

Currently only user and password is supported and these keys are mandatory.
### datasources.php
This file holds the list of external data sources that are to known to the Peppers application and can be accessed through the `ConnectionManager` instance (which is then stored in the `ServiceLocator` as a singleton) . This file contains an associative array as follows:
```
return [
    'default' => 'pdo.peppers',
    'pdo' => [
        'peppers' => [
            'dsn' => 'mysql:host=172.17.0.3;port=3306;dbname=peppers;charset=utf8mb4',
        ]
    ],
];
```
The `default` key sets which data source should be used by default when none is specifically requested to the `ConnectionManager` and is the only mandatory key => value pair. The default value must correspond to the name of the 1st level key concatenated with a dot (.) and then the 2nd key name.
In the example above the 1st level key corresponds to a wrapper (unqualified) class name which must exist, used to access the data source. The 2nd level key indicates the database name. The 3rd level is the DSN, this key is mandatory for all defined data sources.

The only wrapper currently supported is `pdo`.
### eventshandlers.php
This file holds the mappings between event and event handler classes. This file contains an associative array as follows:
```
use App\Events as E;
use App\Events\Handlers as H;
return [
    E\BuildRoutesCacheFileEvent::class => [
        H\BuildRoutesCacheFileHandler::class,
    ],
    E\LogRequestEvent::class => [
        H\LogRequestHandler::class,
    ],
];
```
Above is the basic file shipped with any Peppers application. The array key is the (fully qualified) name of the class that represents the event, the value is an array of (fully qualified) class names that will receive the event for handling. Events can be launched from anywhere in the application and are processed real-time (blocking) or after the response is sent (deferred).

Events and their mappings are stored in the `EventStore` instance (which is stored in the `ServiceLocator` as a singleton). 
### routes.php
This files holds the routes mappings to controllers. Mappings come in the form of an array of `RouteRegister`. You may also use the helper class `Route`, which has more descriptive methods and the result is the same, as in the example that follows:
```
use Peppers\Helpers\Http\Route;
return [
 Route::get(
   '/example/{name}/{phone}',
   [App\Controllers\OneExampleController::class, 'nameOrSurname']
 )->setPathExpression('name', '[a-z]+')
  ->setPathExpression('phone', '\d+')
  ->setQueryExpression('whatever', 'blabla')
  ->setAllowFreeQuery(true),
 Route::post(
   '/example/person',
   [App\Controllers\AnotherController::class, 'newPerson']
 )->setAllowFreeQuery(false),
 Route::restful('/model/peppers', App\Models\Repositories\PeppersRepository::class)
];
```
Peppers routing system uses regular expressions.  In the example above, you can see 3 routes declared, using the `Route` helper class. This class's methods are the names of the allowed HTTP methods by Peppers and one special `restful()`* method. They receive:
 1. The URL path, which may have `{placeholders}` for variable values; you may also pass a query;
 2. One of `array|Closure|string`. If you send:
	 * An array, the 1st value is the (fully qualified) name of a controller class and the 2nd the name of the method to be called on it;
	 * A PHP `Closure` and use any classes inside, said classes must be declared using the fully qualified name. The Closure must always return an instance of `Response`;
	 * A string, which must be the fully qualified name of a class that implements the `ModelRepository` contract. If this the case and:
        * The route is for a GET request, to return a specific model instance set the model primary key columns as path parameters (... don't forget the correspondent regular expressions to match), otherwise a query has to be sent with parameters matching model columns so a more generic query is made against the data source and the Client gets a model collection as a response;
       * The route is for a DELETE request, the URL path must have the primary keys as path parameters, otherwise nothing happens. Peppers only allows model deletion using its primary key value(s);
       * The route is for a POST request, set the model primary key columns as path parameters (... don't forget the correspondent regular expressions to match) to update a specific model instance (... model data to update is read from the request body). If no primary key parameters are set in the request path, Peppers interprets the request to create a new model instance and read model data from the request body.

The return of the method call is an instance of `RouteRegister` which allows the setting of the path parameter and query parameter regular expressions. 

\* This method returns an instance of `RestfulRouteRegister` which expands to *n* instances of `RouteRegister` during the route resolution process, one per allowed HTTP method.
 
Going back to the example above, the 1st `RouteRegister` instance represents a HTTP GET request where the path has 2 parameters - `{name}` and `{phone}` - for which regular expressions must be set with the help of `setPathExpression()`. This request's URL must have a query as well, that query is set with `setQueryExpression()` and must correspond to `whatever=blabla`. The call to `setAllowFreeQuery()` allows the client to send more query parameters for which the developer does not have to write a regular expression. The request is handled by a `OneExampleController`, `nameOrSurname()` method.
The 2nd `RouteRegister` instance represents a HTTP POST request with no dynamic path parameters and no possibility of querying which is handled by a `AnotherController` instance, `newPerson()` method.
The 3rd `Route` instance is different, in its most basic form you only need to declare the URL path, during the route resolution process, this `RouteRegister` expands into *n* instances of itself but with added primary key columns as path parameters for DELETE, GET and POST requests as well as the simplified form.

You may add as many `Route` instances as you need. If no match happens Peppers sends a 404 back to the user agent or uses the default controller to show your custom 404 page or run some other custom business logic.
#### Last but not least...
In all handler cases, the request handling code must return an instance of `Response` or `ResponseSent`. The first case allows for proper formatting of response data, according to the request's `Accept` HTTP header. The 2nd case bypasses all of that, meaning it's the developers responsibility to do it: output buffer flushing, `header()` setting, etc.
### services.php
This files holds the services that are known to Peppers and are stored in the `ServiceLocator` instance. Service `Implementation` come in 2 forms: Abstract and Concrete.
#### Abstract
Abstract implementations allow the developer to bind an interface to the `ServiceLocator`. To resolve a service class, the developer needs to set a provider. That provider can be a `Closure` instance that returns the desired service class instance or a (fully qualified) class `string` name that points to a `Strategy` class that returns the desired service class instance. It's also possible to bind the implementation to specific classes (`array`, 2nd parameter to `abstract()` method), allowing the developer to type hint a `BoundTo` instance in a `Closure` provider and then deciding how to resolve the service class instance.
#### Concrete
Concrete implementations are the opposite of abstract ones. They don't require a provider so the developer must provide a fully qualified class name that'll be resolved by the `ServiceLocator`. This type of implementation can be loaded immediately (not lazy loaded) as opposed to abstract implementations.

Dependency injection is possible with abstract and concrete implementations. The `Factory` will inject the necessary dependencies using the `ServiceLocator` if necessary. If the dependencies have dependencies those will be resolved in a recursive manner.

The following is an example of service(s) registration for a Peppers application:
```
// use Peppers\Helpers\Http\Request\BodyParameter;
// use Peppers\Helpers\Http\Request\QueryParameter;
// use Peppers\Helpers\Http\Request\PathParameter;
use Peppers\Helpers\Service\BoundTo;
use Peppers\Helpers\Service\Implementation as Imp;
use Peppers\Services;
use Peppers\Contracts;
use App\Contracts\HelpfulHelper;

return [
 Imp::abstract(Contracts\RouteResolver::class)
  ->setProvider(Peppers\Strategy\Boot\RouteResolver::class)
  ->setIsSingleton(true),
 Imp::concrete(Services\RequestBody::class)
  ->setIsLazyLoad(true)
  ->setIsSingleton(true),
 Imp::abstract(Contracts\EventStore::class)
  ->setProvider(\Peppers\Strategy\Boot\EventStore::class)
  ->setIsSingleton(true),
 Imp::abstract(Contracts\CredentialStore::class)
  ->setProvider(Peppers\Strategy\Boot\CredentialStore::class)
  ->setIsSingleton(true),
 Imp::abstract(Contracts\ConnectionManager::class)
  ->setProvider(Peppers\Strategy\Boot\ConnectionManager::class)
  ->setIsSingleton(true),
 Imp::abstract(
  App\Contracts\HelpfulHelper::class,
   [App\Controllers\ExampleController::class]
  )->setProvider(function (BoundTo $caller): HelpfulHelper {
   return $caller->getName() == App\Controllers\ExampleController::class 
    ? new App\Helpers\AnotherHelpfulHelper() 
    : new App\Helpers\OneHelpfulHelper();
  })
];
```
In the example above 6 services are described:
1. The `RouteResolver`; it's an abstract implementation so the developer can switch if it chooses to build a different one (watch out for the contracts!). Because it's abstract there's a provider runs custom logic and return an instance `RouteResolver`. It's also set as a singleton so whenever the developer call the `ServiceLocator` the same instance is returned. This is a core service of Peppers.
2. The `RequestBody`; it's a representation of the request body, set as a singleton and lazy loaded so it's instantiated only when needed (which happens "automagically" between the `Factory` and the `ServiceLocator`). This is a core service of Peppers.
*Note*: This service deals with request body encoding so the developer doesn't have to. It's not meant to be used directly. If the developer needs to access body data an instance of `BodyParameter` should be used.
3. The `EventStore`; acts as the repository to deferred events and dispatches events to their registered handlers. This is a core service of Peppers (`Kernel` logs information with it).
4. The `CredentialStore`; credentials stored in the credentials file are stored here. It's used by the `ConnectionManager` service. This is a core service of Peppers.
5. The `ConnectionManager`; acts as the repository for access data to external data sources as well as storing the actual connections. This is a core service of Peppers.
6. This is an example of binding a interface to the `ServiceLocator` with additional and binding to a specific class. When resolving the service type hint a `BoundTo $caller` so the caller class name is available inside the `Closure` resolving the service instance class. In this case if the caller is `App\Controllers\ExampleController::class` a `App\Helpers\AnotherHelpfulHelper` instance is injected/returned; else `App\Helpers\OneHelpfulHelper`.
#### Last but not least...
Services are classes the developer designs to its needs, there are no specific requirements set by Peppers. If the developer needs the service to run some business logic after the response is sent to the client, implement a `shutdown()` method. It'll be called in the end. Please be aware if this code requires some other service, said service may no longer be available!

Provider classes have a single requirement: they must extend `Strategy` base class. `Strategy` based classes allow failure, this means that if the provider fails to do its business - returns something other than expected - and is allowed to fail, the `ServiceLocator` returns an instance of `StrategyFail` to the caller; if not a `StrategyFail` exception is thrown, which will have to be caught somewhere, eventually by the `Kernel` and that stops all request processing (Peppers shutdown will still happen as normal if out of the boot phase).
### strategies.php
This file holds the mapping of `Kernel` stages to code supposed to run at each stage. The following is an example for a standard Peppers application:
```
use Peppers\Strategy;
use Peppers\Strategy\Boot;
use Peppers\Strategy\Response;
return [
    'boot' => [/* these classes are NOT run in a pipeline */
        Boot\ServiceLocator::class,
        Boot\SessionStart::class
    ],
    'requestResponse' => [/* these classes are run in a pipeline */
        Strategy\RouteToHandler::class,
        Response\Html::class,
        Response\Json::class,
        Response\Redirect::class,
        Response\NoBody::class,
        Response\File::class,
        Response\PlainText::class,
        Response\Xml::class
    ],
    'exceptionHandling' => [/* these classes are run in a pipeline */
        Strategy\ResolveException::class,
        Response\Html::class,
        Response\Json::class,
        Response\PlainText::class,
        Response\Xml::class
    ],
    'shutdown' => [/* these classes are NOT run in a pipeline */
        Strategy\ShutdownServices::class,
    ]
];
```
In the example above the keys:
- `boot`; code necessary to boot the framework. These classes are not run in a pipeline so the `Kernel` provides no input, just checks if the return is as expected or the strategy failed. Any failure here stops request processing completely!
- `requestResponse`; code necessary to process the request and send a response back to the client. These classes are run in a pipeline, the 1st receives an instance of the `RouteRegister` (resolved by the `RouteResolver`) as input, from then on, the next receive the previous call return as input. Classes set here must implement `PipelineStage` contract;
- `ExceptionHandling`; code that tries to handle uncaught exceptions and provide meaningful information on it to the client. This code is "environment aware" meaning: if the application is in production mode very little information is shown in the response, else it shows all the exception information. These classes are run in a pipeline, the 1st receives an instance of the uncaught exception as input, from then on, the next receive the previous call return as input. Classes set here must implement `PipelineStage` contract;
- `shutdown`; code necessary to shutdown the framework: trigger deferred event handling and calling `shutdown()` method on services registered in the `ServiceLocator`. These classes are not run in a pipeline so the `Kernel` provides no input. If any of these strategies fail nothing is shown to the client as Peppers is completely "output silent" at this point, it just gets logged in the Kernel panic file.

*Note 1*: The keys should not be modified, they are hard-coded in the `Kernel`.
*Note 2*: Any uncaught exception gets logged in the kernel panic file. Check `Settings` class in `index.php` for its location.
## Comments and sugestions
Send to peppers.php.framework@gmail.com

Thank you :)
