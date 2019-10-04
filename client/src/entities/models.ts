/**
 * TODO rename to RegisterBusinessUser
 */
export class BusinessUser implements IBusinessUser {
  public role?: Role;
  public firstName?: string;
  public lastName?: string;
  public email?: string;
  public phone?: string;
  public password?: string;
  public verifyPassword?: string;
  public jobTitle?: string;
  public companyName?: string;
  constructor(data?: IBusinessUser) {
    this.role = data.role;
    this.firstName = data.firstName;
    this.lastName = data.lastName;
    this.email = data.email;
    this.phone = data.phone;
    this.password = data.password;
    this.verifyPassword = data.verifyPassword;
    this.jobTitle = data.jobTitle;
    this.companyName = data.companyName;
  };
}

export interface IBusinessUser {
  role?: Role;
  firstName?: string;
  lastName?: string;
  email?: string;
  phone?: string;
  password?: string;
  verifyPassword?: string;
  jobTitle?: string;
  companyName?: string;
}

export class CandidateUser implements ICandidateUser {
  public role?: Role;
  public firstName?: string;
  public lastName?: string;
  public email?: string;
  public phone?: string;
  public password?: string;
  public verifyPassword?: string;
  public idNumber?: string;
  //public boards?: number;

  constructor(data?: ICandidateUser) {
    this.role = data.role;
    this.firstName = data.firstName;
    this.lastName = data.lastName;
    this.email = data.email;
    this.phone = data.phone;
    this.password = data.password;
    this.verifyPassword = data.verifyPassword;
    this.idNumber = data.idNumber;
    //this.boards = data.boards;
  };
}

export interface ICandidateUser {
  role?: Role;
  firstName?: string;
  lastName?: string;
  email?: string;
  phone?: string;
  password?: string;
  verifyPassword?: string;
  //boards?: number;
  idNumber?: string;
}

export interface IAccessToken {
  access_token?: string;
  expires_in?: string;
  refresh_token?: string;
  role?: Role;
  id?: string;
}

export enum Role {
  clientRole = 'ROLE_CLIENT',
  candidateRole = 'ROLE_CANDIDATE',
  adminRole = 'ROLE_ADMIN',
  superAdminRole = 'ROLE_SUPER_ADMIN'
}

export interface IBusinessJob {
  jobTitle?: string;
  industry?: number;
  companyName?: string;
  companyAddress?: string;
  addressCountry?: string;
  addressState?: string;
  addressZipCode?: string;
  addressCity?: string;
  addressSuburb?: string;
  addressStreet?: string;
  addressStreetNumber?: string;
  addressBuildName?: string;
  addressUnit?: string;
  companyDescription?: string;
  roleDescription?: string;
  closureDate?: string;
  jobClosureDate?: string;
  started?: string;
  filled?: string;
  articlesFirm?: string[];
  gender?: string;
  ethnicity?: string;
  nationality?: number;
  video?: number;
  availability?: number;
  qualification?: number;
  location?: string;
  postArticles?: number;
  salaryRange?: number;
}


export class BusinessJob implements IBusinessJob {
  jobTitle?: string;
  industry?: number;
  companyName?: string;
  companyAddress?: string;
  addressCountry?: string;
  addressState?: string;
  addressZipCode?: string;
  addressCity?: string;
  addressSuburb?: string;
  addressStreet?: string;
  addressStreetNumber?: string;
  addressBuildName?: string;
  addressUnit?: string;
  companyDescription?: string;
  roleDescription?: string;
  closureDate?: string;
  jobClosureDate?: string;
  started?: string;
  filled?: string;
  articlesFirm?: string[];
  gender?: string;
  ethnicity?: string;
  nationality?: number;
  video?: number;
  availability?: number;
  qualification?: number;
  location?: string;
  postArticles?: number;
  salaryRange?: number;

  constructor(data?: IBusinessJob) {
    this.jobTitle = data.jobTitle;
    this.industry = data.industry;
    this.companyName = data.companyName;
    this.companyAddress = data.companyAddress;
    this.addressCountry = data.addressCountry;
    this.addressState = data.addressState;
    this.addressZipCode = data.addressZipCode;
    this.addressCity = data.addressCity;
    this.addressSuburb = data.addressSuburb;
    this.addressStreet = data.addressStreet;
    this.addressStreetNumber = data.addressStreetNumber;
    this.addressBuildName = data.addressBuildName;
    this.addressUnit = data.addressUnit;
    this.companyDescription = data.companyDescription;
    this.roleDescription = data.roleDescription;
    this.closureDate = data.closureDate;
    this.jobClosureDate = data.jobClosureDate;
    this.started = data.started;
    this.filled = data.filled;
    this.articlesFirm = data.articlesFirm;
    this.gender = data.gender;
    this.ethnicity = data.ethnicity;
    this.nationality = data.nationality;
    this.video = data.video;
    this.availability = data.availability;
    this.qualification = data.qualification;
    this.location = data.location;
    this.postArticles = data.postArticles;
    this.salaryRange = data.salaryRange;
  }
}

