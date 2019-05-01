<?php

namespace Drupal\book_catalogue_import\Controller;

use Drupal\book_author\Entity\Author;
use Drupal\book_publisher\Entity\Publisher;
use Drupal\book_catalogue\Entity\Book;
use Drupal\Core\Controller\ControllerBase;

/**
 * Class DatabaseImportController.
 */
class DatabaseImportController extends ControllerBase {

  /**
   * Collect.
   *
   * @return object
   *   Return book_catalogue object.
   */
  public function collect(){

	$connection = \Drupal::database();
	$query = $connection->select('book_catalogue_import', 'd');	
	$query->condition('d.Pengarang','=', 'IS NOT NULL');
	$query->condition('d.Penerbit','=', 'IS NOT NULL');
	$query->condition('b.name','=', 'IS NULL');
	$query->leftjoin('book_catalogue', 'b', 'd.Judul = b.name');
	$query->leftjoin('book_author', 'a', 'd.Pengarang = a.name');
	$query->leftjoin('book_publisher', 'p', 'd.Penerbit = p.name');
	$query->fields('d', ['Judul', 'Penerbit', 'Pengarang', 'ISBN', 'Website', 'Email', 'Tahun']);
	$query->fields('a', ['id']);
	$query->fields('p', ['id']);
	$query->range(0, 3);
	$query = $query->execute();
	$result = $query->fetchAll();
	
	return $result;
  }

  /**
   * getPublisher.
   *
   * @return object
   *   Return book_cpublisher object.
   */
  public function getPublisher($data){
	$query = \Drupal::entityQuery('publisher')
    ->condition('status', 1)
    ->condition('name', $data->Penerbit)
	->range(0, 1);
	$publisher = $query->execute();
	$return_data = 'Publisher';
    if(!$publisher){
	  $publisher = $this->createPublisher($data);
	  $return_data = $publisher->id();
	}
    return $return_data;
  }	  

  /**
   * createPublisher.
   *
   * @return object
   *   Return book_cpublisher object.
   */
  public function createPublisher($data){
	$database = \Drupal::database();
	$transaction = $database->startTransaction();
	try {  
	  $values = array();
	  $values['name'] = $data->Penerbit;
	  if($data->Email){
	    $values['email'] = $data->Email;
	  }
	  if($data->Website){
		$values['website']['uri'] = $data->Website;
		$values['website']['title'] = $data->Website;
	  }

	  $publisher = Publisher::create($values);
	  $publisher->save();
	}
	catch (\Exception $e) {
	  $transaction->rollback();
	  $publisher = NULL;
	  watchdog_exception('book_catalogue_import', $e, $e->getMessage());
	  throw new \Exception(  $e->getMessage(), $e->getCode(), $e->getPrevious());
	}
    return $publisher;
  }
  
  /**
   * getAuthor.
   *
   * @return object
   *   Return book_author object.
   */
  public function getAuthor($data){
	$query = \Drupal::entityQuery('author')
    ->condition('status', 1)
    ->condition('name', $data->Pengarang)
	->range(0,1);
	$author = $query->execute();
	$return_data = 'Author';
    if(!$author){
	  $author = $this->createAuthor($data);
	  $return_data = $author->id();
	}
    return $return_data;
  }
 
  /**
   * createAuthor.
   *
   * @return object
   *   Return book_author object.
   */
  public function createAuthor($data){
	$database = \Drupal::database();
	$transaction = $database->startTransaction();
	try {  
	  $values = array();
	  $values['name'] = $data->Pengarang;
	  $author = Author::create($values);
	  $author->save();
	}
	catch (\Exception $e) {
	  $transaction->rollback();
	  $author = NULL;
	  watchdog_exception('book_catalogue_import', $e, $e->getMessage());
	  throw new \Exception(  $e->getMessage(), $e->getCode(), $e->getPrevious());
	}
    return $author;
  }

  /**
   * createBook.
   *
   * @return object
   *   Return book_catalogue object.
   */
  public function createBook($data){
	$database = \Drupal::database();
	$transaction = $database->startTransaction();
	try {  
	  $values = array();
	  $values['name'] = $data->Judul;
	  $values['year'] = $data->Tahun;
	  $values['series'] = $data->Seri;
	  $values['isbn'] = $data->ISBN;
	  if($data->p_id){
	    $values['publisher'] = $data->p_id;
	  }
	  if($data->id){
	    $values['author'] = $data->id;
	  }
	  $book = Book::create($values);
	  $book->save();
	}
	catch (\Exception $e) {
	  $transaction->rollback();
	  $book = NULL;
	  watchdog_exception('book_catalogue_import', $e, $e->getMessage());
	  throw new \Exception(  $e->getMessage(), $e->getCode(), $e->getPrevious());
	}
    return $book;
  }
  
  /**
   * Database_import.
   *
   * @return string
   *   Return Hello string.
   */
  public function database_import() {
	$records = $this->collect();
    foreach($records as $data){
		if(!isset($data->p_id)){
			$publisher = $this->getPublisher($data);
			if($publisher){
			  $data->p_id = $publisher;
			}
		}
		if(!isset($data->id)){
			$author = $this->getAuthor($data);
			if($author){
			  $data->id = $author;
			}
		}
		$book = $this->createBook($data);
	}
	
	
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
