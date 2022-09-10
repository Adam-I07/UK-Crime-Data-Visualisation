//---------------------------------Visualisation for the Home Page "index.html"----------------------------------------------
//Creates the Get Request to get all the data for total Cases and sends it to the Bar Chart function to create the visualisation
function getCountiesData()
{
    $.ajax({
        url: '/counties',
        type: 'GET',
        cache: false,
        dataType: 'json',
        success: function (data) {
            createCountyBarChart(data);
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert(jqXHR + '\n' + textStatus + '\n' + errorThrown);
        }
    });
}

//Creates the Barchart to show the Counties total crime data
function createCountyBarChart(data)
{
    
    var countyNameStore = [];
    var totalCrimeStore = [];
    var barColours = [];
    
    /*This for-loop goes through all the data recieved and pushes the appropriate data 
    to the appropriate array to be stored in and used later on.*/
    for (var i =0; i < data.length; i++)
    {
        countyNameStore.push(data[i].countyName);
        totalCrimeStore.push(data[i].totalCrime);
    }

    /*This loop gets the amount of bars to be displayed and assigns them one of the colours 
    from the colours store below*/
    for (var n = 0; n < countyNameStore.length; n++)
    {
        colours = ["#b91d47", "#00aba9", "#2b5797","#e8c3b9","#1e7145", "#7B68EE", "#FFFF00", "#D2691E", "#000000", "#808080"];
        var randColour = getRandomItem(colours);
        barColours.push(randColour);
        
    }
    
    //This piece of code generates the bar chart and sets the values using the arrays generated above
    new Chart("barChart", {
        type: "bar",
        data: {
          labels: countyNameStore,
          datasets: [{
            backgroundColor: barColours,
            data: totalCrimeStore
          }]
        },
        options: {
          legend: {display: false},
          title: {
            display: true,
            text: "Total Crime Data within the England and Wales between July 2019 - June 2021"
          }
        }
    });
}
//---------------------------------------End of Visualisation for the Home Page "index.html"--------------------------------------------------



//---------------------------------Start of Visualisation for County Specfic Page "CountySpecificData.html"-----------------------------------
/*Creates the Get Request to get all the data for the counties selected by the user, once the data is recieved
it is sent to the functions below to create the visualisation*/
function searchCountySpecificData()
{
    //gets the value of the combo box from the html
    var selectedCounty = document.getElementById('countySelected').value;

    $.ajax({
        url: '/crimeData/countyName/' + selectedCounty,
        type: 'GET',
        cache: false,
        dataType: 'json',
        success: function (data) {
            createSpecificCountyBarchart(data);
            createSpecificCountyPiechart(data);
            createSpecificCountyLineGraph(data);
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert(jqXHR + '\n' + textStatus + '\n' + errorThrown);
        }
    }); 
}

//This function takes the data recieved from the back end and uses it to generate a bar chart
function createSpecificCountyBarchart(data)
{
    var selectedCounty = document.getElementById('countySelected').value;

    var crimeTypeStore = [];
    var totalCasesStore = [];
    var barColours = [];

    /*When ever this loop is ran this following piece of code goes into the html and creates a fresh unused canvas
    so the previously displayed data is removed and wont be displayed*/
    document.getElementById("barChartContainer").innerHTML = '&nbsp;';
    document.getElementById("barChartContainer").innerHTML = '<canvas id="barChart" style="width:100%;"></canvas>';
    document.getElementById("barChartLineSeperater").innerHTML = '<p> <hr style="height:1px; width:100%; border-width:0; color:black; background-color:black"> </p>';


    for (var i =0; i < data.length; i++)
    {
        crimeTypeStore.push(data[i].crimeType);
        totalCasesStore.push(data[i].totalCases);
    }


    for (var n = 0; n < crimeTypeStore.length; n++)
    {
        colours = ["#b91d47", "#00aba9", "#2b5797","#e8c3b9","#1e7145", "#7B68EE", "#FFFF00", "#D2691E", "#000000", "#808080"];
        var randColour = getRandomItem(colours);
        barColours.push(randColour);
        
    }

    new Chart("barChart", {
        type: "bar",
        data: {
          labels: crimeTypeStore,
          datasets: [{
            backgroundColor: barColours,
            data: totalCasesStore
          }]
        },
        options: {
          legend: {display: false},
          title: {
            display: true,
            text: selectedCounty + " Crime Data"
          }
        }
    });
}

