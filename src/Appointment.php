<?php
/**
 * User: ericlee
 * Date: 9/28/14
 * Time: 21:09
 */
namespace AKQATest\Booking;

/**
 * Appointment instance
 *
 * Class Appointment
 * @package AKQATest\Booking
 */
class Appointment {
  /**
   * date time from
   *
   * @var \DateTime
   */
  private $from;

  /**
   * date time to
   *
   * @var \DateTime
   */
  private $to;

  /**
   * appointment made by
   *
   * @var string
   */
  private $by;


  /**
   * @param \DateTime $from
   * @param \DateTime $to
   * @param string $by
   */
  public function __construct(\DateTime $from, \DateTime $to, $by) {
    $this->from = $from;
    $this->to = $to;
    $this->by = $by;
  }

  /**
   * return date time from
   *
   * @return \DateTime
   */
  public function getFrom() {
    return $this->from;
  }

  /**
   * return date time to
   *
   * @return \DateTime
   */
  public function getTo() {
    return $this->to;
  }

  /**
   * return formatted FROM string, as 09:15
   *
   * @return string
   */
  public function getFromString() {
    return $this->from->format('H:i');
  }

  /**
   * return formatted TO string, as 17:35
   *
   * @return string
   */
  public function getToString() {
    return $this->to->format('H:i');
  }

  /**
   * @return string|string
   */
  public function getBy() {
    return $this->by;
  }

}