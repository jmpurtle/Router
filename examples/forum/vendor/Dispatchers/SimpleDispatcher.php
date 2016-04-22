<?php
namespace Dispatchers {

	class SimpleDispatcher {

		public function __invoke($previous, $obj, $context = null) {

			if (!is_object($obj)) { 

				if (class_exists($obj)) { $obj = new $obj($context); } 

			}

			if (is_callable($obj)) { return $obj($context); }

			if (is_callable(array($obj, $previous))) { return $obj->$previous($context); }

			

			return $obj;
		}

	}

}