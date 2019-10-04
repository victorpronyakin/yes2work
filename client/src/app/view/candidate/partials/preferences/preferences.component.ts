import { Component, OnInit } from '@angular/core';
import { FormControl, FormGroup, Validators } from '@angular/forms';
import { ToastrService } from 'ngx-toastr';
import { SettingsApiService } from '../../../../services/settings-api.service';
import { CandidateService } from '../../../../services/candidate.service';
import { SharedService } from '../../../../services/shared.service';
import { Router } from '@angular/router';
import {AdminCandidateProfile} from "../../../../../entities/models-admin";

@Component({
  selector: 'app-preferences',
  templateUrl: './preferences.component.html',
  styleUrls: ['./preferences.component.scss']
})
export class PreferencesComponent implements OnInit {

  public passwordForm: FormGroup;

  public checkEmail: boolean;
  public checkSMS: boolean;

  public interviewRequestStatus: boolean;
  public newJobLoadedStatus: boolean;
  public jobEndingSoonStatus: boolean;
  public videoApproveStatus: boolean;
  public documentApproveStatus: boolean;
  public reminderProfileStatus: boolean;
  public applicationDeclineStatus: boolean;

  public interviewRequest: number;
  public newJobLoaded: number;
  public jobEndingSoon: number;
  public reminderProfile: number;
  public applicationDecline: number;
  public statePopupStatus = false;


  public candidateProfileDetails: AdminCandidateProfile;
  public visibilityLooking = false;
  public checkLooking: boolean;
  public videoUploadPopups = false;
  public visibleActivePopup = false;

  public obj;

  public preloaderPage = true;

  public whatsAppLink: string;

  constructor(
    private readonly _settingsApiService: SettingsApiService,
    private readonly _toastr: ToastrService,
    private readonly _candidateService: CandidateService,
    public readonly _sharedService: SharedService,
    private readonly _router: Router
  ) {
    this._sharedService.checkSidebar = false;
  }

  ngOnInit() {
    window.scrollTo(0, 0);
    this.passwordForm = new FormGroup({
      old_password: new FormControl('', [
        Validators.required,
        Validators.minLength(5)
      ]),
      new_password: new FormControl('', [
        Validators.required,
        Validators.minLength(6)
      ]),
      confirm_password: new FormControl('', [
        Validators.required,
        this.matchOtherValidator('new_password'),
        Validators.minLength(6)
      ])
    });
    this.preferanceNotification().then(() => {
      this.getCandidateProfile();
    });

    this._sharedService.progressBar = Number(localStorage.getItem('progressBar'));

    const whatsAppText = encodeURI("Please save this number as \"CAs Online\" and press send to complete the setup.");
    if(this.isMobile()){
      this.whatsAppLink = 'https://api.whatsapp.com/send?phone=85252432313&text='+whatsAppText;
    }
    else{
      this.whatsAppLink = 'https://web.whatsapp.com/send?phone=85252432313&text='+whatsAppText;
    }
  }

  /**
   * Get candidate profile
   * @returns {Promise<void>}
   */
  public async getCandidateProfile(): Promise<any> {
    try {
      this.candidateProfileDetails = await this._candidateService.getCandidateProfileDetails();
        if(this.candidateProfileDetails.profile.percentage < 50 || !this.candidateProfileDetails.profile.copyOfID ||
        !this.candidateProfileDetails.profile.copyOfID[0] ||
        !this.candidateProfileDetails.profile.copyOfID[0].approved ||
        (this.candidateProfileDetails.allowVideo === false && !this.candidateProfileDetails.profile.video) ||
        (this.candidateProfileDetails.allowVideo === false && this.candidateProfileDetails.profile.video && this.candidateProfileDetails.profile.video.approved === false))
        {
          this.checkLooking = false;
          this.visibilityLooking = true;
      } else {
        this.checkLooking = this.candidateProfileDetails.profile.looking;
        this._sharedService.progressBar = this.candidateProfileDetails.profile.percentage;
      }
    }
    catch (err) {
      this._sharedService.showRequestErrors(err);
    }
  }

  /**
   * Change status candidate
   * @param field {string}
   * @param value {boolean}
   */
  public changeStatusCandidate(field: string, value: boolean){
      let error = true;
      if(this.candidateProfileDetails.profile.percentage < 50) {
          this._toastr.error('Your profile needs to be 50% complete');
          error = false;
          if(field === 'looking'){
              this.checkLooking = false;
          }
      }
      if(!this.candidateProfileDetails.profile.copyOfID || this.candidateProfileDetails.profile.copyOfID.length === 0){
          this._toastr.error('Upload a copy of your ID in Edit Profile');
          error = false;
          if(field === 'looking'){
              this.checkLooking = false;
          }
      }
      if(this.candidateProfileDetails.profile.copyOfID[0] && !this.candidateProfileDetails.profile.copyOfID[0].approved){
          this._toastr.error('Copy of your ID files is not approved by the administrator');
          error = false;
          if(field === 'looking'){
              this.checkLooking = false;
          }
      }
      if (!this.candidateProfileDetails.profile.video && this.candidateProfileDetails.allowVideo === false) {
          this._toastr.error('You need to upload video');
          error = false;
          if(field === 'looking'){
              this.checkLooking = false;
          }
      }
      if (this.candidateProfileDetails.profile.video && !this.candidateProfileDetails.profile.video.approved && this.candidateProfileDetails.allowVideo === false) {
          this._toastr.error('Your video is not approved by the administrator');
          error = false;
          if(field === 'looking'){
              this.checkLooking = false;
          }
      }
      if (error === true) {
          if (field === 'looking' && value === false) {
              this.closeLookingPopup(true, false);
          } else {
              const data = {[field]:value};

              this._candidateService.changeStatusCandidate(data).then(data => {
                  this.checkLooking = data.looking;
                  if (field === 'looking') {
                      this._toastr.success('Your profile is now active');
                  }
              }).catch(err => {
                  this._sharedService.showRequestErrors(err);
              });
          }
      }
  }


