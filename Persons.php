<?php
session_start();
include "/home/jntodoro/public_html/teamProject/CRUDPersons.php";
include "/home/jntodoro/public_html/teamProject/Functions.php";

$hostname = "localhost";
$username = "CIS355jldevin1";
$password = "Spirius666";
$dbname = "CIS355jldevin1";
$usertable = "persons";

$mysqli = new mysqli($hostname, $username, $password, $dbname);
checkConnect($mysqli); // program dies if no connection
// ---------- if successful connection...
if ($mysqli) {
    // ---------- c. create table, if necessary -------------------------------
    //createTable($mysqli); 
    // ---------- d. initialize userSelection and $_POST variables ------------
    $userSelection = 0;
    $firstCall = 1; // first time program is called
    $InsertAPerson = 2; // after user clicked InsertAPerson button on list 
    $UpdateAPerson = 3; // after user clicked UpdateAPerson button on list 
    $DeleteAPerson = 4; // after user clicked DeleteAPerson button on list 
    $PersonExecuteInsert = 5; // after user clicked insertSubmit button on form
    $PersonExecuteUpdate = 6; // after user clicked updateSubmit button on form

    $role = $_POST['role'];
    $sec_role = $_POST['sec_role'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $school = $_POST['school'];
    $role = CheckRoles($role);
    $sec_role = CheckRoles($sec_role);
    $userlocation = $_SESSION['location'];
    //$userSelection = $firstCall;
    if (isset($_POST['InsertAPerson']))
        $userSelection = $InsertAPerson;
    if (isset($_POST['UpdateAPerson']))
        $userSelection = $UpdateAPerson;
    if (isset($_POST['DeleteAPerson']))
        $userSelection = $DeleteAPerson;
    if (isset($_POST['PersonExecuteInsert']))
        $userSelection = $PersonExecuteInsert;
    if (isset($_POST['PersonExecuteUpdate']))
        $userSelection = $PersonExecuteUpdate;

    // ---------- f. call function based on what user clicked -----------------
    switch ($userSelection):
//        case $firstCall:
//            displayHTMLHead();
//            showPersons($mysqli);
//            break;
        case $InsertAPerson:
            displayHTMLHead();
            showInsertForm($mysqli);
            break;
        case $UpdateAPerson :
            displayHTMLHead();
            showUpdateForm($mysqli);
            break;
        case $DeleteAPerson:
            displayHTMLHead();
            //showDeleteForm($mysqli); // currently no form is displayed
            deleteRecord($mysqli);   // delete is immediate (no confirmation)
            showPersons($mysqli);
            break;
        case $PersonExecuteInsert: // updated to do Post/Redirect/Get (PRG)
            insertRecord($mysqli);
            header("Location: " . $_SERVER['REQUEST_URI']); // redirect
            displayHTMLHead();
            showPersons($mysqli);
            break;
        case $PersonExecuteUpdate:
            updateRecord($mysqli);
            header("Location: " . $_SERVER['REQUEST_URI']);
            displayHTMLHead();
            showPersons($mysqli);
            break;
    endswitch;
} // ---------- end if ---------- end main processing ----------
?>