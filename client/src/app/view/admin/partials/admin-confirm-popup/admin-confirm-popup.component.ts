import { Component, Input, OnInit } from '@angular/core';
import { BusinessApprove, CandidateApprove, CandidateFileApprove } from '../../../../../entities/models-admin';
import { AdminService } from '../../../../services/admin.service';
import { SharedService } from '../../../../services/shared.service';
import { ToastrService } from 'ngx-toastr';

@Component({
  selector: 'app-admin-confirm-popup',
  templateUrl: './admin-confirm-popup.component.html',
  styleUrls: ['./admin-confirm-popup.component.scss']
})
export class AdminConfirmPopupComponent implements OnInit {

  @Input() closePopup;
  public _confirmFunction;
  public _confirmData;
  public _confirmStatus;
  public _confirmArray;

  @Input('confirmFunction') set confirmFunction(confirmFunction) {
    if (confirmFunction) {
      this._confirmFunction = confirmFunction;
    }
  }

  @Input('confirmData') set confirmData(confirmData) {
    if (confirmData) {
      this._confirmData = confirmData;
    }
  }

  @Input('confirmStatus') set confirmStatus(confirmStatus) {
    if (confirmStatus) {
      if(confirmStatus === 'true') {
        this._confirmStatus = true;
      }
      else if(confirmStatus === 'false'){
        this._confirmStatus = false;
      }
      else {
        this._confirmStatus = confirmStatus;
      }
    }
  }

  @Input('confirmArray') set confirmArray(confirmArray) {
    if (confirmArray) {
      this._confirmArray = confirmArray;
    }
  }

  constructor(
    private readonly _adminService: AdminService,
    private readonly _sharedService: SharedService,
    private readonly _toastr: ToastrService
  ) { }

  ngOnInit() {
  }

  /**
   * Confirm popup
   */
  public confirmPopup(){
    if(this._confirmFunction === 'adminPendingInterview'){
      this[this._confirmFunction](this._confirmData, this._confirmStatus);
      this.closePopup();
    }
    else if(this._confirmFunction === 'removeVideo'){
      this[this._confirmFunction](this._confirmData);
      this.closePopup();
    }
    else{
      this[this._confirmFunction](this._confirmData, this._confirmStatus);
      this.closePopup();
    }
  }

  /**
   * Set up interviews
   * @param interview {object}
   * @param status {string}
   * @return {Promise<void>}
   */
  public async adminPendingInterview(interview, status): Promise<void> {
    try {
      await this._adminService.adminPendingInterview(interview.id, status);
      if(status === true){
        this._sharedService.sidebarAdminBadges.interviewPending--;
        this._sharedService.sidebarAdminBadges.interviewPlaced++;
      }
      else{
        this._sharedService.sidebarAdminBadges.interviewPending--;
      }
      const notificationMessage = (status) ? 'Applicant has been hired!' : 'Applicant has been declined!';
      this._toastr.success(notificationMessage);
      const index = this._confirmArray.indexOf(interview);
      this._confirmArray.splice(index, 1);
    }
    catch (err) {
      this._sharedService.showRequestErrors(err);
    }
  }


  /**
   * Remove video
   * @param id {number}
   * @return {Promise<void>}
   */
  public async removeVideo(id): Promise<void> {
    try{
      await this._adminService.removeVideo(id.userId);

      const index = this._confirmArray.indexOf(id);
      this._confirmArray.splice(index, 1);
      this._sharedService.sidebarAdminBadges.candidateVideoNew--;
      this._toastr.success('Video has been declined');
    }
    catch (err) {
      this._sharedService.showRequestErrors(err);
    }
  }

