import { Component, Input, NgZone, OnInit } from '@angular/core';
import { BusinessAdminJobFullDetails } from '../../../../../entities/models';
import { SharedService } from '../../../../services/shared.service';
import { IMultiSelectSettings, IMultiSelectTexts } from 'angular-2-dropdown-multiselect';
import { IMultiSelectOption } from 'ng2-multiselect';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { INgxMyDpOptions } from 'ngx-mydatepicker';
import { ToastrService } from 'ngx-toastr';
import { closureDateValidator, jobClosureDateValidator } from '../../../../validators/custom.validator';
import { articles } from '../../../../constants/articles.const';
import { AdminService } from '../../../../services/admin.service';
import { industry } from '../../../../constants/industry.const';
import { NgbModal } from '@ng-bootstrap/ng-bootstrap';
import { LabelType, Options } from 'ng5-slider';
import { MapsAPILoader } from '@agm/core';

@Component({
  selector: 'app-business-job-view-popup',
  templateUrl: './business-job-view-popup.component.html',
  styleUrls: ['./business-job-view-popup.component.scss']
})
export class BusinessJobViewPopupComponent implements OnInit {
  @Input('selectedBusinessJob') selectedBusinessJob;
  @Input('selectedBusinessJobArray') selectedBusinessJobArray;
  @Input('selectedBusinessJobStatus') selectedBusinessJobStatus;
  @Input('closePopup') closePopup;
  public myOptions: INgxMyDpOptions = { dateFormat: 'yyyy/mm/dd' };
  public businessJobsForm: FormGroup;
  public specifiedBusinessJob = new BusinessAdminJobFullDetails({});
  public articles = articles;
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

  public preloaderPopup = true;

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
  public modalActiveClose: any;
  public confirmFunction: string;
  public confirmData: any;
  public confirmStatus: any;
  public confirmArray: any;
  public dataFile: any;
  public fileIndex: any;
  public checkDataFile: boolean;

  public minValue: number = null;
  public maxValue: number = null;
  public options: Options;
  public checkRequestCount = false;

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
  public locationModel = [];
  public qualificationLevelModel = [];
  public tertiaryEducationModel = [];
  public specializationModel = [];
  public yearsWorkModel = [];
  public ethnicityModel: any;

  public componentForm = {
    street_number: 'short_name',
    route: 'long_name',
    locality: 'long_name',
    administrative_area_level_1: 'short_name',
    country: 'long_name',
    sublocality_level_2: 'long_name',
    postal_code: 'short_name'
  };

  public requestCount = 0;

  constructor(
      private readonly _formBuilder: FormBuilder,
      private readonly _adminService: AdminService,
      private readonly _toastr: ToastrService,
      public readonly _sharedService: SharedService,
      private readonly _mapsAPILoader: MapsAPILoader,
      private readonly _ngZone: NgZone,
      private readonly _modalService: NgbModal
  ) {
    this._sharedService.checkSidebar = false;

    this.genderOptions = this._sharedService.genderOptions;
    this.availabilityOptions = this._sharedService.availabilityOptions;
    this.ethnicityOptions = this._sharedService.ethnicityOptionsAll;
    this.locationOptions = this._sharedService.citiesWorking;
    this.qualificationLevelOptions = this._sharedService.configQualificationLevel;
    this.tertiaryEducationOptions = this._sharedService.configTertiaryEducation;
    this.specializationOptions = this._sharedService.specializationCandidate;
    this.yearsWorkOptions = this._sharedService.configYearsWork;
  }

  ngOnInit() {
    this.businessJobObject = new BusinessAdminJobFullDetails({});
    this.createJobsForm();
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

    this.getJobsForId();
  }

