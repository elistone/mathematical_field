<?php

namespace Drupal\mathematical_field\Services;

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
   */
  public function tokenizer(string $string): Lexer {
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
   * Get all the regex matching options
   * e.g. NUMBER vs all Operators Available
   *
   * @return string
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
   */
  protected function getAllOperatorsRegex(): array {
    $regex = [];

    // use the reflection to get the available constants
    try {
      $refl = new ReflectionClass('Drupal\mathematical_field\Operators');
    } catch (\Exception $e) {
      new \Exception($e->getMessage());
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

}
