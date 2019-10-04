import { BusinessJobsAwaitingApproval } from './models';

export class BusinessApprove implements IBusinessApprove {
  public id: number;
  public firstName: string;
  public lastName: string;
  public phone: string;
  public email: string;
  public companyName: string;
  constructor(data?: IBusinessApprove) {
    this.id = data.id;
    this.firstName = data.firstName;
    this.lastName = data.lastName;
    this.phone = data.phone;
    this.email = data.email;
    this.companyName = data.companyName;
  };
}

export interface IBusinessApprove {
  id?: number;
  firstName?: string;
  lastName?: string;
  phone?: string;
  email?: string;
  companyName?: string;
}

export class CandidateApproveList implements ICandidateApproveList {
  public items: CandidateApprove[];
  public pagination: InterfacePagination;
  constructor(data?: ICandidateApproveList) {
    this.items = data.items;
    this.pagination = data.pagination;
  };
}

export interface ICandidateApproveList {
  items?: CandidateApprove[];
  pagination?: InterfacePagination;
}

export class CandidateApprove implements ICandidateApprove {
  public id: number;
  public firstName: string;
  public lastName: string;
  public phone: string;
  public email: string;
  public articlesFirm: string;
  constructor(data?: ICandidateApprove) {
    this.id = data.id;
    this.firstName = data.firstName;
    this.lastName = data.lastName;
    this.phone = data.phone;
    this.email = data.email;
    this.articlesFirm = data.articlesFirm;
  };
}

export interface ICandidateApprove {
  id?: number;
  firstName?: string;
  lastName?: string;
  phone?: string;
  email?: string;
  articlesFirm?: string;
}

export class AdminBusinessCompany implements IAdminBusinessCompany {
  public name?: string;
  public address?: string;
  public addressCountry: string;
  public addressState: string;
  public addressZipCode: string;
  public addressCity: string;
  public addressSuburb: string;
  public addressStreet: string;
  public addressStreetNumber: string;
  public addressBuildName: string;
  public addressUnit: string;
  public companySize?: number;
  public jse?: boolean;
  public industry?: number;
  public description?: string;
  constructor(data?: IAdminBusinessCompany) {
    this.name = data.name;
    this.address = data.address;
    this.addressCountry = data.addressCountry;
    this.addressState = data.addressState;
    this.addressZipCode = data.addressZipCode;
    this.addressCity = data.addressCity;
    this.addressSuburb = data.addressSuburb;
    this.addressStreet = data.addressStreet;
    this.addressStreetNumber = data.addressStreetNumber;
    this.addressBuildName = data.addressBuildName;
    this.addressUnit = data.addressUnit;
    this.companySize = data.companySize;
    this.jse = data.jse;
    this.industry = data.industry;
    this.description = data.description;
  };
}

export interface IAdminBusinessCompany {
  name?: string;
  address?: string;
  addressCountry?: string;
  addressState?: string;
  addressZipCode?: string;
  addressCity?: string;
  addressSuburb?: string;
  addressStreet?: string;
  addressStreetNumber?: string;
  addressBuildName?: string;
  addressUnit?: string;
  companySize?: number;
  jse?: boolean;
  industry?: number;
  description?: string;
}

export class AdminBusinessUser implements IAdminBusinessUser {
  public id?: number;
  public firstName?: string;
  public lastName?: string;
  public jobTitle?: string;
  public phone?: string;
  public email?: string;
  public agentName?: string;
  constructor(data?: IAdminBusinessUser) {
    this.id = data.id;
    this.firstName = data.firstName;
    this.lastName = data.lastName;
    this.jobTitle = data.jobTitle;
    this.phone = data.phone;
    this.email = data.email;
    this.agentName = data.agentName;
  };
}

export interface IAdminBusinessUser {
  id?: number;
  firstName?: string;
  lastName?: string;
  jobTitle?: string;
  phone?: string;
  email?: string;
  agentName?: string;
}

export class AdminBusinessProfile implements IBusinessProfile {
  public user?: AdminBusinessUser;
  public company?: AdminBusinessCompany;
  constructor(data?: IBusinessProfile) {
    this.user = data.user;
    this.company = data.company;
  }
}

