<?php
namespace Routers {

    class ObjectRouter {

        public $logger;
        
        public function __construct($logger = null) {
            $this->logger = $logger;
        }

        public function routeIterator(&$path) {
            //Iterating through the path, popping elements from the left as they are seen
            $last = null;
            
            while ($path) {
                yield [$last, $path[0]];
                $last = array_shift($path);
                /* This prevents having to put back a value in the event of a 
                 * readjustment in the router path.
                 * Testing indicates that it's better to do array maninpulation than it is
                 * to implement SplDoublyLinkedList for deque behavior. Likewise, 
                 * simply tracking the index is a bit slower and can add complexity 
                 * when dealing with reorients/reroutes. 
                 */
            }

        }

        public function __invoke($context, $obj, Array $path) {
            /* Bringing some variables only used in debug into scope to eliminate repeat function calls
             * and take advantage of PHP's copy on write
             */
            $debug = false;

            if (getEnv('APP_ROLE') === 'DEBUG') {
                $debug   = true;
                $request = isset($context->request) ? $context->request : null;
            }

            $previous = null;
            $current  = null;

            if (!is_object($obj) && class_exists($obj)) {
                    
                if ($debug && $this->logger) {
                    $this->logger->debug('Instantiating current class', [
                        'context' => $request,
                        'current' => $current
                    ]);
                    
                }

                $obj = new $obj($context);

            }
            
            $routeIterator = $this->routeIterator($path);

            foreach ($routeIterator as list($previous, $current)) {
                
                if (!is_object($obj)) {

                    if (class_exists($obj)) {
                        if ($debug && $this->logger) {
                            $this->logger->debug('Instantiating current class', [
                                'context' => $request,
                                'current' => $current
                            ]);
                            
                        }

                        $obj = new $obj($context);

                    } else {

                        if ($debug && $this->logger) {
                            $this->logger->debug('Refusing to descend on non-objects', [
                                'passed'  => $obj,
                                'context' => $request,
                                'current' => $current
                            ]);
                            
                        }

                        yield [$previous, $obj, true];
                        return;
                    }


                }

                /* Let's check for actual endpoints, methods are always endpoints. By making use of get_class_methods we
                 * prevent the usage of protected/private methods as they aren't populated in the array provided by 
                 * get_class_methods. This also protects against timing attacks as we operate on a safe set rather than 
                 * executing a sub-process when we encounter a part we cannot allow access to.
                 */
                if (in_array($current, get_class_methods($obj))) {
                    
                    if ($debug && $this->logger) {
                        $this->logger->debug('Found an endpoint', [
                            'request'    => $request,
                            'isEndpoint' => true,
                            'parent'     => var_export($obj, true),
                            'handler'    => $current,
                            'path'       => var_export($path, true)
                        ]);
                    }

                    // Since we found an endpoint, we'll bail early and the values should still be preserved for yielding.
                }

                /* Next up is checking for properties that we can use to either descend further into controllers or static 
                 * endpoints such as arrays, strings, etc.
                 */
                if (array_key_exists($current, get_object_vars($obj))) {
                    
                    if ($debug && $this->logger) {
                        
                        $this->logger->debug('Found a property: ' . $current, [
                            'source'  => $obj,
                            'current' => $current,
                            'value'   => var_export($obj->$current, true)
                        ]);

                    }

                    yield [$previous, $obj, false];
                    $obj = $obj->$current;
                    continue;

                } 

                if (method_exists($obj, '__get')) {
                    /* We'll check if we can emulate getattr via __get and approach it that way. */
                    if ($debug && $this->logger) {
                        
                        $this->logger->debug('Using __get() to recover: ' . $current, [
                            'source'  => $obj,
                            'current' => $current,
                            'value'   => var_export($obj->$current, true)
                        ]);

                    }

                    yield [$previous, $obj, false];
                    $obj = $obj->__get($current);
                    continue;

                }
                /* We failed to find the attribute within the object so we break out here, the details should still
                 * be preserved inside this function.
                 */
                break;
            }

            if ($routeIterator->valid()) {
                /* We bailed early for whatever reason, so obj is our last known attribute, previous is the path element
                 * matching that object, and current is the failed element.
                 */
                $isObjectMethod = is_callable(array($obj, $current));

                if ($debug && $this->logger) {
                    $this->logger->debug("Routing interrupted while attempting to resolve attribute: $current", [
                        'handler'   => var_export($obj, true),
                        'endpoint'  => $isObjectMethod,
                        'previous'  => $previous,
                        'attribute' => $current
                    ]);
                }

                if ($isObjectMethod) {
                    yield [$previous, $obj->$current($context, $path), true];
                    return;
                }

                list($previous, $current) = $routeIterator->current();
                yield [$previous, $obj, is_callable($obj)];
                return;

            }

            /* We ended normally so we try to figure out what should be returned */
            $invokable = is_callable($obj);
            
            if ($debug && $this->logger) {
                $this->logger->debug("Terminated normally", [
                    'handler'   => var_export($obj, true),
                    'endpoint'  => $invokable,
                    'previous'  => $previous,
                    'attribute' => $current
                ]);
            }

            list($previous, $current) = $routeIterator->current();
            yield [$previous, $obj, $invokable];
            return;

        }

    }

}