export interface IBusinessListJob {
  id?: number;
  jobTitle?: string;
  jobAddress?: string;
  jobCreated?: string;
  jobClosure?: string;
  approve?: boolean;
  status?: boolean;
  awaitingCount?: string;
  shortListCount?: string;
  approvedCount?: string;
}

export class BusinessListJob implements IBusinessListJob {
  id?: number;
  jobTitle?: string;
  jobAddress?: string;
  jobCreated?: string;
  jobClosure?: string;
  approve?: boolean;
  status?: boolean;
  awaitingCount?: string;
  shortListCount?: string;
  approvedCount?: string;

  constructor(data?: IBusinessListJob) {
    this.id = data.id;
    this.jobTitle = data.jobTitle;
    this.jobAddress = data.jobAddress;
    this.jobCreated = data.jobCreated;
    this.jobClosure = data.jobClosure;
    this.approve = data.approve;
    this.status = data.status;
    this.awaitingCount = data.awaitingCount;
    this.shortListCount = data.shortListCount;
    this.approvedCount = data.approvedCount;
  }
}

export interface IBusinessJobFullDetails {
  addressBuildName?: string;
  addressCity?: string;
  addressSuburb?: string;
  addressCountry?: string;
  addressState?: string;
  addressStreet?: string;
  addressStreetNumber?: string;
  addressUnit?: string;
  addressZipCode?: string;
  approve?: boolean;
  approvedCount?: string;
  articlesFirm?: string[];
  availability?: any;
  awaitingCount?: string;
  closureDate?: string;
  jobClosureDate?: string;
  started?: string;
  filled?: string;
  companyAddress?: string;
  companyDescription?: string;
  companyName?: string;
  createdDate?: string;
  ethnicity?: string;
  gender?: string;
  id?: number;
  industry?: number;
  jobTitle?: string;
  location?: string;
  nationality?: number;
  postArticles?: number;
  qualification?: number;
  roleDescription?: string;
  salaryRange?: number;
  shortListCount?: string;
  status?: boolean;
  salaryFrom?: any;
  salaryTo?: any;
  eligibility?: any;
  highestQualification?: any;
  video?: number;
  candidateCount?: number;
  assessment?: string;
  spec?: {
    url?: string;
    adminUrl?: string;
    name?: string;
    size?: number;
    approved?: boolean;
  };
}

export class BusinessJobFullDetails implements IBusinessJobFullDetails {
  addressBuildName?: string;
  addressCity?: string;
  addressSuburb?: string;
  addressCountry?: string;
  addressState?: string;
  addressStreet?: string;
  addressStreetNumber?: string;
  addressUnit?: string;
  addressZipCode?: string;
  approve?: boolean;
  approvedCount?: string;
  articlesFirm?: string[];
  availability?: any;
  awaitingCount?: string;
  closureDate?: string;
  jobClosureDate?: string;
  started?: string;
  filled?: string;
  companyAddress?: string;
  companyDescription?: string;
  companyName?: string;
  createdDate?: string;
  ethnicity?: string;
  gender?: string;
  id?: number;
  industry?: number;
  jobTitle?: string;
  location?: string;
  nationality?: number;
  postArticles?: number;
  qualification?: number;
  roleDescription?: string;
  salaryRange?: number;
  shortListCount?: string;
  status?: boolean;
  video?: number;
  candidateCount?: number;
  assessment?: string;

  constructor(data?: IBusinessJobFullDetails) {
    this.addressBuildName = data.addressBuildName;
    this.addressCity = data.addressCity;
    this.addressSuburb = data.addressSuburb;
    this.addressCountry = data.addressCountry;
    this.addressState = data.addressState;
    this.addressStreet = data.addressStreet;
    this.addressStreetNumber = data.addressStreetNumber;
    this.addressUnit = data.addressUnit;
    this.addressZipCode = data.addressZipCode;
    this.approve = data.approve;
    this.approvedCount = data.approvedCount;
    this.articlesFirm = data.articlesFirm;
    this.availability = data.availability;
    this.awaitingCount = data.awaitingCount;
    this.closureDate = data.closureDate;
    this.jobClosureDate = data.jobClosureDate;
    this.started = data.started;
    this.filled = data.filled;
    this.companyAddress = data.companyAddress;
    this.companyDescription = data.companyDescription;
    this.companyName = data.companyName;
    this.createdDate = data.createdDate;
    this.ethnicity = data.ethnicity;
    this.gender = data.gender;
    this.id = data.id;
    this.industry = data.industry;
    this.jobTitle = data.jobTitle;
    this.location = data.location;
    this.nationality = data.nationality;
    this.postArticles = data.postArticles;
    this.qualification = data.qualification;
    this.roleDescription = data.roleDescription;
    this.salaryRange = data.salaryRange;
    this.shortListCount = data.shortListCount;
    this.status = data.status;
    this.video = data.video;
    this.candidateCount = data.candidateCount;
    this.assessment = data.assessment;
  }
}

