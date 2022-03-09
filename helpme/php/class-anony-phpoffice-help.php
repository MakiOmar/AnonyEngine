<?php
/**
 * PHPOFFICE helpers
 *
 * PHP version 7.3 Or Later
 *
 * @package  AnonyEngine helpers
 * @author   Makiomar <info@makior.com>
 * @license  https://makiomar.com AnonyEngine Licence
 * @link     https://makiomar.com/anonyengine
 */

defined( 'ABSPATH' ) || die(); // Exit if accessed direct.

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

if ( ! class_exists( 'ANONY_PHPOFFICE_HELP' ) ) {
	/**
	 * PHPOFFICE helpers class
	 *
	 * @package    AnonyEngine helpers
	 * @author     Makiomar <info@makior.com>
	 * @license    https://makiomar.com AnonyEngine Licence
	 * @link       https://makiomar.com
	 */
	class ANONY_PHPOFFICE_HELP extends ANONY_HELP {

		/**
		 * Writes array content into xlsx spread sheet.
		 *
		 * @param  array $array Data array.
		 * @return void
		 */
		public static function array_to_spreadsheet( $array ) {

			// Create new Spreadsheet object
			$spreadsheet = new Spreadsheet();

			// Set document properties
			$spreadsheet->getProperties()->setCreator( 'Maarten Balliauw' )
				->setLastModifiedBy( 'Maarten Balliauw' )
				->setTitle( 'Office 2007 XLSX Test Document' )
				->setSubject( 'Office 2007 XLSX Test Document' )
				->setDescription( 'Test document for Office 2007 XLSX, generated using PHP classes.' )
				->setKeywords( 'office 2007 openxml php' )
				->setCategory( 'Test result file' );

			// Add some data
			$spreadsheet->setActiveSheetIndex( 0 )
				->setCellValue( 'A1', 'Hello' )
				->setCellValue( 'B2', 'world!' )
				->setCellValue( 'C1', 'Hello' )
				->setCellValue( 'D2', 'world!' );

			// Miscellaneous glyphs, UTF-8
			$spreadsheet->setActiveSheetIndex( 0 )
				->setCellValue( 'A4', 'Miscellaneous glyphs' )
				->setCellValue( 'A5', 'éàèùâêîôûëïüÿäöüç' );

			// Rename worksheet
			$spreadsheet->getActiveSheet()->setTitle( 'Simple' );

			// Set active sheet index to the first sheet, so Excel opens this as the first sheet
			$spreadsheet->setActiveSheetIndex( 0 );

			// Redirect output to a client’s web browser (Xlsx)
			header( 'Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' );
			header( 'Content-Disposition: attachment;filename="01simple.xlsx"' );
			header( 'Cache-Control: max-age=0' );
			// If you're serving to IE 9, then the following may be needed
			header( 'Cache-Control: max-age=1' );

			// If you're serving to IE over SSL, then the following may be needed
			header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' ); // Date in the past
			header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' ); // always modified
			header( 'Cache-Control: cache, must-revalidate' ); // HTTP/1.1
			header( 'Pragma: public' ); // HTTP/1.0

			$writer = IOFactory::createWriter( $spreadsheet, 'Xlsx' );
			$writer->save( 'php://output' );
			exit;

		}
	}
}
