import { Component, OnInit } from '@angular/core';
import { AdminService } from '../../../../services/admin.service';
import { ToastrService } from 'ngx-toastr';
import { SharedService } from '../../../../services/shared.service';
import { AdminInterviewList} from '../../../../../entities/models-admin';
import { Router } from '@angular/router';
import { NgbModal } from '@ng-bootstrap/ng-bootstrap';
import { PaginationService } from '../../../../services/pagination.service';

@Component({
  selector: 'app-admin-set-up-interview',
  templateUrl: './admin-set-up-interview.component.html',
  styleUrls: ['./admin-set-up-interview.component.scss']
})
export class AdminSetUpInterviewComponent implements OnInit {

  public setUpInterviewListCandidate = Array<AdminInterviewList>();
  public setUpInterviewListClient = Array<AdminInterviewList>();

  public preloaderPage = true;
  public totalCount: number;

  public paginationLoaderCandidate = false;
  public paginationLoaderClient = false;
  public paginationCandidate = 1;
  public paginationClient = 1;
  public loadMoreCheckCandidate = true;
  public loadMoreCheckClient = true;

  public interviewCheck = false;
  public modalActiveClose: any;
  public selectedBusinessId;
  public selectedBusinessJob;


  public orderByCandidate: string = '';
  public searchCandidate: string = '';
  public orderSortCandidate: boolean;
  public paginationFilterCandidate = false;

  public orderByClient: string = '';
  public searchClient: string = '';
  public orderSortClient: boolean;
  public paginationFilterClient = false;

  public totalItemsCandidate: number;
  public totalItemsClient: number;

  public pagerCandidate: any = {
    currentPage: 1
  };
  public pagerClient: any = {
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
    this.setUpInterviewListCandidate = [];
    this.setUpInterviewListClient = [];
    this.getSetUpInterviewsCandidate(this.searchCandidate).then(() => {
      this.pagerCandidate = this._paginationService.getPager(this.totalItemsCandidate, 1);
      this.getSetUpInterviewsClient(this.searchClient).then(() => {
        this.pagerClient = this._paginationService.getPager(this.totalItemsClient, 1);
      });
    });
  }

  /**
   * Set pagination page
   * @param {number} page
   */
  public setPageCandidate(page: number) {
    this.paginationLoaderCandidate = true;
    this.setUpInterviewListCandidate = [];
    this.pagerCandidate = this._paginationService.getPager(this.totalItemsCandidate, page);
    window.scrollTo(100, 0);

    this.getSetUpInterviewsCandidate(this.searchCandidate);
  }

  /**
   * Set pagination page
   * @param {number} page
   */
  public setPageClient(page: number) {
    this.paginationLoaderClient = true;
    this.setUpInterviewListClient = [];
    this.pagerClient = this._paginationService.getPager(this.totalItemsClient, page);
    window.scrollTo(100, 0);

    this.getSetUpInterviewsClient(this.searchClient);
  }

  /**
   * Sort by table columns
   */
  public sortCandidate(column: string): void {
    this.resetArrayPaginationCandidate();
    this.paginationFilterCandidate = true;

    this.orderByCandidate = column;
    this.orderSortCandidate = !this.orderSortCandidate;

    this.getSetUpInterviewsCandidate(this.searchCandidate);
  }

  /**
   * Sort by table columns
   */
  public sortClient(column: string): void {
    this.resetArrayPaginationClient();
    this.paginationFilterClient = true;

    this.orderByClient = column;
    this.orderSortClient = !this.orderSortClient;

    this.getSetUpInterviewsClient(this.searchClient);
  }

  /**
   * Reset Array
   */
  public resetArrayPaginationCandidate(): void{
    this.setUpInterviewListCandidate = [];
    this.pagerCandidate.currentPage = 1;
  }

  /**
   * Reset Array
   */
  public resetArrayPaginationClient(): void{
    this.setUpInterviewListClient = [];
    this.pagerClient.currentPage = 1;
  }

  /**
   * Reset sorting
   */
  public resetSortingCandidate() {
    this.orderByCandidate = null;
    this.orderSortCandidate = null;
  }

  /**
   * Reset sorting
   */
  public resetSortingClient() {
    this.orderByClient = null;
    this.orderSortClient = null;
  }

  /**
   * Get set up interview
   * @return {Promise<void>}
   */
  public async getSetUpInterviewsCandidate(search): Promise<void> {
    this.searchCandidate = search;
    try {
      const response = await this._adminService.getSetUpInterviewsCandidate(this.pagerCandidate.currentPage, this.orderByCandidate, this.orderSortCandidate, this.searchCandidate);
      response.items.forEach((item) => {
        item.enabled = false;
        this.setUpInterviewListCandidate.push(item);
      });

      this.totalItemsCandidate = response.pagination.total_count;
      this.pagerCandidate = this._paginationService.getPager(this.totalItemsCandidate, this.pagerCandidate.currentPage);

      if (response.pagination.total_count === this.setUpInterviewListCandidate.length) {
        this.loadMoreCheckCandidate = false;
      }
      else if (response.pagination.total_count !== this.setUpInterviewListCandidate.length){
        this.loadMoreCheckCandidate = true;
      }
      this.paginationLoaderCandidate = false;
      this.paginationFilterCandidate = false;

      // this.totalCount = response.pagination.total_count;
    }
    catch (err) {
      this._sharedService.showRequestErrors(err);
    }
  }

  /**
   * Get set up interview
   * @return {Promise<void>}
   */
  public async getSetUpInterviewsClient(search): Promise<void> {
    this.searchClient = search;
    try {
      const response = await this._adminService.getSetUpInterviewsClient(this.pagerClient.currentPage, this.orderByClient, this.orderSortClient, this.searchClient);
      response.items.forEach((item) => {
        item.enabled = false;
        this.setUpInterviewListClient.push(item);
      });

      this.totalItemsClient = response.pagination.total_count;
      this.pagerClient = this._paginationService.getPager(this.totalItemsClient, this.pagerClient.currentPage);

      if (response.pagination.total_count === this.setUpInterviewListClient.length) {
        this.loadMoreCheckClient = false;
      }
      else if (response.pagination.total_count !== this.setUpInterviewListClient.length){
        this.loadMoreCheckClient = true;
      }
      this.paginationLoaderClient = false;
      this.paginationFilterClient = false;

      // this.totalCount = response.pagination.total_count;
      this.preloaderPage = false;
    }
    catch (err) {
      this._sharedService.showRequestErrors(err);
    }
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