export interface IBusinessJobsAwaitingApproval {
  companyName?: string;
  email?: string;
  firstName?: string;
  id?: number;
  jobDate?: string;
  jobTitle?: string;
  lastName?: string;
  phone?: string;
}

export class BusinessJobsAwaitingApproval implements IBusinessJobsAwaitingApproval {
  companyName?: string;
  email?: string;
  firstName?: string;
  id?: number;
  jobDate?: string;
  jobTitle?: string;
  lastName?: string;
  phone?: string;
  constructor(data?: any) {
    this.companyName = data.companyName;
    this.email = data.email;
    this.firstName = data.firstName;
    this.id = data.id;
    this.jobDate = data.jobDate;
    this.jobTitle = data.jobTitle;
    this.lastName = data.lastName;
    this.phone = data.phone;
  }
}

export interface IBusinessAdminJobFullDetails {
  addressBuildName?: string;
  addressCity?: string;
  addressSuburb?: string;
  addressCountry?: string;
  addressState?: string;
  addressStreet?: string;
  addressStreetNumber?: string;
  addressUnit?: string;
  addressZipCode?: string;
  approve?: boolean;
  approvedCount?: string;
  articlesFirm?: string[];
  availability?: any;
  awaitingCount?: string;
  closureDate?: string;
  jobClosureDate?: string;
  started?: string;
  filled?: string;
  clientID?:  number;
  companyAddress?: string;
  companyDescription?: string;
  companyDescriptionChange?: string;
  companyName?: string;
  createdDate?: string;
  ethnicity?: string;
  gender?: string;
  id?: number;
  industry?: number;
  jobTitle?: string;
  location?: string;
  nationality?: number;
  postArticles?: number;
  qualification?: number;
  roleDescription?: string;
  roleDescriptionChange?: string;
  salaryRange?: number;
  shortListCount?: string;
  status?: boolean;
  video?: number;
}

export class BusinessAdminJobFullDetails implements IBusinessAdminJobFullDetails {
  addressBuildName?: string;
  addressCity?: string;
  addressSuburb?: string;
  addressCountry?: string;
  addressState?: string;
  addressStreet?: string;
  addressStreetNumber?: string;
  addressUnit?: string;
  addressZipCode?: string;
  approve?: boolean;
  approvedCount?: string;
  articlesFirm?: string[];
  availability?: any;
  awaitingCount?: string;
  closureDate?: string;
  jobClosureDate?: string;
  started?: string;
  filled?: string;
  clientID?: number;
  companyAddress?: string;
  companyDescription?: string;
  companyDescriptionChange?: string;
  companyName?: string;
  createdDate?: string;
  ethnicity?: string;
  gender?: string;
  id?: number;
  industry?: number;
  jobTitle?: string;
  location?: string;
  nationality?: number;
  postArticles?: number;
  qualification?: number;
  roleDescription?: string;
  roleDescriptionChange?: string;
  salaryRange?: number;
  shortListCount?: string;
  status?: boolean;
  video?: number;

  constructor(data?: IBusinessAdminJobFullDetails) {
    this.addressBuildName = data.addressBuildName;
    this.addressCity = data.addressCity;
    this.addressSuburb = data.addressSuburb;
    this.addressCountry = data.addressCountry;
    this.addressState = data.addressState;
    this.addressStreet = data.addressStreet;
    this.addressStreetNumber = data.addressStreetNumber;
    this.addressUnit = data.addressUnit;
    this.addressZipCode = data.addressZipCode;
    this.approve = data.approve;
    this.approvedCount = data.approvedCount;
    this.articlesFirm = data.articlesFirm;
    this.availability = data.availability;
    this.awaitingCount = data.awaitingCount;
    this.closureDate = data.closureDate;
    this.jobClosureDate = data.jobClosureDate;
    this.started = data.started;
    this.filled = data.filled;
    this.clientID =  data.clientID;
    this.companyAddress = data.companyAddress;
    this.companyDescription = data.companyDescription;
    this.companyDescriptionChange = data.companyDescriptionChange;
    this.companyName = data.companyName;
    this.createdDate = data.createdDate;
    this.ethnicity = data.ethnicity;
    this.gender = data.gender;
    this.id = data.id;
    this.industry = data.industry;
    this.jobTitle = data.jobTitle;
    this.location = data.location;
    this.nationality = data.nationality;
    this.postArticles = data.postArticles;
    this.qualification = data.qualification;
    this.roleDescription = data.roleDescription;
    this.roleDescriptionChange = data.roleDescriptionChange;
    this.salaryRange = data.salaryRange;
    this.shortListCount = data.shortListCount;
    this.status = data.status;
    this.video = data.video;
  }
}

