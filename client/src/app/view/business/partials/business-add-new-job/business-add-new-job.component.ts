import { Component, ElementRef, HostListener, NgZone, OnInit, ViewChild } from '@angular/core';
import { INgxMyDpOptions } from 'ngx-mydatepicker';
import { FormControl, FormGroup, Validators } from '@angular/forms';
import { AdminBusinessAccount } from '../../../../../entities/models-admin';
import { BusinessService } from '../../../../services/business.service';
import { ToastrService } from 'ngx-toastr';
import { SharedService } from '../../../../services/shared.service';
import { articles } from '../../../../constants/articles.const';
import { BusinessJob } from '../../../../../entities/models';
import { IMultiSelectOption, IMultiSelectSettings, IMultiSelectTexts } from 'angular-2-dropdown-multiselect';
import { Router } from '@angular/router';
import { NgbModal } from '@ng-bootstrap/ng-bootstrap';
import {} from '@types/googlemaps';
import { MapsAPILoader } from '@agm/core';
import { industry } from '../../../../constants/industry.const';
import { closureDateValidator, jobClosureDateValidator } from '../../../../validators/custom.validator';
import { LabelType, Options } from 'ng5-slider';

@Component({
  selector: 'app-business-add-new-job',
  templateUrl: './business-add-new-job.component.html',
  styleUrls: ['./business-add-new-job.component.scss']
})
export class BusinessAddNewJobComponent implements OnInit {
  @ViewChild('content') private content : ElementRef;
  @ViewChild('sendEmailPopup') public sendEmailPopup: ElementRef;
  public myOptions: INgxMyDpOptions = { dateFormat: 'yyyy/mm/dd' };
  public businessJobsForm: FormGroup;
  public currentBusinessProfile = new AdminBusinessAccount({});
  public articles = articles;
  public businessJobObject;
  public candidatesMatchingCriteria = 0;
  public articlesFirmPredefined: string[] = [];
  public articlesFirmSelectedName: string[] = [];
  public articlesFirmSettings: IMultiSelectSettings = {
    displayAllSelectedText: true,
    selectionLimit: 0,
    showCheckAll: true,
    showUncheckAll: true,
  };
  public articlesFirmOptions: IMultiSelectOption[] = [];
  public articlesFirmTextConfig: IMultiSelectTexts = {
    checkAll: 'Select all',
    uncheckAll: 'Deselect all',
    checked: 'item selected',
    checkedPlural: 'items selected',
    defaultTitle: 'Articles firm',
    allSelected: 'Articles firm - All selected',
  };
  public switchSteps = true;
  public preloaderPage = true;
  public closureDate: any;
  public jobClosureDate: any;
  public modalActiveClose: any;
  public componentForm = {
    street_number: 'short_name',
    route: 'long_name',
    locality: 'long_name',
    administrative_area_level_1: 'short_name',
    country: 'long_name',
    sublocality_level_2: 'long_name',
    postal_code: 'short_name'
  };
  public urlRedirect: string;
  public articlesFirmTextConfigBus: IMultiSelectTexts = {
    checkAll: 'Select all',
    uncheckAll: 'Deselect all',
    checked: 'item selected',
    checkedPlural: 'items selected',
    defaultTitle: 'Industry',
    allSelected: 'All selected',
  };
  public articlesFirmTextConfigBus1: IMultiSelectTexts = {
    checkAll: 'Select all',
    uncheckAll: 'Deselect all',
    checked: 'item selected',
    checkedPlural: 'items selected',
    defaultTitle: 'Secondary Industry',
    allSelected: 'All selected',
  };
  public articlesFirmSettingsBus: IMultiSelectSettings = {
    displayAllSelectedText: true,
    selectionLimit: 0,
    showCheckAll: true,
    showUncheckAll: true
  };
  public articlesFirmSettingsIndustry: IMultiSelectSettings = {
    displayAllSelectedText: true,
    selectionLimit: 0,
    showCheckAll: true,
    showUncheckAll: true,
    checkedStyle: 'checkboxes'
  };
  public optionsModelBus: string[];
  public optionsModelBus1: string[];
  public indistrySelect: IMultiSelectOption[] = industry;
  // public secondaryIndustrySelect: IMultiSelectOption[] = industry;
  public sendEmail = false;
  public salaryCheck = false;
  public specFilesArray = [];
  public checkSpecFiles = false;
  public genderModel = [];
  public availabilityModel = [];
  public ethnicityModel = [];
  public locationModel = [];
  public qualificationLevelModel = [];
  public tertiaryEducationModel = [];
  public specializationModel = [];
  public yearsWorkModel = [];
  public specializationSettings: IMultiSelectSettings = {
    displayAllSelectedText: true,
    enableSearch: true,
    selectionLimit: 0,
    showCheckAll: true,
    showUncheckAll: true,
  };
  public genderOptions: IMultiSelectOption[];
  public availabilityOptions: IMultiSelectOption[];
  public ethnicityOptions: IMultiSelectOption[];
  public locationOptions: IMultiSelectOption[];
  public qualificationLevelOptions: IMultiSelectOption[];
  public tertiaryEducationOptions: IMultiSelectOption[];
  public specializationOptions: IMultiSelectOption[];
  public yearsWorkOptions: IMultiSelectOption[];
  public minValue: number = null;
  public maxValue: number = null;
  public options: Options;
  public checkRequestCount = false;
  public validateSalarySlider = false;

