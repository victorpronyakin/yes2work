import { Injectable } from '@angular/core';
import { CanActivate, ActivatedRouteSnapshot, RouterStateSnapshot, Router, CanActivateChild } from '@angular/router';
import { AuthService } from '../services/auth.service';

@Injectable()
export class AuthGuard implements CanActivate, CanActivateChild  {

  constructor (
    private _auth: AuthService,
    private _router: Router
  ) { }

  canActivate(route: ActivatedRouteSnapshot, state: RouterStateSnapshot): boolean {
    const token = localStorage.getItem('access_token');
    if (!token) {
      localStorage.setItem('preRouterLink', route['_routerState'].url);
      this._router.navigate(['/login']);
      return false;
    }
    return true;
  }

  canActivateChild (route: ActivatedRouteSnapshot, state: RouterStateSnapshot): boolean {
    const token = localStorage.getItem('access_token');
    if (!token) {
      localStorage.setItem('preRouterLink', route['_routerState'].url);
      this._router.navigate(['/login']);
      return false;
    }
    return true;
  }

}
