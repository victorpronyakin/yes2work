import 'rxjs/add/observable/fromPromise';
import 'rxjs/add/observable/throw';
import 'rxjs/add/operator/catch';
import { Injectable } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import {
  AdminBusinessProfile, AdminCandidateProfile, AdminCandidateProfileNew, AdminDashboardData, AdminInterviewItemList,
  AdminLoggingList,
  AdminProfile, BusinessApprove,
  BusinessApproveList,
  BusinessJobsAwaitingApprovalList,
  CandidateApprove, CandidateApproveList, CandidateFileApprove, CandidateFileApproveList
} from '../../entities/models-admin';
import { SettingsApiService } from './settings-api.service';
import { AuthService } from './auth.service';
import { BusinessAdminJobFullDetails, BusinessJobsAwaitingApproval, IAccessToken } from '../../entities/models';
import { Router } from '@angular/router';

@Injectable()
export class AdminService extends SettingsApiService {

  private _limit = '50';
  constructor(
    protected readonly _http: HttpClient,
    protected readonly _authService: AuthService,
    protected readonly _router: Router
  ) {
    super(_http, _authService, _router);
  }

  /**
   * Get business profile
   * @returns {Promise<AdminBusinessProfile>}
   */
  public async getBusinessProfile(): Promise<AdminBusinessProfile> {
    const headers = await this.createAuthorizationHeader();

    return this._http.get<any>('/api/business/profile/', headers)
      .toPromise()
      .catch(this.handleError);
  }

  /**
   * Ge tDashboard Data
   * @param {string} limit
   * @returns {Promise<any>}
   */
  public async getDashboardData(limit: string = ''): Promise<AdminDashboardData> {
    const headers = await this.createAuthorizationHeader();

    let params = new HttpParams();
    params = params.append('limit', this._limit);
    return this._http.get('/api/admin/dashboard', {params: params, headers: headers['headers']})
      .toPromise()
      .catch(this.handleError);
  }

  /**
   * Get details profile business
   * @param id
   * @return {Promise<AdminBusinessProfile>}
   */
  public async getDetailsProfileBusiness(id): Promise<AdminBusinessProfile> {
    const headers = await this.createAuthorizationHeader();

    return this._http.get('/api/admin/business/' + id, {headers: headers['headers']})
      .toPromise()
      .catch(this.handleError);
  }

  /**
   * Get approve business profiles
   * @param {string} page
   * @param {string} orderBy
   * @param {string} orderSort
   * @param {string} search
   * @return {Promise<BusinessApprove[]>}
   */
  public async getApproveBusiness(page, orderBy, orderSort, search): Promise<BusinessApproveList> {
    const headers = await this.createAuthorizationHeader();

    let params = new HttpParams();
    params = params.append('limit', this._limit);
    params = params.append('page', page);
    params = params.append('search', search);
    params = params.append('orderBy', orderBy);
    params = params.append('orderSort', (orderSort === true) ? 'desc' : 'asc');

    return this._http.get('/api/admin/business/approve', {params: params, headers: headers['headers']})
      .toPromise()
      .catch(this.handleError);
  }

  /**
   * Get approve candidate file
   * @param {string} page
   * @param {string} orderBy
   * @param {string} orderSort
   * @param {string} search
   * @returns {Promise<CandidateFileApprove[]>}
   */
  public async getApproveCandidateFile(page, orderBy, orderSort, search): Promise<CandidateFileApproveList> {
    const headers = await this.createAuthorizationHeader();

    let params = new HttpParams();
    params = params.append('page', page);
    params = params.append('limit', this._limit);
    params = params.append('search', search);
    params = params.append('orderBy', orderBy);
    params = params.append('orderSort', (orderSort === true) ? 'desc' : 'asc');

    return this._http.get('/api/admin/candidate/file/approve', {params: params, headers: headers['headers']})
      .toPromise()
      .catch(this.handleError);
  }

  /**
   * Get approve client file
   * @param {string} page
   * @param {string} orderBy
   * @param {string} orderSort
   * @param {string} search
   * @returns {Promise<CandidateFileApprove[]>}
   */
  public async getApproveClientFile(page, orderBy, orderSort, search): Promise<CandidateFileApproveList> {
    const headers = await this.createAuthorizationHeader();

    let params = new HttpParams();
    params = params.append('page', page);
    params = params.append('limit', this._limit);
    params = params.append('search', search);
    params = params.append('orderBy', orderBy);
    params = params.append('orderSort', (orderSort === true) ? 'desc' : 'asc');

    return this._http.get('/api/admin/job/file/approve', {params: params, headers: headers['headers']})
      .toPromise()
      .catch(this.handleError);
  }