  constructor(
    private readonly _businessService: BusinessService,
    private readonly _toastr: ToastrService,
    public _sharedService: SharedService,
    private readonly _router: Router,
    private _modalService: NgbModal,
    private readonly _mapsAPILoader: MapsAPILoader,
    private readonly _ngZone: NgZone
  ) {
    this._sharedService.checkSidebar = false;
    this.genderOptions = this._sharedService.genderOptions;
    this.availabilityOptions = this._sharedService.availabilityOptions;
    this.ethnicityOptions = this._sharedService.ethnicityOptionsYes;
    this.locationOptions = this._sharedService.citiesWorking;
    this.qualificationLevelOptions = this._sharedService.configQualificationLevel;
    this.tertiaryEducationOptions = this._sharedService.configTertiaryEducation;
    this.specializationOptions = this._sharedService.specializationCandidate;
    this.yearsWorkOptions = this._sharedService.configYearsWork;
  }

  ngOnInit() {
    setTimeout(() => {
      this.checkRequestCount = true;
    }, 1000);

    window.scrollTo(0, 0);

    this.articles.forEach((article, index) => {
      this.articlesFirmOptions.push({ id: article, name: article });
    });

    this.businessJobsForm = new FormGroup({
      step1: new FormGroup({
        jobTitle: new FormControl('', Validators.required),
        industry: new FormControl(null, Validators.required),
        // secondaryIndustry: new FormControl(null),
        companyName: new FormControl('', Validators.required),
        companyAddress: new FormControl('', Validators.required),
        addressCountry: new FormControl('', Validators.required),
        addressState: new FormControl('', Validators.required),
        addressZipCode: new FormControl('', Validators.required),
        addressCity: new FormControl('', Validators.required),
        addressSuburb: new FormControl('', Validators.required),
        addressStreet: new FormControl('', Validators.required),
        addressStreetNumber: new FormControl('', Validators.required),
        addressBuildName: new FormControl(''),
        addressUnit: new FormControl(''),
        companyDescription: new FormControl('', Validators.compose([
          Validators.required,
          Validators.maxLength(300),
        ])),
        roleDescription: new FormControl('', Validators.compose([
          Validators.required,
          Validators.maxLength(400),
        ])),
        closureDate: new FormControl(null, Validators.compose([
          Validators.required,
          closureDateValidator(new Date())
        ])),
        jobClosureDate: new FormControl(new Date(), Validators.compose([
          Validators.required,
          jobClosureDateValidator(new Date())
        ])),
        started: new FormControl(null, Validators.required),
        // jobReference: new FormControl(null),
        typeOfEmployment: new FormControl(null, Validators.required),
        // timePeriod: new FormControl(null),
        salaryFrom: new FormControl(null, Validators.required),
        salaryTo: new FormControl(null, Validators.required)
      }),

      step2: new FormGroup({
        gender: new FormControl('', Validators.required),
        ethnicity: new FormControl('', Validators.required),
        location: new FormControl('', Validators.required),
        availability: new FormControl('', Validators.required),
        video: new FormControl(null, Validators.required),
        highestQualification: new FormControl('', Validators.required),
        field: new FormControl('', Validators.required),
        eligibility: new FormControl('applicable', Validators.required),
        yearsOfWorkExperience: new FormControl('', Validators.required),
        assessment: new FormControl(null)
      })
    });

    this.businessJobObject = new BusinessJob({});

    this.setApplicationClosureDefaultDate();
    this.setApplicationJobClosureDefaultDate();
    this.createJobsForm().then(() => {
      this.getCandidatesCount().then(response => {
        this.googleSearch();
      });
    });
  }

