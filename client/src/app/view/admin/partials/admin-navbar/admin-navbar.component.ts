import { Component, OnInit } from '@angular/core';
import { AuthService } from '../../../../services/auth.service';
import { SharedService } from '../../../../services/shared.service';

@Component({
  selector: 'app-admin-navbar',
  templateUrl: './admin-navbar.component.html',
  styleUrls: ['./admin-navbar.component.scss']
})
export class AdminNavbarComponent implements OnInit {

  public checkRole: boolean;
  public checkSidebar = false;

  constructor(
    private readonly _authService: AuthService,
    public readonly sharedService: SharedService
  ) { }

  ngOnInit() {
    (localStorage.getItem('role') === 'ROLE_ADMIN') ? this.checkRole = false : this.checkRole = true;
  }

  public logout () {
    this._authService.logout();
    localStorage.removeItem('progressBar');
  }

  public openSidebar(): void {
    this.sharedService.checkSidebar = true;
  }

}
