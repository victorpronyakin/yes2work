<?php
/**
 * Created by PhpStorm.
 * Date: 17.04.18
 * Time: 14:47
 */

namespace AppBundle\Repository;


use Doctrine\ORM\EntityRepository;

class ProfileDetailsRepository extends EntityRepository
{
    /**
     * @param $id
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getCandidateDetails($id){
        return $this->createQueryBuilder('pd')
            ->select("pd.idNumber, pd.nationality, pd.ethnicity, pd.beeCheck, pd.mostRole, pd.mostEmployer, pd.specialization, pd.gender, pd.dateOfBirth, 
                pd.mostSalary, pd.salaryPeriod, pd.criminal, pd.criminalDescription, pd.credit, pd.creditDescription, pd.homeAddress, 
                pd.driverLicense, pd.driverNumber, pd.englishProficiency, pd.employed, pd.employedDate, 
                pd.availability, pd.availabilityPeriod, pd.dateAvailability, pd.citiesWorking, 
                pd.copyOfID, pd.cv, pd.universityExemption, pd.matricCertificate, pd.matricTranscript, pd.certificateOfQualification, pd.academicTranscript, pd.creditCheck, pd.payslip,
                pd.picture, pd.video, pd.percentage, pd.looking, pd.firstJob")
            ->where("pd.user = :id")
            ->setParameter('id',$id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param $id
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getCandidateByIdForBusiness($id){
        return $this->createQueryBuilder('pd')
            ->from("AppBundle:User", "u")
            ->select("u.id, u.firstName, u.lastName, pd.nationality, pd.ethnicity, pd.beeCheck, pd.mostRole, pd.mostEmployer, pd.specialization, pd.gender, pd.dateOfBirth, 
                pd.mostSalary, pd.salaryPeriod, pd.criminal, pd.criminalDescription, pd.credit, pd.creditDescription, pd.homeAddress, 
                pd.driverLicense, pd.driverNumber, pd.englishProficiency, pd.employed, pd.employedDate, 
                pd.availability, pd.availabilityPeriod, pd.dateAvailability, pd.citiesWorking, 
                pd.copyOfID, pd.cv, pd.universityExemption, pd.matricCertificate, pd.matricTranscript, pd.certificateOfQualification, pd.academicTranscript, pd.creditCheck, pd.payslip,
                pd.picture, pd.video")
            ->where("pd.user = u.id")
            ->andWhere("pd.user = :id")
            ->setParameter('id',$id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param array $params
     * @return mixed
     */
    public function getCandidateWithCriteria($params = array()){

        $query = $this->createQueryBuilder('pd')
            ->from("AppBundle:User", "u")
            ->select('pd')
            ->where("pd.user = u.id")
            ->andWhere('pd.percentage >= :percentage')
            ->setParameter('percentage', 50)
            ->andWhere("u.approved = :true")
            ->andWhere("u.enabled = :true")
            ->andWhere("pd.looking = :true")
            ->setParameter("true", true)
            ->orderBy('pd.percentage', 'DESC')
        ;

        if(isset($params['search']) && !empty($params['search'])){
            $search = explode(" ", $params['search']);
            if(count($search) > 1){
                $query->andWhere('(u.firstName LIKE :search1) AND (u.lastName LIKE :search2)')
                    ->setParameter('search1', '%'.$search[0].'%')
                    ->setParameter('search2', '%'.$search[1].'%');
            }
            else{
                $query->andWhere('(u.firstName LIKE :search) OR (u.lastName LIKE :search)')
                    ->setParameter('search', '%'.$params['search'].'%');
            }
        }

        if(isset($params['gender']) && $params['gender'] != 'All' && ($params['gender'] == 'Male' || $params['gender'] == 'Female')){
            $query->andWhere('pd.gender = :gender')
                ->setParameter('gender',$params['gender']);
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
//        if(isset($params['ethnicity']) && $params['ethnicity'] != 'null' && $params['ethnicity'] != NULL
//			&& !empty($params['ethnicity']) && $params['ethnicity']!="All"){
//
//            $query->andWhere('pd.ethnicity = :ethnicity')->setParameter('ethnicity', $params['ethnicity']);
//        }

        if(isset($params['nationality']) && $params['nationality'] != 'null' && $params['nationality'] != NULL && $params['nationality'] >0){
            $query->andWhere('pd.nationality = :nationality')
                ->setParameter('nationality', $params['nationality']);
        }

        if(isset($params['location']) && $params['location'] != 'null' && $params['location'] != NULL && !empty($params['location']) && $params['location'] != 'All'){
            $query->andWhere('pd.citiesWorking LIKE :location')
                ->setParameter('location', '%'.$params['location'].'%');
        }


        if(isset($params['availability']) && $params['availability'] != 'null' && $params['availability'] != NULL && $params['availability']>0 && $params['availability']<4){
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

        return $query->getQuery()->getResult();

    }

    /**
     * @param array $params
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getCountCandidateWithCriteria($params = array(), $returnList = false){
		$additional_from = [];
		$is_count = $returnList ?'IDENTITY(pd.user) as candidateID' : 'COUNT(pd.id) as countCandidate' ;
        $query = $this->createQueryBuilder('pd')
            ->from("AppBundle:User", "u")
            ->select($is_count.',
             u.firstName, u.lastName, pd.mostRole as role, pd.mostEmployer as employer, pd.availability, 
             pd.picture, pd.video, pd.mostSalary, pd.salaryPeriod, pd.salaryPeriod, 
             pd.specialization as field, pd.ethnicity, pd.copyOfID')
            ->where("pd.user = u.id")
            ->andWhere('pd.percentage >= :percentage')
            ->setParameter('percentage', 50)
            ->andWhere("u.approved = :true")
            ->andWhere("u.enabled = :true")
            ->andWhere("pd.looking = :true")
            ->setParameter("true", true)
        ;

        if(isset($params['search']) && !empty($params['search'])){
            $search = explode(" ", $params['search']);
            if(count($search) > 1){
                $query->andWhere('(u.firstName LIKE :search1) AND (u.lastName LIKE :search2)')
                    ->setParameter('search1', '%'.$search[0].'%')
                    ->setParameter('search2', '%'.$search[1].'%');
            }
            else{
                $query->andWhere('(u.firstName LIKE :search) OR (u.lastName LIKE :search)')
                    ->setParameter('search', '%'.$params['search'].'%');
            }
        }


        if(isset($params['gender']) && $params['gender'] != 'All' && ($params['gender'] == 'Male' || $params['gender'] == 'Female')){
            $query->andWhere('pd.gender = :gender')
                ->setParameter('gender',$params['gender']);
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

		if(isset($params['nationality']) && $params['nationality'] != 'null' && $params['nationality'] != NULL && $params['nationality'] >0){
            $query->andWhere('pd.nationality = :nationality')
                ->setParameter('nationality', $params['nationality']);
        }

        if(isset($params['location']) && $params['location'] != 'null' && $params['location'] != NULL && !empty($params['location']) && $params['location'] != 'All'){
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
                        $str .= " OR pd.citiesWorking LIKE '".$location."'";
                    }
                    else{
                        $str .= "pd.citiesWorking LIKE '".$location."'";
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
            if(!empty($availabilitys) && !in_array('All', $availabilitys) && !in_array('0', $availabilitys) ){
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
                        $nowD = new \DateTime('+3 month');
                        $now = $nowD->format('Y-m-d');
                        if($key>0){
                            $str .= " OR (((pd.availability = true) OR (pd.availability = false AND pd.availabilityPeriod = 4 AND DATE_FORMAT(pd.dateAvailability, '%Y-%m-%d') <= '$now') OR (pd.availabilityPeriod = 1 OR pd.availabilityPeriod = 2 OR pd.availabilityPeriod = 3)))";
                        }
                        else{
                            $str .= "(((pd.availability = true) OR (pd.availability = false AND pd.availabilityPeriod = 4 AND DATE_FORMAT(pd.dateAvailability, '%Y-%m-%d') <= '$now') OR (pd.availabilityPeriod = 1 OR pd.availabilityPeriod = 2 OR pd.availabilityPeriod = 3)))";
                        }
                    }
                }
                $str .= ')';
                if($str != '()'){
                    $query->andWhere($str);
                }
            }
        }

        //new one

		if(isset($params['video']) && $params['video'] === 'Yes'){
			$query->andWhere('pd.video IS NOT NULL')
				->andWhere('pd.video NOT LIKE :empty_array')
				->setParameter('empty_array', "%".serialize(array())."%")
				->andWhere('pd.video NOT LIKE :empty_null')
				->setParameter('empty_null', "%".serialize(NULL)."%");
		}else if(isset($params['video']) && $params['video'] === 'No'){
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
				if(sizeof($fields) > 1){ $str = '('; } else { $str = ''; }
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
						$str .= "( cq.specialization LIKE '{$field}' OR
									cq.specializationCustom LIKE '{$field}' OR
									pd.specialization LIKE '%{$field}%' OR
									cr.specialization LIKE '%{$field}%'
									)";
					}
				}
				if(sizeof($fields) > 1){ $str .= ')'; }
				if(!in_array('AppBundle:CandidateQualifications',$additional_from)){
					$query->innerJoin("AppBundle:CandidateQualifications", "cq");
					$query->andWhere("cq.user = pd.user ");
					$additional_from[] = 'AppBundle:CandidateQualifications';
				}
				if(!in_array('AppBundle:CandidateReferences',$additional_from)){
					$query->innerJoin("AppBundle:CandidateReferences", "cr");
					$query->andWhere("cr.user = pd.user");
					$additional_from[] = 'AppBundle:CandidateReferences';
				}
				$query->andWhere($str);
			}
		}

		if(isset($params['monthSalaryFrom']) && !empty($params['monthSalaryFrom']) && $params['monthSalaryFrom'] != 'null'){
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
		if(isset($params['monthSalaryTo']) && !empty($params['monthSalaryTo']) && $params['monthSalaryTo'] != 'null'){
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

		/*if(isset($params['highestQualification']) && $params['highestQualification'] != 'null' && $params['highestQualification'] != NULL && $params['highestQualification'] != 'All'){
			if(is_array($params['highestQualification'])){
				$highestQualification = $params['highestQualification'];
			}
			else{
				$highestQualification = explode(',',$params['highestQualification']);
			}
			if(!empty($highestQualification) && !in_array('All', $highestQualification) ) {
                $str = '';
				if (in_array('NQF 4 - Matric', $highestQualification)) {
					$query->andWhere('cq.type = 1');
				} else {
					if (in_array('NQF 2 - Grade 10', $highestQualification)) {
						unset($highestQualification[array_search('NQF 2 - Grade 10', $highestQualification)]);
						$query->andWhere('cq.type = 2');
					}
					if (sizeof($highestQualification) > 0) {

						$str = '(';
						foreach ($highestQualification as $key => $qualif_lvl) {
							$qualif_lvl = "%" . $qualif_lvl . "%";
							if ($key > 0) {
								$str .= " OR cq.levelQ LIKE '$qualif_lvl'";
							} else {
								$str .= "cq.levelQ LIKE '$qualif_lvl'";
							}
						}
						$str .= ')';
						$query->andWhere($str);
					}

				}

				if (!in_array('AppBundle:CandidateQualifications', $additional_from)) {
					$query->from("AppBundle:CandidateQualifications", "cq")->andWhere("pd.user = cq.user");
					$additional_from[] = 'AppBundle:CandidateQualifications';
				}
				$query->andWhere($str);
			}
		}*/

		if(isset($params['eligibility']) && $params['eligibility'] === 'applicable'){
			$query->andWhere("(pd.ethnicity != 'White' AND pd.ethnicity != 'Foreign National')");
		}

		return $query->getQuery()->getResult();
//		return $query->getQuery()->getOneOrNullResult();
    }

    /**
     * @param array $params
     * @param bool $visible
     * @return mixed
     */
    public function getCandidateWithCriteriaWithVisible($params = array(), $visible=true){

        $query = $this->createQueryBuilder('pd')
            ->from("AppBundle:User", "u")
            ->select('pd')
            ->where("pd.user = u.id")
            ->andWhere('pd.percentage > :percentage')
            ->setParameter('percentage', 50)
            ->andWhere("u.approved = :true")
            ->andWhere("u.enabled = :true")
            ->andWhere("pd.looking = :true")
            ->setParameter("true", true)
            ->orderBy('pd.percentage', 'DESC')
        ;

        if(isset($params['search']) && !empty($params['search'])){
            $search = explode(" ", $params['search']);
            if(count($search) > 1){
                $query->andWhere('(u.firstName LIKE :search1) AND (u.lastName LIKE :search2)')
                    ->setParameter('search1', '%'.$search[0].'%')
                    ->setParameter('search2', '%'.$search[1].'%');
            }
            else{
                $query->andWhere('(u.firstName LIKE :search) OR (u.lastName LIKE :search)')
                    ->setParameter('search', '%'.$params['search'].'%');
            }
        }


        if(isset($params['gender']) && $params['gender'] != 'All' && ($params['gender'] == 'Male' || $params['gender'] == 'Female')){
            $query->andWhere('pd.gender = :gender')
                ->setParameter('gender',$params['gender']);
        }

        if(isset($params['ethnicity']) && $params['ethnicity'] != 'null' && $params['ethnicity'] != NULL && !empty($params['ethnicity']) && $params['ethnicity']!="All"){
            $query->andWhere('pd.ethnicity = :ethnicity')
                ->setParameter('ethnicity', $params['ethnicity']);
        }

        if(isset($params['nationality']) && $params['nationality'] != 'null' && $params['nationality'] != NULL && $params['nationality'] >0){
            $query->andWhere('pd.nationality = :nationality')
                ->setParameter('nationality', $params['nationality']);
        }

        if(isset($params['location']) && $params['location'] != 'null' && $params['location'] != NULL && !empty($params['location']) && $params['location'] != 'All'){
            $query->andWhere('pd.citiesWorking LIKE :location')
                ->setParameter('location', '%'.$params['location'].'%');
        }

        if(isset($params['availability']) && $params['availability'] != 'null' && $params['availability'] != NULL && $params['availability']>0 && $params['availability']<4){
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


        return $query->getQuery()->getResult();

    }

    /**
     * @param array $params
     * @param bool $visible
     * @return mixed
     */
    public function getCandidateWithCriteriaWithVisibleNew($params = array(), $visible=true){

        $query = $this->createQueryBuilder('pd')
            ->from("AppBundle:User", "u")
            ->select('pd')
            ->where("pd.user = u.id")
            ->andWhere('pd.percentage > :percentage')
            ->setParameter('percentage', 50)
            ->andWhere("u.approved = :true")
            ->andWhere("u.enabled = :true")
            ->andWhere("pd.looking = :true")
            ->setParameter("true", true)
            ->orderBy('pd.percentage', 'DESC')
        ;

        if(isset($params['search']) && !empty($params['search'])){
            $search = explode(" ", $params['search']);
            if(count($search) > 1){
                $query->andWhere('(u.firstName LIKE :search1) AND (u.lastName LIKE :search2)')
                    ->setParameter('search1', '%'.$search[0].'%')
                    ->setParameter('search2', '%'.$search[1].'%');
            }
            else{
                $query->andWhere('(u.firstName LIKE :search) OR (u.lastName LIKE :search)')
                    ->setParameter('search', '%'.$params['search'].'%');
            }
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

        if(isset($params['nationality']) && $params['nationality'] != 'null' && $params['nationality'] != NULL && $params['nationality'] != 0){
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


        if(isset($params['availability']) && $params['availability'] != 'null' && $params['availability'] != NULL && $params['availability'] != 0){
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

        return $query->getQuery()->getResult();

    }

    /**
     * @param array $params
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getCountCandidateWithCriteriaWithVisible($params = array()){
        $query = $this->createQueryBuilder('pd')
            ->from("AppBundle:User", "u")
            ->select('COUNT(pd.id) as countCandidate')
            ->where("pd.user = u.id")
            ->andWhere('pd.percentage >= :percentage')
            ->setParameter('percentage', 50)
            ->andWhere("u.approved = :true")
            ->andWhere("u.enabled = :true")
            ->andWhere("pd.looking = :true")
            ->setParameter("true", true)
        ;

        if(isset($params['search']) && !empty($params['search'])){
            $search = explode(" ", $params['search']);
            if(count($search) > 1){
                $query->andWhere('(u.firstName LIKE :search1) AND (u.lastName LIKE :search2)')
                    ->setParameter('search1', '%'.$search[0].'%')
                    ->setParameter('search2', '%'.$search[1].'%');
            }
            else{
                $query->andWhere('(u.firstName LIKE :search) OR (u.lastName LIKE :search)')
                    ->setParameter('search', '%'.$params['search'].'%');
            }
        }


        if(isset($params['gender']) && $params['gender'] != 'All' && ($params['gender'] == 'Male' || $params['gender'] == 'Female')){
            $query->andWhere('pd.gender = :gender')
                ->setParameter('gender',$params['gender']);
        }

        if(isset($params['ethnicity']) && $params['ethnicity'] != 'null' && $params['ethnicity'] != NULL && !empty($params['ethnicity']) && $params['ethnicity']!="All"){
//            $query->andWhere('pd.ethnicity = :ethnicity')
//                ->setParameter('ethnicity', $params['ethnicity']);

			if(is_array($params['ethnicity'])){
				$ethnicitys = $params['ethnicity'];
			}
			else{
				$ethnicitys = explode(',',$params['ethnicity']);
			}
			if(!empty($ethnicitys) && !in_array('All', $ethnicitys) ){
				$str = '(';
				foreach ($ethnicitys as $key=>$ethnicity){
					$ethnicity = "%".$ethnicity."%";
					if($key>0){
						$str .= " OR pd.ethnicity LIKE '$ethnicity'";
					}
					else{
						$str .= "pd.ethnicity LIKE '$ethnicity'";
					}
				}
				$str .= ')';
				$query->andWhere($str);
			}
        }

        if(isset($params['nationality']) && $params['nationality'] != 'null' && $params['nationality'] != NULL && $params['nationality'] >0){
            $query->andWhere('pd.nationality = :nationality')
                ->setParameter('nationality', $params['nationality']);
        }

        if(isset($params['location']) && $params['location'] != 'null' && $params['location'] != NULL && !empty($params['location']) && $params['location'] != 'All'){
            $query->andWhere('pd.citiesWorking LIKE :location')
                ->setParameter('location', '%'.$params['location'].'%');
        }

        if(isset($params['availability']) && $params['availability'] != 'null' && $params['availability'] != NULL && $params['availability']>0 && $params['availability']<4){
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

        return $query->getQuery()->getOneOrNullResult();
    }
}
