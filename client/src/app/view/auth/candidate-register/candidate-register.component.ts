import { Component, ElementRef, OnInit, ViewChild } from '@angular/core';
import { FormControl, FormGroup, Validators } from '@angular/forms';
import { CandidateUser, Role } from '../../../../entities/models';
import { ApiService } from '../../../services/api.service';
import { Router } from '@angular/router';
import { SharedService } from '../../../services/shared.service';
import { ValidateIdNumber, ValidateNumber, CustomValidateIdNumber } from '../../../validators/custom.validator';
import { INgxMyDpOptions } from 'ngx-mydatepicker';

@Component({
  selector: 'app-candidate-register',
  templateUrl: './candidate-register.component.html',
  styleUrls: ['./candidate-register.component.scss']
})


export class CandidateRegisterComponent implements OnInit {

  @ViewChild('register') public register : ElementRef;
  @ViewChild('friendForm') public friendForm : ElementRef;

  public candidateRegisterForm: FormGroup;
  public referFriendForm: FormGroup;
  public check = true;
  public emailCheck = false;
  public errorEmail: string;
  public errorRegistration = [];
  public buttonPreloader = false;
  public modalActiveClose: any;

  public afterRegistration = true;
  public afterReg = false;
  public terms = false;

  public beforeRegistration = false;

  public saicaStatus = false;
  public checkSaica = true;
  public ageCheck = false;

  public myOptionsDate: INgxMyDpOptions = { dateFormat: 'yyyy/mm/dd' };
  public model: any = { date: { year: 2018, month: 10, day: 9 } };
  public checkReferFriendObj = {
    firstCheck: true,
    secondCheck: true,
    thirdCheck: true,
    fourthCheck: true,

    firstCheckFriend: true
  };

  public invalidColor = false;

  public referFriendsArray = [];

  public blokArr = [ false, false, false, false, false, false ];
  public userEmailRefer = '';

  constructor(
    private readonly _apiService: ApiService,
    private _router: Router,
    private _sharedService: SharedService
  ) {
    this._sharedService.checkSidebar = false;
  }

  ngOnInit() {
    window.scrollTo(0, 0);
    this.candidateRegisterForm = new FormGroup({
      firstName: new FormControl('', [
        Validators.required,
        Validators.minLength(2)
      ]),
      lastName: new FormControl('', [
        Validators.required,
        Validators.minLength(2)
      ]),
      phone: new FormControl('', [
        Validators.required,
        ValidateNumber
      ]),
      email: new FormControl('', [
        Validators.required,
        Validators.email
      ]),
      idNumber: new FormControl('', [
        Validators.required,
        ValidateIdNumber
      ]),
      password: new FormControl('', [
        Validators.required,
        Validators.minLength(6)
      ]),
      verifyPassword: new FormControl('', [
        Validators.required,
        this.matchOtherValidator('password'),
        Validators.minLength(6)
      ])
    });

    this.referFriendForm = new FormGroup({
      firstFriend: new FormControl(''),
      firstEmail: new FormControl(''),
      secondFriend: new FormControl(''),
      secondEmail: new FormControl(''),
      thirdFriend: new FormControl(''),
      thirdEmail: new FormControl(''),
      fourthFriend: new FormControl(''),
      fourthEmail: new FormControl('')
    });

  }

  /**
   * open terms popup
   */
  public openPopup(){
    this.terms = true;
    this.afterReg = false;
    this.afterRegistration = false;
  }

  /**
   * close terms popup
   */
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
   * Send ReferFriends
   */
  public async sendReferFriends() {
    this.referFriendsArray = [];
    const check = this.customValidationReferFriends();
    if (check) {
      if (this.referFriendsArray.length > 0) {
       this.blokArr[4] = false;
       try {
         await this._apiService.sendReferFriend(this.referFriendsArray, this.userEmailRefer);
         this.referFriendForm.reset( {
           firstFriend: '',
           firstEmail: '',
           secondFriend: '',
           secondEmail: '',
           thirdFriend: '',
           thirdEmail: '',
           fourthFriend: '',
           fourthEmail: ''
         });
         this.referFriendsArray = [];
         this.blokArr[5] = true;
       }
       catch (err) {
         this._sharedService.showRequestErrors(err);
       }
      } else {
       this.blokArr[4] = true;
        this.blokArr[5] = false;
      }
    } else {
      this.blokArr[4] = false;
    }
  }

