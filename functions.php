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
    echo "<form method='GET'>
    <input type='text' name='search' placeholder='Zoek game...'>
    <button type='submit'>Zoeken</button>
</form>";

    echo "<form method='GET'>
<select name='genre'>
    <option value=''>Alle genres</option>
    <option value='0'>Indie</option>
    <option value='1'>Action</option>
    <option value='2'>Adventure</option>
</select>
<button type='submit'>Filter</button>
</form>";

    // Menu-item   insert
    $txt = "
    <h1>Shop</h1>
    <nav>
		<a href='insert.php'>Toevoegen nieuwe Game</a>
    </nav><br>";
    echo $txt;

    // Haal alle fietsen record uit de tabel 
    $genreId = $_GET['genre'] ?? null;
$search = $_GET['search'] ?? null;

if (!empty($search)) {
    $result = searchGames($search);
} 
elseif ($genreId !== null && $genreId !== '') {
    $result = filterGamesByGenre($genreId);
} 
else {
    $result = getData(CRUD_TABLE);
}

    //print table
    printCrudTabel($result);
    
 }


 // Winkelmandje functie CRUD NFT

function crudWinkelmand(){
    $txt = "<h1>Winkelmandje</h1>";
    echo $txt;

    $result = getWinkelmandData();
    printWinkelmandTabel($result);
}


function ensureWinkelmandAllowsDuplicates(PDO $conn): void {
    try {
        $conn->exec("ALTER TABLE " . CRUD_TABLE2 . " DROP INDEX id");
    } catch (PDOException $e) {
        // Index bestaat mogelijk al niet meer of kan niet verwijderd worden.
    }
}

function imageExistsInPictures(string $imageFile): bool {
    $imageFile = basename(trim($imageFile));
    if ($imageFile === '') {
        return false;
    }

    return file_exists(__DIR__ . DIRECTORY_SEPARATOR . 'pictures' . DIRECTORY_SEPARATOR . $imageFile);
}

function resolveImageFileName(string $image, string $name = ''): string {
    $image = trim(basename($image));
    $candidates = [];

    if ($image !== '') {
        $candidates[] = $image;
        if (pathinfo($image, PATHINFO_EXTENSION) === '') {
            $candidates[] = $image . '.jpg';
            $candidates[] = $image . '.png';
        }
    }

    if ($name !== '') {
        $cleanName = preg_replace('/[^A-Za-z0-9 _\-]/', '', $name);
        $cleanName = str_replace(' ', '_', $cleanName);
        $candidates[] = $cleanName . '.jpg';
        $candidates[] = $cleanName . '.png';
        $candidates[] = $cleanName;
    }

    foreach ($candidates as $candidate) {
        if ($candidate === '') {
            continue;
        }

        if (imageExistsInPictures($candidate)) {
            return $candidate;
        }
    }

    return '';
}

function getWinkelmandData(): array {
    $conn = connectDb();

    $sql = "SELECT w.id, w.game_id, w.img, w.naam, w.prijs FROM " . CRUD_TABLE2 . " w";

    $query = $conn->prepare($sql);
    $query->execute();
    return $query->fetchAll();
}

function favoriteExists(int $gameId): bool {
    $conn = connectDb();

    $sql = "SELECT 1 FROM " . CRUD_TABLE3 . " WHERE game_id = :game_id LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':game_id' => $gameId]);

    return (bool) $stmt->fetchColumn();
}

function addFavorite(int $gameId): bool {
    $game = getRecord($gameId);
    if (!$game || favoriteExists($gameId)) {
        return false;
    }

    $conn = connectDb();
    $favoriteImage = resolveImageFileName($game['img'], $game['naam']);

    $sql = "INSERT INTO " . CRUD_TABLE3 . " (game_id, naam, img, prijs)
            VALUES (:game_id, :naam, :img, :prijs)";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':game_id' => $game['id'],
        ':naam'    => $game['naam'],
        ':img'     => $favoriteImage,
        ':prijs'   => $game['prijs']
    ]);

    return ($stmt->rowCount() === 1);
}

