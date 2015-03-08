<?php
namespace CassandraPDO4Doctrine\Doctrine\DBAL\Driver\PDOCassandra;

use Doctrine\DBAL\Connection;

/**
 * PDO Cassandra driver.
 * @author Thang Tran <thang.tran@pyramid-consulting.com>
 */
class Driver implements \Doctrine\DBAL\Driver
{
    /**
     * {@inheritdoc}
     */
    public function connect(array $params, $username = null, $password = null, array $driverOptions = array())
    {
        $driverOptions[\PDO::ATTR_ERRMODE] = \PDO::ERRMODE_EXCEPTION;
        $conn = new \CassandraPDO4Doctrine\Doctrine\DBAL\Driver\PDOCassandra\CassandraConnection(
            $this->_constructPdoDsn($params),
            $username,
            $password,
            $driverOptions
        );
       if (isset($params['dbname']) && $params['dbname'] != '')
            $conn->exec("USE ".$params['dbname']);
        return $conn;
    }

    /**
     * Constructs the Cassandra PDO DSN.
     *
     * @param array $params
     *
     * @return string The DSN.
     */
    private function _constructPdoDsn(array $params)
    {
        $dsn = 'cassandra:';
        if (isset($params['host']) && $params['host'] != '') {
            $arrHosts = explode(',', $params['host']);           
        }
        if (isset($params['port'])) {
            $arrPorts = explode(',', $params['port']);
        }
        for($i=0;$i<count($arrHosts);$i++){
            $dsn .= 'host=' . $arrHosts[$i] . ';';
            $dsn .= 'port=' . $arrPorts[$i];
            $dsn .= ($i==count($arrHosts)-1)?';':',';
        }
        if (isset($params['cqlversion'])) {
            $dsn .= 'cqlversion=' . $params['cqlversion'] . ';';
        }
        return $dsn;
    }

    /**
     * {@inheritdoc}
     */
    public function getDatabasePlatform()
    {
        return new \CassandraPDO4Doctrine\Doctrine\DBAL\Platforms\CassandraPlatform();
    }

    /**
     * {@inheritdoc}
     */
    public function getSchemaManager(\Doctrine\DBAL\Connection $conn)
    {
        return new \Doctrine\DBAL\Schema\CassandraSchemaManager($conn);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'pdo_cassandra';
    }

    /**
     * {@inheritdoc}
     */
    public function getDatabase(\Doctrine\DBAL\Connection $conn)
    {
        $params = $conn->getParams();

        if (isset($params['dbname'])) {
            return $params['dbname'];
        }
        return null;
    }
}
