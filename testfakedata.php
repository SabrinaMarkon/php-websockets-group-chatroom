<?php
# Generate test chat messages. Run php testfakedata.php in CLI.
require('vendor/autoload.php');
require_once "config/Database.php";
$faker = Faker\Factory::create();
$username = "starlight";

$pdo = Database::connect();
$pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

for ($i = 0; $i <= 500; $i++) {
  $msg = $faker->sentence($nbWords = 6, $variableNbWords = true);
  $created_on = $faker->dateTimeBetween($startDate = '-48 hours', $endDate = 'now', $timezone = null);
  $created_on = $created_on->format('Y:m:d H:i:s');
  $sql = "insert into chatroom (username, msg, created_on) values (?, ?, ?)";
  $q = $pdo->prepare($sql);
  $q->execute(array($username, $msg, $created_on));  
}

Database::disconnect();

