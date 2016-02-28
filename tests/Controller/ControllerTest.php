<?php

namespace Zortje\MVC\Tests\Controller;

use Zortje\MVC\Controller\ControllerFactory;
use Zortje\MVC\Controller\Exception\ControllerActionNonexistentException;
use Zortje\MVC\Controller\Exception\ControllerActionPrivateInsufficientAuthenticationException;
use Zortje\MVC\Controller\Exception\ControllerActionProtectedInsufficientAuthenticationException;
use Zortje\MVC\Tests\Controller\Fixture\CarsController;
use Zortje\MVC\User\User;

/**
 * Class ControllerTest
 *
 * @package            Zortje\MVC\Tests\Controller
 *
 * @coversDefaultClass Zortje\MVC\Controller\Controller
 */
class ControllerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \PDO
     */
    private $pdo;

    public function setUp()
    {
        $this->pdo = new \PDO("mysql:host=127.0.0.1;dbname=tests", 'root', '');
    }

    /**
     * @covers ::__construct
     */
    public function testConstruct()
    {
        $user = new User('', '');

        $controllerFactory = new ControllerFactory($this->pdo, '/var/www/html/', $user);

        $controller = $controllerFactory->create(CarsController::class);

        $reflector = new \ReflectionClass($controller);

        $pdoProperty = $reflector->getProperty('pdo');
        $pdoProperty->setAccessible(true);
        $this->assertSame($this->pdo, $pdoProperty->getValue($controller));

        $appPathProperty = $reflector->getProperty('appPath');
        $appPathProperty->setAccessible(true);
        $this->assertSame('/var/www/html/', $appPathProperty->getValue($controller));

        $userProperty = $reflector->getProperty('user');
        $userProperty->setAccessible(true);
        $this->assertSame($user, $userProperty->getValue($controller));
    }

    /**
     * @covers ::getShortName
     */
    public function testGetShortName()
    {
        $controllerFactory = new ControllerFactory($this->pdo, '', null);

        $controller = $controllerFactory->create(CarsController::class);

        $this->assertSame('Cars', $controller->getShortName());
    }

    /**
     * @covers ::setAction
     */
    public function testSetAction()
    {
        $controllerFactory = new ControllerFactory($this->pdo, '', null);

        $carsController = $controllerFactory->create(CarsController::class);
        $carsController->setAction('index');

        $reflector = new \ReflectionClass($carsController);

        $property = $reflector->getProperty('action');
        $property->setAccessible(true);
        $this->assertSame('index', $property->getValue($carsController));
    }

    /**
     * @covers ::setAction
     */
    public function testSetActionNonexistent()
    {
        $this->expectException(ControllerActionNonexistentException::class);
        $this->expectExceptionMessage('Controller Zortje\MVC\Tests\Controller\Fixture\CarsController action nonexistent is nonexistent');

        $controllerFactory = new ControllerFactory($this->pdo, '', null);

        $carsController = $controllerFactory->create(CarsController::class);
        $carsController->setAction('nonexistent');
    }

    /**
     * @covers ::setAction
     */
    public function testSetActionUnauthenticatedProtected()
    {
        $this->expectException(ControllerActionProtectedInsufficientAuthenticationException::class);
        $this->expectExceptionMessage('Controller Zortje\MVC\Tests\Controller\Fixture\CarsController protected action hidden requires authentication');

        $controllerFactory = new ControllerFactory($this->pdo, '', null);

        $carsController = $controllerFactory->create(CarsController::class);
        $carsController->setAction('hidden');
    }

    /**
     * @covers ::setAction
     */
    public function testSetActionUnauthenticatedPrivate()
    {
        $this->expectException(ControllerActionPrivateInsufficientAuthenticationException::class);
        $this->expectExceptionMessage('Controller Zortje\MVC\Tests\Controller\Fixture\CarsController private action add requires authentication');

        $controllerFactory = new ControllerFactory($this->pdo, '', null);

        $carsController = $controllerFactory->create(CarsController::class);
        $carsController->setAction('add');
    }

    // @todo test ::callAction

    // @todo test ::beforeAction

    // @todo test ::afterAction

    // @todo test ::set

    /**
     * @covers ::getLayoutTemplate
     */
    public function testGetLayoutTemplate()
    {
        $controllerFactory = new ControllerFactory($this->pdo, '/var/www/html/', null);

        $carsController = $controllerFactory->create(CarsController::class);

        $reflector = new \ReflectionClass($carsController);

        $method = $reflector->getMethod('getLayoutTemplate');
        $method->setAccessible(true);

        $this->assertSame('/var/www/html/../tests/View/Layout/Fixture/auto.layout', $method->invoke($carsController));
    }

    /**
     * @covers ::getViewTemplate
     */
    public function testGetViewTemplate()
    {
        $controllerFactory = new ControllerFactory($this->pdo, '', null);

        $carsController = $controllerFactory->create(CarsController::class);
        $carsController->setAction('index');

        $reflector = new \ReflectionClass($carsController);

        $action = $reflector->getProperty('view');
        $action->setAccessible(true);
        $action->setValue($carsController, '../tests/View/Cars/Fixture/index');

        $method = $reflector->getMethod('getViewTemplate');
        $method->setAccessible(true);

        $this->assertSame('../tests/View/Cars/Fixture/index.view', $method->invoke($carsController));
    }

    /**
     * @covers ::getViewTemplate
     */
    public function testGetViewTemplateViewNotSet()
    {
        $controllerFactory = new ControllerFactory($this->pdo, '/src/', null);

        $carsController = $controllerFactory->create(CarsController::class);
        $carsController->setAction('index');

        $reflector = new \ReflectionClass($carsController);

        $method = $reflector->getMethod('getViewTemplate');
        $method->setAccessible(true);

        $this->assertSame('/src/View/Cars/index.view', $method->invoke($carsController));
    }

    /**
     * @param int    $code
     * @param string $responseCode
     *
     * @covers ::setResponseCode
     *
     * @dataProvider setResponseCodeProvider
     */
    public function testSetResponseCode(int $code, string $responseCode)
    {
        $controllerFactory = new ControllerFactory($this->pdo, '', null);

        $carsController = $controllerFactory->create(CarsController::class);

        $reflector = new \ReflectionClass($carsController);

        /**
         * Set headers property to an empty array
         */
        $property = $reflector->getProperty('headers');
        $property->setAccessible(true);

        $property->setValue($carsController, []);

        /**
         * Set response
         */
        $method = $reflector->getMethod('setResponseCode');
        $method->setAccessible(true);

        $method->invoke($carsController, $code);

        /**
         * Assert headers value
         */
        $expected = [
            'response_code' => $responseCode
        ];

        $this->assertSame($expected, $property->getValue($carsController));
    }

    /**
     * Provides test data for testSetResponseCode
     * @return array
     */
    public function setResponseCodeProvider(): array
    {
        return [
            [200, 'HTTP/1.1 200 OK'],
            [403, 'HTTP/1.1 403 Forbidden'],
            [404, 'HTTP/1.1 404 Not Found'],
            [500, 'HTTP/1.1 500 Internal Server Error']
        ];
    }

    /**
     * @covers ::setResponseCode
     */
    public function testSetResponseCodeInvalid()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('HTTP status \'42\' is not implemented');

        $controllerFactory = new ControllerFactory($this->pdo, '', null);

        $carsController = $controllerFactory->create(CarsController::class);

        $reflector = new \ReflectionClass($carsController);

        /**
         * Set response
         */
        $method = $reflector->getMethod('setResponseCode');
        $method->setAccessible(true);

        $method->invoke($carsController, 42);
    }
}
