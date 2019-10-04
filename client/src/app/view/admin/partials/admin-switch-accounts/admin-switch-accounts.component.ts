import { Component, OnInit } from '@angular/core';
import { AdminService } from '../../../../services/admin.service';
import { SharedService } from '../../../../services/shared.service';
import { PaginationService } from '../../../../services/pagination.service';
import { Role } from '../../../../../entities/models';
import { Router } from '@angular/router';
import { AuthService } from '../../../../services/auth.service';

@Component({
  selector: 'app-admin-switch-accounts',
  templateUrl: './admin-switch-accounts.component.html',
  styleUrls: ['./admin-switch-accounts.component.scss']
})
export class AdminSwitchAccountsComponent implements OnInit {

  public users = [];

  public search: string = '';
  public orderBy: string = '';
  public orderSort: boolean;
  public paginationFilter = false;
  public preloaderPage = true;

  public paginationLoader = false;
  public pagination = 1;
  public loadMoreCheck = true;

  public totalItems: number;
  public pager: any = {
    currentPage: 1
  };

  constructor(
    private readonly _adminService: AdminService,
    private readonly _sharedService: SharedService,
    private readonly _router: Router,
    private readonly _authService: AuthService,
    private readonly _paginationService: PaginationService,
  ) { }

  ngOnInit() {
    window.scrollTo(0, 0);
    this.users = [];
    this.getSwitchAccountUsers(this.search).then(() => {
      this.pager = this._paginationService.getPager(this.totalItems, 1);
    });
  }

  public async getSwitchAccountUsers(search): Promise<any> {
    this.search = search;
    try {
      const response = await this._adminService.getAdminSwitchAccounts(this.search, this.pager.currentPage, this.orderBy, this.orderSort);

      response.items.forEach((item) => {
        this.users.push(item);
      });

      this.totalItems = response.pagination.total_count;
      this.pager = this._paginationService.getPager(this.totalItems, this.pager.currentPage);

      if (response.pagination.total_count === this.users.length) {
        this.loadMoreCheck = false;
      }
      else if (response.pagination.total_count !== this.users.length){
        this.loadMoreCheck = true;
      }
      this.paginationLoader = false;
      this.paginationFilter = false;
      this.preloaderPage = false;
    } catch (err) {
      this._sharedService.showRequestErrors(err);
    }
  }

  /**
   * Set pagination page
   * @param {number} page
   */
  public setPage(page: number) {
    this.paginationLoader = true;
    this.users = [];
    this.pager = this._paginationService.getPager(this.totalItems, page);
    window.scrollTo(100, 0);

    this.getSwitchAccountUsers(this.search);
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

    this.getSwitchAccountUsers(this.search);
  }

  /**
   * Reset pagination
   */
  public resetArrayPagination() {
    this.users = [];
    this.pager.currentPage = 1;
  }

  /**
   * Reset sorting
   */
  public resetSorting() {
    this.orderBy = null;
    this.orderSort = null;
  }

  public async switchToAccount(user): Promise<any> {
    try {
      const account = await this._adminService.getUserTokenForAdmin(user.id);

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
