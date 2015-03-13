<?php
namespace CassandraPDO4Doctrine\tests;
use CassandraPDO4Doctrine\tests\entity\Product;
use Doctrine\ORM\Query\ResultSetMapping;

class CP4DTest extends CP4DBaseTestCase {

    // FIXME: No assertions, just check
    public function testCreateNewRecord() {
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        $product = new Product();
        $product->setName('A Foo Bar 2');
        $product->setPrice(1.12);
        $product->setDescription('blah blah blah');
        $product->setCreated(new \DateTime());
        $em = $this->getEntityManager();
        $em->persist($product);
        $em->flush();
    }

    public function testRepositoryFindBy() {
        $repo = $this->getRepository(Product::className());
        /** @var Product[] $products */
        $products = $repo->findBy(
            array('name' => 'prod2'),
            array('created' => 'ASC')
        );

        $this->assertNotEmpty($products);
        $this->assertInstanceOf(Product::className(),$products[0]);
        // FIXME: date in far future
        $this->assertGreaterThan($products[0]->getCreated(),new \DateTime());
    }

    public function testFindBySQL() {
        $em = $this->getEntityManager();
        //FIXME: How to define short class name ?
        $query = $em->createQuery("SELECT p FROM " . Product::className() . " p");
        $products = $query->getResult();

        $this->assertNotEmpty($products);
        $this->assertInstanceOf(Product::className(),$products[0]);
    }

    /**
     * FIXME: Throws type exception
Doctrine\DBAL\DBALException: An exception occurred while executing 'SELECT p0_.name AS name0, p0_.price AS price1, p0_.description AS description2, p0_.created AS created3 FROM product p0_ WHERE p0_.price > ? AND p0_.name = ? AND p0_.created = ? ORDER BY p0_.created ASC' with params [19.99, "A Foo Bar 1", "2014-12-10"]:
CQLSTATE[HY000] [2] Invalid STRING constant (19.99) for price of type float
     * @-requires PHP 999.9.9
     */
    public function testFindByQueryBuilder() {
        $repo = $this->getRepository(Product::className());
        $query = $repo->createQueryBuilder('p')
//            ->where('p.price > :price')
            ->where('p.name =:name')
            ->andWhere('p.created =:created')
//            ->setParameter('price', 19.99,'float')//must specify type explicitly
            ->setParameter('name', 'prod1')
            ->setParameter('created', '2014-12-10')
            ->orderBy('p.created', 'ASC')
            ->getQuery();
        $products = $query->getResult();

        $this->assertNotEmpty($products);
        $this->assertInstanceOf(Product::className(),$products[0]);
    }

    public function testFindByNativeCQL() {
        $em = $this->getEntityManager();
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('name', 'name');
        $rsm->addScalarResult('price', 'price');

        $query = $em->createNativeQuery('SELECT * FROM product WHERE name = ?', $rsm);
        $query->setParameter(1, 'prod1');
        $products = $query->getResult();

        $this->assertNotEmpty($products);
        $this->assertInternalType('array',$products[0]);
    }

    public function testCounting() {
        $em = $this->getEntityManager();
        $query = $em->createQuery("SELECT count(p.name) FROM " . Product::className() . " p WHERE p.name= 'prod2' ORDER BY p.created ASC");
        $cnt = $query->getResult();
        var_dump($cnt);
        $this->assertEquals($cnt,[0=>[1=>1]]);
    }

}