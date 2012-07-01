<?php
/**
 * @package     WebService.Tests
* @subpackage  Application
*
* @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE
*/

require_once __DIR__ . '/../../../../application/stubs/webMock.php';

/**
 * Test Case class for WebServiceControllerV1JsonBaseUpdate
*
* @package     WebService.Tests
* @subpackage  Application
* @since       1.0
*/
class WebServiceControllerV1JsonBaseUpdateTest extends TestCase
{

	/**
	 * An instance of the class to test.
	 *
	 * @var    WebServiceControllerV1JsonBaseUpdate
	 * @since  1.0
	 */
	private $_instance;

	/**
	 * Tests __construct()
	 *
	 * @return  void
	 *
	 * @covers  WebServiceControllerV1JsonBaseUpdateTest::__construct
	 * @since   1.0
	 */
	public function test__construct()
	{
		// Create the mock.
		$input = $this->getMock('JInput', array('test'), array(), '', false);
		$input->expects($this->any())
		->method('test')
		->will(
				$this->returnValue('ok')
		);

		// Construct the object.
		$controller = new WebServiceControllerV1JsonBaseUpdate('general', $input, $this->getMockWeb());

		// Verify that the values injected into the constructor are present.
		$this->assertEquals('ok', TestReflection::getValue($controller, 'input')->test());
	}

	/**
	 * Provides test data for request format detection.
	 *
	 * @return  array
	 *
	 * @since   1.0
	 */
	public function seedGetContentIdData()
	{
		// Input, Expected, Exception
		return array(
				array('', '*', true),
				array(null, '*', true),
				array('22', '22', false),
				array('-7', null, true),
				array('22/user', '22', false),
				array('bad/user', '22', true),
				array('-1/user', null, true),
		);
	}

	/**
	 * Tests getContentId()
	 *
	 * @param   string   $input      Input string to test.
	 * @param   string   $expected   Expected fetched string.
	 * @param   boolean  $exception  True if an InvalidArgumentException is expected based on invalid input.
	 *
	 * @return  void
	 *
	 * @covers        WebServiceControllerV1JsonBaseUpdate::getContentId
	 * @dataProvider  seedGetContentIdData
	 * @since         1.0
	 */
	public function testGetContentId($input,  $expected, $exception)
	{
		// Set the input values.
		$_GET['@route'] = $input;

		// Execute the code to test.
		$actual = TestReflection::invoke($this->_instance, 'getContentId');

		// Clean up after ourselves.
		$_GET['@route'] = null;

		// If we are expecting an exception set it.
		if ($exception)
		{
			$app = TestReflection::getValue($this->_instance, 'app');
			$errors = TestReflection::getValue($app->errors, 'errorsArray');
			$this->assertEquals(1, count($errors));
			return;
		}

		// Verify the value.
		$this->assertEquals($expected, $actual);
	}

	/**
	 * Provides test data for request format detection.
	 *
	 * @return  array
	 *
	 * @since   1.0
	 */
	public function seedGetDataFieldsData()
	{
		$dataFields = array('field1' => null, 'field2' => null, 'field3' => null, 'field4' => null, 'field5' => null);

		// Input, Expected, Exception
		return array(
				array(
						$dataFields,
						array(),
						array('field1' => null, 'field2' => null, 'field3' => null, 'field4' => null, 'field5' => null,)
					),
				array(
						$dataFields,
						array('field1' => null),
						array('field1' => null, 'field2' => null, 'field3' => null, 'field4' => null, 'field5' => null,)
					),
				array(
						$dataFields,
						array('field1' => 'test', 'field2' => 'test2'),
						array('field1' => 'test', 'field2' => 'test2', 'field3' => null, 'field4' => null, 'field5' => null,)
					),
				array(
						$dataFields,
						array('field1' => 'test', 'field2' => 'test2'),
						array('field1' => 'test', 'field2' => 'test2', 'field3' => null, 'field4' => null, 'field5' => null,)
					)
		);
	}

