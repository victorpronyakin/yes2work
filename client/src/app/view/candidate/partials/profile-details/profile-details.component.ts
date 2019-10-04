import { ChangeDetectionStrategy, Component, ElementRef, HostListener, NgZone, OnInit, ViewChild } from '@angular/core';
import { FormControl, FormGroup, Validators } from '@angular/forms';
import { ApiService } from '../../../../services/api.service';
import { AdminCandidateProfile, AdminCandidateProfileNew,
         AdminCandidateUser, AdminCandidateUserProfileNew } from '../../../../../entities/models-admin';
import { CandidateService } from '../../../../services/candidate.service';
import { MapsAPILoader } from '@agm/core';
import { ToastrService } from 'ngx-toastr';
import {} from '@types/googlemaps';
import { IMultiSelectSettings, IMultiSelectTexts } from 'angular-2-dropdown-multiselect';
import { INgxMyDpOptions } from 'ngx-mydatepicker';
import { SharedService } from '../../../../services/shared.service';
import {
  CustomValidateIdNumber,
  ValidateNumber
} from '../../../../validators/custom.validator';
import { NgbModal } from '@ng-bootstrap/ng-bootstrap';
import { Router } from '@angular/router';
import { Subject } from 'rxjs/Subject';
import { WebcamImage, WebcamInitError } from 'ngx-webcam';
import { Observable } from 'rxjs/Observable';

@Component({
  selector: 'app-profile-details',
  changeDetection: ChangeDetectionStrategy.Default,
  templateUrl: './profile-details.component.html',
  styleUrls: ['./profile-details.component.scss']
})
export class ProfileDetailsComponent implements OnInit {
  @ViewChild('copyOfID') private copyOfID: ElementRef;
  @ViewChild('cv') private cv: ElementRef;
  @ViewChild('matricCertificate') private matricCertificate : ElementRef;
  @ViewChild('matricTranscript') private matricTranscript: ElementRef;
  @ViewChild('certificateOfQualification') private certificateOfQualification: ElementRef;
  @ViewChild('academicTranscript') private academicTranscript: ElementRef;
  @ViewChild('creditCheck') private creditCheck: ElementRef;
  @ViewChild('payslip') private payslip: ElementRef;
  @ViewChild('picture') private picture: ElementRef;
  @ViewChild('label') public label: ElementRef;
  @ViewChild('content') private content: ElementRef;
  @ViewChild('salary') private salary: ElementRef;
  @ViewChild('dataAvailable') private dataAvailable: ElementRef;
  @ViewChild('addressField') private addressField: ElementRef;

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

  public optionsModel: string[];

  public matricCertificateArray = [];
  public creditCheckArray = [];
  public copyOfIDArray = [];
  public profilePicture = [];
  public cvArray = [];
  public matricTranscriptArray = [];
  public certificateOfQualificationArray = [];
  public academicTranscriptArray = [];
  public payslipArray = [];

  public myOptionsDate: INgxMyDpOptions = { dateFormat: 'yyyy/mm/dd' };
  public model: any = { date: { year: 2018, month: 10, day: 9 } };

  public preloaderPage = true;
  public preloaderPicture = false;
  public buttonPreloader = false;

  public articlesOther = false;
  public saicaStatus = false;
  public checkSaica = true;

  public checkCopyOfID = false;
  public checkCV = false;
  public checkMatricCertificate = false;
  public checkMatricTranscript = false;
  public checkCertificateOfQualification = false;
  public checkAcademicTranscript = false;
  public checkCreditFile = false;
  public checkPayslip = false;

  public availabilityPeriodStatus = false;
  public urlRedirect: string;
  public modalActiveClose: any;
  public checksAvailabilityPeriod = false;
  public checksAvailabilityDate = false;
  public checksEmployedDate = false;

  public visibilityLooking = false;
  public checkLooking: boolean;
  public videoUploadPopup = false;
  public photoUploadPopup = false;
  public visibleActivePopup = false;

  public checkVideo;
  public allowVideo;

  public checkDriverLicenseNumber = false;

  public showWebcam = false;
  public videoOptions: MediaTrackConstraints = {};
  public webcamImage: WebcamImage = null;
  private trigger: Subject<void> = new Subject<void>();
  private nextWebcam: Subject<boolean|string> = new Subject<boolean|string>();

  public checksEnglishProficiency = false;

  public salaryAgain = false;

  public isIE = false;
  public showWebCamAlert = false;