//Using the data sent by the backend this following piece of code creates a pie chart with it
function createSpecificCountyPiechart(data)
{
    var selectedCounty = document.getElementById('countySelected').value;
    var crimeTypeStore = [];
    var totalCasesStore = [];
    var barColours = [];

    

    document.getElementById("pieChartContainer").innerHTML = '&nbsp;';
    document.getElementById("pieChartContainer").innerHTML = '<canvas id="pieChart" style="width:100%;">';


    for (var i = 0; i < data.length; i++)
    {
        crimeTypeStore.push(data[i].crimeType);
        totalCasesStore.push(data[i].totalCases);
    }

    for (var n = 0; n < crimeTypeStore.length; n++)
    {
        colours = ["#b91d47", "#00aba9", "#2b5797","#e8c3b9","#1e7145", "#7B68EE", "#FFFF00", "#D2691E", "#000000", "#808080"];
        var randColour = getRandomItem(colours);
        barColours.push(randColour);
    }

    //A black line is placed between this visualisation and the one above to seperate them
    document.getElementById("pieChartLineSeperater").innerHTML = '<p> <hr style="height:1px; width:100%; border-width:0; color:black; background-color:black"> </p>';

    new Chart("pieChart", 
    {
        type: "pie",
        data: 
        {
            labels: crimeTypeStore,
            datasets: [{
            backgroundColor: barColours,
            data: totalCasesStore
            }]
        },
        options: 
        {
            title: 
            {
            display: true,
            text: selectedCounty + " Crime Data"
            }
        }
    });
}

//The following function creates a line graph with the data recieved from the database
function createSpecificCountyLineGraph(data)
{
    var crimeTypeStore = [];
    var totalCasesStore = [];

    document.getElementById("lineGraphContainer").innerHTML = '&nbsp;';
    document.getElementById("lineGraphContainer").innerHTML = '<canvas id="lineGraph" style="width:100%;">';
    
    document.getElementById("lineChartLineSeperater").innerHTML = '<p> <hr style="height:1px; width:100%; border-width:0; color:black; background-color:black"> </p>';

    for (var i = 0; i < data.length; i++)
    {
        crimeTypeStore.push(data[i].crimeType);
        totalCasesStore.push(data[i].totalCases);
    }

    new Chart("lineGraph", 
    {
        type: "line",
        data: 
        {
          labels: crimeTypeStore,
          datasets: 
          [{
            fill: false,
            lineTension: 0,
            backgroundColor: "rgba(0,0,255,1.0)",
            borderColor: "rgba(255,0,255,0.1)",
            data: totalCasesStore
          }]
        },
        options: 
        {
          legend: {display: false},
          scales: 
          {
            yAxes: [{ticks: {min: Math.min.apply(Math, totalCasesStore), max:Math.max.apply(Math, totalCasesStore), autoSkipPadding: 300}}],
          }
        }
    });
}
//-----------------------------------------End of Visualisation for the County Specfic Page "CountySpecificData.html"---------------------------------------


//-----------------------------------------Start of Visualisation for Crime Specfic Page "CrimeSpecificData.html"--------------------------------------------
/*Creates the Get Request to get all the data for the crime type selected by the user, once the data is recieved
it is sent to the functions below to create the visualisation*/
function searchCrimeSpecificData()
{
    var selectedCrime = document.getElementById('crimeSelection').value;
    $.ajax({
        url: '/crimeData/crimeType/' + selectedCrime,
        type: 'GET',
        cache: false,
        dataType: 'json',
        success: function (data) {
            createSpecficCrimeBarChart(data);
            createSpecficCrimeDonutChart(data);
            createSpecficCrimeScatterPlotChart(data)
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert(jqXHR + '\n' + textStatus + '\n' + errorThrown);
        }
    });
}

