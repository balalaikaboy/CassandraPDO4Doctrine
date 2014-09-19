<?php
namespace Doctrine\DBAL\Schema;

/**
 * Schema manager for Cassandra.
 *
 * @author Thang Tran <thang.tran@pyramid-consulting.com>
 */
class CassandraSchemaManager extends AbstractSchemaManager
{

    /**
     * {@inheritdoc}
     */
    protected function _getPortableTableColumnDefinition($tableColumn)
    {
        $tableColumn = array_change_key_case($tableColumn, CASE_LOWER);

        $dbType = strtolower($tableColumn['type']);
        $dbType = strtok($dbType, '(), ');
        if (isset($tableColumn['length'])) {
            $length = $tableColumn['length'];
        } else {
            $length = strtok('(), ');
        }

        $fixed = null;

        if ( ! isset($tableColumn['name'])) {
            $tableColumn['name'] = '';
        }
        $type = $this->_platform->getDoctrineTypeMapping($dbType);
        $options = array();
        return new Column($tableColumn['field'], \Doctrine\DBAL\Types\Type::getType($type), $options);
    }
}