export interface IBusinessProfile {
  user?: AdminBusinessUser;
  company?: AdminBusinessCompany;
}


export class AdminCandidateUser implements IAdminCandidateUser {
  public id?: number;
  public firstName?: string;
  public lastName?: string;
  public phone?: string;
  public email?: string;
  public agentName?: string;
  constructor(data?: IAdminCandidateUser) {
    this.id = data.id;
    this.firstName =  data.firstName;
    this.lastName = data.lastName;
    this.phone = data.phone;
    this.email = data.email;
    this.agentName = data.agentName;
  }
}

export interface IAdminCandidateUser {
  id?: number;
  firstName?: string;
  lastName?: string;
  phone?: string;
  email?: string;
  agentName?: string;
}

export class AdminCandidateUserProfileVideo implements IAdminCandidateUserProfileVideo {
  public url?: string;
  public name?: string;
  public approved?: boolean;
  constructor(data?: IAdminCandidateUserProfileVideo) {
    this.url = data.url;
    this.name = data.name;
    this.approved = data.approved;
  }
}

export interface IAdminCandidateUserProfileVideo {
  url?: string;
  name?: string;
  approved?: boolean;
}

export class AdminCandidateUserProfileCVFiles implements IAdminCandidateUserProfileCVFiles {
  public url?: string;
  public name?: string;
  public size?: number;
  public approved?: boolean;
  constructor(data?: IAdminCandidateUserProfileCVFiles) {
    this.url = data.url;
    this.name = data.name;
    this.size = data.size;
    this.approved = data.approved;
  }
}

export interface IAdminCandidateUserProfileCVFiles {
  url?: string;
  name?: string;
  size?: number;
  approved?: boolean;
}

export class AdminCandidateUserProfilePayslip implements IAdminCandidateUserProfilePayslip {
    public url?: string;
    public name?: string;
    public size?: number;
    public approved?: boolean;
    constructor(data?: IAdminCandidateUserProfilePayslip) {
        this.url = data.url;
        this.name = data.name;
        this.size = data.size;
        this.approved = data.approved;
    }
}

export interface IAdminCandidateUserProfilePayslip {
    url?: string;
    name?: string;
    size?: number;
    approved?: boolean;
}

export class AdminCandidateUserProfileCreditCheck implements IAdminCandidateUserProfileCreditCheck {
  public url?: string;
  public name?: string;
  public size?: number;
  public approved?: boolean;
  constructor(data?: IAdminCandidateUserProfileCreditCheck) {
    this.url = data.url;
    this.name = data.name;
    this.size = data.size;
    this.approved = data.approved;
  }
}

export interface IAdminCandidateUserProfileCreditCheck {
  url?: string;
  name?: string;
  size?: number;
  approved?: boolean;
}

export class AdminCandidateUserProfileUniversityManuscript implements IAdminCandidateUserProfileUniversityManuscript {
  public url?: string;
  public name?: string;
  public size?: number;
  public approved?: boolean;
  constructor(data?: IAdminCandidateUserProfileUniversityManuscript) {
    this.url = data.url;
    this.name = data.name;
    this.size = data.size;
    this.approved = data.approved;
  }
}

export interface IAdminCandidateUserProfileUniversityManuscript {
  url?: string;
  name?: string;
  size?: number;
  approved?: boolean;
}

export class AdminCandidateUserProfileTertiaryCertificate implements IAdminCandidateUserProfileTertiaryCertificate {
  public url?: string;
  public name?: string;
  public size?: number;
  public approved?: boolean;
  constructor(data?: IAdminCandidateUserProfileTertiaryCertificate) {
    this.url = data.url;
    this.name = data.name;
    this.size = data.size;
    this.approved = data.approved;
  }
}

export interface IAdminCandidateUserProfileTertiaryCertificate {
  url?: string;
  name?: string;
  size?: number;
  approved?: boolean;
}

export class AdminCandidateUserProfileMatricCertificate implements IAdminCandidateUserProfileMatricCertificate {
  public url?: string;
  public name?: string;
  public size?: number;
  public approved?: boolean;
  constructor(data?: IAdminCandidateUserProfileMatricCertificate) {
    this.url = data.url;
    this.name = data.name;
    this.size = data.size;
    this.approved = data.approved;
  }
}

