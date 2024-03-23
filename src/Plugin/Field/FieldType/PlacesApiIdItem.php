<?php

namespace Drupal\google_reviews_extension\Plugin\Field\FieldType;

use Drupal\Component\Utility\Random;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Defines the 'google_reviews_extension_places_api_id' field type.
 *
 * @FieldType(
 *   id = "google_reviews_extension_places_api_id",
 *   label = @Translation("Places Api Id"),
 *   category = @Translation("General"),
 *   default_widget = "google_reviews_extension_places_api_id",
 *   default_formatter = "google_reviews_extension_places_api_id_default"
 * )
 */
class PlacesApiIdItem extends FieldItemBase {

  /**
   * {@inheritdoc}
   */
  public function isEmpty() {
    if ($this->places_id !== NULL) {
      return FALSE;
    }
    elseif ($this->display_review_score == 1) {
      return FALSE;
    }
    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {

    $properties['places_id'] = DataDefinition::create('string')
      ->setLabel(t('Places Id'));
    $properties['display_review_score'] = DataDefinition::create('boolean')
      ->setLabel(t('display review score'));

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public function getConstraints() {
    $constraints = parent::getConstraints();

    // @todo Add more constraints here.
    return $constraints;
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {

    $columns = [
      'places_id' => [
        'type' => 'varchar',
        'length' => 255,
      ],
      'display_review_score' => [
        'type' => 'int',
        'size' => 'tiny',
      ],
    ];

    $schema = [
      'columns' => $columns,
      // @DCG Add indexes here if necessary.
    ];

    return $schema;
  }

  /**
   * {@inheritdoc}
   */
  public static function generateSampleValue(FieldDefinitionInterface $field_definition) {

    $random = new Random();

    $values['places_id'] = $random->word(mt_rand(1, 255));

    $values['display_review_score'] = (bool) mt_rand(0, 1);

    return $values;
  }

}
