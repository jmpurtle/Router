======================
Magnus Object Router
======================

    © 2015 - 2016 John Mark Purtle and contributors.

..

    https://github.com/jmpurtle/Router

Introduction
============

Routing is the process of taking some starting point and a path, then resolving the object that path refers to as a handler. This process is common to almost every web application framework (transforming URLs into controllers), RPC system, and even filesystem shell. Other terms for this process include: "traversal", "dispatch", or "lookup".

Object router is simply a flavor of the routing process that attempts to resolve path elements as a chain of object attributes. This is contrary to the typical routing process involving the use of regex matching in PHP web frameworks. The main cost of this regex matching is the O(n) worst-case performance, in some cases the router continues to seek for more specific routes resulting in every single route being evaluated at least once. This can get particularly nasty in the case of issuing a 404. Certain router implementations will attempt to coerce this process in something resembling a tree for performance gains at great cost of readability. With Object routing, the best AND worst case scenario is O(depth). If a 404 is to be issued, it can terminate on the first object evaluated. 

This router is based on a 'dispatch protocol <https://github.com/marrow/Webcore/wiki/Dispatch-Protocol' and is not intended for direct use but rather as part of a framework. This does not mean that router cannot be used by itself.

Installation
============

Currently, You must copy the file into your project, preferably within vendors/Magnus/Router/ and then modify the autoloader as needed.

Composer installation is planned for the future.

Usage
=====

This section is split into parts to cover integration and the interactions this router provides to users.

Setup
=====

Autoloading
-----------

To start, one must include the autoloader that will allow you to resolve both the router itself and the controllers.

```
require_once 'vendor/autoload.php';

$router = new \Magnus\Router\Object\ObjectRouter();
```

There are several other parts to the setup you may want to create.

Environment
-----------

The first is an environment variable by the name of APP_ROLE. This is used by the router to issue debugging statements and may be used in any other Magnus components for that purpose. If you wish to enable debug mode, simply set the value of this environment variable to DEBUG.

```
putenv('APP_ROLE=DEBUG')
```

At this time, to disable debug, just change the environment variable to something else. Certain values like PRODUCTION may be used in the future to enable certain optimizations, project functionalities or features such as preprocessing, opcode caching or fingers-crossed logging.

Logging
-------

Logging, is a fairly important part of operating a framework as it gives you insight into where and why things might not be going right. Done well, it will allow you to replay requests from start to finish so you don't need to squeeze those details out of your users. In this routing component, logging is for understanding how the path resolves to the final object. Which brings us to our next subject...

Request Processing
------------------

The router only cares about the request as far as the path and any contextual information it introduces. Framework consumers using this router should provide an array of strings to the router. How the request itself gets parsed into that array is not a concern. This deliberate obliviousness is for a reason. Suppose you have a multi-tenanted application and one operates in a web context, another operates in a CLI context. This approach allows the router to serve all requests without any special considerations as long as the ultimate input is an array of strings. '/foo/bar/baz', 'foo\bar\baz', 'foo.bar.baz', etc are all provided to the router as ['foo', 'bar'. 'baz']. If you need to provide closures, functions, etc as part of the path, you should be creating a different version of this router based on the specific use case and the 'Dispatch Protocol <https://github.com/marrow/Webcore/wiki/Dispatch-Protocol' to guide the design.

Routing
-------

In constructing the router, you have the option to pass in an external instance of a logger for use. If one is not provided to the router then debug mode logging will automatically be disabled for the router itself.

The router invocation requires three parameters, a context object, a base object or object name (fully qualified), and the path in the form of an array of strings.

The context object is passed to any object instantiated by the router itself. This context object may be null or an object containing contextual information that any routable object may need for its function.

The base object or object name is what the router begins its descent on. This is typically some sort of Root object, however, during routing, an object may reroute the request on a different object. If you are passing in a string as the second parameter then this must be a fully qualified object reference. E.g. '\\Controller\\ControllerName'.

As mentioned in Request Processing, the third parameter must be an array of strings. The values will be used to discover additional routable objects via properties, endpoints via object methods, or parameterized objects via __get($value);


The router is a generator that returns a tuple of [$previous, $obj, $isEndpoint]. $previous is the last processed chunk of the path provided to the router at the time of yield. $obj is the current object under consideration. The first $obj will be your base object for example. $isEndpoint is a boolean that will evaluate as true if an object method has been discovered from the path you provided. Framework consumers should take this as a cue to break out and begin the dispatch step.

The Object Router makes use of routable objects with the basic structure of:

class RoutableObjectName {
    
    public function __invoke($args = []) {
        return [];
    }

}

Routable objects should return an array or a generator so they can be transformed into an appropriate view by your template engine regardless of the actual engine used. Returning simple data structures or iterables allows you to apply conditional formatting on the response as well such as checking if the request is an AJAX request so the request will be responded with JSON for use on the client side.

To connect to other routable objects, include an object reference or object as a property with the name of a path element. For example, given a root object and the path of '/foo/bar/baz', the root object contains a property $foo. The Foo object contains a property of $bar. The Bar object may have a property of $baz or a method baz.

If the path chunk refers to a non-object (method, function, or a static value that does not resolve to an object) then routing will terminate at that point.

class RoutableObjectName {
    
    public $objectReference   = '\\Routable\\RoutableChildObjectName';
    public $staticValue       = 'foo';
    public $functionReference = 'date';
    public $closureReference  = function () { //do the thing }

    public function __invoke($args = []) {
        return [];
    }

}

With a non-null context object, you may use the constructor to modify the context as you see fit. A common use would be to increment the access control level requirements as you descend therefore the level of authority is kept minimal.


"But what about if I have protected or private properties/methods? Won't PHP throw errors if you try to access those?" Good question. Fear not, object router makes use of get_class_methods and get_object_vars to discover attributes. This means anything marked as protected or private, object router knows nothing about it as far as the process goes. It may as well not exist to router. This offers a nice side effect of not revealing any information for a timing attack nor requiring naming conventions such as prefixing underscores to the name.

Caching
-------
That's fine and all, but what about caching? Surely we could precompile all of the potential routes and use a hash map for O(1) routing lookups. Yeah, it's possible, the question becomes, "Do you really want to do that?". Consider the application in advanced stages where there are dynamic components, path elements and route reorients. You'd need to have a special toolchain to generate those routes and then a route explorer to properly handle the dynamic components. Not to mention all of the extra code to test, lookups, etc. Anything but an O(1) cache is ineffective compared to this.

You certainly could implement a cache with appropriate callback/hook points and extensions but the goal here is to provide a router that could be used as part of a minimum viable product release. Buying you enough time to evaluate whether or not the caching of this particular process does provide a benefit.

Future updates
==============

Test coverage as close to 100% as possible.

Travis integration for automatic building.

License
=======

Object Router has been released under the MIT Open Source license.

The MIT License
---------------

Copyright © 2015-2016 John Mark Purtle and contributors.

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
