<?php

namespace Drupal\custom_work\Service;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use GuzzleHttp\ClientInterface;

/**
 * Handles API imports.
 */
class ApiImporter {

  protected ClientInterface $httpClient;
  protected EntityTypeManagerInterface $entityTypeManager;
  protected $logger;

  public function __construct(ClientInterface $http_client, EntityTypeManagerInterface $entity_type_manager, LoggerChannelFactoryInterface $logger_factory) {
    $this->httpClient = $http_client;
    $this->entityTypeManager = $entity_type_manager;
    $this->logger = $logger_factory->get('custom_work');
  }

  /**
   * Import data from API.
   *
   * Expected JSON:
   * [
   *   {"title": "...", "body": "..."}
   * ]
   */
  public function import(string $api_url): void {

    try {
      $response = $this->httpClient->request('GET', $api_url);
      $data = json_decode($response->getBody()->getContents(), TRUE);

      if (!is_array($data)) {
        $this->logger->error('Invalid API response format.');
        return;
      }

      $count = 0;

      foreach ($data as $item) {

        try {
          $node = $this->entityTypeManager
            ->getStorage('node')
            ->create([
              'type' => 'article',
              'title' => $item['title'] ?? 'Untitled',
              'body' => [
                'value' => $item['body'] ?? '',
                'format' => 'basic_html',
              ],
              'status' => 1,
            ]);

          $node->save();
          $count++;

        }
        catch (\Exception $e) {
          $this->logger->error('API item error: @msg', ['@msg' => $e->getMessage()]);
        }
      }

      $this->logger->notice('API import completed. Imported @count records.', [
        '@count' => $count,
      ]);

    }
    catch (\Exception $e) {
      $this->logger->error('API request failed: @msg', ['@msg' => $e->getMessage()]);
    }
  }

}
