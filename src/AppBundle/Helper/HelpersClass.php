<?php
/**
 * Created by PhpStorm.
 * Date: 16.04.18
 * Time: 10:40
 */

namespace AppBundle\Helper;


use AppBundle\Entity\NotificationAdmin;
use AppBundle\Entity\ProfileDetails;
use AppBundle\Entity\Settings;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;

class HelpersClass
{
    /**
     * @param ProfileDetails $profileDetails
     * @param EntityManager $em
     * @return ProfileDetails
     */
    public static function candidateProfileCompletePercentage(ProfileDetails $profileDetails, EntityManager $em){
        $RFP_MAX = 20;
        $NRFP_MAX = 20;
        $ARP_MAX= 10;

        $RFUser = [
            'firstName', 'lastName', 'email', 'phone'
        ];
        $RFProfile = [
            'idNumber', 'mostRole', 'mostEmployer', 'nationality', 'ethnicity', 'gender', 'homeAddress', 'employed', 'availability', 'citiesWorking'
        ];
        $NRFProfile = [
            'specialization', 'dateOfBirth', 'mostSalary', 'criminal', 'driverLicense', 'englishProficiency', 'cv', 'universityExemption', 'matricCertificate', 'matricTranscript', 'certificateOfQualification', 'academicTranscript', 'creditCheck', 'payslip', 'picture'
        ];

        /*if($profileDetails->getUniversityExemption() == true){
            $NRFProfile[] = 'matricCertificate';
            $NRFProfile[] = 'matricTranscript';
            $NRFProfile[] = 'certificateOfQualification';
            $NRFProfile[] = 'academicTranscript';
        }*/

        $RFP_ONE = ($RFP_MAX/(count($RFUser) + count($RFProfile)));
        $NRFP_ONE = ($NRFP_MAX/count($NRFProfile));
        $ARP_ONE = ($ARP_MAX/2);
        $VP_ONE = $CVP_ONE = 25;

        $RFP = $NRFP = $ARP = $percentage = 0;

        //REQUIRED FIELDS USER
        foreach ($RFUser as $field){
            $methodGet = 'get'.ucfirst($field);
            if(method_exists(User::class,$methodGet)){
                if(!empty($profileDetails->getUser()->$methodGet())){
                    $RFP = $RFP + $RFP_ONE;
                }
            }
        }
        //REQUIRED FIELDS PROFILE
        foreach ($RFProfile as $field){
            $methodGet = 'get'.ucfirst($field);
            if(method_exists(ProfileDetails::class,$methodGet)){
                if (is_bool($profileDetails->$methodGet()) || !empty($profileDetails->$methodGet())){
                    $RFP = $RFP + $RFP_ONE;
                }
            }
        }
        //CHECK MAX REQUIRED FIELDS
        if($RFP > $RFP_MAX){
            $RFP = $RFP_MAX;
        }
        $percentage = $percentage + $RFP;

        //NON REQUIRED FIELDS PROFILE
        foreach ($NRFProfile as $field){
            $methodGet = 'get'.ucfirst($field);
            if(method_exists(ProfileDetails::class,$methodGet)){
                if($field == 'mostSalary' && $profileDetails->$methodGet() >=0 ){
                    $NRFP = $NRFP + $NRFP_ONE;
                }
                elseif (is_bool($profileDetails->$methodGet()) || !empty($profileDetails->$methodGet())){
                    $NRFP = $NRFP + $NRFP_ONE;
                }
            }
        }

        //CHECK MAX NON REQUIRED FIELDS
        if($NRFP > $NRFP_MAX){
            $NRFP = $NRFP_MAX;
        }
        $percentage = $percentage + $NRFP;

        //Achievements
        $achievements = $em->getRepository("AppBundle:CandidateAchievements")->findBy(['user'=>$profileDetails->getUser()]);
        if(!empty($achievements)){
            $ARP = $ARP + $ARP_ONE;
        }
        //References
        $references = $em->getRepository("AppBundle:CandidateReferences")->findBy(['user'=>$profileDetails->getUser()]);
        if(!empty($references)){
            $ARP = $ARP + $ARP_ONE;
        }
        //CHECK MAX Achievements & References
        if($ARP > $ARP_MAX){
            $ARP = $ARP_MAX;
        }
        $percentage = $percentage + $ARP;

        //VIDEO
        if(!empty($profileDetails->getVideo())){
            $percentage = $percentage + $VP_ONE;
        }
        //COPY OF ID
        if(!empty($profileDetails->getCopyOfID())){
            $percentage = $percentage + $CVP_ONE;
        }

        $percentage = round($percentage);
        if($percentage >= 100){
            $percentage = 100;
        }
        $profileDetails->setPercentage($percentage);

        return $profileDetails;
    }

