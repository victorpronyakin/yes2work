import { Component, ElementRef, NgZone, OnInit, ViewChild } from '@angular/core';
import { INgxMyDpOptions } from 'ngx-mydatepicker';
import { IMultiSelectOption, IMultiSelectSettings, IMultiSelectTexts } from 'angular-2-dropdown-multiselect';
import { FormControl, FormGroup, Validators } from '@angular/forms';
import {
  AdminCandidateProfile,
  AdminCandidateProfileNew, AdminCandidateUser,
  AdminCandidateUserProfileNew
} from '../../../../../entities/models-admin';
import {} from '@types/googlemaps';
import { SharedService } from '../../../../services/shared.service';
import { MapsAPILoader } from '@agm/core';
import { ToastrService } from 'ngx-toastr';
import {
  CustomValidateIdNumber, ValidateAvailabilityDate, ValidateIdNumber,
  ValidateNumber
} from '../../../../validators/custom.validator';
import { ActivatedRoute, Router } from '@angular/router';
import { AdminService } from '../../../../services/admin.service';

@Component({
  selector: 'app-admin-edit-candidate-profile',
  templateUrl: './admin-edit-candidate-profile.component.html',
  styleUrls: ['./admin-edit-candidate-profile.component.scss']
})
export class AdminEditCandidateProfileComponent implements OnInit {
  @ViewChild('picture') private picture : ElementRef;
  @ViewChild('matricCertificate') private matricCertificate : ElementRef;
  @ViewChild('tertiaryCertificate') private tertiaryCertificate : ElementRef;
  @ViewChild('universityManuscript') private universityManuscript : ElementRef;
  @ViewChild('videoPlayer') private videoPlayer: ElementRef;
  @ViewChild('video') private video : ElementRef;
  @ViewChild('addressField') private addressField: ElementRef;


  @ViewChild('academicTranscript') private academicTranscript: ElementRef;
  @ViewChild('copyOfID') private copyOfID: ElementRef;
  @ViewChild('cv') private cv: ElementRef;
  @ViewChild('matricTranscript') private matricTranscript: ElementRef;
  @ViewChild('certificateOfQualification') private certificateOfQualification: ElementRef;
  @ViewChild('payslip') private payslip : ElementRef;

  public checkCopyOfID = false;
  public checkCV = false;
  public checkMatricCertificate = false;
  public checkMatricTranscript = false;
  public checkCertificateOfQualification = false;
  public checkAcademicTranscript = false;
  public checkCreditFile = false;
  public checkPayslip = false;

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
  public candidateProfileDetailsUpdate = new AdminCandidateProfileNew({
    'user': new AdminCandidateUser({}),
    'profile': new AdminCandidateUserProfileNew({})
  });

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

  public matricCertificateArray = [];
  public tertiaryCertificateArray = [];
  public universityManuscriptArray = [];
  public creditCheckArray = [];
  public cvFilesArray = [];
  public profilePicture = [];
  public copyOfIDArray = [];
  public cvArray = [];
  public matricTranscriptArray = [];
  public certificateOfQualificationArray = [];
  public academicTranscriptArray = [];
  public payslipArray = [];
  public profileVideo: any;

  public myOptionsDate: INgxMyDpOptions = { dateFormat: 'yyyy/mm/dd' };
  public model: any = { date: { year: 2018, month: 10, day: 9 } };

  public preloaderPage = true;
  public preloaderPicture = false;
  public buttonPreloader = false;
  public buttonPreloaderVideo = false;
  public preloaderVideo = false;

  public articlesOther = false;
  public saicaStatus = false;
  public checkSaica = true;

  public checkCvFiles = false;
  public checkTertiaryCertificate = false;
  public checkUniversityManuscript = false;

  public availabilityPeriodStatus = false;
  public checksAvailabilityPeriod = false;
  public checksAvailabilityDate = false;

  public visibilityLooking = false;
  public checkLooking: boolean;
  public checkVideo;
  public allowVideo;
  public checksEnglishProficiency = false;
  public checkDriverLicenseNumber = false;


  constructor(
    private readonly _toastr: ToastrService,
    private readonly _mapsAPILoader: MapsAPILoader,
    private readonly _ngZone: NgZone,
    public readonly _sharedService: SharedService,
    private readonly _route: ActivatedRoute,
    private readonly _adminService: AdminService,
    private readonly _router: Router
  ) {
    this._sharedService.checkSidebar = false;
  }

