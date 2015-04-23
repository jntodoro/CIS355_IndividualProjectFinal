<?php
//get all my connections
include "/home/jntodoro/public_html/myProject/CRUDPersons.php";
include "/home/jntodoro/public_html/myProject/Functions.php";

ini_set("session.cookie_domain", "csis.svsu.edu/");

// Start the session
session_start();
// Set an error message
$error = "";
$hostname = "localhost";
$username = "CIS355jntodoro";
$password = "jntodoro483899";
$dbname = "CIS355jntodoro";

//make them always log in on this page
$_SESSION['LoggedIn'] = FALSE;

$mysqli = new mysqli($hostname, $username, $password, $dbname);
checkConnect($mysqli);

// If the user pressed the Submit button check to see if the
// information matches someone in the persons table
if (isset($_POST['loginSubmit'])) {
    $usern = $_POST['username'];
    $passw = $_POST['password'];

    if ($result = $mysqli->query("SELECT role, email, password_hash, id, secondary_role FROM `persons` WHERE email = '$usern' AND password_hash = '$passw'")) {

        $row = $result->fetch_row();

        $ValidPassword = strcmp($passw, $row[2]);
        $ValidUser = strcmp(strtoupper($usern), strtoupper($row[1]));

        if (is_null($row) || $ValidPassword != 0 || $ValidUser != 0) {
            //This means the login had an invalid username/password or the user
            //was not in the system.
            $_SESSION['InvalidLogin'] = 1;
            LoginPage();
        } else {
            SessionVars($row);
            getNameOfPersonLoggedIn($mysqli);
            header("Location: http://csis.svsu.edu/~jntodoro/myProject/Dogs.php");
        }
    }
} else {
    LoginPage();
}

# ---------- SessionVars() ----------------------------------------------------
// sets all the variables used throughout the code
function SessionVars($row) {
    $_SESSION['LoggedIn'] = TRUE;
    $_SESSION['InvalidLogin'] = 0;
    $_SESSION['ValidUsername'] = $row[1];
    $_SESSION['PersonID'] = $row[3];
    $_SESSION['DogID'] = "^NO ONE><";
    $_SESSION['OwnerID'] = "^NO ONE><";
    $_SESSION['PersonsRole'] = $row[0];
    $_SESSION['SecRole'] = $row[4];
}

# ---------- getNameofPersonLoggedIn() ----------------------------------------------------
// gets the persons name---for username and password that would be Doctor Corser.... :)
function getNameOfPersonLoggedIn($mysqli) {
$Statement = "SELECT CONCAT_WS(' ',persons.first_name, persons.last_name) AS person FROM `persons` WHERE id =" . $_SESSION['PersonID'];
    $result = $mysqli->query( $Statement);
    $item = $result->fetch_row();
    $_SESSION['UserLoggedIn'] =  $item[0];
    $result->close();
}

