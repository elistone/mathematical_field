<?php

namespace Drupal\mathematical_field;

/**
 * Class InfixToPostfix
 *
 * @package Drupal\mathematical_field
 */
class InfixToPostfix {

  /**
   * Results
   *
   * @var array
   */
  private $result = [];

  /**
   * Stack
   *
   * @var array
   */
  private $stack = [];

  /**
   * Convert Infix to Postfix
   *
   * @param array $matches
   *
   * @return \Drupal\mathematical_field\InfixToPostfix
   */
  public function convert(array $matches): InfixToPostfix {

    // loop thought all the matches
    foreach ($matches as $match) {
      // separate into type & value
      $type = $match['TYPE'];
      $value = $match['VALUE'];

      // if it is a number add straight to the results
      if ($type === "NUMBER") {
        $this->result[] = $value;
      }
      // if is an operator
      elseif (strpos($type, 'OP_') !== FALSE) {
        // while it is empty loop thought the stack only adding items based upon precedence
        while (!empty($this->stack) && $this->precedence($value) <= $this->precedence($this->stack[0])) {
          $this->result[] = array_pop($this->stack);
        }
        // add next operator into the stack to be sorted next time we are here.
        $this->stack[] = $value;
      }
    }

    // once all done make sure we empty the stack of results
    while (!empty($this->stack)) {
      $this->result[] = array_pop($this->stack);
    }

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
   * @return array
   */
  public function getResultArray(): array {
    return $this->result;
  }

  /**
   * @return string
   */
  public function getResultString(): string {
    return implode('', $this->result);
  }

}
