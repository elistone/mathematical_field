<?php

namespace Drupal\Tests\mathematical_lexer_field\Unit;

use Drupal\Tests\UnitTestCase;
use Drupal\mathematical_field\Services\Parser;

/**
 * Tests the Parser
 *
 * @coversDefaultClass \Drupal\mathematical_field\Services\Parser
 * @group mathematical_lexer_field
 */
class ParserTest extends UnitTestCase {

  /**
   * @var \Drupal\mathematical_field\Services\Parser
   */
  private $parser;

  /**
   * Setup method
   */
  protected function setUp() {
    $this->parser = new Parser();
  }

  /**
   * Tear down method
   */
  protected function tearDown() {
    $this->parser = NULL;
  }


  /**
   * Calculation data provider
   *
   * @return array
   */
  public function calculationDataProvider() {
    return [
      // simple int calculations
      ["1 + 1", 2],
      ["3 - 1", 2],
      ["10 / 2", 5],
      ["2 * 3", 6],

      // negative number calculations
      ["-1 + -2", -3],
      ["-1 - -3", 2],
      ["-4 * -3", 12],
      ["-12 / -4", 3],

      // float number calculations
      ["1.2 + 2.4", 3.6],
      ["3.2 - 2.7", 0.5],
      ["5.6 * 8.3", 46.48],
      ["5.5 / 3.8", 1.447368421],

      // testing 0 numbers
      ["0 + 0", 0],
      ["0 - 0", 0],
      ["0 - 0", 0],
      ["3 + 5 * 0", 3],
      ["0 / 10", 0],


      // testing longer calculations with multiple operators
      // int
      ["4 + 9 + 12", 25],
      ["5 - 6 - 2", -3],
      ["2 + 3 * 7", 23],
      ["5 * 7 - 8", 27],
      ["5 / 2 - 9", -6.5],
      ["3 + 6 / 3", 5],

      // testing longer calculations with multiple operators
      // floats
      ["4.3 + 9.2 + 12.8", 26.3],
      ["5.7 - 6.5 - 2.6", -3.4],
      ["2.9 + 3.4 * 7.9", 29.76],
      ["5.9 * 7.1 - 8.2", 33.69],
      ["5.7 / 2.5 - 9.9", -7.62],
      ["3.8 + 6.6 / 3.4", 5.7411764705882],

      // testing much longer calculations
      ["10 + 20 - 30 + 15 * 5", 75],
      ["32.4 + 54.2 - 23.5 + 54.3 * 23.9", 1360.87],
      ["5482.4 + 5391.2 - 1184.5 + 3982.3 * 320.9", 1287609.1700000002],
    ];
  }

  /**
   * Calculations without spaces data provider
   *
   * @return array
   */
  public function withoutSpacesDataProvider() {
    return [
      ["7-4+6- 2", 7],
      ["5+ 3*6", 23],
      ["7*3-10 /-2", 26],
      ["10+20-30+15*5", 75],
      ["11    / 2 + 0.5*    6", 8.5],
    ];
  }

  /**
   * Invalid data to test
   *
   * @return array
   */
  public function invalidDataProvider() {
    return [
      ["11"],
      ["abcd"],
      ["982-"],
      ["953+abc"],
      [""],
      [FALSE],
      [TRUE],
      ["+"],
      ["-"],
      ["/"],
      ["*"],
      ["-1"],
    ];
  }

  /**
   * Divide By Zero data provider
   *
   * @return array
   */
  public function divideByZeroDataProvider() {
    return [
      ["10 / 0"],
      ["10 / 0.0"],
      ["10 / -0"],
      ["10 / -0.0"],
    ];
  }

  /**
   * Test Calculations
   *
   * @param $string
   * @param $expected
   *
   * @dataProvider calculationDataProvider
   * @throws \Exception
   */
  public function testSimpleCalculations($string, $expected) {
    $result = $this->parser->calculate($string)->getResult();
    $this->assertEquals($expected, $result);
  }

  /**
   * Test without spaces calculations
   *
   * @param $string
   * @param $expected
   *
   * @dataProvider withoutSpacesDataProvider
   * @throws \Exception
   */
  public function testWithoutSpacesCalculations($string, $expected) {
    $result = $this->parser->calculate($string)->getResult();
    $this->assertEquals($expected, $result);
  }

  /**
   * Test invalid data
   *
   * @param $string
   *
   * @dataProvider invalidDataProvider
   */
  public function testInvalidData($string) {
    try {
      $this->parser->calculate($string)->getResult();
    } catch (\Exception $e) {
      $this->assertEquals("Invalid Arithmetic", $e->getMessage());
    }
  }

  /**
   * Test to see if dividing by zero causes the correct error message
   *
   * @param $string
   *
   * @dataProvider divideByZeroDataProvider
   */
  public function testDivideByZero($string) {
    try {
      $this->parser->calculate($string)->getResult();
    } catch (\Exception $e) {
      $this->assertEquals("I don't think you want to do that... (Divided by zero)", $e->getMessage());
    }
  }

}
