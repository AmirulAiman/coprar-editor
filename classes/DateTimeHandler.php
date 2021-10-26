<?php

class DateTimeHandler {
    //NOTE: Move datetime here later
    
    private $dt = new DateTime;
    private $datetime;
    
    function __construct($date_time, $format){
        try {
            $this->datetime = $this->dt->createFromFormat($format, $date_time);
        } catch (\Throwable $th) {
            die($th->getMessage());
        }
    }
    
    public function reformatDateTime($new_format){
        return $this->datetime->format($new_format);
    }
}

?>