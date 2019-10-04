import 'rxjs/add/observable/fromPromise';
import 'rxjs/add/observable/throw';
import 'rxjs/add/operator/catch';
import { Injectable } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { AdminBusinessAccount, AdminBusinessProfile } from '../../entities/models-admin';
import { SettingsApiService } from './settings-api.service';
import { AuthService } from './auth.service';
import {
  ApplicantsList, BusinessApplicant, BusinessApplicantList, BusinessCandidate,
  BusinessJob
} from '../../entities/models';
import { Router } from '@angular/router';

@Injectable()
export class BusinessService extends SettingsApiService{

  private _limit = '50';

    constructor(
        protected readonly _http: HttpClient,
        protected readonly _authService: AuthService,
        protected readonly _router: Router
    ) {
        super(_http, _authService, _router);
    }

  /**
   * Get business candidate count according to filter
   * @param search
   * @param data
   * @returns {Promise<any|Object>}
   */
  public async getBusinessCandidatesCount(search, data): Promise<any> {
    const headers = await this.createAuthorizationHeader();
    let params = new HttpParams();
    params = params.append('search', search);
    params = params.append('gender', data.gender);
    params = params.append('ethnicity', data.ethnicity);
    params = params.append('location', data.location);
    params = params.append('availability', data.availability);
    params = params.append('video', data.video);
    params = params.append('highestQualification', data.highestQualification);
    params = params.append('field', data.field);
    params = params.append('monthSalaryFrom', data.monthSalaryFrom);
    params = params.append('monthSalaryTo', data.monthSalaryTo);
    params = params.append('eligibility', data.eligibility);
    params = params.append('yearsOfWorkExperience', data.yearsOfWorkExperience);

    return this._http.get<any>('/api/business/candidate/count', { params: params, headers: headers['headers'] })
      .toPromise()
      .catch(this.handleError);
  }

  /**
   * gets all candidates matching criteria
   * @param {any} search
   * @param {any} articlesFirm
   * @param {any} gender
   * @param {any} qualification
   * @param {any} nationality
   * @param {any} ethnicity
   * @param {any} video
   * @param {any} location
   * @param {any} availability
   * @param {number} page
   * @param {number} articlesCompletedStart
   * @param {number} articlesCompletedEnd
   * @returns {Promise<any>}
   */
  public async getBusinessCandidatesMatchingCriteria(search, articlesFirm = null, gender  = null, qualification = null, nationality = null,
                                                     ethnicity = null, video = null, location = null, availability = null, page, articlesCompletedStart, articlesCompletedEnd) {
    const headers = await this.createAuthorizationHeader();

    let params = new HttpParams();
    params = params.append('search', search);
    params = params.append('articlesFirm', articlesFirm);
    params = params.append('gender', gender);
    params = params.append('ethnicity', ethnicity);
    params = params.append('nationality', nationality);
    params = params.append('location', location);
    params = params.append('qualification', qualification);
    params = params.append('video', video);
    params = params.append('availability', availability);
    params = params.append('page', page);
    params = params.append('limit', this._limit);
    params = params.append('articlesCompletedStart', articlesCompletedStart);
    params = params.append('articlesCompletedEnd', articlesCompletedEnd);

    return this._http.get<any>('/api/business/candidate/', { params: params, headers: headers['headers'] })
        .toPromise()
        .catch(this.handleError);
  }

  /**
   * get all jobs matching criteria
   * @param status {boolean}
   * @param candidateID {number}
   * @returns {Promise<any>}
   */
  public async getBusinessJobsMatchingCriteria(status, candidateID) {
    const headers = await this.createAuthorizationHeader();

    let params = new HttpParams();
    params = params.append('status', status);
    params = params.append('candidateID', candidateID);

    return this._http.get<any>('/api/business/job/criteria', { params: params, headers: headers['headers'] })
        .toPromise()
        .catch(this.handleError);
  }