  /**
   * Google search autocomplete
   */
  public googleSearch() {
    this._mapsAPILoader.load().then(() => {
        const autocomplete = new google.maps.places.Autocomplete((<HTMLInputElement>document.getElementById('search1')), { types:["companyAddress"] });

        autocomplete.addListener("place_changed", () => {
          this._ngZone.run(() => {
            const place: google.maps.places.PlaceResult = autocomplete.getPlace();
            this.businessJobsForm.patchValue({
              companyAddress: place.formatted_address,
            });
            this.businessJobsForm.patchValue({
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
                  this.businessJobsForm.patchValue({
                    addressStreetNumber: valuePlace
                  });
                } else if (addressType === 'sublocality_level_2') {
                  this.businessJobsForm.patchValue({
                    addressSuburb: valuePlace
                  });
                } else if (addressType === 'route') {
                  this.businessJobsForm.patchValue({
                    addressStreet: valuePlace
                  });
                } else if (addressType === 'locality') {
                  this.businessJobsForm.patchValue({
                    addressCity: valuePlace
                  });
                } else if (addressType === 'administrative_area_level_1') {
                  this.businessJobsForm.patchValue({
                    addressState: valuePlace
                  });
                } else if (addressType === 'country') {
                  this.businessJobsForm.patchValue({
                    addressCountry: valuePlace
                  });
                } else if (addressType === 'postal_code') {
                  this.businessJobsForm.patchValue({
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
   * Open confirm popup
   * @param content
   * @param confirmArray
   * @param nameFunction
   * @param status
   */
  public openConfirm(content: any, confirmArray, nameFunction, status): void {
    this.modalActiveClose = this._modalService.open(content, { centered: true, windowClass: 'second-popup', 'size': 'sm' });
    this.confirmFunction = nameFunction;
    this.confirmData = this.selectedBusinessJob;
    this.confirmStatus = status;
    this.confirmArray = this.selectedBusinessJobArray;
    this.closePopup();
  }

  /**
   * Open modal
   * @param content
   * @param data
   * @param index
   * @param status
   */
  public openVerticallyCenterFileClient(content, data, index, status) {
    this.dataFile = data;
    this.checkDataFile = status;
    this.fileIndex = index;
    this.modalActiveClose = this._modalService.open(content, { centered: true, windowClass: 'second-popup', 'size': 'lg' });
  }

  /**
   * Upload admin files for client
   * @param jobId
   * @param fileIndex
   * @param index
   * @param fileName
   * @returns {Promise<void>}
   */
  public async uploadAdminFilesClient(jobId, fileIndex, index, fileName): Promise<any> {
    let elem;
    if(!fileName) {
      elem = (<HTMLInputElement>document.getElementById(fileIndex));
    } else {
      elem = (<HTMLInputElement>document.getElementById(fileName));
    }
    const formData = new FormData();
    if(elem.files.length > 0){
      formData.append('spec', elem.files[0]);
    }

    try {
      const data = await this._adminService.uploadAdminFilesForClientAdmin(formData, jobId);
      this.specifiedBusinessJob['spec'] = data['spec'];
      this.modalActiveClose.dismiss();
    }
    catch (err) {
      this._sharedService.showRequestErrors(err);
    }
  }

  /**
   * Get jobs for ID
   * @return {Promise<void>}
   */
  public async getJobsForId(): Promise <void>{
    try {
      const response = await this._adminService.getBusinessJobById(this.selectedBusinessJob.id);
      this.specifiedBusinessJob = response;
      if (this.specifiedBusinessJob.qualification === 0) {
        this.specifiedBusinessJob.qualification = 1;
      }
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
    }
    catch (err){
      this._sharedService.showRequestErrors(err);
    }
  }

  /**
   * populates form with data
   * @returns void
   */
  public populateFormWithData(): void {

    const companyDescription = (this.specifiedBusinessJob.companyDescriptionChange)
        ? this.specifiedBusinessJob.companyDescriptionChange
        : this.specifiedBusinessJob.companyDescription;
    const roleDescription = (this.specifiedBusinessJob.roleDescriptionChange)
        ? this.specifiedBusinessJob.roleDescriptionChange
        : this.specifiedBusinessJob.roleDescription;

    this.businessJobsForm.controls['jobTitle'].setValue(this.specifiedBusinessJob.jobTitle);
    this.businessJobsForm.controls['industry'].setValue(this.specifiedBusinessJob.industry);

    // this.businessJobsForm.controls['secondaryIndustry'].setValue(this.specifiedBusinessJob['industrySecondary']);
    // this.businessJobsForm.controls['jobReference'].setValue(this.specifiedBusinessJob['jobReference']);
    this.businessJobsForm.controls['typeOfEmployment'].setValue(this.specifiedBusinessJob['typeOfEmployment']);
    // this.businessJobsForm.controls['timePeriod'].setValue(this.specifiedBusinessJob['timePeriod']);
    this.businessJobsForm.controls['salaryFrom'].setValue(this.specifiedBusinessJob['monthSalaryFrom']);
    this.businessJobsForm.controls['salaryTo'].setValue(this.specifiedBusinessJob['monthSalaryTo']);

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
    this.businessJobsForm.controls['companyDescriptionChange'].setValue(companyDescription);
    this.businessJobsForm.controls['roleDescription'].setValue(this.specifiedBusinessJob.roleDescription);
    this.businessJobsForm.controls['roleDescriptionChange'].setValue(roleDescription);
    this.businessJobsForm.controls['closureDate'].setValue(this.specifiedBusinessJob.closureDate);
    this.businessJobsForm.controls['jobClosureDate'].setValue((this.specifiedBusinessJob.jobClosureDate) ? this.specifiedBusinessJob.jobClosureDate : null);

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

    this.businessJobsForm.controls['started'].setValue(this.specifiedBusinessJob.started);
    if (this.specifiedBusinessJob.started) {
      const started = new Date(this.specifiedBusinessJob.started);
      this.businessJobsForm.patchValue({started: {
        date: {
          year: started.getFullYear(),
          month: started.getMonth() + 1,
          day: started.getDate(),
        }
      }});
    }

    this.businessJobsForm.controls['gender'].setValue(this.specifiedBusinessJob.gender);
    this.businessJobsForm.controls['ethnicity'].setValue((this.specifiedBusinessJob.ethnicity.length > 1) ? this.specifiedBusinessJob.ethnicity : this.specifiedBusinessJob.ethnicity[0]);
    this.businessJobsForm.controls['location'].setValue(this.specifiedBusinessJob.location);
    this.businessJobsForm.controls['video'].setValue((this.specifiedBusinessJob.video === 0) ? 'All' : (this.specifiedBusinessJob.video === 1) ? 'Yes' : 'No');
    this.businessJobsForm.controls['highestQualification'].setValue(this.specifiedBusinessJob['highestQualification']);
    this.businessJobsForm.controls['field'].setValue(this.specifiedBusinessJob['field']);
    this.businessJobsForm.controls['eligibility'].setValue(this.specifiedBusinessJob['eligibility']);
    this.businessJobsForm.controls['yearsOfWorkExperience'].setValue(this.specifiedBusinessJob['yearsOfWorkExperience']);
    this.businessJobsForm.controls['assessment'].setValue(this.specifiedBusinessJob['assessment']);
    this.minValue = this.specifiedBusinessJob['salaryFrom'];
    this.maxValue = this.specifiedBusinessJob['salaryTo'];

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

    // this.resetEthnicity(this.specifiedBusinessJob['eligibility']);

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

    this.getCandidatesCount();
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
      companyDescription: [null, null],
      companyDescriptionChange: [null, Validators.compose([
        Validators.required,
        Validators.maxLength(300),
      ])],
      roleDescription: [null, null],
      roleDescriptionChange: [null, Validators.compose([
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
      eligibility: [null, Validators.required],
      yearsOfWorkExperience: ['', Validators.required],
      assessment: [null]
    });
  }

  /**
   * Get candidate criteria count from job posting
   * @returns {Promise<void>}
   */
  public async getCandidatesCount(): Promise<void> {
    if (this.requestCount === 0) {
      this.requestCount++;

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
        const response = await this._adminService.getCandidatesCountSatisfyCriteria(data);
        setTimeout(() => {
          this.requestCount = 0;
        }, 200);
        this.candidatesMatchingCriteria = response.countCandidate;
        this.preloaderPopup = false;
      }
      catch (err) {
        this._sharedService.showRequestErrors(err);
      }
    }
  }

  /**
   * updates business job specified with id
   * @returns void
   */
  public async processJobsEdit(): Promise<void> {
    if (this.businessJobsForm.valid) {

      if (this.businessJobsForm.value.salaryFrom >= this.businessJobsForm.value.salaryTo) {
        this._toastr.error('Monthly Salary From could not be more Monthly Salary To');
      } else {
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
          roleDescriptionChange: this.businessJobsForm.value.roleDescriptionChange,
          companyDescriptionChange: this.businessJobsForm.value.companyDescriptionChange,
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
          await this._adminService.updateBusinessJob(this.selectedBusinessJob.id, data);
          this.selectedBusinessJob.companyName = data.companyName;
          this.selectedBusinessJob.jobTitle = data.jobTitle;

          this._toastr.success('Business Job has been successfully updated!');
          this.closePopup();

        } catch (err) {
          this._sharedService.showRequestErrors(err);
        }
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
   * Managed modal
   * @param content {any} - content to be shown in popup
   */
  public openVerticallyCenter(content: any) {
    this.modalActiveClose = this._modalService.open(content, { centered: true, windowClass: 'second-popup', backdropClass: 'light-blue-backdrop' });
  }
}
