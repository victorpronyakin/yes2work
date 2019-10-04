import { Component, ElementRef, HostListener, NgZone, OnInit, ViewChild } from '@angular/core';
import { IMultiSelectOption, IMultiSelectSettings, IMultiSelectTexts } from 'angular-2-dropdown-multiselect';
import { BusinessAdminJobFullDetails } from '../../../../../entities/models';
import { FormControl, FormGroup, Validators } from '@angular/forms';
import { INgxMyDpOptions } from 'ngx-mydatepicker';
import { AdminService } from '../../../../services/admin.service';
import { ToastrService } from 'ngx-toastr';
import { SharedService } from '../../../../services/shared.service';
import { articles } from '../../../../constants/articles.const';
import { Router } from '@angular/router';
import { NgbModal } from '@ng-bootstrap/ng-bootstrap';
import { industry } from '../../../../constants/industry.const';
import { MapsAPILoader } from '@agm/core';
import { closureDateValidator, jobClosureDateValidator } from '../../../../validators/custom.validator';
import { LabelType, Options } from 'ng5-slider';

@Component({
  selector: 'app-admin-add-new-job',
  templateUrl: './admin-add-new-job.component.html',
  styleUrls: ['./admin-add-new-job.component.scss']
})
export class AdminAddNewJobComponent implements OnInit {
  @ViewChild('content') private content : ElementRef;

  public myOptions: INgxMyDpOptions = { dateFormat: 'yyyy/mm/dd' };
  public businessJobsForm: FormGroup;
  public specifiedBusinessJob = new BusinessAdminJobFullDetails({});
  public businessJobObject: BusinessAdminJobFullDetails;
  public candidatesMatchingCriteria = 0;
  public articlesFirmPredefined: string[] = [];
  public articlesFirmSelectedName: string[];
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

  public componentForm = {
    street_number: 'short_name',
    route: 'long_name',
    locality: 'long_name',
    administrative_area_level_1: 'short_name',
    country: 'long_name',
    sublocality_level_2: 'long_name',
    postal_code: 'short_name'
  };
  public articles = articles;

  public fillDetailsBusiness = [];

