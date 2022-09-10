<?php
	//Calling all the classes that are going to be used in this class
    require "dbinfo.php";
    require "RestService.php";
    require "countySpecific.php";
	require "countyData.php";
	require "crimeSpecific.php";
	require "SpecificCrimeData.php";
	require "AddNewCrime.php";
 
class CrimeRestService extends RestService 
{
	//local variables to be used
	private $countySpecificData;
	private $counties;
	private $crimeSpecificData;
	private $specificCountyCrimeData;
    
	public function __construct() 
	{
		
	}

	/*If the user wants to get anything from the database this function is called. Within this function 
	there is a switch statement which seperates all requests by the number of parameters in the request recieved.
	depending on the amount the request is sent to a case. Inside the case there is either code that completes
	the request by getting the data requested or more validation to get the request to the right destination.
	The job of the switch statement is to call the correct function and send the right requested data back to the 
	front end*/
	public function performGet($url, $parameters, $requestBody, $accept) 
	{
		switch (count($parameters))
		{
			case 1:
				header('Content-Type: application/json; charset=utf-8');
				header('no-cache,no-store');
				$this->getCounties();
				echo json_encode($this->counties);
				break;
		
			case 2:
				header('Content-Type: application/json; charset=utf-8');
				header('no-cache,no-store');
				$this->getCountiesSpecificCrimeData();
				echo json_encode($this->specificCountyCrimeData);
				break;
			case 3:
				if($parameters[1] == "countyName")
				{
					$countySelected = $parameters[2];
					header('Content-Type: application/json; charset=utf-8');
					header('no-cache,no-store');
					$this->getCountySpecificData($countySelected);
					echo json_encode($this->countySpecificData);
					break;
				}
				else if($parameters[1] == "crimeType")
				{
					$crimeType = $parameters[2];
					header('Content-Type: application/json; charset=utf-8');
					header('no-cache,no-store');
					$this->getCrimeSpecificDate($crimeType);
					echo json_encode($this->crimeSpecificData);
					break;
				}
				else if($parameters[1] == "specificCountyData")
				{
					$inputCrimeID = $parameters[2];
					$inputCrimeData = $this->getCrimeCountySpecificData($inputCrimeID);
					if ($inputCrimeData != null)
					{
						header('Content-Type: application/json; charset=utf-8');
						header('no-cache,no-store');
						echo json_encode($inputCrimeData);
						break;
					}
					else
					{
						$this->notFoundResponse();
						break;
					}
					
				}
				else 
				{
					$this->methodNotAllowedResponse();
				}

			default:	
				$this->methodNotAllowedResponse();

			
		}
	
	}

