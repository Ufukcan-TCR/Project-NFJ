<?php
// auteur: Ufukcan
// functie: voeg een game toe aan favorieten
include_once "functions.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['game_id'])) {
    $game_id = (int) $_POST['game_id'];
    addFavorite($game_id);
}

header("Location: shop.php");
exit;
