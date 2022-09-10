<?php
//This class is created to get, store and handle the data inputted by the user to add a new crime case.
class AddNewCrime
{
    public $countyName;
    public $crimeType;

    public function __construct($county_Name, $crime_Type)
    {
        $this->countyName = $county_Name;
        $this->crimeType = $crime_Type;
    }

    public function getCountyName()
    {
        return $this->countyName;
    }

    public function getCrimeType()
    {
        return $this->crimeType;
    }

}
?>