import { Injectable } from '@angular/core';
import { AdminCandidateProfile } from '../../entities/models-admin';
import { SettingsApiService } from './settings-api.service';
import { HttpClient, HttpParams } from '@angular/common/http';
import { AuthService } from './auth.service';
import { Router } from '@angular/router';

@Injectable()
export class CandidateService extends SettingsApiService {

  private _limit = '50';

    constructor(
        protected readonly _http: HttpClient,
        protected readonly _authService: AuthService,
        protected readonly _router: Router
    ) {
        super(_http, _authService, _router);
    }

  /**
   * Get details profile candidate
   * @return {Promise<AdminCandidateProfile>}
   */
  public async getCandidateProfileDetails(): Promise<AdminCandidateProfile> {
    const headers = await this.createAuthorizationHeader();

    return this._http.get<any>('/api/candidate/profile/', headers).toPromise()
      .catch(this.handleError);
  }


  /**
   * Get candidate qualifications
   * @return {Promise<AdminCandidateProfile>}
   */
  public async getCandidateQualification(): Promise<any> {
    const headers = await this.createAuthorizationHeader();

    return this._http.get<any>('/api/candidate/profile/qualifications', headers).toPromise()
      .catch(this.handleError);
  }

  /**
   * Get Candidate Video Status
   * @return {Promise<Object>}
   */
  public async getCandidateVideoStatus(): Promise<any> {
    const headers = await this.createAuthorizationHeader();

    return this._http.get<any>('/api/candidate/settings', headers)
      .toPromise()
      .catch(this.handleError);
  }

  /**
   * Get opportunities job alerts candidate
   * @param {Date} startDate
   * @param {Date} endDate
   * @return {Promise<void>}
   */
  public async getCandidateJobAlertsOpportunities(startDate, endDate): Promise<any> {
    const headers = await this.createAuthorizationHeader();

    let params = new HttpParams();
    params = params.append('startDate', startDate);
    params = params.append('endDate', endDate);

    return this._http.get('/api/candidate/opportunities/jobAlerts', { params: params, headers: headers['headers'] })
      .toPromise()
      .catch(this.handleError);
  }

  /**
   * Remove candidate video
   * @return {Promise<any|Object>}
   */
  public async removeVideo(): Promise<any> {
    const headers = await this.createAuthorizationHeader();

    return this._http.delete('/api/candidate/profile/video', headers)
      .toPromise()
      .catch(this.handleError);
  }

  /**
   * Update candidate profile
   * @param {AdminCandidateProfile} user
   * @return {Promise<void>}
   */
  public async updateCandidateProfile(user): Promise<void> {
    const headers = await this.createAuthorizationHeader();

    return this._http.post('/api/candidate/profile/', user, headers)
      .toPromise()
      .catch(this.handleError);
  }

  /**
   * Create candidate qualification
   * @param {object} data
   * @return {Promise<void>}
   */
  public async createCandidateQualification(data): Promise<any> {
    const headers = await this.createAuthorizationHeader();

    return this._http.post('/api/candidate/profile/qualifications', data, headers)
      .toPromise()
      .catch(this.handleError);
  }

  /**
   * Update candidate qualification
   * @param {object} data
   * @param {number} id
   * @return {Promise<void>}
   */
  public async updateCandidateQualification(data, id): Promise<any> {
    const headers = await this.createAuthorizationHeader();

    return this._http.put('/api/candidate/profile/qualifications/' + id, data, headers)
      .toPromise()
      .catch(this.handleError);
  }

  /**
   * Get Preference Notification
   * @return {Promise<any>}
   */
  public async preferenceNotification(): Promise<any> {
    const headers = await this.createAuthorizationHeader();

    return this._http.get<any>('/api/candidate/preference/notification', headers)
      .toPromise()
      .catch(this.handleError);
  }

  /**
   * Update Preference NotificationEmail
   * @param data
   * @return {Promise<any>}
   */
  public async updatePreferenceNotificationEmail(data): Promise<any> {
    const headers = await this.createAuthorizationHeader();

    return this._http.put('/api/candidate/preference/notification', data, headers)
      .toPromise()
      .catch(this.handleError);
  }

