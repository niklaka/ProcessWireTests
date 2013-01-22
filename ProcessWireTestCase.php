<?php

include './index.php';

/**
 *  Abstract base class for own tests.
 *
 *	Custom assertions for running the same assertion for each of the objects.
 *	(Exactly speaking not a single assertion anymore then.)
 *
 */
abstract class ProcessWireTestCase extends PHPUnit_Framework_TestCase {
	public static function assertPropertyEqualsForeach($expected, $actualPropertyName, $actualArrayOfObjects, $message = '') {
		foreach($actualArrayOfObjects as $actualObject) {
			self::assertEquals($expected, $actualObject->$actualPropertyName, $message);
		}
	}

	public static function assertPropertyNotEqualsForeach($expected, $actualPropertyName, $actualArrayOfObjects, $message = '') {
		foreach($actualArrayOfObjects as $actualObject) {
			self::assertNotEquals($expected, $actualObject->$actualPropertyName, $message);
		}
	}

	public static function assertPropertyLessThanForeach($expected, $actualPropertyName, $actualArrayOfObjects, $message = '') {
		foreach($actualArrayOfObjects as $actualObject) {
			self::assertLessThan($expected, $actualObject->$actualPropertyName, $message);
		}
	}

	public static function assertPropertyLessThanOrEqualForeach($expected, $actualPropertyName, $actualArrayOfObjects, $message = '') {
		foreach($actualArrayOfObjects as $actualObject) {
			self::assertLessThanOrEqual($expected, $actualObject->$actualPropertyName, $message);
		}
	}

	public static function assertPropertyGreaterThanForeach($expected, $actualPropertyName, $actualArrayOfObjects, $message = '') {
		foreach($actualArrayOfObjects as $actualObject) {
			self::assertGreaterThan($expected, $actualObject->$actualPropertyName, $message);
		}
	}

	public static function assertPropertyGreaterThanOrEqualForeach($expected, $actualPropertyName, $actualArrayOfObjects, $message = '') {
		foreach($actualArrayOfObjects as $actualObject) {
			self::assertGreaterThanOrEqual($expected, $actualObject->$actualPropertyName, $message);
		}
	}

	/**
	 * Helper function to mangle test array resulting in one assertion per test.
	 *
	 */
	protected function _flatten($tests) {
		$flatTests = array();
		foreach($tests as $test) {
			foreach($test[2] as $assertionName => $assertionArgumentArray) {
				// message, selector, assertion name, assertion args, skip message
				array_push($flatTests, array($test[0], $test[1], $assertionName, $assertionArgumentArray, $test[3]));
			}
		}
		return $flatTests;
	}
}