<?php

require_once __DIR__ . '/../ProcessWireTestCase.php';

/**
 * Tests for ProcessWire selectors with basic operators:
 *   =   Equal to
 *   !=  Not equal to
 *   <   Less than
 *   >   Greater than
 *   <=  Less than or equal to
 *   >=  Greater than or equal to
 *   and negation of each of the above ("!field OP value")
 *
 */
class BasicOperatorsTest extends ProcessWireTestCase
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
}
