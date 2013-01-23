<?php

require_once __DIR__ . '/../ProcessWireTestCase.php';

/**
 * Tests for ProcessWire selectors with contains operators:
 *   *=   Contains the exact word or phrase
 *   ~=   Contains all the words
 *   ^=   Contains the exact word or phrase at the beginning of the field
 *   $=   Contains the exact word or phrase at the end of the field
 *   %=   Contains the exact word or phrase (using slower SQL LIKE)
 *   %^=  Contains the exact word or phrase at the beginning of the field (using slower SQL LIKE)
 *   %$=  Contains the exact word or phrase at the end of the field (using slower SQL LIKE)
 *   and negation of each of the above ("!field OP value")
 *
 */
class ContainsOperatorsTest extends ProcessWireTestCase
{
	/**
	 * Test find() method with given selector using database queries.
	 *
	 * @dataProvider provider
	 *
	 */
	public function testFindInDatabase($description, $selector, $assertionName, $assertionParams, $skipMessage = '') {
		if($skipMessage) $this->markTestSkipped($skipMessage);

		$results = wire('pages')->find("include=all, $selector");
		array_push($assertionParams, $results, $description);

		call_user_func_array(array($this, $assertionName), $assertionParams);
	}

	/**
	 * Test find() method with given selector using in-memory PageArray.
	 *
	 * @dataProvider provider
	 *
	 */
	public function testFindInMemory($description, $selector, $assertionName, $assertionParams, $skipMessage = '') {
		if($skipMessage) $this->markTestSkipped($skipMessage);

		$allPages = wire('pages')->find('include=all');

		$results = $allPages->find($selector);
		array_push($assertionParams, $results, $description);

		call_user_func_array(array($this, $assertionName), $assertionParams);
	}

	/**
	 * Data provider for selector tests.
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
	 *     'skip message if test is to be skipped'
	 *   )
	 *
	 * The array is flattened to contain tests with only one assertion to get them all executed.
	 *
	 */
	public function provider() {
		return $this->_flatten(array(
			array('Exact word or phrase (SQL LIKE), native field',
				'name%=peachtree',
				array(
					'assertCount' => array(5),
					'assertPropertyContainsForeach' => array('peachtree', 'name')
				)
			),

			// TODO: same for custom field

			array('Negated exact word or phrase (SQL LIKE), native field',
				'!name%=peachtree',
				array(
					'assertCount' => array(1549),
					'assertPropertyNotContainsForeach' => array('peachtree', 'name')
				)
			),
		));
	}
}
