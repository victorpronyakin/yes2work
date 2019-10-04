import { Component, OnInit } from '@angular/core';
import { FormControl, FormGroup, Validators } from '@angular/forms';
import { AdminBusinessAccount } from '../../../../../entities/models-admin';
import { BusinessService } from '../../../../services/business.service';
import { SharedService } from '../../../../services/shared.service';
import { ToastrService } from 'ngx-toastr';
import {} from '@types/googlemaps';
import { SettingsApiService } from '../../../../services/settings-api.service';
import { IMultiSelectOption, IMultiSelectSettings, IMultiSelectTexts } from 'angular-2-dropdown-multiselect';
import { industry } from '../../../../constants/industry.const';
import { phoneCode } from '../../../../constants/phoneCode.const';
import { ValidateNumber } from '../../../../validators/custom.validator';
import { Router } from '@angular/router';

@Component({
  selector: 'app-profile-details',
  templateUrl: './profile-details.component.html',
  styleUrls: ['./profile-details.component.scss']
})
export class ProfileDetailsComponent implements OnInit {
  public businessProfileDetails: AdminBusinessAccount;
  public businessForm: FormGroup;
  public passwordForm: FormGroup;
  public errorUpdate: any;
  public preloaderPage = true;

  public checkEmail: boolean;

  public newCandidateStatus: boolean;
  public jobApproveStatus: boolean;
  public jobDeclineStatus: boolean;
  public candidateApplicantStatus: boolean;
  public candidateDeclineStatus: boolean;

  public newCandidate: number;
  public jobApprove: number;
  public jobDecline: number;
  public candidateApplicant: number;
  public candidateDecline: number;

  public jse: boolean;

  public articlesFirmTextConfigBus: IMultiSelectTexts = {
    checkAll: 'Select all',
    uncheckAll: 'Deselect all',
    checked: 'item selected',
    checkedPlural: 'items selected',
    defaultTitle: 'Industry',
    allSelected: 'All selected',
  };
  public articlesFirmSettingsBus: IMultiSelectSettings = {
    displayAllSelectedText: true,
    selectionLimit: 0,
    showCheckAll: true,
    showUncheckAll: true,
  };
  public optionsModelBus: string[];
  public indistrySelect: IMultiSelectOption[] = industry;
  public phoneCodeStatus;
  public codePh = phoneCode;

  constructor(
    private readonly _businessService: BusinessService,
    private readonly _toastr: ToastrService,
    private readonly _sharedService: SharedService,
    private readonly _settingsApiService: SettingsApiService,
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

    this.businessForm = new FormGroup({
     firstName: new FormControl('', Validators.compose([
         Validators.required,
         Validators.minLength(2),
     ])),
     lastName: new FormControl('', Validators.compose([
         Validators.required,
         Validators.minLength(2),
     ])),
     jobTitle: new FormControl('', Validators.compose([
         Validators.required,
         Validators.minLength(2),
     ])),
     phone: new FormControl('', Validators.compose([
         Validators.required,
         ValidateNumber
     ])),
     email: new FormControl('', Validators.compose([
         Validators.required,
         Validators.email
     ])),
     name: new FormControl('', Validators.compose([
         Validators.required,
         Validators.minLength(2)
     ])),
     address: new FormControl('', Validators.required),
     addressCountry: new FormControl('', Validators.compose([
       Validators.required,
       Validators.minLength(2),
     ])),
     addressState: new FormControl('', Validators.compose([
       Validators.required,
       Validators.minLength(2),
     ])),
     addressZipCode: new FormControl('', Validators.compose([
       Validators.required,
       Validators.minLength(2),
     ])),
     addressCity: new FormControl('', Validators.compose([
       Validators.required,
       Validators.minLength(2),
     ])),
     addressSuburb: new FormControl('', Validators.compose([
       Validators.required,
       Validators.minLength(2),
     ])),
     addressStreet: new FormControl('', Validators.compose([
       Validators.required,
       Validators.minLength(2),
     ])),
     addressStreetNumber: new FormControl('', Validators.compose([
       Validators.required,
       Validators.minLength(1),
     ])),
     addressBuildName: new FormControl(''),
     addressUnit: new FormControl(''),
     industry: new FormControl('', Validators.required),
     jse: new FormControl(''),
     companySize: new FormControl('', Validators.required),
     description: new FormControl('', Validators.compose([
       Validators.required,
       Validators.maxLength(300),
     ]))
    });

    this.getBusinessProfileDetails().then(response => {
      this._sharedService.fetchGoogleAutocompleteDetails(this.businessForm);
    });
  }

  /**
   * jse value check
   */
  public jseValue(field, value): void{
    value = !value;
  }

