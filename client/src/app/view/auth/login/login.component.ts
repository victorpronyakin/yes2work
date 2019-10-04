import { Component, OnInit } from '@angular/core';
import { AuthService } from '../../../services/auth.service';
import { FormControl, FormGroup, Validators } from '@angular/forms';
import { Router } from '@angular/router';
import { Role } from '../../../../entities/models';
import { ApiService } from "../../../services/api.service";
import { SharedService } from '../../../services/shared.service';

@Component({
  selector: 'app-login',
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.scss']
})
export class LoginComponent implements OnInit {

  public loginForm: FormGroup;
  public forgotPasswordForm: FormGroup;
  public errorRegistration: any;

  public checkForgot = true;
  public showPatForgot = false;
  public notAccount = false;
  public showPat = false;
  public deactiveProfile = false;
  public activeProfile = false;

  public buttonPreloader = false;

  constructor(
    private readonly _authService: AuthService,
    private readonly _apiService: ApiService,
    private readonly _router: Router,
    private readonly _sharedService: SharedService
  ) {
    this._sharedService.checkSidebar = false;
  }

  ngOnInit() {
    window.scrollTo(0, 0);
    this.loginForm = new FormGroup({
      login: new FormControl('', [
        Validators.required,
        Validators.email
      ]),
      password: new FormControl('', [
        Validators.required,
        Validators.minLength(5)
      ])
    });

    this.forgotPasswordForm = new FormGroup({
      email: new FormControl('', [
        Validators.required,
        Validators.email
      ])
    })
  }

  /**
   * Back to login page
   */
  public backToLogin(): void{
    this.checkForgot = true;
    this.showPatForgot = false;
    this.notAccount = false;
    this.showPat = false;
    this.deactiveProfile = false;
    this.activeProfile = false;

    this.errorRegistration = null;
    this.loginForm.reset();
    this.forgotPasswordForm.reset();
  }

  /**
   * Transition forgot
   */
  transitionForgot() {
    this.checkForgot = false;
    this.showPatForgot = true;
    this.notAccount = false;
    this.showPat = false;
    this.deactiveProfile = false;
    this.activeProfile = false;

    this.errorRegistration = null;
    this.loginForm.reset();
  }

  /**
   * Back to login
   */
  backLogin() {
    this.checkForgot = true;
    this.showPatForgot = false;
    this.notAccount = false;
    this.showPat = false;
    this.deactiveProfile = false;
    this.activeProfile = false;

    this.errorRegistration = null;
    this.forgotPasswordForm.reset();
  }

  /**
   * Back to landing
   */
  backLanding(){
    this._router.navigate(['/']);
  }

  /**
   * Forgot password
   */
  public forgotPassword(){
    this.buttonPreloader = true;
    this._apiService.forgotPassword(this.forgotPasswordForm.value.email)
      .then(data => {

      this.checkForgot = false;
      this.showPatForgot = false;
      this.notAccount = false;
      this.deactiveProfile = false;
      this.activeProfile = false;
      this.showPat = true;

      this.forgotPasswordForm.reset();
      this.buttonPreloader = false;

    }).catch(err => {
      if (err.error.error && err.error.error === 'user not enabled') {
          this.checkForgot = false;
          this.showPatForgot = false;
          this.notAccount = true;
          this.showPat = false;
          this.deactiveProfile = false;
          this.activeProfile = false;
      }
      else {
          this.errorRegistration = err.error.error_description
      }
      this.errorRegistration = err.error.error;
      this.buttonPreloader = false;
    });
  }


  /**
   * Login
   * @return {void}
   */
  public login (): void {
    this.buttonPreloader = true;
    if (this.loginForm.value.login && this.loginForm.value.password) {
      this._authService.auth(this.loginForm.value.login, this.loginForm.value.password)
        .then(data => {

          const date = Math.round(Number(new Date().getTime() / 1000 + data.expires_in));
          localStorage.setItem('access_token', data.access_token);
          localStorage.setItem('expires_in', date.toString());
          localStorage.setItem('refresh_token', data.refresh_token);
          localStorage.setItem('role', data.role);
          localStorage.setItem('id', data.id);
          const preLink = localStorage.getItem('preRouterLink');
          if(!preLink) {
            switch (data.role) {
              case Role.clientRole:
                this._router.navigate(['/business']);
                break;
              case Role.candidateRole:
                this._router.navigate(['/candidate']);
                break;
              case Role.adminRole:
                this._router.navigate(['/admin']);
                break;
              case Role.superAdminRole:
                this._router.navigate(['/admin']);
                break;
              default:
                this._authService.logout();
            }
          } else {
              localStorage.removeItem('preRouterLink');
              this._router.navigateByUrl(preLink);
          }
          this.buttonPreloader = false;
        }).catch(err => {
          this.buttonPreloader = false;
          if (err.error.error_description === 'User Awaiting approval') {
            this.checkForgot = false;
            this.showPatForgot = false;
            this.notAccount = true;
            this.showPat = false;
            this.deactiveProfile = false;
            this.activeProfile = false;
          } else if (err.error.error_description === 'User Deactivate') {
            this.checkForgot = false;
            this.showPatForgot = false;
            this.notAccount = false;
            this.showPat = false;
            this.deactiveProfile = true;
            this.activeProfile = false;
          }
          else {
            this.errorRegistration = err.error.error_description
          }
      });
    }
    else {
      this._sharedService.validateAllFormFields(this.loginForm);
      this.buttonPreloader = false;
    }
  }

  /**
   * Reactivated account
   */
  public async reactivatedAccount(): Promise<any> {
    try{
      await this._apiService.reactivateAccount(this.loginForm.value.login);
      this.checkForgot = false;
      this.showPatForgot = false;
      this.notAccount = false;
      this.showPat = false;
      this.activeProfile = true;
      this.deactiveProfile = false;
    }
    catch (err) {
      console.log(err);
    }

  }

}
