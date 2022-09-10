<?php
//This class is created to get, store and handle data relating to total amount of crime relating to all counties
class crimeSpecific
{
    public $countyName;
    public $totalCases;

    public function __construct($county_Name, $total_Cases)
    {
        $this->countyName = $county_Name;
        $this->totalCases = $total_Cases;
    }

    public function getCountyName()
    {
        return $this->countyName;
    }

    public function getTotalCases()
    {
        return $this->totalCases;
    }

}
?>