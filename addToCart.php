<?php
// auteur: Ufukcan
// functie: voeg een game toe aan het winkelmandje
include_once "functions.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['game_id'])) {
    $game_id = $_POST['game_id'];

    // Haal de game op uit de shop tabel
    $game = getRecord($game_id);

    if ($game) {
        $conn = connectDb();

        // Zorg dat de unieke index die duplicate cart-items blokkeert wordt verwijderd.
        ensureWinkelmandAllowsDuplicates($conn);

        $cartImage = resolveImageFileName($game['img'], $game['naam']);

        $sql = "INSERT INTO " . CRUD_TABLE2 . " (game_id, naam, img, prijs)
                VALUES (:game_id, :naam, :img, :prijs)";

        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':game_id' => $game['id'],
            ':naam'    => $game['naam'],
            ':img'     => $cartImage,
            ':prijs'   => $game['prijs']
        ]);
    }
}

header("Location: shop.php");
exit;
?>