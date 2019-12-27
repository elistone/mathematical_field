<?php

namespace Drupal\mathematical_field\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;

/**
 * Plugin implementation of the 'mathematical_field_jumble' formatter.
 *
 * @FieldFormatter(
 *   id = "mathematical_field_jumble",
 *   label = @Translation("Mathematical (Jumble)"),
 *   field_types = {
 *     "string",
 *     "string_long",
 *   }
 * )
 */
class MathematicalJumbleFieldFormatter extends FormatterBase {


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

    // loop thought each item
    foreach ($items as $delta => $item) {
      $input = preg_replace('/\s+/', '', $item->value);

      // build the output using the mathematical_field template
      $element[$delta] = [
        '#markup' => '<div class="jumble-field" data-input="' . $input . '"></div>',
        '#attached' => ['library' => ['mathematical_field/mathematical.jumble']],
      ];
    }
    return $element;
  }

}
