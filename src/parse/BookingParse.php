<?php
/**
 * User: ericlee
 * Date: 9/28/14
 * Time: 17:11
 */
namespace AKQATest\Booking;

/**
 * Parse booking data from input string
 *
 * Class BookingParse
 * @package AKQATest\Booking
 */
class BookingParse {

  /**
   * string from input source
   *
   * @var string bookingString
   */
  private $bookingString;

  /**
   * array convert from $bookingString
   *
   * @var array $lines
   */
  private $lines;

  /**
   * office start hours digital, like 09
   *
   * @var string $officeStartHour
   */
  private $officeStartHour;

  /**
   * office start minutes digital, like 30
   *
   * @var string $officeStartMinutes
   */
  private $officeStartMinutes;

  /**
   * office end hours, like 17
   *
   * @var string $officeEndHour
   */
  private $officeEndHour;

  /**
   * office end minutes, like 59
   *
   * @var string $officeEndMinutes
   */
  private $officeEndMinutes;

  /**
   * array for original booking request, with validated
   *
   * @var array $bookingRequest
   */
  private $bookingRequest = array();

  /**
   * the calendar
   *
   * @var array $calendar
   */
  private $calendar = array();


  /**
   * input source as only parameter
   *
   * @param $string
   */
  public function __construct($string) {
    $this->bookingString = $string;
    $this->parse();
  }

  public function getCalenderDateList() {
    return array_keys($this->calendar);
  }

  public function getCalenderAppointmentsByDate($date) {
    return $this->calendar[$date];
  }

  /**
   * process input source from string to legal calendar array
   */
  private function parse() {
    $this->bookingString = trim($this->bookingString);
    if (!empty($this->bookingString)) {
      $this->explodeString();
      $this->validate();
      // the generator
      $this->calendarGenerate();
    }
  }


  /**
   * generate calendar one by one
   */
  private function calendarGenerate() {
    // sort order of request
    usort($this->bookingRequest, array($this, '_sortRequestBySubmissionTime'));
    // make appointment, first come first get
    // TODO optimize compute way, too many loops
    foreach($this->bookingRequest as $request) {
      $meetingDate = $request['start']->format('Y-m-d');
      $meetingFromTo = $this->_calcFromToTime($request['start'], $request['hours']);

      // have no booking for the date yet
      if (!isset($this->calendar[$meetingDate])) {
        $this->calendar[$meetingDate][] = new Appointment($meetingFromTo['from'], $meetingFromTo['to'], $request['employee']);
      }
      else {
        // loop exists calendar
        foreach ($this->calendar[$meetingDate] as $appointment) {
          // book it if empty found
          if (!($meetingFromTo['from'] >= $appointment->getFrom() && $meetingFromTo['to'] <= $appointment->getTo())) {
            $this->calendar[$meetingDate][] = new Appointment($meetingFromTo['from'], $meetingFromTo['to'], $request['employee']);
          }
        }
      }
    }

    // sort calendar
    uksort($this->calendar, array($this, '_sortCalenderByDate'));
    // sort appointments
    foreach ($this->calendar as &$appointments) {
      usort($appointments, array($this, '_sortCalenderAppointmentByDate'));
    }
  }

  /**
   * convert string to lines array
   *
   * @throws BookingParseException
   */
  private function explodeString() {
    $this->lines = explode("\n", $this->bookingString);

    // lines in file, it should be odd number following request
    if (count($this->lines) % 2 == 0) {
      throw new BookingParseException("Wrong format, first line for company office hours, then repeat with request submission time and meeting start time. Please check!");
    }
  }

  /**
   * validation
   *  1/ format wise
   *  2/ meeting need in office hours
   */
  private function validate() {
    $this->validateOfficeHoursFormat();
    $this->validateBookingRequestFormat();
    $this->validateBookingRequestWithinOfficeHours();
  }

  /**
   * validate office hours, by following format as below:
   *  0900 1730
   * @throws BookingParseException
   */
  private function validateOfficeHoursFormat() {
    if (preg_match('/^([01][0-9]|2[0-3])([0-5][0-9])\s+([01][0-9]|2[0-3])([0-5][0-9])$/', $this->lines[0], $matches)) {
      $this->officeStartHour = $matches[1];
      $this->officeStartMinutes = $matches[2];
      $this->officeEndHour = $matches[3];
      $this->officeEndMinutes = $matches[4];
    }
    else {
      throw new BookingParseException("Wrong format, company office hours format illegal");
    }
  }

