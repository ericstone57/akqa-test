<?php
/**
 * User: ericlee
 * Date: 9/27/14
 * Time: 16:40
 */
namespace AKQATest\Booking;

/**
 * Class FileInput
 * @package AKQATest\Booking
 */
class FileInput implements Input {

  /**
   * path of file
   *
   * @var filename
   */
  private $filename;

  /**
   * content of file
   *
   * @var content
   */
  private $content;

  /**
   * @param $filename
   * @throws InputException
   */
  public function __construct($filename) {
    // set path
    $this->filename = $filename;
    // read it
    $this->_read();
  }

  /**
   * file content
   *
   * @return content
   */
  public function getContent() {
    return $this->content;
  }

  /**
   * Ensure file is exist, is regular file and readable
   *
   * @return bool
   */
  private function _isFileExist() {
    return is_file($this->filename) && is_readable($this->filename);
  }

  /**
   * @return bool
   * @throws InputException
   */
  private function _read() {
    if (!$this->_isFileExist()) {
      throw new InputException('The file not exist or un-readable.');
    }

    $this->content = file_get_contents($this->filename);

    if ($this->content === FALSE) {
      throw new InputException('The file read failure.');
    }
  }

}