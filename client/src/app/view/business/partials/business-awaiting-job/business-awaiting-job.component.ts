import { Component, OnInit } from '@angular/core';
import { BusinessListJob } from '../../../../../entities/models';
import { ToastrService } from 'ngx-toastr';
import { Router } from '@angular/router';
import { SharedService } from '../../../../services/shared.service';
import { NgbModal } from '@ng-bootstrap/ng-bootstrap';
import { BusinessService } from '../../../../services/business.service';

@Component({
  selector: 'app-business-awaiting-job',
  templateUrl: './business-awaiting-job.component.html',
  styleUrls: ['./business-awaiting-job.component.scss']
})
export class BusinessAwaitingJobComponent implements OnInit {

  public businessJobs = Array<BusinessListJob>();
  public modalActiveClose;
  public currentlyOpenedBusinessJobId: number;

  public preloaderPage = true;

  public paginationLoader = false;
  public pagination = 1;
  public loadMoreCheck = true;

  constructor(
    private readonly _businessService: BusinessService,
    private _modalService: NgbModal,
    private readonly _sharedService: SharedService,
    private readonly _router: Router,
    private readonly _toastr: ToastrService
  ) {
    this._sharedService.checkSidebar = false;
  }

  ngOnInit() {
    window.scrollTo(0, 0);
    this.fetchAllBusinessJobs(true, null);
  }

  /**
   * Load pagination
   */
  public loadPagination(): void {
    this.pagination++;
    this.paginationLoader = true;
    this.fetchAllBusinessJobs(true, null);
  }

  /**
   * fetch all business jobs
   * params status {boolean} - true - opened jobs, false - closed jobs
   * @returns void
   */
  public async fetchAllBusinessJobs(status = true, approved): Promise<void> {
    const data = {
      status: status,
      approve: approved,
      page: this.pagination,
      limit: 50
    };

    try {
      const response = await this._businessService.getBusinessJobs(data);

      response.items.forEach((item) => {
        this.businessJobs.push(item);
      });

      if (response.pagination.total_count === this.businessJobs.length) {
        this.loadMoreCheck = false;
      }
      else if (response.pagination.total_count !== this.businessJobs.length) {
        this.loadMoreCheck = true;
      }
      this.paginationLoader = false;

      this.preloaderPage = false;
    }
    catch (err) {
      this._sharedService.showRequestErrors(err);
    }
  }

  /**
   * Select change router
   * @param url
   */
  public routerJobs(url): void {
    this._router.navigate([url.selectedValues[0]]);
  }

  /**
   * closes specified business job
   * @param job
   * @returns void
   */
  public async closeBusinessJob(job): Promise<void> {
    try{
      await this._businessService.closeBusinessJob(job.id, { status: false });
      this.businessJobs = this.businessJobs.filter((businessJob) => businessJob.id !== job.id);
      this._toastr.success('Job has been successfully closed!');
      if (job.approve === true) {
        this._sharedService.sidebarBusinessBadges.jobApproved--;
        this._sharedService.sidebarBusinessBadges.jobOld++;
      } else {
        this._sharedService.sidebarBusinessBadges.jobAwaiting--;
        this._sharedService.sidebarBusinessBadges.jobOld++;
      }
    }
    catch (err) {
      this._sharedService.showRequestErrors(err);
    }
  }

  /**
   * Delete job from children popup
   * @param job
   */
  public deleteJob(job) {
    this.businessJobs = this.businessJobs.filter((businessJob) => businessJob.id !== job.id);
  }

  /**
   * Delete job from children popup
   * @param job
   */
  public closeJob(job) {
    this.businessJobs = this.businessJobs.filter((businessJob) => businessJob.id !== job.id);
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

}