  /**
   * Update Preference NotificationEmail
   * @param data
   * @return {Promise<any>}
   */
  public async updatePreferenceNotificationWhatsapp(data): Promise<any> {
    const headers = await this.createAuthorizationHeader();

    return this._http.put('/api/candidate/preference/notification', data, headers)
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

    return this._http.put('/api/candidate/preference/notification', data, headers)
      .toPromise()
      .catch(this.handleError);
  }

  /**
   * Remove file
   * @param fieldName {string}
   * @param url {string}
   * @return {Promise<any>}
   */
  public async removeFile(fieldName: string, url: string): Promise<any> {
    const headers = await this.createAuthorizationHeader();

    const data = {
      [fieldName]: {
        'url': url
      }
    };

    return this._http.patch('/api/candidate/profile/file', data, headers)
      .toPromise()
      .catch(this.handleError);
  }

  /**
   * Update file
   * @return {Promise<any>}
   */
  public async updateProfileFiles(data): Promise<any> {
    const headers = await this.createAuthorizationHeader();

    return this._http.post('/api/candidate/profile/file', data, headers)
      .toPromise()
      .catch(this.handleError);
  }

  /**
   * Get candidate video
   * @returns {Promise<any>}
   */
  public async getCandidateVideo(): Promise<any> {
    const headers = await this.createAuthorizationHeader();

    return this._http.get<any>('/api/candidate/profile/video', headers)
      .toPromise()
      .catch(this.handleError);
  }

  /**
   * Upload candidate video
   * @param {string} token
   * @returns {Promise<any>}
   */
  public async uploadVideo(token: string): Promise<any> {
    const headers = await this.createAuthorizationHeader();

    return this._http.post('/api/candidate/profile/video', { token }, headers)
      .toPromise()
      .catch(this.handleError);
  }

  /**
   * Get candidate professionally video
   * @return {Promise<any>}
   */
  public async getVideoProfessionally(): Promise<any> {
    const headers = await this.createAuthorizationHeader();

    return this._http.post('/api/candidate/profile/video/request', {}, headers)
      .toPromise()
      .catch(this.handleError);
  }

  /**
   * Get candidate achievement
   * @return {Promise<any>}
   */
  public async getCandidateAchievement(): Promise<any> {
    const headers = await this.createAuthorizationHeader();

    return this._http.get<any>('/api/candidate/profile/achievement', headers)
      .toPromise()
      .catch(this.handleError);
  }

  /**
   * Create candidate achievements
   * @param description {string}
   * @return {Promise<any>}
   */
  public async createCandidateAchievement(description: string): Promise<any> {
    const headers = await this.createAuthorizationHeader();

    const data = {
      'description': description
    };

    return this._http.post('/api/candidate/profile/achievement', data, headers)
      .toPromise()
      .catch(this.handleError);
  }

  /**
   * Delete candidate achievements
   * @param id {number}
   * @return {Promise<any>}
   */
  public async deleteCandidateAchievement(id: number): Promise<any> {
    const headers = await this.createAuthorizationHeader();

    return this._http.delete('/api/candidate/profile/achievement/' + id, headers)
      .toPromise()
      .catch(this.handleError);
  }

  /**
   * Delete candidate qualification
   * @param id {number}
   * @return {Promise<any>}
   */
  public async deleteCandidateQualification(id: number): Promise<any> {
    const headers = await this.createAuthorizationHeader();

    return this._http.delete('/api/candidate/profile/qualifications/' + id, headers)
      .toPromise()
      .catch(this.handleError);
  }

  /**
   * Get edit candidate qualification
   * @param id {number}
   * @return {Promise<any>}
   */
  public async getEditCandidateQualification(id: number): Promise<any> {
    const headers = await this.createAuthorizationHeader();

    return this._http.put('/api/candidate/profile/qualifications/' + id, headers)
      .toPromise()
      .catch(this.handleError);
  }

