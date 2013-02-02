<?php

require_once __DIR__ . '/../ProcessWireTestCase.php';

/**
 * Tests for ProcessWire selectors with different operators:
 *   =   Equal to
 *   !=  Not equal to
 *   <   Less than
 *   >   Greater than
 *   <=  Less than or equal to
 *   >=  Greater than or equal to
 *   *=   Contains the exact word or phrase
 *   ~=   Contains all the words
 *   ^=   Contains the exact word or phrase at the beginning of the field
 *   $=   Contains the exact word or phrase at the end of the field
 *   %=   Contains the exact word or phrase (using slower SQL LIKE)
 *   and negation of each of the above ("!field OP value")
 *
 * Focus on covering all the different operators and their basic usage.
 *
 */
class OperatorsTest extends ProcessWireTestCase
{
	/**
	 * Test find() method with given selector using database queries.
	 *
	 * @dataProvider providerForFind
	 *
	 */
	public function testFindInDatabase($description, $selector, $assertions, $skipMessage = '') {
		$this->runMethodInDatabase('find', $description, $selector, $assertions, $skipMessage);
	}

	/**
	 * Test find() method with given selector using in-memory PageArray.
	 *
	 * @dataProvider providerForFind
	 *
	 */
	public function testFindInMemory($description, $selector, $assertions, $skipMessage = '') {
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
					'assertPropertyNotRegExpForeach' => array('/^albuquerque$/', 'name')
				)
			),
			array('Negated equal to, custom field',
				'template=city, !title=Albuquerque',
				array(
					'assertCount' => array(69),
					'assertPropertyEqualsForeach' => array('city', 'template'),
					'assertPropertyNotRegExpForeach' => array('/^Albuquerque$/', 'title')
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
			array('Negated less than, native field',
				'!parent_id<4111',
				array(
					'assertCount' => array(1440),
					'assertPropertyGreaterThanOrEqualForeach' => array(4111, 'parent_id')
				)
			),
			array('Negated less than, custom field',
				'template=skyscraper, !floors<5',
				array(
					'assertCount' => array(1172),
					'assertPropertyMoreThanOrEqualForeach' => array(5, 'floors'),
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
			array('Negated less than or equal, native field',
				'!parent_id<=4111',
				array(
					'assertCount' => array(1231),
					'assertPropertyGreaterThanForeach' => array(4111, 'parent_id'),
				)
			),
			array('Negated less than or equal, custom field',
				'template=skyscraper, !floors<=5',
				array(
					'assertCount' => array(1167),
					'assertPropertyGreaterThanForeach' => array(5, 'floors'),
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
			array('Negated greater than, native field',
				'!parent_id>4111',
				array(
					'assertCount' => array(323),
					'assertPropertyLessThanOrEqualForeach' => array(4111, 'parent_id')
				)
			),
			array('Negated greater than, custom field',
				'template=skyscraper, !floors>15',
				array(
					'assertCount' => array(199),
					'assertPropertyLessThanOrEqualForeach' => array(15, 'floors'),
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
			array('Negated greater than or equal, native field',
				'!parent_id>=4111',
				array(
					'assertCount' => array(114),
					'assertPropertyLessThanForeach' => array(4111, 'parent_id'),
				)
			),
			array('Negated greater than or equal, custom field',
				'template=skyscraper, !floors>=15',
				array(
					'assertCount' => array(181),
					'assertPropertyLessThanForeach' => array(15, 'floors'),
					'assertPropertyEqualsForeach' => array('skyscraper', 'template')
				)
			),
			array('Exact word or phrase (SQL LIKE), native field',
				'name%=peachtree',
				array(
					'assertCount' => array(5),
					'assertPropertyContainsForeach' => array('peachtree', 'name')
				)
			),
			array('Exact word or phrase (SQL LIKE), custom field',
				'title%=peachtree',
				array(
					'assertCount' => array(5),
					'assertPropertyContainsForeach' => array('peachtree', 'title')
				)
			),
			array('Negated exact word or phrase (SQL LIKE), native field',
				'!name%=peachtree',
				array(
					'assertCount' => array(1549),
					'assertPropertyNotContainsForeach' => array('peachtree', 'name')
				)
			),
			array('Negated exact word or phrase (SQL LIKE), custom field',
				'!title%=peachtree',
				array(
					'assertCount' => array(1549),
					'assertPropertyNotContainsForeach' => array('peachtree', 'title')
				)
			),
			array('Exact word or phrase (fulltext), native field',
				'name*=peachtree',
				array(
					'assertCount' => array(5),
					'assertPropertyContainsForeach' => array('peachtree', 'name')
				)
			),
			array('Exact word or phrase (fulltext), custom field',
				'title*=peachtree',
				array(
					'assertCount' => array(5),
					'assertPropertyContainsForeach' => array('peachtree', 'title')
				)
			),
			array('Negated exact word or phrase (fulltext), native field',
				'!name*=peachtree',
				array(
					'assertCount' => array(1549),
					'assertPropertyNotContainsForeach' => array('peachtree', 'name')
				)
			),
			array('Negated exact word or phrase (fulltext), custom field',
				'!title*=peachtree',
				array(
					'assertCount' => array(1549),
					'assertPropertyNotContainsForeach' => array('peachtree', 'title')
				)
			),
			array('All words (fulltext), native field',
				'name~=tower south',
				array(
					'assertCount' => array(5),
					'assertPropertyContainsForeach' => array('tower', 'name'),
					'assertPropertyContainsForeach' => array('south', 'name')
				)
			),
			array('All words (fulltext), custom field',
				'body~=adipiscing sollicitudin suspendisse',
				array(
					'assertCount' => array(236),
					'assertPropertyContainsForeach' => array('adipiscing', 'body'),
					'assertPropertyContainsForeach' => array('sollicitudin', 'body'),
					'assertPropertyContainsForeach' => array('suspendisse', 'body')
				)
			),
			array('Negated all words (fulltext), native field',
				'!name~=tower south',
				array(
					'assertCount' => array(1549),
					'assertPropertyNotContainsForeach' => array('tower', 'name'),
					'assertPropertyNotContainsForeach' => array('south', 'name')
				)
			),
			array('Negated all words (fulltext), custom field',
				'!body~=adipiscing sollicitudin suspendisse',
				array(
					'assertCount' => array(1318),
					'assertPropertyNotContainsForeach' => array('adipiscing', 'body'),
					'assertPropertyNotContainsForeach' => array('sollicitudin', 'body'),
					'assertPropertyNotContainsForeach' => array('suspendisse', 'body')
				)
			),
			array('Exact word or phrase at the beginning of the field, native field',
				'name^=world',
				array(
					'assertCount' => array(5),
					'assertPropertyRegExpForeach' => array('/^world/', 'name')
				)
			),
			array('Exact word or phrase at the beginning of the field, custom field',
				'title^=one',
				array(
					'assertCount' => array(70),
					'assertPropertyRegExpForeach' => array('/^one/i', 'title')
				)
			),
			array('Negated exact word or phrase at the beginning of the field, native field',
				'!name^=world',
				array(
					'assertCount' => array(1549),
					'assertPropertyNotRegExpForeach' => array('/^world/', 'name')
				)
			),
			array('Negated exact word or phrase at the beginning of the field, custom field',
				'!title^=one',
				array(
					'assertCount' => array(1484),
					'assertPropertyNotRegExpForeach' => array('/^one/i', 'title')
				)
			),

		);
	}
}
