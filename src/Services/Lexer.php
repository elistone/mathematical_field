<?php

namespace Drupal\mathematical_field\Services;

use Drupal\mathematical_field\InfixToPostfix;
use Drupal\mathematical_field\Operators;
use Drupal\views\Plugin\views\field\Boolean;
use ReflectionClass;

/**
 * Class Lexer
 *
 * @package Drupal\mathematical_field
 */
class Lexer {

  /**
   * Stores an array of the operators used in the calculation
   *
   * @var array
   */
  private $operators = [];

  /**
   * Stores the string input
   *
   * @var string
   */
  private $string = "";

  /**
   * Stores the matches found from a calculation
   *
   * @var array
   */
  private $matches = [];

  /**
   * Stores the postfix value after being sorted
   *
   * @var InfixToPostfix
   */
  private $postfix = NULL;

  /**
   * Tokenize the string
   *
   * @param string $string
   *
   * @return \Drupal\mathematical_field\Services\Lexer
   * @throws \Exception
   */
  public function tokenizer(string $string): Lexer {
    // make sure the string is formatted correctly
    $string = $this->formatCalculationForTokenizer($string);
    $this->setString($string);

    // found the matches
    $matches = [];

    // get regex
    $regex = $this->getRegex();

    if (preg_match_all($regex, $string, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE) === FALSE) {
      new \Exception('Invalid matching options');
    };

    // clean up matches
    $matches = $this->cleanupMatches($matches);

    // check to make sure that the arithmetic is valid
    if (!$this->isValidArithmetic($matches)) {
      throw new \Exception('Invalid Arithmetic');
    }

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
    // only sort if we have matches
    if (empty($this->getMatches())) {
      throw new \Exception('Cannot sort precedence without any matches');
    }

    // use the infix to postfix class to convert
    $infixToPostfix = new InfixToPostfix();
    $results = $infixToPostfix->convert($this->getMatches());

    // set the postfix value
    $this->setPostfix($results);

    // return this
    return $this;
  }


  /**
   * Formats the incoming calculation to try and make sure it is consistent for
   * the tokenizer.
   * For example:
   * -3 - -5 & -3--5 becomes -3 - -5 to help the tokenizer recognise numbers vs
   * operators
   *
   * @param string $string
   *
   * @return string
   * @throws \Exception
   */
  protected function formatCalculationForTokenizer(string $string): string {
    $output = [];

    // remove all spaces
    $string = preg_replace('/\s+/', '', $string);

    // go through each string
    for ($i = 0; $i < strlen($string); $i++) {
      // get the char
      $char = $string[$i];
      $prevKey = $i - 2;
      $prev = $prevKey >= 0 ? $string[$prevKey] : FALSE;

      // find the last key and item (if isset)
      $lastKey = key(array_slice($output, -1, 1, TRUE));
      $lastItem = $output[$lastKey] ?? FALSE;

      // add numbers into the same array in between operators
      // so we end up with a whole number instead of separate numbers
      if ($lastItem !== FALSE && is_numeric($lastItem) && is_numeric($char)) {
        $output[$lastKey] = $output[$lastKey] . $char;
      }
      // handle negative numbers vs minus operator this is so we don't get things like: "- 3 - - 5" instead it will become "-3 - -5"
      // first check for the current char to be a number and the last added item to be a minus
      elseif ($lastItem !== FALSE && is_numeric($char) && $lastItem === Operators::OP_MINUS) {
        // if the one before that was numeric
        // add it to the next up list
        if (is_numeric($prev)) {
          $output[$i] = $char;
        }
        else {
          // otherwise join them together
          $output[$lastKey] = $output[$lastKey] . $char;
        }
      }
      // this handles the float numbers making sure to create "5.5" instead of "5 . 5"
      elseif ($lastItem !== FALSE && ((is_numeric($lastItem) && $char === ".") || $lastItem === "." && is_numeric($char))) {
        $output[$lastKey] = $output[$lastKey] . $char;
      }
      // lastly everything else gets added to its own array
      else {
        $output[$i] = $char;
      }

    }

    // now we split every time back out into a string with spaces
    $output = implode($output, " ");

    // return
    return $output;
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

          // if empty value (remove spaces) but keep the zeros!
          // continue
          if (empty($value) && !is_numeric($value)) {
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
   * Validates if the string entered is true mathematically calculation
   * It does this by counting the number of operators and making sure there is
   * the right amount of numbers to do a calculation
   *
   * @param $matches
   *
   * @return bool
   */
  protected function isValidArithmetic($matches): bool {
    $numbers = 0;
    $operators = 0;

    // if we have matches
    if (!empty($matches)) {
      // loop thought all the matches
      foreach ($matches as $match) {
        // extract the type
        $type = $match['TYPE'];

        // if number increase number amount
        if ($type === "NUMBER") {
          $numbers++;
        }
        // if is one of the operators increase operators
        elseif (strpos($type, 'OP_') !== FALSE) {
          $operators++;
        }

      }
    }

    // if we have no numbers or no operators
    // this is not a valid calculation
    if ($numbers === 0 || $operators === 0) {
      return FALSE;
    }
    // we need at least 2 numbers to do a calculation
    elseif ($numbers < 2) {
      return FALSE;
    }
    // the number of operators needs to be one less than the count of numbers
    elseif ($operators != $numbers - 1) {
      return FALSE;
    }


    return TRUE;
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
   * @return \Drupal\mathematical_field\InfixToPostfix
   */
  public function getPostfix(): InfixToPostfix {
    return $this->postfix;
  }

  /**
   * @param \Drupal\mathematical_field\InfixToPostfix $postfix
   */
  private function setPostfix(InfixToPostfix $postfix): void {
    $this->postfix = $postfix;
  }

  /**
   * @return string
   */
  public function getString(): string {
    return $this->string;
  }

  /**
   * @param string $string
   */
  private function setString(string $string): void {
    $this->string = $string;
  }

}