    /**
     * @param ProfileDetails $profileDetails
     * @param EntityManager $em
     * @return ProfileDetails
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public static function checkAutoVisible(ProfileDetails $profileDetails, EntityManager $em){
        $settings = $em->getRepository('AppBundle:Settings')->findOneBy([]);
        if(!$settings instanceof Settings){
            $settings = new Settings(false);
            $em->persist($settings);
            $em->flush();
        }
        if($profileDetails->getPercentage() > 50
            && !empty($profileDetails->getCopyOfID() && isset($profileDetails->getCopyOfID()[0]))
            && isset($profileDetails->getCopyOfID()[0]['approved']) && $profileDetails->getCopyOfID()[0]['approved'] == true
            && (
                ($settings->getAllowVideo() == true)
                || (!empty($profileDetails->getVideo()) && isset($profileDetails->getVideo()['approved']) && $profileDetails->getVideo()['approved'] == true)
            )
        ){
            $profileDetails->setLooking(true);
        }
        else{
            $profileDetails->setLooking(false);
        }
        return $profileDetails;
    }

    /**
     * @param $idNumber
     * @return array|bool
     */
    public static function isValidIDNumber($idNumber){
        if(!empty($idNumber) && strlen($idNumber) == 13){
            $yy = substr($idNumber,0,2);
            $mm = substr($idNumber,2,2);
            $dd = substr($idNumber,4,2);
            try{
                $dob = new \DateTime($yy.'-'.$mm.'-'.$dd);
            }
            catch (\Exception $exception){
                $dob = null;
            }

            if($dob instanceof \DateTime){
                if(substr($dob->format('Y'),2,2) == $yy && $dob->format('m') == $mm && $dob->format('d') == $dd){
                    if(in_array(substr($idNumber,10,1),[0,1]) && in_array(substr($idNumber,11,1),[8,9])){
                        $ncheck = 0;
                        $beven = false;
                        for ($c = strlen($idNumber)-1; $c>=0; $c--) {
                            $cdigit = $idNumber[$c];
                            $ndigit = intval($cdigit, 10);
                            if ($beven) {
                                if (($ndigit *= 2) > 9) {
                                  $ndigit -= 9;
                                }
                            }
                            $ncheck += $ndigit;
                            $beven = !$beven;
                        }
                        if (($ncheck % 10) == 0) {
                            if (substr($idNumber,6,1) < 5){
                                $gender = "Female";
                            }
                            else{
                                $gender = "Male";
                            }
                            if(substr($idNumber,10,1) == 1){
                                $nationality = 2;
                            }
                            else{
                                $nationality = 1;
                            }
                            $now = new \DateTime();
                            return [
                                'dateOfBirth' => $dob->setTimezone($now->getTimezone()),
                                'gender' => $gender,
                                'nationality' => $nationality
                            ];
                        }
                    }
                }
            }
        }

        return false;
    }

	/**
	 * @param EntityManager $em
	 * @param string $candidateID
	 * @return string
	 * @throws \Doctrine\ORM\ORMException
	 * @throws \Doctrine\ORM\OptimisticLockException
	 */
    public static function getHighestQualificationForCandidate(EntityManager $em, $candidateID){
		$highest_qualif = '';
		$highest_num = 0;
		$highest_type = 0;
		if (!empty($candidateID)) {
			$candidate_qualifications = $em->getRepository("AppBundle:CandidateQualifications")->getQualificationsCandidate($candidateID);
			if (!empty($candidate_qualifications)) {
				foreach ($candidate_qualifications as $qualif) {
					if (isset($qualif['levelQ']) && $qualif['type'] == 3 && $highest_type <= 3) {
						$qualif_lvl = (int)filter_var($qualif['levelQ'], FILTER_SANITIZE_NUMBER_INT);
						if($qualif_lvl > $highest_num) {
							$highest_num = $qualif_lvl;
							$highest_qualif = $qualif['levelQ'];
							$highest_type = 3;
						}
					}else if(isset($qualif['type']) && $qualif['type'] == 2 && $highest_type <= 2){
						$highest_qualif = 'NQF 2 - Grade 10';
						$highest_type = 2;
					}else if(isset($qualif['type']) && $qualif['type'] == 1 && $highest_type <= 1){
						$highest_qualif = 'NQF 4 - Matric';
						$highest_type = 1;
					}
				}
			}
		}
		return $highest_qualif;
	}

	/**
	 * @param EntityManager $em
	 * @param string $candidateID
	 * @return string
	 * @throws \Doctrine\ORM\ORMException
	 * @throws \Doctrine\ORM\OptimisticLockException
	 */
	public static function getYearsOfWorkExperienceForCandidate(EntityManager $em, $candidateID){
		$years = 0;
		$exp_in_sec = 0;
		if (!empty($candidateID)) {
			$candidate_qualifications = $em->getRepository("AppBundle:CandidateReferences")->findBy(["user" => $candidateID]);
			if(count($candidate_qualifications) > 0) {
				foreach ($candidate_qualifications as $post) {
					$start_date = $post->getStartDate()->format('Y-m-d H:i:s');
					$end_date = $post->getEndDate()->format('Y-m-d H:i:s');
					$unix_start_date = strtotime($start_date);
					$unix_end_date = strtotime($end_date);
					$exp_in_sec += $unix_end_date - $unix_start_date;
				}
				$years = round($exp_in_sec / (60 * 60 * 24 * 365));
			}
		}
		return $years;
	}

