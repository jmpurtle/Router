<?php
namespace Dispatchers {

	class SimpleDispatcher {

		public function __invoke($previous, $obj) {

			if (is_callable($obj)) { return [$obj()]; }

			if (is_callable(array($obj, $previous))) { return [$obj->$previous]; }

			if (!is_object($obj)) { 

				if (class_exists($obj)) { $obj = new $obj; } 
				return [var_export($obj, true)]; 

			}

			return [$obj];
		}

	}

}