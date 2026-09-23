<?php

namespace Drupal\custom_work\Batch;

use Drupal\node\Entity\Node;

/**
 * Batch processing for bulk import.
 */
class BulkImportBatch {

  /**
   * Batch callback: process CSV rows.
   */
  public static function processCsvBatch($chunk, &$context) {

    if (!isset($context['results']['count'])) {
      $context['results']['count'] = 0;
    }

    foreach ($chunk as $data) {

      try {
        $node = Node::create([
          'type' => 'article',
          'title' => $data['title'] ?? 'Untitled',
          'body' => [
            'value' => $data['body'] ?? '',
            'format' => 'basic_html',
          ],
          'status' => $data['status'] ?? 1,
        ]);

        $node->save();
        $context['results']['count']++;

      }
      catch (\Exception $e) {
        $context['results']['errors'][] = $e->getMessage();
      }
    }

    $context['message'] = t('Processed @count items so far...', [
      '@count' => $context['results']['count'],
    ]);
  }

  /**
   * Batch callback: process API data.
   */
  public static function processApiBatch($chunk, &$context) {

    if (!isset($context['results']['count'])) {
      $context['results']['count'] = 0;
    }

    foreach ($chunk as $item) {

      try {
        $node = Node::create([
          'type' => 'article',
          'title' => $item['title'] ?? 'Untitled',
          'body' => [
            'value' => $item['body'] ?? '',
            'format' => 'basic_html',
          ],
          'status' => 1,
        ]);

        $node->save();
        $context['results']['count']++;

      }
      catch (\Exception $e) {
        $context['results']['errors'][] = $e->getMessage();
      }
    }

    $context['message'] = t('Processed @count API items...', [
      '@count' => $context['results']['count'],
    ]);
  }

  /**
   * Finished callback.
   */
  public static function finished($success, $results, $operations) {

    if ($success) {
      \Drupal::messenger()->addStatus(t('Import completed successfully. Imported @count items.', [
        '@count' => $results['count'] ?? 0,
      ]));
    }
    else {
      \Drupal::messenger()->addError(t('Import finished with errors.'));
    }

    if (!empty($results['errors'])) {
      foreach ($results['errors'] as $error) {
        \Drupal::logger('custom_work')->error($error);
      }
    }
  }

}