  @HostListener('window:beforeunload')
  onBeforeUnload() {
    if (this.businessJobsForm.dirty === true && this.businessJobsForm.touched === true) {
      const confirmTest = "Are you sure you want to leave now?";
      window.event.returnValue = false;
      return confirmTest;
    }
  }

  canDeactivate(url) {
    this.urlRedirect = url;
    if (this.businessJobsForm.dirty === true && this.businessJobsForm.touched === true) {
      this.openVerticallyCentered(this.content);
    }
    else {
      return true;
    }
  }

  /**
   * Upload files
   * @param fieldName {string}
   * @param event {File}
   */
  public uploadFiles(fieldName: string, event) {
    for (let item of event.target.files){
      this.specFilesArray = [];
      this[fieldName].push(item);
    }
  }

  /**
   * Remove file
   * @param fieldName {string}
   * @param index {number}
   * @return {Promise<void>}
   */
  public async removeFile(fieldName: string, index: number): Promise<void> {
    this.specFilesArray = [];
  }

  /**
   * Exit add job page
   */
  exitPage(){
    this.modalActiveClose.dismiss();
    this.businessJobsForm.reset();
    this._router.navigate([this.urlRedirect]);
  }

  /**
   * Google search autocomplete
   */
  public googleSearch() {
    this._mapsAPILoader.load().then(() => {
        const autocomplete = new google.maps.places.Autocomplete((<HTMLInputElement>document.getElementById('search1')), { types:["address"] });

        autocomplete.addListener("place_changed", () => {
          this._ngZone.run(() => {
            const place: google.maps.places.PlaceResult = autocomplete.getPlace();
            this.businessJobsForm.controls['step1'].patchValue({
              companyAddress: place.formatted_address,
            });
            this.businessJobsForm.controls['step1'].patchValue({
              addressStreetNumber: '',
              addressSuburb: '',
              addressStreet: '',
              addressCity: '',
              addressState: '',
              addressCountry: '',
              addressZipCode: ''
            });

            for (let i = 0; i < place.address_components.length; i++) {
              let addressType = place.address_components[i].types[0];
              if (addressType === 'sublocality_level_1') {
                addressType = 'sublocality_level_2';
              }
              if (this.componentForm[addressType]) {
                const valuePlace = place.address_components[i][this.componentForm[addressType]];
                (<HTMLInputElement>document.getElementById(addressType)).value = valuePlace;

                if (addressType === 'street_number') {
                  this.businessJobsForm.controls['step1'].patchValue({
                    addressStreetNumber: valuePlace
                  });
                } else if (addressType === 'sublocality_level_2') {
                  this.businessJobsForm.controls['step1'].patchValue({
                    addressSuburb: valuePlace
                  });
                } else if (addressType === 'route') {
                  this.businessJobsForm.controls['step1'].patchValue({
                    addressStreet: valuePlace
                  });
                } else if (addressType === 'locality') {
                  this.businessJobsForm.controls['step1'].patchValue({
                    addressCity: valuePlace
                  });
                } else if (addressType === 'administrative_area_level_1') {
                  this.businessJobsForm.controls['step1'].patchValue({
                    addressState: valuePlace
                  });
                } else if (addressType === 'country') {
                  this.businessJobsForm.controls['step1'].patchValue({
                    addressCountry: valuePlace
                  });
                } else if (addressType === 'postal_code') {
                  this.businessJobsForm.controls['step1'].patchValue({
                    addressZipCode: valuePlace
                  })
                }
              }
            }
            if ( place.geometry === undefined || place.geometry === null ){
              return;
            }
          });
        });
      }
    );
  }

