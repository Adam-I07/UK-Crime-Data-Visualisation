<?php
//This class is created to get, store and handle data relating to specific crimes.
class SpecificCrimeData
{
    public $crimeID;
    public $countyName;
    public $crimeType;
    public $totalCases;

    public function __construct($crime_ID, $county_Name, $crime_Type, $total_Cases)
    {
        $this->crimeID = $crime_ID;
        $this->countyName = $county_Name;
        $this->crimeType = $crime_Type;
        $this->totalCases = $total_Cases;
    }

    public function getCrimeID()
    {
        return $this->crimeID;
    }

    public function getCountyName()
    {
        return $this->countyName;
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