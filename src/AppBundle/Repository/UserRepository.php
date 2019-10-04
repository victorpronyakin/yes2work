<?php
/**
 * Created by PhpStorm.
 * Date: 01.03.18
 * Time: 17:39
 */

namespace AppBundle\Repository;


use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\Request;

class UserRepository extends EntityRepository
{
    /**
     * @param $roles
     * @return array|mixed
     */
    public function findByRoles($roles)
    {
        $result = [];
        if(!empty($roles)){
            $qb = $this->createQueryBuilder('u');
            $qb->select('u')
                ->where('u.roles LIKE :roles0')
                ->setParameter('roles0', '%"'.$roles[0].'"%');
            for($i=1;$i<count($roles);$i++){
                $qb->orWhere("u.roles LIKE :roles$i")
                    ->setParameter("roles$i", '%"'.$roles[$i].'"%');
            }
            $result = $qb->getQuery()->getResult();
        }


        return $result;
    }

    /**
     * @param $roles
     * @param array $params
     * @return array|mixed
     */
    public function findByRolesForSystem($roles, $params = array())
    {
        $result = [];
        if(!empty($roles)){
            $qb = $this->createQueryBuilder('u');
            $qb->select('u.id,u.firstName, u.lastName, u.email, u.phone, u.roles')
                ->where('u.roles LIKE :roles0')
                ->setParameter('roles0', '%"'.$roles[0].'"%');
            for($i=1;$i<count($roles);$i++){
                $qb->orWhere("u.roles LIKE :roles$i")
                    ->setParameter("roles$i", '%"'.$roles[$i].'"%');
            }

            if(isset($params['search']) && !empty($params['search'])){
                $search = explode(" ", $params['search']);
                if(count($search) > 1){
                    $qb->andWhere('(u.firstName LIKE :search1) AND (u.lastName LIKE :search2)')
                        ->setParameter('search1', '%'.$search[0].'%')
                        ->setParameter('search2', '%'.$search[1].'%');
                }
                else{
                    $qb->andWhere('(u.firstName LIKE :search) OR (u.lastName LIKE :search)')
                        ->setParameter('search', '%'.$params['search'].'%');
                }
            }

            if(isset($params['orderBy']) && !empty($params['orderBy']) && in_array($params['orderBy'], ['Name', 'Surname', 'Email', 'Role'])){
                if(isset($params['orderSort']) && !empty($params['orderSort']) && in_array($params['orderSort'], ['asc', 'desc'])){
                    if($params['orderBy'] == 'Name'){
                        $qb->orderBy('u.firstName', $params['orderSort']);
                    }
                    elseif ($params['orderBy'] == 'Surname'){
                        $qb->orderBy('u.lastName', $params['orderSort']);
                    }
                    elseif ($params['orderBy'] == 'Email'){
                        $qb->orderBy('u.email', $params['orderSort']);
                    }
                    elseif ($params['orderBy'] == 'Role'){
                        $qb->orderBy('u.roles', $params['orderSort']);
                    }
                }
            }

            $result = $qb->getQuery()->getResult();
        }


        return $result;
    }


    /**
     * @param array $params
     * @return mixed
     */
    public function getAllClient($params=array()){
        $query = $this->createQueryBuilder('u')
            ->from("AppBundle:CompanyDetails","c")
            ->select('u.id,u.firstName,u.lastName,u.phone,u.email,u.agentName,c.name as companyName, u.approved, u.enabled')
            ->where("u.id = c.user")
            ->andWhere("u.roles = :role")
            ->setParameter('role',serialize(['ROLE_CLIENT']));
        if(isset($params['search']) && !empty($params['search'])){
            $query->andWhere("(u.firstName LIKE :search OR u.lastName LIKE :search OR u.email LIKE :search OR u.phone LIKE :search OR c.name LIKE :search)")
                ->setParameter('search', "%".$params['search']."%");
        }
        if(isset($params['jobTitle']) && !empty($params['jobTitle'])){
            $query->andWhere('u.jobTitle LIKE :jobTitle')
                ->setParameter('jobTitle', "%".$params['jobTitle']."%");
        }
        if(isset($params['address']) && !empty($params['address'])){
            $query->andWhere('c.address LIKE :address')
                ->setParameter('address', "%".$params['address']."%");
        }
        if(isset($params['jse']) && ($params['jse'] == 'false' || $params['jse'] == 'true')){
            $query->andWhere('c.jse = :jse');
            if($params['jse'] == 'false')
                $query->setParameter('jse', 0);
            else
                $query->setParameter('jse', 1);
        }
        if(isset($params['industry']) && $params['industry']>0){
            $query->andWhere('c.industry = :industry')
                ->setParameter('industry', $params['industry']);
        }
        if(isset($params['size']) && $params['size']>0){
            $query->andWhere('c.companySize = :size')
                ->setParameter('size', $params['size']);
        }
        if(isset($params['enabled']) && ($params['enabled'] == 'false' || $params['enabled'] == 'true')){
            $query->andWhere('u.enabled = :enabled');
            if($params['enabled'] == 'false')
                $query->setParameter('enabled', 0);
            else
                $query->setParameter('enabled', 1);
        }

        if(isset($params['orderBy']) && !empty($params['orderBy']) && in_array($params['orderBy'], ['Name', 'Company', 'Email', 'Phone'])){
            if(isset($params['orderSort']) && !empty($params['orderSort']) && in_array($params['orderSort'], ['asc', 'desc'])){
                if($params['orderBy'] == 'Name'){
                    $query->orderBy('u.firstName', $params['orderSort']);
                    $query->addOrderBy('u.lastName', $params['orderSort']);
                }
                elseif ($params['orderBy'] == 'Company'){
                    $query->orderBy('c.name', $params['orderSort']);
                }
                elseif ($params['orderBy'] == 'Email'){
                    $query->orderBy('u.email', $params['orderSort']);
                }
                elseif ($params['orderBy'] == 'Phone'){
                    $query->orderBy('u.phone', $params['orderSort']);
                }
            }
            else{
                $query->orderBy('u.created', 'DESC');
            }
        }
        else{
            $query->orderBy('u.created', 'DESC');
        }


        return $query->getQuery()->getResult();
    }

    /**
     * @return mixed
     */
    public function getAllClientFullDetails(){
        $query = $this->createQueryBuilder('u')
            ->from("AppBundle:CompanyDetails","c")
            ->select('u.id, c.name as companyName, u.jobTitle, c.address, c.addressCountry, c.addressState, c.addressZipCode, c.addressCity, c.addressSuburb, c.addressStreet, c.addressStreetNumber, c.addressBuildName, c.addressUnit, c.companySize, c.jse, c.industry, c.description')
            ->where("u.id = c.user")
            ->andWhere("u.roles = :role")
            ->andWhere("u.enabled = true")
            ->setParameter('role',serialize(['ROLE_CLIENT']));


        return $query->getQuery()->getResult();
    }