  ngOnInit() {
    window.scrollTo(0, 0);
    this.candidateForm = new FormGroup({
      firstName: new FormControl('', Validators.required),
      lastName: new FormControl('', Validators.required),
      phone: new FormControl('', [
        Validators.required,
        Validators.minLength(9),
        ValidateNumber
      ]),
      agentName: new FormControl(''),
      email: new FormControl('', Validators.required),

      idNumber: new FormControl('', Validators.compose([
        CustomValidateIdNumber
      ])),
      nationality: new FormControl('', Validators.required),
      ethnicity: new FormControl('', Validators.required),
      beeCheck: new FormControl(null),
      gender: new FormControl('', Validators.required),
      dateOfBirth: new FormControl(''),
      criminal: new FormControl(''),
      criminalDescription: new FormControl(''),
      credit: new FormControl(''),
      creditDescription: new FormControl(''),
      homeAddress: new FormControl('', Validators.required),
      driverLicense: new FormControl(null),
      driverNumber: new FormControl(''),
      englishProficiency: new FormControl(null),
      availability: new FormControl(null),
      dateAvailability: new FormControl(null),
      availabilityPeriod: new FormControl(null),
      citiesWorking: new FormControl(null)
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

    this._route.queryParams.subscribe(data => {
      this.getCandidateProfileDetails(data.candidateId);
    });

    this.myOptions = this._sharedService.citiesWorking;
  }

  /**
   * Address input autocomplete focus
   */
  public addressFocus() {
    this.addressField.nativeElement.setAttribute("autocomplete", "new-password");
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
   * Check availability value
   */
  public availableValue(){
    this.candidateProfileDetails.profile.availability = !this.candidateProfileDetails.profile.availability;
    if (this.candidateProfileDetails.profile.availability === true){
      this.candidateForm.get('availabilityPeriod').setValue(null, { onlySelf: true });
      this.availabilityPeriodStatus = false;
      this.checksAvailabilityPeriod = false;
      this.checksAvailabilityDate = false;
    }
  }

  /**
   * Get details profile candidate
   * @return {Promise<void>}
   */
  public async getCandidateProfileDetails(id): Promise<void>{
    this.candidateProfileDetails = await this._adminService.getCandidateProfileDetails(id);

    this.candidateForm.setValue({
      firstName: this.candidateProfileDetails.user.firstName,
      lastName: this.candidateProfileDetails.user.lastName,
      phone: this.candidateProfileDetails.user.phone,
      email: this.candidateProfileDetails.user.email,
      agentName: this.candidateProfileDetails.user.agentName,

      idNumber: this.candidateProfileDetails.profile.idNumber,
      citiesWorking: this.candidateProfileDetails.profile.citiesWorking,
      availability: (this.candidateProfileDetails.profile.availability === null) ? true : this.candidateProfileDetails.profile.availability,
      nationality: this.candidateProfileDetails.profile.nationality,
      ethnicity: this.candidateProfileDetails.profile.ethnicity,
      beeCheck: this.candidateProfileDetails.profile.beeCheck,
      gender: this.candidateProfileDetails.profile.gender,
      dateOfBirth: this.candidateProfileDetails.profile.dateOfBirth,
      criminal: (this.candidateProfileDetails.profile.criminal === null) ? false : this.candidateProfileDetails.profile.criminal,
      criminalDescription: this.candidateProfileDetails.profile.criminalDescription,
      credit: (this.candidateProfileDetails.profile.credit === null) ? false : this.candidateProfileDetails.profile.credit,
      creditDescription: this.candidateProfileDetails.profile.creditDescription,
      homeAddress: this.candidateProfileDetails.profile.homeAddress,
      driverLicense: (this.candidateProfileDetails.profile.driverLicense === null) ? false : this.candidateProfileDetails.profile.driverLicense,
      driverNumber: this.candidateProfileDetails.profile.driverNumber,
      englishProficiency: this.candidateProfileDetails.profile.englishProficiency,
      availabilityPeriod: this.candidateProfileDetails.profile.availabilityPeriod,
      dateAvailability: this.candidateProfileDetails.profile.dateAvailability,
    });

    if(this.candidateProfileDetails.profile.percentage < 50 || !this.candidateProfileDetails.profile.copyOfID ||
      !this.candidateProfileDetails.profile.copyOfID[0] ||
      !this.candidateProfileDetails.profile.copyOfID[0].approved ||
      (this.candidateProfileDetails.allowVideo === false && !this.candidateProfileDetails.profile.video) ||
      (this.candidateProfileDetails.allowVideo === false && this.candidateProfileDetails.profile.video && this.candidateProfileDetails.profile.video.approved === false)) {
      this.checkLooking = false;
      this.visibilityLooking = true;
    } else {
      this.checkLooking = this.candidateProfileDetails.profile.looking;
    }

    this.checkVideo = this.candidateProfileDetails.profile.video;
    this.allowVideo = this.candidateProfileDetails['allowVideo'];

    if(this.candidateProfileDetails.profile.availabilityPeriod === 4){
      this.availabilityPeriodStatus = true;
    }
    if (this.candidateProfileDetails.profile.availability === null){
      this.candidateProfileDetails.profile.availability = true;
    }

    let dateAvailability = new Date(this.candidateProfileDetails.profile.dateAvailability);


    if (this.candidateProfileDetails.profile.dateAvailability === null){
      dateAvailability = null;
    } else {
      dateAvailability = new Date(this.candidateProfileDetails.profile.dateAvailability);
      this.candidateForm.patchValue({
        dateAvailability: {
          date: {
            year: dateAvailability.getFullYear(),
            month: dateAvailability.getMonth() + 1,
            day: dateAvailability.getDate(),
          }
        }
      });
    }

    let dateOfBirth = new Date(this.candidateProfileDetails.profile.dateOfBirth);

    if (this.candidateProfileDetails.profile.dateOfBirth === null){
      dateOfBirth = null;
    } else {
      dateOfBirth = new Date(this.candidateProfileDetails.profile.dateOfBirth);
      this.candidateForm.patchValue({
        dateOfBirth: {
          date: {
            year: dateOfBirth.getFullYear(),
            month: dateOfBirth.getMonth() + 1,
            day: dateOfBirth.getDate(),
          }
        }
      });
    }

    if (this.candidateProfileDetails.profile.beeCheck !== null){
      const beeCheckDate = new Date(this.candidateProfileDetails.profile.beeCheck);
      this.candidateForm.patchValue({
        beeCheck: {
          date: {
            year: beeCheckDate.getFullYear(),
            month: beeCheckDate.getMonth() + 1,
            day: beeCheckDate.getDate(),
          }
        }
      });
    }

    this._sharedService.progressBar = Number(this.candidateProfileDetails.profile.percentage);
    this.copyOfIDArray = (this.candidateProfileDetails.profile.copyOfID === null) ? [] : this.candidateProfileDetails.profile.copyOfID;
    this.cvArray = (this.candidateProfileDetails.profile.cv === null) ? [] : this.candidateProfileDetails.profile.cv;
    this.matricCertificateArray = (this.candidateProfileDetails.profile.matricCertificate === null) ? [] : this.candidateProfileDetails.profile.matricCertificate;
    this.matricTranscriptArray = (this.candidateProfileDetails.profile.matricTranscript === null) ? [] : this.candidateProfileDetails.profile.matricTranscript;
    this.certificateOfQualificationArray = (this.candidateProfileDetails.profile.certificateOfQualification === null) ? [] : this.candidateProfileDetails.profile.certificateOfQualification;
    this.academicTranscriptArray = (this.candidateProfileDetails.profile.academicTranscript === null) ? [] : this.candidateProfileDetails.profile.academicTranscript;
    this.creditCheckArray = (this.candidateProfileDetails.profile.creditCheck === null) ? [] : this.candidateProfileDetails.profile.creditCheck;
    this.payslipArray = (this.candidateProfileDetails.profile.payslip === null) ? [] : this.candidateProfileDetails.profile.payslip;
    this.profilePicture = this.candidateProfileDetails.profile.picture;

    this.profileVideo = this.candidateProfileDetails.profile.video;

    this.preloaderPage = false;
  }

  /**
   * Check criminal value
   */
  public criminalValue(){
    this.candidateProfileDetails.profile.criminal = !this.candidateProfileDetails.profile.criminal;
  }

  /**
   * Update profile files
   * @param fieldName {string}
   * @return {Promise<void>}
   */
  public async updateProfileFiles(fieldName: string): Promise<void> {
    const formData = new FormData();
    if (fieldName === 'picture') {
      this.preloaderPicture = true;
    }

    if (fieldName === 'copyOfID') {
      this.checkCopyOfID = true;
    }

    if (fieldName === 'cv') {
      this.checkCV = true;
    }

    if (fieldName === 'matricCertificate') {
      this.checkMatricCertificate = true;
    }

    if (fieldName === 'matricTranscript') {
      this.checkMatricTranscript = true;
    }

    if (fieldName === 'certificateOfQualification') {
      this.checkCertificateOfQualification = true;
    }

    if (fieldName === 'academicTranscript') {
      this.checkAcademicTranscript = true;
    }

    if (fieldName === 'payslip') {
      this.checkPayslip = true;
    }

    for (let i = 0; i < this[fieldName].nativeElement.files.length; i++) {
      formData.append(''+ fieldName +'[]', this[fieldName].nativeElement.files[i]);
    }

    try{
      const data = await this._adminService.updateProfileFiles(formData, this.candidateProfileDetails.user.id);

      if(data.files != {} && data.files.copyOfID) {
        this.checkCopyOfID = false;
        this.copyOfIDArray = data.files.copyOfID;
        this.copyOfID.nativeElement.value = '';
      }

      if(data.files != {} && data.files.cv) {
        this.checkCV = false;
        this.cvArray = data.files.cv;
        this.cv.nativeElement.value = '';
      }

      if(data.files != {} && data.files.matricCertificate) {
        this.checkMatricCertificate = false;
        this.matricCertificateArray = data.files.matricCertificate;
        this.matricCertificate.nativeElement.value = '';
      }

      if (data.files != {} && data.files.matricTranscript) {
        this.checkMatricTranscript = false;
        this.matricTranscriptArray = data.files.matricTranscript;
        this.matricTranscript.nativeElement.value = '';
      }

      if (data.files != {} && data.files.certificateOfQualification) {
        this.checkCertificateOfQualification = false;
        this.certificateOfQualificationArray = data.files.certificateOfQualification;
        this.certificateOfQualification.nativeElement.value = '';
      }

      if (data.files != {} && data.files.academicTranscript) {
        this.checkAcademicTranscript = false;
        this.academicTranscriptArray = data.files.academicTranscript;
        this.academicTranscript.nativeElement.value = '';
      }

      if (data.files != {} && data.files.payslip) {
        this.checkPayslip = false;
        this.payslipArray = data.files.payslip;
        this.payslip.nativeElement.value = '';
      }

      if (data.files != {} && data.files.picture) {
        this.profilePicture = data.files.picture;
        this.picture.nativeElement.value = '';
      }


      if (data.percentage < 50 || !this.copyOfIDArray || this.copyOfIDArray.length === 0) {
        this._sharedService.visibleErrorProfileIcon = true;
      } else {
        this._sharedService.visibleErrorProfileIcon = false;
      }

      localStorage.setItem('progressBar', data.percentage);
      this._sharedService.progressBar = Number(localStorage.getItem('progressBar'));
      setTimeout(() => {
        this.preloaderPicture = false;
      }, 500);

      this._toastr.success('File has been added');
    }
    catch (err) {
      this._sharedService.showRequestErrors(err);
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
      if (this.candidateProfileDetails.profile.availability === false){
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

  public checkDateAvailable(date) {
    if ( this.dateDiffinMonths(new Date(), date.jsdate) >= 6 ) {
      // this.openVerticallyCenterd(this.dataAvailable);
    }
  }

  public dateDiffinMonths(d1, d2) {
    const d1Y = d1.getFullYear();
    const d2Y = d2.getFullYear();
    const d1M = d1.getMonth();
    const d2M = d2.getMonth();

    return (d2M + 12 * d2Y) - (d1M + 12 * d1Y);
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
   * Update profile video
   * @param fieldName {string}
   * @return {Promise<void>}
   */
  public async updateProfileVideo(fieldName: string): Promise<void> {
    this.buttonPreloaderVideo = true;
    const formData = new FormData();

    for (let i = 0; i < this[fieldName].nativeElement.files.length; i++) {
      formData.append(''+ fieldName, this[fieldName].nativeElement.files[i]);
    }

    try{
      const data = await this._adminService.updateProfileVideo(formData, this.candidateProfileDetails.user.id);

      if (data.video != {} && data.video) {
       this.profileVideo = data.video;
       this.video.nativeElement.value = '';
      }
      this.buttonPreloaderVideo = false;

      localStorage.setItem('progressBar', data.percentage);
      this._sharedService.progressBar = Number(localStorage.getItem('progressBar'));

      this._toastr.success('Video has been added');
    }
    catch (err) {
      this._sharedService.showRequestErrors(err);
      this.buttonPreloaderVideo = false;
    }
    this.buttonPreloaderVideo = false;
  }

  /**
   * Remove candidate video
   * @return {Promise<void>}
   */
  public async removeVideo(): Promise<void>{
    this.preloaderVideo = true;
    try {
      const response = await this._adminService.removeVideo(this.candidateProfileDetails.user.id);

      this.profileVideo = null;
      this.preloaderVideo = false;
      this._toastr.success('Video has been deleted');
      localStorage.setItem('progressBar', response.percentage);
      this._sharedService.progressBar = Number(localStorage.getItem('progressBar'));
    }
    catch (err){
      this._sharedService.showRequestErrors(err);
      this.preloaderVideo = false;
    }
  }

  /**
   * Update candidate profile
   * @return {Promise<void>}
   */
  public async updateAdminCandidateProfile(): Promise<void> {
    this.buttonPreloader = true;

    this.candidateProfileDetailsUpdate.user['firstName'] = this.candidateForm.value.firstName;
    this.candidateProfileDetailsUpdate.user['lastName'] = this.candidateForm.value.lastName;
    this.candidateProfileDetailsUpdate.user['phone'] = this.candidateForm.value.phone;
    this.candidateProfileDetailsUpdate.user['email'] = this.candidateForm.value.email;
    this.candidateProfileDetailsUpdate.user['agentName'] = this.candidateForm.value.agentName;

    this.candidateProfileDetailsUpdate.profile['idNumber'] = this.candidateForm.value.idNumber;
    this.candidateProfileDetailsUpdate.profile['nationality'] = (this.candidateForm.value.nationality === null ) ? null : this.candidateForm.value.nationality;
    this.candidateProfileDetailsUpdate.profile['ethnicity'] = (this.candidateForm.value.ethnicity === null ) ? null : this.candidateForm.value.ethnicity;
    this.candidateProfileDetailsUpdate.profile['beeCheck'] = (this.candidateForm.value.beeCheck === null || this.candidateForm.value.beeCheck === undefined) ? null : (this.candidateForm.value.nationality === 1 || this.candidateForm.value.ethnicity === 'White') ? null : new Date(this.candidateForm.value.beeCheck.date.year + '.'  + this.candidateForm.value.beeCheck.date.month + '.'  + this.candidateForm.value.beeCheck.date.day);
    this.candidateProfileDetailsUpdate.profile['gender'] = (this.candidateForm.value.gender === null) ? null : this.candidateForm.value.gender;
    this.candidateProfileDetailsUpdate.profile['dateOfBirth'] = (this.candidateForm.value.dateOfBirth === null ) ? null : new Date(this.candidateForm.value.dateOfBirth.date.year + '.' + this.candidateForm.value.dateOfBirth.date.month + '.'  + this.candidateForm.value.dateOfBirth.date.day);
    this.candidateProfileDetailsUpdate.profile['criminal'] = this.candidateForm.value.criminal;
    this.candidateProfileDetailsUpdate.profile['criminalDescription'] = (this.candidateForm.value.criminal === false) ? null : this.candidateForm.value.criminalDescription;
    this.candidateProfileDetailsUpdate.profile['credit'] = this.candidateForm.value.credit;
    this.candidateProfileDetailsUpdate.profile['creditDescription'] = (this.candidateForm.value.credit === false) ? null : this.candidateForm.value.creditDescription;
    this.candidateProfileDetailsUpdate.profile['homeAddress'] = this.candidateForm.value.homeAddress;
    this.candidateProfileDetailsUpdate.profile['driverLicense'] = this.candidateForm.value.driverLicense;
    this.candidateProfileDetailsUpdate.profile['driverNumber'] = this.candidateForm.value.driverNumber;
    this.candidateProfileDetailsUpdate.profile['englishProficiency'] = this.candidateForm.value.englishProficiency;

    this.candidateProfileDetailsUpdate.profile['availability'] = this.candidateForm.value.availability;
    this.candidateProfileDetailsUpdate.profile['dateAvailability'] = (this.candidateForm.value.dateAvailability === null ) ? null : new Date(this.candidateForm.value.dateAvailability.date.year + '.'  + this.candidateForm.value.dateAvailability.date.month + '.'  + this.candidateForm.value.dateAvailability.date.day);
    this.candidateProfileDetailsUpdate.profile['availabilityPeriod'] = this.candidateForm.value.availabilityPeriod;
    this.candidateProfileDetailsUpdate.profile['citiesWorking'] = (this.candidateForm.value.citiesWorking === undefined) ? null : this.candidateForm.value.citiesWorking;

    if (this.candidateForm.valid) {
      if(this.copyOfIDArray !== null && this.copyOfIDArray.length > 0){
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
        } else if(this.candidateProfileDetails.profile.availability === false && !this.candidateForm.value.availabilityPeriod){
          this._toastr.error('Availability Period is required');
          this.checksAvailabilityPeriod = true;
          this.buttonPreloader = false;
        } else if(this.candidateForm.value.availabilityPeriod === 4 && this.candidateForm.value.dateAvailability === null) {
          this._toastr.error('Date Availability is required');
          this.checksAvailabilityDate = true;
          this.buttonPreloader = false;
        } else{
          this._adminService.updateAdminCandidateProfile(this.candidateProfileDetailsUpdate, this.candidateProfileDetails.user.id).then(data => {

            localStorage.setItem('progressBar', data.percentage);
            this._sharedService.progressBar = Number(localStorage.getItem('progressBar'));

            this._toastr.success('Profile has been updated');

            this._router.navigateByUrl('/admin/all_candidates');

            this.buttonPreloader = false;

          }).catch(err => {
            this._sharedService.showRequestErrors(err);
            this.buttonPreloader = false;
          })
        }
      }
      else{
        this._toastr.error('Please upload copy of ID');
        this.buttonPreloader = false;
      }
    } else {
      if(this.candidateProfileDetails.profile.availability === false && !this.candidateForm.value.availabilityPeriod){
        this._toastr.error('Availability Period is required');
        this.checksAvailabilityPeriod = true;
      }
      if(this.candidateForm.value.availabilityPeriod === 4 && this.candidateForm.value.dateAvailability === null) {
        this._toastr.error('Date Availability is required');
        this.checksAvailabilityDate = true;
        this.buttonPreloader = false;
      }
      if (this.candidateForm.value.driverLicense === true && (
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
      if (this.candidateForm.value.englishProficiency === null) {
        this._toastr.error('English Proficiency is required');
        this.checksEnglishProficiency = true;
        this.buttonPreloader = false;
      }
      this._sharedService.validateAlertCandidateForm(this.candidateForm);
      this._sharedService.validateAllFormFields(this.candidateForm);
      this.buttonPreloader = false;
    }
    this.buttonPreloader = false;
  }

  /**
   * Remove file
   * @param fieldName {string}
   * @param url {string}
   * @return {Promise<void>}
   */
  public async removeFile(fieldName: string, url: string): Promise<void> {
    if (fieldName === 'picture') {
      this.preloaderPicture = true;
    }

    try {
      await this._adminService.removeFile(fieldName, url, this.candidateProfileDetails.user.id).then(data => {
        if (fieldName === 'matricCertificate') {
          this.matricCertificateArray = data[fieldName];
        } else if(fieldName === 'tertiaryCertificate') {
          this.tertiaryCertificateArray = data[fieldName];
        } else if (fieldName === 'universityManuscript') {
          this.universityManuscriptArray = data[fieldName];
        } else if (fieldName === 'creditCheck') {
          this.creditCheckArray = data[fieldName];
        } else if (fieldName === 'cvFiles') {
          this.cvFilesArray = data[fieldName];
        } else if (fieldName === 'picture') {
          this.profilePicture = data[fieldName];
          setTimeout(() => {
            this.preloaderPicture = false;
          }, 500);
        }

        localStorage.setItem('progressBar', data.percentage);
        this._sharedService.progressBar = Number(localStorage.getItem('progressBar'));
      });
      this._toastr.success('File was deleted successfully');
    } catch (err) {
      this._toastr.error(err.error.error);
    }
  }

}