  /**
   * Get approve candidate video
   * @param {string} page
   * @returns {Promise<CandidateFileApprove[]>}
   */
  public async getCandidateVideosApprove(page): Promise<CandidateFileApproveList> {
    const headers = await this.createAuthorizationHeader();

    let params = new HttpParams();
    params = params.append('page', page);
    params = params.append('limit', this._limit);

    return this._http.get('/api/admin/candidate/video/approve', {params: params, headers: headers['headers']})
      .toPromise()
      .catch(this.handleError);
  }

  /**
   * Managed business user (approve/decline)
   * @param {number} id
   * @param {boolean} status
   * @return {Promise<void>}
   */
  public async managedBusinessUser(id: number, status: boolean): Promise<void> {
    const headers = await this.createAuthorizationHeader();

    return this._http.patch('/api/admin/business/' + id + '/approve', { 'approved': status }, headers)
      .toPromise()
      .catch(this.handleError);
  }

  /**
   * Get approve candidate profiles
   * @param {number} page
   * @param {string} orderBy
   * @param {string} orderSort
   * @param {string} search
   * @return {Promise<CandidateApprove[]>}
   */
  public async getApproveCandidate(page, orderBy, orderSort, search): Promise<CandidateApproveList> {
    const headers = await this.createAuthorizationHeader();

    let params = new HttpParams();
    params = params.append('limit', this._limit);
    params = params.append('page', page);
    params = params.append('search', search);
    params = params.append('orderBy', orderBy);
    params = params.append('orderSort', (orderSort === true) ? 'desc' : 'asc');

    return this._http.get('/api/admin/candidate/approve', {params: params, headers: headers['headers']})
      .toPromise()
      .catch(this.handleError);
  }

  /**
   * Managed candidate user (approve/decline)
   * @param {number} id
   * @param {boolean} status
   * @return {Promise<void>}
   */
  public async managedCandidateUser(id: number, status: boolean): Promise<void> {
    const headers = await this.createAuthorizationHeader();

    const data = { 'approved': status };
    return this._http.patch('/api/admin/candidate/' + id + '/approve', data, headers)
      .toPromise()
      .catch(this.handleError);
  }

  /**
   * Managed candidate file (approve/decline)
   * @param {CandidateFileApprove} file
   * @param {boolean} status
   * @returns {Promise<void>}
   */
  public async managedCandidateFile(file: CandidateFileApprove, status: boolean): Promise<void> {
    const headers = await this.createAuthorizationHeader();

    const data = {
      'fieldName': file.fieldName,
      'url': file.url,
      'approved': status
    };

    return this._http.patch('/api/admin/candidate/file/' + file.userId + '/approve', data, headers).toPromise()
      .catch(this.handleError);
  }

  /**
   * Managed client file (approve/decline)
   * @param {object} file
   * @param {boolean} status
   * @returns {Promise<void>}
   */
  public async managedClientFile(file, status: boolean): Promise<void> {
    const headers = await this.createAuthorizationHeader();

    const data = {
      'approve': status
    };

    return this._http.patch('/api/admin/job/' + file.jobId + '/file/approve', data, headers).toPromise()
      .catch(this.handleError);
  }

  /**
   * Upload admin file for candidate
   * @param {string} data
   * @param {string} userId
   * @returns {Promise<void>}
   */
  public async uploadAdminVideoForCandidate(data, userId): Promise<any> {
    const headers = await this.createAuthorizationHeader();

    return this._http.post('/api/admin/candidate/video/' + userId + '/approve', data, headers)
      .toPromise()
      .catch(this.handleError);
  }

  /**
   * Upload admin video for candidate
   * @param {string} data
   * @param {string} userId
   * @returns {Promise<void>}
   */
  public async uploadAdminFilesForCandidate(data, userId): Promise<any> {
    const headers = await this.createAuthorizationHeader();

    return this._http.post('/api/admin/candidate/file/' + userId + '/approve', data, headers)
      .toPromise()
      .catch(this.handleError);
  }

  /**
   * Upload admin video for client
   * @param {string} data
   * @param {string} jobId
   * @returns {Promise<void>}
   */
  public async uploadAdminFilesForClient(data, jobId): Promise<any> {
    const headers = await this.createAuthorizationHeader();

    return this._http.post('/api/admin/job/' + jobId + '/file/approve', data, headers)
      .toPromise()
      .catch(this.handleError);
  }

  /**
   * Upload admin video for client admin
   * @param {string} data
   * @param {string} jobId
   * @returns {Promise<void>}
   */
  public async uploadAdminFilesForClientAdmin(data, jobId): Promise<any> {
    const headers = await this.createAuthorizationHeader();

    return this._http.post('/api/admin/job/' + jobId + '/spec', data, headers)
      .toPromise()
      .catch(this.handleError);
  }