  /**
   * validate the booking request, following format as below:
   *  2011-03-17 10:17:06 EMP001
   *  2011-03-21 09:00 2
   *
   *  // TODO need validate date like 02/30
   *
   * @throws BookingParseException
   */
  private function validateBookingRequestFormat() {
    // format 2011-03-17 10:17:06 EMP001
    $requestSubmissionPattern = '/^(((?:19|20)\d\d)-(0[1-9]|1[0-9]|2[0-3])-(0[1-9]|[12][0-9]|3[01])\s+([01][0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9]))\s+(EMP\d{3})$/';
    // format 2011-03-21 09:00 2
    // TODO only full hour accept at now, could improve late, like 1.5 hours is reasonable
    $meetingStartTimePattern = '/^(((?:19|20)\d\d)-(0[1-9]|1[0-9]|2[0-3])-(0[1-9]|[12][0-9]|3[01])\s+([01][0-9]|2[0-3]):([0-5][0-9]))\s+(\d)$/';

    $lines = $this->lines;
    array_shift($lines);
    $requestChunk = array_chunk($lines, 2);

    foreach ($requestChunk as $key => $value) {
      // request submission time
      if (preg_match($requestSubmissionPattern, $value[0], $matches)) {
        $this->bookingRequest[$key]['submission'] = \DateTime::createFromFormat('Y-m-d H:i:s', $matches[1]);
        $this->bookingRequest[$key]['employee'] = $matches[8];
      } else {
        throw new BookingParseException("Wrong format, request submission time format illegal");
      }
      // meeting start time
      if (preg_match($meetingStartTimePattern, $value[1], $matches)) {
        $this->bookingRequest[$key]['start'] = \DateTime::createFromFormat('Y-m-d H:i', $matches[1]);
        $this->bookingRequest[$key]['hours'] = $matches[7];
      } else {
        throw new BookingParseException("Wrong format, meeting start time format illegal");
      }
    }
  }

  /**
   * validate meeting start time, which need within office hours
   */
  private function validateBookingRequestWithinOfficeHours() {
    foreach ($this->bookingRequest as $key => $request) {
      $meetingFromTo = $this->_calcFromToTime($request['start'], $request['hours']);
      $date = $request['start']->format('Y-m-d');
      $officeStart = \DateTime::createFromFormat('Y-m-d Hi', $date . ' ' . $this->officeStartHour . $this->officeStartMinutes);
      $officeEnd = \DateTime::createFromFormat('Y-m-d Hi', $date . ' ' . $this->officeEndHour . $this->officeEndMinutes);

      // unset illegal request
      if (!($meetingFromTo['from'] >= $officeStart && $meetingFromTo['to'] <= $officeEnd)) {
        unset($this->bookingRequest[$key]);
      }
    }
  }

  /**
   * Calculate meeting duration by start time and hours
   *
   * @param \DateTime $start
   * @param $hours
   * @return array
   */
  private function _calcFromToTime(\DateTime $start, $hours) {
    $to = clone $start;
    $to->add(new \DateInterval("PT{$hours}H"));

    return array(
      'from' => $start,
      'to' => $to
    );
  }

  /**
   * internal function, to sort request by submission time, from early to late
   *
   * @param $a
   * @param $b
   * @return int
   */
  private static function _sortRequestBySubmissionTime($a, $b) {
    return ($a['submission'] > $b['submission']) ? 1 : -1;
  }


  /**
   * internal function, sort the calendar
   *
   * @param $a
   * @param $b
   * @return int
   */
  private static function _sortCalenderByDate($a, $b) {
    return ($a > $b) ? 1 : -1;
  }

  /**
   * internal function, sort the appointment
   *
   * @param $a
   * @param $b
   * @return int
   */
  private static function _sortCalenderAppointmentByDate($a, $b) {
    return ($a->getFrom() > $b->getFrom()) ? 1 : -1;
  }
}