  /**
   * changes status for hob specified with id
   * @param job {object} - id of the job
   * @param status {boolean} - status of the job - true - approve, false - decline
   */
  public async approveJob(job, status: boolean): Promise<void> {
    try {
      await this._adminService.changeJobsStatus(job.id, { approve: status });

      if(status === true){
        this._sharedService.sidebarAdminBadges.jobNew--;
      }
      else{
        this._sharedService.sidebarAdminBadges.jobNew--;
      }

      const notificationMessage = (status) ? 'Job has been approved!' : 'Job has been declined!';
      this._toastr.success(notificationMessage);
      const index = this._confirmArray.indexOf(job);
      this._confirmArray.splice(index, 1);
    }
    catch (err) {
      this._sharedService.showRequestErrors(err);
    }
  }

  /**
   * Managed business user
   * @param {BusinessApprove} user
   * @param {boolean} status
   * @return {void}
   */
  public async managedBusinessUser(user: BusinessApprove, status: boolean): Promise<void> {
    try {
      await this._adminService.managedBusinessUser(user.id, status);

      const index = this._confirmArray.indexOf(user);
      this._confirmArray.splice(index, 1);
      if(status === true){
        this._sharedService.sidebarAdminBadges.clientNew--;
        this._sharedService.sidebarAdminBadges.clientAll++;
      }
      else {
        this._sharedService.sidebarAdminBadges.clientNew--;
      }
      this._toastr.success((status) ? 'Client has been approved' : 'Client has been declined');
    }
    catch (err) {
      this._sharedService.showRequestErrors(err);
    }
  }

  /**
   * Managed candidate user
   * @param {CandidateApprove} user
   * @param {boolean} status
   * @return {Promise<void>}
   */
  public async managedCandidateUser(user: CandidateApprove, status: boolean): Promise<void> {
    try {
      await this._adminService.managedCandidateUser(user.id, status);

      const index = this._confirmArray.indexOf(user);
      this._confirmArray.splice(index, 1);
      if(status === true){
        this._sharedService.sidebarAdminBadges.candidateNew--;
        this._sharedService.sidebarAdminBadges.candidateAll++;
      }
      else {
        this._sharedService.sidebarAdminBadges.candidateNew--;
      }
      this._toastr.success((status) ? 'Candidate has been approved' : 'Candidate has been declined');
    }
    catch (err) {
      this._sharedService.showRequestErrors(err);
    }
  }

  /**
   * Managed Candidate File
   * @param {CandidateFileApprove} file
   * @param {boolean} status
   * @returns {Promise<void>}
   */
  public async managedCandidateFile(file: CandidateFileApprove, status: boolean): Promise<void> {
    try {
      await this._adminService.managedCandidateFile(file, status);

      const index = this._confirmArray.indexOf(file);
      this._confirmArray.splice(index, 1);
      this._sharedService.sidebarAdminBadges.candidateFileNew--;
      this._toastr.success((status) ? 'File has been approved' : 'File has been declined');
    }
    catch (err) {
      this._sharedService.showRequestErrors(err);
    }
  }

  /**
   * Managed Client File
   * @param {CandidateFileApprove} file
   * @param {boolean} status
   * @returns {Promise<void>}
   */
  public async managedClientFile(file, status: boolean): Promise<void> {
    try {
      await this._adminService.managedClientFile(file, status);

      const index = this._confirmArray.indexOf(file);
      this._confirmArray.splice(index, 1);
      this._sharedService.sidebarAdminBadges.clientFiles--;
      this._toastr.success((status) ? 'File has been approved' : 'File has been declined');
    }
    catch (err) {
      this._sharedService.showRequestErrors(err);
    }
  }


  /**
   * Managed Candidate Video
   * @param {Object} file
   * @param {boolean} status
   * @returns {Promise<void>}
   */
  public async managedCandidateVideo(file, status: boolean): Promise<void> {
    try {
      await this._adminService.managedCandidateVideo(file, status);

      const index = this._confirmArray.indexOf(file);
      this._confirmArray.splice(index, 1);
      this._sharedService.sidebarAdminBadges.candidateVideoNew--;
      this._toastr.success((status) ? 'Video has been approved' : 'Video has been declined');
    }
    catch (err) {
      this._sharedService.showRequestErrors(err);
    }
  }

}