    /**
     * @param $id
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getBusinessProfile($id){
        return $this->createQueryBuilder('u')
            ->select('u.id, u.firstName,u.lastName,u.jobTitle,u.phone,u.email,u.agentName')
            ->where("u.id = :id")
            ->setParameter('id',$id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param array $params
     * @return mixed
     */
    public function getClientApprove($params=array()){
        $query = $this->createQueryBuilder('u')
            ->from("AppBundle:CompanyDetails","c")
            ->select('u.id,u.firstName,u.lastName,u.phone,u.email,c.name as companyName')
            ->where("u.id = c.user")
            ->andWhere("u.approved IS NULL")
            ->andWhere("u.roles = :role")
            ->setParameter('role',serialize(['ROLE_CLIENT']));

        if(isset($params['lm']) && $params['lm'] > 0){
            $query->setMaxResults($params['lm'])
                ->setFirstResult(0);
        }

        if(isset($params['search']) && !empty($params['search'])){
            $query->andWhere("(u.firstName LIKE :search OR u.lastName LIKE :search OR u.email LIKE :search OR u.phone LIKE :search OR c.name LIKE :search)")
                ->setParameter('search', "%".$params['search']."%");
        }

        if(isset($params['orderBy']) && !empty($params['orderBy']) && in_array($params['orderBy'], ['Name', 'Company', 'Email', 'Phone'])){
            if(isset($params['orderSort']) && !empty($params['orderSort']) && in_array($params['orderSort'], ['asc', 'desc'])){
                if($params['orderBy'] == 'Name'){
                    $query->orderBy('u.firstName', $params['orderSort']);
                    $query->addOrderBy('u.lastName', $params['orderSort']);
                }
                elseif ($params['orderBy'] == 'Company'){
                    $query->orderBy('c.name', $params['orderSort']);
                }
                elseif ($params['orderBy'] == 'Email'){
                    $query->orderBy('u.email', $params['orderSort']);
                }
                elseif ($params['orderBy'] == 'Phone'){
                    $query->orderBy('u.phone', $params['orderSort']);
                }
            }
            else{
                $query->orderBy('u.created', 'DESC');
            }
        }
        else{
            $query->orderBy('u.created', 'DESC');
        }

        return $query->getQuery()->getResult();
    }

    /**
     * @param array $params
     * @return mixed
     */
    public function getAllCandidate($params = array()){
        $query = $this->createQueryBuilder('u')
            ->from("AppBundle:ProfileDetails", 'pd')
            ->select('u.id,u.firstName,u.lastName, u.phone,u.email,u.agentName,u.enabled, u.approved, pd.percentage, pd.video')
            ->where('u.id = pd.user')
            ->andWhere("u.roles = :role")
            ->setParameter('role',serialize(['ROLE_CANDIDATE']))
            ->orderBy('u.created', 'DESC');

        if(isset($params['search']) && !empty($params['search'])){
            $query->andWhere("(u.firstName LIKE :search OR u.lastName LIKE :search OR u.email LIKE :search)")
                ->setParameter('search', "%".$params['search']."%");
        }


        if(isset($params['gender']) && $params['gender'] != 'All' && ($params['gender'] == 'Male' || $params['gender'] == 'Female')){
            $query->andWhere('pd.gender = :gender')
                ->setParameter('gender',$params['gender']);
        }

        if(isset($params['ethnicity']) && $params['ethnicity'] != 'null' && $params['ethnicity'] != NULL && !empty($params['ethnicity']) && $params['ethnicity']!="All"){
            $query->andWhere('pd.ethnicity = :ethnicity')
                ->setParameter('ethnicity', $params['ethnicity']);
        }

        if(isset($params['nationality']) && $params['nationality'] != 'All' && $params['nationality'] != NULL && $params['nationality'] >0){
            $query->andWhere('pd.nationality = :nationality')
                ->setParameter('nationality', $params['nationality']);
        }

        if(isset($params['location']) && $params['location'] != 'null' && $params['location'] != NULL && !empty($params['location']) && $params['location'] != 'All'){
            $query->andWhere('pd.citiesWorking LIKE :location')
                ->setParameter('location', '%'.$params['location'].'%');
        }


        if(isset($params['video']) && $params['video'] != 'All' && $params['video'] != NULL && $params['video'] == 1){
            $query->andWhere('pd.video != :video')
                ->setParameter('video', serialize(NULL));
        }

        if(isset($params['criminal']) && $params['criminal'] != 0 && ($params['criminal'] == 'false' || $params['criminal'] == false || $params['criminal'] == 'true' || $params['criminal'] == true)){
            $query->andWhere('pd.criminal = :criminal');
            if($params['criminal'] == 'false' || $params['criminal'] == false)
                $query->setParameter('criminal', 0);
            else
                $query->setParameter('criminal', 1);
        }
        if(isset($params['credit']) && $params['credit'] != 0 && ($params['credit'] == 'false' || $params['credit'] == false || $params['credit'] == 'true' || $params['credit'] == true)){
            $query->andWhere('pd.credit = :credit');
            if($params['credit'] == 'false' || $params['credit'] == false)
                $query->setParameter('credit', 0);
            else
                $query->setParameter('credit', 1);
        }

        if(isset($params['availability']) && $params['availability'] != 'All' && $params['availability'] != NULL && $params['availability']>0 && $params['availability']<4){
            if($params['availability'] == 1){
                $now = new \DateTime();
                $query->andWhere("(pd.availability = true OR (pd.availability = false AND pd.availabilityPeriod = 4 AND DATE_FORMAT(pd.dateAvailability, '%Y-%m-%d') <= :now))")
                    ->setParameter('now', $now->format('Y-m-d'));
            }
            elseif ($params['availability'] == 2){
                $now = new \DateTime('+1 month');
                $query->andWhere("(pd.availability = true OR (pd.availability = false AND pd.availabilityPeriod = 4 AND DATE_FORMAT(pd.dateAvailability, '%Y-%m-%d') <= :now) OR pd.availabilityPeriod = 1)")
                    ->setParameter('now', $now->format('Y-m-d'));
            }
            else{
                $now = new \DateTime('+3month');
                $query->andWhere("((pd.availability = true) OR (pd.availability = false AND pd.availabilityPeriod = 4 AND DATE_FORMAT(pd.dateAvailability, '%Y-%m-%d') <= :now) OR (pd.availabilityPeriod = 1 OR pd.availabilityPeriod = 2 OR pd.availabilityPeriod = 3))")
                    ->setParameter('now', $now->format('Y-m-d'));
            }
        }

        if(isset($params['enabled']) && $params['enabled'] == 'false'){
            $query->andWhere('u.enabled = :enabled')
                ->setParameter('enabled', 0);
        }
        else{
            $query->andWhere('u.enabled = :enabled')
                ->setParameter('enabled', 1);
        }


        if(isset($params['profileComplete']) && ($params['profileComplete'] == 'false' || $params['profileComplete'] == 'true')){
            if($params['profileComplete'] == 'false' || $params['profileComplete'] == false){
                $query->andWhere('pd.percentage < 50 OR pd.video IS NULL OR pd.video LIKE :empty_array OR pd.video LIKE :empty_null OR pd.copyOfID IS NULL OR pd.copyOfID LIKE :empty_array OR pd.copyOfID LIKE :empty_null')
                    ->setParameter('empty_array', "%".serialize(array())."%")
                    ->setParameter('empty_null', "%".serialize(NULL)."%");
            }
            else{
                $query->andWhere('pd.percentage > 50')
                    ->andWhere('pd.video IS NOT NULL')
                    ->andWhere('pd.copyOfID IS NOT NULL')
                    ->andWhere('pd.video NOT LIKE :empty_array')
                    ->andWhere('pd.copyOfID NOT LIKE :empty_array')
                    ->setParameter('empty_array', "%".serialize(array())."%")
                    ->andWhere('pd.video NOT LIKE :empty_null')
                    ->andWhere('pd.copyOfID NOT LIKE :empty_null')
                    ->setParameter('empty_null', "%".serialize(NULL)."%");
            }
        }

        return $query->getQuery()->getResult();
    }