export class CandidateReferences implements ICandidateReferences {
  public company?: string;
  public role?: string;
  public specialization?: string;
  public startDate?: string;
  public endDate?: string;
  public isReference?: boolean;
  public managerFirstName?: string;
  public managerLastName?: string;
  public managerTitle?: string;
  public managerEmail?: string;
  public managerComment?: string;
  public permission?: boolean;
  constructor(data?: ICandidateReferences) {
    this.company = data.company;
    this.role = data.role;
    this.specialization = data.specialization;
    this.startDate = data.startDate;
    this.endDate = data.endDate;
    this.isReference = data.isReference;
    this.managerFirstName = data.managerFirstName;
    this.managerLastName = data.managerLastName;
    this.managerTitle = data.managerTitle;
    this.managerEmail = data.managerEmail;
    this.managerComment = data.managerComment;
    this.permission = data.permission;
  };
}

export interface ICandidateReferences {
  company?: string;
  role?: string;
  specialization?: string;
  startDate?: string;
  endDate?: string;
  isReference?: boolean;
  managerFirstName?: string;
  managerLastName?: string;
  managerTitle?: string;
  managerEmail?: string;
  managerComment?: string;
  permission?: boolean;
}

export interface IJobCriteria {
  id: number;
  jobTitle: string;
  articlesFirm: string[];
  gender: string;
  ethnicity: string;
  qualification: number;
  nationality: number;
  video: number;
  availability: number;
  location: string;
  postArticles: number;
  salaryRange: number;
}

export class JobCriteria implements IJobCriteria {
  id: number;
  jobTitle: string;
  articlesFirm: string[];
  gender: string;
  ethnicity: string;
  qualification: number;
  nationality: number;
  video: number;
  availability: number;
  location: string;
  postArticles: number;
  salaryRange: number;
  constructor(data?: IJobCriteria) {
    this.id = data.id;
    this.jobTitle = data.jobTitle;
    this.articlesFirm = data.articlesFirm;
    this.gender = data.gender;
    this.ethnicity = data.ethnicity;
    this.qualification = data.qualification;
    this.nationality = data.nationality;
    this.video = data.video;
    this.availability = data.availability;
    this.location = data.location;
    this.postArticles = data.postArticles;
    this.salaryRange = data.salaryRange;
  }
}

export interface ICandidateByCriteria {
  id: number;
  firstName: string;
  lastName: string;
  articlesFirm: string;
  boards: number;
  picture: [{
    url: string;
    name: string;
    size: number;
    approved: boolean;
  }];
  availability: boolean;
  availabilityPeriod: number;
  dateAvailability: string;
}

export class CandidateByCriteria implements ICandidateByCriteria {
  id: number;
  firstName: string;
  lastName: string;
  articlesFirm: string;
  boards: number;
  picture: [{
    url: string;
    name: string;
    size: number;
    approved: boolean;
  }];
  availability: boolean;
  availabilityPeriod: number;
  dateAvailability: string;
  constructor(data?: ICandidateByCriteria) {
    this.id = data.id;
    this.firstName = data.firstName;
    this.lastName = data.lastName;
    this.articlesFirm = data.articlesFirm;
    this.boards = data.boards;
    this.picture = data.picture;
    this.availability = data.availability;
    this.availabilityPeriod = data.availabilityPeriod;
    this.dateAvailability = data.dateAvailability;
  }
}

export interface IBusinessCandidate {
  achievements?: [{
    id: number;
    description: string;
  }];
  applicable?: boolean;
  applicant?: number;
  details?: {
    academicTranscript: any;
    availability: boolean;
    availabilityPeriod: number;
    beeCheck: any;
    certificateOfQualification: any;
    citiesWorking: string[];
    copyOfID: [{
      adminURL: string;
      approved: boolean;
      name: string;
      size: number;
      time: number;
      url: string;
    }];
    credit: boolean;
    creditCheck: [{
      url: string;
      name: string;
      size: number;
      approved: boolean;
    }];
    creditDescription: string;
    criminal: boolean;
    criminalDescription: string;
    cv: [{
      url: string;
      name: string;
      size: number;
      approved: boolean;
    }];
    dateAvailability: string;
    dateOfBirth: string;
    driverLicense: boolean;
    driverNumber: any;
    employed: any;
    employedDate: any;
    englishProficiency: any;
    ethnicity: any;
    firstName: string;
    gender: string;
    homeAddress: string;
    id: number;
    lastName: string;
    matricCertificate: [{
      url: string;
      name: string;
      size: number;
      approved: boolean;
    }],
    matricTranscript: [{
      adminUrl: string;
      url: string;
      name: string;
      size: number;
      time: number;
      approved: boolean;
    }];
    mostEmployer: string;
    mostRole: string;
    mostSalary: any;
    nationality: number;
    payslip: any;
    picture: [{
      url: string;
      name: string;
      size: number;
      approved: boolean;
    }];
    salaryPeriod: any;
    specialization: any;
    universityExemption: any;
    video: {
      url: string;
      name: string;
      approved: boolean;
    };
  };
  employments?: Array<any>;
  jobID?: number;
  qualifications?: Array<any>;
  references?: [{
    id: number;
    firstName: string;
    lastName: string;
    company: string;
    role: string;
    email: string;
    comment: string;
    permission: boolean;
  }];
}

