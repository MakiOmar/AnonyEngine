<?php
/**
 * PHP Time helpers class
 *
 * @package Anonymous theme
 * @author Makiomar
 * @link http://makiomar.com
 */
if ( ! class_exists( 'ANONY_TIME_HELP' ) ) {
	class ANONY_TIME_HELP extends ANONY_HELP {
		/**
		 * Calculate period of time
		 *
		 * @param  int    $time        the time you want to calculate.
		 * @param  string $time_type   the time type you want to calculate.
		 * @var    int      $period      the calculated time.
		 * @return int
		 */
		static function timeInSeconds( $time, $time_type = null ) {
			$second = 1;
			$minute = $second * 60;
			$hour   = $minute * 60;
			$day    = $hour * 24;
			$week   = $day * 7;
			$month  = $week * 4;
			$year   = $month * 12;

			switch ( $time_type ) {
				case 'second':
					$period = $time * $second;
					break;
				case 'minute':
					$period = $time * $minute;
					break;
				case 'hour':
					$period = $time * $hour;
					break;
				case 'day':
					$period = $time * $day;
					break;
				case 'week':
					$period = $time * $week;
					break;
				case 'month':
					$period = $time * $month;
					break;
				case 'year':
					$period = $time * $year;
					break;
				default:
					$period = 60;
			}

			return $period;
		}
	}
}