    /**
     * @param array $params
     * @return mixed
     */
    public function getAllCandidateNew($params = array()){
		$additional_from = [];
        $query = $this->createQueryBuilder('u')
            ->from("AppBundle:ProfileDetails", 'pd')
            ->select('u.id,u.firstName,u.lastName, u.phone,u.email,u.agentName,u.enabled, 
            u.approved, pd.percentage, pd.video, pd.copyOfID')
            ->where('u.id = pd.user')
            ->andWhere("u.roles = :role")
            ->setParameter('role',serialize(['ROLE_CANDIDATE']))
            ->orderBy('u.created', 'DESC');

        if(isset($params['search']) && !empty($params['search'])){
            $query->andWhere("(u.firstName LIKE :search OR u.lastName LIKE :search OR u.email LIKE :search)")
                ->setParameter('search', "%".$params['search']."%");
        }

        if(isset($params['gender']) && $params['gender'] != 'null' && $params['gender'] != NULL){
            if(is_array($params['gender'])){
                $genders = $params['gender'];
            }
            else{
                $genders = explode(',',$params['gender']);
            }
            if(!empty($genders) && !in_array('All', $genders) ){
                $str = '(';
                foreach ($genders as $key=>$gender){
                    if($key>0){
                        $str .= " OR pd.gender='$gender'";
                    }
                    else{
                        $str .= "pd.gender='$gender'";
                    }
                }
                $str .= ')';
                $query->andWhere($str);
            }
        }

        if(isset($params['ethnicity']) && $params['ethnicity'] != 'null' && $params['ethnicity'] != NULL){
            if(is_array($params['ethnicity'])){
                $ethnicitys = $params['ethnicity'];
            }
            else{
                $ethnicitys = explode(',',$params['ethnicity']);
            }
            if(!empty($ethnicitys) && !in_array('All', $ethnicitys) ){
                $str = '(';
                foreach ($ethnicitys as $key=>$ethnicity){
                    if($key>0){
                        $str .= " OR pd.ethnicity='$ethnicity'";
                    }
                    else{
                        $str .= "pd.ethnicity='$ethnicity'";
                    }
                }
                $str .= ')';
                $query->andWhere($str);
            }
        }

        if(isset($params['location']) && $params['location'] != 'null' && $params['location'] != NULL){
            if(is_array($params['location'])){
                $locations = $params['location'];
            }
            else{
                $locations = explode(',',$params['location']);
            }
            if(!empty($locations) && !in_array('All', $locations) ){
                $str = '(';
                foreach ($locations as $key=>$location){
                    $location = "%".$location."%";
                    if($key>0){
                        $str .= " OR pd.citiesWorking LIKE '$location'";
                    }
                    else{
                        $str .= "pd.citiesWorking LIKE '$location'";
                    }
                }
                $str .= ')';
                $query->andWhere($str);
            }
        }

        if(isset($params['availability']) && $params['availability'] != 'null' && $params['availability'] != NULL){
            if(is_array($params['availability'])){
                $availabilitys = $params['availability'];
            }
            else{
                $availabilitys = explode(',',$params['availability']);
            }
            if(!empty($availabilitys) && !in_array('All', $availabilitys) ){
                $str = '(';
                foreach ($availabilitys as $key=>$availability){
                    if($availability == 1){
                        $nowD = new \DateTime();
                        $now = $nowD->format('Y-m-d');
                        if($key>0){
                            $str .= " OR (pd.availability = true OR (pd.availability = false AND pd.availabilityPeriod = 4 AND DATE_FORMAT(pd.dateAvailability, '%Y-%m-%d') <= '$now'))";
                        }
                        else{
                            $str .= "(pd.availability = true OR (pd.availability = false AND pd.availabilityPeriod = 4 AND DATE_FORMAT(pd.dateAvailability, '%Y-%m-%d') <= '$now'))";
                        }
                    }
                    elseif ($availability == 2){
                        $nowD = new \DateTime('+1 month');
                        $now = $nowD->format('Y-m-d');
                        if($key>0){
                            $str .= " OR (pd.availability = true OR (pd.availability = false AND pd.availabilityPeriod = 4 AND DATE_FORMAT(pd.dateAvailability, '%Y-%m-%d') <= '$now') OR pd.availabilityPeriod = 1)";
                        }
                        else{
                            $str .= "(pd.availability = true OR (pd.availability = false AND pd.availabilityPeriod = 4 AND DATE_FORMAT(pd.dateAvailability, '%Y-%m-%d') <= '$now') OR pd.availabilityPeriod = 1)";
                        }
                    }
                    elseif ($availability == 3){
                        $nowD = new \DateTime('+3month');
                        $now = $nowD->format('Y-m-d');
                        if($key>0){
                            $str .= " OR ((pd.availability = true) OR (pd.availability = false AND pd.availabilityPeriod = 4 AND DATE_FORMAT(pd.dateAvailability, '%Y-%m-%d') <= '$now') OR (pd.availabilityPeriod = 1 OR pd.availabilityPeriod = 2 OR pd.availabilityPeriod = 3))";
                        }
                        else{
                            $str .= "((pd.availability = true) OR (pd.availability = false AND pd.availabilityPeriod = 4 AND DATE_FORMAT(pd.dateAvailability, '%Y-%m-%d') <= '$now') OR (pd.availabilityPeriod = 1 OR pd.availabilityPeriod = 2 OR pd.availabilityPeriod = 3))";
                        }
                    }
                }
                $str .= ')';
                $query->andWhere($str);
            }

        }

		if(isset($params['video']) && $params['video'] === 'Yes'){
			$query->andWhere('pd.video IS NOT NULL')
				->andWhere('pd.video NOT LIKE :empty_array')
				->setParameter('empty_array', "%".serialize(array())."%")
				->andWhere('pd.video NOT LIKE :empty_null')
				->setParameter('empty_null', "%".serialize(NULL)."%");
		}
		else if(isset($params['video']) && $params['video'] === 'No'){
			$query->andWhere('pd.video LIKE :empty_null');
			$query->setParameter('empty_null', "%".serialize(NULL)."%");
		}

		if(isset($params['field']) && $params['field'] != 'null' && $params['field'] != NULL){
			if(is_array($params['field'])){
				$fields = $params['field'];
			}
			else{
				$fields = explode(',',$params['field']);
			}
			if(!empty($fields) && !in_array('All', $fields) ){
				$str = '(';
				foreach ($fields as $key=>$field){
					if($key>0){
						$str .= " OR (
									cq.specialization LIKE '{$field}' OR 
									cq.specializationCustom LIKE '{$field}' OR
									pd.specialization LIKE '%{$field}%' OR
									cr.specialization LIKE '%{$field}%'
									)";
					}
					else{
						$str .= " (
									cq.specialization LIKE '{$field}' OR 
									cq.specializationCustom LIKE '{$field}' OR
									pd.specialization LIKE '%{$field}%' OR
									cr.specialization LIKE '%{$field}%'
									)";
					}
				}
				$str .= ')';
				if(!in_array('AppBundle:CandidateQualifications',$additional_from)){
					$query->from("AppBundle:CandidateQualifications", "cq")->andWhere("u.id = cq.user");
					$additional_from[] = 'AppBundle:CandidateQualifications';
				}
				if(!in_array('AppBundle:CandidateReferences',$additional_from)){
					$query->from("AppBundle:CandidateReferences", "cr")->andWhere("u.id = cr.user");
					$additional_from[] = 'AppBundle:CandidateReferences';
				}
				$query->andWhere($str);
			}
		}

