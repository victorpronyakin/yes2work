import { AfterViewInit, Component, OnInit } from '@angular/core';
import { SharedService } from '../../../../services/shared.service';
import { AuthService } from '../../../../services/auth.service';
import { NavigationEnd, Router } from '@angular/router';

@Component({
  selector: 'app-sidebar',
  templateUrl: './sidebar.component.html',
  styleUrls: ['./sidebar.component.scss']
})
export class SidebarComponent implements OnInit, AfterViewInit{
  public currentUrl;
  public activeLink;

  public applicantLinkStatus = false;
  public applicantRoute = [
    '/business/applicants',
    '/business/applicants_awaiting',
    '/business/applicants_shortlist',
    '/business/applicants_approved',
    '/business/applicants_declined'
  ];
  public jobLinkStatus = false;
  public jobRoute = [
    '/business/awaiting_job',
    '/business/approved_job',
    '/business/old_jobs',
    '/business/jobs/add'
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
        if (this.applicantRoute.indexOf(url) !== -1){
          this.applicantLinkStatus = true;
        }
        else {
          this.applicantLinkStatus = false;
        }
        if (this.jobRoute.indexOf(url) !== -1){
          this.jobLinkStatus = true;
        }
        else {
          this.jobLinkStatus = false;
        }
      }
    });
  }

  public intervalId;

  ngOnInit() {
    this.intervalId = setInterval(() => {
      this.sharedService.getBusinessBadges();
    }, 30000);
  }

  ngAfterViewInit(){
    this.sharedService.getBusinessBadges();
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