  constructor(
    private readonly _apiService: ApiService,
    private readonly _candidateService: CandidateService,
    private readonly _toastr: ToastrService,
    private readonly _mapsAPILoader: MapsAPILoader,
    private readonly _ngZone: NgZone,
    public readonly _sharedService: SharedService,
    private _modalService: NgbModal,
    private readonly _router: Router
  ) {
    this._sharedService.checkSidebar = false;
    this._sharedService.progressBar = Number(localStorage.getItem('progressBar'));
  }

  ngOnInit() {
    window.scrollTo(0, 0);

    this.detectIE();

    this.candidateForm = new FormGroup({
      firstName: new FormControl('', Validators.required),
      lastName: new FormControl('', Validators.required),
      phone: new FormControl('', [
        Validators.required,
        Validators.minLength(9),
        ValidateNumber
      ]),
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
      universityExemption: new FormControl(null),
    });

    this._mapsAPILoader.load().then(() => {
        const autocomplete = new google.maps.places.Autocomplete((<HTMLInputElement>document.getElementById('search1')),
        { types: ['address'] });
        autocomplete.addListener('place_changed', () => {
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

    this.getCandidateProfileDetails();
  }

  public detectIE() {
    const match = navigator.userAgent.search(/(?:Edge|MSIE|Trident\/.*; rv:)/);
    this.isIE = false;

    if (match !== -1) {
        this.isIE = true;
    }
    return this.isIE;
  }

  @HostListener('window:beforeunload')
  onBeforeUnload() {
    if (this.candidateForm.dirty === true && this.candidateForm.touched === true) {
      const confirmTest = 'Are you sure you want to leave now?';
      window.event.returnValue = false;
      return confirmTest;
    }
  }

  canDeactivate(url) {
    this.urlRedirect = url;
    if (this.candidateForm.dirty === true && this.candidateForm.touched === true) {
      this.openVerticallyCentered(this.content);
    } else {
      return true;
    }
  }

  public addressFocus() {
    this.addressField.nativeElement.setAttribute('autocomplete', 'new-password');
  }

  public triggerSnapshot(): void {
    this.trigger.next();
  }

  public handleInitError(error: WebcamInitError): void {
    this.closePhotoPopup(true);
    this.showWebCamAlert = true;
  }

  public handleImage(webcamImage: WebcamImage): void {
    this.webcamImage = webcamImage;
  }

  public get triggerObservable(): Observable<void> {
    return this.trigger.asObservable();
  }

  public get nextWebcamObservable(): Observable<boolean|string> {
    return this.nextWebcam.asObservable();
  }

  public reshootPhoto() {
    this.webcamImage = null;
  }

  /**
   * Select change router
   * @param url
   */
  public routerApplicants(url): void {
    this._router.navigate([url]);
  }

  /**
   * Exit add job page
   */
  exitPage(){
    this.modalActiveClose.dismiss();
    this.candidateForm.markAsPristine();
    this._router.navigate([this.urlRedirect]);
  }

  /**
   * Get details profile candidate
   * @return {Promise<void>}
   */
  public async getCandidateProfileDetails(): Promise<void> {
    this.candidateProfileDetails = await this._candidateService.getCandidateProfileDetails();

    this.candidateForm.setValue({
      firstName: this.candidateProfileDetails.user.firstName,
      lastName: this.candidateProfileDetails.user.lastName,
      phone: this.candidateProfileDetails.user.phone,
      email: this.candidateProfileDetails.user.email,

      idNumber: this.candidateProfileDetails.profile.idNumber,
      nationality: this.candidateProfileDetails.profile.nationality,
      ethnicity: this.candidateProfileDetails.profile.ethnicity,
      beeCheck: this.candidateProfileDetails.profile.beeCheck,
      gender: this.candidateProfileDetails.profile.gender,
      dateOfBirth: this.candidateProfileDetails.profile.dateOfBirth,
      criminal: (this.candidateProfileDetails.profile.criminal === null) ? false : this.candidateProfileDetails.profile.criminal,
      universityExemption: (this.candidateProfileDetails.profile.universityExemption === null) ? false : this.candidateProfileDetails.profile.universityExemption,
      criminalDescription: this.candidateProfileDetails.profile.criminalDescription,
      credit: (this.candidateProfileDetails.profile.credit === null) ? false : this.candidateProfileDetails.profile.credit,
      creditDescription: this.candidateProfileDetails.profile.creditDescription,
      homeAddress: this.candidateProfileDetails.profile.homeAddress,
      driverLicense: (this.candidateProfileDetails.profile.driverLicense === null) ? false : this.candidateProfileDetails.profile.driverLicense,
      driverNumber: this.candidateProfileDetails.profile.driverNumber,
      englishProficiency: this.candidateProfileDetails.profile.englishProficiency,
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

    this.preloaderPage = false;
  }

  /**
   * Change status candidate
   * @param field {string}
   * @param value {boolean}
   */
  public changeStatusCandidate(field: string, value: boolean){
    let error = true;
    if (this.candidateProfileDetails.profile.percentage < 50) {
      this._toastr.error('Your profile needs to be 50% complete');
      error = false;
      if (field === 'looking'){
        this.checkLooking = false;
      }
    }
    if (!this.candidateProfileDetails.profile.copyOfID || this.candidateProfileDetails.profile.copyOfID.length === 0) {
      this._toastr.error('Upload a copy of your ID in Edit Profile');
      error = false;
      if (field === 'looking') {
        this.checkLooking = false;
      }
    }
    if (this.candidateProfileDetails.profile.copyOfID[0] && !this.candidateProfileDetails.profile.copyOfID[0].approved) {
      this._toastr.error('Copy of your ID file is not approved by the administrator');
      error = false;
      if (field === 'looking') {
        this.checkLooking = false;
      }
    }
    if (!this.candidateProfileDetails.profile.video && this.candidateProfileDetails.allowVideo === false) {
      this._toastr.error('You need to upload video');
      error = false;
      if (field === 'looking') {
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
        const data = {[field]: value};

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
    this.videoUploadPopup = value;
    this.checkLooking = check;
  }

  /**
   * Status photo popup
   * @param value
   */
  public closePhotoPopup(value) {
    this.photoUploadPopup = value;
    this.webcamImage = null;
    this.showWebcam = value;
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
   * Check criminal value
   */
  public criminalValue() {
    this.candidateProfileDetails.profile.criminal = !this.candidateProfileDetails.profile.criminal;
  }

  /**
   * Check credit value
   */
  public creditValue() {
    this.candidateProfileDetails.profile.credit = !this.candidateProfileDetails.profile.credit;
  }

  /**
   * Check driver license value
   */
  public driverCheck() {
    this.candidateProfileDetails.profile.driverLicense = !this.candidateProfileDetails.profile.driverLicense;
  }

  /**
   * Check employed value
   */
  public employedCheck() {
    this.candidateProfileDetails.profile.employed = !this.candidateProfileDetails.profile.employed;
  }

  /**
   * Check employed value
   */
  public universityExemptionCheck() {
    this.candidateProfileDetails.profile.universityExemption = !this.candidateProfileDetails.profile.universityExemption;
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
   * Convert Base46ToBlob
   * @param b64Data
   * @param contentType
   * @param {number} sliceSize
   * @returns {Blob}
   */
  public b64toBlob(b64Data, contentType, sliceSize = 512) {
      contentType = contentType || '';
      sliceSize = sliceSize || 512;

      const byteCharacters = atob(b64Data);
      const byteArrays = [];

      for (let offset = 0; offset < byteCharacters.length; offset += sliceSize) {
          const slice = byteCharacters.slice(offset, offset + sliceSize);

          const byteNumbers = new Array(slice.length);
          for (let i = 0; i < slice.length; i++) {
              byteNumbers[i] = slice.charCodeAt(i);
          }

          const byteArray = new Uint8Array(byteNumbers);

          byteArrays.push(byteArray);
      }

      return new Blob(byteArrays, {type: contentType});
  }

  /**
   * Upload Photo Proile
   * @param fieldName
   * @returns {Promise<void>}
   */
  public async uploadPhotoProfile(fieldName): Promise<void> {
    const formData = new FormData();
    if (fieldName === 'picture') {
      this.preloaderPicture = true;
    }
    const block = this.webcamImage.imageAsDataUrl.split(';');
    const contentType = block[0].split(':')[1];
    const realData = block[1].split(',')[1];
    const blob = this.b64toBlob(realData, contentType);
    formData.append('' + fieldName + '[]', blob);

    try {
      const data = await this._candidateService.updateProfileFiles(formData);

      if (data.files !== {} && data.files.picture) {
        this.profilePicture = data.files.picture;
      }

      localStorage.setItem('progressBar', data.percentage);
      this._sharedService.progressBar = Number(localStorage.getItem('progressBar'));

      setTimeout(() => {
        this.preloaderPicture = false;
      }, 500);

      this._toastr.success('Photo has been added');
    } catch (err) {
      this._sharedService.showRequestErrors(err);
    }
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
      formData.append('' + fieldName + '[]', this[fieldName].nativeElement.files[i]);
    }

    try {
      const data = await this._candidateService.updateProfileFiles(formData);
      if (data.files !== {} && data.files.copyOfID) {
        this.checkCopyOfID = false;
        this.copyOfIDArray = data.files.copyOfID;
        if (!this.isIE) {
          this.copyOfID.nativeElement.value = '';
        }
      }

      if (data.files !== {} && data.files.cv) {
        this.checkCV = false;
        this.cvArray = data.files.cv;
        if (!this.isIE) {
          this.cv.nativeElement.value = '';
        }
      }

      if (data.files !== {} && data.files.matricCertificate) {
        this.checkMatricCertificate = false;
        this.matricCertificateArray = data.files.matricCertificate;
        if (!this.isIE) {
          this.matricCertificate.nativeElement.value = '';
        }
      }

      if (data.files !== {} && data.files.matricTranscript) {
        this.checkMatricTranscript = false;
        this.matricTranscriptArray = data.files.matricTranscript;
        if (!this.isIE) {
          this.matricTranscript.nativeElement.value = '';
        }
      }

      if (data.files !== {} && data.files.certificateOfQualification) {
        this.checkCertificateOfQualification = false;
        this.certificateOfQualificationArray = data.files.certificateOfQualification;
        if (!this.isIE) {
          this.certificateOfQualification.nativeElement.value = '';
        }
      }

      if (data.files !== {} && data.files.academicTranscript) {
        this.checkAcademicTranscript = false;
        this.academicTranscriptArray = data.files.academicTranscript;
        if (!this.isIE) {
          this.academicTranscript.nativeElement.value = '';
        }
      }

      if (data.files !== {} && data.files.payslip) {
        this.checkPayslip = false;
        this.payslipArray = data.files.payslip;
        if (!this.isIE) {
          this.payslip.nativeElement.value = '';
        }
      }

      if (data.files !== {} && data.files.picture) {
        this.profilePicture = data.files.picture;
      }

      localStorage.setItem('progressBar', data.percentage);
      this._sharedService.progressBar = Number(localStorage.getItem('progressBar'));

      if (data.percentage < 50 || !this.copyOfIDArray || this.copyOfIDArray.length === 0) {
        this._sharedService.visibleErrorProfileIcon = true;
      } else {
        this._sharedService.visibleErrorProfileIcon = false;
      }

      setTimeout(() => {
        this.preloaderPicture = false;
      }, 500);

      this._toastr.success('File has been added');
    } catch (err) {
      this._sharedService.showRequestErrors(err);
    }
  }

  /**
   * Update candidate profile
   * @return {Promise<void>}
   */
  public async updateCandidateProfile(): Promise<void> {
    this.buttonPreloader = true;

    this.candidateProfileDetailsUpdate.user['firstName'] = this.candidateForm.value.firstName;
    this.candidateProfileDetailsUpdate.user['lastName'] = this.candidateForm.value.lastName;
    this.candidateProfileDetailsUpdate.user['phone'] = this.candidateForm.value.phone;
    this.candidateProfileDetailsUpdate.user['email'] = this.candidateForm.value.email;

    this.candidateProfileDetailsUpdate.profile['idNumber'] = this.candidateForm.value.idNumber;
    this.candidateProfileDetailsUpdate.profile['nationality'] = (this.candidateForm.value.nationality === null ) ? null : this.candidateForm.value.nationality;
    this.candidateProfileDetailsUpdate.profile['ethnicity'] = (this.candidateForm.value.ethnicity === null ) ? null : this.candidateForm.value.ethnicity;
    this.candidateProfileDetailsUpdate.profile['beeCheck'] = (this.candidateForm.value.beeCheck === null || this.candidateForm.value.beeCheck === undefined) ? null : (this.candidateForm.value.nationality === 1 || this.candidateForm.value.ethnicity === 'White') ? null : new Date(this.candidateForm.value.beeCheck.date.year + '.'  + this.candidateForm.value.beeCheck.date.month + '.'  + this.candidateForm.value.beeCheck.date.day);
    this.candidateProfileDetailsUpdate.profile['gender'] = (this.candidateForm.value.gender === null) ? null : this.candidateForm.value.gender;
    this.candidateProfileDetailsUpdate.profile['dateOfBirth'] = (this.candidateForm.value.dateOfBirth === null ) ? null : new Date(this.candidateForm.value.dateOfBirth.date.year + '.' + this.candidateForm.value.dateOfBirth.date.month + '.'  + this.candidateForm.value.dateOfBirth.date.day);
    this.candidateProfileDetailsUpdate.profile['criminal'] = this.candidateForm.value.criminal;
    this.candidateProfileDetailsUpdate.profile['universityExemption'] = this.candidateForm.value.universityExemption;
    this.candidateProfileDetailsUpdate.profile['criminalDescription'] = (this.candidateForm.value.criminal === false) ? null : this.candidateForm.value.criminalDescription;
    this.candidateProfileDetailsUpdate.profile['credit'] = this.candidateForm.value.credit;
    this.candidateProfileDetailsUpdate.profile['creditDescription'] = (this.candidateForm.value.credit === false) ? null : this.candidateForm.value.creditDescription;
    this.candidateProfileDetailsUpdate.profile['homeAddress'] = this.candidateForm.value.homeAddress;
    this.candidateProfileDetailsUpdate.profile['driverLicense'] = this.candidateForm.value.driverLicense;
    this.candidateProfileDetailsUpdate.profile['driverNumber'] = this.candidateForm.value.driverNumber;
    this.candidateProfileDetailsUpdate.profile['englishProficiency'] = this.candidateForm.value.englishProficiency;

    if (this.candidateForm.valid) {
      if (this.copyOfIDArray !== null && this.copyOfIDArray.length > 0) {
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
          } else if (this.candidateForm.value.englishProficiency === null) {
            this._toastr.error('English Proficiency is required');
            this.checksEnglishProficiency = true;
            this.buttonPreloader = false;
        } else {
          this._apiService.sendDemoData(this.candidateProfileDetailsUpdate).then(data => {
            localStorage.setItem('progressBar', data.percentage);
            this._sharedService.progressBar = Number(localStorage.getItem('progressBar'));

            this.candidateForm.markAsPristine();
            if (data.percentage < 50
              || !this.copyOfIDArray
              || this.copyOfIDArray.length === 0) {
              this._sharedService.visibleErrorProfileIcon = true;
            } else {
              this._sharedService.visibleErrorProfileIcon = false;
            }

            this._toastr.success('Profile has been updated');
            this._router.navigate(['/candidate/qualification']);
            this.buttonPreloader = false;
          }).catch(err => {
            this._sharedService.showRequestErrors(err);
            this.buttonPreloader = false;
          });
        }
      } else {
        this._toastr.error('Please upload Copy of ID');
        this.buttonPreloader = false;
      }
    } else {
      if(this.candidateForm.value.driverLicense === true && (this.candidateForm.value.driverNumber === null || this.candidateForm.value.driverNumber === '')){
          this._toastr.error('Driver License Number is required');
          this.checkDriverLicenseNumber = true;
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
      await this._candidateService.removeFile(fieldName, url).then(data => {
        if (fieldName === 'copyOfID') {
          this.copyOfIDArray = data[fieldName];
          this.candidateProfileDetails.profile.copyOfID = data[fieldName];
        } else if (fieldName === 'cv') {
          this.cvArray = data[fieldName];
        } else if (fieldName === 'matricCertificate') {
          this.matricCertificateArray = data[fieldName];
        } else if (fieldName === 'matricTranscript') {
          this.matricTranscriptArray = data[fieldName];
        } else if (fieldName === 'certificateOfQualification') {
          this.certificateOfQualificationArray = data[fieldName];
        } else if (fieldName === 'academicTranscript') {
          this.academicTranscriptArray = data[fieldName];
        } else if (fieldName === 'payslip') {
          this.payslipArray = data[fieldName];
        } else if (fieldName === 'picture') {
          this.profilePicture = data[fieldName];
          setTimeout(() => {
            this.preloaderPicture = false;
          }, 500);
        }

        localStorage.setItem('progressBar', data.percentage);
        this.checkLooking = data.looking;
        this._sharedService.progressBar = Number(localStorage.getItem('progressBar'));
        if (data.percentage < 50
          || !this.copyOfIDArray
          || this.copyOfIDArray.length === 0) {
          this._sharedService.visibleErrorProfileIcon = true;
        } else {
          this._sharedService.visibleErrorProfileIcon = false;
        }
      });
      this._toastr.success('File was deleted successfully');
    } catch (err) {
      this._toastr.error(err.error.error);
    }
  }

  /**
   * validates phone number
   * @param otherControlName
   */
  public validatePhoneNumber(otherControlName: string) {

    return function validatePhoneNumber (control: FormControl) {
      const number = control.value;
      if (number.length >= 8) {
        return null;
      } else {
        return { validPhoneNumber: true };
      }
    };
  }

  /**
   * Open modal
   * @param content
   */
  public openVerticallyCentered(content) {
    this.modalActiveClose = this._modalService.open(content, { centered: true });
  }

}