  /**
   * Managed candidate file (approve/decline)
   * @param {Object} file
   * @param {boolean} status
   * @returns {Promise<void>}
   */
  public async managedCandidateVideo(file, status: boolean): Promise<void> {
    const headers = await this.createAuthorizationHeader();

    const data = {
      'fieldName': file.fieldName,
      'url': file.url,
      'approved': status
    };

    return this._http.patch('/api/admin/candidate/video/' + file.userId + '/approve', data, headers).toPromise()
      .catch(this.handleError);
  }

  /**
   * Update business profile
   * @param {number} id
   * @param {AdminBusinessProfile} user
   * @return {Promise<void>}
   */
  public async updateBusinessProfile(id: number, user: AdminBusinessProfile): Promise<void> {
    const headers = await this.createAuthorizationHeader();

    return this._http.put('/api/admin/business/' + id, user, headers)
      .toPromise()
      .catch(this.handleError);
  }

  /**
   * Get details profile candidate
   * @param id
   * @return {Promise<AdminBusinessProfile>}
   */
  public async getDetailsProfileCandidate(id): Promise<AdminCandidateProfile> {
    const headers = await this.createAuthorizationHeader();

    return this._http.get('/api/admin/candidate/' + id, {headers: headers['headers']})
      .toPromise()
      .catch(this.handleError);
  }

  /**
   * Update candidate profile
   * @param {number} id
   * @param {AdminCandidateProfile} user
   * @return Promise<void>
   */
  public async updateCandidateProfile(id: number, user: AdminCandidateProfile): Promise<void> {
    const headers = await this.createAuthorizationHeader();

    return this._http.put('/api/admin/candidate/' + id, user, headers)
      .toPromise()
      .catch(this.handleError);
  }

  /**
   * Get Admin Profile
   * @returns {Promise<AdminProfile>}
   */
  public async getAdminProfile(): Promise<AdminProfile> {
    const headers = await this.createAuthorizationHeader();

    return this._http.get('/api/admin/profile/', headers)
      .toPromise()
      .catch(this.handleError);
  }

  /**
   * Update admin profile
   * @param {AdminProfile} user
   * @return {Promise<void>}
   */
  public async updateAdminProfile(user: AdminProfile): Promise<void> {
    const headers = await this.createAuthorizationHeader();

    return this._http.put('/api/admin/profile/', user.profile, headers)
      .toPromise()
      .catch(this.handleError);
  }

  /**
   * gets jobs awaiting for approve for admins
   * @returns {Promise<BusinessJobsAwaitingApproval[]>}
   */
  public async getAdminJobsApproved(page, orderBy, orderSort, search): Promise<BusinessJobsAwaitingApprovalList> {
    const headers = await this.createAuthorizationHeader();

    let params = new HttpParams();
    params = params.append('page', page);
    params = params.append('orderBy', orderBy);
    params = params.append('orderSort', (orderSort === true) ? 'desc' : 'asc');
    params = params.append('search', search);
    params = params.append('limit', this._limit);

    return this._http.get('/api/admin/job/approve',  { params: params, headers: headers['headers'] })
      .toPromise()
      .catch(this.handleError);
  }

  /**
   * changes status for the job specified with id
   * @param id {number} - id of the job to update status for
   * @param body {object} - object with status to be set for job
   * @returns {Promise<any|Object>}
   */
  public async changeJobsStatus(id, body: object): Promise<void> {
    const headers = await this.createAuthorizationHeader();

    return this._http.patch(`/api/admin/job/${id}/approve`, body, headers)
      .toPromise()
      .catch(this.handleError);
  }

  /**
   * gets business job specified with id
   * @param jobId {number} - business job id
   * @returns {Promise<any|Object>}
   */
  public async getBusinessJobById(jobId: number): Promise<BusinessAdminJobFullDetails> {
    const headers = await this.createAuthorizationHeader();

    return this._http.get(`/api/admin/job/${jobId}`, headers)
      .toPromise()
      .catch(this.handleError);
  }

  /**
   *
   * Get business candidate count according to filter
   * @param {any} data
   * @returns {Promise<any>}
   */
  public async getCandidatesCountSatisfyCriteria(data): Promise<any> {
    const headers = await this.createAuthorizationHeader();

    let params = new HttpParams();
    params = params.append('gender', data.gender);
    params = params.append('ethnicity', data.ethnicity);
    params = params.append('location', data.location);
    params = params.append('highestQualification', data.highestQualification);
    params = params.append('field', data.field);
    params = params.append('availability', data.availability);
    params = params.append('yearsOfWorkExperience', data.yearsOfWorkExperience);
    params = params.append('video', data.video);
    params = params.append('monthSalaryFrom', data.monthSalaryFrom);
    params = params.append('monthSalaryTo', data.monthSalaryTo);
    params = params.append('eligibility', data.eligibility);


    return this._http.get('/api/admin/job/candidate/count',  { params: params, headers: headers['headers'] })
      .toPromise()
      .catch(this.handleError);
  }

