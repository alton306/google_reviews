<?php

namespace Drupal\google_reviews_extension;

use Drupal\googlereviews\GetGoogleData;
use GuzzleHttp\Exception\RequestException;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Messenger\MessengerInterface;
use GuzzleHttp\ClientInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Routing\UrlGeneratorInterface;
use Drupal\Core\StringTranslation\TranslationInterface;

/**
 * ExampleGetGoogleDataPerLocation service.
 */
class GetGoogleDataPerLocation extends GetGoogleData
{
  public function getGoogleReviews(array $fields = [], int $max_reviews = 5, string $reviews_sort = 'newest', string $language = ''): array
  {
    $config = $this->configFactory->get('googlereviews.settings');
    $auth_key = $config->get('google_auth_key');
    $place_ids = $this->getPlaceId();
    $api_url = $config->get('google_api_url');

    if (empty($place_ids)) {
      $place_ids = $config->get('google_place_id');
    }

    if ($auth_key == '' || ($place_ids == '' && empty($place_ids))) {
      $link = $this->urlGenerator->generateFromRoute('googlereviews.settings_form');
      $this->messenger->addError($this->t('You need to add credentials on the <a href=":link">Google review setings page</a> to show the reviews.', [':link' => $link]));
      return [];
    }

    $url_parameters = [
      'key' => $auth_key,
      'reviews_sort' => $reviews_sort,
      'language' => ($language == '') ? $this->languageManager->getCurrentLanguage()->getId() : $language,
    ];

    $result = [];

    foreach ($place_ids as $place_id) {
      $url_parameters['place_id'] = $place_id->field_google_reviews_places_id;

      if (!empty($fields)) {
        $url_parameters['fields'] = implode(',', $fields);
      }

      try {
        $request = $this->client->get($api_url, ['query' => $url_parameters]);
        $resultArray = json_decode($request->getBody(), TRUE);

        if ($resultArray['status'] !== 'OK') {
          if (isset($resultArray['error_message']) && !empty($resultArray['error_message'])) {
            $this->messenger->addError($this->t('Something went wrong with contacting the Google Maps API. @status, @error', [
              '@status' => $resultArray['status'],
              '@error' => $resultArray['error_message'],
            ]));
          } else {
            $this->messenger->addError($this->t('Something went wrong with contacting the Google Maps API: @status', [
              '@status' => $resultArray['status'],
            ]));
          }
        }

        

        if (isset($resultArray['result']) && !empty($resultArray['result'])) {
          if (isset($resultArray['result']['reviews'])) {
            $resultArray['result']['reviews'] = array_slice($resultArray['result']['reviews'], 0, $max_reviews);
          }

          $result[$place_id->entity_id] = $resultArray['result']['reviews'];
          // $result[$place_id->entity_id]['place_id'] = $place_id->field_google_reviews_places_id;
        }
      } catch (RequestException $e) {
        watchdog_exception('googlereviews', $e);
        $this->messenger->addError($this->t('Something went wrong with contacting the Google Maps API.'));
      }
    }
    return $result;
  }

  public function getPlaceId()
  {

    $database = \Drupal::database();

    // Get all content entity types.
    $content_entity_types = \Drupal::entityTypeManager()->getStorage('node_type')->loadMultiple();

    // Initialize an empty array to store the entity type IDs.
    $entity_type_ids = [];

    // Iterate over the content entity types to get their IDs.
    foreach ($content_entity_types as $content_entity_type) {
      $entity_type_ids[] = $content_entity_type->id();
    }

    // Initialize an empty array to store the entity IDs.
    $entity_ids = [];

    // Iterate over the content entity types to query entities.
    foreach ($entity_type_ids as $entity_type_id) {

      // Get the list of field definitions for the entity type.
      $fields = \Drupal::service('entity_field.manager')->getFieldDefinitions('node', $entity_type_id);

      // Iterate through the fields to check for the specific field type.
      foreach ($fields as $field_name => $field_definition) {
        if ($field_definition->getType() == 'google_reviews_extension_places_api_id') {

          $query = $database->select("node__$field_name", 'grpid')
            ->condition('grpid.' . $field_name . '_places_id', '', '!=')
            ->fields('grpid', ['entity_id', $field_name . '_places_id'])
            ->execute()->fetchAll();

          //Execute the query and merge the results.
          $entity_ids = array_merge($entity_ids, $query);
        }
      }
    }

    return $entity_ids;
  }
}
