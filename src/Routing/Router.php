<?php

namespace Zortje\MVC\Routing;

use Zortje\MVC\Routing\Exception\MissingRouteException;
use Zortje\MVC\Routing\Exception\RouteAlreadyConnectedException;

/**
 * Class Router
 *
 * @package Zortje\MVC\Routing
 */
class Router {

	/**
	 * @var array Routes
	 */
	private $routes = [];

	/**
	 * Connects a new route in the router
	 *
	 * @param string $route      Route
	 * @param string $controller Controller
	 * @param string $action     Action
	 *
	 * @throws RouteAlreadyConnectedException When route is already connected
	 */
	public function connect($route, $controller, $action) {
		if (isset($this->routes[$route]) === true) {
			throw new RouteAlreadyConnectedException([$route]);
		}

		$this->routes[$route] = [
			'controller' => $controller,
			'action'     => $action
		];
	}

	/**
	 * Route to get controller and action
	 *
	 * @param string $route Route
	 *
	 * @return array Controller and action
	 *
	 * @throws MissingRouteException When route is not connected
	 */
	public function route($route) {
		if (isset($this->routes[$route]) === false) {
			throw new MissingRouteException([$route]);
		}

		$result = $this->routes[$route];

		return $result;
	}

}