  /**
   * Custom validation refer friends
   * @returns {boolean}
   */
  public customValidationReferFriends() {
    const reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
    let count = 0;

    if (this.referFriendForm.controls.firstFriend.value.length || this.referFriendForm.controls.firstEmail.value.length) {
      if (this.referFriendForm.controls.firstFriend.value.length < 2) {
        this.blokArr[0] = true;
        count++;
      } else if (!reg.test(this.referFriendForm.controls.firstEmail.value)) {
        this.blokArr[0] = true;
        count++;
      } else {
        this.referFriendsArray.push({
          name: this.referFriendForm.controls.firstFriend.value,
          email: this.referFriendForm.controls.firstEmail.value
        });
        this.blokArr[0] = false;
      }
    } else {
      this.blokArr[0] = false;
    }

    if (this.referFriendForm.controls.secondFriend.value.length || this.referFriendForm.controls.secondEmail.value.length) {
      if (this.referFriendForm.controls.secondFriend.value.length < 2) {
        this.blokArr[1] = true;
        count++;
      } else if (!reg.test(this.referFriendForm.controls.secondEmail.value)) {
        this.blokArr[1] = true;
        count++;
      } else {
        this.referFriendsArray.push({
          name: this.referFriendForm.controls.secondFriend.value,
          email: this.referFriendForm.controls.secondEmail.value
        });
        this.blokArr[1] = false;
      }
    } else {
      this.blokArr[1] = false;
    }

    if (this.referFriendForm.controls.thirdFriend.value.length || this.referFriendForm.controls.thirdEmail.value.length) {
      if (this.referFriendForm.controls.thirdFriend.value.length < 2) {
        this.blokArr[2] = true;
        count++;
      } else if (!reg.test(this.referFriendForm.controls.thirdEmail.value)) {
        this.blokArr[2] = true;
        count++;
      } else {
        this.referFriendsArray.push({
          name: this.referFriendForm.controls.thirdFriend.value,
          email: this.referFriendForm.controls.thirdEmail.value
        });
        this.blokArr[2] = false;
      }
    } else {
      this.blokArr[2] = false;
    }

    if (this.referFriendForm.controls.fourthFriend.value.length || this.referFriendForm.controls.fourthEmail.value.length) {
      if (this.referFriendForm.controls.fourthFriend.value.length < 2) {
        this.blokArr[3] = true;
        count++;
      } else if (!reg.test(this.referFriendForm.controls.fourthEmail.value)) {
        this.blokArr[3] = true;
        count++;
      } else {
        this.referFriendsArray.push({
          name: this.referFriendForm.controls.fourthFriend.value,
          email: this.referFriendForm.controls.fourthEmail.value
        });
        this.blokArr[3] = false;
      }
    } else {
      this.blokArr[3] = false;
    }

    if (count > 0) {
      return false;
    } else {
      return true;
    }
  }

  /**
   * Check Terms
   */
  public cutCheckTerms() {
    if (this.check === false) {
      this.check = true;
    } else {
      this.check = false;
    }
  }

  /**
   * Create candidate
   */
  public createCandidateUser () {
    this.buttonPreloader = true;
    const user = new CandidateUser({
      role: Role.candidateRole,
      firstName: this.candidateRegisterForm.value.firstName,
      lastName: this.candidateRegisterForm.value.lastName,
      phone: this.candidateRegisterForm.value.phone,
      email: this.candidateRegisterForm.value.email,
      idNumber: this.candidateRegisterForm.value.idNumber,
      password: this.candidateRegisterForm.value.password,
      verifyPassword: this.candidateRegisterForm.value.verifyPassword
    });

    if (this.candidateRegisterForm.valid) {
      const checkAgeForID = CustomValidateIdNumber(this.candidateRegisterForm.controls.idNumber);
      if (checkAgeForID['invalidIdNumber'] === undefined) {
        this.ageCheck = false;
        this.userEmailRefer = this.candidateRegisterForm.value.email;
        this._apiService.createUser(user).then( data => {
            this.emailCheck = false;
            this.buttonPreloader = false;
            this.terms = false;
            const date = Math.round(Number(new Date().getTime() / 1000 + data.expires_in));
            localStorage.setItem('access_token', data.access_token);
            localStorage.setItem('expires_in', date.toString());
            localStorage.setItem('refresh_token', data.refresh_token);
            localStorage.setItem('role', data.role);
            localStorage.setItem('id', data.id);
            this._router.navigate(['/candidate/profile_details']);
            /*this.afterReg = true;
            this.afterRegistration = false;
            this.beforeRegistration = true;*/
        }).catch((err) => {

            this.buttonPreloader = false;
            this.afterRegistration = true;
            this.beforeRegistration = false;
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
      } else {
        this.errorRegistration = [];
        this.candidateRegisterForm.reset();
        this.ageCheck = true;
        this.buttonPreloader = false;
      }
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
    };
  }

}
