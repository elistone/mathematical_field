<?php

namespace Drupal\Tests\mathematical_lexer_field\Unit;

use Drupal\Tests\UnitTestCase;
use Drupal\mathematical_field\Parser;

/**
 * Tests
 *
 * @coversDefaultClass \Drupal\mathematical_field\Parser
 * @group mathematical_lexer_field
 */
class ParserTest extends UnitTestCase {

  /**
   * @var \Drupal\mathematical_field\Parser
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
   * Simple data provider
   *
   * @return array
   */
  public function simpleDataProvider() {
    return [
      ["1 + 1", 2],
      ["0 + 0", 0],
      ["-1 + -1", -2],
      ["3 - 1", 2],
      ["0 - 0", 0],
      ["10 / 2", 5],
    ];
  }

  /**
   * Test simple calculations
   *
   * @param $string
   * @param $expected
   *
   * @dataProvider simpleDataProvider
   */
  public function testSimpleCalculations($string, $expected) {
    $result = $this->parser->calculate($string);
    $this->assertEquals($expected, $result);
  }

}