export interface IAdminCandidateUserProfileMatricCertificate {
  url?: string;
  name?: string;
  size?: number;
  approved?: boolean;
}

export class AdminCandidateUserProfilePicture implements IAdminCandidateUserProfilePicture {
  public url?: string;
  public name?: string;
  public size?: number;
  public approved?: boolean;
  constructor(data?: IAdminCandidateUserProfilePicture) {
    this.url = data.url;
    this.name = data.name;
    this.size = data.size;
    this.approved = data.approved;
  }
}

export interface IAdminCandidateUserProfilePicture{
  url?: string;
  name?: string;
  size?: number;
  approved?: boolean;
}

export class AdminCandidateUserProfile implements IAdminCandidateUserProfile {
  public idNumber?: string;
  public nationality?: number;
  public ethnicity?: string;
  public beeCheck?: Date;
  public mostRole?: string;
  public mostEmployer?: string;
  public specialization?: string;
  public gender?: string;
  public dateOfBirth?: Date;
  public mostSalary?: number;
  public salaryPeriod?: string;
  public criminal?: boolean;
  public firstJob?: boolean;
  public universityExemption?: boolean;
  public criminalDescription?: string;
  public credit?: boolean;
  public creditDescription?: string;
  public homeAddress?: string;
  public driverLicense?: boolean;
  public driverNumber?: string;
  public englishProficiency?: number;
  public employed?: boolean;
  public employedDate?: Date;
  public availability?: boolean;
  public availabilityPeriod?: number;
  public dateAvailability?: Date;
  public citiesWorking?: string[];
  public copyOfID?: AdminCandidateUserProfileCVFiles[];
  public cv?: AdminCandidateUserProfileCVFiles[];
  public cvFiles?: AdminCandidateUserProfileCVFiles[];
  public matricCertificate?: AdminCandidateUserProfileMatricCertificate[];
  public matricTranscript?: AdminCandidateUserProfileMatricCertificate[];
  public certificateOfQualification?: AdminCandidateUserProfileMatricCertificate[];
  public academicTranscript?: AdminCandidateUserProfileMatricCertificate[];
  public creditCheck?: AdminCandidateUserProfileCreditCheck[];
  public payslip?: AdminCandidateUserProfilePayslip[];
  public picture?: AdminCandidateUserProfilePicture[];
  public video?: AdminCandidateUserProfileVideo;
  public percentage?: number;
  public looking?: boolean;

  constructor(data?: IAdminCandidateUserProfile) {
    this.idNumber = data.idNumber;
    this.nationality = data.nationality;
    this.ethnicity = data.ethnicity;
    this.beeCheck = data.beeCheck;
    this.mostRole = data.mostRole;
    this.mostEmployer = data.mostEmployer;
    this.specialization = data.specialization;
    this.gender = data.gender;
    this.firstJob = data.firstJob;
    this.dateOfBirth = data.dateOfBirth;
    this.mostSalary = data.mostSalary;
    this.salaryPeriod = data.salaryPeriod;
    this.criminal = data.criminal;
    this.universityExemption = data.universityExemption;
    this.criminalDescription = data.criminalDescription;
    this.credit = data.credit;
    this.creditDescription = data.creditDescription;
    this.homeAddress = data.homeAddress;
    this.driverLicense = data.driverLicense;
    this.driverNumber = data.driverNumber;
    this.englishProficiency = data.englishProficiency;
    this.employed = data.employed;
    this.employedDate = data.employedDate;
    this.availability = data.availability;
    this.availabilityPeriod = data.availabilityPeriod;
    this.dateAvailability = data.dateAvailability;
    this.citiesWorking = data.citiesWorking;
    this.copyOfID = data.copyOfID;
    this.cv = data.cv;
    this.cvFiles = data.cvFiles;
    this.matricCertificate = data.matricCertificate;
    this.matricTranscript = data.matricTranscript;
    this.certificateOfQualification = data.certificateOfQualification;
    this.academicTranscript = data.academicTranscript;
    this.creditCheck = data.creditCheck;
    this.payslip = data.payslip;
    this.picture = data.picture;
    this.video = data.video;
    this.percentage = data.percentage;
    this.looking = data.looking;
  };
}

