import { Injectable } from '@angular/core';
import { IAccessToken } from '../../entities/models';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { AuthService } from './auth.service';
import { Router } from '@angular/router';

@Injectable()
export class SettingsApiService {

  constructor(
    protected readonly _http: HttpClient,
    protected readonly _authService: AuthService,
    protected readonly _router: Router
  ) { }

  /**
   * Generate headers
   * @return {{headers: HttpHeaders}}
   */
  public async createAuthorizationHeader(): Promise<Object> {
    const expire = Number(localStorage.getItem('expires_in'));
    const date = Math.round(Number(new Date().getTime() / 1000));
    let token = localStorage.getItem('access_token');

    if(token){
      if (date >= expire && token) {
        const refresh = await this.refreshToken();

        const date = Math.round(Number(new Date().getTime() / 1000 + refresh.expires_in));
        localStorage.setItem('refresh_token', refresh.refresh_token);
        localStorage.setItem('access_token', refresh.access_token);
        localStorage.setItem('id', refresh.id);
        localStorage.setItem('expires_in', date.toString());
        localStorage.setItem('role', refresh.role);
        token = refresh.access_token;
      }
      return {
        headers: new HttpHeaders({'Authorization': 'Bearer ' + token})
      }
    }
    else {
      this._authService.logout();
      this._router.navigate(['/login']);
    }
  }

  /**
   * Call refresh token
   * @return {Promise<IAccessToken>}
   */
  public async refreshToken (): Promise<IAccessToken> {
    return await this._authService.refreshToken();
  }

  /**
   * Change password
   * @param old_password
   * @param new_password
   * @param confirm_password
   * @return {Promise<any|Object>}
   */
  public async changePassword(old_password: string, new_password: string, confirm_password: string): Promise<void> {
    const headers = await this.createAuthorizationHeader();
    const data = {
      'old_password': old_password,
      'new_password': new_password,
      'confirm_password': confirm_password
    };

    return this._http.put('/api/preference/change_password', data, headers)
      .toPromise()
      .catch(this.handleError);
  }

  /**
   * Error errorHandler
   * @param {any} e - error
   * @return {void}
   */
  protected errorHandler (e: any): void {
    if (e.status === 401) {
      this._authService.logout();
    }
  }

  protected handleError(error: any): Promise<any> {
    return Promise.reject(error);
  }

}