  /**
   * updates business job specified with id
   * @param {number} id
   * @param {object} body
   * @returns {Promise<BusinessAdminJobFullDetails>}
   */
  public async updateBusinessJob(id: number, body: object): Promise<BusinessAdminJobFullDetails> {
    const headers = await this.createAuthorizationHeader();

    return this._http.put(`/api/admin/job/${id}`, body, headers)
      .toPromise()
      .catch(this.handleError);
  }


  /**
   * Get admin profile nitification
   * @returns {Promise<any>}
   */
  public async getAdminProfileNotification(): Promise<any> {
    const headers = await this.createAuthorizationHeader();

    return this._http.get('/api/admin/profile/notification', headers)
      .toPromise()
      .catch(this.handleError);
  }

  /**
   * Get admin profile details
   * @returns {Promise<any>}
   */
  public async getAdminProfileDetails(): Promise<any> {
    const headers = await this.createAuthorizationHeader();

    return this._http.get('/api/admin/profile/', headers)
      .toPromise()
      .catch(this.handleError);
  }

  /**
   * Update preference notification
   * @param data
   * @returns {Promise<any>}
   */
  public async updatePreferenceNotification(data): Promise<any> {
    const headers = await this.createAuthorizationHeader();

    return this._http.put('/api/admin/profile/notification', data, headers)
      .toPromise()
      .catch(this.handleError);
  }

  /**
   * Delete business profile
   * @param id {number}
   * @return {Promise<any|Object>}
   */
  public async deleteBusinessProfile(id: number): Promise<any>{
    const headers = await this.createAuthorizationHeader();

    return this._http.delete('/api/admin/business/' + id, headers)
      .toPromise()
      .catch(this.handleError);
  }

  /**
   * Delete candidate profile
   * @param id {number}
   * @return {Promise<any|Object>}
   */
  public async deleteCandidateProfile(id: number): Promise<any>{
    const headers = await this.createAuthorizationHeader();

    return this._http.delete('/api/admin/candidate/' + id, headers)
      .toPromise()
      .catch(this.handleError);
  }

  /**
   * Update candidate status
   * @param id {number}
   * @param enabled {boolean}
   * @return {Promise<any|Object>}
   */
  public async updateCandidateStatus(id: number, enabled: boolean): Promise<any>{
    const headers = await this.createAuthorizationHeader();

    const data = {
      'enabled': enabled
    };

    return this._http.patch('/api/admin/candidate/' + id, data, headers)
      .toPromise()
      .catch(this.handleError);
  }

  /**
   * Get all business profiles
   * @param data
   * @return {Promise<any|Object>}
   */
  public async getAllBusinessList(data): Promise<any> {
    const headers = await this.createAuthorizationHeader();

    let params = new HttpParams();
    params = params.append('search', data.search);
    params = params.append('page', data.page);
    params = params.append('limit', this._limit);
    params = params.append('csv', data.csv);
    params = params.append('orderSort', (data.orderSort === true) ? 'desc' : 'asc');
    params = params.append('orderBy', data.orderBy);

    return this._http.get('/api/admin/business/?', { params: params, headers: headers['headers'] })
      .toPromise()
      .catch(this.handleError);
  }

  /**
   * Create business profile admin
   * @param data {}
   * @return {Promise<any|Object>}
   */
  public async createBusinessProfile(data): Promise<any> {
    const headers = await this.createAuthorizationHeader();

    return this._http.post('/api/admin/business/', data, headers)
      .toPromise()
      .catch(this.handleError);
  }

  /**
   * Get all jobs admin
   * @param data
   * @return {Promise<any|Object>}
   */
  public async getAllJobs(data): Promise<any> {
    const headers = await this.createAuthorizationHeader();

    let params = new HttpParams();
    params = params.append('csv', data.csv);
    params = params.append('page', data.page);
    params = params.append('limit', this._limit);
    params = params.append('search', data.search);
    params = params.append('orderBy', data.orderBy);
    params = params.append('orderSort', (data.orderSort === true) ? 'desc' : 'asc');
    params = params.append('status', data.status);
    params = params.append('dateStart', data.dateStart);
    params = params.append('dateEnd', data.dateEnd);

    return this._http.get('/api/admin/job/', { params: params, headers: headers['headers'] })
      .toPromise()
      .catch(this.handleError);
  }