export interface IAdminCandidateUserProfile {
  idNumber?: string;
  nationality?: number;
  ethnicity?: string;
  beeCheck?: Date;
  mostRole?: string;
  mostEmployer?: string;
  specialization?: string;
  gender?: string;
  dateOfBirth?: Date;
  mostSalary?: number;
  salaryPeriod?: string;
  firstJob?: boolean;
  criminal?: boolean;
  universityExemption?: boolean;
  criminalDescription?: string;
  credit?: boolean;
  creditDescription?: string;
  homeAddress?: string;
  driverLicense?: boolean;
  driverNumber?: string;
  englishProficiency?: number;
  employed?: boolean;
  employedDate?: Date;
  availability?: boolean;
  availabilityPeriod?: number;
  dateAvailability?: Date;
  citiesWorking?: string[];
  copyOfID?: AdminCandidateUserProfileCVFiles[];
  cv?: AdminCandidateUserProfileCVFiles[];
  cvFiles?: AdminCandidateUserProfileCVFiles[];
  matricCertificate?: AdminCandidateUserProfileMatricCertificate[];
  matricTranscript?: AdminCandidateUserProfileMatricCertificate[];
  certificateOfQualification?: AdminCandidateUserProfileMatricCertificate[];
  academicTranscript?: AdminCandidateUserProfileMatricCertificate[];
  creditCheck?: AdminCandidateUserProfileCreditCheck[];
  payslip?: AdminCandidateUserProfilePayslip[];
  picture?: AdminCandidateUserProfilePicture[];
  video?: AdminCandidateUserProfileVideo;
  percentage?: number;
  looking?: boolean;
}

export class AdminCandidateProfile implements ICandidateProfile {
  public profile?: AdminCandidateUserProfile;
  public user?: AdminCandidateUser;
  public allowVideo?: boolean;
  constructor(data?: ICandidateProfile) {
    this.profile = data.profile;
    this.user = data.user;
    this.allowVideo = data.allowVideo;
  }
}

export interface ICandidateProfile {
  allowVideo?: boolean;
  user?: AdminCandidateUser;
  profile?: AdminCandidateUserProfile;
}

export class AdminCandidateProfileNew implements ICandidateProfileNew {
  public profile?: AdminCandidateUserProfileNew;
  public user?: AdminCandidateUser;
  constructor(data?: ICandidateProfileNew) {
    this.profile = data.profile;
    this.user = data.user;
  }
}

export interface ICandidateProfileNew {
  user?: AdminCandidateUser;
  profile?: AdminCandidateUserProfileNew;
}

export class AdminCandidateUserProfileNew implements IAdminCandidateUserProfileNew {
  public saicaNumber?: string;
  public articlesFirm?: string;
  public boards?: number;
  public nationality?: number;
  public idNumber?: string;
  public ethnicity?: string;
  public gender?: string;
  public dateOfBirth?: Date;
  public dateArticlesCompleted?: Date;
  public costToCompany?: number;
  public criminal?: boolean;
  public credit?: boolean;
  public otherQualifications?: string;
  public homeAddress?: string;

  public addressCountry?: string;
  public addressState?: string;
  public addressZipCode?: string;
  public addressCity?: string;
  public addressSuburb?: string;
  public addressStreet?: string;
  public addressStreetNumber?: string;
  public addressUnit?: string;

