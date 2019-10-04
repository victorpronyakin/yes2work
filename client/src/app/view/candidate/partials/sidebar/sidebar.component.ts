import { AfterViewInit, Component, ElementRef, OnInit, ViewChild } from '@angular/core';
import { SharedService } from '../../../../services/shared.service';
import { AuthService } from '../../../../services/auth.service';
import { NavigationEnd, Router } from '@angular/router';
import { CandidateService } from '../../../../services/candidate.service';

@Component({
  selector: 'app-sidebar',
  templateUrl: './sidebar.component.html',
  styleUrls: ['./sidebar.component.scss']
})
export class SidebarComponent implements OnInit, AfterViewInit {
  @ViewChild('accord') public accord: ElementRef;

  public currentUrl;

  public profileLinkStatus = false;
  public profileRoute = [
    '/candidate/profile_details',
    '/candidate/video',
    '/candidate/qualification',
    '/candidate/achievements',
    '/candidate/preferences'
  ];
  public jobLinkStatus = false;
  public jobRoute = [
    '/candidate/opportunities',
    '/candidate/job_alerts_new',
    '/candidate/job_alerts_declined',
    '/candidate/job_alerts_expired',
    '/candidate/applications',
    '/candidate/awaiting_approval',
    '/candidate/approved_applications',
    '/candidate/declined_applications',
    '/candidate/request_interviews'
  ];

  constructor(
    public readonly sharedService: SharedService,
    private readonly _authService: AuthService,
    private readonly _candidateService: CandidateService,
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
        if (this.profileRoute.indexOf(url) !== -1){
          this.profileLinkStatus = true;
        }
        else {
          this.profileLinkStatus = false;
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
      this.sharedService.getCandidateBadges();
    }, 30000);
  }

  ngAfterViewInit(){
    this.sharedService.getCandidateBadges().then(() => {
      this.getProfile();
    });
  }

  ngOnDestroy(){
    clearInterval(this.intervalId);
  }

  /**
   * Get candidate profile
   * @returns {Promise<void>}
   */
  public async getProfile(): Promise<void> {
    const response = await this._candidateService.getCandidateProfileDetails();

    if (!response.profile.video) {
      this.sharedService.visibleErrorVideoIcon = true;
    }

    if (response.profile.percentage < 50 || !response.profile.copyOfID || response.profile.copyOfID.length === 0) {
      this.sharedService.visibleErrorProfileIcon = true;
    }
  }

  /**
   * Open sidebar
   */
  public openSidebar(): void {
    this.sharedService.checkSidebar = false;
  }

  /**
   * Log out
   */
  public logout () {
    this._authService.logout();
    localStorage.removeItem('progressBar');
  }

}
