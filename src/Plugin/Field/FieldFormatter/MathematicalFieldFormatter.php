<?php

namespace Drupal\mathematical_field\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;
use NumberFormatter;

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
  public static function defaultSettings() {
    return [
        'input' => TRUE,
        'words' => TRUE,
      ] + parent::defaultSettings();
  }


  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    // settings flag for showing input
    $form['input'] = [
      '#title' => $this->t('Display input'),
      '#type' => 'checkbox',
      '#description' => $this->t('If enabled the calculation will be displayed.'),
      '#default_value' => $this->getSetting('input'),
    ];
    // settings flag for showing words
    $form['words'] = [
      '#title' => $this->t('Display in words'),
      '#type' => 'checkbox',
      '#description' => $this->t('If enabled the answer to the calculation will be displayed in words.'),
      '#default_value' => $this->getSetting('words'),
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];
    $summary[] = $this->t('Displays a mathematical calculation.');
    $summary[] = $this->t('Display input: @input', ['@input' => $this->getSetting('input') ? 'True' : 'False']);
    $summary[] = $this->t('Display in words: @words', ['@words' => $this->getSetting('words') ? 'True' : 'False']);
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

    /**
     * @var NumberFormatter
     */
    $numberFormatter = new NumberFormatter($langcode, NumberFormatter::SPELLOUT);

    // loop thought each item
    foreach ($items as $delta => $item) {
      // set the default outputs
      $words = FALSE;
      $input = FALSE;
      $error = FALSE;

      // try and get the result
      try {
        // get the result
        $result = $mathematicalParser->calculate($item->value)->getResult();

        // if enabled the display of input
        if ($this->getSetting('input')) {
          $input = $mathematicalParser->getLexer()->getString();
        }

        // if enabled the display of words
        if ($this->getSetting('words')) {
          $words = $numberFormatter->format($result);
        }
      } catch (\Exception $e) {
        // if error set the error variable and result to the error message
        $error = TRUE;
        $result = $this->t($e->getMessage());
      }

      // build the output using the mathematical_field template
      $element[$delta] = [
        '#theme' => 'mathematical_field',
        '#result' => $result,
        '#input' => $input,
        '#words' => $words,
        '#errors' => $error,
        '#attached' => ['library' => ['mathematical_field/mathematical.field-formatter']],
      ];
    }
    return $element;
  }

}
