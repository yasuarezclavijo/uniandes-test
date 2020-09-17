<?php

namespace Drupal\employees\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\taxonomy\Entity\Term;
use Drupal\Core\Link;
use Drupal\Core\Url;

/**
 * Taxonomy block.
 *
 * @Block(
 *   id = "salaries_block",
 *   admin_label = @Translation("Salaries block")
 * )
 */
class SalariesBlock extends BlockBase {

  /**
   * {@inheritdoc}
   *
   * This method sets the block default configuration. This configuration
   * determines the block's behavior when a block is initially placed in a
   * region. Default values for the block configuration form should be added to
   * the configuration array. System default configurations are assembled in
   * BlockBase::__construct() e.g. cache setting and block title visibility.
   *
   * @see \Drupal\block\BlockBase::__construct()
   */
  public function defaultConfiguration() {
    return [
      'taxonomySelect' => 'salary_types',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    // Name of your vocabulary.
    $links = [];
    $vocabulary_name = 'salary_types';
    $query = \Drupal::entityQuery('taxonomy_term');
    $query->condition('vid', $vocabulary_name);
    $query->sort('weight');
    $tids = $query->execute();
    $terms = Term::loadMultiple($tids);

    foreach ($terms as $term) {
      $name = $term->getName();;
      $url = Url::fromRoute('entity.taxonomy_term.canonical', ['taxonomy_term' => $term->id()]);
      $link = Link::fromTextAndUrl($name, $url);
      array_push($links, ['label' => $name, 'url' => $url]);
    }

    return [
      '#theme' => 'salaries_terms',
      '#links' => $links,
    ];
  }

}
