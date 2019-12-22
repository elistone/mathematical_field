<?php

namespace Drupal\mathematical_field\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'mathematical_field_basic' formatter.
 *
 * @FieldFormatter(
 *   id = "mathematical_field_basic",
 *   label = @Translation("Mathematical"),
 *   field_types = {
 *     "string",
 *     "string_long",
 *   }
 * )
 */
class MathematicalFieldFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];
    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $element = [];
    /**
     * @var \Drupal\mathematical_field\Services\Parser
     */
    $mathematicalParser = \Drupal::service('mathematical_field.parser');
    foreach ($items as $delta => $item) {

      try {
        $result = $mathematicalParser->calculate($item->value)->getResult();
      } catch (\Exception $e) {
        $result = $e->getMessage();
      }

      $element[$delta] = [
        '#markup' => $result,
      ];
    }
    return $element;
  }

}
