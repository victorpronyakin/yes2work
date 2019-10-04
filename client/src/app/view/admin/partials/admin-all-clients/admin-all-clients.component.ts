import { Component, ElementRef, OnInit, ViewChild } from '@angular/core';
import { BusinessApprove } from '../../../../../entities/models-admin';
import { AdminService } from '../../../../services/admin.service';
import { SharedService } from '../../../../services/shared.service';
import { ToastrService } from 'ngx-toastr';
import { NgbModal } from '@ng-bootstrap/ng-bootstrap';
import { Angular5Csv } from 'angular5-csv/Angular5-csv';
import { PaginationService } from '../../../../services/pagination.service';

@Component({
  selector: 'app-admin-all-clients',
  templateUrl: './admin-all-clients.component.html',
  styleUrls: ['./admin-all-clients.component.scss']
})
export class AdminAllClientsComponent implements OnInit {

  @ViewChild('content') private content : ElementRef;

  public businessList = Array<BusinessApprove>();
  public modalActiveClose: any;
  public selectedBusinessId: number;
  public search = '';
  public preloaderPage = true;

  public paginationLoader = false;
  public pagination = 1;
  public loadMoreCheck = true;
  public deleteCheck = false;

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
    private readonly _toastr: ToastrService,
    private readonly _paginationService: PaginationService,
    private readonly _modalService: NgbModal
  ) {
    this._sharedService.checkSidebar = false;
  }

  ngOnInit() {
    window.scrollTo(0, 0);
    this.getAllBusinessList(this.search, false).then(() => {
      this.pager = this._paginationService.getPager(this.totalItems, 1);
    });
  }

  /**
   * Set pagination page
   * @param {number} page
   */
  public setPage(page: number) {
    this.paginationLoader = true;
    this.businessList = [];
    this.pager = this._paginationService.getPager(this.totalItems, page);
    window.scrollTo(100, 0);

    this.getAllBusinessList(this.search, false);
  }

  /**
   * Sort by table columns
   */
  public sortCandidate(column: string): void {
    this.resetArrayPagination();
    this.paginationFilter = true;

    this.orderBy = column;
    this.orderSort = !this.orderSort;

    this.getAllBusinessList(this.search, false);
  }

  /**
   * Export CSV file
   * @return {Promise<void>}
   */
  public async exportDataCSV(search, csv): Promise<void>{

    const params = {
      search: this.search,
      orderBy: this.orderBy,
      orderSort: this.orderSort,
      page: this.pager.currentPage,
      csv: csv
    };

    try {
      const response = await this._adminService.getAllBusinessList(params);

      const options = {
        showLabels: true,
        headers: ['Name', 'Company', 'Email' , 'Tel Number', 'Referring Agent', 'Active']
      };

      new Angular5Csv(response, 'All clients', options);
    }
    catch (err) {
      this._sharedService.showRequestErrors(err);
    }
  }

  /**
   * Reset Array
   */
  public resetArrayPagination(): void{
    this.businessList = [];
    this.pager.currentPage = 1;
  }

  /**
   * Default sorting value
   */
  public defaultSorting() {
    this.orderBy = null;
    this.orderSort = null;
  }

  /**
   * Get all business profiles
   * @param search {string}
   * @param csv {boolean}
   * @return {Promise<void>}
   */
  public async getAllBusinessList(search: string, csv): Promise<void> {
    this.search = search;

    const params = {
      search: this.search,
      orderBy: this.orderBy,
      orderSort: this.orderSort,
      page: this.pager.currentPage,
      csv: csv
    };

    try {
      const response = await this._adminService.getAllBusinessList(params);

      response.items.forEach((item) => {
        this.businessList.push(item);
      });

      this.totalItems = response.pagination.total_count;
      this.pager = this._paginationService.getPager(this.totalItems, this.pager.currentPage);

      if (response.pagination.total_count === this.businessList.length) {
        this.loadMoreCheck = false;
      }
      else if (response.pagination.total_count !== this.businessList.length){
        this.loadMoreCheck = true;
      }

      this.preloaderPage = false;
      this.paginationFilter = false;
      this.paginationLoader = false;
    }
    catch (err) {
      this._sharedService.showRequestErrors(err);
    }
  }

  /**
   * Delete business profile
   * @param id {number}
   * @return {Promise<void>}
   */
  public async deleteBusinessProfile(id): Promise<void> {
    try {
      await this._adminService.deleteBusinessProfile(id);

      this.businessList = this.businessList.filter((listElement) => listElement.id !== id);
      this.modalActiveClose.dismiss();
      this._sharedService.sidebarAdminBadges.clientAll--;
      this._toastr.success('Client has been deleted');
    }
    catch (err) {
      this._sharedService.showRequestErrors(err);
    }
  }

  /**
   * Update business status
   * @param id {number}
   * @param enabled {boolean}
   * @return {Promise<void>}
   */
  public async updateBusinessStatus(id: number, enabled: boolean): Promise<void> {
    enabled = !enabled;
    try {
      await this._adminService.updateBusinessStatus(id, enabled);
      this._toastr.success('Client status has been updated');
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

  /**
   * Managed modal
   * @param content {any} - content to be shown in popup
   */
  public openVerticallyCenter(content: any) {
    this.modalActiveClose = this._modalService.open(content, { centered: true, 'size': 'sm' });
  }

}
