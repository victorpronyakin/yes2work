import { Component, ElementRef, OnInit, ViewChild} from '@angular/core';
import { FormControl, FormGroup, } from '@angular/forms';
import { AdminCandidateProfile } from '../../../../../entities/models-admin';
import { Router } from '@angular/router';
import { NgbModal } from '@ng-bootstrap/ng-bootstrap';
import { ToastrService } from 'ngx-toastr';
import { SharedService } from '../../../../services/shared.service';
import { CandidateService } from '../../../../services/candidate.service';
import { INgxMyDpOptions } from 'ngx-mydatepicker';
import { IMonthCalendarConfigInternal } from 'ng2-date-picker/month-calendar/month-calendar-config';
import {years} from "../../../../constants/year.const";
import {ApiService} from "../../../../services/api.service";

@Component({
  selector: 'app-candidate-qualification',
  templateUrl: './candidate-qualification.component.html',
  styleUrls: ['./candidate-qualification.component.scss']
})
export class CandidateQualificationComponent implements OnInit {
  @ViewChild('contentAchievement') private contentAchievement: ElementRef;
  @ViewChild('contentAll') private contentAll: ElementRef;
  @ViewChild('yearPickers') public yearPickers: ElementRef;

  public candidateProfileDetails: AdminCandidateProfile;

  public progressBar;
  public qualificationForm: FormGroup;
  public qualificationArray = [];
  public achievementsEditId: number;

  public modalActiveClose: any;

  public preloaderPage = true;
  public checkVideo;
  public allowVideo;
  public urlRedirect: string;

  public visibilityLooking = false;
  public checkLooking: boolean;
  public videoUploadPopup = false;
  public myOptionsDate: INgxMyDpOptions = {
    yearSelector: true,
    monthSelector: false,
  };
  // public model: any = { date: { year: 2018, month: 10, day: 9 } };
  public config: IMonthCalendarConfigInternal;
  public selectedDateStart: string = '';
  public selectedDateEnd: string = '';
  public completeSubjectObj = {
    eighty: null,
    seventy: null,
    sixty: null,
    fifty: null,
    forty: null,
    thirty: null,
    twenty: null
  };
  public checkEdit = false;
  public idQualification: number;
  public validError = {};
  public yearsArray = years;
  public showYearPicker = false;
  public checkShowPicker = false;

  public specializationField = [];

  public showUniversityExemption = false;

  constructor(
    private readonly _apiService: ApiService,
    private readonly _candidateService: CandidateService,
    public readonly _sharedService: SharedService,
    private readonly _toastr: ToastrService,
    private readonly _modalService: NgbModal,
    private readonly _router: Router
  ) {
    this._sharedService.checkSidebar = false;
  }

  ngOnInit() {
    window.scrollTo(0, 0);
    this.progressBar = localStorage.getItem('progressBar');

    this.qualificationForm = new FormGroup({
      type: new FormControl(null),
      schoolName: new FormControl(null),
      matriculatedYear: new FormControl(null),
      tertiaryInstitution: new FormControl(null),
      tertiaryInstitutionCustom: new FormControl(null),
      levelQ: new FormControl(null),
      specificQ: new FormControl(null),
      specificQCustom: new FormControl(null),
      specialization: new FormControl(null),
      specializationCustom: new FormControl(null),
      education: new FormControl(null),
      startYear: new FormControl(null),
      endYear: new FormControl(null)
    });

    this.getCandidateQualification().then(() => {
      this.getCandidateProfile();
    });
  }

  /**
   * Is Show University Exemption
   * @returns {boolean}
   */
  public isShowUniversityExemption() {
    let show = false;
    let elseType = true;
    this.qualificationArray.forEach((qualification) => {
      if (qualification.type === 1) {
        show = true;
      } else {
        elseType = false;
      }
    });

    return (show && elseType) ? true : false;
  }

  /**
   * Change University Exemption
   */
  public changeUniversityExemption() {
    this.candidateProfileDetails.profile.universityExemption = !this.candidateProfileDetails.profile.universityExemption;
    this._apiService.sendDemoData(this.candidateProfileDetails).then(data => {
        localStorage.setItem('progressBar', data.percentage);
        this._sharedService.progressBar = Number(localStorage.getItem('progressBar'));
    }).catch(err => {
        this._sharedService.showRequestErrors(err);
    });
  }

