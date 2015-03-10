<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use Doctrine\ORM\Tools\Console\ConsoleRunner;
use CassandraPDO4Doctrine\tests\CP4DBaseTestCase;

// replace with file to your own project bootstrap
// require_once 'bootstrap.php';

// replace with mechanism to retrieve EntityManager in your app
// $entityManager = GetEntityManager();
$entityManager = (new CP4DBaseTestCase())->getEntityManager();

return ConsoleRunner::createHelperSet($entityManager);