<?php

namespace Drupal\mathematical_field\Services;

use Drupal\mathematical_field\Operators;

/**
 * Class Parser
 *
 * @package Drupal\mathematical_field
 */
class Parser {

  /**
   * Result
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
   * Calculate the calculation result
   *
   * @param $string
   *
   * @return \Drupal\mathematical_field\Services\Parser
   * @throws \Exception
   */
  public function calculate($string): Parser {
    // reset the results
    $this->reset();

    // start up the lexer
    $lexer = new Lexer();
    $tokenize = $lexer->tokenizer($string);
    $sorted = $tokenize->sortPrecedence();
    $postfix = $sorted->getPostfix()->getResultArray();

    // evaluate the result
    $this->evaluatePostfix($postfix);

    return $this;
  }


  /**
   * Evaluate the postfix array.
   *
   * @param array $postfix
   *
   * @return void
   * @throws \Exception
   */
  private function evaluatePostfix(array $postfix): void {
    // loop thought the postfix array
    foreach ($postfix as $key => $value) {
      // if the value is a number add to the stack for later
      if (is_numeric($value)) {
        $this->stack[] = $value;
      }
      else {
        // get the last two values
        $val1 = array_pop($this->stack);
        $val2 = array_pop($this->stack);

        // calculate the last two values using using the operator found
        $evaluate = $this->evaluateOperator($val1, $val2, $value);

        // if the result we get is not equal to FALSE
        if ($evaluate !== FALSE) {
          $this->stack[] = $evaluate;
        }
      }
    }

    // set the results
    if (!empty($this->stack)) {
      $this->setResult($this->stack);
    }
  }

  /**
   * Takes two values and uses it operator to do the math
   *
   * @param $val1
   * @param $val2
   * @param $operator
   *
   * @return string
   * @throws \Exception
   */
  private function evaluateOperator($val1, $val2, $operator) {
    $output = FALSE;

    // use switch case to work out the operators and manually do the calculation
    switch ($operator) {
      case Operators::OP_PLUS;
        $output = $val2 + $val1;
        break;
      case Operators::OP_MINUS;
        $output = $val2 - $val1;
        break;
      case Operators::OP_DIVIDE;
        if ($val1 == 0) {
          throw new \Exception("I don't think you want to do that... (Divided by zero)");
        }
        $output = $val2 / $val1;
        break;
      case Operators::OP_MULTIPLY;
        $output = $val2 * $val1;
        break;
    }

    // return the result
    return $output;
  }

  /**
   * @return number
   * @throws \Exception
   */
  public function getResult() {
    $count = count($this->result);

    // If we have no results throw error
    if ($count === 0) {
      throw new \Exception('No result was calculated.');
    }

    // if we have more than one result throw error
    if ($count > 1) {
      throw new \Exception('The result was not evaluated correctly.');
    }

    return $this->result[0];
  }

  /**
   * @param array $result
   */
  private function setResult(array $result): void {
    $this->result = $result;
  }

  /**
   * Make sure we empty out the whole stack and results
   */
  private function reset() {
    $this->stack = [];
    $this->result = [];
  }

}