function removeFavorite(int $favoriteId): bool {
    if ($favoriteId <= 0) {
        return false;
    }

    $conn = connectDb();

    $sql = "DELETE FROM " . CRUD_TABLE3 . " WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':id', $favoriteId, PDO::PARAM_INT);
    $stmt->execute();

    return ($stmt->rowCount() === 1);
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
function printCrudTabel($result) {

    if (empty($result)) {
        echo "<p>Geen gegevens gevonden.</p>";
        return;
    }

    $allowedColumns = ['img', 'naam', 'prijs', 'genre', 'consol'];

    $table = "<table>";
    $table .= "<tr>";
    foreach ($allowedColumns as $header) {
        $table .= "<th>" . htmlspecialchars($header) . "</th>";
    }
    $table .= "<th colspan='4'>Actie</th>";
    $table .= "</tr>";

    foreach ($result as $row) {
        $table .= "<tr>";

        foreach ($allowedColumns as $key) {
            $cell = $row[$key] ?? '';
            if ($key == "img") {
                $imgFile = resolveImageFileName($cell, $row['naam'] ?? '');
                if ($imgFile !== '') {
                    $table .= "<td class='img-cell'><img class='img-table' src='pictures/" . htmlspecialchars($imgFile) . "' alt='Foto'></td>";
                } else {
                    $table .= "<td class='img-cell'>Geen foto</td>";
                }
            } elseif ($key == "naam") {
                $table .= "<td>
                    <a href='game.php?id=" . $row['id'] . "'>
                        " . htmlspecialchars($cell) . "
                    </a>
                </td>";
            } else {
                $table .= "<td>" . htmlspecialchars($cell) . "</td>";
            }
        }

        // Wijzig knop
        $table .= "
        <td>
            <form method='post' action='update.php?id=" . $row['id'] . "'>
                <button class='btn' type='submit'>Wzg</button>
            </form>
        </td>";

        // Verwijder knop
        $table .= "
        <td>
            <form method='post' action='delete.php?id=" . $row['id'] . "'>
                <button class='btn' type='submit'>Verwijder</button>
            </form>
        </td>";

        // Winkelmandje knop
        $table .= "
        <td>
            <form method='post' action='addToCart.php'>
                <input type='hidden' name='game_id' value='" . $row['id'] . "'>
                <button class='btn' type='submit'>🛒</button>
            </form>
        </td>";

        // Favorieten knop
        $table .= "
        <td>
            <form method='post' action='addToFavorites.php'>
                <input type='hidden' name='game_id' value='" . $row['id'] . "'>
                <button class='btn' type='submit'>★</button>
            </form>
        </td>";

        $table .= "</tr>";
    }

    $table .= "</table>";
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
        ':img' => resolveImageFileName($row['img'], $row['naam']),
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
        ':img' => resolveImageFileName($post['img'] ?? '', $post['naam'] ?? '')
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

function printWinkelmandTabel($result) {

    if (empty($result)) {
        echo "<p>Geen gegevens gevonden.</p>";
        return;
    }

    $allowedColumns = ['img', 'naam', 'prijs'];

    $table = "<table>";
    $table .= "<tr>";
    foreach ($allowedColumns as $header) {
        $table .= "<th>" . htmlspecialchars($header) . "</th>";
    }
    $table .= "<th>Actie</th>";
    $table .= "</tr>";

    foreach ($result as $row) {
        $table .= "<tr>";

        foreach ($allowedColumns as $key) {
            $cell = $row[$key] ?? '';
            if ($key == "img") {
                $imgFile = htmlspecialchars($cell);
                if (!empty($imgFile)) {
                    $table .= "<td class='img-cell'><img class='img-table' src='pictures/" . $imgFile . "' alt='Foto'></td>";
                } else {
                    $table .= "<td class='img-cell'>Geen foto</td>";
                }
            } else {
                $table .= "<td>" . htmlspecialchars($cell) . "</td>";
            }
        }

        // Verwijder uit winkelmandje knop
        $table .= "
        <td>
            <form method='post' action='removeFromCart.php'>
                <input type='hidden' name='cart_id' value='" . $row['id'] . "'>
                <button class='btn' type='submit'>Verwijder</button>
            </form>
        </td>";

        $table .= "</tr>";
    }

    $table .= "</table>";
    echo $table;
}

function filterGamesByGenre($genreId) {
    $conn = connectDb();

    $sql = "
        SELECT videogames.*, genre.genre
        FROM videogames
        JOIN genre ON videogames.genrenummer = genre.genreid
        WHERE genre.genreid = :id
    ";

    $stmt = $conn->prepare($sql);
    $stmt->execute([':id' => $genreId]);

    return $stmt->fetchAll();
}

function searchGames($searchTerm) {
    $conn = connectDb();

    $sql = "
        SELECT videogames.*, genre.genre
        FROM videogames
        JOIN genre ON videogames.genrenummer = genre.genreid
        WHERE videogames.naam LIKE :search
    ";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':search' => '%' . $searchTerm . '%'
    ]);

    return $stmt->fetchAll();
}