  /**
   * Update candidate achievement
   * @param id {number}
   * @param description {string}
   * @return {Promise<any>}
   */
  public async updateCandidateAchievement(id: number, description: string): Promise<any> {
    const headers = await this.createAuthorizationHeader();

    const data = {
      'description': description
    };

    return this._http.put('/api/candidate/profile/achievement/' + id, data, headers)
      .toPromise()
      .catch(this.handleError);
  }

  /**
   * Get candidate references
   * @return {Promise<any>}
   */
  public async getCandidateReferences(): Promise<any> {
    const headers = await this.createAuthorizationHeader();

    return this._http.get<any>('/api/candidate/profile/references', headers)
      .toPromise()
      .catch(this.handleError);
  }

  /**
   * Create candidate references
   * @param data {Object}
   * @return {Promise<any>}
   */
  public async createCandidateReferences(data): Promise<any> {
    const headers = await this.createAuthorizationHeader();

    return this._http.post('/api/candidate/profile/references', data, headers)
      .toPromise()
      .catch(this.handleError);
  }

  /**
   * Delete candidate references
   * @param id {number}
   * @return {Promise<any>}
   */
  public async deleteCandidateReferences(id: number): Promise<any> {
    const headers = await this.createAuthorizationHeader();

    return this._http.delete('/api/candidate/profile/references/' + id, headers)
      .toPromise()
      .catch(this.handleError);
  }

  /**
   * Update candidate references
   * @param id {number}
   * @param data {object}
   * @return {Promise<any>}
   */
  public async updateCandidateReferences(id: number, data): Promise<any> {
    const headers = await this.createAuthorizationHeader();

    return this._http.put('/api/candidate/profile/references/' + id, data, headers)
      .toPromise()
      .catch(this.handleError);
  }

  /**
   * Change status candidate
   * @param data {Object}
   * @return {Promise<any>}
   */
  public async changeStatusCandidate(data): Promise<any> {
    const headers = await this.createAuthorizationHeader();

    return this._http.patch('/api/candidate/profile/', data, headers)
      .toPromise()
      .catch(this.handleError);
  }

  /**
   * Get job candidate
   * @param page {number}
   * @return {Promise<any>}
   */
  public async getJobCandidate(page): Promise<any> {
    const headers = await this.createAuthorizationHeader();

    let params = new HttpParams();
    params = params.append('limit', this._limit);
    params = params.append('page', page);

    return this._http.get('/api/candidate/job/', { params: params, headers: headers['headers'] })
      .toPromise()
      .catch(this.handleError);
  }

  /**
   * Get candidate job popup
   * @param id {number}
   * @return {Promise<any>}
   */
  public async getCandidateJob(id: number): Promise<any> {
    const headers = await this.createAuthorizationHeader();

    return this._http.get('/api/candidate/job/' + id, headers)
      .toPromise()
      .catch(this.handleError);
  }

  /**
   * Get candidate job popup
   * @param id {number}
   * @return {Promise<any>}
   */
  public async getCandidateJobInterviewId(id: number): Promise<any> {
    const headers = await this.createAuthorizationHeader();

    return this._http.get('/api/candidate/interviews/' + id, headers)
      .toPromise()
      .catch(this.handleError);
  }

  /**
   * Get candidate job popup clientId
   * @param id {number}
   * @return {Promise<any>}
   */
  public async getCandidateJobClient(id: number): Promise<any> {
    const headers = await this.createAuthorizationHeader();

    return this._http.get('/api/candidate/job/client/' + id, headers)
      .toPromise()
      .catch(this.handleError);
  }

  /**
   * Hide status job by id for Admin
   * @param id {number}
   * @return {Promise<any>}
   */
  public async hideCandidateJob(id: number): Promise<any> {
    const headers = await this.createAuthorizationHeader();

    const data = {
      'hide': true
    };

    return this._http.patch('/api/candidate/job/' + id, data, headers)
      .toPromise()
      .catch(this.handleError);
  }

  /**
   * Create candidate application
   * @param id {number}
   * @return {Promise<any>}
   */
  public async createCandidateApplication(id: number): Promise<any> {
    const headers = await this.createAuthorizationHeader();

    const data = {
      'jobID': id
    };

    return this._http.post('/api/candidate/application/apply', data, headers)
      .toPromise()
      .catch(this.handleError);
  }

