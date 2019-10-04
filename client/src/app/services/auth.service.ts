import { Injectable } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { SettingsService } from './settings.service';
import { Router } from '@angular/router';
import { IAccessToken } from '../../entities/models';

@Injectable()
export class AuthService {

  constructor(
    private readonly _http: HttpClient,
    private readonly _settingService: SettingsService,
    private readonly _router: Router
  ) { }

  /**
   * Get assess token
   * @param {string} username
   * @param {string} password
   * @return {Promise<any>}
   */
  public auth (username: string, password: string): Promise<IAccessToken> {
    let params = new HttpParams();
    params = params.append('client_secret', this._settingService.clientSecret);
    params = params.append('grant_type', this._settingService.grantType);
    params = params.append('client_id', this._settingService.clientId);
    params = params.append('username', username);
    params = params.append('password', password);
    return this._http.get('/oauth/v2/token',  { params: params }).toPromise();
  }

  /**
   * Refresh token
   * @return {void}
   */
  public refreshToken (): Promise<IAccessToken> {
    let params = new HttpParams();
    const refreshToken = localStorage.getItem('refresh_token');
    params = params.append('grant_type', 'refresh_token');
    params = params.append('client_id', this._settingService.clientId);
    params = params.append('client_secret', this._settingService.clientSecret);
    params = params.append('refresh_token', refreshToken);
    return this._http.get('/oauth/v2/token', {params: params})
      .toPromise();
  }

  /**
   * Logout
   * @return {void}
   */
  public logout (): void {
    localStorage.removeItem('access_token');
    localStorage.removeItem('expires_in');
    localStorage.removeItem('id');
    localStorage.removeItem('refresh_token');
    localStorage.removeItem('role');
    localStorage.removeItem('access_token_admin');
    localStorage.removeItem('expires_in_admin');
    localStorage.removeItem('id_admin');
    localStorage.removeItem('refresh_token_admin');
    localStorage.removeItem('role_admin');
    this._router.navigate(['/login']);
  }

}
