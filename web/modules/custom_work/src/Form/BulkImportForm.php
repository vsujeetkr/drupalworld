<?php

namespace Drupal\custom_work\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\custom_work\Service\CsvImporter;
use Drupal\custom_work\Service\ApiImporter;

/**
 * Bulk Import Form.
 */
class BulkImportForm extends FormBase {

  protected CsvImporter $csvImporter;
  protected ApiImporter $apiImporter;

  /**
   * Constructor.
   */
  public function __construct(CsvImporter $csv_importer, ApiImporter $api_importer) {
    $this->csvImporter = $csv_importer;
    $this->apiImporter = $api_importer;
  }

  /**
   * Dependency injection.
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('custom_work.csv_importer'),
      $container->get('custom_work.api_importer')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'bulk_import_form';
  }

  /**
   * Build form.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['import_type'] = [
      '#type' => 'radios',
      '#title' => $this->t('Import Type'),
      '#options' => [
        'csv' => $this->t('CSV Upload'),
        'api' => $this->t('External API'),
      ],
      '#default_value' => 'csv',
      '#required' => TRUE,
    ];

    // CSV upload field.
    $form['csv_file'] = [
      '#type' => 'managed_file',
      '#title' => $this->t('CSV File'),
      '#description' => $this->t('Upload a CSV file.'),
      '#upload_location' => 'public://bulk_import/',
      '#upload_validators' => [
        'FileExtension' => ['extensions' => 'csv'],
      ],
      '#states' => [
        'visible' => [
          ':input[name="import_type"]' => ['value' => 'csv'],
        ],
      ],
      '#required' => FALSE,
    ];

    // API URL field.
    $form['api_url'] = [
      '#type' => 'url',
      '#title' => $this->t('API URL'),
      '#states' => [
        'visible' => [
          ':input[name="import_type"]' => ['value' => 'api'],
        ],
      ],
      '#required' => FALSE,
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Start Import'),
    ];

    return $form;
  }

  /**
   * Submit handler.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    $import_type = $form_state->getValue('import_type');

    if ($import_type === 'csv') {
      $file_ids = $form_state->getValue('csv_file');

      if (empty($file_ids)) {
        $this->messenger()->addError($this->t('Please upload a CSV file.'));
        return;
      }

      $file = \Drupal\file\Entity\File::load(reset($file_ids));
      $file_path = \Drupal::service('file_system')->realpath($file->getFileUri());

      // Call CSV importer.
      $this->csvImporter->import($file_path);

      $this->messenger()->addStatus($this->t('CSV import started successfully.'));
    }

    elseif ($import_type === 'api') {
      $api_url = $form_state->getValue('api_url');

      if (empty($api_url)) {
        $this->messenger()->addError($this->t('Please provide API URL.'));
        return;
      }

      // Call API importer.
      $this->apiImporter->import($api_url);

      $this->messenger()->addStatus($this->t('API import started successfully.'));
    }
  }

}