  /**
   * Get all business jobs
   * @param {object} params
   * @returns {Promise<any>}
   */
  public async getBusinessJobs(params) {
    const headers = await this.createAuthorizationHeader();

    // let params = new HttpParams();
    // params = params.append('page', page);
    // params = params.append('limit', this._limit);

    return this._http.get<any>(`/api/business/job/?`, { params: params, headers: headers['headers'] })
        .toPromise()
        .catch(this.handleError);
  }

  /**
   * fetches business job by id
   * @param {number} id
   * @returns {Promise<any>}
   */
  public async getBusinessJobById(id: number) {
    const headers = await this.createAuthorizationHeader();
    return this._http.get<any>(`/api/business/job/${id}`, headers)
        .toPromise()
        .catch(this.handleError);
  }

  /**
   * deletes specified business job
   * @param {number} id
   * @returns {Promise<any>}
   */
  public async deleteBusinessJob(id: number) {
    const headers = await this.createAuthorizationHeader();
    return this._http.delete<any>(`/api/business/job/${id}`, headers)
        .toPromise()
        .catch(this.handleError);
  }

  /**
   * Remove business job spec file
   * @param {number} id
   * @returns {Promise<any>}
   */
  public async deleteBusinessJobSpec(id: number) {
    const headers = await this.createAuthorizationHeader();
    return this._http.delete<any>(`/api/business/job/${id}/spec`, headers)
        .toPromise()
        .catch(this.handleError);
  }

  /**
   * Upload business job spec file
   * @param {number} id
   * @param {object} data
   * @returns {Promise<any>}
   */
  public async uploadBusinessJobSpec(id: number, data) {
    const headers = await this.createAuthorizationHeader();
    return this._http.post<any>(`/api/business/job/${id}/spec`, data, headers)
        .toPromise()
        .catch(this.handleError);
  }

  /**
   * updates business job specified with id
   * @param id {number} - job id to update
   * @param body {object} - fields data to be updated in business job
   * @returns {Promise<any>}
   */
  public async updateBusinessJob(id: number, body: object) {
    const headers = await this.createAuthorizationHeader();
    return this._http.put(`/api/business/job/${id}`, body, headers)
        .toPromise()
        .catch(this.handleError);
  }

  /**
   * closes specified business job
   * @param {number} id
   * @param {object} body
   * @returns {Promise<any>}
   */
  public async closeBusinessJob(id: number, body: object) {
    const headers = await this.createAuthorizationHeader();
    return this._http.patch(`/api/business/job/${id}`, body, headers)
        .toPromise()
        .catch(this.handleError);
  }

  /**
   * Get details profile business
   * @return {Promise<AdminBusinessProfile>}
   */
  public async getBusinessProfileDetails(): Promise<AdminBusinessAccount> {
    const headers = await this.createAuthorizationHeader();
    return this._http.get<any>('/api/business/profile/', headers)
        .toPromise()
      .catch(this.handleError);
  }

  /**
   * Get details profile business
   * @return {Promise<AdminBusinessAccount>}
   */
  public async getBusinessProfile(): Promise<AdminBusinessAccount> {
    const headers = await this.createAuthorizationHeader();
    return this._http.get<any>('/api/business/profile/', headers)
      .toPromise()
      .catch(this.handleError);
  }

  /**
   * Qualification Drop Down SEND EMAIL
   * @returns {Promise<any>}
   */
  public async sendJobEmail(): Promise<any> {
    const headers = await this.createAuthorizationHeader();
    return this._http.post('/api/business/job/send', null, headers)
      .toPromise()
      .catch(this.handleError);
  }

  /**
   * Create Job fo Business
   * @param {BusinessJob} data
   * @returns {Promise<BusinessJob>}
   */
  public async createBusinessJob(data): Promise<BusinessJob> {
    const headers = await this.createAuthorizationHeader();
    return this._http.post<any>('/api/business/job/', data, headers)
        .toPromise()
        .catch(this.handleError);
  }

