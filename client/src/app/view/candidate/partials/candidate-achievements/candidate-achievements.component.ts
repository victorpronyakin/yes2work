import { Component, ElementRef, HostListener, OnInit, ViewChild } from '@angular/core';
import { CandidateService } from '../../../../services/candidate.service';
import { FormControl, FormGroup, Validators } from '@angular/forms';
import { SharedService} from '../../../../services/shared.service';
import { ToastrService } from 'ngx-toastr';
import { NgbModal } from '@ng-bootstrap/ng-bootstrap';
import { CandidateReferences } from '../../../../../entities/models';
import { Router } from '@angular/router';
import { AdminCandidateProfile } from '../../../../../entities/models-admin';
import { IMonthCalendarConfigInternal } from 'ng2-date-picker/month-calendar/month-calendar-config';
import { ValidateAvailabilityDate } from '../../../../validators/custom.validator';
import { IMultiSelectOption, IMultiSelectSettings, IMultiSelectTexts } from 'angular-2-dropdown-multiselect';
import { ApiService } from '../../../../services/api.service';

@Component({
  selector: 'app-candidate-achievements',
  templateUrl: './candidate-achievements.component.html',
  styleUrls: ['./candidate-achievements.component.scss']
})
export class CandidateAchievementsComponent implements OnInit {
  @ViewChild('contentReference') private contentReference: ElementRef;
  @ViewChild('contentAchievement') private contentAchievement: ElementRef;
  @ViewChild('contentAll') private contentAll: ElementRef;
  @ViewChild('checkPerms') private checkPerms: ElementRef;

  public candidateProfileDetails: AdminCandidateProfile;

  public progressBar;
  public achievementsForm: FormGroup;
  public achievementsArray = [];
  public achievementsEditForm: FormGroup;
  public achievementsEditId: number;

  public referencesArray = [];
  public referenceObject: CandidateReferences;
  public referencesEditForm: FormGroup;
  public referencesEditStepForm: FormGroup;
  public referencesEditId: number;
  public referenceObjectUpdate: CandidateReferences;

  public modalActiveClose: any;

  public preloaderPage = true;
  public checkVideo;
  public allowVideo;
  public permisionUpdate: boolean;
  public isReference: boolean;
  public isReferenceError:boolean = false;
  public urlRedirect: string;

  public visibilityLooking = false;
  public checkLooking: boolean;
  public videoUploadPopup = false;
  public visibleActivePopup = false;
  public permissonPopup = false;
  public checkRequiredPermission = false;
  public checkFirstJob = false;

  public checkEdit = false;
  public config: IMonthCalendarConfigInternal;
  public selectedDateStart: string = '';
  public selectedDateEnd: string = '';
  public validError = {};
  public optionsModel: string[];

  public candidateForm: FormGroup;
  public myOptions: IMultiSelectOption[];
  public buttonPreloader = false;
  public checksAvailabilityPeriod = false;
  public checksAvailabilityDate = false;
  public availabilityPeriodStatus = false;
  public salaryAgain = false;
  @ViewChild('salary') private salary: ElementRef;
  @ViewChild('dataAvailable') private dataAvailable: ElementRef;

  public articlesFirmSettings: IMultiSelectSettings = {
    displayAllSelectedText: true,
    selectionLimit: 0,
    showCheckAll: true,
    showUncheckAll: true,
  };
  public articlesFirmTextConfig: IMultiSelectTexts = {
    checkAll: 'Select all',
    uncheckAll: 'Deselect all',
    checked: 'item selected',
    checkedPlural: 'items selected',
    defaultTitle: 'I would work in these cities...',
    allSelected: 'All selected',
  };
  public articlesFirmSettingsCities: IMultiSelectSettings = {
    displayAllSelectedText: true,
    selectionLimit: 0,
    showCheckAll: true,
    showUncheckAll: true,
    checkedStyle: 'fontawesome'
  };

  constructor(
    private readonly _candidateService: CandidateService,
    public readonly _sharedService: SharedService,
    private readonly _toastr: ToastrService,
    private readonly _modalService: NgbModal,
    private readonly _apiService: ApiService,
    private readonly _router: Router
  ) {
    this._sharedService.checkSidebar = false;
  }