//This function creates a hotizontal bar chart with the data that is recieved
function createSpecficCrimeBarChart(recievedData)
{
    
    var totalCases = [];
    var countyName = [];
    

    for (var i = 0; i < recievedData.length; i++)
    {
        countyName.push(recievedData[i].countyName);
        totalCases.push(recievedData[i].totalCases);
    }

    document.getElementById("barChartLineSeperater").innerHTML = '<p> <hr style="height:1px; width:100%; border-width:0; color:black; background-color:black"> </p>';

    var data =[{
        type: 'bar',
        x: totalCases,
        y: countyName,
        orientation: 'h',
        marker: {color:"rgba(255,0,0,0.6)"}
    }];
    var layout={
        autosize: false,
        width: 1000,
        height: 850,
        yaxis:{
            automargin: true,
        }
    };

    Plotly.newPlot('barChartContainer', data, layout);

}

//This function creates a donut chart with the data taht is recieved
function createSpecficCrimeDonutChart(data)
{

    var totalCases = [];
    var countyName = [];
    var barColours = [];

    document.getElementById("donutChartLineSeperater").innerHTML = '<p> <hr style="height:1px; width:100%; border-width:0; color:black; background-color:black"> </p>';
    document.getElementById("donutChartContainer").innerHTML = '&nbsp;';
    document.getElementById("donutChartContainer").innerHTML = '<canvas id="donutChart" style="width:100%;">';



    for (var i = 0; i < data.length; i++)
    {
        countyName.push(data[i].countyName);
        totalCases.push(data[i].totalCases);
    }

    for (var n = 0; n < countyName.length; n++)
    {
        colours = ["#b91d47", "#00aba9", "#2b5797","#e8c3b9","#1e7145", "#7B68EE", "#FFFF00", "#D2691E", "#000000", "#808080"];
        var randColour = getRandomItem(colours);
        barColours.push(randColour);
    }

    new Chart("donutChart", {
        type: "doughnut",
        data: {
          labels: countyName,
          datasets: [{
              backgroundColor: barColours,
              data: totalCases
            }]
        },
        options: {
            title: {
                display: true
            }
        }
    });
}

//This function creates a scatter plot chart with the data that is recieved
function createSpecficCrimeScatterPlotChart(data)
{
    var totalCases = [];
    var countyName = [];
    

    for (var i = 0; i < data.length; i++)
    {
        countyName.push(data[i].countyName);
        totalCases.push(data[i].totalCases);
    }

    document.getElementById("scatterChartLineSeperater").innerHTML = '<p> <hr style="height:1px; width:100%; border-width:0; color:black; background-color:black"> </p>';

    var data = [{
        x:countyName,
        y:totalCases,
        mode:"markers"
      }];
      
    // Define Layout
    var layout = {
        xaxis: countyName,
        yaxis: {range: [totalCases.min, totalCases.max]}, 
        autosize: false,
        width: 1000,
        height: 700, 
        xaxis: {
            automargin: true
        }
    };
    Plotly.newPlot('scatterPlotContainer', data, layout);
}
//-----------------------------------------------------------End of Visualisation for the Crime Specfic Page "CrimeSpecificData.html"----------------------------------------------


//-------------------------------------------------The following function deals with when a user wants to add a new case----------------------------------------------------------
/*Once the user selects the Add button in the "AddCrime.html" window this function gets the selected 
crime type and county they wish to add a case in and send it to the php to add. If it is successful there
is a alert displayed to tell the user the case was added successfully*/
function addNewCrimeCase()
{
    var userSelectedCrime = document.getElementById('crimeType').value;
    var userSelectedCounty = document.getElementById('countyName').value;

    var newCrimeData = {
        countyName: userSelectedCounty,
        crimeType: userSelectedCrime
    };

    $.ajax({
        url: '/crimeData/NewCase',
        type: 'PUT',
        data: JSON.stringify(newCrimeData),
        contentType: "application/json;charset=utf-8",
    })

    alert("A new crime was added for " + userSelectedCrime + " for " + userSelectedCounty);
}
//------------------------------------------------End of code to Add a new case----------------------------------------------------------------------------------------------------


//--------------------------------------------------Start of code for the "EditCrimeData.html" window-------------------------------------------------------------------------------
/*This get request asks for all the data for the counties and their crimes. Once 
the data is recieved it is sen tot the createEditDataTable() function*/
function getSpecificCrimeData()
{
      
    $.ajax({
        url: '/crimeData/getCounties',
        type: 'GET',
        cache: false,
        dataType: 'json',
        success: function (data) {
            createEditDataTable(data);
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert(jqXHR + '\n' + textStatus + '\n' + errorThrown);
        }
    });
}

