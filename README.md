CassandraPDO4Doctrine
===============

## Introduction
CassandraPDO4Doctrine is the Cassandra driver for Doctrine2. It extends Doctrine2 PDOConnection, using YACassandraPDO driver (https://github.com/Orange-OpenSource/YACassandraPDO). 

## Installation
* Download [Apache Cassandra PDO](https://github.com/Orange-OpenSource/YACassandraPDO) and compile into a PHP extension. 
* After cloning and building, make sure the pdo extension is enabled by adding this line to your cli php.ini file (usually /etc/php5/cli/php.ini):
```
extension=pdo_cassandra.so
```
* See tests in CassandraPDO4Doctrine/tests/src/CP4DTest.php for details
