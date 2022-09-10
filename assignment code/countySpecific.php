<?php
//This class is created to get, store and handle data relating to individual crime statistics of counties.
class countySpecific
{
    public $crimeType;
    public $totalCases;

    public function __construct($crime_Type, $total_Cases)
    {
        $this->crimeType = $crime_Type;
        $this->totalCases = $total_Cases;
    }

    public function getCrimeType()
    {
        return $this->crimeType;
    }

    public function getTotalCases()
    {
        return $this->totalCases;
    }

}
?>
