import { Component, Input, OnInit } from '@angular/core';
import { SharedService } from '../../../../services/shared.service';
import { BusinessService } from '../../../../services/business.service';
import { CookieService } from 'ngx-cookie-service';
import { Router } from '@angular/router';

@Component({
  selector: 'app-business-first-steps-popup',
  templateUrl: './business-first-steps-popup.component.html',
  styleUrls: ['./business-first-steps-popup.component.scss']
})
export class BusinessFirstStepsPopupComponent implements OnInit {

  @Input() closePopup;
  public stepsVideo = 1;
  public checkStatus = false;

  constructor(
    private readonly _businessService: BusinessService,
    private readonly _sharedService: SharedService,
    private readonly _cookieService: CookieService,
    private readonly _router: Router
  ) {
    this._sharedService.checkSidebar = false;
  }

  ngOnInit() {
  }

  /**
   * Change status first popup
   */
  public changeStatus(): void{
    this.checkStatus = !this.checkStatus;
  }

  /**
   *
   * @return {Promise<void>}
   */
  public async setStatusFirstPopUp(): Promise<void> {
    const id = Number(localStorage.getItem('id'));
    const data = this._cookieService.get('firstPopupForBusiness_' + id);
    if (this.checkStatus === true) {
      try {
        await this._businessService.setStatusFirstPopUp(this.checkStatus);
        this.closePopup();
        this._router.navigate(['/business/dashboard']);
        this._cookieService.set('firstPopUp_' + id, 'true', 365, '/');
      }
      catch (err) {
        this._sharedService.showRequestErrors(err);
      }
    }
    else {
      this.closePopup();
      if (data === 'true') {
        this._router.navigate(['/business/dashboard']);
      }
      else {
        this._cookieService.set('firstPopupForBusiness_' + id, 'true', 365, '/');
        this._router.navigate(['/business/my_account']);
      }
    }
  }

  /**
   * Change steps
   * @param step {number}
   */
  public stepsChange(step: number): void {
    this.stepsVideo = step;
  }

}