  /**
   * Toggle year picker
   * @param value
   */
  public togglePicker(value){
    this.showYearPicker = !this.showYearPicker;
    if (this.showYearPicker) {
      this.checkShowPicker = true;
      setTimeout(() => {
        if (value && value > 1927 && value < 2018) {
          const test = document.getElementById('' + value + '');
          test.scrollIntoView();
        }
        this.checkShowPicker = false;
      }, 10);
    }
  }

  /**
   * Scroll from year picker
   * @param position {string}
   */
  public scrollYear(position){
    const yearPicker = document.getElementById('yearPickers');
    if (position === 'top') {
      yearPicker.scrollTop -= 30;
    } else if (position === 'bottom') {
      yearPicker.scrollTop += 30;
    }
  }

  /**
   * Set year value to form
   * @param year
   */
  public setYearValue(year) {
    this.qualificationForm.patchValue({
      matriculatedYear: year
    });
    this.showYearPicker = false;
    this.validMatriculatedYear();
  }

  /**
   * Outside close year picker
   * @param e
   */
  public onClickedOutside(e: Event) {
    if (!this.checkShowPicker) {
      this.showYearPicker = false;
    }
  }

  /**
   * Reset from
   */
  public resetForm() {
    const type = this.qualificationForm.controls.type.value;
    this.qualificationForm.reset();
    this.selectedDateStart = '';
    this.selectedDateEnd = '';
    this.validError = {};
    this.completeSubjectObj = {
      eighty: null,
      seventy: null,
      sixty: null,
      fifty: null,
      forty: null,
      thirty: null,
      twenty: null
    };
    this.qualificationForm.patchValue({
      type: type
    });
  }

  /**
   * Get candidate qualification
   * @returns {Promise<void>}
   */
  public async getCandidateQualification(): Promise<void> {
    try {
      this.qualificationArray = await this._candidateService.getCandidateQualification();
      this.showUniversityExemption = this.isShowUniversityExemption();
      this.preloaderPage = false;
    } catch (err) {
      this._sharedService.showRequestErrors(err);
    }
  }

  /**
   * Create candidate qualification
   * @returns {Promise<void>}
   */
  public async createCandidateQualification(): Promise<void> {
    const valid = this.customQualificationValidate();
    if (valid === 1) {
      const data = {
        type: this.qualificationForm.controls.type.value,
        schoolName: this.qualificationForm.controls.schoolName.value,
        matriculatedYear: this.qualificationForm.controls.matriculatedYear.value,
        completeSubject: this.completeSubjectObj,
        tertiaryInstitution: this.qualificationForm.controls.tertiaryInstitution.value,
        tertiaryInstitutionCustom: this.qualificationForm.controls.tertiaryInstitutionCustom.value,
        levelQ: this.qualificationForm.controls.levelQ.value,
        specificQ: this.qualificationForm.controls.specificQ.value,
        specificQCustom: this.qualificationForm.controls.specificQCustom.value,
        specialization: this.qualificationForm.controls.specialization.value,
        specializationCustom: this.qualificationForm.controls.specializationCustom.value,
        education: this.qualificationForm.controls.education.value,
        startYear: this.qualificationForm.controls.startYear.value,
        endYear: this.qualificationForm.controls.endYear.value
      };
      try {
        const response = await this._candidateService.createCandidateQualification(data);
        this.modalActiveClose.dismiss();
        this.validError = {};
        this.selectedDateStart = '';
        this.selectedDateEnd = '';
        this.qualificationForm.reset();
        this.qualificationArray.unshift(response['qualification']);
        this._toastr.success('Qualification has been created');

        this.progressBar = response['percentage'];
        this._sharedService.progressBar = response['percentage'];
        localStorage.setItem('progressBar', String(response['percentage']));
        this.showUniversityExemption = this.isShowUniversityExemption();
      } catch (err) {
        this._sharedService.showRequestErrors(err);
      }
    }
  }