  /**
   * Status looking popup
   * @param value
   * @param check
   */
  public closeLookingPopup(value, check) {
    this.videoUploadPopups = value;
    this.checkLooking = check;
  }

  /**
   * Send request looking job
   * @param field
   * @param value
   */
  public lookingJobToggle(field: string, value: boolean) {
    this.checkLooking = false;
    const data = {[field]:value};

    this._candidateService.changeStatusCandidate(data).then(data => {
      this.checkLooking = data.looking;
      this._toastr.error('Your profile is now disabled');
    }).catch(err => {
      this._sharedService.showRequestErrors(err);
    });
  }

  /**
   * Step to next page
   */
  public stepNextPage(): void {
    this._router.navigate(['/candidate/dashboard']);
  }

  /**
   * Select change router
   * @param url
   */
  public routerApplicants(url): void {
    this._router.navigate([url]);
  }

  /**
   * Detected Mobile
   * @returns {boolean}
   */
  public isMobile() {
    if( navigator.userAgent.match(/Android/i)
      || navigator.userAgent.match(/webOS/i)
      || navigator.userAgent.match(/iPhone/i)
      || navigator.userAgent.match(/iPad/i)
      || navigator.userAgent.match(/iPod/i)
      || navigator.userAgent.match(/BlackBerry/i)
      || navigator.userAgent.match(/Windows Phone/i)
    ){
      return true;
    }
    else {
      return false;
    }
  }

  /**
   * Update preferences business
   * @param field
   * @param value
   */
  public updatePreferenceNotificationStatus(field, value){
    value = !value;
    const data = {[field]: value};

    this._candidateService.updatePreferenceNotification(data);
  }

  /**
   * Close popup after click WhatsApp
   */
  public statePopup(){
    this.checkSMS = false;
    this.statePopupStatus = !this.statePopupStatus;
  }

  /**
   * Close popup after click WhatsApp
   */
  public closePopup() {
    this.statePopupStatus = !this.statePopupStatus;
  }

  /**
   * Preference notification
   * @return {Promise<void>}
   */
  public async preferanceNotification(): Promise<any> {
    try {
      const data = await this._candidateService.preferenceNotification();
      this.applicationDecline = data.notification.applicationDecline;
      this.applicationDeclineStatus = data.notification.applicationDeclineStatus;
      this.documentApproveStatus = data.notification.documentApproveStatus;
      this.interviewRequest = data.notification.interviewRequest;
      this.interviewRequestStatus = data.notification.interviewRequestStatus;
      this.jobEndingSoon = data.notification.jobEndingSoon;
      this.jobEndingSoonStatus = data.notification.jobEndingSoonStatus;
      this.newJobLoaded = data.notification.newJobLoaded;
      this.newJobLoadedStatus = data.notification.newJobLoadedStatus;
      this.checkEmail = data.notification.notifyEmail;
      this.checkSMS = data.notification.notifySMS;
      this.reminderProfileStatus = data.notification.reminderProfileStatus;
      this.reminderProfile = data.notification.reminderProfile;

      this.preloaderPage = false;
    }
    catch (err) {
      this._sharedService.showRequestErrors(err);
    }
  }

  /**
   * Update preference notification email
   */
  public updatePreferenceNotificationEmail(field, value) {
    value = !value;
    const data = { 'notifyEmail': value };
    this._candidateService.updatePreferenceNotificationEmail(data);
  }

  /**
   * Update preference notification SMS
   */
  public updatePreferenceNotificationSMS(field, value) {
    value = !value;
    const data = { 'notifySMS': value};
    this._candidateService.updatePreferenceNotificationWhatsapp(data);

    // if (value === true){
    //   this.statePopupStatus = true;
    // }
    // else{
    // }
  }

  /**
   * Update preference notification whatsapp
   */
  public async updatePreferenceNotificationWhatsappPopup(field, value): Promise<void> {
    const data = { 'notifySMS': value};
    try{
      await this._candidateService.updatePreferenceNotificationWhatsapp(data);
    }
    catch (err) {
      this._sharedService.showRequestErrors(err);
    }
  }

  /**
   * Upload preference notification
   * @param field {string}
   * @param value {string}
   */
  public updatePreferenceNotification(field, value){
    if(this[field] === value){
      this[field] = null;
    }
    else{
      this[field] = value;
    }
    const data = {[field]:this[field]};

    this._candidateService.updatePreferenceNotification(data);
  }

  /**
   * Update password
   * @return {Promise<void>}
   */
  public async updatePassword(): Promise<void> {

    try {
      await this._settingsApiService.changePassword(
        this.passwordForm.value.old_password,
        this.passwordForm.value.new_password,
        this.passwordForm.value.confirm_password
      );

      this._toastr.success('Password has been changed');

      this.passwordForm = new FormGroup({
        old_password: new FormControl('', [
          Validators.required,
          Validators.minLength(5)
        ]),
        new_password: new FormControl('', [
          Validators.required,
          Validators.pattern('((?=.*\\d)(?=.*[a-z]).{6,30})')
        ]),
        confirm_password: new FormControl('', [
          Validators.required,
          this.matchOtherValidator('new_password'),
          Validators.minLength(6)
        ])
      });

    } catch (err) {
        this._sharedService.showRequestErrors(err);
    }
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
