<?php

namespace Drupal\custom_work\Service;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;

/**
 * Handles CSV imports.
 */
class CsvImporter {

  protected EntityTypeManagerInterface $entityTypeManager;
  protected $logger;

  public function __construct(EntityTypeManagerInterface $entity_type_manager, LoggerChannelFactoryInterface $logger_factory) {
    $this->entityTypeManager = $entity_type_manager;
    $this->logger = $logger_factory->get('custom_work');
  }

  /**
   * Import CSV file.
   *
   * Expected CSV format:
   * title,body,status
   */
  public function import(string $file_path): void {

    if (!file_exists($file_path)) {
      $this->logger->error('CSV file not found: @file', ['@file' => $file_path]);
      return;
    }

    $handle = fopen($file_path, 'r');

    if (!$handle) {
      $this->logger->error('Unable to open CSV file.');
      return;
    }

    $header = fgetcsv($handle, 0, ',', '"', '\\');
    $count = 0;

    while (($row = fgetcsv($handle, 0, ',', '"', '\\')) !== FALSE) {

      $data = array_combine($header, $row);

      try {
        $node = $this->entityTypeManager
          ->getStorage('node')
          ->create([
            'type' => 'article',
            'title' => $data['title'] ?? 'Untitled',
            'body' => [
              'value' => $data['body'] ?? '',
              'format' => 'basic_html',
            ],
            'status' => $data['status'] ?? 1,
          ]);

        $node->save();
        $count++;

      }
      catch (\Exception $e) {
        $this->logger->error('CSV import error: @msg', ['@msg' => $e->getMessage()]);
      }
    }

    fclose($handle);

    $this->logger->notice('CSV import completed. Imported @count records.', [
      '@count' => $count,
    ]);
  }

}