function getFavorites(): array {
    $conn = connectDb();

    $sql = "SELECT f.id, f.game_id, f.naam, f.prijs, f.img FROM " . CRUD_TABLE3 . " f";
    $query = $conn->prepare($sql);
    $query->execute();

    return $query->fetchAll();
}

function printFavoritesTable(array $result): void {
    if (empty($result)) {
        echo "<p>Je hebt nog geen favorieten toegevoegd.</p>";
        return;
    }

    $table = "<table>";
    $table .= "<tr>";
    $table .= "<th>Afbeelding</th><th>Naam</th><th>Prijs</th><th>Actie</th>";
    $table .= "</tr>";

    foreach ($result as $row) {
        $table .= "<tr>";
        $imgFile = htmlspecialchars($row['img']);
        if (!empty($imgFile)) {
            $table .= "<td class='img-cell'><img class='img-table' src='pictures/" . $imgFile . "' alt='Foto'></td>";
        } else {
            $table .= "<td class='img-cell'>Geen foto</td>";
        }
        $table .= "<td>" . htmlspecialchars($row['naam']) . "</td>";
        $table .= "<td>€" . htmlspecialchars($row['prijs']) . "</td>";
        $table .= "<td>\n            <form method='post' action='removeFromFavorites.php'>\n                <input type='hidden' name='favorite_id' value='" . $row['id'] . "'>\n                <button class='btn' type='submit'>Verwijder</button>\n            </form>\n        </td>";
        $table .= "</tr>";
    }

    $table .= "</table>";
    echo $table;
}

function login(){




}

function getFavorites(): array {
    $conn = connectDb();

    $sql = "SELECT f.id, f.game_id, f.naam, f.prijs, f.img FROM " . CRUD_TABLE3 . " f";

    $query = $conn->prepare($sql);
    $query->execute();
    return $query->fetchAll();
}

function printFavoritesTable(array $result): void {
    if (empty($result)) {
        echo "<p>Je hebt nog geen favorieten toegevoegd.</p>";
        return;
    }

    $table = "<table>";
    $table .= "<tr>";
    $table .= "<th>Afbeelding</th><th>Naam</th><th>Prijs</th><th>Actie</th>";
    $table .= "</tr>";

    foreach ($result as $row) {
        $table .= "<tr>";
        $imgFile = htmlspecialchars($row['img']);
        if (!empty($imgFile)) {
            $table .= "<td class='img-cell'><img class='img-table' src='pictures/" . $imgFile . "' alt='Foto'></td>";
        } else {
            $table .= "<td class='img-cell'>Geen foto</td>";
        }
        $table .= "<td>" . htmlspecialchars($row['naam']) . "</td>";
        $table .= "<td>€" . htmlspecialchars($row['prijs']) . "</td>";
        $table .= "<td>\n            <form method='post' action='removeFromFavorites.php'>\n                <input type='hidden' name='favorite_id' value='" . $row['id'] . "'>\n                <button class='btn' type='submit'>Verwijder</button>\n            </form>\n        </td>";
        $table .= "</tr>";
    }

    $table .= "</table>";
    echo $table;
}


?>