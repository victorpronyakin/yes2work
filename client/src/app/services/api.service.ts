import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { BusinessUser, CandidateUser} from '../../entities/models';
import { SettingsApiService } from './settings-api.service';
import { AuthService } from './auth.service';
import 'rxjs/add/observable/fromPromise';
import 'rxjs/add/observable/throw';
import 'rxjs/add/operator/catch';
import { Router } from '@angular/router';

@Injectable()
export class ApiService extends SettingsApiService {

  constructor(
    protected readonly _http: HttpClient,
    protected readonly _authService: AuthService,
    protected readonly _router: Router
  ) {
    super(_http, _authService, _router);
  }

  /**
  * Request on Resetting Password
  * @param { email } email
  * @return {Promise<void>}
  */
  public forgotPassword(email): Promise<any> {
    const headers = new HttpHeaders ();
    headers.append('Content Type', 'application/json');

    return this._http.post('/api/user/reset_password', {'email': email}, { headers: headers })
      .toPromise()
      .catch(this.handleError);
  }

  /**
   * Reactivated profile
   * @param email {string}
   * @returns {Promise<any|Object>}
   */
  public reactivateAccount (email): Promise<any> {
    const headers = new HttpHeaders ();
    headers.append('Content Type', 'application/json');

    return this._http.post('/api/user/candidate_reactivate', {'email': email}, { headers: headers })
      .toPromise()
      .catch(this.handleError);
  }

  /**
   * Check token
   * @param token
   * @return {Promise<any>}
   */
  public checkToken(token): Promise<any>{

    return this._http.get('/api/user/reset_password', {params: {'token': token}})
      .toPromise()
      .catch(this.handleError);
  }

  /**
   * Create Business or Candidate user
   * @param {BusinessUser | CandidateUser} user
   * @return {Promise<void>}
   */
  public async createUser (user: BusinessUser | CandidateUser): Promise<any> {
      const headers = new HttpHeaders ();
      headers.append('Content Type', 'application/json');

      return this._http.post('api/user/', user, {headers:headers})
      .toPromise()
      .catch(this.handleError);
  }

  /**
   * Reset password
   * @param token
   * @param password
   * @param verifyPassword
   * @return {Promise<any>}
   */
  public resetPassword(token, password, verifyPassword): Promise<any>{
    const headers = new HttpHeaders();
    headers.append('Content Type', 'application/json');

    const data = {
      "token": token,
      "password": password,
      "verifyPassword": verifyPassword
    };

    return this._http.put('/api/user/reset_password', data, {headers: headers})
      .toPromise()
      .catch(this.handleError);
  }

  /**
   * Reset ReferFriend
   * @param friends {array}
   * @param refer_email {string}
   * @return {Promise<any>}
   */
  public sendReferFriend(friends, refer_email): Promise<any>{
    const headers = new HttpHeaders();
    headers.append('Content Type', 'application/json');

    const data = {
      emails: friends,
      refer_email: refer_email
    };

    return this._http.post('/api/user/refer_friend', data, {headers: headers})
      .toPromise()
      .catch(this.handleError);
  }

  /**
   *
   * @param data
   * @return {Promise<void|Object>}
   */
  public async sendDemoData (data): Promise<any> {
    const headers = await this.createAuthorizationHeader();

    return this._http.put('/api/candidate/profile/', data, headers)
      .toPromise()
      .catch(this.handleError);
  }

}