  /**
   * Update business profile
   * @param {AdminBusinessProfile} user
   * @return {Promise<void>}
   */
  public async updateBusinessProfile(user: AdminBusinessProfile): Promise<void> {
    const headers = await this.createAuthorizationHeader();
    return this._http.put('/api/business/profile/', user, headers)
      .toPromise()
      .catch(this.handleError);
  }

  /**
   * Get Preference Notification
   * @return {Promise<any>}
   */
  public async preferenceNotification(): Promise<any> {
    const headers = await this.createAuthorizationHeader();
    return this._http.get<any>('/api/business/preference/notification', headers)
      .toPromise()
      .catch(this.handleError);
  }

  /**
   * Update Preference NotificationEmail
   * @param data
   * @return {Promise<any|Object>}
   */
  public async updatePreferenceNotificationEmail(data): Promise<any> {
    const headers = await this.createAuthorizationHeader();
    return this._http.put('/api/business/preference/notification', data, headers)
      .toPromise()
      .catch(this.handleError);
  }

  /**
   * Update Preference Notification
   * @param data
   * @return {Promise<any>}
   */
  public async updatePreferenceNotification(data): Promise<any> {
    const headers = await this.createAuthorizationHeader();
    return this._http.put('/api/business/preference/notification', data, headers)
      .toPromise()
      .catch(this.handleError);
  }

  /**
   * gets candidate by id
   * @param id {number} - id of the candidate to get profile details for
   * @param jobID {number} - id of the candidate to get profile details for
   * @returns {Promise<any>}
   */
  public async getCandidateById(id: number, jobID): Promise<BusinessCandidate> {
    const headers = await this.createAuthorizationHeader();

    if(jobID !== null) {
      let params = new HttpParams();
      params = params.append('jobID', jobID);

      return this._http.get(`/api/business/candidate/${id}`, { params: params, headers: headers['headers'] })
        .toPromise()
        .catch(this.handleError);
    }
    else{
      return this._http.get(`/api/business/candidate/${id}`, headers)
        .toPromise()
        .catch(this.handleError);
    }
  }

  /**
   * gets a list of applicants awaiting approval
   * @param data {object}
   * @returns {Promise<BusinessApplicant[]>}
   */
  public async getApplicantsAwaitingApproval(data): Promise<BusinessApplicantList> {
    const headers = await this.createAuthorizationHeader();

    let params = new HttpParams();
    params = params.append('jobID', data.jobID);
    params = params.append('page', data.page);
    params = params.append('limit', this._limit);
    params = params.append('search', data.search);
    params = params.append('gender', data.gender);
    params = params.append('ethnicity', data.ethnicity);
    params = params.append('location', data.location);
    params = params.append('availability', data.availability);
    params = params.append('video', data.video);
    params = params.append('highestQualification', data.highestQualification);
    params = params.append('field', data.field);
    params = params.append('monthSalaryFrom', data.monthSalaryFrom);
    params = params.append('monthSalaryTo', data.monthSalaryTo);
    params = params.append('orderBy', data.orderBy);
    params = params.append('orderSort', data.orderSort);
    params = params.append('eligibility', data.eligibility);
    params = params.append('yearsOfWorkExperience', data.yearsOfWorkExperience);

    return this._http.get('/api/business/applicants/awaiting', { params: params, headers: headers['headers'] })
        .toPromise()
        .catch(this.handleError);
  }

  /**
   * applicants added to shortlist
   * @param data {object}
   * @returns {Promise<BusinessApplicant[]>}
   */
  public async getApplicantsShortlisted(data): Promise<BusinessApplicantList> {
    const headers = await this.createAuthorizationHeader();

    let params = new HttpParams();
    params = params.append('jobID', data.jobID);
    params = params.append('page', data.page);
    params = params.append('limit', this._limit);
    params = params.append('search', data.search);
    params = params.append('gender', data.gender);
    params = params.append('ethnicity', data.ethnicity);
    params = params.append('location', data.location);
    params = params.append('availability', data.availability);
    params = params.append('video', data.video);
    params = params.append('highestQualification', data.highestQualification);
    params = params.append('field', data.field);
    params = params.append('monthSalaryFrom', data.monthSalaryFrom);
    params = params.append('monthSalaryTo', data.monthSalaryTo);
    params = params.append('orderBy', data.orderBy);
    params = params.append('orderSort', data.orderSort);
    params = params.append('eligibility', data.eligibility);
    params = params.append('yearsOfWorkExperience', data.yearsOfWorkExperience);

    return this._http.get('/api/business/applicants/shortList', { params: params, headers: headers['headers'] })
        .toPromise()
        .catch(this.handleError);
  }

