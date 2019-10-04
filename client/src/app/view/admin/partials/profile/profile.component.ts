import { Component, OnInit } from '@angular/core';
import { FormControl, FormGroup, Validators } from '@angular/forms';
import { AdminProfile } from '../../../../../entities/models-admin';
import { AdminService } from '../../../../services/admin.service';
import { ToastrService } from 'ngx-toastr';
import { SharedService } from '../../../../services/shared.service';
import { SettingsApiService } from '../../../../services/settings-api.service';
import { ValidateNumber } from '../../../../validators/custom.validator';

@Component({
  selector: 'app-profile',
  templateUrl: './profile.component.html',
  styleUrls: ['../../admin.component.scss']
})
export class ProfileComponent implements OnInit {
  public adminProfileForm: FormGroup;
  public adminProfile: AdminProfile;

  public passwordForm: FormGroup;

  public notifyEmail: boolean;
  public candidateSign: number;
  public clientSign: number;
  public candidateDeactivate: number;
  public jobNew: number;
  public jobChange: number;
  public candidateFile: number;
  public candidateRequestVideo: number;
  public interviewSetUp: number;

  public preloaderPage = true;

  constructor(
    private readonly _settingsApiService: SettingsApiService,
    private readonly _adminService: AdminService,
    private readonly _toastr: ToastrService,
    private readonly _sharedService: SharedService,
  ) {
    this._sharedService.checkSidebar = false;
  }

  ngOnInit() {
    window.scrollTo(0, 0);

    this.adminProfileForm = new FormGroup({
      firstName: new FormControl('', [Validators.required, Validators.minLength(2)]),
      lastName: new FormControl('', [Validators.required, Validators.minLength(2)]),
      phone: new FormControl('', [
        Validators.required,
        ValidateNumber
      ]),
      email: new FormControl('', Validators.compose([
        Validators.required,
        Validators.email
      ])),
    });

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

    this.getAdminProfile();
  }

  /**
   * Get Admin Profile
   * @returns {Promise<void>}
   */
  public async getAdminProfile(): Promise<void> {
    try {
      this.adminProfile = await this._adminService.getAdminProfile();

      this.adminProfileForm.setValue({
        firstName: this.adminProfile.profile.firstName,
        lastName: this.adminProfile.profile.lastName,
        phone: this.adminProfile.profile.phone,
        email: this.adminProfile.profile.email,
      });

      this.candidateDeactivate = this.adminProfile.notification.candidateDeactivate;
      this.candidateFile = this.adminProfile.notification.candidateFile;
      this.candidateRequestVideo = this.adminProfile.notification.candidateRequestVideo;
      this.candidateSign = this.adminProfile.notification.candidateSign;
      this.clientSign = this.adminProfile.notification.clientSign;
      this.interviewSetUp = this.adminProfile.notification.interviewSetUp;
      this.jobChange = this.adminProfile.notification.jobChange;
      this.jobNew = this.adminProfile.notification.jobNew;
      this.notifyEmail = this.adminProfile.notification.notifyEmail;

      this.preloaderPage = false;
    }
    catch (err) {
      this._sharedService.showRequestErrors(err);
    }
  }

  /**
   * Update Admin Profile
   * @returns {Promise<void>}
   */
  public async updateAdminProfile(): Promise<void> {
    this.adminProfile.profile.firstName = this.adminProfileForm.value.firstName;
    this.adminProfile.profile.lastName = this.adminProfileForm.value.lastName;
    this.adminProfile.profile.phone = this.adminProfileForm.value.phone;
    this.adminProfile.profile.email = this.adminProfileForm.value.email;
    try {
      await this._adminService.updateAdminProfile(this.adminProfile);
      this._toastr.success('Profile has been updated');
    } catch (err) {
      this._sharedService.showRequestErrors(err);
    }
  }

  /**
   * Update preference notification email
   */
  public async updatePreferenceNotificationEmail(field, value): Promise<void> {
    value = !value;
    const data = {[field]: value};

    try {
      await this._adminService.updatePreferenceNotification(data);
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
  public updatePreferenceNotification(field, value): void {

    try {
      if(this[field] === value){
        this[field] = null;
      }
      else{
        this[field] = value;
      }
      const data = {[field]:this[field]};

      this._adminService.updatePreferenceNotification(data);
    }
    catch (err) {
      this._sharedService.showRequestErrors(err);
    }
  }

  /**
   * Update admin password
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
