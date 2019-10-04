import { Component, OnInit } from '@angular/core';
import { BusinessJobsAwaitingApproval, Role } from '../../../../../entities/models';
import { NgbModal } from '@ng-bootstrap/ng-bootstrap';
import { AdminService } from '../../../../services/admin.service';
import { SharedService } from '../../../../services/shared.service';
import { PaginationService } from '../../../../services/pagination.service';
import { Router } from '@angular/router';
import { AuthService } from '../../../../services/auth.service';

@Component({
  selector: 'app-admin-new-jobs',
  templateUrl: './admin-new-jobs.component.html',
  styleUrls: ['./admin-new-jobs.component.scss']
})
export class AdminNewJobsComponent implements OnInit {

  public jobsAwaitingApprove = Array<BusinessJobsAwaitingApproval>();

  public selectedBusinessJobId: number;
  public modalActiveClose: any;
  public preloaderPage = true;

  public paginationLoader = false;
  public pagination = 1;
  public loadMoreCheck = true;

  public confirmFunction: string;
  public confirmData: any;
  public confirmStatus: any;
  public confirmArray: any;

  public selectedBusinessJob: any;
  public selectedBusinessJobArray: any;
  public selectedBusinessJobStatus: boolean;

  public orderBy: string = '';
  public search: string = '';
  public orderSort: boolean;
  public paginationFilter = false;

  public totalItems: number;
  public pager: any = {
    currentPage: 1
  };

  constructor(
    private readonly _modalService: NgbModal,
    private readonly _adminService: AdminService,
    private readonly _paginationService: PaginationService,
    private readonly _router: Router,
    private readonly _authService: AuthService,
    private readonly _sharedService: SharedService
  ) {
    this._sharedService.checkSidebar = false;
  }

  ngOnInit() {
    window.scrollTo(0, 0);
    this.getAllJobsAwaitingApprove(this.search).then(() => {
      this.pager = this._paginationService.getPager(this.totalItems, 1);
    });
  }

  /**
   * Set pagination page
   * @param {number} page
   */
  public setPage(page: number) {
    this.paginationLoader = true;
    this.jobsAwaitingApprove = [];
    this.pager = this._paginationService.getPager(this.totalItems, page);
    window.scrollTo(100, 0);

    this.getAllJobsAwaitingApprove(this.search);
  }

  /**
   * Sort by table columns
   */
  public sortCandidate(column: string): void {
    this.resetArrayPagination();
    this.paginationFilter = true;

    this.orderBy = column;
    this.orderSort = !this.orderSort;

    this.getAllJobsAwaitingApprove(this.search);
  }

  /**
   * Reset Array
   */
  public resetArrayPagination(): void{
    this.jobsAwaitingApprove = [];
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
   * Managed modal
   * @param content {any} - content to be shown in popup
   * @param job {object} - job id to be used for fetching data and showing in popup
   * @param data {array} - job id to be used for fetching data and showing in popup
   * @param status {boolean} - job id to be used for fetching data and showing in popup
   */
  public openVerticallyCenterJob(content: any,  job, data, status): void {
    this.modalActiveClose = this._modalService.open(content, { centered: true, 'size': 'lg', windowClass: 'jobs-popups' });
    this.selectedBusinessJob = job;
    this.selectedBusinessJobArray = data;
    this.selectedBusinessJobStatus = status;
  }

  /**
   * gets the list of all jobs awaiting approve
   */
  public async getAllJobsAwaitingApprove(search): Promise<void> {
    this.search = search;

    try {
      const response = await this._adminService.getAdminJobsApproved(this.pager.currentPage, this.orderBy, this.orderSort, this.search);

      response.items.forEach((item) => {
        this.jobsAwaitingApprove.push(item);
      });

      this.totalItems = response.pagination.total_count;
      this.pager = this._paginationService.getPager(this.totalItems, this.pager.currentPage);

      if (response.pagination.total_count === this.jobsAwaitingApprove.length) {
        this.loadMoreCheck = false;
      }
      else if (response.pagination.total_count !== this.jobsAwaitingApprove.length){
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
   * Managed modal
   * @param content {any} - content to be shown in popup
   * @param jobId {number} - job id to be used for fetching data and showing in popup
   */
  public openVerticallyCentered(content: any,  jobId: number) {
    this.modalActiveClose = this._modalService.open(content, { centered: true, 'size': 'lg' });
    this.selectedBusinessJobId = jobId;
  }

  /**
   * Switch to client account
   * @param job {object}
   * @returns {Promise<any>}
   */
  public async switchToAccount(job): Promise<any> {
    try {
      const account = await this._adminService.getUserTokenForAdmin(job.clientID);

      const admin = {
        access_token: localStorage.getItem('access_token'),
        expires_in: localStorage.getItem('expires_in'),
        refresh_token: localStorage.getItem('refresh_token'),
        role: localStorage.getItem('role'),
        id: localStorage.getItem('id')
      };

      localStorage.setItem('access_token_admin', admin.access_token);
      localStorage.setItem('expires_in_admin', admin.expires_in);
      localStorage.setItem('refresh_token_admin', admin.refresh_token);
      localStorage.setItem('role_admin', admin.role);
      localStorage.setItem('id_admin', admin.id);

      const date = Math.round(Number(new Date().getTime() / 1000 + account.expires_in));
      localStorage.setItem('access_token', account.access_token);
      localStorage.setItem('expires_in', date.toString());
      localStorage.setItem('refresh_token', account.refresh_token);
      localStorage.setItem('role', account.role);
      localStorage.setItem('id', account.id);

      switch (account.role) {
        case Role.clientRole:
          this._router.navigate(['/business']);
          break;
        case Role.candidateRole:
          this._router.navigate(['/candidate']);
          break;
        case Role.adminRole:
          this._router.navigate(['/admin']);
          break;
        case Role.superAdminRole:
          this._router.navigate(['/admin']);
          break;
        default:
          this._authService.logout();
      }
    } catch (err) {
      this._sharedService.showRequestErrors(err);
    }
  }

}
