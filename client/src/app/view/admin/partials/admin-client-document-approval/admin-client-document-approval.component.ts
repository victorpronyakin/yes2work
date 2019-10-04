import {Component, ElementRef, OnInit, ViewChild} from '@angular/core';
import {CandidateFileApprove} from "../../../../../entities/models-admin";
import {AdminService} from "../../../../services/admin.service";
import {SharedService} from "../../../../services/shared.service";
import {NgbModal} from "@ng-bootstrap/ng-bootstrap";
import {PaginationService} from "../../../../services/pagination.service";

@Component({
  selector: 'app-admin-client-document-approval',
  templateUrl: './admin-client-document-approval.component.html',
  styleUrls: ['./admin-client-document-approval.component.scss']
})
export class AdminClientDocumentApprovalComponent implements OnInit {

  @ViewChild('file') private file: ElementRef;

  public approveClientFileList = [];

  public preloaderPage = true;

  public paginationLoader = false;
  public pagination = 1;
  public loadMoreCheck = true;

  public modalActiveClose: any;

  public confirmFunction: string;
  public confirmData: any;
  public confirmStatus: any;
  public confirmArray: any;
  public dataFile: any;
  public fileIndex: any;
  public checkDataFile: boolean;
  public checkPreloader = [];

  public search: string = '';
  public orderBy: string = '';
  public orderSort: boolean;
  public paginationFilter = false;

  public totalItems: number;
  public pager: any = {
    currentPage: 1
  };

  constructor(
    private readonly _adminService: AdminService,
    private readonly _sharedService: SharedService,
    private readonly _paginationService: PaginationService,
    private readonly _modalService: NgbModal
  ) {
    this._sharedService.checkSidebar = false;
  }

  ngOnInit() {
    window.scrollTo(0, 0);
    this.getCandidateFilesApprove(this.search).then(() => {
      this.pager = this._paginationService.getPager(this.totalItems, 1);
    });
  }

  /**
   * Set pagination page
   * @param {number} page
   */
  public setPage(page: number) {
    this.paginationLoader = true;
    this.approveClientFileList = [];
    this.checkPreloader = [];
    this.pager = this._paginationService.getPager(this.totalItems, page);
    window.scrollTo(100, 0);

    this.getCandidateFilesApprove(this.search);
  }

  /**
   * Sort by table columns
   */
  public sortCandidate(column: string): void {
    this.resetArrayPagination();
    this.paginationFilter = true;

    this.orderBy = column;
    this.orderSort = !this.orderSort;

    this.getCandidateFilesApprove(this.search);
  }

  /**
   * Reset pagination
   */
  public resetArrayPagination() {
    this.approveClientFileList = [];
    this.pager.currentPage = 1;
  }

  /**
   * Reset sorting
   */
  public resetSorting() {
    this.orderBy = null;
    this.orderSort = null;
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
      this.approveClientFileList[index].adminUrl = data.adminUrl;
      this.checkPreloader[index].status = false;
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
      const data = await this._adminService.uploadAdminFilesForClient(formData, jobId);
      this.approveClientFileList[index].adminUrl = data.adminUrl;
      this.modalActiveClose.dismiss();
    }
    catch (err) {
      this._sharedService.showRequestErrors(err);
    }
  }

  /**
   * Get candidate file was need approved
   * @return {Promise<void>}
   */
  public async getCandidateFilesApprove(search): Promise<void> {
    this.search = search;

    try {
      const response = await this._adminService.getApproveClientFile(this.pager.currentPage, this.orderBy, this.orderSort, this.search);

      response.items.forEach((item) => {
        this.approveClientFileList.push(item);
        this.checkPreloader.push({status: false});
      });

      this.totalItems = response.pagination.total_count;
      this.pager = this._paginationService.getPager(this.totalItems, this.pager.currentPage);

      if (response.pagination.total_count === this.approveClientFileList.length) {
        this.loadMoreCheck = false;
      } else if (response.pagination.total_count !== this.approveClientFileList.length){
        this.loadMoreCheck = true;
      }
      this.paginationLoader = false;
      this.paginationFilter = false;
      this.preloaderPage = false;
    }
    catch (err) {
      this._sharedService.showRequestErrors(err);
    }
  }

  /**
   * Open modal
   * @param content
   * @param data
   * @param index
   * @param status
   */
  public openVerticallyCenter(content, data, index, status) {
    this.dataFile = data;
    this.checkDataFile = status;
    this.fileIndex = index;
    this.modalActiveClose = this._modalService.open(content, { centered: true, windowClass: 'second-popup', 'size': 'lg' });
  }

}
