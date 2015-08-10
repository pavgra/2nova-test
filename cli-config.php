<?php
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

require_once "vendor/autoload.php";
require_once "app/Extensions/helpers.php";

$isDevMode = true;

$config = Setup::createYAMLMetadataConfiguration([base_path("config/doctrine")], $isDevMode);
$conn = config('database');

$entityManager = EntityManager::create($conn, $config);

return \Doctrine\ORM\Tools\Console\ConsoleRunner::createHelperSet($entityManager);