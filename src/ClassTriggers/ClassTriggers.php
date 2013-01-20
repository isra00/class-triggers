<?php

namespace ClassTriggers;

class ClassTriggers
{

	//EXECUTION CONDITIONS
	const COND_STOP_EXECUTION = 'ClassTriggers_condition_stop_execution';
	const NO_MORE_ACTIONS = 'ClassTriggers_run_directly_method_not_more_bindings_on_this_chain';

	protected $validTriggers = array('preMethod', 'postMethod');

	//Target metadata
	protected $target;
	protected $targetClass;
	protected $targetMethods = array();

	protected $actions = array();

 	/**
 	 * Constructs a trigger-able object based on any object
 	 *
 	 * @param	object			$target	Original object
 	 * @return	ClassTriggers	The ClassTriggers instance!
 	 */
	public function __construct($target)
	{
		$this->target = $target;

		//Store target metadata for further checks
		$this->targetClass = get_class($target);
		$this->targetMethods = get_class_methods($this->targetClass);

		$this->actions = array();
	}


	/**
	 * Catch calls to non-existing methods. This way we can run the actions and
	 * the original methods from the target object
	 *
	 * @param	string	$requestedMethod	The name of the requested method.
	 * @param	array	$arguments		Arguments passed in the call.
	 * @return	mixed	The returned value of the actual methods, optionally
	 *			manipulated by postMethod actions.
	 *
	 * @see http://php.net/manual/language.oop5.overloading.php
	 *
	 * @todo Implementar la obligatoriedad de parámetros. Para saber los
	 * 		 parámetros del método target, usar reflexión
	 */
	public function __call($requestedMethod, $arguments)
	{
		$willExecuteMethod = true;
		$returnValue = null;

		if (!in_array($requestedMethod, $this->targetMethods))
		{
			throw new Exception("Method $requestedMethod does not exist in class " . $this->targetClass);
		}

		//Run preMethod actions
		if (isset($this->actions[$requestedMethod]['preMethod']))
		{
			foreach ($this->actions[$requestedMethod]['preMethod'] as $closure)
			{
				$bindingReturnValue = $closure($arguments);
				if (self::COND_STOP_EXECUTION == $bindingReturnValue)
				{
					$willExecuteMethod = false;
				}

				//No more actions will be executed
				if (self::NO_MORE_ACTIONS == $bindingReturnValue)
				{
					break;
				}
			}
		}

		//Run the actual method in the target
		if ($willExecuteMethod)
		{
			$returnValue = call_user_func_array(array($this->target, $requestedMethod), $arguments);
		}

		//Run postMethod actions
		if (isset($this->actions[$requestedMethod]['postMethod']))
		{
			foreach ($this->actions[$requestedMethod]['postMethod'] as $closure)
			{
				$bindingReturnValue = $closure($arguments, $returnValue);

				//No more actions will be executed
				if (self::NO_MORE_ACTIONS == $bindingReturnValue)
				{
					break;
				}
			}
		}

		return $returnValue;
	}

	/**
	 * Binds an action to a method trigger
	 *
	 * @param string	$method		A method of the target class
	 * @param string	$event		'preMethod' or 'postMethod'
	 * @param callable	$closure	The action to be executed. Will receive two params:
	 *					$arguments (arguments in the method call) and
	 *					$returnValue (returned value from the original method,
	 *					only available in postMethod actions).
	 */
	public function bind($method, $event, callable $closure)
	{
		if (!in_array($method, $this->targetMethods))
		{
			throw new Exception("Method $method does not exist in target object");
		}

		if (!in_array($event, $this->validTriggers))
		{
			throw new Exception("Event $event does not exist");
		}

		if (!isset($this->actions[$method]))
		{
			$this->actions[$method] = array();
		}

		if (!isset($this->actions[$method][$event]))
		{
			$this->actions[$method][$event] = array();
		}

		//Bind the closure to the $target scope so it can acccess $this object.
		$closure = \Closure::bind($closure, $this->target, $this->targetClass);

		$this->actions[$method][$event][] = $closure;
	}
}
