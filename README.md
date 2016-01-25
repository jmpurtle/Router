======================
Magnus Object Router
======================

    © 2016 John Mark Purtle and contributors.

..

    https://github.com/jmpurtle/Router

Introduction
============

Routing is the process of taking some starting point and a path, then resolving the object that path refers to as a handler. This process is common to almost every web application framework (transforming URLs into controllers), RPC system, and even filesystem shell. Other terms for this process include: "traversal", "dispatch", or "lookup".

Object router is simply a flavor of the routing process that attempts to resolve path elements as a chain of object attributes. This is contrary to the typical routing process involving the use of regex matching in PHP web frameworks. The main cost of this regex matching is the O(n) worst-case performance, in some cases the router continues to seek for more specific routes resulting in every single route being evaluated at least once. This can get particularly nasty in the case of issuing a 404. Certain router implementations will attempt to coerce this process in something resembling a tree for performance gains at great cost of readability. With Object routing, the best AND worst case scenario is O(depth). If a 404 is to be issued, it can terminate on the first object evaluated. 

This router is based on a 'dispatch protocol <https://github.com/marrow/Webcore/wiki/Dispatch-Protocol' and is not intended for direct use but rather as part of a framework. This does not mean that router cannot be used by itself.

Installation
============

Currently, You must copy the file into your project, preferably within vendors/Magnus/Router/ and then modify the autoloader as needed. Alternatively, install the magnus/router package via composer. You will also need to create a context object similar to the one provided below.

Usage
=====

This section is split into parts to cover integration and the interactions this router provides to users.

Framework Use
-------------

To start, one must include the autoloader that will allow you to resolve both the router itself and the controllers.

```
require_once 'vendor/autoload.php';

$router = new \Magnus\Router\Object\ObjectRouter();
```

Now, the router's invocation requires two parameters, a context object and the base object or object name to start routing from. The context object is where you provide your path, logging, controller prefixes, and anything else that may be necessary to properly instantiate objects.

The bare minimum of a context object would be

```

namespace Magnus\Request;

class Context {
    protected $requestURI;
    protected $baseURI;
    protected $requestPath;
    protected $appMode;
    protected $logger;
    protected $controllerPrefix;

    public function __construct(Array $config) {
        $this->documentRoot     = isset($config['documentRoot']) 
                                ? $this->normalizeURI($config['documentRoot']) 
                                : $this->normalizeURI($_SERVER['DOCUMENT_ROOT']);

        $this->requestURI       = isset($config['requestURI']) 
                                ? $this->normalizeURI($config['requestURI'])
                                : '/';

        $this->baseURI          = isset($config['baseURI']) 
                                ? str_replace($this->documentRoot, '', $this->normalizeURI($config['baseURI']))
                                : '/';

        $this->assetRoot        = isset($config['assetRoot'])
                                ? $this->normalizeURI($config['assetRoot'])
                                : rtrim($this->baseURI, '/') . '/src/assets';

        $this->appMode          = isset($config['appMode']) 
                                ? $config['appMode'] 
                                : 'DEVELOPMENT';

        $this->logger           = isset($config['logger']) 
                                ? $config['logger'] 
                                : null;

        $this->controllerPrefix = isset($config['controllerPrefix']) 
                                ? $config['controllerPrefix'] 
                                : 'Magnus\\Controllers\\';
        
        $this->requestPath = explode('/', $this->requestURI);
        
        if (end($this->requestPath) === '') {
            array_pop($this->requestPath);
        }

        if ($this->requestPath[0] === '') {
            array_shift($this->requestPath);
        }

    }

    public function normalizeURI($uri) {
        return strtolower(str_replace('\\', '/', $uri));
    }

    public function getRequestURI() {
        return $this->requestURI;
    }

    public function getAssetRoot() {
        return $this->assetRoot;
    }

    public function setRequestURI($uri) {
        $this->requestURI = $this->normalizeURI($uri);
    }

    public function getBaseURI() {
        return $this->baseURI;
    }
    public function setBaseURI($uri) {
        $this->baseURI = $this->normalizeURI($uri);
    }

    public function getRequestPath() {
        return $this->requestPath;
    }

    public function getAppMode() {
        return $this->appMode;
    }

    public function getLogger() {
        return $this->logger;
    }

    public function getControllerPrefix() {
        return $this->controllerPrefix;
    }

}
```

The context object handles the normalization and splitting of the path into an array for use in the routing process. The exact implementation of the normalization process is entirely up to you. If null is passed as context to the router then it will attempt to invoke the root object otherwise issue a 404.

