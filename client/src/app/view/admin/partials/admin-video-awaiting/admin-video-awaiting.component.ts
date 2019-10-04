import { Component, OnInit } from '@angular/core';
import { CandidateFileApprove } from '../../../../../entities/models-admin';
import { SharedService } from '../../../../services/shared.service';
import { ToastrService } from 'ngx-toastr';
import { AdminService } from '../../../../services/admin.service';
import { NgbModal } from '@ng-bootstrap/ng-bootstrap';

@Component({
  selector: 'app-admin-video-awaiting',
  templateUrl: './admin-video-awaiting.component.html',
  styleUrls: ['./admin-video-awaiting.component.scss']
})
export class AdminVideoAwaitingComponent implements OnInit {

  public approveCandidateVideoList = Array<CandidateFileApprove>();

  public preloaderPage = true;

  public paginationLoader = false;
  public pagination = 1;
  public loadMoreCheck = true;
  public modalActiveClose: any;

  public confirmFunction: string;
  public confirmData: any;
  public confirmStatus: any;
  public confirmArray: any;

  public dataVideo: any;
  public videoIndex: any;
  public checkDataVideo: boolean;
  public checkPreloaderVideo = [];

  constructor(
    private readonly _adminService: AdminService,
    private readonly _toastr: ToastrService,
    private readonly _sharedService: SharedService,
    private readonly _modalService: NgbModal
  ) {
    this._sharedService.checkSidebar = false;
  }

  ngOnInit() {
    window.scrollTo(0, 0);
    this.getCandidateVideosApprove();
  }

  /**
   * Upload admin video for candidate
   * @param userId
   * @param index
   * @param fileName
   * @returns {Promise<void>}
   */
  public async uploadAdminVideo(userId, index, fileName): Promise<any> {
    this.checkPreloaderVideo[index].status = true;

    let elem;
    if(!fileName) {
      elem = (<HTMLInputElement>document.getElementById(index));
    } else {
      elem = (<HTMLInputElement>document.getElementById(fileName));
    }
    const formData = new FormData();

    if(elem.files.length > 0){
      formData.append('video', elem.files[0]);
    }

    try {
      const data = await this._adminService.uploadAdminVideoForCandidate(formData, userId);
      this.approveCandidateVideoList[index].adminUrl = data.adminUrl;
      this.checkPreloaderVideo[index].status = false;
      this.modalActiveClose.dismiss();
    }
    catch (err) {
      this._sharedService.showRequestErrors(err);
    }
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
   * Load pagination
   */
  public async loadPagination(): Promise<void> {
    this.pagination++;
    this.paginationLoader = true;
    this.getCandidateVideosApprove();
  }

  /**
   * Get candidate video was need approved
   * @return {Promise<void>}
   */
  public async getCandidateVideosApprove(): Promise<void> {
    try {
      const response = await this._adminService.getCandidateVideosApprove(this.pagination);

      response.items.forEach((item) => {
        this.approveCandidateVideoList.push(item);
        this.checkPreloaderVideo.push({status: false});
      });

      if (response.pagination.total_count === this.approveCandidateVideoList.length) {
        this.loadMoreCheck = false;
      }
      else if (response.pagination.total_count !== this.approveCandidateVideoList.length){
        this.loadMoreCheck = true;
      }
      this.paginationLoader = false;

      this.preloaderPage = false;
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

      const index = this.approveCandidateVideoList.indexOf(file);
      this.approveCandidateVideoList.splice(index, 1);
      this._sharedService.sidebarAdminBadges.candidateVideoNew--;
      this._toastr.success((status) ? 'Video has been approved' : 'Video has been declined');
    }
    catch (err) {
      this._sharedService.showRequestErrors(err);
    }
  }

  /**
   * Remove video
   * @param id
   * @return {Promise<void>}
   */
  public async removeVideo(id): Promise<void> {
    try{
      await this._adminService.removeVideo(id);

      const index = this.approveCandidateVideoList.indexOf(id);
      this.approveCandidateVideoList.splice(index, 1);
      this._sharedService.sidebarAdminBadges.candidateVideoNew--;
      this._toastr.success('Video has been declined');
    }
    catch (err) {
      this._sharedService.showRequestErrors(err);
    }
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

}
