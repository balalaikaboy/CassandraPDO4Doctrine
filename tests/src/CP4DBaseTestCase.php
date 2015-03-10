<?php

namespace CassandraPDO4Doctrine\tests;

use CassandraPDO4Doctrine\Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;

class CP4DBaseTestCase extends \PHPUnit_Framework_TestCase {

    static protected function _getDataDir() {
        return realpath(__DIR__ . '/../data');
    }

    static public function setUpBeforeClass() {
        $D = self::_getDataDir();
        system("cqlsh < {$D}/db_create.cql3");
    }

    static public function tearDownAfterClass() {
        $D = self::_getDataDir();
        system("cqlsh < {$D}/db_drop.cql3");
    }

    public function setUp() {
        $D = self::_getDataDir();
        system("cqlsh < {$D}/db_data.cql3");
    }

    /**
     * @return EntityManager
     * @throws \Doctrine\ORM\ORMException
     */
    public function getEntityManager() {

        $paths = array(__DIR__ . "/entity/");
        $isDevMode = true;

        // the connection configuration
        $dbParams = array(
            'driver'   => $GLOBALS['DB_DRIVER'],
            'host'     => $GLOBALS['DB_HOST'],
            'port'     => $GLOBALS['DB_PORT'],
            'user'     => $GLOBALS['DB_USER'],
            'password' => $GLOBALS['DB_PASSWD'],
            'dbname'   => $GLOBALS['DB_DBNAME'],
        );

        $config = Setup::createAnnotationMetadataConfiguration($paths, $isDevMode,null,null,false);
        $conn = DriverManager::getConnection($dbParams,$config);
        
        $entityManager = EntityManager::create($conn, $config);
        return $entityManager;
    }

}