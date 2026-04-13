<?php
// auteur: Ufukcan
// functie: verwijder een game uit het winkelmandje
include_once "functions.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cart_id'])) {
    $cart_id = $_POST['cart_id'];

    $conn = connectDb();

    $sql = "DELETE FROM " . CRUD_TABLE2 . " WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':id' => $cart_id]);
}

header("Location: winkelmandje.php");
exit;
?>