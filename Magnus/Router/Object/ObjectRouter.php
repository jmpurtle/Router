<?php
namespace Magnus\Router\Object;

class ObjectRouter {

    /* This version of object router makes use of yield syntax which is only available from PHP 5.5 onwards.
     * If support for lower level versions are needed, another version of this routerer can be written to eliminate yield syntax.
     */
    
    public function routeIterator(&$path) {
        while (!empty($path)) {
            yield $path[0];
            array_shift($path);
            /* This prevents having to put back a value in the event of a 
             * readjustment in the router path.
             * Testing indicates that it's better to do array maninpulation than it is
             * to implement SplDoublyLinkedList for deque behavior. Likewise, 
             * simply tracking the index is a bit slower and can add complexity 
             * when dealing with reorients/rerouteres. 
             */
        }
    }

    public function __invoke($context, $root) {
        $log              = (!empty($context)) ? $context->getLogger() : null;
        $path             = (!empty($context)) ? $context->getRequestPath() : [];
        $last             = '';
        $parent           = null;
        $current          = $root;
        $controllerPrefix = (!empty($context)) ? $context->getControllerPrefix() : '';
        
        if (!empty($context) && $context->getAppMode() === 'DEBUG' && $log !== null) {
            $log->addDebug('Starting Object router', [
                'request' => (!empty($context)) ? $context->getRequestURI() : '/',
                'path'    => var_export($path, true),
                'root'    => var_export($root, true)
            ]);
        }

        foreach ($this->routeIterator($path) as $chunk) {
            if (!empty($context) && $context->getAppMode() === 'DEBUG' && $log !== null) {
                $log->addDebug('Beginning router step.', [
                    'chunk'   => $chunk,
                    'path'    => var_export($path, true),
                    'current' => var_export($current, true)
                ]);
            }

            if (!is_object($current) && class_exists($controllerPrefix . $current)) {
                if (!empty($context) && $context->getAppMode() === 'DEBUG' && $log !== null) {
                    $log->addDebug('Instantiating current class', [
                        'request' => (!empty($context)) ? $context->getRequestURI() : '/',
                        'current' => $current
                    ]);
                }
                
                $resolvedClass = $controllerPrefix . $current;
                $current       = new $resolvedClass($context);
            }

            if (is_object($current)) {
                $parent = $current;
            }

            if (!is_numeric($chunk)) {
                if (array_key_exists($chunk, get_class_methods($parent))) {
                    if (!empty($context) && $context->getAppMode() === 'DEBUG' && $log !== null) {
                        $log->addDebug('Found an endpoint', [
                            'request'    => (!empty($context)) ? $context->getRequestURI() : '/',
                            'isEndpoint' => true,
                            'parent'     => var_export($parent, true),
                            'handler'    => $chunk,
                            'arguments'  => var_export($path, true)
                        ]);
                    }

                    yield array($parent, $chunk, $path, true);

                } elseif (array_key_exists($chunk, get_object_vars($parent))) {
                    if (!empty($context) && $context->getAppMode() === 'DEBUG' && $log !== null) {
                        $log->addDebug('Found a property', [
                            'request'    => (!empty($context)) ? $context->getRequestURI() : '/',
                            'property'   => $chunk,
                            'parent'     => var_export($parent, true)
                        ]);
                    }

                    $current = $parent->$chunk;

                }

            } elseif (method_exists($parent, 'lookup')) {
                try {
                    list($current, $consumed) = $parent->lookup($path, $context);
                    $chunk = implode('/', $consumed);
                    array_splice($path, 0, count($consumed) - 1);
                } catch (Exception $e) {
                    throw new \Magnus\Exceptions\HTTPNotFound();
                }

            } else {
                $log->addDebug('Nothing found', [
                    'object properties' => get_object_vars($parent),
                    'object methods'    => get_class_methods($parent),
                    'parent'            => var_export($parent, true)
                ]);
                throw new \Magnus\Exceptions\HTTPNotFound();
            }

            yield array($parent, $chunk, explode('/', $last), false);
            $last = $last . '/' . $chunk;

        }

        if (!empty($context) && $context->getAppMode() === 'DEBUG' && $log !== null) {
            $log->addDebug('No endpoint found', [
                'request' => (!empty($context)) ? $context->getRequestURI() : '/',
                'current' => var_export($current, true),
                'parent'  => var_export($parent, true)
            ]);
        }

        if (!is_object($current) && class_exists($controllerPrefix . $current)) {
            $resolvedClass = $controllerPrefix . $current;
            $current       = new $resolvedClass($context);
        }

        if (is_callable($current)) {
            $log->addDebug('Calling current.', [
                'request' => (!empty($context)) ? $context->getRequestURI() : '/',
                'current' => var_export($current, true)
            ]);
            yield array($current, '', $path, true);
        } elseif (is_callable($parent)) {
            $log->addDebug('Calling parent.', [
                'request' => (!empty($context)) ? $context->getRequestURI() : '/',
                'parent'  => var_export($parent, true)
            ]);
            yield array($parent, '', $path, true);
        }

    }

}