  /**
   * gets list of approved applicants
   * @param data {object}
   * @returns {Promise<BusinessApplicant[]>}
   */
  public async getApplicantsApproved(data): Promise<BusinessApplicantList> {
    const headers = await this.createAuthorizationHeader();

    let params = new HttpParams();
    params = params.append('jobID', data.jobID);
    params = params.append('page', data.page);
    params = params.append('limit', this._limit);
    params = params.append('search', data.search);
    params = params.append('gender', data.gender);
    params = params.append('ethnicity', data.ethnicity);
    params = params.append('location', data.location);
    params = params.append('availability', data.availability);
    params = params.append('video', data.video);
    params = params.append('highestQualification', data.highestQualification);
    params = params.append('field', data.field);
    params = params.append('monthSalaryFrom', data.monthSalaryFrom);
    params = params.append('monthSalaryTo', data.monthSalaryTo);
    params = params.append('orderBy', data.orderBy);
    params = params.append('orderSort', data.orderSort);
    params = params.append('eligibility', data.eligibility);
    params = params.append('yearsOfWorkExperience', data.yearsOfWorkExperience);

    return this._http.get('/api/business/applicants/approve', { params: params, headers: headers['headers'] })
        .toPromise()
        .catch(this.handleError);
  }

  /**
   * gets list of all applicants declined
   * @param data {object}
   * @returns {Promise<BusinessApplicant[]>}
   */
  public async getApplicantsDeclined(data): Promise<BusinessApplicantList> {
    const headers = await this.createAuthorizationHeader();

    let params = new HttpParams();
    params = params.append('jobID', data.jobID);
    params = params.append('page', data.page);
    params = params.append('limit', this._limit);
    params = params.append('search', data.search);
    params = params.append('gender', data.gender);
    params = params.append('ethnicity', data.ethnicity);
    params = params.append('location', data.location);
    params = params.append('availability', data.availability);
    params = params.append('video', data.video);
    params = params.append('highestQualification', data.highestQualification);
    params = params.append('field', data.field);
    params = params.append('monthSalaryFrom', data.monthSalaryFrom);
    params = params.append('monthSalaryTo', data.monthSalaryTo);
    params = params.append('orderBy', data.orderBy);
    params = params.append('orderSort', data.orderSort);
    params = params.append('eligibility', data.eligibility);
    params = params.append('yearsOfWorkExperience', data.yearsOfWorkExperience);

    return this._http.get('/api/business/applicants/decline', { params: params, headers: headers['headers'] })
        .toPromise()
        .catch(this.handleError);
  }

  /**
   * applicants count
   * @param data {object}
   * @param status {number}
   * @returns {Promise<any>}
   */
  public async getApplicantsCount(status, data): Promise<any> {
    const headers = await this.createAuthorizationHeader();

    let params = new HttpParams();
    params = params.append('status', status);
    params = params.append('jobID', data.jobID);
    params = params.append('page', data.page);
    params = params.append('limit', this._limit);
    params = params.append('search', data.search);
    params = params.append('gender', data.gender);
    params = params.append('ethnicity', data.ethnicity);
    params = params.append('location', data.location);
    params = params.append('availability', data.availability);
    params = params.append('video', data.video);
    params = params.append('highestQualification', data.highestQualification);
    params = params.append('field', data.field);
    params = params.append('monthSalaryFrom', data.monthSalaryFrom);
    params = params.append('monthSalaryTo', data.monthSalaryTo);
    params = params.append('orderBy', data.orderBy);
    params = params.append('orderSort', data.orderSort);
    params = params.append('eligibility', data.eligibility);
    params = params.append('yearsOfWorkExperience', data.yearsOfWorkExperience);

    return this._http.get('/api/business/applicants/count', { params: params, headers: headers['headers'] })
      .toPromise()
      .catch(this.handleError);
  }

