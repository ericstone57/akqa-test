<?php
/**
 * User: ericlee
 * Date: 9/27/14
 * Time: 16:30
 */
namespace AKQATest\Booking;

/**
 * Class Booking
 * @package AKQATest\Booking
 */
class Booking {

  /**
   * source input
   *
   * @var FileInput
   */
  private $input;

  /**
   * booking object
   *
   * @var BookingParse
   */
  private $booking;

  /**
   * @param $input
   */
  public function __construct($input) {
    $this->input = new FileInput($input);
    $this->booking = new BookingParse($this->input->getContent());
  }

  /**
   * output as formatted
   * like:
   *  2011-03-21
   *    09:00 11:00 EMP002
   *  2011-03-22
   *    11:00 12:00 EMP004
   *    14:00 16:00 EMP003
   */
  public function output() {
    print $this->__toString();
  }

  public function __toString() {
    $string = '';
    foreach ($this->booking->getCalenderDateList() as $date) {
      $string .= $date . "\n";
      foreach ($this->booking->getCalenderAppointmentsByDate($date) as $appointment) {
        $string .= $appointment->getFromString() . ' ' . $appointment->getToString() . ' ' . $appointment->getBy() . "\n";
      }
    }
    return $string;
  }

  public function getBooking() {
    return $this->booking;
  }

}
