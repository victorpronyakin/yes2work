import { Component, OnInit } from '@angular/core';
import { AdminService } from '../../../../services/admin.service';
import { ToastrService } from 'ngx-toastr';
import { SharedService } from '../../../../services/shared.service';
import { AdminInterviewList } from "../../../../../entities/models-admin";
import { Router } from '@angular/router';
import { NgbModal } from '@ng-bootstrap/ng-bootstrap';
import { PaginationService } from '../../../../services/pagination.service';

@Component({
  selector: 'app-admin-pending-interview',
  templateUrl: './admin-pending-interview.component.html',
  styleUrls: ['./admin-pending-interview.component.scss']
})
export class AdminPendingInterviewComponent implements OnInit {

  public pendingInterviewList = Array<AdminInterviewList>();

  public preloaderPage = true;
  public totalCount: number;

  public paginationLoader = false;
  public pagination = 1;
  public loadMoreCheck = true;
  public modalActiveClose: any;

  public confirmFunction: string;
  public confirmData: any;
  public confirmStatus: any;
  public confirmArray: any;

  public selectedBusinessId;
  public selectedBusinessJob;

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
    private readonly _toastr: ToastrService,
    private readonly _sharedService: SharedService,
    private readonly _router: Router,
    private readonly _paginationService: PaginationService,
    private readonly _modalService: NgbModal
  ) {
    this._sharedService.checkSidebar = false;
  }

  ngOnInit() {
    window.scrollTo(0, 0);
    this.pendingInterviewList = [];
    this.getPendingInterview(this.search).then(() => {
      this.pager = this._paginationService.getPager(this.totalItems, 1);
    });
  }

  /**
   * Set pagination page
   * @param {number} page
   */
  public setPage(page: number) {
    this.paginationLoader = true;
    this.pendingInterviewList = [];
    this.pager = this._paginationService.getPager(this.totalItems, page);
    window.scrollTo(100, 0);

    this.getPendingInterview(this.search);
  }

  /**
   * Sort by table columns
   */
  public sortCandidate(column: string): void {
    this.resetArrayPagination();
    this.paginationFilter = true;

    this.orderBy = column;
    this.orderSort = !this.orderSort;

    this.getPendingInterview(this.search);
  }

  /**
   * Reset Array
   */
  public resetArrayPagination(): void{
    this.pendingInterviewList = [];
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
  public openVerticallyCentered(content: any,  id: number) {
    this.modalActiveClose = this._modalService.open(content, { centered: true, 'size': 'lg' });
    this.selectedBusinessId = id;
  }

  /**
   * Managed modal
   * @param content {any} - content to be shown in popup
   * @param jobId {number} - job id to be used for fetching data and showing in popup
   * @param clientID {number} - job id to be used for fetching data and showing in popup
   * @param subcontent {any} - job id to be used for fetching data and showing in popup
   */
  public openVerticallyCenterJob(content: any, jobId, clientID, subcontent): void {
    if (jobId){
      this.selectedBusinessJob = {
        id: jobId
      };
      this.modalActiveClose = this._modalService.open(content, { centered: true, 'size': 'lg' });
    } else {
      this.selectedBusinessId = clientID;
      this.modalActiveClose = this._modalService.open(subcontent, { centered: true, 'size': 'lg' });
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
    this.getPendingInterview(this.search);
  }

  /**
   * Get pending interview
   * @return {Promise<void>}
   */
  public async getPendingInterview(search): Promise<void> {
    this.search = search;

    try {
      const response = await this._adminService.getPendingInterview(this.pager.currentPage, this.orderBy, this.orderSort, this.search);

      response.items.forEach((item) => {
        this.pendingInterviewList.push(item);
      });

      this.totalItems = response.pagination.total_count;
      this.pager = this._paginationService.getPager(this.totalItems, this.pager.currentPage);

      if (response.pagination.total_count === this.pendingInterviewList.length) {
        this.loadMoreCheck = false;
      }
      else if (response.pagination.total_count !== this.pendingInterviewList.length){
        this.loadMoreCheck = true;
      }
      this.paginationLoader = false;
      this.paginationFilter = false;

      this.totalCount = response.pagination.total_count;
      this.preloaderPage = false;
    }
    catch (err) {
      this._sharedService.showRequestErrors(err);
    }
  }

  /**
   * Set up interviews
   * @param interview {object}
   * @param listItems {Array}
   * @param status {string}
   * @return {Promise<void>}
   */
  public async adminPendingInterview(interview, listItems, status): Promise<void> {
    try {
      await this._adminService.adminPendingInterview(interview.id, status);
      const notificationMessage = (status) ? 'Applicant has been hired!' : 'Applicant has been declined!';
      if(status === true){
        this._sharedService.sidebarAdminBadges.interviewPending--;
        this._sharedService.sidebarAdminBadges.interviewPlaced++;
      }
      else{
        this._sharedService.sidebarAdminBadges.interviewPending--;
      }
      this._toastr.success(notificationMessage);
      const index = listItems.indexOf(interview);
      listItems.splice(index, 1);
    }
    catch (err) {
      this._sharedService.showRequestErrors(err);
    }
  }

  /**
   * Select change router
   * @param url
   */
  public routerApplicants(url): void {
    this._router.navigate([url]);
  }


}
