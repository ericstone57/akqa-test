<?php
/**
 * User: ericlee
 * Date: 9/28/14
 * Time: 11:39
 */
namespace AKQATest\Booking;

// assume default timezone
date_default_timezone_set('Asia/Shanghai');

class BookingTest extends \PHPUnit_Framework_TestCase {

  private $correctOutput = "2011-03-21\n09:00 11:00 EMP002\n2011-03-22\n11:00 12:00 EMP004\n14:00 16:00 EMP003\n";

  /**
   * @expectedException Exception
   */
  public function testExceptionFileNotExist() {
    new FileInput('data_not_exist.txt');
  }

  public function testFileEmpty() {
    $input = new FileInput('tests/assets/empty.txt');
    $this->assertEmpty($input->getContent());
  }

  public function testBookingEmpty() {
    $booking = new Booking('tests/assets/empty.txt');
    $this->assertEmpty($booking->getBooking()->getCalenderDateList());
  }

  /**
   * @expectedException Exception
   * @expectedExceptionMessage /^Wrong format, first line for company office hours/
   */
  public function testInputMissingOfficeHours() {
    new Booking('tests/assets/without_officehours.txt');
  }

  /**
   * @expectedException Exception
   * @expectedExceptionMessage /^Wrong format, company office hours format illegal/
   */
  public function testInputWrongFormatOfOfficeHours() {
    new Booking('tests/assets/wrong_officehours.txt');
  }

  /**
   * @expectedException Exception
   * @expectedExceptionMessage /^Wrong format, first line for company office hours/
   */
  public function testMissingData1() {
    new Booking('tests/assets/missing_submission1.txt');
  }

  /**
   * @expectedException Exception
   * @expectedExceptionMessage /^Wrong format, request submission time format illegal/
   */
  public function testMissingData2() {
    new Booking('tests/assets/missing_submission2.txt');
  }

  /**
   * @expectedException Exception
   * @expectedExceptionMessage /^Wrong format, meeting start time format illegal/
   */
  public function testMissingData3() {
    new Booking('tests/assets/missing_submission3.txt');
  }

  /**
   * @expectedException Exception
   * @expectedExceptionMessage /^Wrong format, request submission time format illegal/
   */
  public function testSubmissionDataDateWrong() {
    new Booking('tests/assets/missing_submission4.txt');
  }

  /**
   * @expectedException Exception
   * @expectedExceptionMessage /^Wrong format, request submission time format illegal/
   */
  public function testSubmissionDataTimeWrong() {
    new Booking('tests/assets/missing_submission5.txt');
  }

  /**
   * @expectedException Exception
   * @expectedExceptionMessage /^Wrong format, request submission time format illegal/
   */
  public function testSubmissionDataEmployeeWrong() {
    new Booking('tests/assets/missing_submission6.txt');
  }

  /**
   * @expectedException Exception
   * @expectedExceptionMessage /^Wrong format, meeting start time format illegal/
   */
  public function testMeetingStartDateWrong() {
    new Booking('tests/assets/missing_submission7.txt');
  }

  /**
   * @expectedException Exception
   * @expectedExceptionMessage /^Wrong format, meeting start time format illegal/
   */
  public function testMeetingStartTimeWrong() {
    new Booking('tests/assets/missing_submission8.txt');
  }

  /**
   * @expectedException Exception
   * @expectedExceptionMessage /^Wrong format, meeting start time format illegal/
   */
  public function testMeetingHoursWrong() {
    new Booking('tests/assets/missing_submission9.txt');
  }

  public function testMeetingOverOfficeEndHours() {
    $booking = new Booking('tests/assets/missing_submission10.txt');
    $this->assertEmpty($booking->getBooking()->getCalenderDateList());
  }

  public function testBookingWithCorrectOrder1() {
    $booking = new Booking('tests/assets/order1.txt');
    $this->assertEquals($booking->__toString(), $this->correctOutput);
  }

  public function testBookingWithCorrectOrder2() {
    $booking = new Booking('tests/assets/order2.txt');
    $this->assertEquals($booking->__toString(), $this->correctOutput);
  }

  public function testBookingWithCorrectOrder3() {
    $booking = new Booking('tests/assets/order3.txt');
    $this->assertEquals($booking->__toString(), $this->correctOutput);
  }
}