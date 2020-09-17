<?php

namespace Drupal\employees\batch;

use Drupal\node\Entity\Node;

/**
 * Class batch.
 */
class ImportEmployees {

  /**
   * Function batch.
   */
  public static function importEmployee($employee, &$context) {
    $config = \Drupal::config('employees.settings');
    $message = 'Create employee...';
    $results = [];
    $content = [
      'type' => 'employee',
      'moderation_state' => 'published',
      'title' => $employee->employee_name,
      'field_age' => [
        'value' => $employee->employee_age,
      ],
      'field_name' => [
        'value' => $employee->employee_name,
      ],
      'field_remote_id' => [
        'value' => $employee->id,
      ],
    ];

    if ($employee->employee_salary >= $config->get('low_range_low') and $employee->employee_salary <= $config->get('low_range_higher')) {
      $salaryName = 'Salario Bajo';
    }
    elseif ($employee->employee_salary >= $config->get('middle_range_low') and $employee->employee_salary <= $config->get('middle_range_higher')) {
      $salaryName = 'Salario Medio';
    }
    else {
      $salaryName = 'Salario Alto';
    }
    $terms = \Drupal::entityManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'salary_types', 'name' => $salaryName]);
    $term = reset($terms);
    $content['field_salary_type'] = [
      [
        'target_id' => $term->id(),
      ]
    ];
    $nid = \Drupal::entityQuery('node')
      ->condition('field_remote_id', $employee->id)
      ->execute();
    // Create nid.
    if (!$nid) {
      // Create a new node.
      $entity = Node::create($content);
      $entity->save();
      // Save the node.
      $results[] = $employee->employee_name;
    }
    else {
      $nid = array_pop($nid);
      $entity = Node::load($nid);
      $entity->setTitle($employee->employee_name);
      $entity->set('field_age', $employee->employee_age);
      $entity->set('field_name', $employee->employee_name);
      $entity->set('field_salary_type', $term->id());
      $entity->save();
      $results[] = $employee->employee_name;
    }
    $context['message'] = $nid;
    $context['results'] = $results;
  }

  /**
   * Callback finished.
   */
  public function importEmployeeFinishedCallback($success, $results, $operations) {
    // The 'success' parameter means no fatal PHP errors were detected. All
    // other error management should be handled using 'results'.
    if ($success) {
      $message = \Drupal::translation()->formatPlural(
        count($results),
        'Employees processed.', '@count posts processed.'
      );
    }
    else {
      $message = t('Finished with an error.');
    }
    drupal_set_message($message);
  }

}