  /**
   * Other change form
   * @param key {string}
   */
  public otherChangeForm(key) {
    if (key === 'tertiaryInstitution') {
      if (!this.qualificationForm.controls[key].value || this.qualificationForm.controls[key].value === 'Other') {
        this.qualificationForm.patchValue({
          tertiaryInstitutionQCustom: null,
          levelQ: null,
          specificQ: null,
          specificQCustom: null,
          specialization: null,
          specializationCustom: null,
        });
      }
    } else if (key === 'tertiaryInstitutionCustom') {
      if (!this.qualificationForm.controls[key].value) {
        this.qualificationForm.patchValue({
          levelQ: null,
          specificQ: null,
          specificQCustom: null,
          specialization: null,
          specializationCustom: null,
        });
      }
    } else if (key === 'levelQ') {
      if (!this.qualificationForm.controls[key].value) {
        this.qualificationForm.patchValue({
          specificQ: null,
          specificQCustom: null,
          specialization: null,
          specializationCustom: null,
        });
      }
    } else if (key === 'specificQ') {
      this.qualificationForm.patchValue({
          specificQCustom: null,
          specialization: null,
          specializationCustom: null,
      });
      this.specializationField = JSON.parse(JSON.stringify(this._sharedService.specializationCandidate));
      if (this.qualificationForm.controls[key].value && this.qualificationForm.controls[key].value !== 'Other') {
        const specializationItem = this._sharedService.qualificationData.specialization.find(item => item.key === this.qualificationForm.controls[key].value);
        if (specializationItem.value !== undefined && specializationItem.value && specializationItem.value !== 'Other') {
          const specializationFieldIndex = this.specializationField.findIndex(item => item.id === specializationItem.value);
          if (this.specializationField[specializationFieldIndex] !== undefined) {
            this.specializationField.splice(specializationFieldIndex, 1);
            this.specializationField.unshift({id: specializationItem.value, name: specializationItem.value});
            this.qualificationForm.patchValue({
              specialization: specializationItem.value,
            });
          }
        }
      }
    } else if (key === 'specificQCustom') {
      if (!this.qualificationForm.controls[key].value) {
        this.qualificationForm.patchValue({
          specialization: null,
          specializationCustom: null,
        });
      }
    } else if (key === 'specialization') {
      this.qualificationForm.patchValue({
          specializationCustom: null,
      });
    }
  }

  /**
   * Validate matriculated year
   */
  public validMatriculatedYear() {
    if (!this.qualificationForm.controls.matriculatedYear.value) {
      this.validError['matriculatedYear'] = true;
      this.validError['matriculatedYearEnter'] = false;
    } else if(!/^[0-9]+$/.test(this.qualificationForm.controls.matriculatedYear.value)) {
      this.validError['matriculatedYearEnter'] = true;
      this.validError['matriculatedYear'] = false;
    } else if (this.qualificationForm.controls.matriculatedYear.value &&
      (this.qualificationForm.controls.matriculatedYear.value < 1927 ||
      this.qualificationForm.controls.matriculatedYear.value > 2018)) {
      this.validError['matriculatedYearEnter'] = true;
      this.validError['matriculatedYear'] = false;
    } else {
      this.validError['matriculatedYearEnter'] = false;
      this.validError['matriculatedYear'] = false;
    }
  }

