<?php

require_once __DIR__ . '/../ProcessWireTestCase.php';

/**
 * Tests for ProcessWire selectors with subfields.
 *
 * Focus on covering all the different aspects of using subfields in selectors.
 *
 */
class SubfieldTest extends ProcessWireTestCase
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
			array('Parent, native subfield',
				'parent.name=cities',
				array(
					'assertCount' => array(70),
					'assertPropertyEqualsForeach' => array(4049, 'parent_id')
				)
			),
		);
	}
}
