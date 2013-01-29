<?php

include './index.php';

/**
 *  Abstract base class for own tests.
 *
 *  Includes custom assertions and run methods for different types of tests.
 *  Arguments for run*In*() methods are as follows:
 *
 * Each test is an item in the array, being an array itself (items of which represent arguments of the test method):
 *   array(
 *     'description',
 *     'selector string',
 *     array(
 *       'assertion1' => array('argument1', ...),
 *       'assertion2' => array('argument1', ...),
 *       ...
 *     ),
 *     'skip message, if test is to be skipped'
 *   )
 *
 */
abstract class ProcessWireTestCase extends PHPUnit_Framework_TestCase {

	protected $allPages = null;

	/**
	 * Set up a known state before every test.
	 *
	 */
	protected function setUp() {
		// make sure there's nothing in the cache
		// TODO: this makes everything oh so slow - should in-mem tests be separated from db tests?
		//wire('pages')->uncacheAll();

		// populate $this->allPages to be able to run in-memory selector tests.
		// TODO: this belongs to suite-level setup (does not yet exist)
		if(is_null($this->allPages)) $this->allPages = wire('pages')->find('include=all');
	}

	/**
	 * Wrapper for db selector method calls.
	 *
	 * Includes 'include=all' in every selector given to make things match up with in-memory selectors.
	 *
	 * TODO: include=all doesn't make sense when testing for include-selectors!
	 * TODO: make it possible to run tests on single Page-objects as well
	 *
	 * @see runMethod() for parameter descriptions
	 *
	 */
	protected function runMethodInDatabase($method, $description, $selector, $assertions, $skipMessage) {
		$this->runMethod(wire('pages'), $method, 'include=all', $description, $selector, $assertions, $skipMessage);
	}

	/**
	 * Wrapper for in-memory selector method calls.
	 *
	 * @see runMethod() for parameter descriptions
	 *
	 */
	protected function runMethodInMemory($method, $description, $selector, $assertions, $skipMessage) {
		$this->runMethod($this->allPages, $method, '', $description, $selector, $assertions, $skipMessage);
	}

	/**
	 * A convenience method for running different methods of different objects with different selectors and different assertions.
	 *
	 * @param object $object Object which has the method we're about to run (Page, Pages or PageArray)
	 * @param string $method Method to run ('find', 'get', 'children', etc.)
	 * @param string $description Description of this test
	 * @param string $selector Selector to use
	 * @param array $assertions An associative array where key is name of the assertion to run
	 *                          and value is an array of parameters to pass to the assertion method.
	 * @param string $skipMessage Optional: if present, test will be skipped
	 *                            and $skipMessage is given as a description for the skip.
	 *
	 */
	protected function runMethod($object, $method, $baseSelector, $description, $selector, $assertions, $skipMessage = null) {
		// mark this test skipped if $skipMessage has been given
		if($skipMessage) $this->markTestSkipped($skipMessage);

		// add base selector if one given
		if($baseSelector) $selector = "$baseSelector, $selector";
		// run the given method for the given object with given selectors
		$results = $object->$method($selector);
		// iterate through all given assertions
		foreach($assertions as $assertionName => $assertionParams) {
			// add run-time parameters for the assertion (actual result and message)
			array_push($assertionParams, $results, $description);

			// call the assertion method
			call_user_func_array(array($this, $assertionName), $assertionParams);
		}
	}

	/**
	 * Custom assertions for running the same assertion for each of the objects.
	 * (Exactly speaking not a single assertion anymore then.)
	 * TODO: see if these could be implemented at a lower level (to make them count as a single assertion)
	 *
	 */

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

	// case in-sensitive!
	public static function assertPropertyContainsForeach($expected, $actualPropertyName, $actualArrayOfObjects, $message = '') {
		foreach($actualArrayOfObjects as $actualObject) {
			self::assertContains(strtolower($expected), strtolower($actualObject->$actualPropertyName), $message);
		}
	}

	// case in-sensitive!
	public static function assertPropertyNotContainsForeach($expected, $actualPropertyName, $actualArrayOfObjects, $message = '') {
		foreach($actualArrayOfObjects as $actualObject) {
			self::assertNotContains(strtolower($expected), strtolower($actualObject->$actualPropertyName), $message);
		}
	}
}