  /**
   * fetches list of all applicants
   * @return {Promise<any|Object>}
   */
  public async getListOfApplicants(jobId: number): Promise<ApplicantsList> {
      const headers = await this.createAuthorizationHeader();

    let params = new HttpParams();
    params = params.append('jobID', String(jobId));

      return this._http.get('/api/business/applicants/', { params: params, headers: headers['headers'] })
          .toPromise()
          .catch(this.handleError);
  }

  /**
   * Add Candidate to ShortList
   * @param {number} candidateID
   * @param {number} jobID
   * @returns {Promise<any>}
   */
  public async addCandidateToShortList(candidateID: number, jobID: number = null): Promise<any> {
      const headers = await this.createAuthorizationHeader();
      const data = {
          candidateID: candidateID,
          jobID: jobID
      };
      return this._http.post('/api/business/applicants/shortList', data, headers)
          .toPromise()
          .catch(this.handleError);
  }

  /**
   * Remove candidate From ShortList
   * @param {number} candidateID
   * @param {number} jobID
   * @returns {Promise<any>}
   */
  public async removeCandidateFromShortList(candidateID: number, jobID: number = null): Promise<any> {
      const headers = await this.createAuthorizationHeader();
      const data = {
          candidateID: candidateID,
          jobID: jobID
      };
      return this._http.post('/api/business/applicants/shortList/remove', data, headers)
          .toPromise()
          .catch(this.handleError);
  }

  /**
   * Decline candidate
   * @param {number} candidateID
   * @param {number} jobID
   * @returns {Promise<any>}
   */
  public async declineCandidateApplication(candidateID: number, jobID: number = null): Promise<any> {
      const headers = await this.createAuthorizationHeader();
      const data = {
          candidateID: candidateID,
          jobID: jobID
      };
      return this._http.post('/api/business/applicants/decline', data, headers)
          .toPromise()
          .catch(this.handleError);
  }

    /**
    * Cancel Application From Business
    * @param {number} candidateID
    * @param {number} jobID
    * @returns {Promise<any>}
    */
    public async cancelApplicationFromBusiness(candidateID: number, jobID: number = null): Promise<any> {
        const headers = await this.createAuthorizationHeader();
        const data = {
            candidateID: candidateID,
            jobID: jobID
        };
        return this._http.post('/api/business/applicants/cancel', data, headers)
            .toPromise()
            .catch(this.handleError);
    }

  /**
   * Add to interview candidate
   * @param candidateID {number}
   * @param jobID {number}
   * @return {Promise<any|Object>}
   */
    public async setUpInterviewCandidate(candidateID: number, jobID: number = null): Promise<any> {
      const headers = await this.createAuthorizationHeader();
      const data = {
        candidateID: candidateID,
        jobID: jobID
      };
      return this._http.post('/api/business/applicants/approve', data, headers)
        .toPromise()
        .catch(this.handleError);
    }

  /**
   * Get applicants details
   * @param id {number}
   * @return {Promise<any|Object>}
   */
    public async getApplicantDetails(id: number): Promise<any> {
      const headers = await this.createAuthorizationHeader();

      return this._http.get('/api/business/applicants/' + id, headers)
        .toPromise()
        .catch(this.handleError);
    }

  /**
   * Get business dashboard
   * @param limit {string}
   * @return {Promise<any|Object>}
   */
    public async getBusinessDashboard(limit: string = ''): Promise<any> {
      const headers = await this.createAuthorizationHeader();

      return this._http.get('/api/business/dashboard', headers)
        .toPromise()
        .catch(this.handleError);
    }