/*Once the data is recieved this function creates a table to display all the data and a button that 
allows the user to edit the data when clicked */
function createEditDataTable(crimes)
{
    var strResult = '<div class="col-md-12">' + 
                    '<table class="table table-bordered table-hover">' +
                    '<thead>' +
                    '<tr>' +
                    '<th>Crime ID</th>' +
                    '<th>County Name</th>' +
                    '<th>Crime Type</th>' +
                    '<th>Total Cases</th>' +
                    '<th>&nbsp;</th>' +
                    '</tr>' +
                    '</thead>' +
                    '<tbody>';
    $.each(crimes, function (index, crime) 
    {                        
        strResult += "<tr><td>" + crime.crimeID + "</td><td> " + crime.countyName + "</td><td>" + crime.crimeType + "</td><td>"  + crime.totalCases + "</td><td>";
        strResult += '<input type="button" value="Edit Cases" class="selectEditButton" onclick="editCrimeData('+ crime.crimeID + ');"/>';
        strResult += '</td></td>';
    });
    strResult += "</tbody></table>";
    $("#editDataTable").html(strResult);
}

/*Once the user selects the edit button on the form created in "createEditDataTable" this function is called
this function then sends a request using the id of the crime record the user wants to edit. Once the data is 
recieved the data is sent to the "createEditCountyDataForm". */
function editCrimeData(crimeID)
{

    $.ajax({
        url: '/crimeData/specificCountyData/' + crimeID,
        type: 'GET',
        cache: false,
        dataType: 'json',
        success: function (data) {
            createEditCountyDataForm(data);

        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert(jqXHR + '\n' + textStatus + '\n' + errorThrown);
        }
    });
   
}

//Once the data is recieved this function creates and puts the data into a form, allowing the user to edit the data they want to.
function createEditCountyDataForm(data)
{
    var strResult = '<div class="editForm">';
    strResult += '<h1><b>Edit Existing Data:</b></h1><br>'
    strResult += '<form class="form-horizontal" role="form">';
    strResult += '<hr><br>';
    strResult += '<label for="crimeid">CrimeID:</label><br><input type="text" id="crimeid" value="' + data.crimeID + '" disabled><br>';
    strResult += '<label for="countyname">County Name:</label><br><input type="text" id="countyname" value="' + data.countyName + '" disabled><br>';
    strResult += '<label for="crimetype">Crime Type:</label><br><input type="text" id="crimetype"  value="' + data.crimeType + '" disabled><br>';
    strResult += '<label for="totalcases">Total Cases:</label><br><input type="text" id="totalcases" value="' + data.totalCases + '" ><br>';
    strResult += '<input type="button" value="Edit Data" class="editFormButtons" onclick="editInputCrimeData(' + data.crimeID + ');" />&nbsp;&nbsp;<input type="button" value="Cancel" class="editFormButtons" onclick="getSpecificCrimeData();" /></div></div>';
    strResult += '</form></div>';
    $("#editDataTable").html(strResult);
}

/*Once the user has editied the data and clicks the Edit data button this function is triggered
this function gets all the data inputted into each of the textboxs assigns them to a variable inside a new array
and sends them to the back end to deal with. If succeseful an alert is displayed letting the user know the data was ammended*/
function editInputCrimeData(crimeID)
{
    var crimeData = {
        crimeID: crimeID,
        countyName: $('#countyname').val(),
        crimeType: $('#crimetype').val(),
        totalCases: $('#totalcases').val()
    };

    $.ajax({
        url: '/crimeData',
        type: 'PUT',
        data: JSON.stringify(crimeData),
        contentType: "application/json;charset=utf-8",
        success: function (data) {
            alert("Changes were successfully made to " + crimeData.countyName + " for " + crimeData.crimeType);
            getSpecificCrimeData();
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert(jqXHR + '\n' + textStatus + '\n' + errorThrown);
        }
    });
    $("#newbookform").html("");
}
//----------------------------------------------------------------------------End of function--------------------------------------------------------------------------

//Function to select random value from an array
function getRandomItem(arr) {
    // get random index value
    const randomIndex = Math.floor(Math.random() * arr.length);
    // get random item
    const item = arr[randomIndex];
    return item;
}