<?php
// auteur: Ufukcan
// functie: verwijder een favoriet item
include_once "functions.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['favorite_id'])) {
    $favorite_id = (int) $_POST['favorite_id'];
    if ($favorite_id > 0) {
        removeFavorite($favorite_id);
    }
}

header("Location: favorieten.php");
exit;