	/**
	 * @param $em
	 * @param $result
	 * @param $params
	 * @return array
	 * @throws \Doctrine\ORM\ORMException
	 * @throws \Doctrine\ORM\OptimisticLockException
	 */
	public function filtering_candidates($em, $result, $params)
	{
		$applicants = [];
		if (!empty($result)) {

			if (isset($params['highestQualification']) && $params['highestQualification'] != 'null' && $params['highestQualification'] != NULL && $params['highestQualification'] != 'All') {
				if (is_array($params['highestQualification'])) {
					$highestQualification = $params['highestQualification'];
				} else {
					$highestQualification = explode(',', $params['highestQualification']);
				}
				if (in_array('NQF 4 - Matric', $highestQualification)) {
					$highestQualification = array('NQF 4 - Matric');
				}
			}
			else {
				$highestQualification = array();
			}
			if (isset($params['yearsOfWorkExperience']) && $params['yearsOfWorkExperience'] != 'null' && $params['yearsOfWorkExperience'] != NULL && $params['yearsOfWorkExperience'] != 0) {
				if (is_array($params['yearsOfWorkExperience'])) {
					$yearsOfWorkExperience = $params['yearsOfWorkExperience'];
				} else {
					$yearsOfWorkExperience = explode(',', $params['yearsOfWorkExperience']);
				}
			}
			else {
				$yearsOfWorkExperience = array();
			}

			$applicants_ids = [];
			foreach ($result as $key => $applicant) {
				if (isset($applicants_ids[$applicant['id']])) {
					continue;
				}
				$applicants_ids[$applicant['id']] = $applicant['id'];
				$applicant['yearsOfWorkExperience'] = HelpersClass::getYearsOfWorkExperienceForCandidate($em, $applicant['id']);
				$applicant['highestQualification'] = HelpersClass::getHighestQualificationForCandidate($em, $applicant['id']);

				$filter_ok = true;
				if (!empty($highestQualification)) {
					if (!in_array($applicant['highestQualification'], $highestQualification)) {
						$filter_ok = false;
					}
				}
				if (!empty($yearsOfWorkExperience) && $filter_ok == true) {
					$filter_ok = false;
					/* $yearsOfWorkExperience
					0 All
					1 0
					2 0-1
					3 1-2
					4 3-5
					5 5+
					*/
					if (in_array(1, $yearsOfWorkExperience) && $applicant['yearsOfWorkExperience'] == 0) {
						$filter_ok = true;
					} elseif (in_array(2, $yearsOfWorkExperience) && ($applicant['yearsOfWorkExperience'] == 0 || $applicant['yearsOfWorkExperience'] == 1)) {
						$filter_ok = true;
					} elseif (in_array(3, $yearsOfWorkExperience) && ($applicant['yearsOfWorkExperience'] == 1 || $applicant['yearsOfWorkExperience'] == 2)) {
						$filter_ok = true;
					} elseif (in_array(4, $yearsOfWorkExperience) && ($applicant['yearsOfWorkExperience'] > 2 && $applicant['yearsOfWorkExperience'] < 6)) {
						$filter_ok = true;
					} elseif (in_array(5, $yearsOfWorkExperience) && $applicant['yearsOfWorkExperience'] > 5) {
						$filter_ok = true;
					}
				}
				if ($filter_ok === true) {
					$applicants[] = $applicant;
				}
			}

			//Sorting
			if(isset($params['orderBy']) && !empty($params['orderBy']) && in_array($params['orderBy'], ['Name', 'Email', 'Phone'])){
				if(isset($params['orderSort']) && !empty($params['orderSort']) && in_array($params['orderSort'], ['asc', 'desc'])){
					if($params['orderBy'] == 'Name'){
						if($params['orderSort'] == 'asc'){
							usort($applicants, function($a, $b) {
								return $a['firstName'] > $b['firstName'];
							});
						}
						else{
							usort($applicants, function($a, $b) {
								return $a['firstName'] < $b['firstName'];
							});
						}
					}
					elseif ($params['orderBy'] == 'Email'){
						if($params['orderSort'] == 'asc'){
							usort($applicants, function($a, $b) {
								return $a['email'] > $b['email'];
							});
						}
						else{
							usort($applicants, function($a, $b) {
								return $a['email'] < $b['email'];
							});
						}
					}
					elseif ($params['orderBy'] == 'Phone'){
						if($params['orderSort'] == 'asc'){
							usort($applicants, function($a, $b) {
								return $a['phone'] > $b['phone'];
							});
						}
						else{
							usort($applicants, function($a, $b) {
								return $a['phone'] < $b['phone'];
							});
						}
					}
				}
			}
		}
		return $applicants;
	}

}