<?php
/**
 * Created by PhpStorm.
 * Date: 16.04.18
 * Time: 16:01
 */

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class CompanyDetailsRepository extends EntityRepository
{
    /**
     * @param $id
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getBusinessCompanyDetails($id){
        return $this->createQueryBuilder('cd')
            ->select('cd.name, cd.address, cd.addressCountry, cd.addressState, cd.addressZipCode, cd.addressCity, cd.addressSuburb, cd.addressStreet, cd.addressStreetNumber, cd.addressBuildName, cd.addressUnit, cd.companySize, cd.jse ,cd.industry, cd.description')
            ->where("cd.user = :id")
            ->setParameter('id',$id)
            ->getQuery()
            ->getOneOrNullResult();
    }

}