  public preloaderPage = true;
  public switchSteps = true;
  public closureDate: any;
  public jobClosureDate: any;
  public modalActiveClose: any;

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
    showUncheckAll: true,
  };
  public optionsModelBus: string[];
  public optionsModelBus1: string[];
  public indistrySelect: IMultiSelectOption[] = industry;
  // public secondaryIndustrySelect: IMultiSelectOption[] = industry;

  public specFilesArray = [];
  public checkSpecFiles = false;

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
  public genderModel = [];
  public availabilityModel = [];
  public ethnicityModel = [];
  public locationModel = [];
  public qualificationLevelModel = [];
  public tertiaryEducationModel = [];
  public specializationModel = [];
  public yearsWorkModel = [];
  public salaryCheck = false;
  public options: Options;
  public checkRequestCount = false;
  public minValue: number = null;
  public maxValue: number = null;
  public validateSalarySlider = false;

  constructor(
    private readonly _adminService: AdminService,
    private readonly _toastr: ToastrService,
    private readonly _sharedService: SharedService,
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
    window.scrollTo(0, 0);

    this.businessJobObject = new BusinessAdminJobFullDetails({});

    this.businessJobsForm = new FormGroup({
      step1: new FormGroup({
        jobTitle: new FormControl('', Validators.required),
        industry: new FormControl(null, Validators.required),
        // secondaryIndustry: new FormControl(null),
        companyName: new FormControl(null, Validators.required),
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
        jobClosureDate: new FormControl(null, Validators.compose([
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

    this.getFullDetailsBusiness().then(response => {
      this.setApplicationClosureDefaultDate();
      this.setApplicationJobClosureDefaultDate();
      this.googleSearch();
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

  /**
   * Check utl params
   * @param url {string}
   * @returns {boolean}
   */
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
    this.googleSearch();
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
   * Get full details business
   * @return {Promise<void>}
   */
  public async getFullDetailsBusiness(): Promise<void> {
    try {
      this.fillDetailsBusiness = await this._adminService.getFullDetailsBusiness();

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
    catch (err) {
      if (err.status === 403){
        this.businessJobsForm.reset();
        window.location.reload();
      }
      else if (err.status === 401){
        this.businessJobsForm.reset();
        window.location.reload();
      }
      else {
        this._sharedService.showRequestErrors(err);
      }
    }
  }

  /**
   * Get full info company
   * @param companyId
   */
  public getFullInfo(companyId): void {

    // const data = this.fillDetailsBusiness.filter((listElement) => listElement.id === Number(companyId));
    //
    // this.businessJobsForm.controls['step1'].setValue({
    //   jobTitle: '',
    //   industry: data[0]['industry'],
    //   secondaryIndustry: [],
    //   companyName: data[0]['id'],
    //   companyAddress: data[0]['address'],
    //   addressCountry: data[0]['addressCountry'],
    //   addressState: data[0]['addressState'],
    //   addressZipCode: data[0]['addressZipCode'],
    //   addressCity: data[0]['addressCity'],
    //   addressSuburb: data[0]['addressSuburb'],
    //   addressStreet: data[0]['addressStreet'],
    //   addressStreetNumber: data[0]['addressStreetNumber'],
    //   addressBuildName: data[0]['addressBuildName'],
    //   addressUnit: data[0]['addressUnit'],
    //   companyDescription: data[0]['description'],
    //   roleDescription: this.businessJobsForm.controls['step1'].value.roleDescription,
    //   closureDate: this.businessJobsForm.controls['step1'].value.closureDate,
    //   jobClosureDate: this.businessJobsForm.controls['step1'].value.jobClosureDate,
    //   started: this.businessJobsForm.controls['step1'].value.started,
    //   jobReference: '',
    //   typeOfEmployment: null,
    //   timePeriod: null,
    //   salaryFrom: '',
    //   salaryTo: ''
    // });

    // this.setApplicationClosureDefaultDate();

  }

  /**
   * sets default date for application closure date equal to 14 days from current date
   * @returns void
   */
  public setApplicationClosureDefaultDate(): void {
    const date = new Date(Date.now() + 6048e5);
    this.businessJobsForm.controls['step1'].patchValue({closureDate: {
      date: {
        year: date.getFullYear(),
        month: date.getMonth() + 1,
        day: date.getDate(),
      }
    }});
  }

  public setApplicationJobClosureDefaultDate(): void {
    // Set today date using the patchValue function
    const date = new Date(Date.now());
    this.businessJobsForm.controls['step1'].patchValue({jobClosureDate: {
        date: {
          year: date.getFullYear(),
          month: date.getMonth() + 1 ,
          day: date.getDate() + 7,
        }
    }});
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
   * Get candidate criteria count from job posting
   * @returns {Promise<void>}
   */
  public async getCandidatesCount(): Promise<void> {
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
      const count = await this._adminService.getCandidatesCountSatisfyCriteria(data);
      this.candidatesMatchingCriteria = count.countCandidate;
    }
    catch (err) {
      this._sharedService.showRequestErrors(err);
    }
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
   * Creation job for admin version
   * @returns {Promise<any>}
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

        formData.append('clientID', this.businessJobsForm.controls['step1'].value.companyName);
        const companyName = this.fillDetailsBusiness.filter((listElement) => listElement.id === Number(this.businessJobsForm.controls['step1'].value.companyName));
        formData.append('companyName', companyName[0]['companyName']);
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

        formData.append('eligibility', this.transformEligibility(this.businessJobsForm.controls['step2']['controls']['eligibility'].value));
        formData.append('ethnicity', this.checkingFormControl(this.businessJobsForm.controls['step2']['controls']['ethnicity'].value, this._sharedService.ethnicityOptions));
        formData.append('gender', this.checkingFormControl(this.businessJobsForm.controls['step2']['controls']['gender'].value, this._sharedService.genderOptions));
        formData.append('location', this.checkingFormControl(this.businessJobsForm.controls['step2']['controls']['location'].value, this._sharedService.citiesWorking));
        formData.append('highestQualification', this.checkingFormControl(this.businessJobsForm.controls['step2']['controls']['highestQualification'].value, this._sharedService.configQualificationLevel));

        formData.append('yearsOfWorkExperience', this.checkingFormControlYears(this.businessJobsForm.controls['step2']['controls']['yearsOfWorkExperience'].value, this._sharedService.configYearsWork));
        formData.append('availability', this.availabilityRequestData());
        formData.append('video', (this.businessJobsForm.controls['step2'].value.video === 'All') ? '0' : (this.businessJobsForm.controls['step2'].value.video === 'Yes') ? '1' : '2');
        formData.append('assessment', String(this.businessJobsForm.controls['step2'].value.assessment));
        formData.append('salaryRange', '0');
        formData.append('salaryFrom', String(this.minValue));
        formData.append('salaryTo', String(this.maxValue));

        const field = this.checkingFormControl(this.businessJobsForm.controls['step2']['controls']['field'].value, this._sharedService.specializationCandidate);
        if(field !== 'All' && typeof field === 'object'){
          field.forEach((item, index) => {
            formData.append('field['+index+']', item);
          });
        } else {
          if (this.businessJobsForm.controls['step2']['controls']['field'].value.length ===
              this._sharedService.specializationCandidate.length) {
            formData.append('field', 'All');
          } else {
            formData.append('field', this.businessJobsForm.controls['step2']['controls']['field'].value);
          }
        }

        try {
          await this._adminService.createAdminJobs(formData);

          this._toastr.success('Job has been created!');
          this.businessJobsForm.reset();
          this._sharedService.sidebarAdminBadges.jobAll++;
          this._router.navigate(['/admin/all_jobs']);
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
   * Open modal
   * @param content
   */
  public openVerticallyCentered(content) {
    this.modalActiveClose = this._modalService.open(content, { centered: true });
  }

}