  /**
   * Get details profile business
   * @return {Promise<void>}
   */
  public async getBusinessProfileDetails(): Promise<void> {
    this.businessProfileDetails = await this._businessService.getBusinessProfile();

    this.businessForm.setValue({
      firstName: this.businessProfileDetails.profile.user.firstName,
      lastName: this.businessProfileDetails.profile.user.lastName,
      jobTitle: this.businessProfileDetails.profile.user.jobTitle,
      phone: this.businessProfileDetails.profile.user.phone,
      email: this.businessProfileDetails.profile.user.email,
      name: this.businessProfileDetails.profile.company.name,
      address: this.businessProfileDetails.profile.company.address,
      addressCountry: this.businessProfileDetails.profile.company.addressCountry,
      addressState: this.businessProfileDetails.profile.company.addressState,
      addressZipCode: this.businessProfileDetails.profile.company.addressZipCode,
      addressCity: this.businessProfileDetails.profile.company.addressCity,
      addressSuburb: this.businessProfileDetails.profile.company.addressSuburb,
      addressStreet: this.businessProfileDetails.profile.company.addressStreet,
      addressStreetNumber: this.businessProfileDetails.profile.company.addressStreetNumber,
      addressBuildName: this.businessProfileDetails.profile.company.addressBuildName,
      addressUnit: this.businessProfileDetails.profile.company.addressUnit,
      industry: this.businessProfileDetails.profile.company.industry,
      jse: this.businessProfileDetails.profile.company.jse,
      companySize: this.businessProfileDetails.profile.company.companySize,
      description: this.businessProfileDetails.profile.company.description
    });

    this.jse = this.businessProfileDetails.profile.company.jse;

    this.checkEmail = this.businessProfileDetails.notification.notifyEmail;

    this.newCandidateStatus = this.businessProfileDetails.notification.newCandidateStatus;
    this.jobApproveStatus = this.businessProfileDetails.notification.jobApproveStatus;
    this.jobDeclineStatus = this.businessProfileDetails.notification.jobDeclineStatus;
    this.candidateApplicantStatus = this.businessProfileDetails.notification.candidateApplicantStatus;
    this.candidateDeclineStatus = this.businessProfileDetails.notification.candidateDeclineStatus;
    this.newCandidate = this.businessProfileDetails.notification.newCandidate;
    this.jobApprove = this.businessProfileDetails.notification.jobApprove;
    this.jobDecline = this.businessProfileDetails.notification.jobDecline;
    this.candidateApplicant = this.businessProfileDetails.notification.candidateApplicant;
    this.candidateDecline = this.businessProfileDetails.notification.candidateDecline;

    this.preloaderPage = false;
  }

  /**
   * Update profile business
   * @return {Promise<void>}
   */
  public async updateBusinessProfile(): Promise<void> {

    this.businessProfileDetails.profile.company.address = this.businessForm.value.address;
    this.businessProfileDetails.profile.company.addressCountry = this.businessForm.value.addressCountry;
    this.businessProfileDetails.profile.company.addressState = this.businessForm.value.addressState;
    this.businessProfileDetails.profile.company.addressZipCode = this.businessForm.value.addressZipCode;
    this.businessProfileDetails.profile.company.addressCity = this.businessForm.value.addressCity;
    this.businessProfileDetails.profile.company.addressSuburb = this.businessForm.value.addressSuburb;
    this.businessProfileDetails.profile.company.addressStreet = this.businessForm.value.addressStreet;
    this.businessProfileDetails.profile.company.addressStreetNumber = this.businessForm.value.addressStreetNumber;
    this.businessProfileDetails.profile.company.addressBuildName = this.businessForm.value.addressBuildName;
    this.businessProfileDetails.profile.company.addressUnit = this.businessForm.value.addressUnit;
    this.businessProfileDetails.profile.company.companySize = (this.businessForm.value.companySize === null) ? null : Number(this.businessForm.value.companySize);
    this.businessProfileDetails.profile.company.description = this.businessForm.value.description;
    this.businessProfileDetails.profile.company.industry = (this.businessForm.value.industry === undefined) ? null : this.businessForm.value.industry;
    this.businessProfileDetails.profile.company.jse = this.jse;
    this.businessProfileDetails.profile.company.name = this.businessForm.value.name;
    this.businessProfileDetails.profile.user.email = this.businessForm.value.email;
    this.businessProfileDetails.profile.user.firstName = this.businessForm.value.firstName;
    this.businessProfileDetails.profile.user.jobTitle = this.businessForm.value.jobTitle;
    this.businessProfileDetails.profile.user.lastName = this.businessForm.value.lastName;
    this.businessProfileDetails.profile.user.phone = this.businessForm.value.phone;

    if (this.businessForm.valid) {
      try {
        await this._businessService.updateBusinessProfile(this.businessProfileDetails.profile);
        this._toastr.success('Your details have been updated');
        this._router.navigate(['business/jobs/add']);
      } catch (err) {
        this._sharedService.showRequestErrors(err);
      }
    }
  }

  public updatePreferenceNotificationEmail(field, value) {
    value = !value;
    const data = { 'notifyEmail': value };
    /*this.checkEmail = !this.checkEmail;*/
    this._businessService.updatePreferenceNotificationEmail(data);
  }

  /**
   * Update preferences business
   * @param field
   * @param value
   */
  public updatePreferenceNotificationStatus(field, value){
    value = !value;
    const data = {[field]: value};

    this._businessService.updatePreferenceNotification(data);
  }

  /**
   * Update preferences business
   * @param field
   * @param value
   */
  public updatePreferenceNotification(field, value){
    if(this[field] === value){
      this[field] = null;
    }
    else{
      this[field] = value;
    }
    const data = {[field]:this[field]};

    this._businessService.updatePreferenceNotification(data);
  }

  /**
   * Update password
   * @return {Promise<void>}
   */
  public async updatePassword(): Promise<void> {

    if(this.passwordForm.valid) {
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