  /**
   * Switch steps
   * @param page
   */
  public switchStep(page): void {
    if (this.businessJobsForm.controls['step1'].invalid) {
      this._sharedService.validateAllFormFieldsJob(this.businessJobsForm.controls['step1']);
    } else if (this.businessJobsForm.controls['step1']['controls']['salaryFrom'].value &&
               this.businessJobsForm.controls['step1']['controls']['salaryTo'].value &&
               this.businessJobsForm.controls['step1']['controls']['salaryFrom'].value >=
               this.businessJobsForm.controls['step1']['controls']['salaryTo'].value) {
      this.salaryCheck = true;
    } else {
      this.switchSteps = page;
    }

    if (this.switchSteps === false) {
      this.getCandidatesCount();
      this.options = {
        floor: 0,
        ceil: 35000,
        step: 3500,
        showTicks: true,
        showTicksValues: true,
        translate: (value: number, label: LabelType): string => {
          switch (label) {
            case LabelType.Low:
              return 'R' + value;
            case LabelType.High:
              return 'R' + value;
            default:
              return 'R' + value;
          }
        }
      };
    }
    this.googleSearch();
  }

  /**
   * creates businessJobsForm and populates with specified data
   * @returns void
   */
  public async createJobsForm(): Promise<void> {
    this.currentBusinessProfile = await this._businessService.getBusinessProfileDetails();

    this.genderModel = ['Male', 'Female'];
    this._sharedService.availabilityOptions.forEach((data) => {
      this.availabilityModel.push(data.id);
    });
    this._sharedService.ethnicityOptionsYes.forEach(data => {
      this.ethnicityModel.push(data.id);
    });
    this._sharedService.citiesWorking.forEach((data) => {
      this.locationModel.push(data.id);
    });
    this._sharedService.configQualificationLevel.forEach((data) => {
      this.qualificationLevelModel.push(data.id);
    });
    this._sharedService.specializationCandidate.forEach((data) => {
      this.specializationModel.push(data.id);
    });
    this._sharedService.configYearsWork.forEach((data) => {
      this.yearsWorkModel.push(data.id);
    });

    this.businessJobsForm.controls['step1'].setValue({
      jobTitle: '',
      industry: this.currentBusinessProfile.profile.company.industry,
      // secondaryIndustry: null,
      companyName: this.currentBusinessProfile.profile.company.name,
      companyAddress: this.currentBusinessProfile.profile.company.address,
      addressCountry: this.currentBusinessProfile.profile.company.addressCountry,
      addressState: this.currentBusinessProfile.profile.company.addressState,
      addressZipCode: this.currentBusinessProfile.profile.company.addressZipCode,
      addressCity: this.currentBusinessProfile.profile.company.addressCity,
      addressSuburb: this.currentBusinessProfile.profile.company.addressSuburb,
      addressStreet: this.currentBusinessProfile.profile.company.addressStreet,
      addressStreetNumber: this.currentBusinessProfile.profile.company.addressStreetNumber,
      addressBuildName: this.currentBusinessProfile.profile.company.addressBuildName,
      addressUnit: this.currentBusinessProfile.profile.company.addressUnit,
      companyDescription: this.currentBusinessProfile.profile.company.description,
      roleDescription: null,
      closureDate: this.closureDate,
      jobClosureDate: this.jobClosureDate,
      started: null,
      // jobReference: null,
      typeOfEmployment: null,
      // timePeriod: null,
      salaryFrom: null,
      salaryTo: null
    });

    this.businessJobsForm.controls['step2'].setValue({
      gender: this.genderModel,
      ethnicity: this.ethnicityModel,
      location: this.locationModel,
      availability: this.availabilityModel,
      video: 'All',
      highestQualification: this.qualificationLevelModel,
      field: this.specializationModel,
      eligibility: 'applicable',
      yearsOfWorkExperience: this.yearsWorkModel,
      assessment: 1
    });

    this.preloaderPage = false;
  }

