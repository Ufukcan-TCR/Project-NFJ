<?php
    // functie: formulier en database insert fiets
    // auteur: Ufukcan

    echo "<h1>Insert Game</h1>";

    require_once('functions.php');
	 
    // Test of er op de insert-knop is gedrukt 
    if(isset($_POST) && isset($_POST['btn_ins'])){

        // test of insert gelukt is
        if(insertRecord($_POST) == true){
            echo "<script>alert('Game is toegevoegd')</script>";
        } else {
            // Foutmelding wordt al geprint in de functie insertRecord
        }
    }
?>
<html>
    <body>
        <form method="post">

        <label for="naam">Naam:</label>
        <input type="text" id="naam" name="naam" required><br>

        <label for="prijs">Prijs:</label>
        <input type="float" id="prijs" name="prijs" required><br>

        <label for="description">Description:</label>
        <input type="text" id="description" name="description" required><br>
        
        <label for="genre">Genre:</label>
        <input type="text" id="genre" name="genre" required><br>

        <label for="player">Player:</label>
        <input type="text" id="player" name="player" required><br>

        <label for="domain">Domain:</label>
        <input type="text" id="domain" name="domain" required><br>

        <label for="consol">Consol:</label>
        <input type="text" id="consol" name="consol" required><br>

        <label for="img">Img:</label>
        <input type="text" id="img" name="img" ><br>

        <button type="submit" name="btn_ins">Insert</button>
        </form>
        
        <br><br>
        <a href='index.php'>Home</a>
    </body>
</html>
