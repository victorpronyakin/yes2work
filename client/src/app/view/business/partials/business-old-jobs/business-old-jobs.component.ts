import { Component, OnInit } from '@angular/core';
import { ToastrService } from 'ngx-toastr';
import { BusinessService } from '../../../../services/business.service';
import { BusinessListJob } from '../../../../../entities/models';
import { NgbModal } from '@ng-bootstrap/ng-bootstrap';
import { SharedService } from '../../../../services/shared.service';
import { Router } from '@angular/router';

@Component({
  selector: 'app-business-old-jobs',
  templateUrl: './business-old-jobs.component.html',
  styleUrls: ['./business-old-jobs.component.scss']
})
export class BusinessOldJobsComponent implements OnInit {

  public businessJobs = new Array<BusinessListJob>();
  public modalActiveClose;
  public currentlyOpenedBusinessJobId: number;
  public jobsToShow: boolean;

  public preloaderPage = true;

  public paginationLoader = false;
  public pagination = 1;
  public loadMoreCheck = true;

  constructor(
    private readonly _businessService: BusinessService,
    private readonly _toastr: ToastrService,
    private _modalService: NgbModal,
    private readonly _sharedService: SharedService,
    private readonly _router: Router
  ) {
    this._sharedService.checkSidebar = false;
  }

  ngOnInit() {
    window.scrollTo(0, 0);
    this.fetchAllBusinessJobs(false, null);
  }

  /**
   * Load pagination
   */
  public loadPagination(): void {
    this.pagination++;
    this.paginationLoader = true;
    this.fetchAllBusinessJobs(false, null);
  }

  /**
   * fetch all business jobs
   * params status {boolean} - true - opened jobs, false - closed jobs
   * @returns void
   */
  public async fetchAllBusinessJobs(status = false, approved): Promise<void> {
    const data = {
      status: status,
      // approve: approved,
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
   * @param jobId
   * @returns void
   */
  public async closeBusinessJob(jobId: number): Promise<void> {
    try{
      await this._businessService.closeBusinessJob(jobId, { status: false });
      this._toastr.success('Job has been successfully closed!');
      this.businessJobs = this.businessJobs.filter((businessJob) => businessJob.id !== jobId);
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
   * opens popup
   * @param content - content to be placed within
   * @param jobId - job id to show within popup
   */
  public openVerticallyCentered(content: any, jobId: number) {
    this.currentlyOpenedBusinessJobId = jobId;
    this.modalActiveClose = this._modalService.open(content, { centered: true, 'size': 'lg' });
  }

}