  /**
   * Delete jobs admin
   * @param id {number}
   * @return {Promise<any|Object>}
   */
  public async deleteJobs(id: number): Promise<void> {
    const headers = await this.createAuthorizationHeader();

    return this._http.delete('/api/admin/job/' + id, headers)
      .toPromise()
      .catch(this.handleError);
  }

  /**
   * Created jobs admin
   * @param data
   * @return {Promise<any|Object>}
   */
  public async createAdminJobs(data): Promise<void> {
    const headers = await this.createAuthorizationHeader();

    return this._http.post('/api/admin/job/', data, headers)
      .toPromise()
      .catch(this.handleError);
  }

  /**
   * Get user token for admin
   * @param id
   * @return {Promise<any|Object>}
   */
  public async getUserTokenForAdmin(id): Promise<IAccessToken> {
    const headers = await this.createAuthorizationHeader();

    return this._http.post('/api/admin/switch_user/' + id, null, headers)
      .toPromise()
      .catch(this.handleError);
  }

  /**
   * Update business status
   * @param id {number}
   * @param enabled {boolean}
   * @return {Promise<any|Object>}
   */
  public async updateBusinessStatus(id: number, enabled: boolean): Promise<void> {
    const headers = await this.createAuthorizationHeader();

    const data = {
      'enabled': enabled
    };

    return this._http.patch('/api/admin/business/' + id, data, headers)
      .toPromise()
      .catch(this.handleError);
  }

  /**
   * Update status job for admin
   * @param id {number}
   * @param status {boolean}
   * @return {Promise<any|Object>}
   */
  public async updateJobStatus(id: number, status: boolean): Promise<void> {
    const headers = await this.createAuthorizationHeader();

    const data = {
      'status': status
    };

    return this._http.patch('/api/admin/job/' + id, data, headers)
      .toPromise()
      .catch(this.handleError);
  }

  /**
   * Get all candidate list
   * @param data
   * @return {Promise<any|Object>}
   */
  public async getAllCandidateList(data): Promise<any> {
    const headers = await this.createAuthorizationHeader();

    let params = new HttpParams();
    params = params.append('csv', data.csv);
    params = params.append('page', data.page);
    params = params.append('limit', this._limit);
    params = params.append('search', data.search);
    params = params.append('orderBy', data.orderBy);
    params = params.append('orderSort', (data.orderSort === true) ? 'desc' : 'asc');
    params = params.append('eligibility', data.eligibility);
    params = params.append('ethnicity', data.ethnicity);
    params = params.append('gender', data.gender);
    params = params.append('location', data.location);
    params = params.append('highestQualification', data.highestQualification);
    params = params.append('field', data.field);
    params = params.append('yearsOfWorkExperience', data.yearsOfWorkExperience);
    params = params.append('availability', data.availability);
    params = params.append('video', data.video);
    params = params.append('monthSalaryFrom', data.monthSalaryFrom);
    params = params.append('monthSalaryTo', data.monthSalaryTo);
    params = params.append('enabled', data.enabled);
    params = params.append('profileComplete', data.profileComplete);

    return this._http.get('/api/admin/candidate/?', { params: params, headers: headers['headers'] })
      .toPromise()
      .catch(this.handleError);
  }

  /**
   * Get all candidate list
   * @param data
   * @return {Promise<any|Object>}
   */
  public async getAllCandidateListCount(data): Promise<any> {
    const headers = await this.createAuthorizationHeader();

    let params = new HttpParams();
    params = params.append('search', data.search);
    params = params.append('gender', data.gender);
    params = params.append('ethnicity', data.ethnicity);
    params = params.append('location', data.location);
    params = params.append('highestQualification', data.highestQualification);
    params = params.append('field', data.field);
    params = params.append('availability', data.availability);
    params = params.append('yearsOfWorkExperience', data.yearsOfWorkExperience);
    params = params.append('video', data.video);
    params = params.append('monthSalaryFrom', data.monthSalaryFrom);
    params = params.append('monthSalaryTo', data.monthSalaryTo);
    params = params.append('eligibility', data.eligibility);
    params = params.append('orderBy', data.orderBy);
    params = params.append('orderSort', (data.orderSort === true) ? 'desc' : 'asc');
    params = params.append('profileComplete', data.profileComplete);
    params = params.append('enabled', data.enabled);

    return this._http.get('/api/admin/candidate/count?', { params: params, headers: headers['headers'] })
      .toPromise()
      .catch(this.handleError);
  }

