<?php

session_start();
$hostname = "localhost";
$username = "CIS355jntodoro";
$password = "jntodoro483899";
$dbname = "CIS355jntodoro";
$usertable = "owners";

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
// using the Owners table. 

function showOwners($mysqli, $Created = -1) {
    if ($Created != -1) {
        $_SESSION['DogID'] = $Created;
    }

   //This will show the initial screen to the user. It will display the back to dog list button as well
   //as the header information on the html table
    echo '<div class="col-md-12">
        <form action="Dogs.php" method="POST">
				<input type="submit" name="BackToDogs" value="Back to Dog List" class="btn btn-primary""> </form> <br>
        <form action="Owners.php" method="POST">
        <table class="table table-condensed" 
        style="border: 1px solid #dddddd; border-radius: 5px; 
        box-shadow: 2px 2px 10px;">
        <tr><td colspan="11" style="text-align: center; border-radius: 5px; 
        color: white; background-color:#333333;">
        <h2 style="color: white;">Owner Information</h2>
        </td></tr><tr style="font-weight:800; font-size:20px;">
        <td>ID</td><td>Dog\'s Name</td><td>Dog\'s Breed</td>
        <td>Days Expected to Stay</td> <td>Owner Name</td><td>Contact Information</td></tr>';

// get count of records in mysql table
    $countresult = $mysqli->query("SELECT COUNT(*) FROM owners WHERE dogs_id= " . $_SESSION['DogID']);
    $countfetch = $countresult->fetch_row();
    $countvalue = $countfetch[0];
    $countresult->close();

// if records > 0 in mysql table, then populate html table, 
// else display "no records" message
    if ($countvalue > 0) {
        populateOwners($mysqli); // populate html table, from mysql table
    } else {
        echo '<p>No records in database table</p><br>';
    }

// display html buttons 
    echo '</table>
        <input type="hidden" id="hid" name="hid" value="">
        <input type="hidden" id="uid" name="uid" value="">
        <input type="submit" name="InsertAOwner" value="Add an Entry" class="btn btn-primary""></form>';

    echo "<script>
        function setHid(num){
            document.getElementById('hid').value = num;
	}
	function setUid(num) {
            document.getElementById('uid').value = num;
	}
	</script>";
}

# ---------- populateOwners ----------------------------------------------------
// populate html table, from data in mysql table

function populateOwners($mysqli) {
    global $usertable;
    $Lesson = $_SESSION['DogID'];

    if ($result = $mysqli->query("SELECT owners.id, dogs.title, dogs.subject, attempts_allowed, "
            . "owners.title, owners.description FROM dogs LEFT JOIN owners ON dogs.id=owners.dogs_id "
            . "where owners.dogs_id =$Lesson")) {

		//while there is still data in the table get the information and then display
        while ($row = $result->fetch_row()) {
            echo '<tr><td>' . $row[0] . '</td><td>' . $row[1] . '</td><td>' .
            $row[2] . '</td><td>' . $row[3] . '</td><td>' . $row[4] .
            '</td><td>' . $row[5] . '</td><td>' . $row[6] . '</td><td>' .
            $row[7] . '</td><td>' . $row[8] . '</td><td>' . $row[9];

            if ($_SESSION['PersonsRole'] == "Teacher" || $_SESSION['PersonsRole'] == "Peer Reviewer" ||
                    $_SESSION['SecRole'] == "Teacher" || $_SESSION['SecRole'] == "Peer Reviewer") {
						
						//after each entry make sure to include the delete and update owner
						//buttons.
                echo '</td><td><input name="DeleteAOwner" type="submit" 
				class="btn btn-danger" value="Delete" onclick="setHid(' .
                $row[0] . ')" />';
                echo '<input style="margin-left: 10px;" type="submit" 
				name="UpdateAOwner" class="btn btn-primary" value="Update" 
				onclick="setUid(' . $row[0] . ');" />';
            }
        }
    } //close connection
    $result->close();
}

# ---------- deleteOwnersRecord ----------------------------------------------------
// delete record from the table
function deleteOwnersRecord($mysqli) {
    $index = $_SESSION['OwnerID'];  // "hid" is id of db record to be deleted
    global $usertable;

	 //delete based on index number
    $stmt = $mysqli->stmt_init();
    if ($stmt = $mysqli->prepare("DELETE FROM $usertable WHERE id='$index'")) {
        $stmt->bind_param('i', $index);
        $stmt->execute();
        $stmt->close();
    }
}