The base for the second parameter can either be a resolvable class name such as 'RootController' given `$context->getControllerPrefix() = 'Magnus\Controllers\'` or it can be an instantiated object. This grants you the flexibility to switch between routers with a "meta-router" or router middleware. For example, if a resource cannot be found through Object Route, a router event can be emitted, switching the router to a different type that can correctly resolve the request.

To begin the routing process:

```
foreach ($router($context, $root) as $signal) {
    list($object, $chunk, $path, $isEndpoint) = $signal;
    if ($isEndpoint) {
        //Your chosen handling process for obtaining the server response data from $object
    }
}
```

It is recommended that you wrap the routing process in a try/catch/finally block so you can handle the rendering process appropriately in cases of exceptions such as the \Magnus\Exceptions\HTTPNotFound.

The recommended response from your controllers is an array containing data so they can be transformed into JSON or into views by your template engine.

Routable Objects
--------------------

Every routable object requires at the very least, `an __invoke($args = [])`. A very basic example:

```
namespace Magnus\Controllers;
class RootController {

    public function __invoke($args = []) {
        return [];
    }
}
```

For further descent, add a property with either a string or an object to the controller:

```
namespace Magnus\Controllers;
class RootController {

    public $user = 'UsersController';

    public function __invoke($args = []) {
        return [];
    }
}
```

If you require some initial setup such as instantiating the object for the property or setting access control levels, do so in the constructor of the controller.

```
namespace Magnus\Controllers;
class RootController {

    public $user;

    public function __construct($context) {
        $this->user = new UsersController();
    }

    public function __invoke($args = []) {
        return [];
    }
}
```

"But what about if I have protected or private properties/methods? Won't PHP throw errors if you try to access those?" Good question. Fear not, object router makes use of get_class_methods and get_object_vars to discover attributes. This means anything marked as protected or private, object router knows nothing about it as far as the process goes. It may as well not exist to router. This offers a nice side effect of not revealing any information for a timing attack nor requiring naming conventions such as prefixing underscores to the name.

The first thing router will attempt to do is instantiate the current object in question (your base object or root controller) if it is not already an object.

The next step is to check for potential endpoints. Endpoints are methods defined in your controller and if one is discovered then router will end there and yield the endpoint along with remaining path elements for you to handle.

If no methods matching the current path element are found then router will attempt to find a property matching that name and proceed with the next router step.

If neither of those applies, (no attributes found or if the path element is numeric) then router will check for the existence of a 'lookup' method and pass any remaining path elements and context to it. An example callable with a lookup:

```
namespace Magnus\Controllers;
class UsersController {

    public function __invoke($args = []) {
        return [];
    }

    public function lookup($path, $context) {
        $userID = $path[0];
        $userProfile = new UserController($userID);
        return [$userProfile, [$path[0]]];
    }
}
```

The lookup method must return a current object and the consumed elements in an array. Router will then remove the number minus one of consumed elements from the beginning of the remaining path. We do not remove the final consumed element because it naturally gets popped off at the end of the iteration by the routeIterator and this prevents us from overconsuming a path element. For example:

$chunk = 27; $path = [27, 'modify']; will become $chunk = '/27'; $path = [27, 'modify'];

$chunk = 'projects'; $path = ['projects', 'subdomain', 'user', '27' 'modify']; will become $chunk = '/projects/subdomain'; $path = ['subdomain', 'user', '27' 'modify'];

If an exception is issued from the lookup then a \Magnus\Exceptions\HTTPNotFound exception will be thrown.

If the previous checks failed to apply then a \Magnus\Exceptions\HTTPNotFound exception will be thrown.

Once there are no more path elements to consume then the dispatcher will attempt to instantiate the current class if not already so. Then it will check to see if the current object can be called as a function `__invoke($args)` and yield that on success. Otherwise check if the parent object can be called and yield that.

Future updates
==============
Removing the coupling between the router and the context object/logging mechanism. Ideally, context would be its own package and the router should be less dependent on exact functions provided in the context object.

Test coverage as close to 100% as possible.

Travis integration for automatic building.

License
=======

Object Router has been released under the MIT Open Source license.

The MIT License
---------------

Copyright © 2015 John Mark Purtle and contributors.

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated
documentation files (the “Software”), to deal in the Software without restriction, including without limitation the
rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit
persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the
Software.

THE SOFTWARE IS PROVIDED “AS IS”, WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE
WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NON-INFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
