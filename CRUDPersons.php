<?php
session_start();
$hostname = "localhost";
$username = "CIS355jntodoro";
$password = "jntodoro483899";
$dbname = "CIS355jntodoro";
$usertable = "persons";

# ========== FUNCTIONS ========================================================
# ---------- checkConnect -----------------------------------------------------

function checkConnect($mysqli) {
    if ($mysqli->connect_errno) {
        die('Unable to connect to database [' . $mysqli->connect_error . ']');
        exit();
    }
}

# ---------- showPersons ---------------------------------------------------------
// this function gets records from a "mysql table" and builds an "html table"

function showPersons($mysqli) {
    global $usertable;

    // display current user and location_id
    // echo "You are logged in as user: ".$_SESSION["user"]." (".$_SESSION["id"].") ".
    //     " location: ".$_SESSION["location"]."<br>";
    // display html table column headings
    echo '<div class="col-md-12">
			<form action="Persons.php" method="POST">
			<table class="table table-condensed" 
			style="border: 1px solid #dddddd; border-radius: 5px; 
			box-shadow: 2px 2px 10px;">
			<tr><td colspan="11" style="text-align: center; border-radius: 5px; 
			color: white; background-color:#333333;">
			<h2 style="color: white;">Persons</h2>
			</td></tr><tr style="font-weight:800; font-size:20px;">
			<td>ID</td><td>Role</td><td>Secondary Role</td>
			<td>First Name</td><td>Last Name</td><td>Email</td>
			<td>Password</td><td>School</td></tr>';

    // get count of records in mysql table
    $countresult = $mysqli->query("SELECT COUNT(*) FROM $usertable");
    $countfetch = $countresult->fetch_row();
    $countvalue = $countfetch[0];
    $countresult->close();

    // if records > 0 in mysql table, then populate html table, 
    // else display "no records" message
    if ($countvalue > 0) {
        populatePersons($mysqli); // populate html table, from mysql table
    } else {
        echo '<br><p>No records in database table</p><br>';
    }

    // display html buttons 
    echo '</table>
			<input type="hidden" id="hid" name="hid" value="">
			<input type="hidden" id="uid" name="uid" value="">
			<input type="submit" name="InsertAPerson" value="Add an Entry" 
			class="btn btn-primary"">
			</form></div>';

    // below: JavaScript functions at end of html body section
    // "hid" is id of item to be deleted
    // "uid" is id of item to be updated.
    // see also: populatePersons function
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

# ---------- populatePersons ----------------------------------------------------
// populate html table, from data in mysql table

function populatePersons($mysqli) {
    global $usertable;

    if ($result = $mysqli->query("SELECT * FROM $usertable")) {
        while ($row = $result->fetch_row()) {
            echo '<tr><td>' . $row[0] . '</td><td>' . $row[1] . '</td><td>' .
            $row[2] . '</td><td>' . $row[3] . '</td><td>' . $row[4] .
            '</td><td>' . $row[5] . '</td><td>' . $row[6] . '</td><td>' .
            $row[7] . '</td><td>' . $row[8] . '</td><td>' . $row[9];

            if ($_SESSION["id"] == $row[9]) {
                echo '</td><td><input name="DeleteAPerson" type="submit" 
				class="btn btn-danger" value="Delete" onclick="setHid(' .
                $row[0] . ')" />';
                echo '<input style="margin-left: 10px;" type="submit" 
				name="UpdateAPerson" class="btn btn-primary" value="Update" 
				onclick="setUid(' . $row[0] . ');" />';
            }
        }
    }
    $result->close();
}

function InsertCombobox($DisplayedValue, $Section) {
    $TitleOfPostName = str_replace(' ', '', $Section);
    $Output = "";
    if ($DisplayedValue == "Teacher") {
        $Output = '<tr><td>' . "$Section" . ': </td><td> <select name="' . $TitleOfPostName . '">
  			   		<option value="TEACHER">Teacher</option>
  			   		<option value="STUDENT">Student</option>
  			   		<option value="PEERREVIEWER">Peer Reviewer</option>';

        if ($TitleOfPostName == "SecondaryRole")
            $Output .= ' <option value="NONE">None</option> ';

        $Output .= '</select>';
    } elseif ($DisplayedValue == "Student") {
        $Output = '<tr><td>' . "$Section" . ': </td><td> <select name="' . $TitleOfPostName . '">
  			   		<option value="STUDENT">Student</option>
  			   		<option value="TEACHER">Teacher</option>
  			   		<option value="PEERREVIEWER">Peer Reviewer</option>';

        if ($TitleOfPostName == "SecondaryRole")
            $Output .= ' <option value="NONE">None</option> ';

        $Output .= '</select>';
    } else {
        $Output = '<tr><td>' . "$Section" . ': </td><td> <select name="' . $TitleOfPostName . '">
	   				<option value="PEERREVIEWER">Peer Reviewer</option>
  			   		<option value="STUDENT">Student</option>
  			   		<option value="TEACHER">Teacher</option>';

        if ($TitleOfPostName == "SecondaryRole")
            $Output .= ' <option value="NONE">None</option> ';

        $Output .= '</select>';
    }
    return $Output;
}

# ---------- showInsertForm ---------------------------------------------------

function showInsertForm($mysqli) {
    global $userlocation;
    // display current user and location_id
    // echo "You are logged in as user: ".$_SESSION["user"].
    //     " location: ".$_SESSION["location"]."<br>";

    echo '<div class="col-md-4">
		<form name="basic" method="POST" action="Persons.php" 
	    onSubmit="return validate();">
		<table class="table table-condensed" style="border: 1px solid #dddddd; 
		border-radius: 5px; box-shadow: 2px 2px 10px;">
		<tr><td colspan="2" style="text-align: center; border-radius: 5px; 
		color: white; background-color:#333333;">
		<h2>Insert New Person</h2></td></tr>';

    echo InsertCombobox("Teacher", "Role");

    echo InsertCombobox("", "Secondary Role");

    echo '<tr><td>First Name: </td><td><input type="edit" name="first_name" value="" 
		size="30"></td></tr>
		<tr><td>Last Name: </td><td><input type="edit" name="last_name" 
		value="" size="30"></td></tr>
		<tr><td>Email: </td><td><input type="edit" name="email" value="" 
		size="20"></td></tr>
		<tr><td>Password: </td><td><input type="edit" name="password" value="" 
		size="20"></td></tr>
		<tr><td>School: </td><td><input type="edit" name="school" value="" 
		size="30"></td></tr>';

    echo '<tr><td><input type="submit" name="PersonExecuteInsert" 
			    class="btn btn-success" value="Add Entry"></td>
			    <td style="text-align: right;"><input type="reset" 
			    class="btn btn-danger" value="Reset Form"></td></tr>
		        </table><a href="Persons.php" class="btn btn-primary">
		        Display Persons Table</a></form></div>';
}

function showUpdateForm($mysqli) {
    $index = $_POST['uid'];  // "uid" is id of db record to be updated 
    global $usertable;

    if ($result = $mysqli->query("SELECT * FROM $usertable WHERE id = $index")) {
        while ($row = $result->fetch_row()) {
            // display current user and location_id
            // echo "You are logged in as user: ".$_SESSION["user"].
            //       " location: ".$_SESSION["location"]."<br>";
            echo '	<br>
					<div class="col-md-4">
					<form name="basic" method="POST" action="Persons.php">
						<table class="table table-condensed" 
						    style="border: 1px solid #dddddd; 
							border-radius: 5px; box-shadow: 2px 2px 10px;">
							<tr><td colspan="2" style="text-align: center; 
							border-radius: 5px; color: white; 
							background-color:#333333;">
							<h2>Update Person</h2></td></tr>';
            echo InsertCombobox($row[1], "Role");

            echo InsertCombobox($row[2], "Secondary Role");

            echo '<tr><td>First Name: </td><td><input type="edit" 
					name="first_name" value="' . $row[3] . '" size="30">
					</td></tr>
					<tr><td>Last Name: </td><td><input type="edit" 
					name="last_name" value="' . $row[4] . '" size="20">
					</td></tr>
					<tr><td>email: </td><td><input type="edit" 
					name="email" value="' . $row[5] . '" size="30">
					</td></tr>
					<tr><td>Password: </td><td><input type="edit" 
					name="password" value="' . $row[6] . '" size="20">
					</td></tr>
					<tr><td>School: </td><td><textarea 
					style="resize: none;" name="school" cols="40" 
					rows="3">' . $row[7] . '</textarea></td></tr>';
            echo '</td></tr>
			       <tr><td><input type="submit" name="PersonExecuteUpdate" 
					class="btn btn-primary" value="Update Entry"></td>
				   <td style="text-align: right;"><input type="reset" 
					class="btn btn-danger" value="Reset Form"></td></tr>
				   </table>
					<input type="hidden" name="uid" value="' . $row[0] . '">
					</form>
				   </div>';
        }
        $result->close();
    }
}

# ---------- deleteRecord -----------------------------------------------------

function deleteRecord($mysqli) {
    $index = $_POST['hid'];  // "hid" is id of db record to be deleted
    global $usertable;
    $stmt = $mysqli->stmt_init();
    if ($stmt = $mysqli->prepare("DELETE FROM persons WHERE id='$index'")) {
        // Bind parameters. Types: s=string, i=integer, d=double, etc.
        // protects against sql injections
        $stmt->bind_param('i', $index);
        $stmt->execute();
        $stmt->close();
    }
}

# ---------- insertRecord -----------------------------------------------------

function insertRecord($mysqli) {
    global $role, $sec_role, $first_name, $last_name, $email, $password, $school;
    global $usertable;

    $Insert_role = $_POST['Role'];
    $Insert_secondary_role = $_POST['SecondaryRole'];
    $Insert_role = CheckRoles($Insert_role);
    $Insert_secondary_role = CheckRoles($Insert_secondary_role);

    $stmt = $mysqli->stmt_init();
    if ($stmt = $mysqli->prepare("INSERT INTO $usertable (id, role, secondary_role, 
				first_name, last_name, email, password_hash, school) VALUES ('NULL', '$Insert_role', '$Insert_secondary_role', '$first_name', 
				'$last_name', '$email', '$password', '$school')")) {
        $stmt->execute();
        $stmt->close();
    }
}

function CheckRoles($roles) {
    $roles = strtoupper($roles);
    $roles = str_replace(' ', '', $roles);
    $value = 0;

    if ($roles == "TEACHER")
        $value = 1;
    elseif ($roles == "STUDENT")
        $value = 2;
    elseif ($roles == "PEERREVIEWER")
        $value = 3;
    else
        $roles = "";

    return $value;
}

# ---------- updateRecord -----------------------------------------------------

function updateRecord($mysqli) {
    global $type, $brand, $model, $color, $strWind, $price, $descript, $location_id, $user_id;
    global $usertable;
    $index = $_POST['uid'];  // "uid" is id of db record to be updated


    $Update_role = $_POST['Role'];
    $Update_secondary_role = $_POST['SecondaryRole'];
    $Update_role = CheckRoles($Update_role);
    $Update_secondary_role = CheckRoles($Update_secondary_role);

    $Update_first_name = $_POST['first_name'];
    $Update_last_name = $_POST['last_name'];
    $Update_email = $_POST['email'];
    $Update_password = $_POST['password'];
    $Update_school = $_POST['school'];

    $stmt = $mysqli->stmt_init();
    if ($stmt = $mysqli->prepare("UPDATE persons SET role = '$Update_role', secondary_role = '$Update_secondary_role',  
    	first_name = '$Update_first_name', last_name = '$Update_last_name', 
    	email = '$Update_email', password_hash = '$Update_password', school = '$Update_school' WHERE id = '$index'")) {
        
        $stmt->execute();
        $stmt->close();
    }
}