  /**
   * Created candidate profile for admin
   * @param data
   * @return {Promise<any|Object>}
   */
  public async createdCandidateProfile(data): Promise<void> {
    const headers = await this.createAuthorizationHeader();

    return this._http.post('/api/admin/candidate/', data, headers)
      .toPromise()
      .catch(this.handleError);
  }

  /**
   * Get full details business
   * @return {Promise<Object>}
   */
  public async getFullDetailsBusiness(): Promise<any> {
    const headers = await this.createAuthorizationHeader();

    return this._http.get('/api/admin/business/full', headers)
      .toPromise()
  }

  /**
   * Set up interview
   * @param {number} id
   * @return {Promise<void>}
   */
  public async adminSetUpInterview(id: number): Promise<void> {
    const headers = await this.createAuthorizationHeader();

    return this._http.patch('/api/admin/interviews/' + id + '/setUp', { 'setUp': true }, headers)
      .toPromise()
      .catch(this.handleError);
  }

  /**
   * Set up interview
   * @param {number} id
   * @param {boolean} status
   * @return {Promise<void>}
   */
  public async adminPendingInterview(id: number, status: boolean): Promise<void> {
    const headers = await this.createAuthorizationHeader();

    return this._http.patch('/api/admin/interviews/' + id + '/placed', { 'placed': status }, headers)
      .toPromise()
      .catch(this.handleError);
  }

  /**
   * Get set up interviews candidate for admin
   * @param page {number}
   * @param orderBy {string}
   * @param orderSort {string}
   * @param search {string}
   * @return {Promise<Object>}
   */
  public async getSetUpInterviewsCandidate(page, orderBy, orderSort, search): Promise<any> {
    const headers = await this.createAuthorizationHeader();

    let params = new HttpParams();
    params = params.append('page', page);
    params = params.append('orderBy', orderBy);
    params = params.append('orderSort', (orderSort === true) ? 'desc' : 'asc');
    params = params.append('search', search);
    params = params.append('limit', this._limit);

    return this._http.get('/api/admin/interviews/setUp/candidate', { params: params, headers: headers['headers'] })
      .toPromise()
  }


  /**
   * Get set up interviews client for admin
   * @param page {number}
   * @param orderBy {string}
   * @param orderSort {string}
   * @param search {string}
   * @return {Promise<Object>}
   */
  public async getSetUpInterviewsClient(page, orderBy, orderSort, search): Promise<any> {
    const headers = await this.createAuthorizationHeader();

    let params = new HttpParams();
    params = params.append('page', page);
    params = params.append('search', search);
    params = params.append('orderBy', orderBy);
    params = params.append('orderSort', (orderSort === true) ? 'desc' : 'asc');
    params = params.append('limit', this._limit);

    return this._http.get('/api/admin/interviews/setUp/client', { params: params, headers: headers['headers'] })
      .toPromise()
  }

  /**
   * Get pending interviews for admin
   * @param page {number}
   * @param orderBy {string}
   * @param orderSort {string}
   * @param search {string}
   * @return {Promise<Object>}
   */
  public async getPendingInterview(page, orderBy, orderSort, search): Promise<any> {
    const headers = await this.createAuthorizationHeader();

    let params = new HttpParams();
    params = params.append('page', page);
    params = params.append('search', search);
    params = params.append('orderBy', orderBy);
    params = params.append('orderSort', (orderSort === true) ? 'desc' : 'asc');
    params = params.append('limit', this._limit);

    return this._http.get('/api/admin/interviews/pending', { params: params, headers: headers['headers'] })
      .toPromise()
  }

  /**
   * Get successful placed interviews
   * @param page {number}
   * @param orderBy {string}
   * @param orderSort {string}
   * @param search {string}
   * @return {Promise<Object>}
   */
  public async getSuccessfulPlacedInterviews(page, orderBy, orderSort, search): Promise<any> {
    const headers = await this.createAuthorizationHeader();

    let params = new HttpParams();
    params = params.append('page', page);
    params = params.append('search', search);
    params = params.append('orderBy', orderBy);
    params = params.append('orderSort', (orderSort === true) ? 'desc' : 'asc');
    params = params.append('limit', this._limit);

    return this._http.get('/api/admin/interviews/placed', { params: params, headers: headers['headers'] })
      .toPromise()
  }

  /**
   * Get successful placed interviews
   * @param page {number}
   * @param orderBy {string}
   * @param orderSort {string}
   * @param search {string}
   * @return {Promise<Object>}
   */
  public async getApplicantsAwaiting(page, orderBy, orderSort, search): Promise<any> {
    const headers = await this.createAuthorizationHeader();

    let params = new HttpParams();
    params = params.append('page', page);
    params = params.append('search', search);
    params = params.append('orderBy', orderBy);
    params = params.append('orderSort', (orderSort === true) ? 'desc' : 'asc');
    params = params.append('limit', this._limit);

    return this._http.get('/api/admin/interviews/awaiting', { params: params, headers: headers['headers'] })
      .toPromise()
  }