  public availability?: boolean;
  public dateAvailability?: Date;
  public citiesWorking?: string[];
  public linkedinUrl?: string;
  public percentage?: number;
  public looking?: boolean;
  public visible?: boolean;
  constructor(data?: IAdminCandidateUserProfileNew) {
    this.saicaNumber = data.saicaNumber;
    this.boards = data.boards;
    this.articlesFirm = data.articlesFirm;
    this.nationality = data.nationality;
    this.idNumber = data.idNumber;
    this.ethnicity = data.ethnicity;
    this.gender = data.gender;
    this.dateOfBirth = data.dateOfBirth;
    this.dateArticlesCompleted = data.dateArticlesCompleted;
    this.costToCompany = data.costToCompany;
    this.criminal = data.criminal;
    this.credit = data.credit;
    this.otherQualifications = data.otherQualifications;
    this.homeAddress = data.homeAddress;
    this.addressCountry = data.addressCountry;
    this.addressState = data.addressState;
    this.addressZipCode = data.addressZipCode;
    this.addressCity = data.addressCity;
    this.addressSuburb = data.addressSuburb;
    this.addressStreet = data.addressStreet;
    this.addressStreetNumber = data.addressStreetNumber;
    this.addressUnit = data.addressUnit;
    this.availability = data.availability;
    this.dateAvailability = data.dateAvailability;
    this.citiesWorking = data.citiesWorking;
    /*this.picture = data.picture;
    this.matricCertificate = data.matricCertificate;
    this.tertiaryCertificate = data.tertiaryCertificate;
    this.universityManuscript = data.universityManuscript;
    this.creditCheck = data.creditCheck;
    this.cvFiles =  data.cvFiles;*/
    this.linkedinUrl = data.linkedinUrl;
    /*this.video = data.video;*/
    this.percentage = data.percentage;
    this.looking = data.looking;
    this.visible = data.visible;
  };

}

export interface IAdminCandidateUserProfileNew {
  saicaNumber?: string;
  articlesFirm?: string;
  boards?: number;
  nationality?: number;
  idNumber?: string;
  ethnicity?: string;
  gender?: string;
  dateOfBirth?: Date;
  dateArticlesCompleted?: Date;
  costToCompany?: number;
  criminal?: boolean;
  credit?: boolean;
  otherQualifications?: string;
  homeAddress?: string;

  addressCountry?: string;
  addressState?: string;
  addressZipCode?: string;
  addressCity?: string;
  addressSuburb?: string;
  addressStreet?: string;
  addressStreetNumber?: string;
  addressUnit?: string;

  availability?: boolean;
  dateAvailability?: Date;
  citiesWorking?: string[];
  linkedinUrl?: string;
  percentage?: number;
  looking?: boolean;
  visible?: boolean;
}


export class CandidateFileApproveList implements ICandidateFileApproveList {
  public items?: CandidateFileApprove[];
  public pagination?: InterfacePagination;
  constructor(data?: ICandidateFileApproveList) {
    this.items = data.items;
    this.pagination = data.pagination;
  }
}

export interface ICandidateFileApproveList {
  items?: CandidateFileApprove[];
  pagination?: InterfacePagination;
}

export class CandidateFileApprove implements ICandidateFileApprove {
    public userId?: number;
    public firstName?: string;
    public lastName?: string;
    public url?: string;
    public adminUrl?: string;
    public fileName?: string;
    public size?: number;
    public fieldName?: string;
    constructor(data?: ICandidateFileApprove) {
        this.userId = data.userId;
        this.firstName = data.firstName;
        this.lastName = data.lastName;
        this.url = data.url;
        this.adminUrl = data.adminUrl;
        this.fileName = data.fileName;
        this.size = data.size;
        this.fieldName = data.fieldName;
    }
}

export interface ICandidateFileApprove {
    userId?: number;
    firstName?: string;
    lastName?: string;
    url?: string;
    adminUrl?: string;
    fileName?: string;
    size?: number;
    fieldName?: string;
}





export class CandidateVideoApproveList implements ICandidateVideoApproveList {
  public items?: CandidateVideoApprove[];
  public pagination?: InterfacePagination;
  constructor(data?: ICandidateVideoApproveList) {
    this.items = data.items;
    this.pagination = data.pagination;
  }
}

export interface ICandidateVideoApproveList {
  items?: CandidateVideoApprove[];
  pagination?: InterfacePagination;
}

export class CandidateVideoApprove implements ICandidateVideoApprove {
  public userId?: number;
  public firstName?: string;
  public lastName?: string;
  public url?: string;
  public adminUrl?: string;
  public fileName?: string;
  constructor(data?: ICandidateVideoApprove) {
    this.userId = data.userId;
    this.firstName = data.firstName;
    this.lastName = data.lastName;
    this.url = data.url;
    this.adminUrl = data.adminUrl;
    this.fileName = data.fileName;
  }
}

