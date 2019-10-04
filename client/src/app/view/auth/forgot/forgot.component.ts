import { Component, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { FormControl, FormGroup, Validators } from '@angular/forms';
import { ApiService } from '../../../services/api.service';

@Component({
  selector: 'app-forgot',
  templateUrl: './forgot.component.html',
  styleUrls: ['./forgot.component.scss']
})
export class ForgotComponent implements OnInit {

  public token: any;
  public resetPasswordForm: FormGroup;

  errorRegistration: any;
  showResetPass: boolean = false;
  validToken: boolean = false;
  public buttonPreloader = false;

  constructor(
    private activatedRoute: ActivatedRoute,
    private _apiService: ApiService,
    private _router: Router
  ) { }

  ngOnInit() {
    window.scrollTo(0, 0);
    this.resetPasswordForm = new FormGroup({
      password: new FormControl('', [
        Validators.required,
        Validators.minLength(5)
      ]),
      verifyPassword: new FormControl('', [
        Validators.required,
        this.matchOtherValidator('password'),
        Validators.minLength(5)
      ])
    });

    this.activatedRoute.queryParams.subscribe(params => {
      localStorage.setItem('access_token', params['token']);
      this.token = params['token'];
    });

    this._apiService.checkToken(this.token).then(dataToken => {
      this.validToken = true;
    }).catch(err => {
      this._router.navigate(['/']);
    });

  }

  /**
   * reset password
   */
  public resetPassword(){
    this.buttonPreloader = true;
    if (this.resetPasswordForm.value.password && this.resetPasswordForm.value.verifyPassword) {
      this._apiService.resetPassword(this.token, this.resetPasswordForm.value.password, this.resetPasswordForm.value.verifyPassword)
        .then(data => {
          this.showResetPass = true;
          this.buttonPreloader = false;
        }).catch(err => {
        this.buttonPreloader = false;
        this.errorRegistration = err.error.error;
      });
    }
    this.buttonPreloader = false;
  }

  /**
   * back to login page
   */
  public backToLogin(){
    this._router.navigate(['/login']);
  }

  /**
   * password validation
   * @param otherControlName {string}
   * @return {(control:FormControl)=>(null|{matchOther: boolean})}
   */
  public matchOtherValidator (otherControlName: string) {

    let thisControl: FormControl;
    let otherControl: FormControl;

    return function matchOtherValidate (control: FormControl) {

      if (!control.parent) {
        return null;
      }

      // Initializing the validator.
      if (!thisControl) {
        thisControl = control;
        otherControl = control.parent.get(otherControlName) as FormControl;
        if (!otherControl) {
          throw new Error('matchOtherValidator(): other control is not found in parent group');
        }
        otherControl.valueChanges.subscribe(() => {
          thisControl.updateValueAndValidity();
        });
      }

      if (!otherControl) {
        return null;
      }

      if (otherControl.value !== thisControl.value) {
        return {
          matchOther: true
        };
      }

      return null;

    }

  }
}
