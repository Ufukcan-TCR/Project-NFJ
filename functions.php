<?php
// auteur: Ufukcan
// functie: algemene functies tbv hergebruik

include_once "config.php";

 function connectDb(){
    // Maak een database connectie met PDO
    try {
        // Opties voor de PDO connectie
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ];

        // Gebruik correcte DSN met constants uit config.php

        $conn = new PDO("mysql:host=" . SERVERNAME . ";dbname=" . DATABASE, 
                USERNAME, PASSWORD, $options);       
        //echo "Connected successfully";
        return $conn;
    } 
    catch(PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
    }

 }

 // Main functie CRUD NFT

 function crudMain(){

    // Menu-item   insert
    $txt = "
    <h1>Shop</h1>
    <nav>
		<a href='insert.php'>Toevoegen nieuwe Game</a>
    </nav><br>";
    echo $txt;

    // Haal alle fietsen record uit de tabel 
    $result = getData(CRUD_TABLE);

    //print table
    printCrudTabel($result);
    
 }


 // Winkelmandje functie CRUD NFT

 function crudWinkelmand(){

    // Menu-item   insert
    $txt = "
    <h1>Winkelmandje</h1>";
    echo $txt;

    // Haal alle Games record uit de tabel 
    $result = getData(CRUD_TABLE2);

    //print table
    printCrudTabel($result);
    
 }


 // selecteer de data uit de opgeven table
 function getData($table): array {
    // Connect database
    $conn = connectDb();

    // Select data uit de opgegeven table methode prepare
    $sql = "SELECT * FROM $table";
    $query = $conn->prepare($sql);
    $query->execute();
    $result = $query->fetchAll();

    return $result;
 }

 // selecteer de rij van de opgeven id uit de table fietsen
 function getRecord($id){
    // Connect database
    $conn = connectDb();

    // Select data uit de opgegeven table methode prepare
    $sql = "SELECT * FROM " . CRUD_TABLE . " WHERE id = :id";
    $query = $conn->prepare($sql);
    $query->execute([':id'=>$id]);
    $result = $query->fetch();

    return $result;
 }


// Function 'printCrudTabel' print een HTML-table met data uit $result 
// en een wzg- en -verwijder-knop.
function printCrudTabel($result){
    // Zet de hele table in een variable en print hem 1 keer 
    $table = "<table>";

    // Print header table

    // haal de kolommen uit de eerste rij [0] van het array $result mbv array_keys
    if (empty($result)) {
        echo "<p>Geen gegevens gevonden.</p>";
        return;
    }
    $headers = array_keys($result[0]);
    $table .= "<tr>";
    foreach($headers as $header){
        $table .= "<th>" . $header . "</th>";   
    }
    // Voeg actie kopregel toe
    $table .= "<th colspan=2>Actie</th>";
    $table .= "</tr>";

    // print elke rij
    foreach ($result as $row) {
        
        $table .= "<tr>";
        // print elke kolom
        foreach ($row as $cell) {
            $table .= "<td>" . $cell . "</td>";  
        }
        
        // Wijzig knopje
        $table .= "<td>
            <form method='post' action='update.php?id=$row[id]' >       
                <button class='btn'>Wzg</button>	 
            </form></td>";

        // Delete knopje
        $table .= "<td>
            <form method='post' action='delete.php?id=$row[id]' >       
                <button class='btn'>Verwijder</button>	 
            </form></td>";

        $table .= "</tr>";
    }
    $table.= "</table>";

    echo $table;
}


function updateRecord(array $row){

    // Maak database connectie
    $conn = connectDb();

    // Maak een query 
    $sql = "UPDATE " . CRUD_TABLE .
    " SET 
        naam = :naam,
        prijs = :prijs, 
        description = :description, 
        genre = :genre,
        player = :player,
        domain = :domain,
        consol = :consol,
        img = :img
    WHERE id = :id
    ";

    // De waarden die worden doorgegeven aan de query
    $values = [
        ':naam' => $row['naam'],
        ':prijs' => $row['prijs'],
        ':description' => $row['description'],
        ':genre' => $row['genre'],
        ':player' => $row['player'],
        ':domain' => $row['domain'],
        ':consol' => $row['consol'],
        ':img' => $row['img'],
        ':id'=>$row['id']
    ];

    // Prepare query
    $stmt = $conn->prepare($sql);
    // Uitvoeren
    $stmt->execute($values);

    // test of database actie is gelukt
    $retVal = ($stmt->rowCount() == 1) ? true : false ;
    return $retVal;
}

function insertRecord($post): bool  {
    $retVal = false;

    // Maak database connectie
    $conn = connectDb();

    // Maak een query
    // Merk dat de id niet in de insert query staat, deze wordt automatisch door de database aangemaakt
    $sql = "
        INSERT INTO " . CRUD_TABLE . " (naam, prijs, description, genre, player, domain, consol, img)
        VALUES (:naam, :prijs, :description, :genre, :player, :domain, :consol, :img) 
    ";
 
    // De waarden die worden doorgegeven aan de query
    $values = [
        ':naam' => $post['naam'],
        ':prijs' => $post['prijs'],
        ':description' => $post['description'],
        ':genre' => $post['genre'],
        ':player' => $post['player'],
        ':domain' => $post['domain'],
        ':consol' => $post['consol'],
        ':img' => $post['img']
    ];

    try {
          
        // Prepare query
        $stmt = $conn->prepare($sql);
        // Uitvoeren
        $stmt->execute($values);

        // test of database actie is gelukt
        $retVal = ($stmt->rowCount() == 1) ? true : false ;
    } catch (PDOException $e) {
        sql_error($e, $sql, $values);
        $retVal = false;
    }
    
    return $retVal;  
}

function sql_error(PDOException $e, string $sql, array $values): void {
    // Print de foutmelding naar de browser
    $err = "
    <h2>Foutmelding</h2>
    Fout op bestand: " . $e->getFile() . " op regel " . $e->getLine() . "<br>" .
    "SQL-fout: " . $e->getMessage() . "<br>" .
    "Foute SQL: " . $sql . "<br>" .
    "Opgegeven waarden: " . print_r($values, true) . "<br><br>";
    echo $err;
}

function deleteRecord($id){

    // Connect database
    $conn = connectDb();
    
    // Maak een query 
    $sql = "
    DELETE FROM " . CRUD_TABLE . 
    " WHERE id = :id";

    // Prepare query
    $stmt = $conn->prepare($sql);

    // Uitvoeren
    $stmt->execute([
        ':id' => $id
    ]);

    // test of database actie is gelukt
    $retVal = ($stmt->rowCount() == 1) ? true : false ;
    return $retVal;
}

function login(){




}

?>