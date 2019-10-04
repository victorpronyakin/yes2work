import { Component, OnInit } from '@angular/core';
import { AdminService } from '../../../../services/admin.service';
import { SharedService } from '../../../../services/shared.service';
import { ToastrService } from 'ngx-toastr';
import { NgbModal } from '@ng-bootstrap/ng-bootstrap';
import { EditAdmin } from '../../../../../entities/models-admin';
import { Router } from '@angular/router';
import { PaginationService } from '../../../../services/pagination.service';

@Component({
  selector: 'app-admin-manage-system-users',
  templateUrl: './admin-manage-system-users.component.html',
  styleUrls: ['./admin-manage-system-users.component.scss']
})
export class AdminManageSystemUsersComponent implements OnInit {

  public adminsList = Array<EditAdmin>();
  public selectedAdmin = new EditAdmin({});
  public modalActiveClose: any;
  public preloaderPage = true;

  public paginationLoader = false;
  public pagination = 1;
  public loadMoreCheck = false;

  public allowVideo = false;

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
    private readonly _sharedService: SharedService,
    private readonly _toastr: ToastrService,
    private readonly _modalService: NgbModal,
    private readonly _paginationService: PaginationService,
    private readonly _router: Router
  ) {
    this._sharedService.checkSidebar = false;
  }

  ngOnInit() {
    window.scrollTo(0, 0);
    if (localStorage.getItem('role') === 'ROLE_ADMIN'){
      this._router.navigate(['/admin/dashboard']);
    }
    else {
      this.getAllAdmins(this.search).then(() => {
        this.pager = this._paginationService.getPager(this.totalItems, 1);
        this.getAdminVideoStatusCandidate();
      });
    }
  }

  /**
   * Set pagination page
   * @param {number} page
   */
  public setPage(page: number) {
    this.paginationLoader = true;
    this.adminsList = [];
    this.pager = this._paginationService.getPager(this.totalItems, page);
    window.scrollTo(100, 0);

    this.getAllAdmins(this.search);
  }

  /**
   * Sort by table columns
   */
  public sortCandidate(column: string): void {
    this.resetArrayPagination();
    this.paginationFilter = true;

    this.orderBy = column;
    this.orderSort = !this.orderSort;

    this.getAllAdmins(this.search);
  }

  /**
   * Reset sorting
   */
  public resetSorting() {
    this.orderBy = null;
    this.orderSort = null;
  }

  /**
   * Get candidate video status for admin
   * @returns {Promise<void>}
   */
  public async getAdminVideoStatusCandidate(): Promise<void> {
    try {
      const response = await this._adminService.getAdminVideoStatusCandidate();
      this.allowVideo = response.allowVideo;
    }
    catch (err) {
      this._sharedService.showRequestErrors(err);
    }
  }

  /**
   * Update candidate video status for admin
   * @returns {Promise<void>}
   */
  public async updateAdminVideoStatusCandidate(field, value): Promise<void> {
    value = !value;
    const data = {[field]: value};

    try {
      await this._adminService.updateAdminVideoStatusCandidate(data);
    }
    catch (err) {
      this._sharedService.showRequestErrors(err);
    }
  }

  /**
   * Reset Array
   */
  public resetArrayPagination(): void{
    this.adminsList = [];
    this.pager.currentPage = 1;
  }

  /**
   * Get all admins
   * @param search
   * @return {Promise<void>}
   */
  public async getAllAdmins(search: string): Promise<void> {
    this.search = search;

    try {
      const response = await this._adminService.getAllAdmins(this.search, this.pager.currentPage, this.orderBy, this.orderSort);
      response.items.forEach(item => {
        this.adminsList.push(item);
      });

      this.totalItems = response.pagination.total_count;
      this.pager = this._paginationService.getPager(this.totalItems, this.pager.currentPage);

      this.preloaderPage = false;
      this.paginationFilter = false;
      this.paginationLoader = false;
    }
    catch (err) {
      this._sharedService.showRequestErrors(err);
    }
  }

  /**
   * Delete admin
   * @param admin {object}
   * @param adminList {Array}
   * @return {Promise<void>}
   */
  public async deleteAdmin(admin, adminList): Promise<void> {
    try {
      await this._adminService.deleteAdmin(admin.id);
      this._toastr.success('Admin has been deleted');
      const index = adminList.indexOf(admin);
      adminList.splice(index, 1);
    }
    catch (err) {
      this._sharedService.showRequestErrors(err);
    }
  }

  /**
   * Managed modal
   * @param content {any}
   * @param admin {object}
   */
  public openVerticallyCentered(content: any, admin): void {
    this.modalActiveClose = this._modalService.open(content, { centered: true, 'size': 'lg' });
    this.selectedAdmin = admin;
  }

  /**
   * Managed modal
   * @param content {any}
   */
  public openVerticallyCenteredCreated(content: any): void {
    this.modalActiveClose = this._modalService.open(content, { centered: true, 'size': 'lg' });
  }
}
