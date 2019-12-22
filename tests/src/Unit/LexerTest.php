<?php

namespace Drupal\Tests\mathematical_lexer_field\Unit;

use Drupal\mathematical_field\Operators;
use Drupal\Tests\UnitTestCase;
use Drupal\mathematical_field\Services\Lexer;

/**
 * Tests
 *
 * @coversDefaultClass \Drupal\mathematical_field\Services\Lexer
 * @group mathematical_lexer_field
 */
class LexerTest extends UnitTestCase {

  /**
   * @var \Drupal\mathematical_field\Services\Lexer
   */
  private $lexer;

  /**
   * Setup method
   */
  protected function setUp() {
    $this->lexer = new Lexer();
  }

  /**
   * Tear down method
   */
  protected function tearDown() {
    $this->lexer = NULL;
  }


  /**
   * Operators data provider
   *
   * @return array
   */
  public function operatorsDataProvider() {
    return [
      ["1 + 1", [Operators::OP_PLUS]],
      ["3 - 1", [Operators::OP_MINUS]],
      ["10 / 2", [Operators::OP_DIVIDE]],
      ["8 * 7", [Operators::OP_MULTIPLY]],
      ["1.3 + 1.2", [Operators::OP_PLUS]],
      ["-3 - -5", [Operators::OP_MINUS]],
      [
        "10 + 20 - 30 + 15 * 5",
        [
          Operators::OP_PLUS,
          Operators::OP_MINUS,
          Operators::OP_PLUS,
          Operators::OP_MULTIPLY,
        ],
      ],
    ];
  }

  /**
   * Operators data provider
   *
   * @return array
   */
  public function tokenizerDataProvider() {
    return [
      [
        "1 + 1",
        [
          [
            'VALUE' => 1,
            'TYPE' => 'NUMBER',
          ],
          [
            'VALUE' => Operators::OP_PLUS,
            'TYPE' => 'OP_PLUS',
          ],
          [
            'VALUE' => 1,
            'TYPE' => 'NUMBER',
          ],
        ],
      ],
      [
        "3 - 1",
        [
          [
            'VALUE' => 3,
            'TYPE' => 'NUMBER',
          ],
          [
            'VALUE' => Operators::OP_MINUS,
            'TYPE' => 'OP_MINUS',
          ],
          [
            'VALUE' => 1,
            'TYPE' => 'NUMBER',
          ],
        ],
      ],
      [
        "10 / 2",
        [
          [
            'VALUE' => 10,
            'TYPE' => 'NUMBER',
          ],
          [
            'VALUE' => Operators::OP_DIVIDE,
            'TYPE' => 'OP_DIVIDE',
          ],
          [
            'VALUE' => 2,
            'TYPE' => 'NUMBER',
          ],
        ],
      ],
      [
        "8 * 7",
        [
          [
            'VALUE' => 8,
            'TYPE' => 'NUMBER',
          ],
          [
            'VALUE' => Operators::OP_MULTIPLY,
            'TYPE' => 'OP_MULTIPLY',
          ],
          [
            'VALUE' => 7,
            'TYPE' => 'NUMBER',
          ],
        ],
      ],
      [
        "1.3 + 1.2",
        [
          [
            'VALUE' => 1.3,
            'TYPE' => 'NUMBER',
          ],
          [
            'VALUE' => Operators::OP_PLUS,
            'TYPE' => 'OP_PLUS',
          ],
          [
            'VALUE' => 1.2,
            'TYPE' => 'NUMBER',
          ],
        ],
      ],
      [
        "-3 - -5",
        [
          [
            'VALUE' => -3,
            'TYPE' => 'NUMBER',
          ],
          [
            'VALUE' => Operators::OP_MINUS,
            'TYPE' => 'OP_MINUS',
          ],
          [
            'VALUE' => -5,
            'TYPE' => 'NUMBER',
          ],
        ],
      ],
      [
        "10 + 20 - 30 + 15 * 5",
        [
          [
            'VALUE' => 10,
            'TYPE' => 'NUMBER',
          ],
          [
            'VALUE' => Operators::OP_PLUS,
            'TYPE' => 'OP_PLUS',
          ],
          [
            'VALUE' => 20,
            'TYPE' => 'NUMBER',
          ],
          [
            'VALUE' => Operators::OP_MINUS,
            'TYPE' => 'OP_MINUS',
          ],
          [
            'VALUE' => 30,
            'TYPE' => 'NUMBER',
          ],
          [
            'VALUE' => Operators::OP_PLUS,
            'TYPE' => 'OP_PLUS',
          ],
          [
            'VALUE' => 15,
            'TYPE' => 'NUMBER',
          ],
          [
            'VALUE' => Operators::OP_MULTIPLY,
            'TYPE' => 'OP_MULTIPLY',
          ],
          [
            'VALUE' => 5,
            'TYPE' => 'NUMBER',
          ],
        ],
      ],
    ];
  }

  /**
   * Sort precedence data provider
   *
   * @return array
   */
  function sortingPrecedenceDataProvider() {
    return [
      ["1 + 1", "11+"],
      ["12 - 2", "122-"],
      ["3 * 2", "32*"],
      ["10 / 2", "102/"],
      ["5 * 2 / 2", "52*2/"],
      ["100 + 100 - 50", "100100+50-"],
      ["1 / 2 * 3 - 4", "12/3*4-"],
    ];
  }

  /**
   * Test operators
   *
   * @param $string
   * @param $expected
   *
   * @dataProvider operatorsDataProvider
   * @throws \Exception
   */
  public function testOperatorsCalculations($string, $expected) {
    $tokenize = $this->lexer->tokenizer($string);
    $operators = $tokenize->getOperators();
    $this->assertEquals($expected, $operators);
  }


  /**
   * Test Tokenizer array
   *
   * @param $string
   * @param $expected
   *
   * @dataProvider tokenizerDataProvider
   * @throws \Exception
   */
  public function testTokenizer($string, $expected) {
    $tokenize = $this->lexer->tokenizer($string);
    $matches = $tokenize->getMatches();
    $this->assertEquals($expected, $matches);
  }

  /**
   * @param $string
   * @param $expected
   *
   * @dataProvider sortingPrecedenceDataProvider
   * @throws \Exception
   */
  public function testSortingPrecedence($string, $expected) {
    $tokenize = $this->lexer->tokenizer($string);
    $sorted = $tokenize->sortPrecedence();
    $postfix = $sorted->getPostfix()->getResultString();
    $this->assertEquals($expected, $postfix);
  }

  /**
   * Test the invalid sorting precedence
   * Makes sure that if sort precedence is called before tokenizer an exception
   * is thrown
   *
   * @throws \Exception
   */
  public function testInvalidSortingPrecedence() {
    $this->expectException(\Exception::class);
    $this->lexer->sortPrecedence();
  }

}
