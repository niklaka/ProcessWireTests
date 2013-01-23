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
	 * @dataProvider providerForFind
	 *
	 */
	public function testFindInDatabase($description, $selector, $assertions, $skipMessage) {
		$this->runMethodInDatabase('find', $description, $selector, $assertions, $skipMessage);
	}

	/**
	 * Test find() method with given selector using in-memory PageArray.
	 *
	 * @dataProvider providerForFind
	 *
	 */
	public function testFindInMemory($description, $selector, $assertions, $skipMessage) {
		$this->runMethodInMemory('find', $description, $selector, $assertions, $skipMessage);
	}

	/**
	 * Data provider for selector tests using find() method.
	 * 
	 * Each test is an item in the array, being an array itself
	 * (items of which represent arguments of the test method, see ProcessWireTestCase for details)
	 * 
	 */
	public function providerForFind() {
		return array(
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
		);
	}
}
