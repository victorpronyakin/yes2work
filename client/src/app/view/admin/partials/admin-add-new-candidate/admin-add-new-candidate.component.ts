import { Component, ElementRef, NgZone, OnInit, ViewChild } from '@angular/core';
import { FormControl, FormGroup, Validators } from '@angular/forms';
import {
  CustomValidateIdNumber, ValidateAvailabilityDate, ValidateIdNumber,
  ValidateNumber
} from '../../../../validators/custom.validator';
import { SharedService } from '../../../../services/shared.service';
import { MapsAPILoader } from '@agm/core';
import { ToastrService } from 'ngx-toastr';
import { CandidateService } from '../../../../services/candidate.service';
import { INgxMyDpOptions } from 'ngx-mydatepicker';
import { IMultiSelectOption, IMultiSelectSettings, IMultiSelectTexts } from 'angular-2-dropdown-multiselect';
import {
  AdminCandidateProfile, AdminCandidateUser,
  AdminCandidateUserProfile
} from '../../../../../entities/models-admin';
import { AdminService } from '../../../../services/admin.service';
import { Router } from '@angular/router';
import { DomSanitizer } from '@angular/platform-browser';

@Component({
  selector: 'app-admin-add-new-candidate',
  templateUrl: './admin-add-new-candidate.component.html',
  styleUrls: ['./admin-add-new-candidate.component.scss']
})
export class AdminAddNewCandidateComponent implements OnInit {

  @ViewChild('picture') private picture : ElementRef;

  @ViewChild('cvFiles') private cvFiles : ElementRef;
  @ViewChild('copyOfID') private copyOfID: ElementRef;
  @ViewChild('matricCertificate') private matricCertificate : ElementRef;
  @ViewChild('matricTranscript') private matricTranscript: ElementRef;
  @ViewChild('certificateOfQualification') private certificateOfQualification: ElementRef;
  @ViewChild('academicTranscript') private academicTranscript: ElementRef;
  @ViewChild('payslip') private payslip : ElementRef;
  @ViewChild('addressField') private addressField: ElementRef;

  @ViewChild('video') private video : ElementRef;
  @ViewChild('videoPlayer') private videoPlayer : ElementRef;

  public articlesFirmTextConfig: IMultiSelectTexts = {
    checkAll: 'Select all',
    uncheckAll: 'Deselect all',
    checked: 'item selected',
    checkedPlural: 'items selected',
    defaultTitle: 'I would work in these cities...',
    allSelected: 'All selected',
  };
  public articlesFirmSettings: IMultiSelectSettings = {
    displayAllSelectedText: true,
    selectionLimit: 0,
    showCheckAll: true,
    showUncheckAll: true,
  };
  public articlesFirmOptions: IMultiSelectOption[] = [];

  public candidateProfileDetails: AdminCandidateProfile;
  public candidateForm: FormGroup;
  public componentForm = {
    street_number: 'short_name',
    route: 'long_name',
    locality: 'long_name',
    administrative_area_level_1: 'short_name',
    country: 'long_name',
    sublocality_level_2: 'long_name',
    postal_code: 'short_name'
  };

  public optionsModel: string[];
  public myOptions: IMultiSelectOption[];

  public copyOfIDArray = [];
  public cvFilesArray = [];
  public matricCertificateArray = [];
  public matricTranscriptArray = [];
  public certificateOfQualificationArray = [];
  public academicTranscriptArray = [];
  public payslipArray = [];

  public profilePicture = [];
  public profileVideo = null;

  public myOptionsDate: INgxMyDpOptions = { dateFormat: 'yyyy/mm/dd' };
  public model: any = { date: { year: 2018, month: 10, day: 9 } };

  public preloaderPage = true;
  public buttonPreloader = false;
  public preloaderPicture = false;

  public articlesOther = false;
  public saicaStatus = false;
  public checkSaica = true;