# ---------- ShowOwnersUpdateForm ----------------------------------------------------
// show the update form for the user to modify an existing entry.
function ShowOwnersUpdateForm($mysqli) {
    $index = $_SESSION['OwnerID'];  // "uid" is id of db record to be updated 
    global $usertable;
    if ($result = $mysqli->query("SELECT id, attempts_allowed, title, description FROM $usertable WHERE id = $index")) {
        while ($row = $result->fetch_row()) {
            echo '<div class="col-md-4">
        <form name="basic" method="POST" action="Owners.php" 
        onSubmit="return validate();"> 
        <table class="table table-condensed" style="border: 1px solid #dddddd; 
        border-radius: 5px; box-shadow: 2px 2px 10px;">
        <tr><td colspan="2" style="text-align: center; border-radius: 5px; 
        color: white; background-color:#333333;"> <h2>Update Owner</h2></td></tr>';

			//the entries for the user to change
            echo '<tr><td>Days Expected Gone: </td><td><input type="number" name="max_attempts" value= ' . $row[1] . ' min="1" max="100000"></td></tr>
             <tr><td>Owner Name: </td><td><input type="edit" name="quiz_title" value= "' . $row[2] . ' " size="49"></td></tr>
             <tr><td>Contact Information: </td><td><textarea maxlength="200" style="resize: none;" name="quiz_des" cols="51" rows="3">' . $row[3] . ' </textarea></td></tr>';

		//on submit call the ownerExecuteUpdateFunction to finish the update and then go back to owners.php to see the changes
            echo '
        </td></tr> 
        <tr><td><input type="submit" name="OwnerExecuteUpdate" class="btn btn-primary" value="Update Entry"></td> 
	</table> <input type="hidden" name="uid" value="' . $row[0] . '"> </form> 
        <form action="Owners.php"> <input type="submit" name="BackToOwners" value="Back to Owners List" class="btn btn-primary""> </form> <br> </div>';
        } //close database connection
        $result->close();
    }
}

# ---------- showOwnersInsertForm ----------------------------------------------------
// populate html table, from data in mysql table
function showOwnersInsertForm($mysqli) {
    echo '<div class="col-md-4">
        <form name="basic" method="POST" action="Owners.php" 
        onSubmit="return validate();">
        <table class="table table-condensed" style="border: 1px solid #dddddd; 
        border-radius: 5px; box-shadow: 2px 2px 10px;">
        <tr><td colspan="2" style="text-align: center; border-radius: 5px; 
        color: white; background-color:#333333;">
        <h2>Create New Owner</h2></td></tr>';

    InsertDogFieldsCombobox($mysqli);
		//the entries for the user to change
    echo '<tr><td>Days Expected Gone: </td><td><input type="number" name="max_attempts" min="1" max="100000"></td></tr>
             <tr><td>Owner Name: </td><td><input type="edit" name="quiz_title" value="" size="49"></td></tr>
             <tr><td>Contact Information: </td><td><textarea maxlength="200" style="resize: none;" name="quiz_des" cols="51" rows="3"></textarea></td></tr>';

		//on submit call the OwnerExecuteInsert function to finish the insert and then go back to owners.php to see changes
		//if decide to not add hit cancel and go back to owners.php
    echo '<tr><td><input type="submit" name="OwnerExecuteInsert" class="btn btn-success" value="Add Entry"></td> <td style="text-align: right;">
        <input type="reset" class="btn btn-danger" name="UpdateAOwner" onclick="history.go(-1);" value="Cancel"></td></tr> </table>
        <a href="Owners.php" class="btn btn-primary">Back To Owner List</a></form></div>';
}

# ---------- InsertDogFieldsComboBox ----------------------------------------------------
// Doesn't work how I want
function InsertDogFieldsCombobox($mysqli) {
    if ($result = $mysqli->query("SELECT id, title, subject FROM dogs WHERE persons_id = " . $_SESSION['PersonID'] . ' AND id = ' . $_SESSION['DogID'])) {
        $row = $result->fetch_row();
        echo '<tr><td>' . "Select Lesson: " . ': </td><td> <input type="edit" name="quiz_title" value= "'
        . $row[0] . " - " . $row[1] . " - " . $row[2] . '" size="49" readonly></td></tr>';
    }
}

# ---------- CreateOwner ----------------------------------------------------
// Insert new user into table
function CreateOwner($mysqli) {
    global $usertable;
    $lesson_id = $_SESSION['DogID'];
    $max_attempts = $_POST['max_attempts'];
    $title = $_POST['quiz_title'];
    $description = $_POST['quiz_des'];

    $stmt = $mysqli->stmt_init();
    if ($stmt = $mysqli->prepare("INSERT INTO $usertable (id, dogs_id, attempts_allowed, 
                                    title, description) VALUES ('NULL', '$lesson_id', '$max_attempts', '$title', 
                                    '$description')")) {
        $stmt->execute();
        $stmt->close();
    }
    $_SESSION['DogID'] = $lesson_id;
}

# ---------- updateOwner ----------------------------------------------------
// update existing owner
function updateOwner($mysqli) {
    global $usertable;

    $stmt = $mysqli->stmt_init();
    if ($stmt = $mysqli->prepare('UPDATE ' . $usertable . ' SET  `attempts_allowed` =  ' . $_POST['max_attempts'] . ',
                `title` =  ' . "'" . $_POST['quiz_title'] . "'" . ',
                `description` =  ' . "'" . $_POST['quiz_des'] . "'" . ' WHERE  id =' . $_SESSION['OwnerID'])) {
        $stmt->execute();
        $stmt->close();
    }
}

# ---------- getSelectedValue ----------------------------------------------------
// get value on person
function GetSelectedValue($mysqli) {
    $item = 0;
    if ($result = $mysqli->query("SELECT id, title, subject FROM dogs WHERE persons_id = " . $_SESSION['PersonID'])) {
        while ($row = $result->fetch_row()) {
            $SelectedItems[$item++] = $row[0];
        }
    }
    return $SelectedItems;
}