	/*Function is called when the user wants to add or edit anything in the database. Works similarly to get
	depending on the length of the request it preforms case 1 or 2.*/
	public function performPut($url, $parameters, $requestBody, $accept)
	{
		global $dbserver, $dbusername, $dbpassword, $dbdatabase;

		switch (count($parameters))
		{
			case 1:
				/*If the request sent by the user only contains one parameter this is executed. The code first extracts the data recieved and 
				assigns it as local variables to use. The code gets the current total for the county they wish to edit and the current total cases relating to the 
				specific cirme type chosen of the county. Which is then ammended to the new data recieved and stored in the database.*/
				$crimeEditData = $this->extractEditCrimeDataFromJSON($requestBody);
				$connection = new mysqli($dbserver, $dbusername, $dbpassword, $dbdatabase);
				if(!$connection->connect_error)
				{
					$currentCountyTotalCrime;
					$currentCountyCrimeSpecificTotalCases;
					$currentCrimeID = $crimeEditData->getCrimeID();
					$currentCountyName = $crimeEditData->getCountyName();
					$currentCrimeType = $crimeEditData->getCrimeType();
					$currentTotalCases = $crimeEditData->getTotalCases();

					$sqlGetTotalCrimeData = "select totalCrime from counties where countyName = ?";
					$statement = $connection->prepare($sqlGetTotalCrimeData);
					$statement->bind_param('s', $currentCountyName);
					$statement->execute();
					$statement->store_result();
					$statement->bind_result($databaseTotalCrime);
					if($statement -> fetch())
					{
						$currentCountyTotalCrime = $databaseTotalCrime;
					}
					$statement->close();

					$sqlGetCountyCrimeSepecificData = "select totalCases from crimeData where crimeID = ?";
					$statement = $connection->prepare($sqlGetCountyCrimeSepecificData);
					$statement->bind_param('i', $currentCrimeID);
					$statement->execute();
					$statement->store_result();
					$statement->bind_result($databaseSpecificTotalCases);
					if($statement -> fetch())
					{
						$currentCountyCrimeSpecificTotalCases = $databaseSpecificTotalCases;
					}
					$statement->close();

					$newTotalCountyCrime = $currentCountyTotalCrime;
					$newTotalCountyCrime = $currentCountyTotalCrime - $currentCountyCrimeSpecificTotalCases;
					$newTotalCountyCrime = $newTotalCountyCrime + $currentTotalCases;
					$sqlUpdateCountiesSpecificCrime = "update counties set totalCrime = ? where countyName = ?";
					$statement = $connection->prepare($sqlUpdateCountiesSpecificCrime);
					$statement->bind_param('is', $newTotalCountyCrime, $currentCountyName);
					$result = $statement->execute();
					if ($result == FALSE)
					{
						$errorMessage = $statement->error;
					}
					$statement->close();
					if ($result == TRUE)
					{
						// We need to return the status as 204 (no content) rather than 200 (OK) since
						// we are not returning any data
						$this->noContentResponse();
					}
					else
					{
						$this->errorResponse($errorMessage);
					}

					$sqlNewCountySpecificCrime = "update crimeData set totalCases = ? where crimeID = ?";
					$statement = $connection->prepare($sqlNewCountySpecificCrime);
					$statement->bind_param('ii', $currentTotalCases, $currentCrimeID);
					$statement->execute();
					if ($result == FALSE)
					{
						$errorMessage = $statement->error;
					}
					$statement->close();
					$connection->close();
					if ($result == TRUE)
					{
						// We need to return the status as 204 (no content) rather than 200 (OK) since
						// we are not returning any data
						$this->noContentResponse();
					}
					else
					{
						$this->errorResponse($errorMessage);
					}
					break;
				}
			case 2:
				/*If the request has 2 parameters this code is implemented. The code first extracts the data that is recieved, this code is written to add a new case.
				To add a case the the total crime statistics relating to the county selected is pulled from the database and so is the specific data to the crime chosen.
				The system then adds a new case and sends the new valued back to the database to be stored.*/
				$newCrimeData = $this->extractAddCrimeDataFromJSON($requestBody);
				$connection = new mysqli($dbserver, $dbusername, $dbpassword, $dbdatabase);
				if(!$connection->connect_error)
				{
					$newUserAddedCrime = $newCrimeData->getCrimeType();
					$newUserAddedCounty = $newCrimeData->getCountyName();
					$currentCountyTotalCrime;
					$currentCountyCrimeSpecificTotalCases;

					$sqlGetTotalCrimeData = "select totalCrime from counties where countyName = ?";
					$statement = $connection->prepare($sqlGetTotalCrimeData);
					$statement->bind_param('s', $newUserAddedCounty);
					$statement->execute();
					$statement->store_result();
					$statement->bind_result($databaseTotalCrime);
					if($statement -> fetch())
					{
						$currentCountyTotalCrime = $databaseTotalCrime;
					}
					$statement->close();

					$sqlGetCountyCrimeSepecificData = "select totalCases from crimeData where countyName = ? AND crimeType = ?";
					$statement = $connection->prepare($sqlGetCountyCrimeSepecificData);
					$statement->bind_param('ss', $newUserAddedCounty, $newUserAddedCrime);
					$statement->execute();
					$statement->store_result();
					$statement->bind_result($databaseSpecificTotalCases);
					if($statement -> fetch())
					{
						$currentCountyCrimeSpecificTotalCases = $databaseSpecificTotalCases;
					}
					$statement->close();


					$newTotalCountyCrime = $currentCountyTotalCrime + 1;
					$sqlUpdateCountiesSpecificCrime = "update counties set totalCrime = ? where countyName = ?";
					$statement = $connection->prepare($sqlUpdateCountiesSpecificCrime);
					$statement->bind_param('is', $newTotalCountyCrime, $newUserAddedCounty);
					$result = $statement->execute();
					if ($result == FALSE)
					{
						$errorMessage = $statement->error;
					}
					$statement->close();
					if ($result == TRUE)
					{
						// We need to return the status as 204 (no content) rather than 200 (OK) since
						// we are not returning any data
						$this->noContentResponse();
					}
					else
					{
						$this->errorResponse($errorMessage);
					}

					$newTotalCrime = $currentCountyCrimeSpecificTotalCases + 1;
					$sqlNewCountySpecificCrime = "update crimeData set totalCases = ? where countyName = ? AND crimeType = ?";
					$statement = $connection->prepare($sqlNewCountySpecificCrime);
					$statement->bind_param('iss', $newTotalCrime, $newUserAddedCounty, $newUserAddedCrime);
					$statement->execute();
					if ($result == FALSE)
					{
						$errorMessage = $statement->error;
					}
					$statement->close();
					$connection->close();
					if ($result == TRUE)
					{
						// We need to return the status as 204 (no content) rather than 200 (OK) since
						// we are not returning any data
						$this->noContentResponse();
					}
					else
					{
						$this->errorResponse($errorMessage);
					}
					break;
				}
			default:
				$this->methodNotAllowedResponse();
			
		}
		
	}

