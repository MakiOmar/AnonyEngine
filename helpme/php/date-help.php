<?php
/**
 * PHP Date helpers class
 *
 * @package Anonymous theme
 * @author Makiomar
 * @link http://makiomar.com
 */
if ( ! class_exists( 'ANONY_DATE_HELP' ) ) {
	class ANONY_DATE_HELP extends ANONY_HELP{
		/**
		 * Check if valid date
		 * @param string $date 
		 * @return boolean true on success otherwise false
		 */
		static function isDate($date){
			// date example mm-dd-year -> 09-25-2012
			$datechunks = explode("-",$date);
			if(sizeof($datechunks)==3){
				if(is_numeric($datechunks[0]) && is_numeric($datechunks[1]) && is_numeric($datechunks[2]))
				{
					// now check if its a valid date
					if(checkdate($datechunks[0], $datechunks[1], $datechunks[2])){
					return true;
					}else{
					return false;
					}

				}else{
					return false;
				}
			}
			
			return false;
		}
		
		/**
		 * Convert date/time from timezone to another
		 *
		 * @param  string   $date         required to be converted.
		 * @param  string   $timezone     current timezone to be converted from.
		 * @param  string   $timezone_to  current timezone to be converted to.
		 * @param  string   $format       format to be converted to.
		 * @return string                 converted date.
		 */	
		static function convertDateFromTimezone($date,$timezone,$timezone_to,$format='Y-m-d H:i'){
			
			$date = new DateTime($date,new DateTimeZone($timezone));

			$date->setTimezone( new DateTimeZone($timezone_to) );

			return $date->format($format);
		}

		/**
		 * Get formated date/time difference e.g. 2 days and 3 hours and 40 minutes and 25 seconds
		 *
		 * @param  string   $timeStamp         the timestamp you want to calculate difference from.
		 * @param  string   $timezone          timezone you want to use for conversion.
		 * @var    object   $setTimeZone       an object of DateTimeZone.
		 * @var    string   $converted_date    Store formated timestamp.
		 * @var    object   $date              object of formated date/time according to timezone.
		 * @var    object   $current_date      object of current date/time.
		 * @var    string   $diff              stors formated date/time difference.
		 * @return string                      
		 */
		static function getTimeDifference($timeStamp, $time_zone){
			$setTimeZone = new DateTimeZone($time_zone);
			
			$converted_date = date("Y-m-d H:i:s", $timeStamp);
				
			
			$date = DateTime::createFromFormat ('Y-m-d H:i:s', $converted_date, $setTimeZone);

			$current_date = new DateTime();
			
			$diff = $current_date->diff($date)->format("%a days and %H hours and %i minutes and %s seconds");
			
			return $diff;
		}
		
		/**
		 * Calculate the difference between to dates in days.
		 * 
		 * @param  string $new_date Date/Time string
		 * @param  string $old_date Date/Time string
		 * @param  int    $round    The optional number of decimal digits to round to.(Round percision).
		 * @return float            The date difference
		 */
		static function dateDiffInDays($new_date, $old_date, $round = 3){
			$new_date_timestamp = strtotime($new_date);
			$old_date_timestamp = strtotime($old_date);
			
			$date_diff = $new_date_timestamp - $old_date_timestamp;
			
			return round($date_diff / (60 * 60 * 24), $round);
		}
	}
}