  /**
   * sets default date for application closure date equal to 14 days from current date
   * @returns void
   */
  public setApplicationClosureDefaultDate(): void {
    // Set today date using the patchValue function
    const date = new Date(Date.now() + 6048e5);
    this.closureDate = {
        date: {
          year: date.getFullYear(),
          month: date.getMonth() + 1,
          day: date.getDate(),
        }
    };
  }

  public setApplicationJobClosureDefaultDate(): void {
    // Set today date using the patchValue function
    const date = new Date(Date.now());
    this.jobClosureDate = {
        date: {
          year: date.getFullYear(),
          month: date.getMonth() + 1 ,
          day: date.getDate() + 7,
        }
    };
  }

  /**
   * Send job email
   * @returns {Promise<void>}
   */
  public sendJobEmail(qualification): any{
    if (qualification === 3) {
      this.sendEmail = true;
      try {
        this._businessService.sendJobEmail();
        this.openVerticallyCentered(this.sendEmailPopup);
      }
      catch (err) {
        this._sharedService.showRequestErrors(err);
      }
    }
    else{
      this.sendEmail = false;
    }
  }

  /**
   * creates new job for business
   * @returns void
   */
  public async processJobsCreation(): Promise<any> {
    if (this.businessJobsForm.valid) {

      if (this.minValue === null || !this.maxValue === null) {
        this.validateSalarySlider = true;
      } else {
        const formData = new FormData();

        for (let i = 0; i < this.specFilesArray.length; i++) {
          formData.append('spec', this.specFilesArray[i]);
        }

        formData.append('jobTitle', this.businessJobsForm.controls['step1'].value.jobTitle);
        const industry = this.businessJobsForm.controls['step1'].value.industry;
        if(typeof industry === 'object'){
          industry.forEach((item, index) => {
            formData.append('industry['+index+']', item);
          });
        }
        // const secondaryIndustry = this.businessJobsForm.controls['step1'].value.secondaryIndustry;
        // if (secondaryIndustry) {
        //   if(typeof secondaryIndustry === 'object'){
        //     if (secondaryIndustry && secondaryIndustry.length > 0) {
        //       secondaryIndustry.forEach((item, index) => {
        //         formData.append('industrySecondary['+index+']', item);
        //       });
        //     }
        //   }
        // }
        formData.append('companyName', this.businessJobsForm.controls['step1'].value.companyName);
        formData.append('companyAddress', this.businessJobsForm.controls['step1'].value.companyAddress);
        formData.append('addressCountry', this.businessJobsForm.controls['step1'].value.addressCountry);
        formData.append('addressState', this.businessJobsForm.controls['step1'].value.addressState);
        formData.append('addressZipCode', this.businessJobsForm.controls['step1'].value.addressZipCode);
        formData.append('addressCity', this.businessJobsForm.controls['step1'].value.addressCity);
        formData.append('addressSuburb', this.businessJobsForm.controls['step1'].value.addressSuburb);
        formData.append('addressStreet', this.businessJobsForm.controls['step1'].value.addressStreet);
        formData.append('addressStreetNumber', this.businessJobsForm.controls['step1'].value.addressStreetNumber);
        formData.append('addressBuildName', this.businessJobsForm.controls['step1'].value.addressBuildName);
        formData.append('addressUnit', this.businessJobsForm.controls['step1'].value.addressUnit);
        formData.append('companyDescription', this.businessJobsForm.controls['step1'].value.companyDescription);
        formData.append('roleDescription', this.businessJobsForm.controls['step1'].value.roleDescription);
        formData.append('closureDate', (this.businessJobsForm.controls['step1'].value.closureDate == null ) ? null : this.businessJobsForm.controls['step1'].value.closureDate.date.day + '.'  + this.businessJobsForm.controls['step1'].value.closureDate.date.month + '.'  + this.businessJobsForm.controls['step1'].value.closureDate.date.year);
        formData.append('jobClosureDate', (this.businessJobsForm.controls['step1'].value.jobClosureDate == null ) ? null : this.businessJobsForm.controls['step1'].value.jobClosureDate.date.day + '.'  + this.businessJobsForm.controls['step1'].value.jobClosureDate.date.month + '.'  + this.businessJobsForm.controls['step1'].value.jobClosureDate.date.year);
        formData.append('started', (this.businessJobsForm.controls['step1'].value.started == null ) ? null : this.businessJobsForm.controls['step1'].value.started.date.day + '.'  + this.businessJobsForm.controls['step1'].value.started.date.month + '.'  + this.businessJobsForm.controls['step1'].value.started.date.year);
        // formData.append('jobReference', this.businessJobsForm.controls['step1'].value.jobReference);
        formData.append('typeOfEmployment', this.businessJobsForm.controls['step1'].value.typeOfEmployment);
        // formData.append('timePeriod', this.businessJobsForm.controls['step1'].value.timePeriod);
        formData.append('monthSalaryFrom', this.businessJobsForm.controls['step1'].value.salaryFrom);
        formData.append('monthSalaryTo', this.businessJobsForm.controls['step1'].value.salaryTo);

        formData.append('gender', this.checkingFormControl(this.businessJobsForm.controls['step2']['controls']['gender'].value, this._sharedService.genderOptions));
        formData.append('ethnicity', this.checkingFormControl(this.businessJobsForm.controls['step2']['controls']['ethnicity'].value, this._sharedService.ethnicityOptions));
        formData.append('availability', this.availabilityRequestData());
        formData.append('location', this.checkingFormControl(this.businessJobsForm.controls['step2']['controls']['location'].value, this._sharedService.citiesWorking));
        formData.append('salaryRange', '0');
        formData.append('video', (this.businessJobsForm.controls['step2'].value.video === 'All') ? '0' : (this.businessJobsForm.controls['step2'].value.video === 'Yes') ? '1' : '2');
        const field = this.checkingFormControl(this.businessJobsForm.controls['step2']['controls']['field'].value, this._sharedService.specializationCandidate);
        if(field === 'All'){
          formData.append('field[0]', field);
        }
        else if(typeof field === 'object'){
          field.forEach((item, index) => {
            formData.append('field['+index+']', item);
          });
        }
        formData.append('highestQualification', this.checkingFormControl(this.businessJobsForm.controls['step2']['controls']['highestQualification'].value, this._sharedService.configQualificationLevel));
        formData.append('eligibility', this.transformEligibility(this.businessJobsForm.controls['step2']['controls']['eligibility'].value));
        formData.append('yearsOfWorkExperience', this.checkingFormControlYears(this.businessJobsForm.controls['step2']['controls']['yearsOfWorkExperience'].value, this._sharedService.configYearsWork));
        formData.append('salaryFrom', String(this.minValue));
        formData.append('salaryTo', String(this.maxValue));
        formData.append('assessment', String(this.businessJobsForm.controls['step2'].value.assessment));

        try {
          await this._businessService.createBusinessJob(formData);

          this._toastr.success('Your job has been submitted to CAs Online for review.');
          this.businessJobsForm.reset();
          this._sharedService.sidebarBusinessBadges.jobAwaiting++;
          this._router.navigate(['/business/awaiting_job']);
        } catch (err) {
          this._sharedService.showRequestErrors(err);
        }
      }
    } else {
      this._sharedService.validateAllFormFields(this.businessJobsForm);
    }
  }

