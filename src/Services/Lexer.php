<?php

namespace Drupal\mathematical_field\Services;

use Drupal\mathematical_field\Operators;
use ReflectionClass;

/**
 * Class Lexer
 *
 * @package Drupal\mathematical_field
 */
class Lexer {

  /**
   * Stores an array of the operators used in the formula
   *
   * @var array
   */
  private $operators = [];

  /**
   * Stores the matches found from a formula
   *
   * @var array
   */
  private $matches = [];

  /**
   * Stores the postfix value after being sorted
   *
   * @var string
   */
  private $postfix = "";

  /**
   * Parser constructor.
   */
  public function __construct() {

  }

  /**
   * Tokenize the string
   *
   * @param string $string
   *
   * @return \Drupal\mathematical_field\Services\Lexer
   * @throws \Exception
   */
  public function tokenizer(string $string): Lexer {
    // found the matches
    $matches = [];

    // get regex
    $regex = $this->getRegex();

    if (preg_match_all($regex, $string, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE) === FALSE) {
      new \Exception('Invalid matching options');
    };

    // clean up matches
    $matches = $this->cleanupMatches($matches);

    // set the found matches
    $this->matches = $matches;

    // return the lexer
    return $this;
  }

  /**
   * Sort the matched values into operator precedence converting infix to
   * postfix notation
   *
   * @throws \Exception
   */
  public function sortPrecedence(): Lexer {
    if (empty($this->getMatches())) {
      throw new \Exception('Cannot sort precedence without any matches');
    }

    // store the results
    $result = "";

    // items that belong to the stack
    $stack = [];

    // loop thought all the matches
    foreach ($this->getMatches() as $match) {
      // separate into type & value
      $type = $match['TYPE'];
      $value = $match['VALUE'];

      // if it is a number add straight to the results
      if ($type === "NUMBER") {
        $result .= $value;
      }
      // if is an operator
      elseif (strpos($type, 'OP_') !== FALSE) {
        // while it is empty loop thought the stack only adding items based upon precedence
        while (!empty($stack) && $this->precedence($value) <= $this->precedence($stack[0])) {
          $result .= array_pop($stack);
        }
        // add next operator into the stack to be sorted next time we are here.
        $stack[] = $value;
      }
    }

    // once all done make sure we empty the stack of results
    while (!empty($stack)) {
      $result .= array_pop($stack);
    }

    // set the postfix value
    $this->setPostfix($result);

    // return this
    return $this;
  }


  /**
   * The precedence value of each operator
   *
   * @param string $operator
   *
   * @return int
   */
  private function precedence(string $operator): int {
    switch ($operator) {
      case Operators::OP_MINUS:
      case Operators::OP_PLUS:
        return 1;
      case Operators::OP_MULTIPLY:
      case Operators::OP_DIVIDE:
        return 2;
      default:
        return -1;
    }
  }

  /**
   * Get all the regex matching options
   * e.g. NUMBER vs all Operators Available
   *
   * @return string
   * @throws \Exception
   */
  protected function getRegex(): string {

    $regex = [
      // regex to look for numbers e.g. 1, 1.2 or -2.3 and set group name to NUMBER
      sprintf('(?P<NUMBER>%s)', '\-?\d+\.?\d*(E-?\d+)?'),
      // \-?\d+\.?\d*(E-?\d+)?
      sprintf('(?P<BRACKET_LEFT>%s)', '\\('),
      sprintf('(?P<BRACKET_RIGHT>%s)', '\\)'),
    ];

    // load in the available operators and merge with the number one
    $operatorsRegex = $this->getAllOperatorsRegex();
    $regex = array_merge($regex, $operatorsRegex);

    // convert into a string and return regex
    $regex = implode('|', $regex);
    return "/$regex/i";
  }

  /**
   * Converts all the available operators into regex
   *
   * @return array
   * @throws \Exception
   */
  protected function getAllOperatorsRegex(): array {
    $regex = [];

    // use the reflection to get the available constants
    try {
      $refl = new ReflectionClass('Drupal\mathematical_field\Operators');
    } catch (\Exception $e) {
      throw new \Exception($e->getMessage());
    }

    $allOperators = $refl->getConstants();

    // use the operator values to create regex look ups
    if (!empty($allOperators)) {
      foreach ($allOperators as $key => $operator) {
        $regex[] = sprintf('(?P<' . $key . '>%s)', "\\" . $operator);
      }
    }

    // return
    return $regex;
  }

  /**
   * Cleans up any regex matches removing things like duplicates or invalid
   * results Leaving just the valid matches
   *
   * @param $matches
   *
   * @return array
   */
  protected function cleanupMatches(array $matches): array {
    $output = [];
    // make sure we have some matches
    if (!empty($matches)) {
      // loop through all the matches
      foreach ($matches as $mKey => $match) {
        foreach ($match as $key => $info) {

          // if the key is numeric
          // continue
          if (is_numeric($key)) {
            continue;
          }

          // get the value from first response
          $value = $info[0];

          // if empty value (remove spaces)
          // continue
          if (empty($value)) {
            continue;
          }

          // set the used operators
          if (strpos($key, 'OP_') !== FALSE) {
            $this->setOperators($value);
          }

          // build a new array keeping only the information required
          $matched = [
            'VALUE' => $value,
            'TYPE' => $key,
          ];
          $output[] = $matched;
        }
      }
    }

    return $output;
  }

  /**
   * @return array
   */
  public function getOperators(): array {
    return $this->operators;
  }

  /**
   * @param string $operators
   */
  private function setOperators(string $operators): void {
    $this->operators[] = $operators;
  }

  /**
   * @return array
   */
  public function getMatches(): array {
    return $this->matches;
  }

  /**
   * @return string
   */
  public function getPostfix(): string {
    return $this->postfix;
  }

  /**
   * @param string $postfix
   */
  private function setPostfix(string $postfix): void {
    $this->postfix = $postfix;
  }

}
