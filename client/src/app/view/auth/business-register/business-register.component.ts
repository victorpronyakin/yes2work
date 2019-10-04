import { Component, OnInit } from '@angular/core';
import { FormControl, FormGroup, Validators } from '@angular/forms';
import { BusinessUser, Role } from '../../../../entities/models';
import { ApiService } from '../../../services/api.service';
import { Router } from '@angular/router';
import { SharedService } from '../../../services/shared.service';
import { ValidateNumber } from '../../../validators/custom.validator';

@Component({
  selector: 'app-business-register',
  templateUrl: './business-register.component.html',
  styleUrls: ['./business-register.component.scss']
})
export class BusinessRegisterComponent implements OnInit {

  public check: boolean = true;
  public emailCheck: boolean = false;
  public errorEmail: string;
  public errorRegistration = [];
  public buttonPreloader = false;

  public afterRegistration = true;
  public afterReg = false;
  public terms = false;

  public businessRegisterForm: FormGroup;

  constructor(
    private readonly _apiService: ApiService,
    private _router: Router,
    private _sharedService: SharedService
  ) {
    this._sharedService.checkSidebar = false;
  }

  ngOnInit() {
    window.scrollTo(0, 0);
    this.businessRegisterForm = new FormGroup({
      firstName: new FormControl('', [
        Validators.required,
        Validators.minLength(2)
      ]),
      lastName: new FormControl('', [
        Validators.required,
        Validators.minLength(2)
      ]),
      email: new FormControl('', Validators.compose([
        Validators.required,
        Validators.email
      ])),
      phone: new FormControl('', [
        Validators.required,
        ValidateNumber
      ]),
      password: new FormControl('', [
        Validators.required,
        Validators.minLength(6)
      ]),
      verifyPassword: new FormControl('', [
        Validators.required,
        this.matchOtherValidator('password'),
        Validators.minLength(6)
      ]),
      jobTitle: new FormControl('', [
        Validators.required,
        Validators.minLength(2)
      ]),
      companyName: new FormControl('', [
        Validators.required,
        Validators.minLength(2)
      ])
    });
  }

  public openPopup(){
    this.terms = true;
    this.afterReg = false;
    this.afterRegistration = false;
  }

  public closePopup(){
    this.terms = false;
    this.afterReg = false;
    this.afterRegistration = true;
  }

  /**
   * Check terms
   */
  public checkTerms(){
    this.check = false;
    this.terms = false;
    this.afterReg = false;
    this.afterRegistration = true;
  }

  /**
   * Check terms
   */
  public cutCheckTerms(){
    if (this.check === false) {
      this.check = true;
    } else {
      this.check = false;
    }
  }

  /**
   * Creare business user
   */
  public createBusinessUser (): void {
    this.buttonPreloader = true;
    const user = new BusinessUser({
      role: Role.clientRole,
      firstName: this.businessRegisterForm.value.firstName,
      lastName: this.businessRegisterForm.value.lastName,
      email: this.businessRegisterForm.value.email,
      phone: this.businessRegisterForm.value.phone,
      password: this.businessRegisterForm.value.password,
      verifyPassword: this.businessRegisterForm.value.verifyPassword,
      jobTitle: this.businessRegisterForm.value.jobTitle,
      companyName: this.businessRegisterForm.value.companyName,
    });

    if (
      this.businessRegisterForm.value.firstName &&
      this.businessRegisterForm.value.lastName &&
      this.businessRegisterForm.value.email &&
      this.businessRegisterForm.value.phone &&
      this.businessRegisterForm.value.password &&
      this.businessRegisterForm.value.verifyPassword &&
      this.businessRegisterForm.value.jobTitle &&
      this.businessRegisterForm.value.companyName &&
      this.check === false
    ) {
      this._apiService.createUser(user).then(() => {
        this.emailCheck = false;

        this.terms = false;
        this.afterReg = true;
        this.afterRegistration = false;

        this.buttonPreloader = false;
      }).catch((err) => {
        this.buttonPreloader = false;
        this.afterRegistration = true;
        this.errorRegistration = [];
        this.errorEmail = '';
        if(err.error.message){
          this.errorRegistration.push(err.error.message);
        }
        else if(err.error.error){
          err.error.error.forEach((errorText) => {
            if(errorText === 'email already use'){
              this.emailCheck = true;
              this.errorEmail = errorText;
            }
            else{
              this.errorRegistration.push(errorText);
            }
          });
        }
      });
    }
    else {
      this.buttonPreloader = false;
    }
  }

  /**
   * Back to login page
   */
  public backToLogin(): void{
    this._router.navigate(['/login']);
  }

  /**
   * Password validator
   * @param otherControlName
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
