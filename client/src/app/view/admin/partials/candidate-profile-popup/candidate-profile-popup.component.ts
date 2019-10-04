import { Component, Input, OnInit } from '@angular/core';
import { FormControl, FormGroup, Validators } from '@angular/forms';
import { AdminCandidateProfile } from '../../../../../entities/models-admin';
import { ToastrService } from 'ngx-toastr';
import { SharedService } from '../../../../services/shared.service';
import { AdminService } from '../../../../services/admin.service';
import { articles } from '../../../../constants/articles.const';
import { ValidateNumber } from '../../../../validators/custom.validator';
import { INgxMyDpOptions } from 'ngx-mydatepicker';

@Component({
  selector: 'app-candidate-profile-popup',
  templateUrl: './candidate-profile-popup.component.html',
  styleUrls: ['./candidate-profile-popup.component.scss']
})
export class CandidateProfilePopupComponent implements OnInit {


  private _currentId: number;
  private _candidateList = [];

  @Input() closePopup;
  @Input('currentId') set currentId(currentId: number) {
    if (currentId) {
      this._currentId = currentId;
      // this.getDetailsProfileCandidate(currentId);
    }
  }
  get currentId(): number {
    return this._currentId;
  }

  @Input('candidateList') set candidateList(candidateList) {
    if (candidateList) {
      this._candidateList = candidateList;
    }
  }
  get candidateList() {
    return this._candidateList;
  }

  public candidateForm: FormGroup;
  public candidateProfileDetails: AdminCandidateProfile;

  public modalActiveClose: any;
  public articles = articles;
  public preloaderPopup = true;

  public articlesOther = false;
  public saicaStatus = false;
  public checkSaica = true;

  public myOptionsDate: INgxMyDpOptions = { dateFormat: 'yyyy/mm/dd' };
  public model: any = { date: { year: 2018, month: 10, day: 9 } };

  constructor(
    private readonly _adminService: AdminService,
    private readonly _sharedService: SharedService,
    private readonly _toastr: ToastrService
  ) {
    this._sharedService.checkSidebar = false;
  }

  ngOnInit() {
    this.candidateForm = new FormGroup({
      firstName: new FormControl('', [Validators.required, Validators.minLength(2)]),
      lastName: new FormControl('', [Validators.required, Validators.minLength(2)]),
      phone: new FormControl('', [
        Validators.required,
        ValidateNumber
      ]),
      email: new FormControl('', Validators.compose([
        Validators.required,
        Validators.email
      ])),
      //boards: new FormControl('', [Validators.required]),
      saicaStatus: new FormControl(null, [Validators.required]),
      articlesFirm: new FormControl(null, [Validators.required]),
      dateArticlesCompleted: new FormControl('', [Validators.required]),
      saicaNumber: new FormControl('', [
        this.saicaValidator('saicaStatus')
      ]),
      articlesFirmName: new FormControl('', [
        this.articlesFirmNameValidator('articlesFirm')
      ])
    });
    this.preloaderPopup = false;
  }
  /**
   * Check select status articles firm
   * @param label
   */
  public checkStatusArticlesFirm(label){
    if (label === 'Other'){
      this.articlesOther = true;
    }
    else{
      this.articlesOther = false;
    }
  }

  /**
   * Check SAICA status
   * @param label
   */
  public checkSaicaStatus(label){
    if(label === 1){
      this.saicaStatus = true;
      this.checkSaica = true;
    }
    else{
      this.saicaStatus = false;
      this.checkSaica = true;
    }
  }

  /**
   * Articles Firm Name validator
   * @param otherControlName {string}
   * @return {(control:FormControl)=>(null|{matchOther: boolean})}
   */
  public articlesFirmNameValidator (otherControlName: string) {
    let thisControl: FormControl;
    let otherControl: FormControl;
    return function articlesFirmNameValidator (control: FormControl) {
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
      if (otherControl.value === 'Other' && !thisControl.value) {
        return {
          matchOther: true
        };
      }
      return null;
    };
  }

  /**
   * Password validator
   * @param otherControlName {string}
   * @return {(control:FormControl)=>(null|{matchOther: boolean})}
   */
  public saicaValidator (otherControlName: string) {
    let thisControl: FormControl;
    let otherControl: FormControl;
    return function saicaValidator (control: FormControl) {
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
      if (otherControl.value === 1 && !thisControl.value) {
        return {
          matchOther: true
        };
      }
      return null;
    };
  }

