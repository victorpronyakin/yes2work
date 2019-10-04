import { Component, OnInit } from '@angular/core';
import { AdminService } from '../../../../services/admin.service';
import { BusinessApprove } from '../../../../../entities/models-admin';
import { NgbModal } from '@ng-bootstrap/ng-bootstrap';
import { ToastrService } from 'ngx-toastr';
import { SharedService } from '../../../../services/shared.service';
import { PaginationService } from '../../../../services/pagination.service';

@Component({
  selector: 'app-admin-new-clients',
  templateUrl: './admin-new-clients.component.html',
  styleUrls: ['./admin-new-clients.component.scss']
})
export class AdminNewClientsComponent implements OnInit {

  public approveBusinessList = Array<BusinessApprove>();

  public modalActiveClose: any;

  public selectedBusinessId: number;
  public preloaderPage = true;

  public paginationLoader = false;
  public pagination = 1;
  public loadMoreCheck = true;

  public confirmFunction: string;
  public confirmData: any;
  public confirmStatus: any;
  public confirmArray: any;

  public orderBy: string = '';
  public search: string = '';
  public orderSort: boolean;
  public paginationFilter = false;

  public totalItems: number;
  public pager: any = {
    currentPage: 1
  };

  constructor(
    private readonly _adminService: AdminService,
    private readonly _modalService: NgbModal,
    private readonly _toastr: ToastrService,
    private readonly _paginationService: PaginationService,
    private readonly _sharedService: SharedService
  ) {
    this._sharedService.checkSidebar = false;
  }

  ngOnInit() {
    window.scrollTo(0, 0);
    this.getApproveBusiness(this.search).then(() => {
      this.pager = this._paginationService.getPager(this.totalItems, 1);
    });
  }

  /**
   * Set pagination page
   * @param {number} page
   */
  public setPage(page: number) {
    this.paginationLoader = true;
    this.approveBusinessList = [];
    this.pager = this._paginationService.getPager(this.totalItems, page);
    window.scrollTo(100, 0);

    this.getApproveBusiness(this.search);
  }

  /**
   * Sort by table columns
   */
  public sortCandidate(column: string): void {
    this.resetArrayPagination();
    this.paginationFilter = true;
    this.loadMoreCheck = false;

    this.orderBy = column;
    this.orderSort = !this.orderSort;

    this.getApproveBusiness(this.search);
  }

  /**
   * Reset pagination
   */
  public resetArrayPagination() {
    this.approveBusinessList = [];
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
   * Get approve business
   * @return {Promise<void>}
   */
  public async getApproveBusiness(search): Promise<void> {
    this.search = search;

    try {
      const response = await this._adminService.getApproveBusiness(this.pager.currentPage, this.orderBy, this.orderSort, this.search);

      response.items.forEach((item) => {
        this.approveBusinessList.push(item);
      });

      this.totalItems = response.pagination.total_count;
      this.pager = this._paginationService.getPager(this.totalItems, this.pager.currentPage);

      if (response.pagination.total_count === this.approveBusinessList.length) {
        this.loadMoreCheck = false;
      }
      else if (response.pagination.total_count !== this.approveBusinessList.length){
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
   * @param id {number} - job id to be used for fetching data and showing in popup
   */
  public openVerticallyCentered(content: any,  id: number) {
    this.modalActiveClose = this._modalService.open(content, { centered: true, 'size': 'lg' });
    this.selectedBusinessId = id;
  }
}
