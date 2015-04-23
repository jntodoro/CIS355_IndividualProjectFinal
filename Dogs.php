<?php
//must be logged in to see
session_start();

//connections to functions in other files
include "/home/jntodoro/public_html/myProject/CRUDDogs.php";
include "/home/jntodoro/public_html/myProject/Functions.php";

$hostname = "localhost";
$username = "CIS355jntodoro";
$password = "jntodoro483899";
$dbname = "CIS355jntodoro";
$usertable = "dogs";

$mysqli = new mysqli($hostname, $username, $password, $dbname);
checkConnect($mysqli); // program dies if no connection
// ---------- if successful connection...
if ($mysqli) {
    // ---------- c. create table, if necessary -------------------------------
    //createTable($mysqli); 
    // ---------- d. initialize userSelection and $_POST variables ------------
    $userSelection = 0;
    $firstCall = 1; 			// first time program is called
    $InsertADog = 2; 			// after user clicked InsertADog button on list 
    $UpdateADog = 3; 			// after user clicked UpdateADog button on list 
    $DeleteADog = 4; 			// after user clicked DeleteADog button on list 
    $SelectADog = 5;			// after user clicked selectDog button on list
    $DogExecuteInsert = 6; 		// after user clicked insertSubmit button on form
    $DogExecuteUpdate = 7; 		// after user clicked updateSubmit button on form

    $_SESSION['DogID'] = $_POST['uid'];
    $userlocation = $_SESSION['location'];

    $userSelection = $firstCall; 			// assumes first call unless button was clicked
    if (isset($_POST['InsertADog']))		// if add entry button selected show add entry form
        $userSelection = $InsertADog;
    if (isset($_POST['UpdateADog']))		//if update button selected show update entry form
        $userSelection = $UpdateADog;
    if (isset($_POST['DeleteADog']))		//if delete button selected call delete funtion
        $userSelection = $DeleteADog;
    if (isset($_POST['SelectADog']))		//if select button chosen go to owner information
        $userSelection = $SelectADog;
    if (isset($_POST['DogExecuteInsert']))	//if Insert selected in add entry form call insert function
        $userSelection = $DogExecuteInsert;
    if (isset($_POST['DogExecuteUpdate']))	//if update selected in update entry form call update function
        $userSelection = $DogExecuteUpdate;

		//switch statements that are executed depending on which button is selected.
		//the userSelection variable will determine which case to execute and the descriptions of what
		//will happen are listed above next to the if statements
    switch ($userSelection):
        case $firstCall:
            displayHTMLHead();
            showDogs($mysqli);
            break;
        case $InsertADog:
            displayHTMLHead();
            showDogInsertForm($mysqli);
            break;
        case $UpdateADog:
            $_SESSION['DogID'] = $_POST['uid'];
            displayHTMLHead();
            ShowDogsUpdateForm($mysqli);
            break;
        case $DeleteADog:
            $_SESSION['DogID'] = $_POST['hid'];
            displayHTMLHead();
            deleteDogRecord($mysqli);   // delete is immediate (no confirmation)
            header("Location: http://csis.svsu.edu/~jntodoro/myProject/Dogs.php");
            break;
        case $SelectADog:
            $_SESSION['DogID'] = $_POST['uid'];
            header("Location: http://csis.svsu.edu/~jntodoro/myProject/Owners.php");
            break;
        case $DogExecuteInsert:
            insertDog($mysqli);
            header("Location: http://csis.svsu.edu/~jntodoro/myProject/Dogs.php");
            break;
        case $DogExecuteUpdate:
            updateDog($mysqli);
            header("Location: http://csis.svsu.edu/~jntodoro/myProject/Dogs.php");
            break;
    endswitch;
} // ---------- end if ---------- end main processing ----------