  /**
   * Get details profile candidate
   * @param id {number}
   * @return {Promise<void>}
   */
  public async getDetailsProfileCandidate(id: number): Promise<void> {
    try {
      this.candidateProfileDetails = await this._adminService.getDetailsProfileCandidate(id);

      this.candidateForm.setValue({
        firstName: this.candidateProfileDetails.user.firstName,
        lastName: this.candidateProfileDetails.user.lastName,
        phone: this.candidateProfileDetails.user.phone,
        email: this.candidateProfileDetails.user.email,
        //boards: this.candidateProfileDetails.profile.boards,
        // articlesFirm: this.candidateProfileDetails.profile.articlesFirm,
        // saicaNumber: this.candidateProfileDetails.profile.saicaNumber,
        // dateArticlesCompleted: this.candidateProfileDetails.profile.dateArticlesCompleted,
        // saicaStatus: (this.candidateProfileDetails.profile.saicaStatus === 0) ? null : this.candidateProfileDetails.profile.saicaStatus,
        // articlesFirmName: this.candidateProfileDetails.profile.articlesFirmName,
      });
      this.preloaderPopup = false;
      // this.checkStatusArticlesFirm(this.candidateProfileDetails.profile.articlesFirm);
      // this.checkSaicaStatus(this.candidateProfileDetails.profile.saicaStatus);

      // let dateArticlesCompleted = new Date(this.candidateProfileDetails.profile.dateArticlesCompleted);

      // if (this.candidateProfileDetails.profile.dateArticlesCompleted === null){
      //   dateArticlesCompleted = null;
      // } else {
      //   dateArticlesCompleted = new Date(this.candidateProfileDetails.profile.dateArticlesCompleted);
      //
      //   this.candidateForm.patchValue({
      //     dateArticlesCompleted: {
      //       date: {
      //         year: dateArticlesCompleted.getFullYear(),
      //         month: dateArticlesCompleted.getMonth() + 1,
      //         day: dateArticlesCompleted.getDate(),
      //       }
      //     }
      //   });
      // }
    }
    catch (err) {
      this._sharedService.showRequestErrors(err);
    }
  }

  /**
   * Update profile candidate
   * @return {Promise<void>}
   */
  public async updateCandidateProfile(): Promise<void> {

    //this.candidateProfileDetails.profile.boards = Number(this.candidateForm.value.boards);
    // this.candidateProfileDetails.profile.articlesFirm = this.candidateForm.value.articlesFirm;
    // this.candidateProfileDetails.profile.saicaStatus = this.candidateForm.value.saicaStatus;
    // this.candidateProfileDetails.profile.dateArticlesCompleted = this.candidateForm.value.dateArticlesCompleted.formatted;

    // this.candidateProfileDetails.profile.articlesFirmName = (this.candidateForm.value.articlesFirm !== 'Other') ? '' : this.candidateForm.value.articlesFirmName;
    // this.candidateProfileDetails.profile.saicaNumber = (this.candidateForm.value.saicaStatus !== 1) ? '' : this.candidateForm.value.saicaNumber;

    this.candidateProfileDetails.user.email = this.candidateForm.value.email;
    this.candidateProfileDetails.user.firstName = this.candidateForm.value.firstName;
    this.candidateProfileDetails.user.lastName = this.candidateForm.value.lastName;
    this.candidateProfileDetails.user.phone = this.candidateForm.value.phone;

    try {
      await this._adminService.updateCandidateProfile(this.candidateProfileDetails.user.id, this.candidateProfileDetails);

      const getUpdateProfile = this._candidateList.find(user => user.id === this.candidateProfileDetails.user.id);

      getUpdateProfile.firstName = this.candidateForm.value.firstName;
      getUpdateProfile.lastName = this.candidateForm.value.lastName;
      getUpdateProfile.phone = this.candidateForm.value.phone;
      getUpdateProfile.email = this.candidateForm.value.email;
      getUpdateProfile.articlesFirm = this.candidateForm.value.articlesFirm;

      this._toastr.success('Candidate has been updated');
      this.closePopup();
    } catch (err) {
      this._sharedService.showRequestErrors(err);
    }

  }

}