		if(isset($params['monthSalaryFrom']) && !is_null($params['monthSalaryFrom']) && $params['monthSalaryFrom'] != 'null'){
			$additional_where = '';
			if(!isset($params['monthSalaryFrom']) || empty($params['monthSalaryFrom']) || $params['monthSalaryFrom'] != 'null'){
				$additional_where = 'OR pd.firstJob = 1';
			}

			$month_salary = $params['monthSalaryFrom'];
			$annual_salary = (int)$params['monthSalaryFrom'] * 12;
			$query->andWhere("(
					(pd.mostSalary >= :month_salary_from AND pd.salaryPeriod = 'monthly') OR
					(pd.mostSalary >= :annual_salary_from AND pd.salaryPeriod = 'annual') ".$additional_where."
				)");
			$query->setParameter('month_salary_from', $month_salary);
			$query->setParameter('annual_salary_from', $annual_salary);
		}
		if(isset($params['monthSalaryTo']) && !is_null($params['monthSalaryTo']) && $params['monthSalaryTo'] != 'null'){
			//2000
			if($params['monthSalaryTo'] != 35000){
				$additional_where = '';
				if(!isset($params['monthSalaryFrom']) || empty($params['monthSalaryFrom']) || $params['monthSalaryFrom'] != 'null'){
					$additional_where = 'OR pd.firstJob = 1';
				}

				$month_salary = $params['monthSalaryTo'];
				$annual_salary = (int)$params['monthSalaryTo'] * 12;
				$query->andWhere("(
					(pd.mostSalary <= :month_salary_to AND pd.salaryPeriod = 'monthly') OR
					(pd.mostSalary <= :annual_salary_to AND pd.salaryPeriod = 'annual') ".$additional_where."
				)");
				$query->setParameter('month_salary_to', $month_salary);
				$query->setParameter('annual_salary_to', $annual_salary);
			}
		}

		if(isset($params['eligibility']) && $params['eligibility'] === 'applicable'){
			$query->andWhere("(pd.ethnicity != 'White' AND pd.ethnicity != 'Foreign National')");
		}

        if(isset($params['enabled']) && $params['enabled'] != 'null' && $params['enabled'] != NULL){
            if(is_array($params['enabled'])){
                $enableds = $params['enabled'];
            }
            else{
                $enableds = explode(',',$params['enabled']);
            }
            if(!empty($enableds) && !in_array('All', $enableds) ){
                $str = '(';
                foreach ($enableds as $key=>$enabled){
                    if($enabled == 'false' || $enabled === false){
                        if($key>0){
                            $str .= " OR u.enabled = 0";
                        }
                        else{
                            $str .= "u.enabled = 0";
                        }
                    }
                    elseif($enabled == 'true' || $enabled === true){
                        if($key>0){
                            $str .= " OR u.enabled = 1";
                        }
                        else{
                            $str .= "u.enabled = 1";
                        }
                    }
                }
                $str .= ')';
                $query->andWhere($str);
            }
        }
        else{
            $query->andWhere('u.enabled = :enabled')
                ->setParameter('enabled', 1);
        }

        if(isset($params['profileComplete']) && $params['profileComplete'] != 'null' && $params['profileComplete'] != NULL){
            if(is_array($params['profileComplete'])){
                $profileCompletes = $params['profileComplete'];
            }
            else{
                $profileCompletes = explode(',',$params['profileComplete']);
            }
            if(!empty($profileCompletes) && !in_array('All', $profileCompletes) ){
                $str = '(';
                $empty_array = "%".serialize(array())."%";
                $empty_null = "%".serialize(NULL)."%";
                foreach ($profileCompletes as $key=>$profileComplete){
                    if($profileComplete == 'false' || $profileComplete === false){
                        if($key>0){
                            $str .= " OR (pd.percentage < 50 OR pd.video IS NULL OR pd.video LIKE '$empty_array' OR pd.video LIKE '$empty_null' OR pd.copyOfID IS NULL OR pd.copyOfID LIKE '$empty_array' OR pd.copyOfID LIKE '$empty_null')";
                        }
                        else{
                            $str .= "(pd.percentage < 50 OR pd.video IS NULL OR pd.video LIKE '$empty_array' OR pd.video LIKE '$empty_null' OR pd.copyOfID IS NULL OR pd.copyOfID LIKE '$empty_array' OR pd.copyOfID LIKE '$empty_null')";
                        }
                    }
                    elseif($profileComplete == 'true' || $profileComplete === true){
                        if($key>0){
                            $str .= " OR (pd.percentage > 50 AND pd.video IS NOT NULL AND pd.copyOfID IS NOT NULL AND pd.video NOT LIKE '$empty_array' AND pd.copyOfID NOT LIKE '$empty_array' AND pd.video NOT LIKE '$empty_null' AND pd.copyOfID NOT LIKE '$empty_null')";
                        }
                        else{
                            $str .= "(pd.percentage > 50 AND pd.video IS NOT NULL AND pd.copyOfID IS NOT NULL AND pd.video NOT LIKE '$empty_array' AND pd.copyOfID NOT LIKE '$empty_array' AND pd.video NOT LIKE '$empty_null' AND pd.copyOfID NOT LIKE '$empty_null')";
                        }
                    }
                }
                $str .= ')';
                $query->andWhere($str);
            }
        }