export class BusinessCandidate implements IBusinessCandidate {
  achievements?: [{
    id: number;
    description: string;
  }];
  applicable?: boolean;
  applicant?: number;
  details?: {
    academicTranscript: any;
    availability: boolean;
    availabilityPeriod: number;
    beeCheck: any;
    certificateOfQualification: any;
    citiesWorking: string[];
    copyOfID: [{
      adminURL: string;
      approved: boolean;
      name: string;
      size: number;
      time: number;
      url: string;
    }];
    credit: boolean;
    creditCheck: [{
      url: string;
      name: string;
      size: number;
      approved: boolean;
    }];
    creditDescription: string;
    criminal: boolean;
    criminalDescription: string;
    cv: [{
      url: string;
      name: string;
      size: number;
      approved: boolean;
    }];
    dateAvailability: string;
    dateOfBirth: string;
    driverLicense: boolean;
    driverNumber: any;
    employed: any;
    employedDate: any;
    englishProficiency: any;
    ethnicity: any;
    firstName: string;
    gender: string;
    homeAddress: string;
    id: number;
    lastName: string;
    matricCertificate: [{
      url: string;
      name: string;
      size: number;
      approved: boolean;
    }],
    matricTranscript: [{
      adminUrl: string;
      url: string;
      name: string;
      size: number;
      time: number;
      approved: boolean;
    }];
    mostEmployer: string;
    mostRole: string;
    mostSalary: any;
    nationality: number;
    payslip: any;
    picture: [{
      url: string;
      name: string;
      size: number;
      approved: boolean;
    }];
    salaryPeriod: any;
    specialization: any;
    universityExemption: any;
    video: {
      url: string;
      name: string;
      approved: boolean;
    };
  };
  employments?: Array<any>;
  jobID?: number;
  qualifications?: Array<any>;
  references?: [{
    id: number;
    firstName: string;
    lastName: string;
    company: string;
    role: string;
    email: string;
    comment: string;
    permission: boolean;
  }];
  constructor(data?: IBusinessCandidate) {
    this.achievements = data.achievements;
    this.applicable = data.applicable;
    this.applicant = data.applicant;
    this.details = data.details;
    this.employments = data.employments;
    this.jobID = data.jobID;
    this.qualifications = data.qualifications;
    this.references = data.references;
  }
}

export interface IBusinessApplicant {
  candidateID: number;
  firstName: string;
  lastName: string;
  articlesFirm: string;
  picture: [{
    url: string;
    name: string;
    size: string;
    approved: boolean;
  }];
  created: string;
  jobID: number;
  jobTitle: string;
  employer: string;
  role: string;
  applicant: boolean;
  dateAvailability: any;
  availability: boolean;
  availabilityPeriod: any;
}

export class BusinessApplicant implements IBusinessApplicant {
  candidateID: number;
  firstName: string;
  lastName: string;
  articlesFirm: string;
  picture: [{
    url: string;
    name: string;
    size: string;
    approved: boolean;
  }];
  created: string;
  jobID: number;
  jobTitle: string;
  employer: string;
  // salaryRange: number;
  mostSalary: number;
  highestQualification: string;
  role: string;
  applicant: boolean;
  dateAvailability: any;
  availability: boolean;
  availabilityPeriod: any;
  constructor(data?: IBusinessApplicant) {
    this.candidateID = data.candidateID;
    this.firstName = data.firstName;
    this.lastName = data.lastName;
    this.articlesFirm = data.articlesFirm;
    this.picture = data.picture;
    this.created = data.created;
    this.jobID = data.jobID;
    this.jobTitle = data.jobTitle;
    this.employer = data.employer;
    this.role = data.role;
    this.applicant = data.applicant;
    this.dateAvailability = data.dateAvailability;
    this.availability = data.availability;
    this.availabilityPeriod = data.availabilityPeriod;
  }
}

export interface IBusinessApplicantList {
  items: BusinessApplicant[];
  pagination: BusinessApplicantListPagination;
}

export class BusinessApplicantList implements IBusinessApplicantList {
  items: BusinessApplicant[];
  pagination: BusinessApplicantListPagination;
  constructor(data?: IBusinessApplicantList) {
    this.items = data.items;
    this.pagination = data.pagination;
  }
}