	/*If the user requests crime specific data this function is executed. The function gets the county name and crime data for the 
	crime type chosen by the user and stores it for use*/
	private function getCrimeSpecificDate($crime)
    {
		global $dbserver, $dbusername, $dbpassword, $dbdatabase;
	
		$connection = new mysqli($dbserver, $dbusername, $dbpassword, $dbdatabase);
		if (!$connection->connect_error)
		{
			$query = "select countyName, totalCases from crimeData where crimeType = ?";
			$statement = $connection->prepare($query);
			$statement->bind_param('s', $crime);
			$statement->execute();
			$statement->store_result();
			$statement->bind_result($countyName, $totalCases);	
			while ($statement -> fetch())
			{
				$this->crimeSpecificData[] = new crimeSpecific($countyName, $totalCases);
			}
			$statement->close();
			$connection->close();
		}
	}

	/*If the user requests county specific crime data this function is executed. The function gets the crime type add total cases for the 
	county chosen by the user and stores it for use*/
    private function getCountySpecificData($countySelected)
    {
		global $dbserver, $dbusername, $dbpassword, $dbdatabase;
	
		$connection = new mysqli($dbserver, $dbusername, $dbpassword, $dbdatabase);
		if (!$connection->connect_error)
		{
			$query = "select crimeType, totalCases from crimeData where countyName = ?";
			$statement = $connection->prepare($query);
			$statement->bind_param('s', $countySelected);
			$statement->execute();
			$statement->store_result();
			$statement->bind_result($crimeType, $totalCases);	
			while ($statement -> fetch())
			{
				$this->countySpecificData[] = new countySpecific($crimeType, $totalCases);
			}
			$statement->close();
			$connection->close();
		}
	}	 

	//This function gets all the current data of all the counties in the database from the Counties table and stores it for use. 
	private function getCounties()
	{
		global $dbserver, $dbusername, $dbpassword, $dbdatabase;
	
		$connection = new mysqli($dbserver, $dbusername, $dbpassword, $dbdatabase);
		if (!$connection->connect_error)
		{
			$query = "select countyName, totalCrime from counties";
			if ($result = $connection->query($query))
			{
				while ($row = $result->fetch_assoc())
				{
					$this->counties[] = new Counties($row["countyName"], $row["totalCrime"]);
				}
				$result->close();
			}
			$connection->close();
		}
	}

	//Gets all the data of all the specific crime data stored for all counties in the database from the crimeData table and stores it for use.
	private function getCountiesSpecificCrimeData()
	{
		global $dbserver, $dbusername, $dbpassword, $dbdatabase;
	
		$connection = new mysqli($dbserver, $dbusername, $dbpassword, $dbdatabase);
		if (!$connection->connect_error)
		{
			$query = "select crimeID, countyName, crimeType, totalCases from crimeData";
			if ($result = $connection->query($query))
			{
				while ($row = $result->fetch_assoc())
				{
					$this->specificCountyCrimeData[] = new SpecificCrimeData($row['crimeID'] ,$row["countyName"], $row["crimeType"], $row["totalCases"]);
				}
				$result->close();
			}
			$connection->close();
		}
	}

	/*Takes the ID that is inherited and asks the database for all information relating to that ID stored. Once recieved is stored and 
	assigned to SpecificCrimeData, then is returned to be sent to the frontend.*/
	private function getCrimeCountySpecificData($id)
	{
		global $dbserver, $dbusername, $dbpassword, $dbdatabase;
		
		$connection = new mysqli($dbserver, $dbusername, $dbpassword, $dbdatabase);
		if (!$connection->connect_error)
		{
			$query = "select countyName, crimeType, totalCases from crimeData where crimeID = ?";
			$statement = $connection->prepare($query);
			$statement->bind_param('i', $id);
			$statement->execute();
			$statement->store_result();
			$statement->bind_result($countyName, $crimeType, $totalCases);
			if ($statement->fetch())
			{
				return new SpecificCrimeData($id, $countyName, $crimeType, $totalCases);
			}
			else
			{
				return null;
			}
			$statement->close();
			$connection->close();
		}
	}

	// The following functions are needed because of the perculiar way json_decode works. 
	// By default, it will decode an object into a object of type stdClass.  There is no
	// way in PHP of casting a stdClass object to another object type.  So we use the
	// approach of decoding the JSON into an associative array (that's what the second
	// parameter set to true means in the call to json_decode). Then we create a new
	// object using the elements of the associative array.
	private function extractEditCrimeDataFromJSON($requestBody)
	{
		$crimeDataArray = json_decode($requestBody, true);
		$newCrimeCase = new SpecificCrimeData($crimeDataArray['crimeID'],
						$crimeDataArray['countyName'],
						$crimeDataArray['crimeType'],
						$crimeDataArray['totalCases']);
		unset($crimeDataArray);
		return $newCrimeCase;
	}
	
	private function extractAddCrimeDataFromJSON($requestBody)
	{
		$crimeData = json_decode($requestBody, true);
		$newCrimeCaseData = new AddNewCrime($crimeData['countyName'],
						$crimeData['crimeType']);
		unset($crimeData);
		return $newCrimeCaseData;
	}

}
?>
