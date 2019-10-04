import { Component, OnInit } from '@angular/core';
import { INgxMyDpOptions } from 'ngx-mydatepicker';
import { AdminService } from '../../../../services/admin.service';
import { AdminLogging } from '../../../../../entities/models-admin';
import { ToastrService } from 'ngx-toastr';
import { SharedService } from '../../../../services/shared.service';
import { PaginationService } from '../../../../services/pagination.service';

@Component({
  selector: 'app-admin-activity',
  templateUrl: './admin-activity.component.html',
  styleUrls: ['./admin-activity.component.scss']
})
export class AdminActivityComponent implements OnInit {

  public myOptionsDate: INgxMyDpOptions = { dateFormat: 'mm.dd.yyyy' };
  public model: any = { date: { year: 2018, month: 10, day: 9 } };

  public preloaderPage = true;
  public loggingList = Array<AdminLogging>();

  public paginationLoader = false;
  public pagination = 1;
  public loadMoreCheck = true;

  public search: string = '';
  public startDay: string = '';
  public endDay: string = '';
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
    private readonly _paginationService: PaginationService,
    private readonly _sharedService: SharedService
  ) {
    this._sharedService.checkSidebar = false;
  }

  ngOnInit() {
    window.scrollTo(0, 0);
    this.getAdminLogging('', '', '', this.pagination).then(() => {
      this.pager = this._paginationService.getPager(this.totalItems, 1);
    });
  }

  /**
   * Set pagination page
   * @param {number} page
   */
  public setPage(page: number) {
    this.paginationLoader = true;
    this.loggingList = [];
    this.pager = this._paginationService.getPager(this.totalItems, page);
    window.scrollTo(100, 0);

    this.getAdminLogging(this.search, this.startDay, this.endDay, page);
  }

  /**
   * Sort by table columns
   */
  public sortCandidate(column: string): void {
    this.resetCurrentPage();
    this.loggingList = [];
    this.paginationFilter = true;

    this.orderBy = column;
    this.orderSort = !this.orderSort;

    this.getAdminLogging(this.search, this.startDay, this.endDay, this.pagination);
  }

  /**
   * Reset sorting
   */
  public resetSorting() {
    this.orderBy = null;
    this.orderSort = null;
  }

  /**
   * Reset Array
   */
  public resetCurrentPage(): void{
    this.loggingList = [];
    this.pager.currentPage = 1;
  }

  /**
   * Get admin loggings
   * @param search
   * @param startD
   * @param endD
   * @param page
   * @return {Promise<void>}
   */
  public async getAdminLogging(search, startD, endD, page): Promise<void> {
    const startDate = new Date(startD);
    const endDate = new Date(endD);
    if (startDate > endDate) {
      this._toastr.error('Date End not be shorter than the Date Start');
    }
    else{
      try {
        const start = (startD !== '') ? (startDate.getMonth() + 1) + '.' + startDate.getDate() + '.' + startDate.getFullYear() : startD;
        const end = (endD !== '') ? (endDate.getMonth() + 1) + '.' + endDate.getDate() + '.' + endDate.getFullYear() : endD;
        const response = await this._adminService.getAdminLogging(search, start, end, this.pager.currentPage, this.orderBy, this.orderSort);

        this.search = search;
        this.startDay = start;
        this.endDay = end;

        response.items.forEach((item) => {
          this.loggingList.push(item);
        });

        this.totalItems = response.pagination.total_count;
        this.pager = this._paginationService.getPager(this.totalItems, this.pager.currentPage);

        if (response.pagination.total_count === this.loggingList.length) {
          this.loadMoreCheck = false;
        }
        else if (response.pagination.total_count !== this.loggingList.length) {
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

}
