import { Injectable } from '@angular/core';
import { CanActivate, ActivatedRouteSnapshot, RouterStateSnapshot, Router } from '@angular/router';
import { Role } from '../../entities/models';
import { AuthService } from '../services/auth.service';

@Injectable()
export class RoleGuard implements CanActivate {
    constructor (
        private readonly _authService: AuthService,
        private readonly _router: Router
    ) {

    }

    canActivate(route: ActivatedRouteSnapshot, state: RouterStateSnapshot): boolean {
      const role = localStorage.getItem('role');
      const roles = route.data['roles'];
      if(role && roles){
        if (roles.indexOf(role)>=0){
          return true;
        }
        else{
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
              this._authService.logout();
              return false;
          }
        }

      }
      this._authService.logout();
      return false;
    }

    canActivateChild(route: ActivatedRouteSnapshot, state: RouterStateSnapshot): boolean {
        const role = localStorage.getItem('role');
        const roles = route.data['roles'];
        if(role && roles){
            if (roles.indexOf(role)>=0){
                return true;
            }
            else{
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
                        this._authService.logout();
                        return false;
                }
            }

        }
        this._authService.logout();
        return false;
    }
}
