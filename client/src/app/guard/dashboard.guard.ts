import { Injectable } from '@angular/core';
import { CanActivate, ActivatedRouteSnapshot, RouterStateSnapshot, Router } from '@angular/router';
import { BusinessService } from '../services/business.service';
import { AuthService } from '../services/auth.service';
import { AdminService } from '../services/admin.service';

@Injectable()
export class DashboardGuard implements CanActivate {

  constructor (
    private readonly _router: Router,
    private readonly _businessService: BusinessService,
    private readonly _adminService: AdminService,
    private readonly _authService: AuthService,
  ) {}

  canActivate(route: ActivatedRouteSnapshot, state: RouterStateSnapshot): Promise<boolean> {
    return new Promise(async (resolve) => {
      const roles = route.data['roles'];
       if (roles.indexOf('ROLE_CLIENT')>=0) {
        try {
          await this._businessService.getBusinessProfile();
        }
        catch (err) {
          this._authService.logout();
        }
      } else if (roles.indexOf('ROLE_ADMIN')>=0) {
         try {
           await this._adminService.getAdminProfile();
         }
         catch (err) {
           this._authService.logout();
         }
       }
      resolve(true);
    });
  }

  canActivateChild(route: ActivatedRouteSnapshot, state: RouterStateSnapshot): Promise<boolean> {
    return new Promise(async (resolve) => {
      const roles = route.data['roles'];
      if (roles.indexOf('ROLE_CLIENT')>=0) {
        try {
          await this._businessService.getBusinessProfile();
        }
        catch (err) {
          this._authService.logout();
        }
      }
      resolve(true);
    });
  }
}
