import { Component, OnInit } from '@angular/core';
import { NgbModal } from '@ng-bootstrap/ng-bootstrap';
import { ToastrService } from 'ngx-toastr';
import {} from '@types/googlemaps';
import {
  BusinessApprove, CandidateApprove, CandidateFileApprove, AdminInterviewList,
  CandidateVideoApprove
} from '../../../../../entities/models-admin';
import { BusinessJobsAwaitingApproval } from '../../../../../entities/models';
import { AdminService } from '../../../../services/admin.service';
import { AuthService } from '../../../../services/auth.service';
import { SharedService } from '../../../../services/shared.service';
import { Router } from '@angular/router';

@Component({
  selector: 'app-admin-dashboard',
  templateUrl: './admin-dashboard.component.html',
  styleUrls: ['./admin-dashboard.component.scss']
})
export class AdminDashboardComponent implements OnInit {

  public approveBusinessList = Array<BusinessApprove>();
  public approveCandidateList = Array<CandidateApprove>();
  public approveCandidateFileList = Array<CandidateFileApprove>();
  public approveClientFileList = [];
  public selectedBusinessJobId: number;

  public preloader = false;
  public modalActiveClose: any;

  public jobsAwaitingApprove = Array<BusinessJobsAwaitingApproval>();
  public setUpInterviewListCandidate = Array<AdminInterviewList>();
  public setUpInterviewListClient = Array<AdminInterviewList>();
  public pendingInterviewList = Array<AdminInterviewList>();
  public applicantsAwaiting = Array<AdminInterviewList>();
  public applicantsShortlist = Array<AdminInterviewList>();

  public preloaderPage = true;

  public confirmFunction: string;
  public confirmData: any;
  public confirmStatus: any;
  public confirmArray: any;

  public selectedBusinessJob: any;
  public selectedBusinessJobArray: any;
  public selectedBusinessJobStatus: boolean;

  public dataFile: any;
  public fileIndex: any;
  public checkDataFile: boolean;
  public checkPreloader = [];

  public dataVideo: any;
  public videoIndex: any;
  public checkDataVideo: boolean;

  constructor(
    private readonly _modalService: NgbModal,
    private readonly _adminService: AdminService,
    private readonly _authService: AuthService,
    private readonly _toastr: ToastrService,
    private readonly _sharedService: SharedService,
    private readonly _router: Router
  ) {
    this._sharedService.checkSidebar = false;
  }

  ngOnInit() {
    window.scrollTo(0, 0);
    this.getDashboardData('');
  }

  /**
   * Open confirm popup
   * @param content
   * @param confirmArray
   * @param nameFunction
   * @param data
   * @param status
   */
  public openConfirm(content: any, confirmArray, nameFunction, data, status): void {
    this.modalActiveClose = this._modalService.open(content, { centered: true, 'size': 'sm', windowClass: 'width-min' });
    this.confirmFunction = nameFunction;
    this.confirmData = data;
    this.confirmStatus = status;
    this.confirmArray = confirmArray;
  }

