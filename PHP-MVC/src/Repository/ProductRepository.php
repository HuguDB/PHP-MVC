<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;

class ProductRepository extends EntityRepository
{
    public function __construct(
        EntityManagerInterface $em
    ) {
        $productMetadata = new ClassMetadata(Product::class);
        parent::__construct($em, $productMetadata);
    }
}