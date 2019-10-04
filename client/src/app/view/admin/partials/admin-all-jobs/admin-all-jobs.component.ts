import { Component, OnInit } from '@angular/core';
import { SharedService } from '../../../../services/shared.service';
import { ToastrService } from 'ngx-toastr';
import { AdminService } from '../../../../services/admin.service';
import { NgbModal } from '@ng-bootstrap/ng-bootstrap';
import { BusinessJobsAwaitingApproval, Role } from '../../../../../entities/models';
import { INgxMyDpOptions } from 'ngx-mydatepicker';
import { Angular5Csv } from 'angular5-csv/Angular5-csv';
import { PaginationService } from '../../../../services/pagination.service';
import { Router } from '@angular/router';
import { AuthService } from '../../../../services/auth.service';

@Component({
  selector: 'app-admin-all-jobs',
  templateUrl: './admin-all-jobs.component.html',
  styleUrls: ['./admin-all-jobs.component.scss']
})
export class AdminAllJobsComponent implements OnInit {

  public jobsAwaitingApprove = Array<BusinessJobsAwaitingApproval>();

  public selectedBusinessJobId: number;
  public modalActiveClose: any;

  public myOptionsDate: INgxMyDpOptions = { dateFormat: 'dd.mm.yyyy',
    dayLabels: {su: 'S', mo: 'M', tu: 'T', we: 'W', th: 'T', fr: 'F', sa: 'S'},
    monthLabels: {1: 'January', 2: 'February', 3: 'March', 4: 'April', 5: 'May', 6: 'June', 7: 'July', 8: 'August', 9: 'September', 10: 'October', 11: 'November', 12: 'December'}};
  public model: any = { date: { year: 2018, month: 10, day: 9 } };

  public preloaderPage = true;

  public paginationLoader = false;
  public pagination = 1;
  public loadMoreCheck = true;

  public selectedBusinessJob: any;
  public selectedBusinessJobArray: any;
  public selectedBusinessJobStatus: boolean;

  public orderBy: string = '';
  public orderSort: boolean;
  public paginationFilter = false;
  public search = '';
  public status = true;
  public dateStart = '';
  public dateEnd = '';

  public totalItems: number;
  public pager: any = {
    currentPage: 1
  };

  constructor(
    private readonly _modalService: NgbModal,
    private readonly _adminService: AdminService,
    private readonly _toastr: ToastrService,
    private readonly _paginationService: PaginationService,
    private readonly _router: Router,
    private readonly _authService: AuthService,
    private readonly _sharedService: SharedService
  ) {
    this._sharedService.checkSidebar = false;
  }

  ngOnInit() {
    window.scrollTo(0, 0);
    this.getAllJobs(this.search, this.status, this.dateStart, this.dateEnd, false).then(() => {
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

    this.getAllJobs(this.search, this.status, this.dateStart, this.dateEnd, false);
  }

  /**
   * Sort by table columns
   */
  public sortCandidate(column: string): void {
    this.resetArrayPagination();
    this.paginationFilter = true;

    this.orderBy = column;
    this.orderSort = !this.orderSort;

    this.getAllJobs(this.search, this.status, this.dateStart, this.dateEnd, false);
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
   * Get all jobs
   * @return {Promise<void>}
   */
  public async getAllJobs(search, status, dateStart, dateEnd, csv): Promise<void> {
    this.search = search;
    this.status = status;
    this.dateStart = dateStart;
    this.dateEnd = dateEnd;

    this.status = Boolean(this.status);
    const data = {
      search: this.search,
      status: String(this.status),
      dateStart: this.dateStart,
      dateEnd: this.dateEnd,
      page: this.pager.currentPage,
      orderBy: this.orderBy,
      orderSort: this.orderSort,
      csv: csv
    };

    if (dateStart && dateEnd && dateStart > dateEnd ) {
      this._toastr.error('Date End not be shorter than the Date Start');
    }
    else {
      try {
        const response = await this._adminService.getAllJobs(data);

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
   * Export CSV file
   * @return {Promise<void>}
   */
  public async exportDataCSV(search, status, dateStart, dateEnd): Promise<void>{

    const data = {
      search: this.search,
      status: String(this.status),
      dateStart: this.dateStart,
      dateEnd: this.dateEnd,
      page: this.pager.currentPage,
      orderBy: this.orderBy,
      orderSort: this.orderSort,
      csv: true
    };

    try {
      if (dateStart && dateEnd && dateStart > dateEnd ) {
        this._toastr.error('Date End not be shorter than the Date Start');
      }
      else {
        const response = await this._adminService.getAllJobs(data);

        const options = {
          showLabels: true,
          headers: ['Date', 'Contact', 'Email', 'Phone', 'Company', 'Job Title', 'Active']
        };

        new Angular5Csv(response, 'All jobs', options);
      }
    }
    catch (err) {
      this._sharedService.showRequestErrors(err);
    }
  }

  /**
   * changes status for hob specified with id
   * @param id {number} - id of the job
   * @param status {boolean} - status of the job - true - approve, false - decline
   */
  public async approveJob(id: number, status: boolean): Promise<void> {
    try {
      await this._adminService.changeJobsStatus(id, { approve: status });
      const notificationMessage = (status) ? 'Job has been approved!' : 'Job has been declined!';
      this._toastr.success(notificationMessage);
      this.jobsAwaitingApprove = this.jobsAwaitingApprove.filter((job) => job.id !== id);
    }
    catch (err) {
      this._sharedService.showRequestErrors(err);
    }
  }

  /**
   * Delete jobs
   * @param id {number}
   * @return {Promise<void>}
   */
  public async deleteJobs(id: number): Promise<void> {
    try {
      await this._adminService.deleteJobs(id);
      this.jobsAwaitingApprove = this.jobsAwaitingApprove.filter((listElement) => listElement.id !== id);
      this.modalActiveClose.dismiss();
      this._sharedService.sidebarAdminBadges.jobAll--;
      this._toastr.success('Job has been closed');
    }
    catch (err) {
      this._sharedService.showRequestErrors(err);
    }
  }

  /**
   * Update status job for admin
   * @param id {number}
   * @param status {boolean}
   * @return {Promise<void>}
   */
  public async updateJobStatus(id: number, status: boolean): Promise<void> {
    status = !status;
    try {
      await this._adminService.updateJobStatus(id, status);
      this.jobsAwaitingApprove = this.jobsAwaitingApprove.filter((listElement) => listElement.id !== id);
      this._toastr.success('Job status has been changed');
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
   * Managed modal
   * @param content {any} - content to be shown in popup
   */
  public openVerticallyCenter(content: any) {
    this.modalActiveClose = this._modalService.open(content, { centered: true, 'size': 'sm' });
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