  /**
   * Set status candidate profile
   * @param id {number}
   * @param action {string}
   * @return {Promise<any|Object>}
   */
    public async setStatusCandidateProfile(id: number, action: string): Promise<void> {
      const headers = await this.createAuthorizationHeader();

      const data = {
        'action': action
      };

      return this._http.patch('/api/business/candidate/' + id + '/stats', data, headers)
        .toPromise()
        .catch(this.handleError);
    }

  /**
   * Get status first popup
   * @return {Promise<any|Object>}
   */
  public async getStatusFirstPopup(): Promise<any> {
    const headers = await this.createAuthorizationHeader();

    return this._http.get('/api/business/profile/firstPopUp', headers)
      .toPromise()
      .catch(this.handleError);
  }


  public async setStatusFirstPopUp(status): Promise<any> {
    const headers = await this.createAuthorizationHeader();

    const data = {
      'firstPopUp': status
    };

    return this._http.patch('/api/business/profile/firstPopUp', data, headers)
      .toPromise()
      .catch(this.handleError);
  }


// КНОПКИ

  /**
   * Add Candidate to Short List
   * @param candidate
   * @returns {Promise<void>}
   */
  // public async declineCandidateApplication(candidate): Promise<void>{
  //   try {
  //     await this._businessService.declineCandidateApplication(candidate.details.id, candidate.jobID);
  //
  //     const index = this._listJob.indexOf(this._candidateToView);
  //     this._listJob.splice(index, 1);
  //     this._totalCount.number-=1;
  //     if(candidate.applicant === 1){
  //       this._sharedService.sidebarBusinessBadges.applicantAwaiting--;
  //       this._sharedService.sidebarBusinessBadges.applicantDecline++;
  //     }
  //     else if(candidate.applicant === 2){
  //       this._sharedService.sidebarBusinessBadges.applicantShortlist--;
  //       this._sharedService.sidebarBusinessBadges.applicantDecline++;
  //     }
  //     this.closePopup();
  //     this._toastr.success('Application was declined');
  //   }
  //   catch (err) {
  //     this._sharedService.showRequestErrors(err);
  //   }
  // }
  /**
   * Add Candidate to Short List
   * @param candidate {object}
   * @returns {Promise<void>}
   */
  // public async addCandidateToShortList(candidate): Promise<void>{
  //   try {
  //     await this._businessService.addCandidateToShortList(candidate.details.id, candidate.jobID);
  //     if(this._viewPopup !== 'true'){
  //       if(candidate.applicant === 1 || candidate.applicant === 4) {
  //         const index = this._listJob.indexOf(this._candidateToView);
  //         this._listJob.splice(index, 1);
  //         this._totalCount.number-=1;
  //       }
  //     }
  //     else{
  //       if(candidate.applicant === 3 || candidate.applicant === 6 || candidate.applicant === 7){
  //         this._sharedService.sidebarBusinessBadges.applicantShortlist++;
  //         this._sharedService.sidebarBusinessBadges.applicantAll++;
  //       }
  //     }
  //     if(candidate.applicant === 0){
  //       this._sharedService.sidebarBusinessBadges.applicantShortlist++;
  //       this._sharedService.sidebarBusinessBadges.applicantAll++;
  //     }
  //     else if(candidate.applicant === 1){
  //       this._sharedService.sidebarBusinessBadges.applicantAwaiting--;
  //       this._sharedService.sidebarBusinessBadges.applicantShortlist++;
  //     }
  //     else if(candidate.applicant === 4){
  //       this._sharedService.sidebarBusinessBadges.applicantDecline--;
  //       this._sharedService.sidebarBusinessBadges.applicantShortlist++;
  //     }
  //
  //     this.closePopup();
  //     this._toastr.success('Added to ShortList');
  //   }
  //   catch (err) {
  //     this._sharedService.showRequestErrors(err);
  //   }
  // }








}
