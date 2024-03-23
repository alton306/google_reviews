<?php

namespace Drupal\google_reviews_extension\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\Validator\ConstraintViolationInterface;

/**
 * Defines the 'google_reviews_extension_places_api_id' field widget.
 *
 * @FieldWidget(
 *   id = "google_reviews_extension_places_api_id",
 *   label = @Translation("Places Api Id"),
 *   field_types = {"google_reviews_extension_places_api_id"},
 * )
 */
class PlacesApiIdWidget extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {

    $element['places_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Places Id'),
      '#default_value' => isset($items[$delta]->places_id) ? $items[$delta]->places_id : NULL,
    ];

    $element['display_review_score'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('display review score'),
      '#default_value' => isset($items[$delta]->display_review_score) ? $items[$delta]->display_review_score : NULL,
    ];

    $element['#theme_wrappers'] = ['container', 'form_element'];
    $element['#attributes']['class'][] = 'google-reviews-extension-places-api-id-elements';
    $element['#attached']['library'][] = 'google_reviews_extension/google_reviews_extension_places_api_id';

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function errorElement(array $element, ConstraintViolationInterface $violation, array $form, FormStateInterface $form_state) {
    return isset($violation->arrayPropertyPath[0]) ? $element[$violation->arrayPropertyPath[0]] : $element;
  }

  /**
   * {@inheritdoc}
   */
  public function massageFormValues(array $values, array $form, FormStateInterface $form_state) {
    foreach ($values as $delta => $value) {
      if ($value['places_id'] === '') {
        $values[$delta]['places_id'] = NULL;
      }
      if ($value['display_review_score'] === '') {
        $values[$delta]['display_review_score'] = NULL;
      }
    }
    return $values;
  }

}