export interface IBusinessApplicantListPagination {
  current_page_number?: number;
  total_count?: number;
}

export class BusinessApplicantListPagination implements IBusinessApplicantListPagination {
  public current_page_number?: number;
  public total_count?: number;
  constructor(data?: IBusinessApplicantListPagination) {
    this.current_page_number = data.current_page_number;
    this.total_count = data.total_count;
  }
}

export interface IApplicantsList {
  awaiting: number;
  shortList: number;
  approve: number;
  decline: number;
}

export class ApplicantsList implements IApplicantsList {
  awaiting: number;
  shortList: number;
  approve: number;
  decline: number;
  constructor(data?: IApplicantsList) {
    this.awaiting = data.awaiting;
    this.shortList = data.shortList;
    this.approve = data.approve;
    this.decline = data.decline;
  }
}

export class BusinessDashboard implements IBusinessDashboard {
  firstName: string;
  jobs: [{
    approved: number;
    awaiting: number;
    closureDate: Date;
    id: number;
    jobTitle: string;
    shortlist: number;
  }];
  stats: {
    approved: number;
    awaiting: number;
    shortlist: number;
  };
  totalJobs: number;
  constructor(data?: IBusinessDashboard) {
    this.firstName = data.firstName;
    this.jobs = data.jobs;
    this.stats = data.stats;
    this.totalJobs = data.totalJobs;
  }
}

export interface IBusinessDashboard {
  firstName: string;
  jobs: [{
    approved: number;
    awaiting: number;
    closureDate: Date;
    id: number;
    jobTitle: string;
    shortlist: number;
  }];
  stats: {
    approved: number;
    awaiting: number;
    shortlist: number;
  };
  totalJobs: number;
}

export class OpportunitiesList implements IOpportunitiesList {
  awaiting: CandidateOpportunities[];
  successful: CandidateOpportunities[];
  decline: CandidateOpportunities[];
  constructor(data?: IOpportunitiesList) {
    this.awaiting = data.awaiting;
    this.successful = data.successful;
    this.decline = data.decline;
  }
}

export interface IOpportunitiesList {
  awaiting: CandidateOpportunities[];
  successful: CandidateOpportunities[];
  decline: CandidateOpportunities[];
}

export class OpportunitiesJobsList implements IOpportunitiesJobList {
  candidateAddress?: string;
  new?: CandidateOpportunities[];
  decline?: CandidateOpportunities[];
  expired?: CandidateOpportunities[];
  constructor(data?: IOpportunitiesJobList) {
    this.candidateAddress = data.candidateAddress;
    this.new = data.new;
    this.decline = data.decline;
    this.expired = data.expired;
  }
}

export interface IOpportunitiesJobList {
  candidateAddress?: string;
  new?: CandidateOpportunities[];
  decline?: CandidateOpportunities[];
  expired?: CandidateOpportunities[];
}

export class CandidateOpportunities implements ICandidateOpportunities {
  id?: number;
  jobTitle?: string;
  industry?: string[];
  roleDescription?: string;
  companyAddress?: string;
  addressCity?: string;
  endDate?: Date;
  createdDate?: Date;
  constructor(data?: ICandidateOpportunities) {
    this.addressCity = data.addressCity;
    this.companyAddress = data.companyAddress;
    this.createdDate = data.createdDate;
    this.endDate = data.endDate;
    this.id = data.id;
    this.industry = data.industry;
    this.jobTitle = data.jobTitle;
    this.roleDescription = data.roleDescription;
  }
}

export interface ICandidateOpportunities {
  addressCity?: string;
  companyAddress?: string;
  createdDate?: Date;
  endDate?: Date;
  id?: number;
  industry?: string[];
  jobTitle?: string;
  roleDescription?: string;
}

export class CandidateFindJob implements ICandidateFindJob {
  public companyAddress?: string;
  public createdDate?: Date;
  public endDate?: Date;
  public id?: number;
  public jobTitle?: string;
  public roleDescription?: string;
  constructor(data?: ICandidateFindJob) {
    this.companyAddress = data.companyAddress;
    this.createdDate = data.createdDate;
    this.endDate = data.endDate;
    this.id = data.id;
    this.jobTitle = data.jobTitle;
    this.roleDescription = data.roleDescription;
  }
}

export interface ICandidateFindJob {
  companyAddress?: string;
  createdDate?: Date;
  endDate?: Date;
  id?: number;
  jobTitle?: string;
  roleDescription?: string;
}


