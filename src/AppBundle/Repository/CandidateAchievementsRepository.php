<?php

namespace AppBundle\Repository;

/**
 * CandidateAchievementsRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CandidateAchievementsRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @param $id
     * @return mixed
     */
    public function getAchievementsCandidate($id){
        return $this->createQueryBuilder('ca')
            ->select('ca.id, ca.description')
            ->where("ca.user = :id")
            ->setParameter('id',$id)
            ->orderBy('ca.id', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
