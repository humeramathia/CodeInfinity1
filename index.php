<?php
require 'vendor/autoload.php';

// Connect to MongoDB
$mongoClient = new MongoDB\Client("mongodb://localhost:27017");
$collection = $mongoClient->bases->users;

// Regular expression pattern for name validation
$namePattern = '/^[a-zA-Z]+$/'; 


//-------------------------------------------------------------------------------------------

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $surname = $_POST['surname'];
    $idNumber = $_POST['id_number'];
    $dob = $_POST['dob'];
    
    
    if (!preg_match($namePattern, $name)) {
        echo "<script>alert('Invalid Name. Only alphabetic characters are allowed.')</script>";
    }
   
    elseif (!preg_match($namePattern, $surname)) {
        echo "<script>alert('Invalid Surname. Only alphabetic characters are allowed.')</script>";
    }
   
    elseif (strlen($idNumber) !== 13) {
        echo "<script>alert('Invalid ID Number length. It must be exactly 13 characters long.')</script>";
    }
   
    elseif (!is_numeric($idNumber)) {
        echo "<script>alert('Invalid ID Number format. It must be numeric dd/mm/yyyy.')</script>";
    }
  
    else {
       
        $idDay = substr($idNumber, 4, 2);
        $idMonth = substr($idNumber, 2, 2);
        $idYear = substr($idNumber, 0, 2);

      
        list($dobDay, $dobMonth, $dobYear) = explode('/', $dob);

        
        if ($idDay != $dobDay || $idMonth != $dobMonth || $idYear != substr($dobYear, -2)) {
            echo "<script>alert('Date of birth and ID number do not match.')</script>";
        }
        // Check for duplicate ID Number
        elseif ($collection->countDocuments(['ID Number' => $idNumber]) > 0) {
            echo "<script>alert('This ID is already in the database.')</script>";
        } else {
            $dobDateTime = DateTime::createFromFormat('d/m/Y', $dob);
            if ($dobDateTime && $dobDateTime->format('d/m/Y') === $dob) {
                $data = [
                    'Name' => $name,
                    'Surname' => $surname,
                    'ID Number' => $idNumber,
                    'Date of Birth' => $dobDateTime->format('d-m-y'), 
                ];
                $insertResult = $collection->insertOne($data);
                if ($insertResult->getInsertedCount() > 0) {
                    echo "<script>alert('Data for $name has been successfully captured')</script>";
                } else {
                    echo "<script>alert('Error inserting data into MongoDB.')</script>";
                }
            } else {
                echo "<script>alert('Invalid Date of Birth format. Please use dd/mm/yyyy format.')</script>";
            }
        }
    }
}
?>





<!----------------------------------------------------HTML---------------------------------------------------->



<!DOCTYPE html>
<html>
<head>
    <title>Form to MongoDB</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
<div class="brand-title" style="margin-top: -20px;">Enter Data</div>

        <form method="POST">
        <div class="inputs">
    <div class="input-row">
    <label for="name">Name:</label>
    <input type="text" id="name" name="name" placeholder= "Name">
    <label for="surname">Surname:</label>
    <input type="text" id="surname" name="surname" placeholder= "Surname">
    </div>
    <div class="input-row">
    <label for="id_number">ID Number:</label>
    <input type="text" id="id_number" name="id_number"  placeholder= "13 Digits">
    <label for="dob">Date of Birth:</label>
    <input type="text" id="dob" name="dob" placeholder= "dd/mm/yyyy">
    </div>
</div>

            
<div class="button-container">

            <input type="button" value="CANCEL" onclick="window.location.href='cancel_page.php'" class="cancel-button">
            <input type="submit" value="POST" class="submit-button">
</div>
            
        </form>
    </div>
</body>
</html>

<!----------------------------------------------------END OF CODE---------------------------------------------------->