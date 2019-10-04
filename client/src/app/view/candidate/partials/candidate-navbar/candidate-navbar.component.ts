import { AfterContentInit, Component, OnInit } from '@angular/core';
import { AuthService } from '../../../../services/auth.service';
import { CandidateService } from '../../../../services/candidate.service';
import { SharedService } from '../../../../services/shared.service';

@Component({
  selector: 'app-candidate-navbar',
  templateUrl: './candidate-navbar.component.html',
  styleUrls: ['./candidate-navbar.component.scss']
})
export class CandidateNavbarComponent implements OnInit, AfterContentInit {

  public checkSidebar = false;
  public admin: any;

  constructor(
    private readonly _authService: AuthService,
    private readonly _candidateService: CandidateService,
    public readonly sharedService: SharedService
  ) {
    this.admin = {
      access_token: localStorage.getItem('access_token_admin'),
      expires_in: localStorage.getItem('expires_in_admin'),
      refresh_token: localStorage.getItem('refresh_token_admin'),
      role: localStorage.getItem('role_admin'),
      id: localStorage.getItem('id_admin')
    };
  }

  ngOnInit() {
  }

  ngAfterContentInit() {
    if (!localStorage.getItem('progressBar')) {
      this._candidateService.getCandidateProfileDetails().then(data => {
        localStorage.setItem('progressBar', String(data.profile.percentage));
        this.sharedService.progressBar = Number(localStorage.getItem('progressBar'));
      });
    } else{
      this.sharedService.progressBar = Number(localStorage.getItem('progressBar'));
    }

  }

  public logout () {
    this._authService.logout();
    localStorage.removeItem('progressBar');
  }

  public openSidebar(): void {
    this.sharedService.checkSidebar = true;
  }

}
