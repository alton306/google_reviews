<?php

namespace Drupal\google_reviews_extension\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;

/**
 * Plugin implementation of the 'google_reviews_extension_places_api_id_default' formatter.
 *
 * @FieldFormatter(
 *   id = "google_reviews_extension_places_api_id_default",
 *   label = @Translation("Default"),
 *   field_types = {"google_reviews_extension_places_api_id"}
 * )
 */
class PlacesApiIdDefaultFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $element = [];

    foreach ($items as $delta => $item) {

      if ($item->places_id) {
        $element[$delta]['places_id'] = [
          '#type' => 'item',
          '#title' => $this->t('Places Id'),
          '#markup' => $item->places_id,
        ];
      }

      $element[$delta]['display_review_score'] = [
        '#type' => 'item',
        '#title' => $this->t('display review score'),
        '#markup' => $item->display_review_score ? $this->t('Yes') : $this->t('No'),
      ];

    }

    return $element;
  }

}