  public availabilityPeriodStatus = false;
  public checksAvailabilityPeriod = false;
  public checksAvailabilityDate = false;
  public checksEnglishProficiency = false;
  public checkDriverLicenseNumber = false;

  constructor(
    private readonly _candidateService: CandidateService,
    private readonly _toastr: ToastrService,
    private readonly _mapsAPILoader: MapsAPILoader,
    private readonly _ngZone: NgZone,
    private readonly _sharedService: SharedService,
    private readonly _adminService: AdminService,
    private readonly _router: Router,
    private readonly _sanitizer: DomSanitizer
  ) {
    this._sharedService.checkSidebar = false;
  }

  ngOnInit() {
    window.scrollTo(0, 0);
    this.myOptions = this._sharedService.citiesWorking;
    this.candidateProfileDetails = new AdminCandidateProfile({
      profile: new AdminCandidateUserProfile({}),
      user: new AdminCandidateUser({})
    });

    this.candidateForm = new FormGroup({
      firstName: new FormControl('', Validators.required),
      lastName: new FormControl('', Validators.required),
      phone: new FormControl('+27', [
        Validators.required,
        Validators.minLength(9),
        ValidateNumber
      ]),
      email: new FormControl('', Validators.required),

      idNumber: new FormControl('', Validators.compose([
        CustomValidateIdNumber
      ])),
      nationality: new FormControl(null),
      ethnicity: new FormControl(null, Validators.required),
      beeCheck: new FormControl(''),
      gender: new FormControl(null, Validators.required),
      dateOfBirth: new FormControl(null),
      criminal: new FormControl(null),
      criminalDescription: new FormControl(''),
      credit: new FormControl(null),
      creditDescription: new FormControl(''),
      homeAddress: new FormControl('', Validators.required),
      driverLicense: new FormControl(null),
      driverNumber: new FormControl(''),
      englishProficiency: new FormControl(null),
      availability: new FormControl(true),
      dateAvailability: new FormControl(null),
      availabilityPeriod: new FormControl(null),
      citiesWorking: new FormControl(null, Validators.required)
    });

    this._mapsAPILoader.load().then(() => {
        const autocomplete = new google.maps.places.Autocomplete((<HTMLInputElement>document.getElementById('search1')), { types:["address"] });

        autocomplete.addListener("place_changed", () => {
          this._ngZone.run(() => {
            const place: google.maps.places.PlaceResult = autocomplete.getPlace();

            this.candidateForm.controls.homeAddress.setValue(place.formatted_address);
            if ( place.geometry === undefined || place.geometry === null ){
              return;
            }
          });
        });
      }
    );

    setTimeout(() => {
      this.preloaderPage = false;
    }, 500);
  }

  /**
   * Selected id number in dateOfBirth/gender/nationality
   */
  public selectedIdNumber() {
    if (!CustomValidateIdNumber(this.candidateForm.controls.idNumber)['invalidIdNumber']) {
      const dateOfBirth = this.candidateForm.controls['idNumber'].value.substr(0, 6);
      const gender = this.candidateForm.controls['idNumber'].value.substr(6, 4);
      const nationality = this.candidateForm.controls['idNumber'].value.substr(10, 1);

      this.candidateForm.get('nationality').setValue((Number(nationality) + 1), { onlySelf: true });

      if (gender < 4999) {
        this.candidateForm.get('gender').setValue('Female', { onlySelf: true });
      } else {
        this.candidateForm.get('gender').setValue('Male', { onlySelf: true });
      }

      if (Number(dateOfBirth.substr(0, 2)) > 40) {
        this.candidateForm.get('dateOfBirth').setValue({date: {
          year: Number('19' + dateOfBirth.substr(0, 2)),
          month: Number(dateOfBirth.substr(2, 2)),
          day: Number(dateOfBirth.substr(4, 2)),
        }}, { onlySelf: true });
      } else {
        this.candidateForm.get('dateOfBirth').setValue({date: {
          year: Number('20' + dateOfBirth.substr(0, 2)),
          month: Number(dateOfBirth.substr(2, 2)),
          day: Number(dateOfBirth.substr(4, 2)),
        }}, { onlySelf: true });
      }
    }
  }

