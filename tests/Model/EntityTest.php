<?php

namespace Zortje\MVC\Tests\Model;

use Zortje\MVC\Tests\Model\Fixture\CarEntity;

/**
 * Class EntityTest
 *
 * @package            Zortje\MVC\Tests\Model
 *
 * @coversDefaultClass Zortje\MVC\Model\Entity
 */
class EntityTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @covers ::getColumns
	 */
	public function testGetColumns() {
		$expected = [
			'id'       => 'integer',
			'make'     => 'string',
			'model'    => 'string',
			'hp'       => 'integer',
			'released' => 'Date',
			'modified' => 'DateTime',
			'created'  => 'DateTime'
		];

		$this->assertSame($expected, CarEntity::getColumns());
	}

	/**
	 * @covers ::set
	 * @covers ::get
	 */
	public function testSetAndGet() {
		$car = new CarEntity('Ford', 'Model T', 20, new \DateTime('1908-10-01'));
		$car->set('model', 'Model A');
		$car->set('hp', 65);
		$car->set('released', new \DateTime('1927-10-20'));

		$this->assertSame('Model A', $car->get('model'));
		$this->assertSame(65, $car->get('hp'));
		$this->assertEquals(new \DateTime('1927-10-20'), $car->get('released'));
	}

	/**
	 * @covers ::set
	 *
	 * @expectedException Zortje\MVC\Model\Exception\InvalidEntityPropertyException
	 * @expectedExceptionMessage Entity Zortje\MVC\Tests\Model\Fixture\CarEntity does not have a property named invalid-property
	 */
	public function testSetInvalidProperty() {
		$car = new CarEntity(null, null, null, new \DateTime());

		$car->set('invalid-property', 'value');
	}

	/**
	 * @covers ::get
	 *
	 * @expectedException Zortje\MVC\Model\Exception\InvalidEntityPropertyException
	 * @expectedExceptionMessage Entity Zortje\MVC\Tests\Model\Fixture\CarEntity does not have a property named invalid-property
	 */
	public function testGetInvalidProperty() {
		$car = new CarEntity(null, null, null, new \DateTime());

		$car->get('invalid-property');
	}

	/**
	 * @covers ::toArray
	 */
	public function testToArrayWithId() {
		$car = new CarEntity('Ford', 'Model T', 20, new \DateTime('1908-10-01'));

		$now      = new \DateTime();
		$released = new \DateTime('1908-10-01');

		$expected = [
			':id'       => null,
			':make'     => 'Ford',
			':model'    => 'Model T',
			':hp'       => 20,
			':released' => $released->format('Y-m-d'),
			':modified' => $now->format('Y-m-d H:i:s'),
			':created'  => $now->format('Y-m-d H:i:s')
		];

		$this->assertSame($expected, $car->toArray(true));
	}

	/**
	 * @covers ::toArray
	 */
	public function testToArrayWithoutId() {
		$car = new CarEntity('Ford', 'Model T', 20, new \DateTime('1908-10-01'));

		$now      = new \DateTime();
		$released = new \DateTime('1908-10-01');

		$expected = [
			':make'     => 'Ford',
			':model'    => 'Model T',
			':hp'       => 20,
			':released' => $released->format('Y-m-d'),
			':modified' => $now->format('Y-m-d H:i:s'),
			':created'  => $now->format('Y-m-d H:i:s')
		];

		$this->assertSame($expected, $car->toArray(false));
	}

	/**
	 * @covers ::validatePropertyForValue
	 */
	public function testValidatePropertyForValue() {
		$car = new CarEntity(null, null, null, new \DateTime());

		$reflector = new \ReflectionClass($car);

		$method = $reflector->getMethod('validatePropertyForValue');
		$method->setAccessible(true);

		/**
		 * Entity
		 */
		$this->assertSame(null, $method->invoke($car, 'id', null), 'ID property');
		$this->assertSame(42, $method->invoke($car, 'id', 42), 'ID property');

		$this->assertEquals(new \DateTime(), $method->invoke($car, 'modified', new \DateTime()), 'Modified property');
		$this->assertEquals(new \DateTime(), $method->invoke($car, 'created', new \DateTime()), 'Created property');

		/**
		 * CarEntity
		 */
		$this->assertSame(null, $method->invoke($car, 'make', null), 'Make property');
		$this->assertSame('Ford', $method->invoke($car, 'make', 'Ford'), 'Make property');

		$this->assertSame(null, $method->invoke($car, 'model', null), 'Model property');
		$this->assertSame('Model A', $method->invoke($car, 'model', 'Model A'), 'Model property');

		$this->assertSame(null, $method->invoke($car, 'hp', null), 'HP property');
		$this->assertSame(65, $method->invoke($car, 'hp', 65), 'HP property');

		$this->assertEquals(null, $method->invoke($car, 'released', null), 'Released  property');
		$this->assertEquals(new \DateTime('1927-10-20'), $method->invoke($car, 'released', new \DateTime('1927-10-20')), 'Released  property');
	}

	/**
	 * @covers ::validatePropertyForValue
	 *
	 * @expectedException Zortje\MVC\Model\Exception\InvalidEntityPropertyException
	 * @expectedExceptionMessage Entity Zortje\MVC\Tests\Model\Fixture\CarEntity does not have a property named invalid-property
	 */
	public function testValidatePropertyForValueInvaidProperty() {
		$car = new CarEntity(null, null, null, new \DateTime());

		$reflector = new \ReflectionClass($car);

		$method = $reflector->getMethod('validatePropertyForValue');
		$method->setAccessible(true);

		$method->invoke($car, 'invalid-property', 'value');
	}

	/**
	 * @covers ::validatePropertyForValue
	 *
	 * @expectedException Zortje\MVC\Model\Exception\InvalidValueTypeForEntityPropertyException
	 * @expectedExceptionMessage Entity Zortje\MVC\Tests\Model\Fixture\CarEntity property id is of type string and not integer
	 */
	public function testValidatePropertyForValueInvalidValue() {
		$car = new CarEntity(null, null, null, new \DateTime());

		$reflector = new \ReflectionClass($car);

		$method = $reflector->getMethod('validatePropertyForValue');
		$method->setAccessible(true);

		$method->invoke($car, 'id', 'string');
	}

}
