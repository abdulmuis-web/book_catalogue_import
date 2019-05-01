<?php

namespace Drupal\book_catalogue_import\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Class DatabaseImportController.
 */
class DatabaseImportController extends ControllerBase {

  /**
   * Database_import.
   *
   * @return string
   *   Return Hello string.
   */
  public function database_import() {
    return [
      '#type' => 'markup',
      '#markup' => $this->t('Implement method: database_import')
    ];
  }
  /**
   * Csv_file_import.
   *
   * @return string
   *   Return Hello string.
   */
  public function csv_file_import() {
    return [
      '#type' => 'markup',
      '#markup' => $this->t('Implement method: csv_file_import')
    ];
  }
  /**
   * Json_file_import.
   *
   * @return string
   *   Return Hello string.
   */
  public function json_file_import() {
    return [
      '#type' => 'markup',
      '#markup' => $this->t('Implement method: json_file_import')
    ];
  }

}