export interface ICandidateVideoApprove {
  userId?: number;
  firstName?: string;
  lastName?: string;
  url?: string;
  adminUrl?: string;
  fileName?: string;
}




export class AdminDashboardData implements IDashboardData {
    public newClients?: BusinessApprove[];
    public newCandidates?: CandidateApprove[];
    public newFiles?: CandidateFileApprove[];
    public newJobs?: BusinessJobsAwaitingApproval[];
    public interviewsSetUpCandidate?: AdminInterviewList[];
    public interviewsSetUpClient?: AdminInterviewList[];
    public interviewsPending?: AdminInterviewList[];
    public awaitingApplicants?: AdminInterviewList[];
    public shortlistApplicants?: AdminInterviewList[];
    public newVideos?: AdminVideoList[];
    constructor(data?: IDashboardData) {
        this.newClients = data.newClients;
        this.newCandidates = data.newCandidates;
        this.newFiles = data.newFiles;
        this.newJobs = data.newJobs;
        this.interviewsSetUpCandidate = data.interviewsSetUpCandidate;
        this.interviewsSetUpClient = data.interviewsSetUpClient;
        this.interviewsPending = data.interviewsPending;
        this.awaitingApplicants = data.awaitingApplicants;
        this.shortlistApplicants = data.shortlistApplicants;
        this.newVideos = data.newVideos;
    }
}

export interface IDashboardData {
    newClients?: BusinessApprove[];
    newCandidates?: CandidateApprove[];
    newFiles?: CandidateFileApprove[];
    newJobs?: BusinessJobsAwaitingApproval[];
    interviewsSetUpCandidate?: AdminInterviewList[];
    interviewsSetUpClient?: AdminInterviewList[];
    interviewsPending?: AdminInterviewList[];
    awaitingApplicants?: AdminInterviewList[];
    shortlistApplicants?: AdminInterviewList[];
    newVideos?: AdminVideoList[];
}

export class AdminProfile implements IAdminProfile {
  public notification?: {
    candidateDeactivate?: number;
    candidateFile?: number;
    candidateRequestVideo?: number;
    candidateSign?: number;
    clientSign?: number;
    interviewSetUp?: number;
    jobChange?: number;
    jobNew?: number;
    notifyEmail?: boolean;
  };
  public profile?: {
    firstName?: string,
    lastName?: string,
    phone?: string,
    email?: string
  };
  constructor(data?: IAdminProfile) {
      this.notification = data.notification;
      this.profile = data.profile;
  }
}

export interface IAdminProfile {
  notification?: {
    candidateDeactivate?: number;
    candidateFile?: number;
    candidateRequestVideo?: number;
    candidateSign?: number;
    clientSign?: number;
    interviewSetUp?: number;
    jobChange?: number;
    jobNew?: number;
    notifyEmail?: boolean;
  };
  profile?: {
    firstName?: string,
    lastName?: string,
    phone?: string,
    email?: string
  };
}

export class AdminInterviewList implements IAdminInterviewList {
  id?: number;
  candidateID?: number;
  candidateFirstName?: string;
  candidateLastName?: string;
  clientID?: number;
  companyName?: string;
  jobTitle?: string;
  created?: Date;
  status?: string;
  enabled?: boolean;
  constructor(data?: IAdminInterviewList) {
    this.id = data.id;
    this.candidateID = data.candidateID;
    this.candidateFirstName = data.candidateFirstName;
    this.candidateLastName = data.candidateLastName;
    this.clientID = data.clientID;
    this.companyName = data.companyName;
    this.jobTitle = data.jobTitle;
    this.created = data.created;
    this.status = data.status;
    this.enabled = data.enabled;
  }
}
export interface IAdminInterviewList {
  id?: number;
  candidateID?: number;
  candidateFirstName?: string;
  candidateLastName?: string;
  clientID?: number;
  companyName?: string;
  jobTitle?: string;
  created?: Date;
  status?: string;
  enabled?: boolean;
}

export class AdminVideoList implements IAdminVideoList {
  userId?: number;
  firstName?: string;
  lastName?: string;
  url?: string;
  fileName?: string;
  constructor(data?: IAdminVideoList) {
    this.userId = data.userId;
    this.firstName = data.firstName;
    this.lastName = data.lastName;
    this.url = data.url;
    this.fileName = data.fileName;
  }
}
export interface IAdminVideoList {
  userId?: number;
  firstName?: string;
  lastName?: string;
  url?: string;
  fileName?: string;
}



