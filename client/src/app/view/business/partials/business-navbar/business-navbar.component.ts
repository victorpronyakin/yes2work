import { AfterContentInit, Component, OnInit, ViewChild } from '@angular/core';
import { AuthService } from '../../../../services/auth.service';
import { NgbModal } from '@ng-bootstrap/ng-bootstrap';
import { CookieService } from 'ngx-cookie-service';
import { BusinessService } from '../../../../services/business.service';
import { SharedService } from '../../../../services/shared.service';
import { Router } from '@angular/router';

@Component({
  selector: 'app-business-navbar',
  templateUrl: './business-navbar.component.html',
  styleUrls: ['./business-navbar.component.scss']
})
export class BusinessNavbarComponent implements OnInit, AfterContentInit {

  public modalActiveClose: any;
  public admin: any;

  @ViewChild('steps') private _steps;

  constructor(
    private readonly _authService: AuthService,
    private readonly _modalService: NgbModal,
    private readonly _businessService: BusinessService,
    private readonly _cookieService: CookieService,
    private readonly _router: Router,
    public readonly sharedService: SharedService
  ) {
    this.admin = {
      access_token: localStorage.getItem('access_token_admin'),
      expires_in: localStorage.getItem('expires_in_admin'),
      refresh_token: localStorage.getItem('refresh_token_admin'),
      role: localStorage.getItem('role_admin'),
      id: localStorage.getItem('id_admin')
    };
  }

  ngOnInit() {
  }

  ngAfterContentInit() {
    if (this._cookieService.get('firstPopUp_' + Number(localStorage.getItem('id'))) !== 'true') {
      this.getStatusFirstPopup();
    }
  }

  /**
   * Get status first popup
   * @return {Promise<void>}
   */
  public async getStatusFirstPopup(): Promise<void> {
    try {
      const result = await this._businessService.getStatusFirstPopup();
      if(result.firstPopUp === true){
        this._cookieService.set('firstPopUp_' + Number(localStorage.getItem('id')), 'true', 365, '/');
      }
      else{
          setTimeout(() => {
              this.openVerticallyCentered(this._steps);
          }, 1000);
      }
    }
    catch (err) {
      this.sharedService.showRequestErrors(err);
    }
  }

  public openSidebar(): void {
    this.sharedService.checkSidebar = true;
  }

  /**
   * opens popup
   * @param content
   */
  public openVerticallyCentered(content: any) {
    this.modalActiveClose = this._modalService.open(content, { centered: true, size: 'lg', windowClass: 'xlModal' });
  }

  /**
   * logs user out
   * @returns void
   */
  public logout(): void {
    sessionStorage.removeItem('session' + localStorage.getItem('id'));
    this._authService.logout();
  }

}
