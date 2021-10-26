<?php
    require 'vendor/autoload.php';
    require 'classes/SpreadsheetHandler.php';
    
    use PhpOffice\PhpSpreadsheet\IOFactory;
    use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;

    try{
        session_start();
        if(isset($_POST['submit'])){
            $data = [
                'status' => true,
                'message' => [],
                'errors' => []
            ];
            array_push($data['message'], 'Process start');
            
            $receiver_code = $_POST['receiver_code'];
            $callsign_code = $_POST['callsign_code'];
            $excel_sheet = $_FILES['excel_file'];
            $date = date('Y/m/d H:m:s');
            $timestamp = new DateTime($date);
            
            //Move upload file from temp to desired loc.
            $file = 'uploads/'.basename($excel_sheet['name']);
            move_uploaded_file($excel_sheet['tmp_name'], $file);

            //Check if file exist
            if(!isset($excel_sheet) && $excel_sheet['name'] == ''){
                array_push($data['message'], 'Process failed');
                array_push($data['errors'], 'No file uploaded/found');
                $data['status'] = false;
                header("location: index.php");
            }

            $file_type = IOFactory::identify($file);
            $acceptable_extention = array('xls','csv','xlsx');

            if(!in_array(strtolower($file_type), $acceptable_extention)){
                array_push($data['message'], 'Process failed. File type: '.$file_type);
                array_push($data['errors'], 'File type not in '.implode(',', $acceptable_extention));
                $data['status'] = false;
                // header("location: index.php");
            } else {
                
                $reader = IOFactory::createReader($file_type); //Create reader for selected file type.
                $reader->setReadDataOnly(true); //Set read only.
                array_push($data['message'], "Created reader for file type > $file_type");

                $spreadsheet = $reader->load($file);
                $active_sheet = $spreadsheet->getActiveSheet();
                
                $details = array();
                
                $report         = $active_sheet->getCell('B2')->getValue();
                $line_code      = $active_sheet->getCell('B3')->getValue();
                $vessel_name    = $active_sheet->getCell('B4')->getValue();
                $eta            = $active_sheet->getCell('B5')->getValue();
                $ts_local       = $active_sheet->getCell('D3')->getValue();
                $voyage_scn_opr = explode('/',$active_sheet->getCell('D4')->getValue());
                $voyage         = $voyage_scn_opr[0];
                $scn            = $voyage_scn_opr[1];
                $opr            = $voyage_scn_opr[2];
                $enquired_by    = $active_sheet->getCell('D5')->getValue();
                $yard_open      = date($active_sheet->getCell('B6')->getValue());
                $yard_close     = date($active_sheet->getCell('D6')->getValue());
                $coprar_version = 'COPRAR:D:00B:UN:SMDG21';
                
                array_push($data['message'], "Begin creating header...");
                $header = "";
                $header .= "UNB+UNOA:2+KMT+$receiver_code+".$timestamp->format('Ymd:Hms')."+".$timestamp->format('YmdHms')."'".PHP_EOL;
                $header .= "UNH+".date('YmdHms')."$coprar_version+LOADINGCOPRAR'".PHP_EOL;
                $header .= "BGM+45+".date('YmdHms',strtotime($report))."+5'".PHP_EOL;
                $header .= "TDT+20+$voyage+1++172:$opr+++$callsign_code:103::$vessel_name'".PHP_EOL;
                $header .= "RFF+VON:$voyage'".PHP_EOL;
                $header .= "NAD+CA+$line_code'".PHP_EOL;
                
                $details['header'] = $header;
                
                array_push($data['message'], "Done creating header!");
                array_push($data['message'], "Begin creating body...");
                
                $rows = range(9,45);//Based on the row the package start.
                foreach($rows as $index => $row){
                    $body[$index] = array();
                    $order_ref          = $active_sheet->getCell("A$row")->getValue() ?? '';
                    $container          = $active_sheet->getCell("B$row")->getValue() ?? '';
                    $box                = $active_sheet->getCell("C$row")->getValue() ?? '';
                    $box_status         = $active_sheet->getCell("D$row")->getValue() ?? '';
                    $fcl_lcl            = $active_sheet->getCell("E$row")->getValue() ?? '';
                    $spod               = $active_sheet->getCell("F$row")->getValue() ?? '';
                    $pod                = $active_sheet->getCell("G$row")->getValue() ?? '';
                    $iso                = $active_sheet->getCell("H$row")->getValue() ?? '';
                    $lg                 = $active_sheet->getCell("I$row")->getValue() ?? '';
                    $hg                 = $active_sheet->getCell("J$row")->getValue() ?? '';
                    $type               = $active_sheet->getCell("K$row")->getValue() ?? '';
                    $ts                 = $active_sheet->getCell("L$row")->getValue() ?? '';
                    $commodity          = $active_sheet->getCell("M$row")->getValue() ?? '';
                    $gross_weight       = $active_sheet->getCell("N$row")->getValue() ?? '';
                    $dg                 = $active_sheet->getCell("O$row")->getValue() ?? '';
                    $temp               = $active_sheet->getCell("P$row")->getValue() ?? '';
                    $operation_refer    = $active_sheet->getCell("Q$row")->getValue() ?? '';
                    $oog                = $active_sheet->getCell("R$row")->getValue() ?? '';
                    $storage_indicator  = $active_sheet->getCell("S$row")->getValue() ?? '';
                    $pol                = $active_sheet->getCell("T$row")->getValue() ?? '';
                    $in_date            = $active_sheet->getCell("U$row")->getValue() ?? '';
                    $out_date           = $active_sheet->getCell("V$row")->getValue() ?? '';
                    $yard_location      = $active_sheet->getCell("W$row")->getValue() ?? '';
                    $custom_block       = $active_sheet->getCell("X$row")->getValue() ?? '';
                    $remarks            = $active_sheet->getCell("Y$row")->getValue() ?? '';
                    $seal_no            = $active_sheet->getCell("Z$row")->getValue() ?? '';
                    $bl_numbers         = $active_sheet->getCell("AA$row")->getValue() ?? '';
                    $slot_owner         = $active_sheet->getCell("AB$row")->getValue() ?? '';
                    
                    array_push($body[$index],"EQN+CN+$container+$iso:102:5++2+5'".PHP_EOL);
                    array_push($body[$index],"LOC+11+$spod+136:6'".PHP_EOL);
                    array_push($body[$index],"LOC+7+$pod:139:6'".PHP_EOL);
                    array_push($body[$index],"LOC+9+$pol:139:6'".PHP_EOL);
                    array_push($body[$index],"MEA+AAE+VGM+KGM:$gross_weight'".PHP_EOL);
                    array_push($body[$index],"FTX+AAI+++40'".PHP_EOL);
                    array_push($body[$index],"FTX+AAA+++$commodity'".PHP_EOL);
                    array_push($body[$index],"FTX+HAN++$storage_indicator'".PHP_EOL);
                    array_push($body[$index],"NAD+CF+'$line_code:160:ZZZ'".PHP_EOL);
                }
                $details['body'] = $body;
                array_push($data['message'], "Done creating body!");
                
                array_push($data['message'], "Begin creating footer...");
                $footer = array();
                array_push($footer,"CNT+16:39'".PHP_EOL);
                array_push($footer,"UNT+318+".$timestamp->format('YmdHms')."'".PHP_EOL);
                array_push($footer,"UNZ+1+".$timestamp->format('YmdHms')."'".PHP_EOL);
                $details['footer'] = $footer;
                array_push($data['message'], "Done creating footer!");
                
                $data['details'] = $details;
            }
            $_SESSION['data'] = $data;
            header("location: index.php");
        } else {
            session_destroy();
        }
    } catch(Exception $ex) {
        array_push($data['message'], 'Process failed');
        array_push($data['errors'], $ex->getMessage());
        die($ex->getMessage());
    }
?>