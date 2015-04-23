<?php
//must be logged in to see
session_start();
//get functions in other files
include "/home/jntodoro/public_html/myProject/CRUDOwners.php";
include "/home/jntodoro/public_html/myProject/Functions.php";

//database information
$hostname = "localhost";
$username = "CIS355jntodoro";
$password = "jntodoro483899";
$dbname = "CIS355jntodoro";
$usertable = "owners";

$mysqli = new mysqli($hostname, $username, $password, $dbname);
checkConnect($mysqli); // program dies if no connection
// ---------- if successful connection...
if ($mysqli) {
    // ---------- d. initialize userSelection and $_POST variables ------------
    $userSelection = 0;
    $firstCall = 1; 			// first time program is called
    $InsertAOwner = 2; 			// after user clicked InsertAOwner button on list 
    $UpdateAOwner = 3; 			// after user clicked UpdateAOwner button on list 
    $DeleteAOwner = 4; 			// after user clicked DeleteAOwner button on list 
    $OwnerExecuteInsert = 6;	// after user clicked insertSubmit button on form
    $OwnerExecuteUpdate = 7; 	// after user clicked updateSubmit button on form
    $BackToDogs = 8;			// after user clicked go back to dogs.php
    $BackToOwners = 9;			// after user clicked go back to owners.php
	
    $userlocation = $_SESSION['location'];

    $userSelection = $firstCall; 				// assumes first call unless button was clicked
    if (isset($_POST['InsertAOwner']))			// if add entry selected show add form
        $userSelection = $InsertAOwner;
    if (isset($_POST['UpdateAOwner']))			//if update entry selected show update form
        $userSelection = $UpdateAOwner;
    if (isset($_POST['DeleteAOwner']))			//if delete entry selected call delete function
        $userSelection = $DeleteAOwner;
    if (isset($_POST['OwnerExecuteInsert']))	//if user clicks add after inserting call insert function
        $userSelection = $OwnerExecuteInsert;
    if (isset($_POST['OwnerExecuteUpdate']))	//if user selects update after changing call update function
        $userSelection = $OwnerExecuteUpdate;
    if (isset($_POST['BackToDogs']))			//if back to previous screen selected go back
        $userSelection = $BackToDogs;
        if (isset($_POST['BackToOwners']))		// if back to owners screen selected go back
        $userSelection = $BackToOwners;

		
		//switch statements that are executed depending on which button is selected.
		//the userSelection variable will determine which case to execute and the descriptions of what
		//will happen are listed above next to the if statements
    switch ($userSelection):
        case $firstCall:
            displayHTMLHead();
            showOwners($mysqli);
            break;
        case $InsertAOwner:
            displayHTMLHead();
            showOwnersInsertForm($mysqli);
            break;
        case $UpdateAOwner :
            $_SESSION['OwnerID'] = $_POST['uid'];
            displayHTMLHead();
            ShowOwnersUpdateForm($mysqli);
            break;
        case $DeleteAOwner:
            $_SESSION['OwnerID'] = $_POST['hid'];
            deleteOwnersRecord($mysqli);   // delete is immediate (no confirmation)
            header("Location: http://csis.svsu.edu/~jntodoro/myProject/Owners.php");
            break;
        case $OwnerExecuteInsert:
            CreateOwner($mysqli);
            header("Location: http://csis.svsu.edu/~jntodoro/myProject/Owners.php");
            break;
        case $OwnerExecuteUpdate:
            updateOwner($mysqli);
            header("Location: http://csis.svsu.edu/~jntodoro/myProject/Owners.php");
            break;
        case $BackToDogs:
            header("Location: http://csis.svsu.edu/~jntodoro/myProject/Dogs.php");
            break;
        case $BackToOwners:
            header("Location: http://csis.svsu.edu/~jntodoro/myProject/Owners.php");
            break;
    endswitch;
} // ---------- end if ---------- end main processing ----------