export class InterfacePagination implements IInterfacePagination {
  public current_page_number?: number;
  public total_count?: number;
  constructor(data?: IInterfacePagination) {
    this.current_page_number = data.current_page_number;
    this.total_count = data.total_count;
  }
}
export interface IInterfacePagination {
  current_page_number?: number;
  total_count?: number;
}

export class AdminInterviewItemList implements IAdminInterviewItemList {
  public items?: AdminInterviewList[];
  public pagination?: {
    current_page_number?: number;
    total_count?: number;
  };
  constructor(data?: IAdminInterviewItemList) {
    this.items = data.items;
    this.pagination = data.pagination;
  }
}
export interface IAdminInterviewItemList {
  items?: AdminInterviewList[];
  pagination?: {
    current_page_number?: number;
    total_count?: number;
  };
}

export class EditAdmin implements IEditAdmin {
  id?: number;
  firstName?: string;
  lastName?: string;
  phone?: string;
  email?: string;
  roles?: string[];
  constructor(data?: IEditAdmin) {
    this.id = data.id;
    this.firstName = data.firstName;
    this.lastName = data.lastName;
    this.phone = data.phone;
    this.email = data.email;
    this.roles = data.roles;
  }
}
export interface IEditAdmin {
  id?: number;
  firstName?: string;
  lastName?: string;
  phone?: string;
  email?: string;
  roles?: string[];
}


export class AdminBusinessUserProfile implements IAdminBusinessUserProfile {
  public user?: AdminBusinessUserProfileUser;
  public company?: AdminBusinessUserProfileCompany;
  constructor(data?: IAdminBusinessUserProfile) {
    this.user = data.user;
    this.company = data.company;
  };
}

export interface IAdminBusinessUserProfile {
  user?: AdminBusinessUserProfileUser;
  company?: AdminBusinessUserProfileCompany;
}


export class AdminBusinessUserProfileUser implements IAdminBusinessUserProfileUser {
  public id?: number;
  public firstName?: string;
  public lastName?: string;
  public jobTitle?: string;
  public phone?: string;
  public email?: string;
  constructor(data?: IAdminBusinessUserProfileUser) {
    this.id = data.id;
    this.firstName = data.firstName;
    this.lastName = data.lastName;
    this.jobTitle = data.jobTitle;
    this.phone = data.phone;
    this.email = data.email;
  };
}

export interface IAdminBusinessUserProfileUser {
  id?: number;
  firstName?: string;
  lastName?: string;
  jobTitle?: string;
  phone?: string;
  email?: string;
}

export class AdminBusinessUserProfileCompany implements IAdminBusinessUserProfileCompany {
  public name?: string;
  public address?: string;
  public addressCountry: string;
  public addressState: string;
  public addressZipCode: string;
  public addressCity: string;
  public addressSuburb: string;
  public addressStreet: string;
  public addressStreetNumber: string;
  public addressBuildName: string;
  public addressUnit: string;
  public companySize?: number;
  public jse?: boolean;
  public industry?: number;
  public description?: string;
  constructor(data?: IAdminBusinessUserProfileCompany) {
    this.name = data.name;
    this.address = data.address;
    this.addressCountry = data.addressCountry;
    this.addressState = data.addressState;
    this.addressZipCode = data.addressZipCode;
    this.addressCity = data.addressCity;
    this.addressSuburb = data.addressSuburb;
    this.addressStreet = data.addressStreet;
    this.addressStreetNumber = data.addressStreetNumber;
    this.addressBuildName = data.addressBuildName;
    this.addressUnit = data.addressUnit;
    this.companySize = data.companySize;
    this.jse = data.jse;
    this.industry = data.industry;
    this.description = data.description;
  };
}

export interface IAdminBusinessUserProfileCompany {
  name?: string;
  address?: string;
  addressCountry?: string;
  addressState?: string;
  addressZipCode?: string;
  addressCity?: string;
  addressSuburb?: string;
  addressStreet?: string;
  addressStreetNumber?: string;
  addressBuildName?: string;
  addressUnit?: string;
  companySize?: number;
  jse?: boolean;
  industry?: number;
  description?: string;
}

