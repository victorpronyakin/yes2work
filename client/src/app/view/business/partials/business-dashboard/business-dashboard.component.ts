import { Component, OnInit } from '@angular/core';
import { BusinessService } from '../../../../services/business.service';
import { BusinessDashboard } from '../../../../../entities/models';
import { SharedService } from '../../../../services/shared.service';
import { CookieService } from 'ngx-cookie-service';
import { NgbModal } from '@ng-bootstrap/ng-bootstrap';
import { Router } from '@angular/router';

@Component({
  selector: 'app-business-dashboard',
  templateUrl: './business-dashboard.component.html',
  styleUrls: ['./business-dashboard.component.scss']
})
export class BusinessDashboardComponent implements OnInit {

  public businessDashboardProfile: BusinessDashboard;
  public businessFirstName: string;
  public sumJobs: number;
  public sumAwaiting: number;
  public sumShoerList: number;
  public sumApproved: number;
  public jobs = [];

  public preloaderPage = true;
  public checkStatus = false;
  public modalActiveClose;
  public currentlyOpenedBusinessJobId: number;

  constructor(
    private readonly _businessService: BusinessService,
    public readonly _sharedService: SharedService,
    public readonly _cookieService: CookieService,
    private readonly _modalService: NgbModal,
    private readonly _router: Router
  ) {
    this._sharedService.checkSidebar = false;
  }

  ngOnInit() {
    window.scrollTo(0, 0);
    this.setStatusFirstNameUser().then(() => {
      this.getBusinessProfileDetails().then(() => {
        this.getBusinessDashboard();
      });
    });
  }

  /**
   * Get details profile business
   * @return {Promise<void>}
   */
  public async getBusinessProfileDetails(): Promise<void> {
    const data = await this._businessService.getBusinessProfile();
    if(!data.profile.user.firstName ||
      !data.profile.user.lastName ||
      !data.profile.user.jobTitle ||
      !data.profile.user.phone ||
      !data.profile.user.email ||
      !data.profile.company.name ||
      !data.profile.company.address ||
      !data.profile.company.addressCountry ||
      !data.profile.company.addressState ||
      !data.profile.company.addressZipCode ||
      !data.profile.company.addressCity ||
      !data.profile.company.addressStreet ||
      !data.profile.company.addressStreetNumber ||
      !data.profile.company.addressBuildName ||
      !data.profile.company.addressUnit ||
      !data.profile.company.industry ||
      !data.profile.company.companySize ||
      !data.profile.company.description) {
      const session = sessionStorage.getItem('session' + data.profile.user.id);
      if (!session) {
        const session1 = sessionStorage.setItem('session' + data.profile.user.id, 'true');
        this._router.navigate(['/business/my_account']);
      }
    }
  }

  // public async getBusinessProfileDetails(): Promise<void> {
  //   const data = await this._businessService.getBusinessProfile();
  //   if(!data.profile.user.firstName ||
  //     !data.profile.user.lastName ||
  //     !data.profile.user.jobTitle ||
  //     !data.profile.user.phone ||
  //     !data.profile.user.email ||
  //     !data.profile.company.name ||
  //     !data.profile.company.address) {
  //     const session = sessionStorage.getItem('session' + data.profile.user.id);
  //     if(!session) {
  //       const session = sessionStorage.setItem('session' + data.profile.user.id, 'true');
  //       this._router.navigate(['/business/my_account']);
  //     }
  //   }
  //
  // }

  /**
   *
   * @return {Promise<void>}
   */
  public async setStatusFirstNameUser(): Promise<void> {
    const id = Number(localStorage.getItem('id'));
    const data = this._cookieService.get('firstLoginBusiness_' + id);
    if (Boolean(data) === false) {
      this._cookieService.set('firstLoginBusiness_' + id, 'true', 365, '/');
    }
    else {
      this.checkStatus = true;
      //this._sharedService.checkStatusPopup = true;
    }
  }

  /**
   * Get business dashboard
   * @return {Promise<void>}
   */
  public async getBusinessDashboard(): Promise<void> {
    this.businessDashboardProfile = await this._businessService.getBusinessDashboard();
    this.jobs = this.businessDashboardProfile.jobs;
    this.businessFirstName = this.businessDashboardProfile.firstName;
    this.sumJobs = this.businessDashboardProfile.totalJobs;
    this.sumAwaiting = this.businessDashboardProfile.stats.awaiting;
    this.sumShoerList = this.businessDashboardProfile.stats.shortlist;
    this.sumApproved = this.businessDashboardProfile.stats.approved;
    this.preloaderPage = false;
  }

  /**
   * opens popup
   * @param content - content to be placed within
   * @param jobId - job id to show within popup
   */
  public openVerticallyCentered(content: any, jobId: number) {

    this.currentlyOpenedBusinessJobId = jobId;
    this.modalActiveClose = this._modalService.open(content, { centered: true, 'size': 'lg' });
  }

  /**
   * Delete job from children popup
   * @param job
   */
  public deleteJob(job) {
    this.jobs = this.jobs.filter((businessJob) => businessJob.id !== job.id);
  }

  /**
   * Delete job from children popup
   * @param job
   */
  public closeJob(job) {
    this.jobs = this.jobs.filter((businessJob) => businessJob.id !== job.id);
  }

}