export class CandidateDashboard implements ICandidateDashboard {
  application?: ApplicationObject;
  candidateAddress?: string;
  interviewRequest?: CandidateOpportunities[];
  interviewRequestTotal?: number;
  jobAlerts?: CandidateFindJob[];
  jobAlertsTotal?: number;
  stats?: StatsObject;
  constructor(data?: ICandidateDashboard) {
    this.application = data.application;
    this.candidateAddress = data.candidateAddress;
    this.interviewRequest = data.interviewRequest;
    this.interviewRequestTotal = data.interviewRequestTotal;
    this.jobAlerts = data.jobAlerts;
    this.jobAlertsTotal = data.jobAlertsTotal;
    this.stats = data.stats;
  }
}

export interface ICandidateDashboard {
  application?: ApplicationObject;
  candidateAddress?: string;
  interviewRequest?: CandidateInterviewRequest[];
  interviewRequestTotal?: number;
  jobAlerts?: CandidateOpportunities[];
  jobAlertsTotal?: number;
  stats?: StatsObject;
}

export class CandidateInterviewRequest implements ICandidateInterviewRequest {
  public jobID?: number;
  public jobTitle?: string;
  public industry?: string[];
  public roleDescription?: string;
  public companyAddress?: string;
  public addressCity?: string;
  public created?: Date;
  public status?: number;
  constructor(data?: ICandidateInterviewRequest) {
    this.jobID = data.jobID;
    this.jobTitle = data.jobTitle;
    this.industry = data.industry;
    this.roleDescription = data.roleDescription;
    this.companyAddress = data.companyAddress;
    this.addressCity = data.addressCity;
    this.created = data.created;
    this.status = data.status;
  }
}

export interface ICandidateInterviewRequest {
  jobID?: number;
  jobTitle?: string;
  industry?: string[];
  roleDescription?: string;
  companyAddress?: string;
  addressCity?: string;
  created?: Date;
  status?: number;
}

export class ApplicationObject implements IApplicationObject {
  awaiting?: number;
  successful?: number;
  declined?: number;
  unsuccessful?: number;
  missed?: number;
  constructor(data?: IApplicationObject) {
    this.awaiting = data.awaiting;
    this.successful = data.declined;
    this.declined = data.declined;
    this.unsuccessful = data.unsuccessful;
    this.missed = data.missed;
  }
}

export interface IApplicationObject {
  awaiting?: number;
  successful?: number;
  declined?: number;
  unsuccessful?: number;
  missed?: number;
}

export class StatsObject implements IStatsObject {
  view?: number;
  unique?: number;
  play?: number;
  constructor(data?: IStatsObject) {
    this.view = data.view;
    this.unique = data.unique;
    this.play = data.play;
  }
}
export interface IStatsObject {
  view?: number;
  unique?: number;
  play?: number;
}

export class CandidateJobPopup implements ICandidateJobPopup {
  public addressCity?: string;
  public candidateAddress?: string;
  public clientID?: number;
  public companyAddress?: string;
  public companyDescription?: string;
  public createdDate?: Date;
  public startedDate?: Date;
  public endDate?: Date;
  public id?: number;
  public industry?: number;
  public jobTitle?: string;
  public roleDescription?: string;
  public status?: number;
  public spec?: {
    url?: string;
    adminUrl?: string;
    name?: string;
    size?: number,
    approved?: boolean
  };
  constructor(data?: ICandidateJobPopup) {
    this.addressCity = data.addressCity;
    this.candidateAddress = data.candidateAddress;
    this.clientID = data.clientID;
    this.companyAddress = data.companyAddress;
    this.companyDescription = data.companyDescription;
    this.createdDate = data.createdDate;
    this.startedDate = data.startedDate;
    this.endDate = data.endDate;
    this.id = data.id;
    this.industry = data.industry;
    this.jobTitle = data.jobTitle;
    this.roleDescription = data.roleDescription;
    this.status = data.status;
    this.spec = data.spec;
  }
}
export interface ICandidateJobPopup {
  addressCity?: string;
  candidateAddress?: string;
  clientID?: number;
  companyAddress?: string;
  companyDescription?: string;
  createdDate?: Date;
  startedDate?: Date;
  endDate?: Date;
  id?: number;
  industry?: number;
  jobTitle?: string;
  roleDescription?: string;
  spec?: {
    url?: string;
    adminUrl?: string;
    name?: string;
    size?: number,
    approved?: boolean
  };
  status?: number;
}

/*
* awaitingApplicants: 1
 candidateAll: 101
 candidateFileNew: 6
 candidateNew: 12
 candidateVideoNew: 2
 clientAll: 72
 clientNew: 1
 interviewAll: 16
 interviewPending: 5
 interviewPlaced: 6
 interviewSetUp: 5
 jobAll: 43
 jobNew: 10
 shortlistApplicants: 4*/
