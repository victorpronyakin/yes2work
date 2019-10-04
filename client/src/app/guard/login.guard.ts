import { Injectable } from '@angular/core';
import { ActivatedRouteSnapshot, CanActivate, Router, RouterStateSnapshot } from '@angular/router';
import { Role } from '../../entities/models';
import { AuthService } from '../services/auth.service';
import { SharedService } from '../services/shared.service';

@Injectable()
export class LoginGuard implements CanActivate {

  constructor (
    private readonly _authService: AuthService,
    private readonly _sharedService: SharedService,
    private readonly _router: Router
  ) {

  }

  canActivate(route: ActivatedRouteSnapshot, state: RouterStateSnapshot): boolean {
    const token = localStorage.getItem('access_token');
    const tokenAdmin = localStorage.getItem('access_token_admin');
    const role = localStorage.getItem('role');
    if (tokenAdmin) {
      const admin = {
        access_token: localStorage.getItem('access_token_admin'),
        expires_in: localStorage.getItem('expires_in_admin'),
        refresh_token: localStorage.getItem('refresh_token_admin'),
        role: localStorage.getItem('role_admin'),
        id: localStorage.getItem('id_admin')
      };
      this._sharedService.goToAdminProfile(admin);
    } else if(token){
        switch (role) {
            case Role.clientRole:
                this._router.navigate(['/business']);
                return false;
            case Role.candidateRole:
                this._router.navigate(['/candidate']);
                return false;
            case Role.adminRole:
                this._router.navigate(['/admin']);
                return false;
            case Role.superAdminRole:
                this._router.navigate(['/admin']);
                return false;
            default:
               return true;
        }
    }
    return true;
  }

}
