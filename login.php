<?php

function Login($clicked) {
    if ($clicked) {
        echo "";
        return;
    }

    echo '
    <form method="post">
        <button class="end" type="submit" name="login">login</button>
    </form>
    ';
}
?>