  /**
   *
   * @param value
   * @returns {any}
   */
  public transformEligibility(value) {
    let newWord;
    if (value === 'all') {
      newWord = value[0].toUpperCase() + value.slice(1);
    } else {
      newWord = value;
    }
    return newWord;
  }

  /**
   * Get candidate criteria count from job posting
   * @returns {Promise<void>}
   */
  public async getCandidatesCount(): Promise<void> {
    if (this.checkRequestCount) {
      const data = {
        gender: this.checkingFormControl(this.genderModel, this._sharedService.genderOptions),
        ethnicity: this.checkingFormControl(this.ethnicityModel, this._sharedService.ethnicityOptions),
        location: this.checkingFormControl(this.locationModel, this._sharedService.citiesWorking),
        highestQualification: this.checkingFormControl(this.qualificationLevelModel, this._sharedService.configQualificationLevel),
        field: this.checkingFormControl(this.specializationModel, this._sharedService.specializationCandidate),
        availability: this.checkingFormControl(this.availabilityModel, this._sharedService.availabilityOptions),
        yearsOfWorkExperience: this.checkingFormControl(this.yearsWorkModel, this._sharedService.configYearsWork),
        video: this.businessJobsForm.controls['step2']['controls']['video'].value,
        monthSalaryFrom: this.minValue,
        monthSalaryTo: this.maxValue,
        eligibility: this.businessJobsForm.controls['step2']['controls']['eligibility'].value,
      };

      try {
        const response = await this._businessService.getBusinessCandidatesCount('', data);
        this.candidatesMatchingCriteria = response.countCandidate;
      }
      catch (err) {
        this._sharedService.showRequestErrors(err);
      }
    }
  }