  /**
   * Custom qualification from validate
   * @returns {number}
   */
  public customQualificationValidate() {
    let count = 1;
    if (this.qualificationForm.controls.type.value === 1) {
      let countTable = 0;
      for (const i of Object.keys(this.completeSubjectObj)) {
        if (!this.completeSubjectObj[i]) {
          countTable++;
        }
      }
      if (countTable === 7) {
        this.validError['completeSubject'] = true;
        count++;
      }
      if (!this.qualificationForm.controls.schoolName.value) {
        this.validError['schoolName'] = true;
        count++;
      }
      if (!this.qualificationForm.controls.matriculatedYear.value) {
        this.validError['matriculatedYear'] = true;
        this.validError['matriculatedYearEnter'] = false;
        count++;
      } else if (!/^[0-9]+$/.test(this.qualificationForm.controls.matriculatedYear.value)) {
        this.validError['matriculatedYearEnter'] = true;
        this.validError['matriculatedYear'] = false;
        count++;
      } else if (this.qualificationForm.controls.matriculatedYear.value &&
        (this.qualificationForm.controls.matriculatedYear.value < 1927 ||
        this.qualificationForm.controls.matriculatedYear.value > 2018)) {
        this.validError['matriculatedYearEnter'] = true;
        this.validError['matriculatedYear'] = false;
        count++;
      } else {
        this.validError['matriculatedYear'] = false;
        this.validError['matriculatedYearEnter'] = false;
      }
    } else if (this.qualificationForm.controls.type.value === 3) {
      if (!this.qualificationForm.controls.tertiaryInstitution.value) {
        this.validError['tertiaryInstitution'] = true;
        count++;
      } else if (this.qualificationForm.controls.tertiaryInstitution.value === 'Other' &&
          !this.qualificationForm.controls.tertiaryInstitutionCustom.value
      ) {
        this.validError['tertiaryInstitutionCustom'] = true;
        count++;
      }

      if (!this.qualificationForm.controls.levelQ.value) {
        this.validError['levelQ'] = true;
        count++;
      }

      if (!this.qualificationForm.controls.specificQ.value) {
        this.validError['specificQ'] = true;
        count++;
      } else if (this.qualificationForm.controls.specificQ.value === 'Other' &&
        !this.qualificationForm.controls.specificQCustom.value) {
        this.validError['specificQCustom'] = true;
        count++;
      }

      if (!this.qualificationForm.controls.specialization.value) {
        this.validError['specialization'] = true;
        count++;
      } else if (this.qualificationForm.controls.specialization.value === 'Other' &&
        !this.qualificationForm.controls.specializationCustom.value) {
        this.validError['specializationCustom'] = true;
        count++;
      }

      if (!this.qualificationForm.controls.startYear.value) {
        this.validError['startYear'] = true;
        count++;
      }
      if (!this.qualificationForm.controls.endYear.value) {
        this.validError['endYear'] = true;
        count++;
      }
      if (this.qualificationForm.controls.startYear.value && this.qualificationForm.controls.endYear.value) {
        const start = new Date(this.qualificationForm.controls.startYear.value);
        const end = new Date(this.qualificationForm.controls.endYear.value);
        if (start >= end) {
          count++;
        }
      }
    } else if (!this.qualificationForm.controls.type.value) {
      this.validError['type'] = true;
      count++;
    }
    return count;
  }

  /**
   * Check end year
   */
  public endYearValidate(){
    if (this.qualificationForm.controls.startYear.value && this.qualificationForm.controls.endYear.value) {
      const start = new Date(this.qualificationForm.controls.startYear.value);
      const end = new Date(this.qualificationForm.controls.endYear.value);
      if (start.getFullYear() > end.getFullYear()) {
        this.validError['endYearCheck'] = true;
      } else if (start.getFullYear() === end.getFullYear()) {
        if (start.getMonth() >= end.getMonth()) {
          this.validError['endYearCheck'] = true;
        } else {
          this.validError['endYearCheck'] = false;
        }
      } else {
        this.validError['endYearCheck'] = false;
      }
    }
  }

  /**
   * Exit add job page
   */
  public exitPage(){
    this.modalActiveClose.dismiss();
    this.qualificationForm.markAsPristine();
    this._router.navigate([this.urlRedirect]);
  }

  /**
   * Chosce permision check
   * @param permision {string}
   * @param value {string}
   */
  public choicePermisionCheck(permision, value) {
    this[permision] = value;
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
        this.checkLooking = false;
        this.visibilityLooking = true;
      } else {
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
    this._router.navigate(['/candidate/achievements']);
  }

  /**
   * Delete candidate achievements
   * @param achievement {object}
   * @return {Promise<void>}
   */
  public async deleteCandidateQualification(achievement): Promise<void> {
    const data = await this._candidateService.deleteCandidateQualification(achievement.id);
    this.progressBar = data.percentage;
    this._sharedService.progressBar = data.percentage;
    localStorage.setItem('progressBar', String(data.percentage));
    this._toastr.success('Qualification has been deleted');

    for (let i = 0; i < this.qualificationArray.length; i++) {
      if(this.qualificationArray[i].id === achievement.id ) {
        this.qualificationArray.splice(i, 1);
      }
    }
    this.showUniversityExemption = this.isShowUniversityExemption();
  }

  /**
   * Close modal
   */
  public closeActiveModal() {
    this.modalActiveClose.dismiss();
  }

  /**
   * Set value with subjects object
   * @param value {string}
   * @param key {string}
   */
  public setValueSubjects(value, key) {
    this.completeSubjectObj[key] = value;
  }

  /**
   * Get qualification to edit
   * @param id {number}
   * @returns {Promise<void>}
   */
  public async getEditQualification(id: number): Promise<void> {
    try {
      const data = await this._candidateService.getEditCandidateQualification(id);
    } catch (err) {
      this._sharedService.showRequestErrors(err);
    }
  }

