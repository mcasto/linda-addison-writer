<?php
$contents = file_get_contents("/Users/mikecasto/laravel-projects/linda-addison-writer/storage/app/private/freebies/1.md");

$seeds = json_decode(file_get_contents("/Users/mikecasto/laravel-projects/linda-addison-writer/app/Providers/seed-data.json"));

$freebies = $seeds->freebies;

foreach ($freebies as $key => $freebie) {
    if ($freebie->id == 1) {
        $freebies[$key]->story = $contents;
    }
}

$seeds->freebies = $freebies;

$output = json_encode($seeds);

file_put_contents("/Users/mikecasto/laravel-projects/linda-addison-writer/app/Providers/seed-data.json", $output);