export class AdminBadges implements IAdminBadges {
  public awaitingApplicants?: number;
  public shortlistApplicants?: number;
  public clientNew?: number;
  public clientAll?: number;
  public jobNew?: number;
  public jobAll?: number;
  public candidateNew?: number;
  public candidateFileNew?: number;
  public candidateVideoNew?: number;
  public candidateAll?: number;
  public interviewAll?: number;
  public interviewSetUp?: number;
  public interviewPending?: number;
  public interviewPlaced?: number;
  public clientFiles?: number;
  constructor(data?: IAdminBadges) {
    this.awaitingApplicants = (data.awaitingApplicants) ? data.awaitingApplicants : 0;
    this.shortlistApplicants = (data.shortlistApplicants) ? data.shortlistApplicants : 0;
    this.clientNew = (data.clientNew) ? data.clientNew : 0;
    this.clientAll = (data.clientAll) ? data.clientAll : 0;
    this.jobNew = (data.jobNew) ? data.jobNew : 0;
    this.jobAll = (data.jobAll) ? data.jobAll : 0;
    this.candidateNew = (data.candidateNew) ? data.candidateNew : 0;
    this.candidateFileNew = (data.candidateFileNew) ? data.candidateFileNew : 0;
    this.candidateVideoNew = (data.candidateVideoNew) ? data.candidateVideoNew : 0;
    this.candidateAll = (data.candidateAll) ? data.candidateAll : 0;
    this.interviewAll = (data.interviewAll) ? data.interviewAll : 0;
    this.interviewSetUp = (data.interviewSetUp) ? data.interviewSetUp : 0;
    this.interviewPending = (data.interviewPending) ? data.interviewPending : 0;
    this.interviewPlaced = (data.interviewPlaced) ? data.interviewPlaced : 0;
    this.clientFiles = (data.clientFiles) ? data.clientFiles : 0;
  }
}
export interface IAdminBadges {
  awaitingApplicants?: number;
  shortlistApplicants?: number;
  clientNew?: number;
  clientAll?: number;
  jobNew?: number;
  jobAll?: number;
  candidateNew?: number;
  candidateFileNew?: number;
  candidateVideoNew?: number;
  candidateAll?: number;
  interviewAll?: number;
  interviewSetUp?: number;
  interviewPending?: number;
  interviewPlaced?: number;
  clientFiles?: number;
}

export class CandidateBadges implements ICandidateBadges {
  public jobAlertsNew?: number;
  public jobAlertsDeclined?: number;
  public jobAlertsExpired?: number;
  public applicantAwaiting?: number;
  public applicantApproved?: number;
  public applicantDeclined?: number;
  public interviewRequest?: number;
  constructor(data?: ICandidateBadges) {
    this.jobAlertsNew = (data.jobAlertsNew) ? data.jobAlertsNew : 0;
    this.jobAlertsDeclined = (data.jobAlertsDeclined) ? data.jobAlertsDeclined : 0;
    this.jobAlertsExpired = (data.jobAlertsExpired) ? data.jobAlertsExpired : 0;
    this.applicantAwaiting = (data.applicantAwaiting) ? data.applicantAwaiting : 0;
    this.applicantApproved = (data.applicantApproved) ? data.applicantApproved : 0;
    this.applicantDeclined = (data.applicantDeclined) ? data.applicantDeclined : 0;
    this.interviewRequest = (data.interviewRequest) ? data.interviewRequest : 0;
  }
}
export interface ICandidateBadges {
  jobAlertsNew?: number;
  jobAlertsDeclined?: number;
  jobAlertsExpired?: number;
  applicantAwaiting?: number;
  applicantApproved?: number;
  applicantDeclined?: number;
  interviewRequest?: number;
}

export class BusinessBadges implements IBusinessBadges {
  public applicantAll?: number;
  public applicantAwaiting?: number;
  public applicantShortlist?: number;
  public applicantApprove?: number;
  public applicantDecline?: number;
  public jobAwaiting?: number;
  public jobApproved?: number;
  public jobOld?: number;
  constructor(data?: IBusinessBadges) {
    this.applicantAll = (data.applicantAll) ? data.applicantAll : 0;
    this.applicantAwaiting = (data.applicantAwaiting) ? data.applicantAwaiting : 0;
    this.applicantShortlist = (data.applicantShortlist) ? data.applicantShortlist : 0;
    this.applicantApprove = (data.applicantApprove) ? data.applicantApprove : 0;
    this.applicantDecline = (data.applicantDecline) ? data.applicantDecline : 0;
    this.jobAwaiting = (data.jobAwaiting) ? data.jobAwaiting : 0;
    this.jobApproved = (data.jobApproved) ? data.jobApproved : 0;
    this.jobOld = (data.jobOld) ? data.jobOld : 0;
  }
}
export interface IBusinessBadges {
  applicantAll?: number;
  applicantAwaiting?: number;
  applicantShortlist?: number;
  applicantApprove?: number;
  applicantDecline?: number;
  jobAwaiting?: number;
  jobApproved?: number;
  jobOld?: number;
}
