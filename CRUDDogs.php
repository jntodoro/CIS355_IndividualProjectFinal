<?php

session_start();
$hostname = "localhost";
$username = "CIS355jntodoro";
$password = "jntodoro483899";
$dbname = "CIS355jntodoro";
$usertable = "dogs";

# ========== FUNCTIONS ========================================================
# ---------- checkConnect -----------------------------------------------------

function checkConnect($mysqli) {
    if ($mysqli->connect_errno) {
        die('Unable to connect to database [' . $mysqli->connect_error . ']');
        exit();
    }
}

# ---------- showOwners ---------------------------------------------------------
// this function gets records from a "mysql table" and builds an "html table"
// using the Dogs table. 

function showDogs($mysqli) {
    global $usertable;
	
	//This will show the initial screen to the user. It will display the
   //header information on the html table
    echo '<div class="col-md-12">
			<form action="Dogs.php" method="POST">
			<table class="table table-condensed" 
			style="border: 1px solid #dddddd; border-radius: 5px; 
			box-shadow: 2px 2px 10px;">
			<tr><td colspan="11" style="text-align: center; border-radius: 5px; 
			color: white; background-color:#333333;">
			<h2 style="color: white;">Dogs Checked In</h2>
			</td></tr><tr style="font-weight:800; font-size:20px;">
			<td>ID</td><td>Dog Name</td><td>Breed</td>
			<td>Health Issues</td><td>Neutered?</td><td>User</td>
			<td>Date Created</td><td>Search Info</td></tr>';

    // get count of records in mysql table
    $countresult = $mysqli->query("SELECT COUNT(*) FROM $usertable");
    $countfetch = $countresult->fetch_row();
    $countvalue = $countfetch[0];
    $countresult->close();

    // if records > 0 in mysql table, then populate html table, 
    // else display "no records" message
    if ($countvalue > 0) {
        populateDogs($mysqli); // populate html table, from mysql table
    } else {
        echo '<br><p>No records in database table</p><br>';
    }

    // display html buttons 
    echo '</table> ';
        echo '<input type="hidden" id="hid" name="hid" value="">
            <input type="hidden" id="uid" name="uid" value="">
            <input type="submit" name="InsertADog" value="Add an Entry" 
            class="btn btn-primary"">
            </form></div>';

        echo "<script>
			function setHid(num)
			{
				document.getElementById('hid').value = num;
		    }
		    function setUid(num)
			{
				document.getElementById('uid').value = num;
		    }
		 </script>";
}

# ---------- populateDogs ----------------------------------------------------
// populate html table, from data in mysql table

function populateDogs($mysqli) {
    global $usertable;
    $Person = $_SESSION['PersonID'];

    if ($result = $mysqli->query("SELECT dogs.id, title, subject, description, resources, "
            . "CONCAT_WS(' ',persons.first_name, persons.last_name) AS person, date_created, "
            . "search_field FROM dogs LEFT JOIN persons ON dogs.persons_id=persons.id WHERE persons_id = $Person")) {
				
				//while there is still data in the table get the information and then display
        while ($row = $result->fetch_row()) {
            echo '<tr><td>' . $row[0] . '</td><td>' . $row[1] . '</td><td>' .
            $row[2] . '</td><td>' . $row[3] . '</td><td>' . $row[4] .
            '</td><td>' . $row[5] . '</td><td>' . $row[6] . '</td><td>' .
            $row[7] . '</td><td>';
            
            echo '<input type="submit" name="SelectADog" class="btn btn-primary" value="Select" 
				onclick="setUid(' . $row[0] . ');" />';
            if ($_SESSION['PersonsRole'] == "Teacher" || $_SESSION['PersonsRole'] == "Peer Reviewer" ||
                    $_SESSION['SecRole'] == "Teacher" || $_SESSION['SecRole'] == "Peer Reviewer") {
						
						//after each entry make sure to include the delete and update and select
						// Dog buttons.
                echo '</td><td><input name="DeleteADog" type="submit" 
				class="btn btn-danger" value="Delete" onclick="setHid(' . $row[0] . ')" />';
                echo '<input style="margin-left: 10px;" type="submit"  name="UpdateADog" class="btn btn-primary" value="Update"  onclick="setUid(' . $row[0] . ');" />';
            }
        }
    } //close database connection
    $result->close();
}

# ---------- deleteDogRecord ----------------------------------------------------
// delete record from the table
function deleteDogRecord($mysqli) {
    $index = $_SESSION['DogID'];  // "hid" is id of db record to be deleted
    global $usertable;
    $stmt = $mysqli->stmt_init();
	
	//delete based on index number
    if ($stmt = $mysqli->prepare("DELETE FROM $usertable WHERE id='$index'")) {
        $stmt->bind_param('i', $index);
        $stmt->execute();
        $stmt->close();
    }
}