  /**
   * Approve job post
   * @param clientId {number}
   * @param jobId {number}
   * @return {Promise<any>}
   */
  public async approveJobPost(jobId: number, clientId: number): Promise<any> {
    const headers = await this.createAuthorizationHeader();

    const data = {
      'clientID': clientId,
      'jobID': jobId
    };

    return this._http.post('/api/candidate/opportunities/approve', data, headers)
      .toPromise()
      .catch(this.handleError);
  }

  /**
   * Approve job post
   * @param jobId {number}
   * @return {Promise<any>}
   */
  public async applyJobAlerts(jobId: number): Promise<any> {
    const headers = await this.createAuthorizationHeader();

    const data = {
      'jobID': jobId
    };

    return this._http.post('/api/candidate/opportunities/jobAlerts/apply', data, headers)
      .toPromise()
      .catch(this.handleError);
  }

  /**
   * Approve job post
   * @param jobId {number}
   * @return {Promise<any>}
   */
  public async declineJobAlerts(jobId: number): Promise<any> {
    const headers = await this.createAuthorizationHeader();

    const data = {
      'jobID': jobId
    };

    return this._http.post('/api/candidate/opportunities/jobAlerts/decline', data, headers)
      .toPromise()
      .catch(this.handleError);
  }

  /**
   * Decline job post
   * @param clientId {number}
   * @param jobId {number}
   * @return {Promise<any>}
   */
  public async declineJobPost(jobId: number, clientId: number): Promise<any> {
    const headers = await this.createAuthorizationHeader();

    const data = {
      'clientID': clientId,
      'jobID': jobId
    };

    return this._http.post('/api/candidate/opportunities/decline', data, headers)
      .toPromise()
      .catch(this.handleError);
  }

  /**
   * Cancel application
   * @param clientId {number}
   * @param jobId {number}
   * @return {Promise<any>}
   */
  public async cancelApplication(jobId: number, clientId: number): Promise<any> {
    const headers = await this.createAuthorizationHeader();

    const data = {
      'clientID': clientId,
      'jobID': jobId
    };

    return this._http.post('/api/candidate/application/cancel', data, headers)
      .toPromise()
      .catch(this.handleError);
  }

  /**
   * Get opportunities
   * @param data {Object}
   * @return {Promise<any|Object>}
   */
  public async getOpportunities(data): Promise<any> {
    const headers = await this.createAuthorizationHeader();

    let params = new HttpParams();
    params = params.append('limit', this._limit);
    params = params.append('startDate', data.dateStart);
    params = params.append('endDate', data.dateEnd);

    return this._http.get('/api/candidate/opportunities/', { params: params, headers: headers['headers'] })
      .toPromise()
      .catch(this.handleError);
  }

  /**
   * Get opportunities new
   * @param data {Object}
   * @param page {number}
   * @return {Promise<any|Object>}
   */
  public async getOpportunitiesNew(data, page): Promise<any> {
    const headers = await this.createAuthorizationHeader();

    let params = new HttpParams();
    params = params.append('startDate', data.dateStart);
    params = params.append('endDate', data.dateEnd);
    params = params.append('page', page);
    params = params.append('limit', this._limit);

    return this._http.get('/api/candidate/opportunities/jobAlerts/new', { params: params, headers: headers['headers'] })
      .toPromise()
      .catch(this.handleError);
  }

  /**
   * Get opportunities declined
   * @param data {Object}
   * @param page {number}
   * @return {Promise<any|Object>}
   */
  public async getOpportunitiesDeclined(data, page): Promise<any> {
    const headers = await this.createAuthorizationHeader();

    let params = new HttpParams();
    params = params.append('startDate', data.dateStart);
    params = params.append('endDate', data.dateEnd);
    params = params.append('page', page);
    params = params.append('limit', this._limit);

    return this._http.get('/api/candidate/opportunities/jobAlerts/decline', { params: params, headers: headers['headers'] })
      .toPromise()
      .catch(this.handleError);
  }

