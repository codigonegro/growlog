<?php
function generateRandomString($length = 4) {
    return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
}

// Echo the random string.
// Optionally, you can give it a desired string length up to 62 characters.
echo generateRandomString();
?>