import { Component, ElementRef, HostListener, OnInit, ViewChild } from '@angular/core';
import { INgxMyDpOptions } from 'ngx-mydatepicker';
import {FormBuilder, FormControl, FormGroup, Validators} from '@angular/forms';
import { BusinessService } from '../../../../services/business.service';
import { ToastrService } from 'ngx-toastr';
import { SharedService } from '../../../../services/shared.service';
import { articles } from '../../../../constants/articles.const';
import { IMultiSelectOption, IMultiSelectSettings, IMultiSelectTexts } from 'angular-2-dropdown-multiselect';
import { ActivatedRoute, Router } from '@angular/router';
import { BusinessJobFullDetails } from '../../../../../entities/models';
import { closureDateValidator, jobClosureDateValidator } from '../../../../validators/custom.validator';
import { NgbModal } from '@ng-bootstrap/ng-bootstrap';
import { industry } from '../../../../constants/industry.const';
import { LabelType, Options } from 'ng5-slider';

@Component({
  selector: 'app-business-edit-job',
  templateUrl: './business-edit-job.component.html',
  styleUrls: ['./business-edit-job.component.scss']
})
export class BusinessEditJobComponent implements OnInit {
  @ViewChild('content') private content : ElementRef;
  public myOptions: INgxMyDpOptions = { dateFormat: 'yyyy/mm/dd' };
  public businessJobsForm: FormGroup;
  public specifiedBusinessJob: BusinessJobFullDetails;
  public articles = articles;
  public businessJobObject: object;
  public candidatesMatchingCriteria = 0;
  public articlesFirmPredefined: string[] = [];
  public articlesFirmSelectedName: string[];
  public articlesFirmSettings: IMultiSelectSettings = {
    displayAllSelectedText: true,
    selectionLimit: 0,
    showCheckAll: true,
    showUncheckAll: true,
  };
  public currentBusineesJobId: number;
  public articlesFirmOptions: IMultiSelectOption[] = [];
  public articlesFirmTextConfig: IMultiSelectTexts = {
    checkAll: 'Select all',
    uncheckAll: 'Deselect all',
    checked: 'item selected',
    checkedPlural: 'items selected',
    defaultTitle: 'Articles firm',
    allSelected: 'Articles firm - All selected',
  };
  public preloaderPage = true;

  public urlRedirect: string;
  public modalActiveClose: any;

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
  public genderModel = [];
  public availabilityModel = [];
  public ethnicityModel: any;
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
  public salaryCheck = false;
  public validateSalarySlider = false;

  constructor(
      private readonly _formBuilder: FormBuilder,
      private readonly _businessService: BusinessService,
      private readonly _toastr: ToastrService,
      public readonly _sharedService: SharedService,
      private readonly _route: ActivatedRoute,
      private readonly _router: Router,
      private readonly _modalService: NgbModal
  ) {
    this._sharedService.checkSidebar = false;

    this.genderOptions = this._sharedService.genderOptions;
    this.availabilityOptions = this._sharedService.availabilityOptions;
    this.locationOptions = this._sharedService.citiesWorking;
    this.qualificationLevelOptions = this._sharedService.configQualificationLevel;
    this.tertiaryEducationOptions = this._sharedService.configTertiaryEducation;
    this.specializationOptions = this._sharedService.specializationCandidate;
    this.yearsWorkOptions = this._sharedService.configYearsWork;
  }

