<?php
namespace CassandraPDO4Doctrine\Doctrine\DBAL\Driver\PDOCassandra;

/**
 * @author Thang Tran <thang.tran@pyramid-consulting.com>
 */
class CassandraConnection extends \Doctrine\DBAL\Driver\PDOConnection
{
    public function beginTransaction(){
        return true;     
    }
    /**
     * {@inheritdoc}
     */
    public function commit()
    {
        return true;
    }

    /**
     * {@inheritdoc}non-PHPdoc)
     */
    public function rollBack()
    {
        return true;
    }
    /**
     * {@inheritdoc}non-PHPdoc)
     */
    function prepare($prepareString)
    {
        $prepareString = $this->removeTableAlias($prepareString);
        $prepareString = $this->normalizeCount($prepareString);
        return parent::prepare($prepareString);
        
    }
    /**
     * {@inheritdoc}non-PHPdoc)
     */
    public function query()
    {
        $args = func_get_args();
        $sql = $this->removeTableAlias($args[0]);
        $sql = $this->normalizeCount($sql);
        return parent::query($sql);
    }
    /**
     * For COUNT(), Cassandra only allows two formats: COUNT(1) and COUNT(*)
     * @param string $sql
     */
    private function normalizeCount($sql)
    {
        $sql = trim(preg_replace('/COUNT\(.*\)/i','COUNT(1)', $sql));
        return $sql;
    }
    /**
     * Cassandra does not support table alias. Let's remove them
     * @param string $sql
     */
    private function removeTableAlias($sql)
    {
        //clean up extra space
        $sql = trim(preg_replace('/\s+/',' ', $sql));
        $arrSplitByFROM = explode('FROM ', $sql,2);
        if(count($arrSplitByFROM)>=2)
        {
            $arrSplit4TableAlias = explode(' ',trim($arrSplitByFROM[1]),3);
            if(count($arrSplit4TableAlias)>=2 
                    && strtoupper($arrSplit4TableAlias[1])!='WHERE'){   
                //replace table alias and merge stuff
                $alias = $arrSplit4TableAlias[1];
                $arrSplit4TableAlias[1]='';
                $arrSplitByFROM[0] = str_replace($alias.'.','',$arrSplitByFROM[0]);
                $arrSplitByFROM[1] = implode(' ', $arrSplit4TableAlias);
                $arrSplitByFROM[1] = str_replace($alias.'.','',$arrSplitByFROM[1]);
                return implode('FROM ', $arrSplitByFROM);        
            }
            
        }
        return $sql;
    }
}
