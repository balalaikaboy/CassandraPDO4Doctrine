<?php
namespace CassandraPDO4Doctrine\tests;
use CassandraPDO4Doctrine\tests\entity\Product;

class CP4DTest extends CP4DBaseTestCase {

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

}