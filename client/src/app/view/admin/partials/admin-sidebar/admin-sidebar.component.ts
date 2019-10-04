import { AfterViewInit, Component, OnInit } from '@angular/core';
import { SharedService } from '../../../../services/shared.service';
import { AuthService } from '../../../../services/auth.service';
import { NavigationEnd, Router } from '@angular/router';

@Component({
  selector: 'app-admin-sidebar',
  templateUrl: './admin-sidebar.component.html',
  styleUrls: ['./admin-sidebar.component.scss']
})
export class AdminSidebarComponent implements OnInit, AfterViewInit {
  public checkRole: boolean;
  public currentUrl;

  public clientLinkStatus = true;
  public clientRoute = [
    '/admin/new_clients',
    '/admin/client_document',
    '/admin/all_clients'
  ];
  public jobLinkStatus = true;
  public jobRoute = [
    '/admin/new_jobs',
    '/admin/all_jobs'
  ];
  public candidateLinkStatus = true;
  public candidateRoute = [
    '/admin/new_candidates',
    '/admin/candidate_document',
    '/admin/candidate_video',
    '/admin/all_candidates'
  ];
  public applicantLinkStatus = true;
  public applicantRoute = [
    '/admin/all_applicants',
    '/admin/applications_awaiting',
    '/admin/applications_shortlist',
    '/admin/set_up_interview',
    '/admin/pending_interview',
    '/admin/successful_placed'
  ];

  constructor(
    public readonly sharedService: SharedService,
    private readonly _authService: AuthService,
    private readonly _router: Router
  ) {
    _router.events.subscribe((event) => {
      if(event instanceof NavigationEnd) {
        let url;
        if(event.url.indexOf('?') > 0){
          url = event.url.slice(0, event.url.indexOf('?'));
        }
        else{
          url = event.url;
        }

        /*if (this.clientRoute.indexOf(url) !== -1){
          this.clientLinkStatus = true;
        }
        else {
          this.clientLinkStatus = false;
        }
        if (this.jobRoute.indexOf(url) !== -1){
          this.jobLinkStatus = true;
        }
        else {
          this.jobLinkStatus = false;
        }

        if (this.candidateRoute.indexOf(url) !== -1){
          this.candidateLinkStatus = true;
        }
        else {
          this.candidateLinkStatus = false;
        }
        if (this.applicantRoute.indexOf(url) !== -1){
          this.applicantLinkStatus = true;
        }
        else {
          this.applicantLinkStatus = false;
        }*/
      }
    });
  }
  public intervalId;

  ngOnInit() {
    (localStorage.getItem('role') === 'ROLE_ADMIN') ? this.checkRole = false : this.checkRole = true;
    this.intervalId = setInterval(() => {
      this.sharedService.getAdminBadges();
    }, 30000);
  }

  ngAfterViewInit(){
    this.sharedService.getAdminBadges();
  }

  ngOnDestroy(){
    clearInterval(this.intervalId);
  }

  public openSidebar(): void {
    this.sharedService.checkSidebar = false;
  }

  public logout () {
    this._authService.logout();
    localStorage.removeItem('progressBar');
  }

}