  /**
   * Get successful placed interviews
   * @param page {number}
   * @param orderBy {string}
   * @param orderSort {string}
   * @param search {string}
   * @return {Promise<Object>}
   */
  public async getApplicantsShortlist(page, orderBy, orderSort, search): Promise<any> {
    const headers = await this.createAuthorizationHeader();

    let params = new HttpParams();
    params = params.append('page', page);
    params = params.append('search', search);
    params = params.append('orderBy', orderBy);
    params = params.append('orderSort', (orderSort === true) ? 'desc' : 'asc');
    params = params.append('limit', this._limit);

    return this._http.get('/api/admin/interviews/shortlist', { params: params, headers: headers['headers'] })
      .toPromise()
  }

  /**
   * Get all applicants for admin
   * @param search {string}
   * @param page {string}
   * @param orderBy {string}
   * @param orderSort {string}
   * @param csv {boolean}
   * @return {Promise<Object>}
   */
  public async getAdminAllApplicants(search: string, page, csv, orderBy, orderSort): Promise<AdminInterviewItemList> {
    const headers = await this.createAuthorizationHeader();

    let params = new HttpParams();
    params = params.append('search', search);
    params = params.append('orderBy', orderBy);
    params = params.append('orderSort', (orderSort === true) ? 'desc' : 'asc');
    params = params.append('page', page);
    params = params.append('limit', this._limit);
    params = params.append('csv', csv);

    return this._http.get('/api/admin/interviews/', { params: params, headers: headers['headers'] })
      .toPromise()
  }

  /**
   * Get all applicants for admin
   * @param search {string}
   * @param page {string}
   * @param orderBy {string}
   * @param orderSort {string}
   * @return {Promise<Object>}
   */
  public async getAdminSwitchAccounts(search: string, page, orderBy, orderSort): Promise<AdminInterviewItemList> {
    const headers = await this.createAuthorizationHeader();

    let params = new HttpParams();
    params = params.append('page', page);
    params = params.append('limit', this._limit);
    params = params.append('search', search);
    params = params.append('orderBy', orderBy);
    params = params.append('orderSort', (orderSort === true) ? 'DESC' : 'ASC');

    return this._http.get('/api/admin/switch_user/', { params: params, headers: headers['headers'] })
      .toPromise()
  }

  /**
   * Get all admins
   * @param search {string}
   * @param page {number}
   * @param orderBy {string}
   * @param orderSort {string}
   * @return {Promise<Object>}
   */
  public async getAllAdmins(search: string, page, orderBy, orderSort): Promise<any> {
    const headers = await this.createAuthorizationHeader();

    let params = new HttpParams();
    params = params.append('search', search);
    params = params.append('orderBy', orderBy);
    params = params.append('orderSort', (orderSort === true) ? 'desc' : 'asc');
    params = params.append('page', page);
    params = params.append('limit', this._limit);

    return this._http.get('/api/admin/manage_user/', { params: params, headers: headers['headers'] })
      .toPromise()
  }

  /**
   * Get candidate video status for admin
   * @return {Promise<Object>}
   */
  public async getAdminVideoStatusCandidate(): Promise<any> {
    const headers = await this.createAuthorizationHeader();

    return this._http.get('/api/admin/settings/', headers)
      .toPromise()
  }

  /**
   * Update candidate video status for admin
   * @param data {boolean}
   * @returns {Promise<any>}
   */
  public async updateAdminVideoStatusCandidate(data): Promise<any> {
    const headers = await this.createAuthorizationHeader();

    return this._http.put('/api/admin/settings/', data, headers)
      .toPromise()
      .catch(this.handleError);
  }

  /**
   * Edit admin
   * @param data {object}
   * @returns {Promise<any>}
   */
  public async editAdmin(data): Promise<any> {
    const headers = await this.createAuthorizationHeader();

    const user = {
      firstName: data.firstName,
      lastName: data.lastName,
      phone: data.phone,
      email: data.email,
      role: data.roles[0]
    };

    return this._http.put('/api/admin/manage_user/' + data.id, user, headers)
      .toPromise()
      .catch(this.handleError);
  }

  /**
   * Delete admin
   * @param id {number}
   * @return {Promise<any|Object>}
   */
  public async deleteAdmin(id: number): Promise<any>{
    const headers = await this.createAuthorizationHeader();

    return this._http.delete('/api/admin/manage_user/' + id, headers)
      .toPromise()
      .catch(this.handleError);
  }

