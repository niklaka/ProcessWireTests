<?php

include "./index.php";

/**
 * Tests for ProcessWire selectors with basic operators.
 *
 */
class BasicOperatorsTest extends PHPUnit_Framework_TestCase
{
	/**
	 * Test given selector using database queries.
	 *
	 * @dataProvider provider
	 *
	 */
	public function testSelectorInDatabase($description, $selector, $assertionName, $assertionParams, $skipMessage = '') {
		if($skipMessage) $this->markTestSkipped($skipMessage);

		$results = wire('pages')->find("include=all, $selector");
		array_push($assertionParams, $results, $description);

		call_user_func_array(array($this, $assertionName), $assertionParams);
	}

	/**
	 * Test given selector using in-memory PageArray.
	 *
	 * @dataProvider provider
	 *
	 */
	public function testSelectorInMemory($description, $selector, $assertionName, $assertionParams, $skipMessage = '') {
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
			array('Equal to, native field',
				'template=city',
				array(
					'assertCount' => array(70),
					'assertPropertyEqualsForeach' => array('city', 'template')
				)
			),
			array('Equal to, custom field',
				'title=Albuquerque',
				array(
					'assertCount' => array(1),
					'assertPropertyEqualsForeach' => array('Albuquerque', 'title')
				)
			),
			array('Equal to, custom field',
				'template=skyscraper, floors=0',
				array(
					'assertCount' => array(57),
					'assertPropertyEqualsForeach' => array('skyscraper', 'template')
				)
			),
			array('Not equal to, native field',
				'template=city, name!=albuquerque',
				array(
					'assertCount' => array(69),
					'assertPropertyEqualsForeach' => array('city', 'template'),
					'assertPropertyNotEqualsForeach' => array('albuquerque', 'name')
				)
			),
			array('Not equal to, custom field',
				'template=city, title!=Albuquerque',
				array(
					'assertCount' => array(69),
					'assertPropertyEqualsForeach' => array('city', 'template'),
					'assertPropertyNotEqualsForeach' => array('Albuquerque', 'title')
				)
			),
			array('Negated equal to, native field',
				'template=city, !name=albuquerque',
				array(
					'assertCount' => array(69),
					'assertPropertyEqualsForeach' => array('city', 'template'),
					// in-memory: 'albuquerqueue' does not match '<string:albuquerque>'
					//'assertPropertyNotEqualsForeach' => array('albuquerque', 'name')
				)
			),
			array('Negated equal to, custom field',
				'template=city, !title=Albuquerque',
				array(
					'assertCount' => array(69),
					'assertPropertyEqualsForeach' => array('city', 'template'),
					// in-memory: 'Albuquerqueue' does not match '<string:Albuquerque>'
					//'assertPropertyNotEqualsForeach' => array('Albuquerque', 'title')
				)
			),
			array('Not equal to, custom field',
				'template=skyscraper, floors!=0',
				array(
					'assertCount' => array(1175),
					'assertPropertyEqualsForeach' => array('skyscraper', 'template')
				)
			),
			array('Less than, native field',
				'parent_id<4111',
				array(
					'assertCount' => array(114),
					'assertPropertyLessThanForeach' => array(4111, 'parent_id')
				)
			),
			array('Less than, custom field',
				'template=skyscraper, floors<5',
				array(
					'assertCount' => array(60),
					'assertPropertyLessThanForeach' => array(5, 'floors'),
					'assertPropertyEqualsForeach' => array('skyscraper', 'template')
				)
			),
			array('Less than or equal, native field',
				'parent_id<=4111',
				array(
					'assertCount' => array(323),
					'assertPropertyLessThanOrEqualForeach' => array(4111, 'parent_id'),
				)
			),
			array('Less than or equal, custom field',
				'template=skyscraper, floors<=5',
				array(
					'assertCount' => array(65),
					'assertPropertyLessThanOrEqualForeach' => array(5, 'floors'),
					'assertPropertyEqualsForeach' => array('skyscraper', 'template')
				)
			),
			array('Greater than, native field',
				'parent_id>4111',
				array(
					'assertCount' => array(1231),
					'assertPropertyGreaterThanForeach' => array(4111, 'parent_id')
				)
			),
			array('Greater than, custom field',
				'template=skyscraper, floors>15',
				array(
					'assertCount' => array(1033),
					'assertPropertyGreaterThanForeach' => array(15, 'floors'),
					'assertPropertyEqualsForeach' => array('skyscraper', 'template')
				)
			),
			array('Greater than or equal, native field',
				'parent_id>=4111',
				array(
					'assertCount' => array(1440),
					'assertPropertyGreaterThanOrEqualForeach' => array(4111, 'parent_id'),
				)
			),
			array('Greater than or equal, custom field',
				'template=skyscraper, floors>=15',
				array(
					'assertCount' => array(1051),
					'assertPropertyGreaterThanOrEqualForeach' => array(15, 'floors'),
					'assertPropertyEqualsForeach' => array('skyscraper', 'template')
				)
			),
		));
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

	/**
	 *	Custom assertions for running the same assertion for each of the objects.
	 *	(Exactly speaking not a single assertion anymore.)
	 *
	 */
	public function assertPropertyEqualsForeach($expected, $actualPropertyName, $actualArrayOfObjects, $message = '') {
		foreach($actualArrayOfObjects as $actualObject) {
			$this->assertEquals($expected, $actualObject->$actualPropertyName, $message);
		}
	}

	public function assertPropertyNotEqualsForeach($expected, $actualPropertyName, $actualArrayOfObjects, $message = '') {
		foreach($actualArrayOfObjects as $actualObject) {
			$this->assertNotEquals($expected, $actualObject->$actualPropertyName, $message);
		}
	}

	public function assertPropertyLessThanForeach($expected, $actualPropertyName, $actualArrayOfObjects, $message = '') {
		foreach($actualArrayOfObjects as $actualObject) {
			$this->assertLessThan($expected, $actualObject->$actualPropertyName, $message);
		}
	}

	public function assertPropertyLessThanOrEqualForeach($expected, $actualPropertyName, $actualArrayOfObjects, $message = '') {
		foreach($actualArrayOfObjects as $actualObject) {
			$this->assertLessThanOrEqual($expected, $actualObject->$actualPropertyName, $message);
		}
	}

	public function assertPropertyGreaterThanForeach($expected, $actualPropertyName, $actualArrayOfObjects, $message = '') {
		foreach($actualArrayOfObjects as $actualObject) {
			$this->assertGreaterThan($expected, $actualObject->$actualPropertyName, $message);
		}
	}

	public function assertPropertyGreaterThanOrEqualForeach($expected, $actualPropertyName, $actualArrayOfObjects, $message = '') {
		foreach($actualArrayOfObjects as $actualObject) {
			$this->assertGreaterThanOrEqual($expected, $actualObject->$actualPropertyName, $message);
		}
	}
}
