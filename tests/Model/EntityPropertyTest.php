<?php

namespace Zortje\MVC\Tests\Model;

use Zortje\MVC\Model\EntityProperty;

/**
 * Class EntityPropertyTest
 *
 * @package            Zortje\MVC\Tests\Model
 *
 * @coversDefaultClass Zortje\MVC\Model\EntityProperty
 */
class EntityPropertyTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @covers ::formatValueForEntity
	 */
	public function testFormatValueStringToString() {
		$property = new EntityProperty('string');

		$this->assertSame('foo', $property->formatValueForEntity('foo'));
	}

	/**
	 * @covers ::formatValueForEntity
	 */
	public function testFormatValueStringToInteger() {
		$property = new EntityProperty('integer');

		$this->assertSame(42, $property->formatValueForEntity('42'));
	}

	/**
	 * @covers ::formatValueForEntity
	 */
	public function testFormatValueStringToFloat() {
		$property = new EntityProperty('float');

		$this->assertSame(3.14159265359, $property->formatValueForEntity('3.14159265359'));
	}

	/**
	 * @covers ::formatValueForEntity
	 */
	public function testFormatValueStringToDateTime() {
		$property = new EntityProperty('DateTime');

		$this->assertEquals(new \DateTime('2015-05-03 01:15:42'), $property->formatValueForEntity('2015-05-03 01:15:42'));
	}

	/**
	 * @covers ::formatValueForEntity
	 */
	public function testFormatValueStringToDate() {
		$property = new EntityProperty('Date');

		$this->assertEquals(new \DateTime('2015-05-04'), $property->formatValueForEntity('2015-05-04'));
	}

	/**
	 * @covers ::formatValueForDatabase
	 */
	public function testFormatValueForDatabaseDateTime() {
		$property = new EntityProperty('DateTime');

		$this->assertEquals('2015-05-08 22:42:42', $property->formatValueForDatabase(new \DateTime('2015-05-08 22:42:42')));
	}

	/**
	 * @covers ::formatValueForDatabase
	 */
	public function testFormatValueForDatabaseDate() {
		$property = new EntityProperty('Date');

		$this->assertEquals('2015-05-08', $property->formatValueForDatabase(new \DateTime('2015-05-08')));
	}

	/**
	 * @covers ::__construct
	 */
	public function testConstruct() {
		$property = new EntityProperty('foo');

		$reflector = new \ReflectionClass($property);

		$tableName = $reflector->getProperty('type');
		$tableName->setAccessible(true);
		$this->assertSame('foo', $tableName->getValue($property));
	}

}
