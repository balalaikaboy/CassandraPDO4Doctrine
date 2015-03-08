<?php

namespace CassandraPDO4Doctrine\tests;

use CassandraPDO4Doctrine\tests\entity\Product;

class CP4DTest extends CP4DBaseTestCase {

    public function testCreateNewRecord() {
        $product = new Product();
        $product->setName('A Foo Bar 1');
        $product->setPrice(1.12);
        $product->setDescription('blah blah blah');
        $product->setCreated(new \DateTime());
        $em = $this->getEntityManager();
        $em->persist($product);
        $em->flush();
    }

}