	/**
	 * Tests getDataFields()
	 *
	 * @param   array   $df        Data fields to set up
	 * @param   string  $input     Input string to test.
	 * @param   string  $expected  Expected fetched string.
	 *
	 * @return  void
	 *
	 * @covers        WebServiceControllerV1JsonBaseUpdate::getDataFields
	 * @dataProvider  seedGetDataFieldsData
	 * @since         1.0
	 */
	public function testGetDataFields($df, $input,  $expected)
	{
		TestReflection::setValue($this->_instance, 'dataFields', $df);

		foreach ($input as $key => $value)
		{
			$_GET[$key] = $value;
		}

		// Execute the code to test.
		TestReflection::invoke($this->_instance, 'getDataFields');

		// Clean up after ourselves.
		foreach ($input as $key => $value)
		{
			$_GET[$key] = null;
		}

		// Verify the value.
		$this->assertEquals($expected, TestReflection::getValue($this->_instance, 'dataFields'));
	}

/**
	 * Provides test data for getAction
	 *
	 * @return  array
	 *
	 * @since   1.0
	 */
	public function seedGetAction()
	{
		// Input, Expected, Exception
		return array(
				array(null),
				array('like')
		);
	}

	/**
	 * Tests getAction()
	 *
	 * @param   string   $data  Input to test
	 *
	 * @return  void
	 *
	 * @covers        WebServiceControllerV1JsonBaseUpdate::getAction
	 * @dataProvider  seedGetAction
	 * @since         1.0
	 */
	public function testGetAction($data)
	{
		// Set the input values.
		$_GET['action'] = $data;

		// Execute the code to test.
		$actual = TestReflection::invoke($this->_instance, 'getAction');

		// Clean up after ourselves.
		$_GET['action'] = null;

		// Verify the value.
		$this->assertEquals($data, $actual);
	}

	/** Test execute with errors
	 *
	 * @return void
	 *
	 * @covers        WebServiceControllerV1JsonBaseUpdate::execute
	 * @since
	 */
	public function testExecuteWithErrors()
	{
		$_GET['@route'] = '22';

		// Get app
		$app = TestReflection::getValue($this->_instance, 'app');

		// Set errors
		TestReflection::setValue($app->errors, 'errors', true);
		$errors = TestReflection::setValue($app->errors, 'errorsArray', array('foo'));

		TestReflection::invoke($this->_instance, 'execute');

		$actual = TestReflection::invoke($app, 'getBody');
		$expected = json_encode(array('foo'));

		$this->assertEquals($expected, $actual);

	}

	/** Test init
	 *
	 * @return void
	 *
	 * @covers        WebServiceControllerV1JsonBaseUpdate::init
	 * @since
	 */
	public function testInit()
	{
		$_GET['@route'] = '22';
		TestReflection::setValue($this->_instance, 'fieldsMap', array());

		$mf = array('f1' => null, 'f2' => null, 'f3' => null);
		$of = array('f4' => null, 'f5' => null);
		TestReflection::setValue($this->_instance, 'mandatoryFields', $mf);
		TestReflection::setValue($this->_instance, 'optionalFields', $of);

		foreach (array('f1' => 'test', 'f2' => 'test2') as $key => $value)
		{
			$_GET[$key] = $value;
		}

		TestReflection::invoke($this->_instance, 'init');

		foreach (array('f1' => 'test', 'f2' => 'test2') as $key => $value)
		{
			$_GET[$key] = null;
		}

		// Test expected id
		$ai = TestReflection::getValue($this->_instance, 'id');
		$this->assertEquals('22', $ai);

		$ad = TestReflection::getValue($this->_instance, 'dataFields');
		$this->assertEquals(array('f1' => 'test', 'f2' => 'test2', 'f3' => null, 'f4' => null, 'f5' => null), $ad);
	}

	/** Test buildFields
	 *
	 * @return void
	 *
	 * @covers        WebServiceControllerV1JsonBaseUpdate::buildFields
	 * @since
	 */
	public function testBuildFields()
	{
		$mf = array('f1' => null, 'f2' => null, 'f3' => null);
		$of = array('f4' => null, 'f5' => null);
		TestReflection::setValue($this->_instance, 'mandatoryFields', $mf);
		TestReflection::setValue($this->_instance, 'optionalFields', $of);

		TestReflection::invoke($this->_instance, 'buildFields');

		$expected = TestReflection::getValue($this->_instance, 'dataFields');

		$this->assertEquals(array('f1' => null, 'f2' => null, 'f3' => null, 'f4' => null, 'f5' => null), $expected);
	}
	/**
	 * Prepares the environment before running a test.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	protected function setUp()
	{
		parent::setUp();

		$testInput = new JInput;
		$testMock = WebServiceApplicationWebMock::create($this);
		$this->_instance = new WebServiceControllerV1JsonBaseUpdate('general', $testInput, $testMock);
	}

	/**
	 * Cleans up the environment after running a test.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	protected function tearDown()
	{
		$this->_instance = null;

		parent::tearDown();
	}

}