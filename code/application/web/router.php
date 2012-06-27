<?php
/**
 * @package     WebService.Application
 * @subpackage  Application
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

/**
 * Web Service application router.
 *
 * @package     WebService.Application
 * @subpackage  Application
 * @since       1.0
 */
class WebServiceApplicationWebRouter extends JApplicationWebRouterRest
{
	/**
	 * @var    string  The api content type to use for messaging.
	 * @since  1.0
	 */
	protected $apiType = 'json';

	/**
	 * @var    integer  The api revision number.
	 * @since  1.0
	 */
	protected $apiVersion = 'v1';

	/**
	 * @var    array  The URL => controller map for routing requests.
	 * @since  1.0
	 */
	protected $routeMap = array(
		'#([\w\/]*)/(\d+)/(\w+)#i' => '$3'
	);

	/**
	 * @var    array  The possible actions
	 * @since  1.0
	 */
	protected $actions = array('like', 'count', 'hit');

	/**
	 * Find and execute the appropriate controller based on a given route.
	 *
	 * @param   string  $route  The route string for which to find and execute a controller.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 * @throws  InvalidArgumentException
	 * @throws  RuntimeException
	 *
	 */
	public function execute($route)
	{
		// Allow poor clients to make advanced requests
		$this->setMethodInPostRequest(true);

		// Make route to match our API structure
		$route = $this->reorderRoute($route);

		// Move actions from route to input
		$route = $this->actionRoute($route);

		// Parse route to get only the main
		$route = $this->rewriteRoute($route);

		// Set controller prefix
		$this->setControllerPrefix('WebServiceController' . ucfirst($this->apiVersion) . ucfirst($this->apiType));

		// Get the controller name based on the route patterns and requested route.
		$name = $this->parseRoute($route);

		$type = $name;

		// Get the effective route after matching the controller
		$route = $this->removeControllerFromRoute($route);

		// Set the remainder of the route path in the input object as a local route.
		$this->input->get->set('@route', $route);

		// Append the HTTP method based suffix.
		$name .= $this->fetchControllerSuffix();

		// Get the controller object by name.
		$controller = $this->fetchController($name, $type);

		// Execute the controller.
		$controller->execute();
	}

	/**
	 * Rewrite routes to be compatible with the application's controller layout.
	 *
	 * @param   string  $input  Route string to rewrite.
	 *
	 * @return  string
	 *
	 * @since   1.0
	 */
	protected function reorderRoute($input)
	{
		// Get the patterns and replacement fields from the route map.
		$pattern = array_keys($this->routeMap);
		$replace = array_values($this->routeMap);

		// Replace the route
		$output = preg_replace($pattern, $replace, $input);

		// If there are changes in the route, make the changes in the input
		foreach ($this->routeMap as $pattern => $replace)
		{
			// /collection1/id/collection2 becames /collection2?collection1=id
			if (preg_match($pattern, $input, $matches))
			{
				$this->input->get->set($matches[1], $matches[2]);
			}
		}

		return $output;
	}

	/**
	 * Move actions from route to input and change route
	 *
	 * @param   string  $input  Route string to review
	 *
	 * @return  string
	 *
	 * @since   1.0
	 */
	protected function actionRoute($input)
	{
		$parts = explode('/', trim($input, ' /'));

		foreach ($this->actions as $key => $action)
		{
			if(strcmp($parts[count($parts)-1], $action) === 0)
			{
				// Set action in input
				$this->input->get->set('action', $action);

				// Remove action from route
				unset($parts[count($parts)-1]);

				// Rebuild route
				$route = implode('/', $parts);

				// Return new route
				return $route;
			}
		}

		return $input;
	}

	/**
	 * Get the effective route after matching the controller by removing the controller name
	 *
	 * @param   string  $route  The route string which to parse
	 *
	 * @return  string
	 *
	 * @since   1.0
	 */
	protected function removeControllerFromRoute($route)
	{
		// Explode route
		$parts = explode('/', trim($route, ' /'));

		// Remove the first part of the route
		unset($parts[0]);

		// Reindex the array
		$parts = array_values($parts);

		// Build route back
		$route = implode('/', $parts);

		return $route;
	}

	/**
	 * Gets from the current route the api version and the output format
	 *
	 * @param   string  $route  The route string which to parse
	 *
	 * @return  string
	 *
	 * @since   1.0
	 */
	protected function rewriteRoute($route)
	{
		// Get the path from the route
		$uri = JUri::getInstance($route);

		// Explode path in multiple parts
		$parts = explode('/', trim($uri->getPath(), ' /'));

		// Get version
		if (preg_match('/^v\d$/', $parts[0]))
		{
			$this->apiVersion = $parts[0];
			unset($parts[0]);
			$parts = array_values($parts);
		}

		// Check if there is a json request
		if (preg_match('/(\.json)$/', $parts[count($parts) - 1]))
		{
			$this->apiType = 'json';
			$parts[count($parts) - 1] = str_replace('.json', '', $parts[count($parts) - 1]);
		}

		// Check if there is a xml request
		if (preg_match('/(\.xml)$/', $parts[count($parts) - 1]))
		{
			$this->apiType = 'xml';
			$parts[count($parts) - 1] = str_replace('.xml', '', $parts[count($parts) - 1]);
		}

		// Build route back
		$route = implode('/', $parts);

		return $route;
	}

	/**
	 * Get a JController object for a given name.
	 *
	 * @param   string  $name  The controller name (excluding prefix) for which to fetch and instance.
	 * @param   string  $type  The type of the content
	 *
	 * @return  JController
	 *
	 * @since   12.3
	 * @throws  RuntimeException
	 *
	 * @codeCoverageIgnore
	 */
	protected function fetchController($name, $type)
	{
		// Derive the controller class name.
		$class = $this->controllerPrefix . ucfirst($name);

		// If the controller class does not exist panic.
		if (!class_exists($class) || !is_subclass_of($class, 'JController'))
		{
			throw new RuntimeException(sprintf('Unable to locate controller `%s`.', $class), 404);
		}

		// Instantiate the controller.
		$controller = new $class($type, $this->input, $this->app);

		return $controller;
	}
}
