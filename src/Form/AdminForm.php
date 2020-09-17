<?php

namespace Drupal\employees\Form;

/**
 * @file
 * Contains Drupal\employees\Form\AdminForm.
 */

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Admin form module.
 */
class AdminForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'employees.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'employees_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('employees.settings');
    $form = parent::buildForm($form, $form_state);

    $form['endpoint'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Endpoint'),
      '#description' => $this->t('Endpoint to employees.'),
      '#required' => TRUE,
      '#default_value' => $config->get('endpoint'),
    ];

    $form['low_range'] = [
      '#type' => 'details',
      '#title' => t('Salary Low'),
      '#open' => TRUE,
    ];

    $form['low_range']['low_range_low'] = [
      '#type' => 'number',
      '#placeholder' => $this->t('Lower range'),
      '#description' => $this->t('Indicate lower range for salary low.'),
      '#required' => TRUE,
      '#default_value' => $config->get('low_range_low'),
    ];

    $form['low_range']['low_range_higher'] = [
      '#type' => 'number',
      '#placeholder' => $this->t('Higher range'),
      '#description' => $this->t('Indicate higher range for salary high.'),
      '#required' => TRUE,
      '#default_value' => $config->get('low_range_higher'),
    ];

    $form['middle_range'] = [
      '#type' => 'details',
      '#title' => t('Salary medium'),
      '#open' => TRUE,
      '#suffix' => "<p>" . $this->t('The high salary is considered from the next available value of the middle range.') . $this->t('When this field will be write the import button will be activate.') . "</p>",
    ];

    $form['middle_range']['middle_range_low'] = [
      '#type' => 'number',
      '#placeholder' => $this->t('Lower range'),
      '#description' => $this->t('Indicate lower range for salary medium.'),
      '#required' => TRUE,
      '#default_value' => $config->get('middle_range_low'),
    ];

    $form['middle_range']['middle_range_high'] = [
      '#type' => 'number',
      '#placeholder' => $this->t('Higher range'),
      '#description' => $this->t('Indicate higher range for salary medium.'),
      '#required' => TRUE,
      '#default_value' => $config->get('middle_range_high'),
    ];

    $form['actions']['submit_import'] = [
      '#type' => 'submit',
      '#disabled' => TRUE,
      '#description' => $this->t('First, you should assing endpoint.'),
      '#value' => t('Import Employees'),
      '#submit' => ['::batchImportEmployee'],
    ];

    if ($config->get('endpoint') != '' and $config->get('low_range_low') != '' and
    $config->get('low_range_higher') != '' and $config->get('middle_range_low') != '' and
    $config->get('middle_range_high') != '') {
      $form['actions']['submit_import']['#disabled'] = FALSE;
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function batchImportEmployee(array &$form, FormStateInterface $form_state) {
    $batch = [
      'title' => t('Import employees...'),
      'operations' => [],
      'init_message'     => t('Commencing'),
      'progress_message' => t('Processed @current out of @total.'),
      'error_message'    => t('An error occurred during processing'),
      'finished' => '\Drupal\employees\batch\ImportEmployees::importEmployeeFinishedCallback',
    ];
    $config = $this->config('employees.settings');
    $response = \Drupal::httpClient()->get($config->get('endpoint'));

    $json_string = (string) $response->getBody();
    $employees = json_decode($json_string);
    foreach ($employees->data as $employee) {
      $batch['operations'][] = ['\Drupal\employees\batch\ImportEmployees::importEmployee', [$employee]];
    }

    batch_set($batch);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $this->config('employees.settings')
      ->set('endpoint', $form_state->getValue('endpoint'))
      ->set('low_range_low', $form_state->getValue('low_range_low'))
      ->set('low_range_higher', $form_state->getValue('low_range_higher'))
      ->set('middle_range_low', $form_state->getValue('middle_range_low'))
      ->set('middle_range_high', $form_state->getValue('middle_range_high'))
      ->save();
  }

}
