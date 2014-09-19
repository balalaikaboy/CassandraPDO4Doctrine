CassandraPDO4Doctrine
===============

## Introduction
CassandraPDO4Doctrine is the Cassandra driver for Doctrine2. It extends Doctrine2 PDOConnection, using YACassandraPDO driver (https://github.com/Orange-OpenSource/YACassandraPDO). 

## Installation
1. Download [Apache Cassandra PDO](https://github.com/Orange-OpenSource/YACassandraPDO) and compile into a PHP extension. 
2. After cloning and building, make sure the pdo extension is enabled by adding this line to your cli php.ini file (usually /etc/php5/cli/php.ini):
```
extension=pdo_cassandra.so
```
3.Set up your Symfony2 project (or any PHP framework that uses Doctrine2) and copy the files in CassandraPDO4Doctrine directory into the coresponding folders.
```
* New files:
vendor/doctrine/dbal/lib/Doctrine/DBAL/Driver/PDOCassandra/Driver.php
vendor/doctrine/dbal/lib/Doctrine/DBAL/Platforms/CassandraPlatform.php
vendor/doctrine/dbal/lib/Doctrine/DBAL/Platforms/Keywords/CassandraKeywords.php
vendor/doctrine/dbal/lib/Doctrine/DBAL/Schema/CassandraSchemaManager.php
vendor/doctrine/dbal/lib/Doctrine/DBAL/Driver/PDOCassandra/CassandraConnection.php 

* Modified files:
vendor/doctrine/dbal/lib/Doctrine/DBAL/DriverManager.php (added pdo_cassandra)
vendor/doctrine/dbal/lib/Doctrine/DBAL/Types/DateTimeType.php (getDateStringFromHex for timestamp type from PDO)
vendor/doctrine/dbal/lib/Doctrine/DBAL/Types/FloatType.php (override getBindingType())

```

## Usage
1. Create a KEYSPACE and COLUMNFAMILY in your Cassandra for testing (use cqlsh or another tool that you prefer). Below is the stuff used for my test
```
CREATE KEYSPACE mydb WITH REPLICATION = { 'class' : 'SimpleStrategy', 'replication_factor' : 3 };
USE mydb;

CREATE TABLE product (name varchar,price float,description varchar,created timestamp, 
	PRIMARY KEY (name,created,price)) 
	WITH CLUSTERING ORDER BY (created DESC);
INSERT INTO product (name, price, description, created) VALUES ('prod1', 1.00,'prod #1 desc',dateof(now()));
INSERT INTO product (name, price, description, created) VALUES ('prod2', 2.00,'prod #2 desc',dateof(now()));
INSERT INTO product (name, price, description, created) VALUES ('prod3', 3.00,'prod #3 desc',dateof(now()));
```

2. Config your DB connection for Cassandra. E.g.
```
parameters:
    database_driver:   pdo_cassandra
    database_host:     127.0.0.1
    database_port:     9160
    database_name:     mydb
    database_user:     mydbuser
    database_password: mypass
```

3. Tests
All tests below are conducted on Symfony2 enviroment.

- Create a new record
```
$product = new Product();
$product->setName('A Foo Bar 1');
$product->setPrice(1.12);
$product->setDescription('blah blah blah');
$product->setCreated();
$em = $this->getDoctrine()->getManager();
$em->persist($product);
$em->flush();
```
- Find records using repo object
```
$repo = $this->getDoctrine()
            ->getRepository('MyBundle:Product');
$products = $repo->findBy( 
                array('name' => 'A Foo Bar 1'),
                array('created' => 'ASC')));
```
- Find records using DQL
```
$em = $this->getDoctrine()->getManager();
$query = $em->createQuery("SELECT p FROM MyBundle:Product p");
$products = $query->getResult();
```
- Find records using Doctrine's Query Builder
```
$repo = $this->getDoctrine()
            ->getRepository('PycoLumenakiBundle:Product');
$query = $repo->createQueryBuilder('p')
  ->where('p.price > :price')
  ->andWhere('p.name =:name')
  ->andWhere('p.created =:created')
  ->setParameter('price', 19.99,'float')//must specify type explicitly
  ->setParameter('name', 'A Foo Bar 1')
  ->setParameter('created', '2014-12-10')
  ->orderBy('p.created', 'ASC')
  ->getQuery();
$products = $query->getResult();
```
- Find records using Native SQL (i.e. CQL)
```
$em = $this->getDoctrine()->getManager();
$rsm = new ResultSetMapping();
//build rsm
$rsm->addScalarResult('name', 'name');
$rsm->addScalarResult('price', 'price');

$query = $em->createNativeQuery('SELECT * FROM product WHERE name = ?', $rsm);
$query->setParameter(1, 'A Foo Bar 1');  
var_dump($query->getResult());
```
- Counting
```
em = $this->getDoctrine()->getManager();
$query = $em->createQuery("SELECT count(p.name) FROM PycoLumenakiBundle:Product p WHERE p.name= 'A Foo Bar 1' ORDER BY p.created ASC");
$cnt = $query->getResult();
```
