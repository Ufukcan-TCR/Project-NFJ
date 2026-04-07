<?php
    // functie: update Games
    // auteur: Ufukcan

    require_once('functions.php');

    // Test of er op de wijzig-knop is gedrukt 
    if(isset($_POST['btn_wzg'])){

        // test of update gelukt is
        if(updateRecord($_POST) == true){
            echo "<script>alert('Game is gewijzigd')</script>";
        } else {
            echo '<script>alert("Game is NIET gewijzigd")</script>';
        }
    }

    // Test of id is meegegeven in de URL
    if(isset($_GET['id'])){  
        // Haal alle info van de betreffende id $_GET['id']
        $id = $_GET['id'];
        $row = getRecord($id);
      } else {
          echo "Geen id opgegeven<br>";
          exit;
      }
  ?> 

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="style.css">
  <title>Wijzig Game</title>
</head>
<body>
  <h2>Wijzig Game</h2>
  <form method="post">
    
    <input type="hidden" id="id" name="id" required value="<?php echo $row['id']; ?>"><br>

    <label for="naam">Naam:</label>
    <input type="text" id="naam" name="naam" required value="<?php echo $row['naam']; ?>"><br>

    <label for="prijs">Prijs:</label>
    <input type="float" id="prijs" name="prijs" required value="<?php echo $row['prijs']; ?>"><br>

    <label for="description">Description:</label>
    <input type="text" id="description" name="description" required value="<?php echo $row['description']; ?>"><br>

    <label for="genre">Genre:</label>
    <input type="text" id="genre" name="genre" required value="<?php echo $row['genre']; ?>"><br>

    <label for="player">Player:</label>
    <input type="text" id="player" name="player" required value="<?php echo $row['player']; ?>"><br>

    <label for="domain">domain:</label>
    <input type="text" id="domain" name="domain" required value="<?php echo $row['domain']; ?>"><br>

    <label for="consol">consol:</label>
    <input type="text" id="consol" name="consol" required value="<?php echo $row['consol']; ?>"><br>

    <label for="img">img:</label>
    <input type="text" id="img" name="img" value="<?php echo $row['img']; ?>"><br>


    <button type="submit" name="btn_wzg">Wijzig</button>
  </form>
  <br><br>
  <a href='index.php'>Home</a>
</body>
</html>