        return $query->getQuery()->getResult();
    }

    /**
     * @param array $params
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getAllCandidateCount($params = array()){
        $query = $this->createQueryBuilder('u')
            ->from("AppBundle:ProfileDetails", 'pd')
            ->select('count(u.id) as countCandidate')
            ->where('u.id = pd.user')
            ->andWhere("u.roles = :role")
            ->setParameter('role',serialize(['ROLE_CANDIDATE']));

        if(isset($params['search']) && !empty($params['search'])){
            $query->andWhere("(u.firstName LIKE :search OR u.lastName LIKE :search OR u.email LIKE :search)")
                ->setParameter('search', "%".$params['search']."%");
        }

        if(isset($params['gender']) && $params['gender'] != 'All' && ($params['gender'] == 'Male' || $params['gender'] == 'Female')){
            $query->andWhere('pd.gender = :gender')
                ->setParameter('gender',$params['gender']);
        }

        if(isset($params['ethnicity']) && $params['ethnicity'] != 'null' && $params['ethnicity'] != NULL && !empty($params['ethnicity']) && $params['ethnicity']!="All"){
            $query->andWhere('pd.ethnicity = :ethnicity')
                ->setParameter('ethnicity', $params['ethnicity']);
        }

        if(isset($params['nationality']) && $params['nationality'] != 'All' && $params['nationality'] != NULL && $params['nationality'] >0){
            $query->andWhere('pd.nationality = :nationality')
                ->setParameter('nationality', $params['nationality']);
        }

        if(isset($params['location']) && $params['location'] != 'null' && $params['location'] != NULL && !empty($params['location']) && $params['location'] != 'All'){
            $query->andWhere('pd.citiesWorking LIKE :location')
                ->setParameter('location', '%'.$params['location'].'%');
        }

        if(isset($params['qualification']) && $params['qualification'] != 'All' && $params['qualification'] != NULL && $params['qualification']>0){
            if($params['qualification'] == 1 || $params['qualification'] == '1'){
                $query->andWhere('pd.boards IN (1,2)');
            }
            elseif($params['qualification'] == 2 || $params['qualification'] == '2'){
                $query->andWhere('pd.boards IN (3,4)');
            }
        }

        if(isset($params['video']) && $params['video'] != 'All' && $params['video'] != NULL && $params['video'] == 1){
            $query->andWhere('pd.video != :video')
                ->setParameter('video', serialize(NULL));
        }

        if(isset($params['criminal']) && $params['criminal'] != 0 && ($params['criminal'] == 'false' || $params['criminal'] == false || $params['criminal'] == 'true' || $params['criminal'] == true)){
            $query->andWhere('pd.criminal = :criminal');
            if($params['criminal'] == 'false' || $params['criminal'] == false)
                $query->setParameter('criminal', 0);
            else
                $query->setParameter('criminal', 1);
        }
        if(isset($params['credit']) && $params['credit'] != 0 && ($params['credit'] == 'false' || $params['credit'] == false || $params['credit'] == 'true' || $params['credit'] == true)){
            $query->andWhere('pd.credit = :credit');
            if($params['credit'] == 'false' || $params['credit'] == false)
                $query->setParameter('credit', 0);
            else
                $query->setParameter('credit', 1);
        }

        if(isset($params['availability']) && $params['availability'] != 'All' && $params['availability'] != NULL && $params['availability']>0 && $params['availability']<4){
            if($params['availability'] == 1){
                $now = new \DateTime();
                $query->andWhere("(pd.availability = true OR (pd.availability = false AND pd.availabilityPeriod = 4 AND DATE_FORMAT(pd.dateAvailability, '%Y-%m-%d') <= :now))")
                    ->setParameter('now', $now->format('Y-m-d'));
            }
            elseif ($params['availability'] == 2){
                $now = new \DateTime('+1 month');
                $query->andWhere("(pd.availability = true OR (pd.availability = false AND pd.availabilityPeriod = 4 AND DATE_FORMAT(pd.dateAvailability, '%Y-%m-%d') <= :now) OR pd.availabilityPeriod = 1)")
                    ->setParameter('now', $now->format('Y-m-d'));
            }
            else{
                $now = new \DateTime('+3month');
                $query->andWhere("((pd.availability = true) OR (pd.availability = false AND pd.availabilityPeriod = 4 AND DATE_FORMAT(pd.dateAvailability, '%Y-%m-%d') <= :now) OR (pd.availabilityPeriod = 1 OR pd.availabilityPeriod = 2 OR pd.availabilityPeriod = 3))")
                    ->setParameter('now', $now->format('Y-m-d'));
            }
        }

        if(isset($params['enabled']) && $params['enabled'] == 'false'){
            $query->andWhere('u.enabled = :enabled')
                ->setParameter('enabled', 0);
        }
        else{
            $query->andWhere('u.enabled = :enabled')
                ->setParameter('enabled', 1);
        }

        if(isset($params['articlesCompletedStart']) && $params['articlesCompletedStart'] != 'null' && $params['articlesCompletedStart'] != NULL){
            $articlesCompletedStart = ($params['articlesCompletedStart'] instanceof \DateTime) ? $params['articlesCompletedStart'] : new \DateTime($params['articlesCompletedStart']);
            $date_arr = explode(', ',$params['articlesCompletedStart']);
            if(isset($date_arr[1])){
                $articlesCompletedStart->setDate($date_arr[1],$articlesCompletedStart->format('m'),$articlesCompletedStart->format('d'));
            }
            $query->andWhere("DATE_FORMAT(pd.dateArticlesCompleted, '%Y-%m') >= :articlesCompletedStart")
                ->setParameter('articlesCompletedStart', $articlesCompletedStart->format('Y-m'));
        }

        if(isset($params['articlesCompletedEnd']) && $params['articlesCompletedEnd'] != 'null' && $params['articlesCompletedEnd'] != NULL){
            $articlesCompletedEnd = ($params['articlesCompletedEnd'] instanceof \DateTime) ? $params['articlesCompletedEnd'] : new \DateTime($params['articlesCompletedEnd']);
            $date_arr = explode(', ',$params['articlesCompletedEnd']);
            if(isset($date_arr[1])){
                $articlesCompletedEnd->setDate($date_arr[1],$articlesCompletedEnd->format('m'),$articlesCompletedEnd->format('d'));
            }
            $query->andWhere("DATE_FORMAT(pd.dateArticlesCompleted, '%Y-%m') <= :articlesCompletedEnd")
                ->setParameter('articlesCompletedEnd', $articlesCompletedEnd->format('Y-m'));
        }
        if(isset($params['profileComplete']) && ($params['profileComplete'] == 'false' || $params['profileComplete'] == 'true')){
            if($params['profileComplete'] == 'false' || $params['profileComplete'] == false){
                $query->andWhere('pd.percentage < 50 OR pd.video IS NULL OR pd.video LIKE :empty_array OR pd.video LIKE :empty_null OR pd.copyOfID IS NULL OR pd.copyOfID LIKE :empty_array OR pd.copyOfID LIKE :empty_null')
                    ->setParameter('empty_array', "%".serialize(array())."%")
                    ->setParameter('empty_null', "%".serialize(NULL)."%");
            }
            else{
                $query->andWhere('pd.percentage > 50')
                    ->andWhere('pd.video IS NOT NULL')
                    ->andWhere('pd.copyOfID IS NOT NULL')
                    ->andWhere('pd.video NOT LIKE :empty_array')
                    ->andWhere('pd.copyOfID NOT LIKE :empty_array')
                    ->setParameter('empty_array', "%".serialize(array())."%")
                    ->andWhere('pd.video NOT LIKE :empty_null')
                    ->andWhere('pd.copyOfID NOT LIKE :empty_null')
                    ->setParameter('empty_null', "%".serialize(NULL)."%");
            }
        }

        return $query->getQuery()->getOneOrNullResult();
    }

    /**
     * @param array $params
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getAllCandidateCountNew($params = array()){
        $query = $this->createQueryBuilder('u')
            ->from("AppBundle:ProfileDetails", 'pd')
            ->select('count(u.id) as countCandidate')
            ->where('u.id = pd.user')
            ->andWhere("u.roles = :role")
            ->setParameter('role',serialize(['ROLE_CANDIDATE']));

        if(isset($params['search']) && !empty($params['search'])){
            $query->andWhere("(u.firstName LIKE :search OR u.lastName LIKE :search OR u.email LIKE :search)")
                ->setParameter('search', "%".$params['search']."%");
        }

        if(isset($params['gender']) && $params['gender'] != 'null' && $params['gender'] != NULL){
            if(is_array($params['gender'])){
                $genders = $params['gender'];
            }
            else{
                $genders = explode(',',$params['gender']);
            }
            if(!empty($genders) && !in_array('All', $genders) ){
                $str = '(';
                foreach ($genders as $key=>$gender){
                    if($key>0){
                        $str .= " OR pd.gender='$gender'";
                    }
                    else{
                        $str .= "pd.gender='$gender'";
                    }
                }
                $str .= ')';
                $query->andWhere($str);
            }
        }

        if(isset($params['ethnicity']) && $params['ethnicity'] != 'null' && $params['ethnicity'] != NULL){
            if(is_array($params['ethnicity'])){
                $ethnicitys = $params['ethnicity'];
            }
            else{
                $ethnicitys = explode(',',$params['ethnicity']);
            }
            if(!empty($ethnicitys) && !in_array('All', $ethnicitys) ){
                $str = '(';
                foreach ($ethnicitys as $key=>$ethnicity){
                    if($key>0){
                        $str .= " OR pd.ethnicity='$ethnicity'";
                    }
                    else{
                        $str .= "pd.ethnicity='$ethnicity'";
                    }
                }
                $str .= ')';
                $query->andWhere($str);
            }
        }

        if(isset($params['nationality']) && $params['nationality'] != 'null' && $params['nationality'] != NULL){
            if(is_array($params['nationality'])){
                $nationalitys = $params['nationality'];
            }
            else{
                $nationalitys = explode(',',$params['nationality']);
            }
            if(!empty($nationalitys) && !in_array('All', $nationalitys) ){
                $str = '(';
                foreach ($nationalitys as $key=>$nationality){
                    if($key>0){
                        $str .= " OR pd.nationality='$nationality'";
                    }
                    else{
                        $str .= "pd.nationality='$nationality'";
                    }
                }
                $str .= ')';
                $query->andWhere($str);
            }
        }

        if(isset($params['location']) && $params['location'] != 'null' && $params['location'] != NULL){
            if(is_array($params['location'])){
                $locations = $params['location'];
            }
            else{
                $locations = explode(',',$params['location']);
            }
            if(!empty($locations) && !in_array('All', $locations) ){
                $str = '(';
                foreach ($locations as $key=>$location){
                    $location = "%".$location."%";
                    if($key>0){
                        $str .= " OR pd.citiesWorking LIKE '$location'";
                    }
                    else{
                        $str .= "pd.citiesWorking LIKE '$location'";
                    }
                }
                $str .= ')';
                $query->andWhere($str);
            }
        }
        

        if(isset($params['criminal']) && $params['criminal'] != 'null' && $params['criminal'] != NULL){
            if(is_array($params['criminal'])){
                $criminals = $params['criminal'];
            }
            else{
                $criminals = explode(',',$params['criminal']);
            }
            if(!empty($criminals) && !in_array('All', $criminals) ){
                $str = '(';
                foreach ($criminals as $key=>$criminal){
                    if($criminal == 'false' || $criminal === false){
                        if($key>0){
                            $str .= " OR pd.criminal = 0";
                        }
                        else{
                            $str .= "pd.criminal = 0";
                        }
                    }
                    elseif($criminal == 'true' || $criminal === true){
                        if($key>0){
                            $str .= " OR pd.criminal = 1";
                        }
                        else{
                            $str .= "pd.criminal = 1";
                        }
                    }
                }
                $str .= ')';
                $query->andWhere($str);
            }
        }
        if(isset($params['credit']) && $params['credit'] != 'null' && $params['credit'] != NULL){
            if(is_array($params['credit'])){
                $credits = $params['credit'];
            }
            else{
                $credits = explode(',',$params['credit']);
            }
            if(!empty($credits) && !in_array('All', $credits) ){
                $str = '(';
                foreach ($credits as $key=>$credit){
                    if($credit == 'false' || $credit === false){
                        if($key>0){
                            $str .= " OR pd.credit = 0";
                        }
                        else{
                            $str .= "pd.credit = 0";
                        }
                    }
                    elseif($credit == 'true' || $credit === true){
                        if($key>0){
                            $str .= " OR pd.credit = 1";
                        }
                        else{
                            $str .= "pd.credit = 1";
                        }
                    }
                }
                $str .= ')';
                $query->andWhere($str);
            }
        }

        if(isset($params['availability']) && $params['availability'] != 'null' && $params['availability'] != NULL){
            if(is_array($params['availability'])){
                $availabilitys = $params['availability'];
            }
            else{
                $availabilitys = explode(',',$params['availability']);
            }
            if(!empty($availabilitys) && !in_array('All', $availabilitys) ){
                $str = '(';
                foreach ($availabilitys as $key=>$availability){
                    if($availability == 1){
                        $nowD = new \DateTime();
                        $now = $nowD->format('Y-m-d');
                        if($key>0){
                            $str .= " OR (pd.availability = true OR (pd.availability = false AND pd.availabilityPeriod = 4 AND DATE_FORMAT(pd.dateAvailability, '%Y-%m-%d') <= '$now'))";
                        }
                        else{
                            $str .= "(pd.availability = true OR (pd.availability = false AND pd.availabilityPeriod = 4 AND DATE_FORMAT(pd.dateAvailability, '%Y-%m-%d') <= '$now'))";
                        }
                    }
                    elseif ($availability == 2){
                        $nowD = new \DateTime('+1 month');
                        $now = $nowD->format('Y-m-d');
                        if($key>0){
                            $str .= " OR (pd.availability = true OR (pd.availability = false AND pd.availabilityPeriod = 4 AND DATE_FORMAT(pd.dateAvailability, '%Y-%m-%d') <= '$now') OR pd.availabilityPeriod = 1)";
                        }
                        else{
                            $str .= "(pd.availability = true OR (pd.availability = false AND pd.availabilityPeriod = 4 AND DATE_FORMAT(pd.dateAvailability, '%Y-%m-%d') <= '$now') OR pd.availabilityPeriod = 1)";
                        }
                    }
                    elseif ($availability == 3){
                        $nowD = new \DateTime('+3month');
                        $now = $nowD->format('Y-m-d');
                        if($key>0){
                            $str .= " OR ((pd.availability = true) OR (pd.availability = false AND pd.availabilityPeriod = 4 AND DATE_FORMAT(pd.dateAvailability, '%Y-%m-%d') <= '$now') OR (pd.availabilityPeriod = 1 OR pd.availabilityPeriod = 2 OR pd.availabilityPeriod = 3))";
                        }
                        else{
                            $str .= "((pd.availability = true) OR (pd.availability = false AND pd.availabilityPeriod = 4 AND DATE_FORMAT(pd.dateAvailability, '%Y-%m-%d') <= '$now') OR (pd.availabilityPeriod = 1 OR pd.availabilityPeriod = 2 OR pd.availabilityPeriod = 3))";
                        }
                    }
                }
                $str .= ')';
                $query->andWhere($str);
            }

        }

        if(isset($params['enabled']) && $params['enabled'] != 'null' && $params['enabled'] != NULL){
            if(is_array($params['enabled'])){
                $enableds = $params['enabled'];
            }
            else{
                $enableds = explode(',',$params['enabled']);
            }
            if(!empty($enableds) && !in_array('All', $enableds) ){
                $str = '(';
                foreach ($enableds as $key=>$enabled){
                    if($enabled == 'false' || $enabled === false){
                        if($key>0){
                            $str .= " OR u.enabled = 0";
                        }
                        else{
                            $str .= "u.enabled = 0";
                        }
                    }
                    elseif($enabled == 'true' || $enabled === true){
                        if($key>0){
                            $str .= " OR u.enabled = 1";
                        }
                        else{
                            $str .= "u.enabled = 1";
                        }
                    }
                }
                $str .= ')';
                $query->andWhere($str);
            }
        }
        else{
            $query->andWhere('u.enabled = :enabled')
                ->setParameter('enabled', 1);
        }

        if(isset($params['profileComplete']) && $params['profileComplete'] != 'null' && $params['profileComplete'] != NULL){
            if(is_array($params['profileComplete'])){
                $profileCompletes = $params['profileComplete'];
            }
            else{
                $profileCompletes = explode(',',$params['profileComplete']);
            }
            if(!empty($profileCompletes) && !in_array('All', $profileCompletes) ){
                $str = '(';
                $empty_array = "%".serialize(array())."%";
                $empty_null = "%".serialize(NULL)."%";
                foreach ($profileCompletes as $key=>$profileComplete){
                    if($profileComplete == 'false' || $profileComplete === false){
                        if($key>0){
                            $str .= " OR (pd.percentage < 50 OR pd.video IS NULL OR pd.video LIKE '$empty_array' OR pd.video LIKE '$empty_null' OR pd.copyOfID IS NULL OR pd.copyOfID LIKE '$empty_array' OR pd.copyOfID LIKE '$empty_null')";
                        }
                        else{
                            $str .= "(pd.percentage < 50 OR pd.video IS NULL OR pd.video LIKE '$empty_array' OR pd.video LIKE '$empty_null' OR pd.copyOfID IS NULL OR pd.copyOfID LIKE '$empty_array' OR pd.copyOfID LIKE '$empty_null')";
                        }
                    }
                    elseif($profileComplete == 'true' || $profileComplete === true){
                        if($key>0){
                            $str .= " OR (pd.percentage > 50 AND pd.video IS NOT NULL AND pd.copyOfID IS NOT NULL AND pd.video NOT LIKE '$empty_array' AND pd.copyOfID NOT LIKE '$empty_array' AND pd.video NOT LIKE '$empty_null' AND pd.copyOfID NOT LIKE '$empty_null')";
                        }
                        else{
                            $str .= "(pd.percentage > 50 AND pd.video IS NOT NULL AND pd.copyOfID IS NOT NULL AND pd.video NOT LIKE '$empty_array' AND pd.copyOfID NOT LIKE '$empty_array' AND pd.video NOT LIKE '$empty_null' AND pd.copyOfID NOT LIKE '$empty_null')";
                        }
                    }
                }
                $str .= ')';
                $query->andWhere($str);
            }
        }

        return $query->getQuery()->getOneOrNullResult();
    }

    /**
     * @param $id
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getCandidateProfile($id){
        return $this->createQueryBuilder('u')
            ->select('u.id, u.firstName,u.lastName,u.phone,u.email,u.agentName')
            ->where("u.id = :id")
            ->setParameter('id',$id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param array $params
     * @return mixed
     */
    public function getCandidateApprove($params = array()){
        $result = $this->createQueryBuilder('u')
            ->from("AppBundle:ProfileDetails", "pd")
            ->select('u.id,u.firstName,u.lastName,u.phone,u.email')
            ->where('u.id = pd.user')
            ->andWhere("u.approved IS NULL")
            ->andWhere("u.roles = :role")
            ->setParameter('role',serialize(['ROLE_CANDIDATE']))
            ->orderBy('u.created','DESC');

        if(isset($params['lm']) && $params['lm'] > 0){
            $result->setMaxResults($params['lm'])
                ->setFirstResult(0);
        }

        return $result->getQuery()->getResult();
    }

    /**
     * @param array $params
     * @return array
     */
    public function getCandidateFilesApprove($params=array()){
        $result = $this->createQueryBuilder('u')
            ->from("AppBundle:ProfileDetails", "pd")
            ->select('u.id,u.firstName,u.lastName, pd.copyOfID, pd.cv, pd.matricCertificate, pd.matricTranscript, pd.certificateOfQualification, pd.academicTranscript, pd.creditCheck, pd.payslip')
            ->where("u.id = pd.user")
            ->andWhere("(pd.copyOfID IS NOT NULL OR pd.cv IS NOT NULL OR pd.matricCertificate IS NOT NULL OR pd.matricTranscript IS NOT NULL OR pd.certificateOfQualification IS NOT NULL OR pd.academicTranscript IS NOT NULL OR pd.creditCheck IS NOT NULL OR pd.payslip IS NOT NULL)")
            ->getQuery()
            ->getResult();
        $files = [];
        if(!empty($result)){
            foreach ($result as $item) {
                if(!empty($item)){
                    if(!empty($item['copyOfID']) && is_array($item['copyOfID'])){
                        foreach ($item['copyOfID'] as $file){
                            if(isset($file['approved']) && $file['approved'] == false){
                                $files[] = [
                                    'userId' => $item['id'],
                                    'firstName' => $item['firstName'],
                                    'lastName' => $item['lastName'],
                                    'url' => $file['url'],
                                    'adminUrl' => (isset($file['adminUrl'])) ? $file['adminUrl'] : null,
                                    'fileName' => $file['name'],
                                    'fieldName' => 'copyOfID',
                                    'type' => 'Copy of ID',
                                    'time'=>(isset($file['time'])) ? $file['time'] : null
                                ];
                            }
                        }
                    }
                    if(!empty($item['cv']) && is_array($item['cv'])){
                        foreach ($item['cv'] as $file){
                            if(isset($file['approved']) && $file['approved'] == false){
                                $files[] = [
                                    'userId' => $item['id'],
                                    'firstName' => $item['firstName'],
                                    'lastName' => $item['lastName'],
                                    'url' => $file['url'],
                                    'adminUrl' => (isset($file['adminUrl'])) ? $file['adminUrl'] : null,
                                    'fileName' => $file['name'],
                                    'fieldName' => 'cv',
                                    'type' => 'CV',
                                    'time'=>(isset($file['time'])) ? $file['time'] : null
                                ];
                            }
                        }
                    }
                    if(!empty($item['matricCertificate']) && is_array($item['matricCertificate'])){
                        foreach ($item['matricCertificate'] as $file){
                            if(isset($file['approved']) && $file['approved'] == false){
                                $files[] = [
                                    'userId' => $item['id'],
                                    'firstName' => $item['firstName'],
                                    'lastName' => $item['lastName'],
                                    'url' => $file['url'],
                                    'adminUrl' => (isset($file['adminUrl'])) ? $file['adminUrl'] : null,
                                    'fileName' => $file['name'],
                                    'fieldName' => 'matricCertificate',
                                    'type' => 'Matric Certificate',
                                    'time'=>(isset($file['time'])) ? $file['time'] : null
                                ];
                            }
                        }
                    }
                    if(!empty($item['matricTranscript']) && is_array($item['matricTranscript'])){
                        foreach ($item['matricTranscript'] as $file){
                            if(isset($file['approved']) && $file['approved'] == false){
                                $files[] = [
                                    'userId' => $item['id'],
                                    'firstName' => $item['firstName'],
                                    'lastName' => $item['lastName'],
                                    'url' => $file['url'],
                                    'adminUrl' => (isset($file['adminUrl'])) ? $file['adminUrl'] : null,
                                    'fileName' => $file['name'],
                                    'fieldName' => 'matricTranscript',
                                    'type' => 'Matric Transcript',
                                    'time'=>(isset($file['time'])) ? $file['time'] : null
                                ];
                            }
                        }
                    }
                    if(!empty($item['certificateOfQualification']) && is_array($item['certificateOfQualification'])){
                        foreach ($item['certificateOfQualification'] as $file){
                            if(isset($file['approved']) && $file['approved'] == false){
                                $files[] = [
                                    'userId' => $item['id'],
                                    'firstName' => $item['firstName'],
                                    'lastName' => $item['lastName'],
                                    'url' => $file['url'],
                                    'adminUrl' => (isset($file['adminUrl'])) ? $file['adminUrl'] : null,
                                    'fileName' => $file['name'],
                                    'fieldName' => 'certificateOfQualification',
                                    'type' => 'Certificate of Qualification',
                                    'time'=>(isset($file['time'])) ? $file['time'] : null
                                ];
                            }
                        }
                    }
                    if(!empty($item['academicTranscript']) && is_array($item['academicTranscript'])){
                        foreach ($item['academicTranscript'] as $file){
                            if(isset($file['approved']) && $file['approved'] == false){
                                $files[] = [
                                    'userId' => $item['id'],
                                    'firstName' => $item['firstName'],
                                    'lastName' => $item['lastName'],
                                    'url' => $file['url'],
                                    'adminUrl' => (isset($file['adminUrl'])) ? $file['adminUrl'] : null,
                                    'fileName' => $file['name'],
                                    'fieldName' => 'academicTranscript',
                                    'type' => 'Academic Transcript',
                                    'time'=>(isset($file['time'])) ? $file['time'] : null
                                ];
                            }
                        }
                    }
                    if(!empty($item['creditCheck']) && is_array($item['creditCheck'])){
                        foreach ($item['creditCheck'] as $file){
                            if(isset($file['approved']) && $file['approved'] == false){
                                $files[] = [
                                    'userId' => $item['id'],
                                    'firstName' => $item['firstName'],
                                    'lastName' => $item['lastName'],
                                    'url' => $file['url'],
                                    'adminUrl' => (isset($file['adminUrl'])) ? $file['adminUrl'] : null,
                                    'fileName' => $file['name'],
                                    'fieldName' => 'creditCheck',
                                    'type' => 'Credit Check',
                                    'time'=>(isset($file['time'])) ? $file['time'] : null
                                ];
                            }
                        }
                    }
                    if(!empty($item['payslip']) && is_array($item['payslip'])){
                        foreach ($item['payslip'] as $file){
                            if(isset($file['approved']) && $file['approved'] == false){
                                $files[] = [
                                    'userId' => $item['id'],
                                    'firstName' => $item['firstName'],
                                    'lastName' => $item['lastName'],
                                    'url' => $file['url'],
                                    'adminUrl' => (isset($file['adminUrl'])) ? $file['adminUrl'] : null,
                                    'fileName' => $file['name'],
                                    'fieldName' => 'payslip',
                                    'type' => 'Latest Payslip',
                                    'time'=>(isset($file['time'])) ? $file['time'] : null
                                ];
                            }
                        }
                    }
                }
            }
        }
        $newFiles = [];
        if(isset($params['search']) && !empty($params['search'])){
            foreach ($files as $file){
                if(strpos($file['firstName'], $params['search']) !== false
                    || strpos($file['lastName'], $params['search']) !== false
                    || strpos($file['fileName'], $params['search']) !== false
                ){
                    $newFiles[] = $file;
                }
            }
        }
        else{
            $newFiles = $files;
        }

        if(isset($params['orderBy']) && !empty($params['orderBy']) && in_array($params['orderBy'], ['Name', 'Document'])){
            if(isset($params['orderSort']) && !empty($params['orderSort']) && in_array($params['orderSort'], ['asc', 'desc'])){
                if($params['orderBy'] == 'Name'){
                    if($params['orderSort'] == 'asc'){
                        usort($newFiles, function($a, $b) {
                            return $a['firstName'] > $b['firstName'];
                        });
                    }
                    else{
                        usort($newFiles, function($a, $b) {
                            return $a['firstName'] < $b['firstName'];
                        });
                    }
                }
                elseif ($params['orderBy'] == 'Document'){
                    if($params['orderSort'] == 'asc'){
                        usort($newFiles, function($a, $b) {
                            return $a['fileName'] > $b['fileName'];
                        });
                    }
                    else{
                        usort($newFiles, function($a, $b) {
                            return $a['fileName'] < $b['fileName'];
                        });
                    }
                }
            }
            else{
                usort($newFiles, function($a, $b) {
                    return $a['time'] < $b['time'];
                });
            }
        }
        else{
            usort($newFiles, function($a, $b) {
                return $a['time'] < $b['time'];
            });
        }



        if(isset($params['lm']) && $params['lm'] > 0){
            $chunkFiles = array_chunk($newFiles, $params['lm']);
            $newFiles = (isset($chunkFiles[0])) ? $chunkFiles[0] : [];
        }

        return $newFiles;
    }

    /**
     * @param array $params
     * @return array
     */
    public function getCandidateVideosApprove($params=array()){
        $result = $this->createQueryBuilder('u')
            ->from("AppBundle:ProfileDetails", "pd")
            ->select('u.id,u.firstName,u.lastName, pd.video')
            ->where("u.id = pd.user")
            ->andWhere("pd.video IS NOT NULL")
            ->getQuery()
            ->getResult();
        $videos = [];
        if(!empty($result)){
            foreach ($result as $item) {
                if(!empty($item)){
                    if(!empty($item['video']) && is_array($item['video']) && isset($item['video']['url']) && $item['video']['name']){
                        if(!isset($item['video']['approved']) || $item['video']['approved'] == false){
                            $videos[] = [
                                'userId' => $item['id'],
                                'firstName' => $item['firstName'],
                                'lastName' => $item['lastName'],
                                'url' => $item['video']['url'],
                                'adminUrl' => (isset($item['video']['adminUrl'])) ? $item['video']['adminUrl'] : null,
                                'fileName' => $item['video']['name'],
                                'time'=>(isset($item['video']['time'])) ? $item['video']['time'] : null
                            ];
                        }
                    }
                }
            }
        }

        usort($videos, function($a, $b) {
            return $a['time'] < $b['time'];
        });

        if(isset($params['lm']) && $params['lm'] > 0){
            $chunkFiles = array_chunk($videos, $params['lm']);
            $videos = (isset($chunkFiles[0])) ? $chunkFiles[0] : [];
        }

        return $videos;
    }

	/**
	 * @param array $params
	 * @return mixed
	 */
	public function getAllCandidatesAndClients($params = array()){
		$query = $this->createQueryBuilder('u')
			->select('u.id,u.firstName,u.lastName, u.phone,u.email')
			->where("(u.roles = :role1 OR u.roles = :role2)")
			->andWhere("u.enabled = 1")
			->andWhere("u.approved = 1")
			->setParameter('role1',serialize(['ROLE_CLIENT']))
			->setParameter('role2',serialize(['ROLE_CANDIDATE']));

		if(isset($params['search']) && !empty($params['search'])){
			$query->andWhere("(u.firstName LIKE :search OR 
								u.lastName LIKE :search OR 
								u.phone LIKE :search OR 
								u.email LIKE :search)")
				->setParameter('search', "%".$params['search']."%");
		}

		$orderSort = 'ASC';
		$orderBy = 'u.created';
		$orderByKeys = [
			'id'=>'u.id',
			'firstName'=>'u.firstName',
			'lastName'=>'u.lastName',
			'phone'=>'u.phone',
			'email'=>'u.email'
		];
		if(isset($params['orderBy']) && $params['orderBy'] != 'null' && $params['orderBy'] != NULL && array_key_exists($params['orderBy'],$orderByKeys)){
			$orderBy = $orderByKeys[$params['orderBy']];

		}
		if(isset($params['orderSort']) && $params['orderSort'] != 'null' && $params['orderSort'] != NULL && array_key_exists($params['orderBy'],$orderByKeys)){
			$orderSort = $params['orderSort'];
		}

		$query->orderBy($orderBy, $orderSort);


		return $query->getQuery()->getResult();
	}


}