  /**
   * Availability request data
   * @returns {string}
   */
  public availabilityRequestData() {
    let req = 0;
    if (this.businessJobsForm.controls['step2']['controls']['availability'].value &&
      this.businessJobsForm.controls['step2']['controls']['availability'].value.length === this._sharedService.availabilityOptions.length) {
      req = 0;
    } else {
      req = this.businessJobsForm.controls['step2']['controls']['availability'].value;
    }
    return String(req);
  }

  /**
   * Checking form control
   * @param value {object}
   * @param options {array}
   * @returns {any}
   */
  public checkingFormControl(value, options) {
    let ret: any;
    (value && value.length === options.length) ? ret = 'All' : ret = value;
    return ret;
  }

  /**
   * Checking from control number
   * @param value {object}
   * @param options {array}
   * @returns {any}
   */
  public checkingFormControlNumber(value, options) {
    let ret: any;
    (value && value.length === options.length) ? ret = '' : ret = value;
    return ret;
  }

  /**
   * Checking from control number
   * @param value {object}
   * @param options {array}
   * @returns {any}
   */
  public checkingFormControlYears(value, options) {
    let ret: any;
    (value && value.length === options.length) ? ret = 0 : ret = value;
    return ret;
  }

  /**
   * Reset ethnicity
   * @param value {string}
   */
  public resetEthnicity(value) {
    if (value === 'all') {
      this.ethnicityOptions = this._sharedService.ethnicityOptionsAll;
    } else {
      this.ethnicityOptions = this._sharedService.ethnicityOptionsYes;
      this.ethnicityModel = [];
    }
  }

  /**
   * Open modal
   * @param content
   */
  public openVerticallyCentered(content) {
    this.modalActiveClose = this._modalService.open(content, { centered: true });
  }

}