  ngOnInit() {
    window.scrollTo(0, 0);
    this.progressBar = localStorage.getItem('progressBar');
    this.myOptions = this._sharedService.citiesWorking;

    this.achievementsForm = new FormGroup({
      description: new FormControl('', [
        Validators.required,
        Validators.maxLength(50)
      ])
    });

    this.candidateForm = new FormGroup({
      mostRole: new FormControl('', Validators.required),
      mostEmployer: new FormControl('', Validators.required),
      specialization: new FormControl(''),
      mostSalary: new FormControl(null),
      salaryPeriod: new FormControl(null, Validators.required),
      availability: new FormControl(''),
      availabilityPeriod: new FormControl(null),
      dateAvailability: new FormControl('', ValidateAvailabilityDate),
      citiesWorking: new FormControl('', Validators.required)
    });

    this.getCandidateAchievement().then(response => {
      this.getCandidateReferences().then(() => {
        this.getCandidateProfile();

        this.getCandidateProfileDetails();
      });
    });
  }

  @HostListener('window:beforeunload')
  onBeforeUnload() {
    if (this.achievementsForm.dirty === true &&
        this.achievementsForm.touched === true) {
      const confirmTest = "Are you sure you want to leave now?";
      window.event.returnValue = false;
      return confirmTest;
    } else if (this.achievementsForm.dirty === true && this.achievementsForm.touched === true) {
      const confirmTest = "Are you sure you want to leave now?";
      window.event.returnValue = false;
      return confirmTest;
    }
  }

  canDeactivate(url) {
    this.urlRedirect = url;
    if (this.achievementsForm.dirty === true &&
        this.achievementsForm.touched === true) {
      this.openVerticallyCenter(this.contentAll);
    } else if (this.achievementsForm.dirty === true && this.achievementsForm.touched === true) {
      this.openVerticallyCenter(this.contentAchievement);
    } else {
      return true;
    }
  }

  /**
   * Exit add job page
   */
  exitPage(){
    this.modalActiveClose.dismiss();
    this.achievementsForm.markAsPristine();
    this._router.navigate([this.urlRedirect]);
  }

  public choicePermisionCheck(permision, value) {
    this[permision] = value;
    if (permision === 'isReference') {
      this.isReferenceError = false;
    }
  }

  /**
   * Select change router
   * @param url
   */
  public routerApplicants(url): void {
    this._router.navigate([url]);
  }