  /**
   * Create new admin
   * @param data {object}
   * @returns {Promise<any>}
   */
  public async createNewAdmin(data): Promise<any> {
    const headers = await this.createAuthorizationHeader();

    const user = {
      firstName: data.firstName,
      lastName: data.lastName,
      phone: data.phone,
      email: data.email,
      role: data.roles[0]
    };

    return this._http.post('/api/admin/manage_user/', user, headers)
      .toPromise()
      .catch(this.handleError);
  }

  /**
   * Get admin logging list
   * @param search {string}
   * @param dateStart {string}
   * @param dateEnd {string}
   * @param orderBy {string}
   * @param orderSort {string}
   * @param page {number}
   * @return {Promise<Object>}
   */
  public async getAdminLogging(search, dateStart, dateEnd, page, orderBy, orderSort): Promise<AdminLoggingList> {
    const headers = await this.createAuthorizationHeader();

    let params = new HttpParams();
    params = params.append('search', search);
    params = params.append('dateStart', dateStart);
    params = params.append('dateEnd', dateEnd);
    params = params.append('orderBy', orderBy);
    params = params.append('orderSort', (orderSort === true) ? 'desc' : 'asc');
    params = params.append('page', page);
    params = params.append('limit', this._limit);

    return this._http.get('/api/admin/logging/', { params: params, headers: headers['headers'] })
      .toPromise()
      .catch(this.handleError);
  }

  /**
   * Remove file for admin
   * @param fieldName {string}
   * @param url {string}
   * @return {Promise<any>}
   */
  public async removeAdminFile(fieldName: string, url: string): Promise<any> {
    const headers = await this.createAuthorizationHeader();

    const data = {
      [fieldName]: {
        'url': url
      }
    };

    return this._http.patch('/api/admin/candidate/candidateId/file', data, headers)
      .toPromise()
      .catch(this.handleError);
  }

  /**
   * Remove file for admin
   * @param data {formData}
   * @return {Promise<any>}
   */
  public async updateAdminFile(data): Promise<any> {
    const headers = await this.createAuthorizationHeader();

    return this._http.post('/api/admin/candidate/candidateId/file', data, headers)
      .toPromise()
      .catch(this.handleError);
  }

  /**
   * Get details profile candidate
   * @param id {string}
   * @return {Promise<AdminCandidateProfile>}
   */
  public async getCandidateProfileDetails(id): Promise<AdminCandidateProfile> {
    const headers = await this.createAuthorizationHeader();

    return this._http.get('/api/admin/candidate/' + id, headers)
      .toPromise()
      .catch(this.handleError);
  }

  /**
   * Update candidate profile
   * @param data {object}
   * @param id {number}
   * @returns {Promise<any>}
   */
  public async updateAdminCandidateProfile(data: AdminCandidateProfileNew, id: number): Promise<any> {
    const headers = await this.createAuthorizationHeader();

    return this._http.put('/api/admin/candidate/' + id, data, headers)
      .toPromise()
      .catch(this.handleError);
  }

  /**
   * Update file for admin
   * @param data {object}
   * @param id {number}
   * @return {Promise<any>}
   */
  public async updateProfileFiles(data, id): Promise<any> {
    const headers = await this.createAuthorizationHeader();

    return this._http.post('/api/admin/candidate/'+ id +'/file', data, headers)
      .toPromise()
      .catch(this.handleError);
  }

  /**
   * Update file for admin
   * @param data {object}
   * @param id {number}
   * @return {Promise<any>}
   */
  public async updateProfileVideo(data, id): Promise<any> {
    const headers = await this.createAuthorizationHeader();

    return this._http.post('/api/admin/candidate/'+ id +'/video', data, headers)
      .toPromise()
      .catch(this.handleError);
  }

  /**
   * Remove candidate file for admin
   * @param fieldName {string}
   * @param url {string}
   * @param id {number}
   * @return {Promise<any>}
   */
  public async removeFile(fieldName: string, url: string, id: number): Promise<any> {
    const headers = await this.createAuthorizationHeader();

    const data = {
      [fieldName]: {
        'url': url
      }
    };

    return this._http.patch('/api/admin/candidate/'+ id +'/file', data, headers)
      .toPromise()
      .catch(this.handleError);
  }

  /**
   * Remove candidate video for admin
   * @param id {number}
   * @return {Promise<any>}
   */
  public async removeVideo(id: number): Promise<any> {
    const headers = await this.createAuthorizationHeader();

    return this._http.delete('/api/admin/candidate/'+ id +'/video', headers)
      .toPromise()
      .catch(this.handleError);
  }
}