  /**
   * Check driver license value
   */
  public driverCheck(){
    this.candidateProfileDetails.profile.driverLicense = !this.candidateProfileDetails.profile.driverLicense;
  }

  /**
   * Update notification for English Proficiency
   * @param number {number}
   */
  public updateNotification(number) {
    if (this.candidateForm.controls.englishProficiency.value !== number) {
      this.candidateForm.get('englishProficiency').setValue(number, { onlySelf: true });
      this.candidateForm.value.englishProficiency = number;
      this.checksEnglishProficiency = false;
    } else {
      this.candidateForm.get('englishProficiency').setValue(null, { onlySelf: true });
      this.candidateForm.value.englishProficiency = null;
      this.checksEnglishProficiency = true;
    }
  }

  /**
   * Upload profile picture
   * @param event {File}
   */
  public uploadPicture(event){
    this.profilePicture = [];
    if (event.target.files && event.target.files[0]) {
      let reader = new FileReader();

      reader.onload = (event: ProgressEvent) => {
        this.profilePicture.push({
          url : (<FileReader>event.target).result
        });
      };

      reader.readAsDataURL(event.target.files[0]);
    }
  }

  /**
   * Upload profile video
   * @param event {File}
   */
  public uploadVideo(event){
    this.profileVideo = [];
    if (event.target.files && event.target.files[0]) {
      let reader = new FileReader();

      reader.onload = (event: ProgressEvent) => {
        this.profileVideo.push({
          url : (<FileReader>event.target).result
        });
        this.videoPlayer.nativeElement.src = (<FileReader>event.target).result;
        this.videoPlayer.nativeElement.load();
      };

      reader.readAsDataURL(event.target.files[0]);
    }
  }

