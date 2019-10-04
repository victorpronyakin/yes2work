import { Injectable } from '@angular/core';
import { CanActivate, ActivatedRouteSnapshot, RouterStateSnapshot, Router } from '@angular/router';
import { CandidateService } from '../services/candidate.service';


@Injectable()
export class CheckCandidateCompleteGuard implements CanActivate {

  constructor (
    private readonly _router: Router,
    private readonly _candidateService: CandidateService
  ) {}

  canActivate(route: ActivatedRouteSnapshot, state: RouterStateSnapshot): Promise<boolean> {
    return new Promise(async (resolve) => {
      const data = await this._candidateService.getCandidateProfileDetails();
      if (data) {
        if (
          data.profile.percentage > 50
          && data.profile.copyOfID && data.profile.copyOfID[0]
          && (data.allowVideo === true || data.profile.video)
        ) {
          resolve(true);
        } else {
          this._router.navigate(['/candidate/profile_details']);
          resolve(false);
        }
      } else {
        resolve(false);
      }
    });
  }

  canActivateChild(route: ActivatedRouteSnapshot, state: RouterStateSnapshot): Promise<boolean> {
    return new Promise(async (resolve) => {
      const data = await this._candidateService.getCandidateProfileDetails();
      if (data) {
        if (
          data.profile.percentage > 50
          && data.profile.copyOfID && data.profile.copyOfID[0]
          && (data.allowVideo === true || data.profile.video)
        ) {
          resolve(true);
        } else {
          this._router.navigate(['/candidate/profile_details']);
          resolve(false);
        }
      } else {
        resolve(false);
      }
    });
  }
}