  /**
   * Get opportunities missed
   * @param data {Object}
   * @param page {number}
   * @return {Promise<any|Object>}
   */
  public async getOpportunitiesMissed(data, page): Promise<any> {
    const headers = await this.createAuthorizationHeader();

    let params = new HttpParams();
    params = params.append('startDate', data.dateStart);
    params = params.append('endDate', data.dateEnd);
    params = params.append('page', page);
    params = params.append('limit', this._limit);

    return this._http.get('/api/candidate/opportunities/jobAlerts/expired', { params: params, headers: headers['headers'] })
      .toPromise()
      .catch(this.handleError);
  }

  /**
   * Get opportunities application
   * @param data {Object}
   * @return {Promise<any|Object>}
   */
  public async getOpportunitiesApplication(data): Promise<any> {
    const headers = await this.createAuthorizationHeader();

    let params = new HttpParams();
    params = params.append('limit', this._limit);
    params = params.append('startDate', data.dateStart);
    params = params.append('endDate', data.dateEnd);


    return this._http.get('/api/candidate/application/', { params: params, headers: headers['headers'] })
      .toPromise()
      .catch(this.handleError);
  }

  /**
   * Get applications awaiting
   * @param data {Object}
   * @param page {number}
   * @return {Promise<any|Object>}
   */
  public async getOpportunitiesAwaiting(data, page): Promise<any> {
    const headers = await this.createAuthorizationHeader();

    let params = new HttpParams();
    params = params.append('startDate', data.dateStart);
    params = params.append('endDate', data.dateEnd);
    params = params.append('page', page);
    params = params.append('limit', this._limit);

    return this._http.get('/api/candidate/application/awaiting', { params: params, headers: headers['headers'] })
      .toPromise()
      .catch(this.handleError);
  }
  /**
   * Get Interviews Request
   * @param data {Object}
   * @param page {number}
   * @return {Promise<any|Object>}
   */
  public async getInterviewsRequest(data, page): Promise<any> {
    const headers = await this.createAuthorizationHeader();

    let params = new HttpParams();
    params = params.append('startDate', data.dateStart);
    params = params.append('endDate', data.dateEnd);
    params = params.append('page', page);
    params = params.append('limit', this._limit);
    params = params.append('status', data.status);

    return this._http.get('/api/candidate/interviews/', { params: params, headers: headers['headers'] })
      .toPromise()
      .catch(this.handleError);
  }

  /**
   * Get applications successful
   * @param data {Object}
   * @param page {number}
   * @return {Promise<any|Object>}
   */
  public async getOpportunitiesSuccessful(data, page): Promise<any> {
    const headers = await this.createAuthorizationHeader();

    let params = new HttpParams();
    params = params.append('limit', this._limit);
    params = params.append('startDate', data.dateStart);
    params = params.append('endDate', data.dateEnd);
    params = params.append('page', page);
    params = params.append('limit', this._limit);

    return this._http.get('/api/candidate/application/successful', { params: params, headers: headers['headers'] })
      .toPromise()
      .catch(this.handleError);
  }

  /**
   * Get applications declined
   * @param data {Object}
   * @param page {number}
   * @return {Promise<any|Object>}
   */
  public async getOpportunitiesDecline(data, page): Promise<any> {
    const headers = await this.createAuthorizationHeader();

    let params = new HttpParams();
    params = params.append('limit', this._limit);
    params = params.append('startDate', data.dateStart);
    params = params.append('endDate', data.dateEnd);
    params = params.append('page', page);
    params = params.append('limit', this._limit);

    return this._http.get('/api/candidate/application/decline', { params: params, headers: headers['headers'] })
      .toPromise()
      .catch(this.handleError);
  }

  /**
   * Get candidate dashboard
   * @param limit {string}
   * @return {Promise<any|Object>}
   */
  public async getCandidateDashboard(limit: string = ''): Promise<any> {
    const headers = await this.createAuthorizationHeader();

    let params = new HttpParams();
    params = params.append('limit', this._limit);

    return this._http.get('/api/candidate/dashboard', { params: params, headers: headers['headers'] })
      .toPromise()
      .catch(this.handleError);
  }
}
