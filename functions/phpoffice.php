<?php 

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}



use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

/**
 * Read comma separated string to terms
 * @param array $trow An array passed by reference
 * @return array
 */
function diwan_tax_terms(&$trow){
	$terms_string = array_shift($trow);
	
	$terms = [];
	
	if($terms_string != '' && !is_null($terms_string)){
		$terms = explode(',', $terms_string);
	
		$terms = array_map('trim', $terms);
	}
	
	
	return $terms;
}
/**
 * Reads an excel's file data
 * @param string $input_name File's input name
 * @return array An array of read data from an excel sheet
 */
function read_excel_data($input_name){
	
	if(empty($_FILES) || !isset($_FILES) || !isset($_FILES[$input_name]) || empty($_FILES[$input_name]['tmp_name']) ) return;
	
	$inputFileType = IOFactory::identify($_FILES[$input_name]['tmp_name']);
	
	$reader = PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
	
	//This is to not read empty rows
	//$reader->setReadDataOnly(true);
	$reader->setReadEmptyCells(false);
	
	$spreadsheet = $reader->load( $_FILES[$input_name]['tmp_name'] );
	$worksheet   = $spreadsheet->getActiveSheet();
	$rows        = $worksheet->toArray();
	
	
	
	//Remove Nulls (empty cells)
	/*$temp = [];
	foreach ($rows as $row) {
		$row = array_filter($row);
		
		if(!empty($row)) $temp[] = $row;
	}
	
	*/
	$temp = $rows;
	$rows = [];
	foreach ($temp as $trow) {
		$temp_2['title']    = array_shift($trow);
		$temp_2['interval'] = array_shift($trow);
		
		if(!empty($trow)){
			$temp_2['categories'] = diwan_tax_terms($trow);
		}
		
		if(!empty($trow)){
			$temp_2['tags'] = diwan_tax_terms($trow);
		}
		
		$rows[] = $temp_2;
		
		$temp_2 = [];
	}
		
	return $rows;
}














































