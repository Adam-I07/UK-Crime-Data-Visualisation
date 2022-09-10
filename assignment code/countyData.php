<?php
//This class is created to get, store and handle data relating to counties.
class Counties
{
    public $countyName;
    public $totalCrime;

    public function __construct($county_Name, $total_Crime)
    {
        $this->countyName = $county_Name;
        $this->totalCrime = $total_Crime;
    }


    public function getCountyName()
    {
        return $this->countyName;
    }

    public function getTotalCrime()
    {
        return $this->totalCrime;
    }

}
?>