  /**
   * Upload admin files for candidate
   * @param fieldName
   * @param url
   * @param userId
   * @param index
   * @param fileName
   * @returns {Promise<void>}
   */
  public async uploadAdminFiles(fieldName, url, userId, index, fileName): Promise<any> {
    this.checkPreloader[index].status = true;

    let elem;
    if(!fileName) {
      elem = (<HTMLInputElement>document.getElementById(index));
    } else {
      elem = (<HTMLInputElement>document.getElementById(fileName));
    }
    const formData = new FormData();

    if(elem.files.length > 0){
      formData.append('file', elem.files[0]);
    }

    formData.append('fieldName', fieldName);
    formData.append('url', url);

    try {
      const data = await this._adminService.uploadAdminFilesForCandidate(formData, userId);
      this.approveCandidateFileList[index].adminUrl = data.adminUrl;
      this.checkPreloader[index].status = false;
      this.modalActiveClose.dismiss();
    }
    catch (err) {
      this._sharedService.showRequestErrors(err);
    }
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
      const data = await this._adminService.uploadAdminFilesForClient(formData, jobId);
      this.approveClientFileList[index].adminUrl = data.adminUrl;
      this.modalActiveClose.dismiss();
    }
    catch (err) {
      this._sharedService.showRequestErrors(err);
    }
  }

  /**
   * Hide articles firm
   * @param elem
   */
  public hideArticlesFirm(elem): void {
    let nextSibling = elem.nextSibling;
    while(nextSibling && nextSibling.nodeType != 1) {
      nextSibling = nextSibling.nextSibling
    }
    nextSibling.style.opacity = 0;
  }

  /**
   * Get Dashboard Data (approveBusinessList, approveCandidateList)
   * @param {string} limit
   * @returns {Promise<void>}
   */
  public async getDashboardData(limit = '50'): Promise<void> {
    try {
      const dashboardData = await this._adminService.getDashboardData(limit);

      this.approveBusinessList = dashboardData.newClients;
      this.approveCandidateList = dashboardData.newCandidates;
      this.approveCandidateFileList = dashboardData.newFiles;
      this.approveCandidateFileList.forEach(() => {
        this.checkPreloader.push({status: false});
      });

      this.approveClientFileList = dashboardData['clientFiles'];

      this.jobsAwaitingApprove = dashboardData.newJobs;

      dashboardData.interviewsSetUpCandidate.forEach((item) => {
        item.enabled = false;
        this.setUpInterviewListCandidate.push(item);
      });

      dashboardData.interviewsSetUpClient.forEach((item) => {
          item.enabled = false;
          this.setUpInterviewListClient.push(item);
      });

      // this.setUpInterviewList = dashboardData.interviewsSetUp;
      this.pendingInterviewList = dashboardData.interviewsPending;
      this.applicantsAwaiting = dashboardData.awaitingApplicants;
      this.applicantsShortlist = dashboardData.shortlistApplicants;
      this.preloaderPage = false;
    }
    catch (err) {
      this._sharedService.showRequestErrors(err);
    }
  }

  /**
   * Close modal
   */
  public closeActiveModal(): void {
    this.modalActiveClose.dismiss();
  }

  /**
   * Managed modal
   * @param content {any} - content to be shown in popup
   * @param jobId {number} - job id to be used for fetching data and showing in popup
   */
  public openVerticallyCentered(content: any,  jobId: number): void {
    this.modalActiveClose = this._modalService.open(content, { centered: true, 'size': 'lg' });
    this.selectedBusinessJobId = jobId;
  }

  /**
   * Managed modal
   * @param content {any} - content to be shown in popup
   * @param job {object} - job id to be used for fetching data and showing in popup
   * @param data {array} - job id to be used for fetching data and showing in popup
   * @param status {boolean} - job id to be used for fetching data and showing in popup
   */
  public openVerticallyCenterJob(content: any,  job, data, status): void {
    this.selectedBusinessJob = job;
    this.selectedBusinessJobArray = data;
    this.selectedBusinessJobStatus = status;
    this.modalActiveClose = this._modalService.open(content, {centered: true, 'size': 'lg'});
  };

  /**
   * Set up interviews
   * @param interview {object}
   * @param listItems {Array}
   * @param enabled {boolean}
   * @return {Promise<void>}
   */
  public async adminSetUpInterview(interview, listItems, enabled): Promise<void> {
    try {
      enabled = true;
      await this._adminService.adminSetUpInterview(interview.id);
      this._sharedService.sidebarAdminBadges.interviewPending++;
      this._sharedService.sidebarAdminBadges.interviewSetUp--;
      this._toastr.success('Interview has been set up');
      let getUpdateProfile = listItems.find(user => user === interview);
      getUpdateProfile.created = new Date();
      this.pendingInterviewList.push(getUpdateProfile);
      const index = listItems.indexOf(interview);
      listItems.splice(index, 1);
    }
    catch (err) {
      this._sharedService.showRequestErrors(err);
    }
  }

  /**
   * Open modal
   * @param content
   */
  public openVerticallyCenter(content) {
    this.modalActiveClose = this._modalService.open(content, { centered: true, windowClass: 'second-popup', 'size': 'lg' });
  }

  /**
   * Open modal
   * @param content
   * @param data
   * @param index
   * @param status
   */
  public openVerticallyCenterFile(content, data, index, status) {
    this.dataFile = data;
    this.checkDataFile = status;
    this.fileIndex = index;
    this.modalActiveClose = this._modalService.open(content, { centered: true, windowClass: 'second-popup', 'size': 'lg' });
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
   * opens popup
   * @param content - content to be placed within
   * @param data - candidateId id to show within popup
   * @param status - candidateId id to show within popup
   * @param index - candidateId id to show within popup
   */
  public openVerticallyCenterVideo(content: any, data, index, status) {
    this.dataVideo = data;
    this.checkDataVideo = status;
    this.videoIndex = index;
    this.modalActiveClose = this._modalService.open(content, { centered: true, size: 'lg', windowClass: 'xlModal' });
  }

  /**
   * logs user out
   * @returns void
   */
  public logout(): void {
    this._authService.logout();
  }


  /**
   * Router admin for candidate on id
   * @param id
   */
  public routeCandidate(id) {
    this._router.navigate(['/admin/edit_candidate'], { queryParams: { candidateId: id} });
  }

  /**
   * Managed modal
   * @param content {any} - content to be shown in popup
   * @param id {number} - job id to be used for fetching data and showing in popup
   */
  public openVerticallyCenteredCompany(content: any,  id: number) {
    this.modalActiveClose = this._modalService.open(content, { centered: true, 'size': 'lg' });
    this.selectedBusinessJobId = id;
  }

  /**
   * Managed modal
   * @param content {any} - content to be shown in popup
   * @param jobId {number} - job id to be used for fetching data and showing in popup
   * @param clientID {number} - job id to be used for fetching data and showing in popup
   * @param subcontent {any} - job id to be used for fetching data and showing in popup
   */
  public openVerticallyCenterClientJob(content: any, jobId, clientID, subcontent): void {
    if (jobId){
      this.selectedBusinessJob = {
        id: jobId
      };
      this.modalActiveClose = this._modalService.open(content, { centered: true, 'size': 'lg' });
    } else {
      this.selectedBusinessJobId = clientID;
      this.modalActiveClose = this._modalService.open(subcontent, { centered: true, 'size': 'lg' });
    }
  }

}