  /**
   * Get candidate profile
   * @returns {Promise<void>}
   */
  public async getCandidateProfile(): Promise<any> {
    try {
      const data = await this._candidateService.getCandidateProfileDetails();
      this.checkVideo = data.profile.video;
      this.progressBar = data.profile.percentage;
      localStorage.setItem('progressBar', String(data.profile.percentage));
      this.allowVideo = data['allowVideo'];
      this.candidateProfileDetails = data;
      if(this.candidateProfileDetails.profile.percentage < 50 || !this.candidateProfileDetails.profile.copyOfID ||
        !this.candidateProfileDetails.profile.copyOfID[0] ||
        !this.candidateProfileDetails.profile.copyOfID[0].approved ||
        (this.candidateProfileDetails.allowVideo === false && !this.candidateProfileDetails.profile.video) ||
        (this.candidateProfileDetails.allowVideo === false && this.candidateProfileDetails.profile.video && this.candidateProfileDetails.profile.video.approved === false)) {
        // this.checkVisible = false;
        this.checkLooking = false;
        this.visibilityLooking = true;
        // this.visibilityVisible = true;
      } else {
        // this.checkVisible = this.candidateProfileDetails.profile.visible;
        this.checkLooking = this.candidateProfileDetails.profile.looking;
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
    this.videoUploadPopup = value;
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
    this._router.navigate(['/candidate/video']);
  }

  /**
   * Get candidate achievements
   * @return {Promise<void>}
   */
  public async getCandidateAchievement(): Promise<void> {
    this.achievementsArray = await this._candidateService.getCandidateAchievement();
  }

  /**
   * Create candidate achievements
   * @return {Promise<void>}
   */
  public async createCandidateAchievement(): Promise<void> {
    this._candidateService.createCandidateAchievement(this.achievementsForm.value.description).then(data => {
      this.achievementsArray.unshift(data.achievement);
      this._toastr.success('Achievement has been created');

      this.progressBar = data.percentage;
      this._sharedService.progressBar = data.percentage;
      localStorage.setItem('progressBar', String(data.percentage));
      this.achievementsForm = new FormGroup({
        description: new FormControl('', [
          Validators.required,
          Validators.maxLength(50)
        ])
      });
    }).catch(err => {
      this._sharedService.showRequestErrors(err);
    })
  }

  /**
   * Delete candidate achievements
   * @param id {number}
   * @return {Promise<void>}
   */
  public async deleteCandidateAchievement(id: number): Promise<void> {
    const data = await this._candidateService.deleteCandidateAchievement(id);
    this.progressBar = data.percentage;
    this._sharedService.progressBar = data.percentage;
    localStorage.setItem('progressBar', String(data.percentage));
    this._toastr.success('Achievement has been deleted');

    for (let i = 0; i < this.achievementsArray.length; i++) {
      if(this.achievementsArray[i].id === id ) {
        this.achievementsArray.splice(i, 1);
      }
    }
  }

  /**
   * Get achievement edit
   * @param id {number}
   * @param description {string}
   * @return {Promise<void>}
   */
  public async getAchievementEdit(id: number, description: string): Promise<void> {
    this.achievementsEditForm = new FormGroup({
      description: new FormControl(description, [
        Validators.required,
        Validators.maxLength(50)
      ])
    });
    this.achievementsEditId = id;
  }

  /**
   * Update candidate achievement
   * @return {Promise<void>}
   */
  public async updateCandidateAchievement(): Promise<void> {
    try {
      const data = await this._candidateService.updateCandidateAchievement(this.achievementsEditId, this.achievementsEditForm.value.description);

      const getUpdateAchievements = this.achievementsArray.find(user => user.id === this.achievementsEditId);
      getUpdateAchievements.description = this.achievementsEditForm.value.description;

      this.progressBar = data.percentage;
      localStorage.setItem('progressBar', String(data.percentage));
      this._sharedService.progressBar = data.percentage;

      this.closeActiveModal();

      this._toastr.success('Achievement has been updated');
    }
    catch (err) {
      this._sharedService.showRequestErrors(err);
    }
  }

  /**
   * Permission value check
   */
  public permisionValue(field, value){
    value = !value;
  }

  /**
   * Get candidate references
   * @return {Promise<void>}
   */
  public async getCandidateReferences(): Promise<void> {
    try {
      this.referencesArray = await this._candidateService.getCandidateReferences();

      // this.preloaderPage = false;
    }
    catch (err) {
      this._sharedService.showRequestErrors(err);
    }
  }

  /**
   * Events permission popup
   * @param value {boolean}
   * @param toggleValue {boolean}
   */
  public eventPermissionPopup(value, toggleValue) {
    this.permissonPopup = value;
    if (toggleValue === true) {
      this.referenceObject.permission = undefined;
      this.permisionUpdate = undefined;
    }
  }

  /**
   * Delete candidate references
   * @param id {number}
   * @return {Promise<void>}
   */
  public async deleteCandidateReferences(id: number): Promise<void> {
    const data = await this._candidateService.deleteCandidateReferences(id);
    this.progressBar = data.percentage;
    this._sharedService.progressBar = data.percentage;
    localStorage.setItem('progressBar', String(data.percentage));
    this._toastr.success('Reference has been deleted');

    for (let i = 0; i < this.referencesArray.length; i++) {
      if(this.referencesArray[i].id === id ) {
        this.referencesArray.splice(i, 1);
      }
    }
  }

  /**
   * Get references edit
   * @param id {number}
   * @param data {Object}
   * @param permission {string}
   * @return {Promise<void>}
   */
  public async getReferencesEdit(id: number, data, permission: boolean): Promise<void> {
    this.referencesEditForm = new FormGroup({
      company: new FormControl(data.company, Validators.required),
      role: new FormControl(data.role, Validators.required),
      specialization: new FormControl(data.specialization, Validators.required),
      startDate: new FormControl(data.startDate, Validators.required),
      endDate: new FormControl(data.endDate, Validators.required),
      isReference: new FormControl(data.isReference),
    });

    this.referencesEditStepForm = new FormGroup({
      managerFirstName: new FormControl(data.managerFirstName, Validators.required),
      managerLastName: new FormControl(data.managerLastName, Validators.required ),
      managerTitle: new FormControl(data.managerTitle, Validators.required),
      managerEmail: new FormControl(data.managerEmail, [Validators.required, Validators.email]),
      managerComment: new FormControl(data.managerComment),
      permission: new FormControl(data.permission)
    });

    this.referencesEditId = id;
    this.permisionUpdate = permission;
    this.isReference = data.isReference;
  }

  /**
   * Second find to add reference
   */
  public subCreateReference() {
    this.referenceObject = {
      company: this.referencesEditForm.value.company,
      role: this.referencesEditForm.value.role,
      specialization: this.referencesEditForm.value.specialization,
      startDate: this.referencesEditForm.value.startDate,
      endDate: this.referencesEditForm.value.endDate,
      isReference: this.isReference,
      managerFirstName: this.referencesEditStepForm.value.managerFirstName,
      managerLastName: this.referencesEditStepForm.value.managerLastName,
      managerTitle: this.referencesEditStepForm.value.managerTitle,
      managerEmail: this.referencesEditStepForm.value.managerEmail,
      managerComment: this.referencesEditStepForm.value.managerComment,
      permission: this.permisionUpdate
    };
    if (this.isReference === false) {
      this.referenceObject.managerFirstName = null;
      this.referenceObject.managerLastName = null;
      this.referenceObject.managerTitle = null;
      this.referenceObject.managerEmail = null;
      this.referenceObject.managerComment = null;
      this.referenceObject.permission = null;
    }
    this._candidateService.createCandidateReferences(this.referenceObject).then(data => {
      this.referencesArray.unshift(data.reference);

      this.progressBar = data.percentage;
      this._sharedService.progressBar = data.percentage;
      localStorage.setItem('progressBar', String(data.percentage));

      this.closeActiveModal();

      this._toastr.success('Work Experience has been created');

      this.checkRequiredPermission = false;
    }).catch(err => {
        this._sharedService.showRequestErrors(err);
    });
  }

  /**
   * Create candidate references
   */
  public createCandidateReferences() {
    this.referenceObject = {
      company: this.referencesEditForm.value.company,
      role: this.referencesEditForm.value.role,
      specialization: this.referencesEditForm.value.specialization,
      startDate: this.referencesEditForm.value.startDate,
      endDate: this.referencesEditForm.value.endDate,
      isReference: this.isReference,
      managerFirstName: this.referencesEditStepForm.value.managerFirstName,
      managerLastName: this.referencesEditStepForm.value.managerLastName,
      managerTitle: this.referencesEditStepForm.value.managerTitle,
      managerEmail: this.referencesEditStepForm.value.managerEmail,
      managerComment: this.referencesEditStepForm.value.managerComment,
      permission: this.permisionUpdate
    };

    if (this.referencesEditForm.valid) {
      if (this.isReference === undefined) {
        this._toastr.error('Reference field needs to be completed');
      } else if (this.isReference === false) {
        this.referenceObject.managerFirstName = null;
        this.referenceObject.managerLastName = null;
        this.referenceObject.managerTitle = null;
        this.referenceObject.managerEmail = null;
        this.referenceObject.managerComment = null;
        this.referenceObject.permission = null;
        this._candidateService.createCandidateReferences(this.referenceObject).then(data => {
          this.referencesArray.unshift(data.reference);

          this.progressBar = data.percentage;
          this._sharedService.progressBar = data.percentage;
          localStorage.setItem('progressBar', String(data.percentage));

          this.closeActiveModal();

          this._toastr.success('Work Experience has been created');

          this.checkRequiredPermission = false;
        }).catch(err => {
          this._sharedService.showRequestErrors(err);
        });
      } else {
        if (this.referencesEditStepForm.valid) {
          if (this.permisionUpdate === undefined) {
            this._toastr.error('Permission field needs to be completed');
            this.checkRequiredPermission = true;
          } else if (this.permisionUpdate === false) {
            this.permissonPopup = true;
          } else {
            this._candidateService.createCandidateReferences(this.referenceObject).then(data => {
              this.referencesArray.unshift(data.reference);

              this.progressBar = data.percentage;
              this._sharedService.progressBar = data.percentage;
              localStorage.setItem('progressBar', String(data.percentage));

              this.closeActiveModal();

              this._toastr.success('Work Experience has been created');

              this.checkRequiredPermission = false;
            }).catch(err => {
                this._sharedService.showRequestErrors(err);
            });
          }
        } else {
          if (this.permisionUpdate === undefined) {
            this._toastr.error('Permission field needs to be completed');
            this.checkRequiredPermission = true;
          }
          this._sharedService.validateAllFormFields(this.referencesEditStepForm);
        }
      }
    } else {
      if (this.isReference === undefined) {
          this._toastr.error('Reference field needs to be completed');
      }
      this._sharedService.validateAllFormFields(this.referencesEditForm);
      if (this.isReference === true) {
        if (this.permisionUpdate === undefined) {
            this._toastr.error('Permission field needs to be completed');
            this.checkRequiredPermission = true;
        }
        this._sharedService.validateAllFormFields(this.referencesEditStepForm);
      }
    }
  }

  /**
   * Update candidate references
   * @return {Promise<void>}
   */
  public async updateCandidateReferences(): Promise<void> {

    this.referenceObjectUpdate = {
      company: this.referencesEditForm.value.company,
      role: this.referencesEditForm.value.role,
      specialization: this.referencesEditForm.value.specialization,
      startDate: this.referencesEditForm.value.startDate,
      endDate: this.referencesEditForm.value.endDate,
      isReference: this.isReference,
      managerFirstName: this.referencesEditStepForm.value.managerFirstName,
      managerLastName: this.referencesEditStepForm.value.managerLastName,
      managerTitle: this.referencesEditStepForm.value.managerTitle,
      managerEmail: this.referencesEditStepForm.value.managerEmail,
      managerComment: this.referencesEditStepForm.value.managerComment,
      permission: this.permisionUpdate
    };

    if (this.referencesEditForm.valid) {
      if (this.isReference === undefined || this.isReference === null) {
        this._toastr.error('Reference field needs to be completed');
      } else if (this.isReference === false) {
        try {
          this.referenceObjectUpdate.managerFirstName = null;
          this.referenceObjectUpdate.managerLastName = null;
          this.referenceObjectUpdate.managerTitle = null;
          this.referenceObjectUpdate.managerEmail = null;
          this.referenceObjectUpdate.managerComment = null;
          this.referenceObjectUpdate.permission = null;

          const data = await this._candidateService.updateCandidateReferences(this.referencesEditId, this.referenceObjectUpdate);

          const getUpdateReferences = this.referencesArray.find(user => user.id === this.referencesEditId);
          getUpdateReferences.company = this.referencesEditForm.value.company;
          getUpdateReferences.role = this.referencesEditForm.value.role;
          getUpdateReferences.specialization = this.referencesEditForm.value.specialization;
          getUpdateReferences.startDate = this.referencesEditForm.value.startDate;
          getUpdateReferences.endDate = this.referencesEditForm.value.endDate;
          getUpdateReferences.isReference = this.isReference;
          getUpdateReferences.managerFirstName = null;
          getUpdateReferences.managerLastName = null;
          getUpdateReferences.managerTitle = null;
          getUpdateReferences.managerEmail = null;
          getUpdateReferences.managerComment = null;
          getUpdateReferences.permission = null;

          this.progressBar = data.percentage;
          this._sharedService.progressBar = data.percentage;
          localStorage.setItem('progressBar', String(data.percentage));

          this.closeActiveModal();

          this._toastr.success('Work Experience has been updated');
        } catch (err) {
            this._sharedService.showRequestErrors(err);
        }
      } else {
        if (this.referencesEditStepForm.valid) {
          if (this.permisionUpdate === undefined || this.permisionUpdate === null) {
              this._toastr.error('Permission field needs to be completed');
          } else {
            try {
              const data = await this._candidateService.updateCandidateReferences(this.referencesEditId, this.referenceObjectUpdate);

              const getUpdateReferences = this.referencesArray.find(user => user.id === this.referencesEditId);
              getUpdateReferences.company = this.referencesEditForm.value.company;
              getUpdateReferences.role = this.referencesEditForm.value.role;
              getUpdateReferences.specialization = this.referencesEditForm.value.specialization;
              getUpdateReferences.startDate = this.referencesEditForm.value.startDate;
              getUpdateReferences.endDate = this.referencesEditForm.value.endDate;
              getUpdateReferences.isReference = this.isReference;
              getUpdateReferences.managerFirstName = this.referencesEditStepForm.value.managerFirstName;
              getUpdateReferences.managerLastName = this.referencesEditStepForm.value.managerLastName;
              getUpdateReferences.managerTitle = this.referencesEditStepForm.value.managerTitle;
              getUpdateReferences.managerEmail = this.referencesEditStepForm.value.managerEmail;
              getUpdateReferences.managerComment = this.referencesEditStepForm.value.managerComment;
              getUpdateReferences.permission = this.permisionUpdate;

              this.progressBar = data.percentage;
              this._sharedService.progressBar = data.percentage;
              localStorage.setItem('progressBar', String(data.percentage));

              this.closeActiveModal();

              this._toastr.success('Work Experience has been updated');
            } catch (err) {
                this._sharedService.showRequestErrors(err);
            }
          }
        } else {
            if (this.permisionUpdate === undefined || this.permisionUpdate === null) {
                this._toastr.error('Permission field needs to be completed');
                this.checkRequiredPermission = true;
            }
            this._sharedService.validateAllFormFields(this.referencesEditStepForm);
        }
      }
    } else {
        this._sharedService.validateAllFormFields(this.referencesEditForm);
    }

  }

  /**
   * Close modal
   */
  closeActiveModal() {
    this.modalActiveClose.dismiss();
  }

  /**
   * Managed modal
   * @param content {any} - content to be shown in popup
   * @param id {number} - job id to be used for fetching data and showing in popup
   */
  public openVerticallyCentered(content: any,  id: number) {
    this.modalActiveClose = this._modalService.open(content, { centered: true, 'size': 'lg' });
    this.modalActiveClose.result.then(
      (data) => {
        this._sharedService.resetForm(this.achievementsForm);
      },
      (res) => {
        this._sharedService.resetForm(this.achievementsForm);
      });
  }

  /**
   *
   * @param content
   * @param data
   */
  public openVerticallyCenteredReferences(content: any,  data: any) {
    if (data) {
      if (data.startDate !== null) {
          const dateStart = new Date(data.startDate);
          this.selectedDateStart = this.transformationDate((dateStart.getMonth() + 1), dateStart.getFullYear());
      }
      if (data.endDate !== null) {
        const dateEnd = new Date(data.endDate);
        this.selectedDateEnd = this.transformationDate((dateEnd.getMonth() + 1), dateEnd.getFullYear());
      }

      this.referencesEditId = data.id;
      this.permisionUpdate = data.permission;
      this.isReference = data.isReference;
      this.checkEdit = true;

      this.referencesEditForm = new FormGroup({
        company: new FormControl(data.company, Validators.required),
        role: new FormControl(data.role, Validators.required),
        specialization: new FormControl(data.specialization, Validators.required),
        startDate: new FormControl(data.startDate, Validators.required),
        endDate: new FormControl(data.endDate, Validators.required),
        isReference: new FormControl(data.isReference)
      });

      this.referencesEditStepForm = new FormGroup({
        managerFirstName: new FormControl(data.managerFirstName, Validators.required),
        managerLastName: new FormControl(data.managerLastName, Validators.required ),
        managerTitle: new FormControl(data.managerTitle, Validators.required),
        managerEmail: new FormControl(data.managerEmail, [Validators.required, Validators.email]),
        managerComment: new FormControl(data.managerComment),
        permission: new FormControl(data.permission)
      });

    } else {
      this.selectedDateStart = '';
      this.selectedDateEnd = '';
      this.permisionUpdate = undefined;
      this.isReference = undefined;
      this.checkEdit = false;
      this.referencesEditForm = new FormGroup({
        company: new FormControl('', Validators.required),
        role: new FormControl('', Validators.required),
        specialization: new FormControl(null, Validators.required),
        startDate: new FormControl(null, Validators.required),
        endDate: new FormControl(null, Validators.required),
        isReference: new FormControl(null)
      });

      this.referencesEditStepForm = new FormGroup({
        managerFirstName: new FormControl('', Validators.required),
        managerLastName: new FormControl('', Validators.required ),
        managerTitle: new FormControl('', Validators.required),
        managerEmail: new FormControl('', [Validators.required, Validators.email]),
        managerComment: new FormControl(''),
        permission: new FormControl(null)
      });
    }
    this.modalActiveClose = this._modalService.open(content, { centered: true, 'size': 'lg', backdrop: 'static' });
  }

  /**
   * Managed modal
   * @param content {any} - content to be shown in popup
   */
  public openVerticallyCenter(content: any) {
    this.modalActiveClose = this._modalService.open(content, { centered: true});
  }

  /**
   * Check end year
   */
  public endYearValidate() {
    if (this.referencesEditForm.controls.startDate.value && this.referencesEditForm.controls.endDate.value) {
      const start = new Date(this.referencesEditForm.controls.startDate.value);
      const end = new Date(this.referencesEditForm.controls.endDate.value);
      if (start.getFullYear() > end.getFullYear()) {
          this.validError['endDateCheck'] = true;
      } else if (start.getFullYear() === end.getFullYear()) {
          if (start.getMonth() >= end.getMonth()) {
              this.validError['endDateCheck'] = true;
          } else {
              this.validError['endDateCheck'] = false;
          }
      } else {
          this.validError['endDateCheck'] = false;
      }
    }
  }

  /**
   * @param month
   * @param year
   * @returns {string}
   */
  public transformationDate(month, year) {
    let returnMonth;
    switch (month) {
      case 1:
          returnMonth = 'Jan';
          break;
      case 2:
          returnMonth = 'Feb';
          break;
      case 3:
          returnMonth = 'Mar';
          break;
      case 4:
          returnMonth = 'Apr';
          break;
      case 5:
          returnMonth = 'May';
          break;
      case 6:
          returnMonth = 'Jun';
          break;
      case 7:
          returnMonth = 'Jul';
          break;
      case 8:
          returnMonth = 'Aug';
          break;
      case 9:
          returnMonth = 'Sep';
          break;
      case 10:
          returnMonth = 'Oct';
          break;
      case 11:
          returnMonth = 'Nov';
          break;
      case 12:
          returnMonth = 'Dec';
          break;
    }
    return (returnMonth + ', ' + year);
  }

  /**
   * Get details profile candidate
   * @return {Promise<void>}
   */
  public async getCandidateProfileDetails(): Promise<void>{
    this.candidateProfileDetails = await this._candidateService.getCandidateProfileDetails();

    this.candidateForm.setValue({
      mostRole: this.candidateProfileDetails.profile.mostRole,
      mostEmployer: this.candidateProfileDetails.profile.mostEmployer,
      specialization: this.candidateProfileDetails.profile.specialization,
      mostSalary: this.candidateProfileDetails.profile.mostSalary,
      salaryPeriod: this.candidateProfileDetails.profile.salaryPeriod,
      availability: (this.candidateProfileDetails.profile.availability === null) ? true : this.candidateProfileDetails.profile.availability,
      availabilityPeriod: this.candidateProfileDetails.profile.availabilityPeriod,
      dateAvailability: this.candidateProfileDetails.profile.dateAvailability,
      citiesWorking: this.candidateProfileDetails.profile.citiesWorking
    });

    if (this.candidateProfileDetails.profile.firstJob === null) {
      this.checkFirstJob = true;
    } else {
      this.checkFirstJob = this.candidateProfileDetails.profile.firstJob;
    }

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

    this._sharedService.progressBar = Number(this.candidateProfileDetails.profile.percentage);

    this.parseSalary();

    this.preloaderPage = false;
  }

  /**
   * Parse salary
   */
  public parseSalary() {
    if ( this.candidateForm.value.mostSalary !== null && this.candidateForm.value.mostSalary !== '') {
      let salary = this.candidateForm.value.mostSalary;
      if ( !Number.isInteger(salary) ) {
        salary = salary.replace(/[\D\s\._\-]+/g, '');
        salary = salary ? parseInt( salary, 10 ) : 0;
      }
      this.candidateForm.patchValue({mostSalary: ( salary === 0 ) ? '0' : salary.toLocaleString( 'en-US' )});
    }
  }

  /**
   * Check salary number
   */
  public checkSalary() {
    if (this.candidateForm.value.salaryPeriod !== 'monthly') {
      let salary = this.candidateForm.value.mostSalary;
      salary = salary.replace(/[\D\s\._\-]+/g, '');
      salary = salary ? parseInt( salary, 10 ) : 0;
      if (salary && salary < 20000 && salary !== 0 && !this.salaryAgain) {
        this.openVerticallyCenterd(this.salary);
        this.candidateForm.patchValue({mostSalary: null});
        this.salaryAgain = true;
      } else {
        this.salaryAgain = false;
      }
    }
  }

  /**
   * Open modal
   * @param content
   */
  public openVerticallyCenterd(content) {
    this.modalActiveClose = this._modalService.open(content, { centered: true });
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
      this.openVerticallyCenterd(this.dataAvailable);
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
   * Change check first job
   */
  public changeCheckFirstJob(){
    this.checkFirstJob = !this.checkFirstJob;
  }

  /**
   * Update candidate profile
   * @return {Promise<void>}
   */
  public async updateCandidateProfile(): Promise<void> {
    this.buttonPreloader = true;

    this.candidateProfileDetails.profile['mostRole'] = this.candidateForm.value.mostRole;
    this.candidateProfileDetails.profile['mostEmployer'] = this.candidateForm.value.mostEmployer;
    this.candidateProfileDetails.profile['specialization'] = this.candidateForm.value.specialization;
    let salary = this.candidateForm.value.mostSalary;
    if (salary === null || salary === '') {
      salary = null;
    } else {
      if ( !Number.isInteger(salary) ) {
        salary = salary.replace(/[\D\s\._\-]+/g, '');
        salary = salary ? parseInt( salary, 10 ) : 0;
      }
    }
    this.candidateProfileDetails.profile['mostSalary'] = salary;
    this.candidateProfileDetails.profile['salaryPeriod'] = this.candidateForm.value.salaryPeriod;
    this.candidateProfileDetails.profile['firstJob'] = this.checkFirstJob;
    this.candidateProfileDetails.profile['availability'] = this.candidateForm.value.availability;
    this.candidateProfileDetails.profile['availabilityPeriod'] = this.candidateForm.value.availabilityPeriod;
    const availabilityDate = this.candidateForm.value.dateAvailability;
    this.candidateProfileDetails.profile['dateAvailability'] = (this.candidateForm.value.dateAvailability === null ) ? null : (this.candidateForm.value.availability === true ) ? null : availabilityDate.formatted;
    this.candidateProfileDetails.profile['citiesWorking'] = (this.candidateForm.value.citiesWorking === undefined) ? null : this.candidateForm.value.citiesWorking;

    if (this.checkFirstJob === false) {

      if (this.candidateForm.valid) {
        if(this.candidateProfileDetails.profile.availability === false && !this.candidateForm.value.availabilityPeriod){
          this._toastr.error('Availability Period is required');
          this.checksAvailabilityPeriod = true;
          this.buttonPreloader = false;
        }
        else if(this.candidateForm.value.availabilityPeriod === 4 && this.candidateForm.value.dateAvailability === null) {
          this._toastr.error('Earliest date of availability is required');
          this.checksAvailabilityDate = true;
          this.buttonPreloader = false;
        }
        else{
          this._apiService.sendDemoData(this.candidateProfileDetails).then(data => {
            localStorage.setItem('progressBar', data.percentage);
            this._sharedService.progressBar = Number(localStorage.getItem('progressBar'));

            this.candidateForm.markAsPristine();
            if (data.percentage < 50 ||
              !this.candidateProfileDetails.profile.copyOfID ||
              this.candidateProfileDetails.profile.copyOfID.length === 0) {
              this._sharedService.visibleErrorProfileIcon = true;
            } else {
              this._sharedService.visibleErrorProfileIcon = false;
            }

            this._toastr.success('Profile has been updated');
            this.buttonPreloader = false;
          }).catch(err => {
            this._sharedService.showRequestErrors(err);
            this.buttonPreloader = false;
          })
        }
      } else {
        if(this.candidateProfileDetails.profile.availability === false && !this.candidateForm.value.availabilityPeriod){
          this._toastr.error('Availability Period is required');
          this.checksAvailabilityPeriod = true;
        }
        if(this.candidateForm.value.availabilityPeriod === 4 && this.candidateForm.value.dateAvailability === null) {
          this._toastr.error('Earliest date of availability is required');
          this.checksAvailabilityDate = true;
          this.buttonPreloader = false;
        }

        this._sharedService.validateAlertCandidateForm(this.candidateForm);
        this._sharedService.validateAllFormFields(this.candidateForm);
        this.buttonPreloader = false;
      }
    } else {
      if (this.candidateForm.value.citiesWorking === undefined ||
        this.candidateForm.value.citiesWorking === null ||
        this.candidateForm.value.citiesWorking.length === 0) {
        this._toastr.error('Cities you would consider working in is required');
        this._sharedService.validateAllFormFields(this.candidateForm);
        this.buttonPreloader = false;
      } else {
        this.candidateProfileDetails.profile['mostRole'] = 'Workforce Entrant';
        this.candidateProfileDetails.profile['mostEmployer'] = null;
        this.candidateProfileDetails.profile['specialization'] = null;
        this.candidateProfileDetails.profile['mostSalary'] = 0;
        this.candidateProfileDetails.profile['salaryPeriod'] = null;
        this.candidateProfileDetails.profile['availability'] = true;
        this.candidateProfileDetails.profile['availabilityPeriod'] = null;
        this.candidateProfileDetails.profile['dateAvailability'] = null;

        this._apiService.sendDemoData(this.candidateProfileDetails).then(data => {
          localStorage.setItem('progressBar', data.percentage);
          this._sharedService.progressBar = Number(localStorage.getItem('progressBar'));
          this.candidateForm.patchValue({
            mostRole: this.candidateProfileDetails.profile.mostRole,
            mostEmployer: this.candidateProfileDetails.profile.mostEmployer,
            specialization: this.candidateProfileDetails.profile.specialization,
            mostSalary: this.candidateProfileDetails.profile.mostSalary,
            salaryPeriod: this.candidateProfileDetails.profile.salaryPeriod,
            availability: (this.candidateProfileDetails.profile.availability === null) ? true : this.candidateProfileDetails.profile.availability,
            availabilityPeriod: this.candidateProfileDetails.profile.availabilityPeriod,
            dateAvailability: this.candidateProfileDetails.profile.dateAvailability.toDateString()
          });
          this.candidateForm.markAsPristine();
          if (data.percentage < 50 ||
            !this.candidateProfileDetails.profile.copyOfID ||
            this.candidateProfileDetails.profile.copyOfID.length === 0) {
            this._sharedService.visibleErrorProfileIcon = true;
          } else {
            this._sharedService.visibleErrorProfileIcon = false;
          }

          this._toastr.success('Profile has been updated');
          this.buttonPreloader = false;
        }).catch(err => {
          this._sharedService.showRequestErrors(err);
          this.buttonPreloader = false;
        });
      }
    }
  }

}