  /**
   * Update candidate qualification
   * @returns {Promise<void>}
   */
  public async updateCandidateQualification(): Promise<any> {
    const valid = this.customQualificationValidate();
    if (valid === 1) {
      const data = {
        type: this.qualificationForm.controls.type.value,
        schoolName: this.qualificationForm.controls.schoolName.value,
        matriculatedYear: this.qualificationForm.controls.matriculatedYear.value,
        completeSubject: this.completeSubjectObj,
        tertiaryInstitution: this.qualificationForm.controls.tertiaryInstitution.value,
        tertiaryInstitutionCustom: this.qualificationForm.controls.tertiaryInstitutionCustom.value,
        levelQ: this.qualificationForm.controls.levelQ.value,
        specificQ: this.qualificationForm.controls.specificQ.value,
        specificQCustom: this.qualificationForm.controls.specificQCustom.value,
        specialization: this.qualificationForm.controls.specialization.value,
        specializationCustom: this.qualificationForm.controls.specializationCustom.value,
        education: this.qualificationForm.controls.education.value,
        startYear: this.qualificationForm.controls.startYear.value,
        endYear: this.qualificationForm.controls.endYear.value
      };
      try {
        const response = await this._candidateService.updateCandidateQualification(data, this.idQualification);
        this.validError = {};
        this.selectedDateStart = '';
        this.selectedDateEnd = '';
        this.qualificationForm.reset();
        this.qualificationArray.forEach((resp, i) => {
          if (resp.id === this.idQualification) {
            data['id'] = this.idQualification;
            this.qualificationArray[i] = data;
          }
        });
        this.modalActiveClose.dismiss();
        this._toastr.success('Qualification has been updated');

        this.progressBar = response['percentage'];
        this._sharedService.progressBar = response['percentage'];
        localStorage.setItem('progressBar', String(response['percentage']));
      } catch (err) {
        this._sharedService.showRequestErrors(err);
      }
    }
  }

  /**
   * Transformation date with month picker
   * @param month {number}
   * @param year {number}
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
   * Managed modal
   * @param content {any} - content to be shown in popup
   * @param qualification {object} - job id to be used for fetching data and showing in popup
   */
  public openVerticallyCentered(content: any, qualification) {
    this.showYearPicker = false;
    this.checkEdit = true;
    this.idQualification = qualification.id;
    this.qualificationForm.reset();

    let dateStart;
    if (qualification.startYear === null){
      dateStart = null;
    } else {
      dateStart = new Date(qualification.startYear);
      this.selectedDateStart = this.transformationDate((dateStart.getMonth() + 1), dateStart.getFullYear());
    }

    let dateEnd;
    if (qualification.endYear === null){
      dateEnd = null;
    } else {
      dateEnd = new Date(qualification.endYear);
      this.selectedDateEnd = this.transformationDate((dateEnd.getMonth() + 1), dateEnd.getFullYear());
    }

    this.qualificationForm.setValue({
      type: qualification.type,
      schoolName: qualification.schoolName,
      matriculatedYear: Number(qualification.matriculatedYear),
      tertiaryInstitution: qualification.tertiaryInstitution,
      tertiaryInstitutionCustom: qualification.tertiaryInstitutionCustom,
      levelQ: qualification.levelQ,
      specificQ: qualification.specificQ,
      specificQCustom: qualification.specificQCustom,
      specialization: qualification.specialization,
      specializationCustom: qualification.specializationCustom,
      education: qualification.education,
      startYear: qualification.startYear,
      endYear:  qualification.endYear
    });

    if (qualification.completeSubject === null) {
      this.completeSubjectObj = {
        eighty: null,
        seventy: null,
        sixty: null,
        fifty: null,
        forty: null,
        thirty: null,
        twenty: null
      };
    } else {
      this.completeSubjectObj = qualification.completeSubject;
    }
    this.modalActiveClose = this._modalService.open(content, { centered: true});
  }

  /**
   * Managed modal
   * @param content {any} - content to be shown in popup
   */
  public openVerticallyCenter(content: any) {
    this.showYearPicker = false;
    this.checkEdit = false;
    this.qualificationForm.reset();
    this.modalActiveClose = this._modalService.open(content, { centered: true, backdrop: 'static' });
    this.completeSubjectObj = {
      eighty: null,
      seventy: null,
      sixty: null,
      fifty: null,
      forty: null,
      thirty: null,
      twenty: null
    };
  }

}