# ---------- ShowDogsUpdateForm ----------------------------------------------------
// show the update form for the user to modify an existing entry.
function ShowDogsUpdateForm($mysqli) {
    $index = $_POST['uid'];  // "uid" is id of db record to be updated 
    global $usertable;
    if ($result = $mysqli->query("SELECT id, title, subject, description, resources, search_field FROM $usertable WHERE id = $index")) {
        while ($row = $result->fetch_row()) {
            echo '<div class="col-md-4">
        <form name="basic" method="POST" action="Dogs.php" 
        onSubmit="return validate();"> 
        <table class="table table-condensed" style="border: 1px solid #dddddd; 
        border-radius: 5px; box-shadow: 2px 2px 10px;">
        <tr><td colspan="2" style="text-align: center; border-radius: 5px; 
        color: white; background-color:#333333;"> <h2>Update Dog Information</h2></td></tr>';

			//the entries for the user to change
            echo
            '<tr><td>Dog Name: </td><td><input type="edit" name="title" value="' . $row[1] . '" size="30"></td></tr>
	<tr><td>Breed: </td><td><input type="edit" name="subject" value="' . $row[2] . '" size="30"></td></tr>
	<tr><td>Health Issues: </td><td><input type="edit" name="description" value="' . $row[3] . '" size="20"></td></tr>
	<tr><td>Neutered: </td><td><input type="edit" name="resources" value="' . $row[4] . '" size="20"></td></tr>
	<tr><td>Search Info: </td><td><input type="edit" name="search_field" value="' . $row[5] . '" size="30"></td></tr>';
	
	//on submit call the DogUpdate function to finish the update and then go back to Dogs.php to see the changes
            echo '
        </td></tr> 
        <tr><td><input type="submit" name="DogExecuteUpdate" class="btn btn-primary" value="Update Entry"></td> 
	</table> <input type="hidden" name="uid" value="' . $row[0] . '"> </form> 
        <form action="Dogs.php"> <input type="submit" name="updateDog" value="Back to List" class="btn btn-primary""> </form> <br> </div>';
        } //close db connection
        $result->close();
    }
}

# ---------- showDogInsertForm ----------------------------------------------------
// populate html table, from data in mysql table
function showDogInsertForm() {
    echo '<div class="col-md-4">
        <form name="basic" method="POST" action="Dogs.php" 
        onSubmit="return validate();"> 
        <table class="table table-condensed" style="border: 1px solid #dddddd; 
        border-radius: 5px; box-shadow: 2px 2px 10px;">
        <tr><td colspan="2" style="text-align: center; border-radius: 5px; 
        color: white; background-color:#333333;"> <h2>Insert New Dog</h2></td></tr>';

		//the entries for the user to change
    echo '<tr><td>Dog Name: </td><td><input type="edit" name="title" value="" 
		size="30"></td></tr>
		<tr><td>Breed: </td><td><input type="edit" name="subject" 
		value="" size="30"></td></tr>
		<tr><td>Health Issues: </td><td><input type="edit" name="description" value="" 
		size="20"></td></tr>
		<tr><td>Neutered?: </td><td><input type="edit" name="resources" value="" 
		size="20"></td></tr>
		<tr><td>Search Keywords: </td><td><input type="edit" 
                                name="search_field" value="" size="30"></td></tr>';

	//on submit call the DogInsert function to finish the insert and then go back to Dogs.php to see changes
	//if decide to not add hit cancel and go back to Dogs.php
    echo '<tr><td><input type="submit" name="DogExecuteInsert" 
        class="btn btn-success" value="Add Entry"></td>
        <td style="text-align: right;"> </table><a href="Dogs.php" 
        class="btn btn-primary">Back to List</a></form></div>';
}


# ---------- InsertDog ----------------------------------------------------
// Insert the dog into the table
function insertDog($mysqli) {
    global $usertable;

    $stmt = $mysqli->stmt_init();
    if ($stmt = $mysqli->prepare("INSERT INTO `CIS355jntodoro`.`dogs` (`id`, `title`, `subject`, "
            . "`description`, `resources`, `persons_id`, `date_created`, `search_field`) VALUES "
            . "(NULL, '" . $_POST['title'] . "', '" . $_POST['subject'] . "', '" . $_POST['description'] . "', '" .
            $_POST['resources'] . "', '" . $_SESSION['PersonID'] . "', '" . date('Y-m-d H:i:s') . "', '" . $_POST['search_field'] . "');")) {

        $stmt->execute();
        $stmt->close();
    }
}
# ---------- UpdateDog ----------------------------------------------------
// Update Existing dog information
function updateDog($mysqli) {
    global $usertable;

    $stmt = $mysqli->stmt_init();
    if ($stmt = $mysqli->prepare("UPDATE  $usertable SET  title =  '" . $_POST['title'] .
            "', subject =  '" . $_POST['subject'] .
            "', description =  '" . $_POST['description'] .
            "', resources =  '" . $_POST['resources'] .
            "', search_field =  '" . $_POST['search_field'] .
            "' WHERE  $usertable .id = " . $_SESSION['DogID'])) {
        $stmt->execute();
        $stmt->close();
    }
}
