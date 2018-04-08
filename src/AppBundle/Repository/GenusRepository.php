<?php

namespace AppBundle\Repository;


use AppBundle\Entity\Genus;
use Doctrine\ORM\EntityRepository;

class GenusRepository extends EntityRepository
{
    /**
     * @return Genus[]
     */
    public function findAllPublishedOrderedByRecentlyActive()
    {
        return $this->createQueryBuilder('genus')
            ->andWhere('genus.isPublished = :isPublished')
            ->setParameter('isPublished', true)
            // using the inverse side mapping of the relationship
            ->leftJoin('genus.notes', 'genus_note')
            ->orderBy('genus_note.createdAt', 'DESC')
            // join on a relation property
            ->leftJoin('genus.genusScientists', 'genusScientist')
            ->addSelect('genusScientist')
            ->getQuery()
            ->execute();
        // use execute() to get an array of results
        // or getOneOrNullResult to get one result or null
    }
}