  /**
   * Upload files
   * @param fieldName {string}
   * @param event {File}
   */
  public uploadFiles(fieldName: string, event) {
    if (fieldName === 'copyOfIDArray' || fieldName === 'cvFilesArray' || fieldName === 'payslipArray') {
      for (let item of event.target.files){
        this[fieldName] = [];
        this[fieldName].push(item);
      }
    } else {
      for (let item of event.target.files){
        this[fieldName].push(item);
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
    this[fieldName].splice(index, 1);
  }

  /**
   * Remove picture
   */
  public removePicture() {
    this.profilePicture = [];
    this.picture.nativeElement.value = '';
  }

  /**
   * Remove video
   */
  public removeVideo() {
    this.profileVideo = null;
    this.videoPlayer.nativeElement.src = '';
    this.video.nativeElement.value = '';
  }

  /**
   * Check criminal value
   */
  public criminalValue(): void {
    this.candidateProfileDetails.profile.criminal = !this.candidateProfileDetails.profile.criminal;
  }

  /**
   * Check credit value
   */
  public creditValue(): void {
    this.candidateProfileDetails.profile.credit = !this.candidateProfileDetails.profile.credit;
  }

  /**
   * Check availability value
   */
  public availableValue(value){
    value = !value;

    if (value === true){
      this.candidateForm.get('availabilityPeriod').setValue(null, { onlySelf: true });
      this.availabilityPeriodStatus = false;
      this.checksAvailabilityPeriod = false;
      this.checksAvailabilityDate = false;
    }
  }

  /**
   * Check availability period
   * @param select {number}
   */
  public checkAvailabilityPeriod(select) {
    this.checksAvailabilityPeriod = false;
    this.checksAvailabilityDate = false;
    if(select === 4){
      if (this.candidateForm.value.availability === false){
        this.availabilityPeriodStatus = true;
      }
      else{
        this.candidateForm.get('dateAvailability').setValue(null, { onlySelf: true });
        this.availabilityPeriodStatus = false;
      }
    }
    else{
      this.candidateForm.get('dateAvailability').setValue(null, { onlySelf: true });
      this.availabilityPeriodStatus = false;
    }
  }

  /**
   * Address input autocomplete focus
   */
  public addressFocus() {
    this.addressField.nativeElement.setAttribute("autocomplete", "new-password");
  }

  /**
   * Check Driver LinenseNumber
   */
  public checksDriverLicenseNumber() {
    if (this.candidateForm.value.driverLicense === true && (
        this.candidateForm.value.driverNumber === null
        || this.candidateForm.value.driverNumber === ''
        || this.candidateForm.value.driverNumber.length !== 12
        || !(/[A-Za-z0-9]{12}/i.test(this.candidateForm.value.driverNumber))
      )
    ) {
      this.checkDriverLicenseNumber = true;
    } else {
      this.checkDriverLicenseNumber = false;
    }
  }

  /**
   * Create new candidate
   * @return {Promise<void>}
   */
  public async createCandidateProfile(): Promise<void> {
    this.buttonPreloader = true;

    const formData = new FormData();

    for (let i = 0; i < this.copyOfIDArray.length; i++) {
      formData.append('copyOfID[]', this.copyOfIDArray[i]);
    }
    for (let i = 0; i < this.cvFilesArray.length; i++) {
      formData.append('cv[]', this.cvFilesArray[i]);
    }
    for (let i = 0; i < this.matricCertificateArray.length; i++) {
      formData.append('matricCertificate[]', this.matricCertificateArray[i]);
    }
    for (let i = 0; i < this.matricTranscriptArray.length; i++) {
      formData.append('matricTranscript[]', this.matricTranscriptArray[i]);
    }
    for (let i = 0; i < this.certificateOfQualificationArray.length; i++) {
      formData.append('certificateOfQualification[]', this.certificateOfQualificationArray[i]);
    }
    for (let i = 0; i < this.academicTranscriptArray.length; i++) {
      formData.append('academicTranscript[]', this.academicTranscriptArray[i]);
    }
    for (let i = 0; i < this.payslipArray.length; i++) {
      formData.append('payslip[]', this.payslipArray[i]);
    }

    formData.append('user[firstName]',this.candidateForm.value.firstName);
    formData.append('user[lastName]',this.candidateForm.value.lastName);
    formData.append('user[phone]', this.candidateForm.value.phone);
    formData.append('user[email]',this.candidateForm.value.email);

    formData.append('profile[idNumber]', this.candidateForm.value.idNumber);
    formData.append('profile[nationality]', (this.candidateForm.value.nationality === null ) ? null : this.candidateForm.value.nationality);
    formData.append('profile[ethnicity]', this.candidateForm.value.ethnicity);
    formData.append('profile[beeCheck]', (!this.candidateForm.value.beeCheck) ? null : (this.candidateForm.value.beeCheck.formatted) ? this.candidateForm.value.beeCheck.formatted : this.candidateForm.value.beeCheck);
    formData.append('profile[gender]', this.candidateForm.value.gender);
    formData.append('profile[dateOfBirth]', (!this.candidateForm.value.dateOfBirth) ? null : (this.candidateForm.value.dateOfBirth.formatted) ? this.candidateForm.value.dateOfBirth.formatted :
      this.candidateForm.value.dateOfBirth.date.year + '/' + this.candidateForm.value.dateOfBirth.date.month + '/' + this.candidateForm.value.dateOfBirth.date.day);
    formData.append('profile[criminal]', this.candidateForm.value.criminal);
    formData.append('profile[criminalDescription]', (this.candidateForm.value.criminal === false) ? null : this.candidateForm.value.criminalDescription);
    formData.append('profile[credit]', this.candidateForm.value.credit);
    formData.append('profile[creditDescription]', (this.candidateForm.value.credit === false) ? null :this.candidateForm.value.creditDescription);
    formData.append('profile[homeAddress]', this.candidateForm.value.homeAddress);
    formData.append('profile[driverLicense]', this.candidateForm.value.driverLicense);
    formData.append('profile[driverNumber]', this.candidateForm.value.driverNumber);
    formData.append('profile[englishProficiency]', this.candidateForm.value.englishProficiency);
    formData.append('profile[availability]', this.candidateForm.value.availability);
    formData.append('profile[dateAvailability]', (this.candidateForm.value.dateAvailability === null ) ? null : this.candidateForm.value.dateAvailability.formatted);
    formData.append('profile[availabilityPeriod]', this.candidateForm.value.availabilityPeriod);
    formData.append('profile[citiesWorking]', (this.candidateForm.value.citiesWorking === undefined) ? null : this.candidateForm.value.citiesWorking);


    if(this.picture.nativeElement.files.length > 0){
      formData.append('picture', this.picture.nativeElement.files[0]);
    }
    if(this.video.nativeElement.files.length > 0){
      formData.append('video', this.video.nativeElement.files[0]);
    }

    if (this.candidateForm.valid) {
      if(formData.get('copyOfID[]') !== null){
        if(this.candidateForm.value.driverLicense === true && (
            this.candidateForm.value.driverNumber === null
            || this.candidateForm.value.driverNumber === ''
            || this.candidateForm.value.driverNumber.length !== 12
            || !(/[A-Za-z0-9]{12}/i.test(this.candidateForm.value.driverNumber))
          )
        ) {
          this._toastr.error('Please enter a valid driver license number');
          this.checkDriverLicenseNumber = true;
          this.buttonPreloader = false;
        } else if(this.candidateForm.value.englishProficiency === null) {
          this._toastr.error('English Proficiency is required');
          this.checksEnglishProficiency = true;
          this.buttonPreloader = false;
        } else if(this.candidateForm.value.availability === false && !this.candidateForm.value.availabilityPeriod){
          this._toastr.error('Availability Period is required');
          this.checksAvailabilityPeriod = true;
          this.buttonPreloader = false;
        }
        else if(this.candidateForm.value.availabilityPeriod === 4 && this.candidateForm.value.dateAvailability === null) {
          this._toastr.error('Date Availability is required');
          this.checksAvailabilityDate = true;
          this.buttonPreloader = false;
        }
        else{
          try {
            await this._adminService.createdCandidateProfile(formData);
            this._toastr.success('Candidate has been created');
            this._sharedService.sidebarAdminBadges.candidateAll++;
            this.buttonPreloader = false;
            this._router.navigate(['/admin/all_candidates']);
          }
          catch(err) {
            this._sharedService.showRequestErrors(err);
            this.buttonPreloader = false;
          }
        }
      }
      else{
        this._toastr.error('Please upload copy of ID');
        this.buttonPreloader = false;
      }
    } else {
      this.buttonPreloader = false;
      if(this.candidateProfileDetails.profile.availability === false && !this.candidateForm.value.availabilityPeriod){
        this._toastr.error('Availability Period is required');
        this.checksAvailabilityPeriod = true;
      }
      if(this.candidateForm.value.driverLicense === true && (
          this.candidateForm.value.driverNumber === null
          || this.candidateForm.value.driverNumber === ''
          || this.candidateForm.value.driverNumber.length !== 12
          || !(/[A-Za-z0-9]{12}/i.test(this.candidateForm.value.driverNumber))
        )
      ) {
        this._toastr.error('Please enter a valid driver license number');
        this.checkDriverLicenseNumber = true;
        this.buttonPreloader = false;
      }
      if(this.candidateForm.value.englishProficiency === null) {
        this._toastr.error('English Proficiency is required');
        this.checksEnglishProficiency = true;
        this.buttonPreloader = false;
      }
      this._sharedService.validateAlertCandidateForm(this.candidateForm);
      this._sharedService.validateAllFormFields(this.candidateForm);
    }
    this.buttonPreloader = false;
  }

}
