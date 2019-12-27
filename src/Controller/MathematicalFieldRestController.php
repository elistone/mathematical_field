<?php

namespace Drupal\mathematical_field\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class MathematicalFieldRestController
 *
 * @package Drupal\mathematical_field\Controller
 */
class MathematicalFieldRestController extends ControllerBase {


  /**
   * @param \Symfony\Component\HttpFoundation\Request $request
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   */
  public function getCalculation(Request $request) {
    // hold the error status
    $error = FALSE;

    // set the default response
    $response = [
      'status' => '',
      'result' => FALSE,
    ];


    // try and get the input
    $input = $request->get('input');

    // check we have an input set
    if (!$input) {
      $response['status'] = 'error';
      $response['result'] = t('Invalid response');
      return new JsonResponse($response, '400');
    }

    /**
     * @var \Drupal\mathematical_field\Services\Parser
     */
    $mathematicalParser = \Drupal::service('mathematical_field.parser');

    // try and get the result
    try {
      // get the result
      $result = $mathematicalParser->calculate($input)->getResult();

    } catch (\Exception $e) {
      // if error set the error variable and result to the error message
      $error = TRUE;
      $result = $this->t($e->getMessage());
    }

    // setup the response data
    $response['status'] = $error ? 'error' : 'success';
    $response['result'] = $result;
    $status = $error ? 400 : 200;

    // return as json response
    return new JsonResponse($response, $status);
  }

}
