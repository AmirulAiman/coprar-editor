<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;

class SpreadsheetHandler implements IReadFilter{
    //NOTE: mode spreadsheet handler here later
    private $dir, $fileType, $spreadSheets, $activeSheet, $endRow;
    private $startRow = 1;
    private $columns = [];
    private $acceptable_file_type = [];
    // private $acceptable_extention = array('xls','csv','xlsx');
    
    function __construct($dir, $acceptable_file_type, $startRow = null, $endRow = null, $columns = null, $readOnly = true){
        $this->dir = $dir;
        $this->$acceptable_file_type = $acceptable_file_type;
        $this->fileType = IOFactory::identify($dir);

        $reader = IOFactory::createReader($this->fileType);
        $reader->setReadDataOnly($readOnly);

        $this->spreadheets = $reader->load($dir);
        $this->activeSheet->getActiveSheet();

        //If the sheet has row where it need to start read and untill which row and columns.
        if($startRow != null){
            $this->startRow = $startRow;
        }
        if($endRow != null){
            $this->endRow = $endRow;
        }
        
        if($columns != null){
            $this->columns = $columns;
        }
        //
    }
    
    public function getFileType()
    {
        return $this->fileType;
    }
    
    
    function getSheetDetail()
    {
        $active_sheet = $this->activeSheet;
        $detail = [
            'report' => $active_sheet->getCell('B2')->getValue()
            , 'line_code' => $active_sheet->getCell('B3')->getValue()
            , 'vesssel_name' => $active_sheet->getCell('B4')->getValue()
            , 'eta' => format_date($active_sheet->getCell('B5')->getValue())
            , 'tsl' => $active_sheet->getCell('D3')->getValue()
            , 'voyage' => $active_sheet->getCell('D4')->getValue()
            // , 'voyage_scn_opr' => [
            //     'voyage' => explode('/',$active_sheet->getCell('D4')->getValue()),
            //     , 'scn' => explode('/',$active_sheet->getCell('D4')->getValue()),
            //     , 'opr' => explode('/',$active_sheet->getCell('D4')->getValue())
            // ]
            , 'enquire_by' => $active_sheet->getCell('D5')->getValue()
            , 'yard' => [
                'open' => $active_sheet->getCell('B6')->getValue()
                , 'close' => $active_sheet->getCell('D6')->getValue()
            ]
        ];
        
        return $detail;
    }
    
    function readCell($column, $row, $worksheetName = ''){
        //Check if the cell in the pre-defined 
        if($this->endRow != null){
            if($row >= $this->startRow && $row <= $this->endRow){
                if(in_array($column, $this->columns)){
                    return true;
                }
            }
            return false;
        } else {
            return true;
        }
    }
}
?>