  ngOnInit() {
    window.scrollTo(0, 0);
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

    this.currentBusineesJobId = this._route.snapshot.params.id;

    this.articles.forEach((article) => {
      this.articlesFirmOptions.push({ id: article, name: article });
    });

    this.businessJobObject = new BusinessJobFullDetails({});
    this.createJobsForm();
    this._businessService.getBusinessJobById(this.currentBusineesJobId).then(
      (response) => {
        this.specifiedBusinessJob = response;
        this.resetEthnicity(this.specifiedBusinessJob['eligibility']);
        this.candidatesMatchingCriteria = response.candidateCount;
        if (this.specifiedBusinessJob['spec']) {
          this.specFilesArray.push({
            name: this.specifiedBusinessJob['spec'].name
          });
        }

        if (this.specifiedBusinessJob.qualification === 0) {
          this.specifiedBusinessJob.qualification = 1;
        }

        setTimeout(()=>{
          this.getCandidatesCount();
        }, 200);

        this.businessJobsForm.controls['closureDate'].setValidators([
          Validators.required,
          closureDateValidator(new Date())
        ]);

        this.businessJobsForm.controls['jobClosureDate'].setValidators([
          Validators.required,
          jobClosureDateValidator(new Date())
        ]);

        this.populateFormWithData();
        this._sharedService.fetchGoogleAutocompleteDetails(this.businessJobsForm);
        this.preloaderPage = false;
      }
    ).catch((err) => {
      if (err.status === 403){
        this.businessJobsForm.reset();
      }
      else if (err.status === 401){
        this.businessJobsForm.reset();
      }
      else {
        this._sharedService.showRequestErrors(err);
      }
    });

    setTimeout(() => {
      this.checkRequestCount = true;
    }, 2000);
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
  public async uploadFiles(fieldName: string, event) {
    for (let item of event.target.files){
      this.specFilesArray = [];

      this[fieldName].push(item);

      const formData = new FormData();

      for (let i = 0; i < this.specFilesArray.length; i++) {
        formData.append('spec', this.specFilesArray[i]);
      }

      /*const data = {
        spec: this.specFilesArray[0].name
      };*/

      try {
        const response = await this._businessService.uploadBusinessJobSpec(this.currentBusineesJobId, formData);
      }
      catch (err) {
        this._sharedService.showRequestErrors(err);
      }
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
    try {
      const data = await this._businessService.deleteBusinessJobSpec(this.currentBusineesJobId);
    }
    catch (err) {
      this._sharedService.showRequestErrors(err);
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
   * creates businessJobsForm and populates with specified data
   * @returns void
   */
  public createJobsForm(): void {

    this.businessJobsForm = this._formBuilder.group({
      jobTitle: [null, Validators.required],
      industry: [null, Validators.required],
      // secondaryIndustry: [null],
      companyName: [null, Validators.required],
      address: [null, Validators.required],
      addressCountry: [null],
      addressState: [null],
      addressZipCode: [null],
      addressCity: [null],
      addressSuburb: [null],
      addressStreet: [null],
      addressStreetNumber: [null],
      addressBuildName: [null],
      addressUnit: [null],
      companyDescription: [null, Validators.compose([
        Validators.required,
        Validators.maxLength(300),
      ])],
      roleDescription: [null, Validators.compose([
        Validators.required,
        Validators.maxLength(400),
      ])],
      closureDate: [null, null],
      jobClosureDate: [null, null],
      started: [null, Validators.required],
      // jobReference: [null],
      typeOfEmployment: [null, Validators.required],
      // timePeriod: [null],
      salaryFrom: [null, Validators.required],
      salaryTo: [null, Validators.required],

      gender: [null, Validators.required],
      ethnicity: [null, Validators.required],
      video: [null, Validators.required],
      availability: ['', Validators.required],
      location: [null, Validators.required],
      qualification: [1, Validators.required],
      highestQualification: ['', Validators.required],
      field: ['', Validators.required],
      eligibility: ['', Validators.required],
      yearsOfWorkExperience: ['', Validators.required],
      assessment: [null]
    });
  }

  /**
   * populates form with data
   * @returns void
   */
  public populateFormWithData(): void {

    this.businessJobsForm.controls['jobTitle'].setValue(this.specifiedBusinessJob.jobTitle);
    this.businessJobsForm.controls['companyName'].setValue(this.specifiedBusinessJob.companyName);
    this.businessJobsForm.controls['address'].setValue(this.specifiedBusinessJob.companyAddress);
    this.businessJobsForm.controls['addressCountry'].setValue(this.specifiedBusinessJob.addressCountry);
    this.businessJobsForm.controls['addressState'].setValue(this.specifiedBusinessJob.addressState);
    this.businessJobsForm.controls['addressCity'].setValue(this.specifiedBusinessJob.addressCity);
    this.businessJobsForm.controls['addressSuburb'].setValue(this.specifiedBusinessJob.addressSuburb);
    this.businessJobsForm.controls['addressZipCode'].setValue(this.specifiedBusinessJob.addressZipCode);
    this.businessJobsForm.controls['addressStreet'].setValue(this.specifiedBusinessJob.addressStreet);
    this.businessJobsForm.controls['addressStreetNumber'].setValue(this.specifiedBusinessJob.addressStreetNumber);
    this.businessJobsForm.controls['addressBuildName'].setValue(this.specifiedBusinessJob.addressBuildName);
    this.businessJobsForm.controls['addressUnit'].setValue(this.specifiedBusinessJob.addressUnit);
    this.businessJobsForm.controls['companyDescription'].setValue(this.specifiedBusinessJob.companyDescription);
    this.businessJobsForm.controls['roleDescription'].setValue(this.specifiedBusinessJob.roleDescription);
    this.businessJobsForm.controls['closureDate'].setValue(this.specifiedBusinessJob.closureDate);
    this.businessJobsForm.controls['jobClosureDate'].setValue((this.specifiedBusinessJob.jobClosureDate) ? this.specifiedBusinessJob.jobClosureDate : null);
    this.businessJobsForm.controls['started'].setValue(this.specifiedBusinessJob.started);

    // this.businessJobsForm.controls['jobReference'].setValue(this.specifiedBusinessJob['jobReference']);
    this.businessJobsForm.controls['typeOfEmployment'].setValue(this.specifiedBusinessJob['typeOfEmployment']);
    // this.businessJobsForm.controls['timePeriod'].setValue(this.specifiedBusinessJob['timePeriod']);
    this.businessJobsForm.controls['salaryFrom'].setValue(this.specifiedBusinessJob['monthSalaryFrom']);
    this.businessJobsForm.controls['salaryTo'].setValue(this.specifiedBusinessJob['monthSalaryTo']);
    this.businessJobsForm.controls['assessment'].setValue(this.specifiedBusinessJob.assessment);

    const date = new Date(this.specifiedBusinessJob.closureDate);
    this.businessJobsForm.patchValue({closureDate: {
        date: {
            year: date.getFullYear(),
            month: date.getMonth() + 1,
            day: date.getDate(),
        }
    }});
    if (this.specifiedBusinessJob.jobClosureDate) {
      const jobDate = new Date(this.specifiedBusinessJob.jobClosureDate);
      this.businessJobsForm.patchValue({jobClosureDate: {
          date: {
            year: jobDate.getFullYear(),
            month: jobDate.getMonth() + 1,
            day: jobDate.getDate(),
          }
        }});
    }
    if (this.specifiedBusinessJob.started) {
      const dateStarted = new Date(this.specifiedBusinessJob.started);
      this.businessJobsForm.patchValue({started: {
        date: {
          year: dateStarted.getFullYear(),
          month: dateStarted.getMonth() + 1,
          day: dateStarted.getDate(),
        }
      }});
    }

    if (this.specifiedBusinessJob.filled) {
      const dateFilled = new Date(this.specifiedBusinessJob.filled);
      this.businessJobsForm.patchValue({filled: {
        date: {
          year: dateFilled.getFullYear(),
          month: dateFilled.getMonth() + 1,
          day: dateFilled.getDate(),
        }
      }});
    }

    this.businessJobsForm.controls['gender'].setValue(this.specifiedBusinessJob.gender);
    this.businessJobsForm.controls['location'].setValue(this.specifiedBusinessJob.location);
    this.businessJobsForm.controls['video'].setValue((this.specifiedBusinessJob.video === 0) ? 'All' : (this.specifiedBusinessJob.video === 1) ? 'Yes' : 'No');
    this.businessJobsForm.controls['highestQualification'].setValue(this.specifiedBusinessJob['highestQualification']);
    this.businessJobsForm.controls['field'].setValue(this.specifiedBusinessJob['field']);
    this.businessJobsForm.controls['eligibility'].setValue(this.specifiedBusinessJob['eligibility']);
    this.businessJobsForm.controls['ethnicity'].setValue((this.specifiedBusinessJob.ethnicity.length > 1) ? this.specifiedBusinessJob.ethnicity : this.specifiedBusinessJob.ethnicity[0]);
    this.businessJobsForm.controls['yearsOfWorkExperience'].setValue(this.specifiedBusinessJob['yearsOfWorkExperience']);
    this.businessJobsForm.controls['assessment'].setValue(this.specifiedBusinessJob['assessment']);
    this.minValue = this.specifiedBusinessJob['salaryFrom'];
    this.maxValue = this.specifiedBusinessJob['salaryTo'];
    this.businessJobsForm.controls['industry'].setValue(this.specifiedBusinessJob.industry);
    // this.businessJobsForm.controls['secondaryIndustry'].setValue(this.specifiedBusinessJob['industrySecondary']);

    if (this.specifiedBusinessJob.gender === 'All') {
      this.genderModel = ['Male', 'Female'];
    } else {
      this.genderModel = [this.specifiedBusinessJob.gender];
    }
    if (this.specifiedBusinessJob.availability == 0 || this.specifiedBusinessJob.availability[0] == '0') {
      let arr = [];
      this.availabilityOptions.forEach((data) => {
        arr.push(data.id);
      });
      this.availabilityModel = arr;
    } else {
      this.specifiedBusinessJob.availability.forEach(item => {
        this.availabilityModel.push(Number(item));
      });
    }

    this.businessJobsForm.controls['availability'].setValue(this.availabilityModel);

    if (this.specifiedBusinessJob.ethnicity === 'All' || this.specifiedBusinessJob.ethnicity[0] === 'All') {
      const arr = [];
      this._sharedService.ethnicityOptionsAll.forEach(data => {
        arr.push(data.id);
      });
      this.ethnicityModel = arr;
    } else {
      this.ethnicityOptions = this._sharedService.ethnicityOptionsYes;
      this.ethnicityModel = this.specifiedBusinessJob.ethnicity;
    }

    if (this.specifiedBusinessJob.location === 'All') {
      let arr = [];
      this.locationOptions.forEach((data) => {
        arr.push(data.id);
      });
      this.locationModel = arr;
    } else {
      this.locationModel = this.specifiedBusinessJob.location.split(',');
    }

    if (this.specifiedBusinessJob['highestQualification'] === 'All') {
      let arr = [];
      this._sharedService.configQualificationLevel.forEach((data) => {
        arr.push(data.id);
      });
      this.qualificationLevelModel = arr;
    } else {
      this.qualificationLevelModel = this.specifiedBusinessJob['highestQualification'].split(',');
    }

    if (this.specifiedBusinessJob['field'][0] === 'All') {
      let arr = [];
      this._sharedService.specializationCandidate.forEach((data) => {
        arr.push(data.id);
      });
      this.specializationModel = arr;
    } else {
      this.specializationModel = this.specifiedBusinessJob['field'];
    }

    if (this.specifiedBusinessJob['yearsOfWorkExperience'][0] === '0') {
      let arr = [];
      this.yearsWorkOptions.forEach((data) => {
        arr.push(data.id);
      });
      this.yearsWorkModel = arr;
    } else {
      this.yearsWorkModel = this.specifiedBusinessJob['yearsOfWorkExperience'];
    }


  }

  /**
   * updates business job specified with id
   * @returns void
   */
  public async processJobsEdit(id: number): Promise<any> {
    if (this.businessJobsForm.valid) {

      const data = {
        jobTitle: this.businessJobsForm.value.jobTitle,
        industry: this.businessJobsForm.value.industry,
        // industrySecondary: this.businessJobsForm.value.secondaryIndustry,
        companyName: this.businessJobsForm.value.companyName,
        companyAddress: this.businessJobsForm.value.companyAddress,
        addressCountry: this.businessJobsForm.value.addressCountry,
        addressState: this.businessJobsForm.value.addressState,
        addressZipCode: this.businessJobsForm.value.addressZipCode,
        addressCity: this.businessJobsForm.value.addressCity,
        addressSuburb: this.businessJobsForm.value.addressSuburb,
        addressStreet: this.businessJobsForm.value.addressStreet,
        addressStreetNumber: this.businessJobsForm.value.addressStreetNumber,
        addressBuildName: this.businessJobsForm.value.addressBuildName,
        addressUnit: this.businessJobsForm.value.addressUnit,
        companyDescription: this.businessJobsForm.value.companyDescription,
        roleDescription: this.businessJobsForm.value.roleDescription,
        closureDate: (this.businessJobsForm.value.closureDate == null ) ? null : this.businessJobsForm.value.closureDate.date.day + '.'  + this.businessJobsForm.value.closureDate.date.month + '.'  + this.businessJobsForm.value.closureDate.date.year,
        jobClosureDate: (this.businessJobsForm.value.jobClosureDate == null ) ? null : this.businessJobsForm.value.jobClosureDate.date.day + '.'  + this.businessJobsForm.value.jobClosureDate.date.month + '.'  + this.businessJobsForm.value.jobClosureDate.date.year,
        started: (this.businessJobsForm.value.started == null ) ? null : this.businessJobsForm.value.started.date.day + '.'  + this.businessJobsForm.value.started.date.month + '.'  + this.businessJobsForm.value.started.date.year,
        // jobReference: this.businessJobsForm.value.jobReference,
        typeOfEmployment: this.businessJobsForm.value.typeOfEmployment,
        // timePeriod: this.businessJobsForm.value.timePeriod,
        monthSalaryFrom: this.businessJobsForm.value.salaryFrom,
        monthSalaryTo: this.businessJobsForm.value.salaryTo,

        gender: this.checkingFormControlGender(this.businessJobsForm['controls']['gender'].value, this._sharedService.genderOptions),
        ethnicity: this.checkingFormControl(this.businessJobsForm['controls']['ethnicity'].value, this._sharedService.ethnicityOptions),
        availability: this.availabilityRequestData(),
        location: this.checkingFormControl(this.businessJobsForm['controls']['location'].value, this._sharedService.citiesWorking),
        salaryRange: 0,
        video: (this.businessJobsForm.value.video === 'All') ? 0 : (this.businessJobsForm.value.video === 'Yes') ? 1 : 2,
        field: this.checkingFormControl(this.businessJobsForm['controls']['field'].value, this._sharedService.specializationCandidate),
        highestQualification: this.checkingFormControl(this.businessJobsForm['controls']['highestQualification'].value, this._sharedService.configQualificationLevel),
        eligibility: this.transformEligibility(this.businessJobsForm['controls']['eligibility'].value),
        yearsOfWorkExperience: this.checkingFormControlYears(this.businessJobsForm['controls']['yearsOfWorkExperience'].value, this._sharedService.configYearsWork),
        salaryFrom: this.minValue,
        salaryTo: this.maxValue,
        assessment: String(this.businessJobsForm.value.assessment)
      };

      try {
        await this._businessService.updateBusinessJob(id, data);
        this._toastr.success('Business Job has been successfully updated!');
        this.businessJobsForm.reset();
        if (this.specifiedBusinessJob.approve === true) {
          this._sharedService.sidebarBusinessBadges.jobAwaiting++;
          this._sharedService.sidebarBusinessBadges.jobApproved--;
        }
        this._router.navigate(['/business/awaiting_job']);

      } catch (err) {
        this._sharedService.showRequestErrors(err);
      }
    } else {
      this._sharedService.validateAllFormFields(this.businessJobsForm);
    }
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
   * Transform eligibility
   * @param value {string}
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
        video: this.businessJobsForm.controls['video'].value,
        monthSalaryFrom: this.minValue,
        monthSalaryTo: this.maxValue,
        eligibility: this.businessJobsForm.controls['eligibility'].value,
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
    if (this.businessJobsForm['controls']['availability'].value &&
      this.businessJobsForm['controls']['availability'].value.length === this._sharedService.availabilityOptions.length) {
      req = 0;
    } else {
      req = this.businessJobsForm['controls']['availability'].value;
    }
    return req;
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
   * Checking form control gender
   * @param value {object}
   * @param options {array}
   * @returns {any}
   */
  public checkingFormControlGender(value, options) {
    let ret: any;
    (value && value.length === options.length) ? ret = 'All' : ret = value[0];
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
   * Reset ethnicity
   * @param value {string}
   */
  public resetEthnicity(value) {
    if (value === 'All') {
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
