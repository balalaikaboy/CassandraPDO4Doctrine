<?php
namespace CassandraPDO4Doctrine\tests;
use CassandraPDO4Doctrine\tests\entity\Product;
use Doctrine\ORM\Query\ResultSetMapping;

class CP4DTest extends CP4DBaseTestCase {

    // FIXME: No assertions, just check
    public function testCreateNewRecord() {
        date_default_timezone_set('UTC');
        $product = new Product();
        $product->setName('A Foo Bar');
        $product->setPrice(1.12);
        $product->setDescription('blah blah blah');
        $product->setCreated(new \DateTime(date('Y-m-d')));
        $em = $this->getEntityManager();
        $em->persist($product);
        $em->flush();
    }

    public function testRepositoryFindBy() {
        $repo = $this->getRepository(Product::className());
        /** @var Product[] $products */
        $products = $repo->findBy(
            array('name' => 'prod1'),
            array('created' => 'ASC')
        );

        $this->assertNotEmpty($products);
        $this->assertInstanceOf(Product::className(),$products[0]);
        $this->assertEquals(new \DateTime('2015-01-01 01:01:01+0000'),$products[0]->getCreated());
    }

    public function testFindBySQL() {
        $em = $this->getEntityManager();
        //FIXME: How to define short class name ?
        $query = $em->createQuery("SELECT p FROM " . Product::className() . " p");
        $products = $query->getResult();

        $this->assertNotEmpty($products);
        $this->assertInstanceOf(Product::className(),$products[0]);
    }
    public function testFindByQueryBuilder() {
        $repo = $this->getRepository(Product::className());
        $query = $repo->createQueryBuilder('p')

            ->where('p.name =:name')
            ->setParameter('name', 'prod3')

            ->andWhere('p.created =:created')
            ->setParameter('created', new \DateTime('2015-01-03 01:01:01+0000'),'cassandra_datetime')

            // FIXME: Uncommenting this makes result empty
            ->andWhere('p.price =:price')
            ->setParameter('price', 1.12,'cassandra_float')//must specify type explicitly

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
        $query->setParameter(1, 'prod2');
        $products = $query->getResult();

        $this->assertNotEmpty($products);
        $this->assertInternalType('array',$products[0]);
    }

    public function testCounting() {
        $em = $this->getEntityManager();
        $query = $em->createQuery("SELECT count(p.name) FROM " . Product::className() . " p WHERE p.name= 'prod2' ORDER BY p.created ASC");
        $cnt = $query->getResult();
        $this->assertEquals($cnt,[0=>[1=>1]]);
    }

}