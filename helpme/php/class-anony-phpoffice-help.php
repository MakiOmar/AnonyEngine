<?php
/**
 * PHPOFFICE helpers
 *
 * PHP version 7.3 Or Later
 *
 * @package  AnonyEngine helpers
 * @author   Makiomar <info@makiomar.com>
 * @license  https://makiomar.com AnonyEngine Licence.
 * @link     https://makiomar.com/anonyengine.
 */

defined( 'ABSPATH' ) || die(); // Exit if accessed direct..

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

if ( ! class_exists( 'ANONY_PHPOFFICE_HELP' ) ) {
	/**
	 * PHPOFFICE helpers class.
	 *
	 * @package    AnonyEngine helpers
	 * @author     Makiomar <info@makiomar.com>
	 * @license    https://makiomar.com AnonyEngine Licence.
	 * @link       https://makiomar.com.
	 */
	class ANONY_PHPOFFICE_HELP extends ANONY_HELP {

		/**
		 * Writes array content into xlsx spread sheet.
		 * Please note: If the spreadsheet will contain arabic user: $wpdb->set_charset($wpdb->dbh, 'utf8', 'utf8_general_ci') before you get the data from the database.
		 *
		 * @param  array $arr Data array.
		 * @param  mixed $report_headers Header array or false.
		 * @return void
		 */
		public static function array_to_spreadsheet( $arr, $report_headers = false ) {

			// Create new Spreadsheet object.
			$spreadsheet = new Spreadsheet();

			// Set document properties.
			$spreadsheet->getProperties()->setCreator( 'Reporter' )
				->setLastModifiedBy( 'Reporter' )
				->setTitle( 'Office 2007 XLSX Test Document' )
				->setSubject( 'Office 2007 XLSX Test Document' )
				->setDescription( 'Report' )
				->setKeywords( 'office 2007 openxml php' )
				->setCategory( 'Test result file' );

			// Add some data.
			$sheet = $spreadsheet->setActiveSheetIndex( 0 )->setCellValue( 'A1', 'Report ' . gmdate( 'Y-m-d' ) );

			// Get the active sheet.
			$sheet = $spreadsheet->getActiveSheet();

			// Set headers.
			if ( $report_headers && is_array( $report_headers ) ) {

				$header_row = 2;
				$col        = 'A';
				foreach ( $report_headers as $header ) {
					$sheet->setCellValue( $col . $header_row, $header );
					$sheet->getStyle( $col . $header_row )->applyFromArray(
						array(
							'font' => array(
								'bold'  => true,
								'color' => array( 'rgb' => 'FFFFFF' ),
							),
							'fill' => array(
								'fillType'   => Fill::FILL_SOLID,
								'startColor' => array( 'rgb' => '4CAF50' ),
							),
						)
					);
					$sheet->getColumnDimension( $col )->setAutoSize( true );
					++$col;
				}
			}

			// Set data and formatting.
			$data_row = 3;
			foreach ( $arr as $row_data ) {
				$col = 'A';
				foreach ( $row_data as $value ) {
					$sheet->setCellValue( $col . $data_row, $value );
					$sheet->getStyle( $col . $data_row )->applyFromArray(
						array(
							'alignment' => array( 'horizontal' => Alignment::HORIZONTAL_CENTER ),
							'borders'   => array(
								'allBorders' => array(
									'borderStyle' => Border::BORDER_THIN,
									'color'       => array( 'rgb' => '000000' ),
								),
							),
						)
					);
					++$col;
				}
				++$data_row;
			}

			// Autofit columns to content.
			foreach ( range( 'A', $sheet->getHighestColumn() ) as $column ) {
				$sheet->getColumnDimension( $column )->setAutoSize( true );
			}

			// Rename worksheet.
			$spreadsheet->getActiveSheet()->setTitle( 'Orders Report' );

			// Set active sheet index to the first sheet, so Excel opens this as the first sheet.
			$spreadsheet->setActiveSheetIndex( 0 );

			// Redirect output to a client’s web browser (Xlsx).
			header( 'Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' );
			header( 'Content-Disposition: attachment;filename="Report-' . gmdate( 'Y-m-d' ) . '.xlsx"' );
			header( 'Cache-Control: max-age=0' );
			// If you're serving to IE 9, then the following may be needed.
			header( 'Cache-Control: max-age=1' );

			// If you're serving to IE over SSL, then the following may be needed.
			header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' ); // Date in the past.
			header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' ); // always modified.
			header( 'Cache-Control: cache, must-revalidate' ); // HTTP/1.1.
			header( 'Pragma: public' ); // HTTP/1.0.

			$writer = IOFactory::createWriter( $spreadsheet, 'Xlsx' );
			$writer->save( 'php://output' );
			exit;
		}
		/**
		 * Writes array content into xlsx spreadsheet, appending to the file if it exists or creating a new one if it doesn't.
		 * Please note: If the spreadsheet will contain arabic user: $wpdb->set_charset($wpdb->dbh, 'utf8', 'utf8_general_ci') before you get the data from the database.
		 *
		 * @param  array  $arr Data array.
		 * @param  mixed  $report_headers Header array or false.
		 * @param  string $title Spreadsheet title.
		 * @param  string $file_path File path.
		 * @return void
		 */
		public static function array_to_spreadsheet_append( $arr, $report_headers = false, $title = 'Spreadsheet', $file_path = false ) {
			$file_name = 'Report-' . gmdate( 'Y-m-d' ) . '.xlsx';
			if ( ! $file_path ) {
				$file_path = __DIR__ . '/' . $file_name;
			}

			// Check if the file exists.
			if ( file_exists( $file_path ) ) {
				// Load the existing spreadsheet.
				$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load( $file_path );
			} else {
				// Create a new spreadsheet.
				$spreadsheet = new Spreadsheet();
				// Set document properties.
				$spreadsheet->getProperties()->setCreator( 'Reporter' )
					->setLastModifiedBy( 'Reporter' )
					->setTitle( 'Office 2007 XLSX Test Document' )
					->setSubject( 'Office 2007 XLSX Test Document' )
					->setDescription( 'Report' )
					->setKeywords( 'office 2007 openxml php' )
					->setCategory( 'Test result file' );

				// Add a title for a new spreadsheet.
				$spreadsheet->setActiveSheetIndex( 0 )->setCellValue( 'A1', $title . ' ' . gmdate( 'Y-m-d' ) );
			}

			// Get the active sheet.
			$sheet = $spreadsheet->getActiveSheet();

			// Get the current highest row to append new data.
			$highest_row = $sheet->getHighestRow();

			// If the spreadsheet is new, start from row 2, otherwise start after the last row.
			$data_row = $highest_row + 1;

			// Set headers if we are creating a new file (i.e., when the current highest row is 1).
			if ( $report_headers && is_array( $report_headers ) && 1 === $highest_row ) {
				$header_row = 2;
				$col        = 'A';
				foreach ( $report_headers as $header ) {
					$sheet->setCellValue( $col . $header_row, $header );
					$sheet->getStyle( $col . $header_row )->applyFromArray(
						array(
							'font'      => array(
								'bold'  => true,
								'color' => array( 'rgb' => 'FFFFFF' ),
							),
							'fill'      => array(
								'fillType'   => Fill::FILL_SOLID,
								'startColor' => array( 'rgb' => '4CAF50' ),
							),
							'alignment' => array(
								'horizontal' => Alignment::HORIZONTAL_CENTER, // Centering the header text.
							),
						)
					);
					$sheet->getColumnDimension( $col )->setAutoSize( true );
					++$col;
				}
				$data_row = $header_row + 1;
			}

			// Set data and formatting.
			foreach ( $arr as $row_data ) {
				$col = 'A';
				foreach ( $row_data as $value ) {
					if ( ! is_string( $value ) ) {
						$value = '';
					}
					// Ensure the value contains proper line breaks for Excel.
					$value = str_replace( '\n', "\n", $value ); // Replace literal '\n' with actual new lines.
					// Set cell value explicitly to allow multi-line content. You can use setCellValue but may not replace new line \n.
					$sheet->setCellValueExplicit( $col . $data_row, $value, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING );
					$sheet->getStyle( $col . $data_row )->applyFromArray(
						array(
							'alignment' => array(
								'horizontal' => Alignment::HORIZONTAL_CENTER,
								'vertical'   => Alignment::VERTICAL_CENTER,
							),
							'borders'   => array(
								'allBorders' => array(
									'borderStyle' => Border::BORDER_THIN,
									'color'       => array( 'rgb' => '000000' ),
								),
							),
						)
					)->getAlignment()->setWrapText( true );
					++$col;
				}
				++$data_row;
			}

			// Autofit columns to content.
			foreach ( range( 'A', $sheet->getHighestColumn() ) as $column ) {
				$sheet->getColumnDimension( $column )->setAutoSize( true );
			}

			// Rename worksheet (only set if the sheet was new).
			if ( 1 === $highest_row ) {
				$spreadsheet->getActiveSheet()->setTitle( $title );
			}

			// Set active sheet index to the first sheet, so Excel opens this as the first sheet.
			$spreadsheet->setActiveSheetIndex( 0 );

			// Save the spreadsheet to file (append or create).
			$writer = IOFactory::createWriter( $spreadsheet, 'Xlsx' );
			$writer->save( $file_path );
		}

		/**
		 * Reads an excel's file data.
		 *
		 * @param string $input_name File's input name.
		 * @return array An array of read data from an excel sheet.
		 */
		public static function read_excel_data( $input_name ) {
			//phpcs:disable
			$sent_files = wp_unslash( $_FILES );
			//phpcs:enable
			if ( empty( $sent_files ) || ! isset( $sent_files ) || ! isset( $sent_files[ $input_name ] ) || empty( $sent_files[ $input_name ]['tmp_name'] ) ) {
				return;
			}

			$input_file_type = IOFactory::identify( $sent_files[ $input_name ]['tmp_name'] );

			$reader = PhpOffice\PhpSpreadsheet\IOFactory::createReader( $input_file_type );

			// This is to not read empty rows.
			// $reader->setReadDataOnly(true);.
			$reader->setReadEmptyCells( false );

			$spreadsheet = $reader->load( $sent_files[ $input_name ]['tmp_name'] );
			$worksheet   = $spreadsheet->getActiveSheet();
			$rows        = $worksheet->toArray();

			// Remove Nulls (empty cells).
			$temp = array();
			foreach ( $rows as $row ) {
				$row = array_filter( $row );

				if ( ! empty( $row ) ) {
					$temp[] = $row;
				}
			}

			$rows = array();

			foreach ( $temp as $trow ) {
				$rows[] = array_shift( $trow );
			}
			return $rows;
		}
	}
}