export class AdminBusinessNotification implements IAdminBusinessNotification {
  public notifyEmail?: boolean;
  public newCandidateStatus?: boolean;
  public jobApproveStatus?: boolean;
  public jobDeclineStatus?: boolean;
  public candidateApplicantStatus?: boolean;
  public candidateDeclineStatus?: boolean;
  public newCandidate?: number;
  public jobApprove?: number;
  public jobDecline?: number;
  public candidateApplicant?: number;
  public candidateDecline?: number;
  constructor(data?: IAdminBusinessNotification) {
    this.notifyEmail = data.notifyEmail;
    this.newCandidateStatus = data.newCandidateStatus;
    this.jobApproveStatus = data.jobApproveStatus;
    this.jobDeclineStatus = data.jobDeclineStatus;
    this.candidateApplicantStatus = data.candidateApplicantStatus;
    this.candidateDeclineStatus = data.candidateDeclineStatus;
    this.newCandidate = data.newCandidate;
    this.jobApprove = data.jobApprove;
    this.jobDecline = data.jobDecline;
    this.candidateApplicant = data.candidateApplicant;
    this.candidateDecline = data.candidateDecline;
  }
}

export interface IAdminBusinessNotification {
  notifyEmail?: boolean;
  newCandidateStatus?: boolean;
  jobApproveStatus?: boolean;
  jobDeclineStatus?: boolean;
  candidateApplicantStatus?: boolean;
  candidateDeclineStatus?: boolean;
  newCandidate?: number;
  jobApprove?: number;
  jobDecline?: number;
  candidateApplicant?: number;
  candidateDecline?: number;
}

export class AdminBusinessAccount implements IAdminBusinessAccount {
  public profile?: AdminBusinessUserProfile;
  public notification?: AdminBusinessNotification;
  constructor(data?: IAdminBusinessAccount) {
    this.profile = data.profile;
    this.notification = data.notification;
  }
}

export interface IAdminBusinessAccount {
  profile?: AdminBusinessUserProfile;
  notification?: AdminBusinessNotification;
}

export class BusinessApproveList implements IBusinessApproveList {
  public items?: BusinessApprove[];
  public pagination?: InterfacePagination;
  constructor(data?: IBusinessApproveList) {
    this.items = data.items;
    this.pagination = data.pagination;
  }
}

export interface IBusinessApproveList {
  items?: BusinessApprove[];
  pagination?: InterfacePagination;
}

export class BusinessJobsAwaitingApprovalList implements IBusinessJobsAwaitingApprovalList {
  public items?: BusinessJobsAwaitingApproval[];
  public pagination?: InterfacePagination;
  constructor(data?: IBusinessJobsAwaitingApprovalList) {
    this.items = data.items;
    this.pagination = data.pagination;
  }
}

export interface IBusinessJobsAwaitingApprovalList {
  items?: BusinessJobsAwaitingApproval[];
  pagination?: InterfacePagination;
}



export class AdminLogging implements IAdminLogging {
  public id?: number;
  public adminID?: number;
  public firstName?: string;
  public lastName?: string;
  public type?: number;
  public action?: string;
  public itemID?: number;
  public created?: Date;
  constructor(data?: IAdminLogging) {
    this.id = data.id;
    this.adminID = data.adminID;
    this.firstName = data.firstName;
    this.lastName = data.lastName;
    this.type = data.type;
    this.action = data.action;
    this.itemID = data.itemID;
    this.created = data.created;
  };
}

export interface IAdminLogging {
  id?: number;
  adminID?: number;
  firstName?: string;
  lastName?: string;
  type?: number;
  action?: string;
  itemID?: number;
  created?: Date;
}

export class AdminLoggingList implements IAdminLoggingList {
  public items: AdminLogging[];
  public pagination: InterfacePagination;
  constructor(data?: IAdminLoggingList) {
    this.items = data.items;
    this.pagination = data.pagination;
  };
}

export interface IAdminLoggingList {
  items?: AdminLogging[];
  pagination?: InterfacePagination;
}
