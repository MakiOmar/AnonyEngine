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

            $spreadsheet = new Spreadsheet();
            $spreadsheet->setActiveSheetIndex(0);
            $spreadsheet->getActiveSheet()->fromArray($array, NULL, 'A1');
            $spreadsheet->getActiveSheet()->setTitle("Spread Sheet");

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="test.xlsx"');
            header('Cache-Control: max-age=0');
            header('Cache-Control: max-age=1');
            header('Cache-Control: cache, must-revalidate');
            header('Pragma: public');
            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save('php://output');

        }
    }
}