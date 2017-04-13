<?php

// Create key
$key = '';
for ($i = 0; $i < 15; $i++) {
   $key .= chr(rand(48, 57));
}

// Hash stored value
$keyhash = hash('sha256', $key);