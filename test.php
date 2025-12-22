<?php
require "config/database.php";

if ($conn) {
    echo "Database connected successfully!";
}

$colors = ["red", "blue", "green"];
echo $colors[0]; // red


