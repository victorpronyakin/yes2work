import { Component, OnInit, Pipe } from '@angular/core';
import { BusinessService } from '../../../../services/business.service';
import { SharedService } from '../../../../services/shared.service';
import { BusinessApplicant, JobCriteria, ApplicantsList } from '../../../../../entities/models';
import { NgbModal } from '@ng-bootstrap/ng-bootstrap';
import { ToastrService } from 'ngx-toastr';
import { ActivatedRoute, Router } from '@angular/router';

@Component({
  selector: 'app-business-applicants',
  templateUrl: './business-applicants.component.html',
  styleUrls: ['./business-applicants.component.scss']
})
export class BusinessApplicantsComponent implements OnInit {

  public applicantsAwaitingApproval: number;
  public applicantsShortlisted: number;
  public applicantsApproved: number;
  public applicantsDeclined: number;

  public listOfJobs: JobCriteria[];
  public applicantsList: ApplicantsList;
  public requestJobId: number;

  public modalActiveClose;
  public candidateToView;
  public preloaderPage = true;

  constructor(
      private readonly _businessService: BusinessService,
      private readonly _sharedService: SharedService,
      private readonly _router: Router,
      private readonly _route: ActivatedRoute
  ) {
    this._sharedService.checkSidebar = false;
  }

  ngOnInit() {
    window.scrollTo(0, 0);
    this._route.queryParams.subscribe(data => {
      if (data.jobId !== 'undefined'){
        this.requestJobId = Number(data.jobId);
      }
    });
    this.fetchListOfApplicants(this.requestJobId).then(response => {
      this.fetchAllJobs();
    });
  }

  /**
   * Select change router
   * @param url
   * @param jobID
   */
  public routerApplicants(url, jobID): void {
    this._router.navigate([url], (jobID != 'null') ? { queryParams: { jobId: jobID }} : (isNaN(jobID)) ? {} : {} );
  }

  /**
   * Fetches list of all applicants
   * @returns void
   */
  public async fetchListOfApplicants(jobID: number): Promise<void> {
    try {
      this.applicantsList = await this._businessService.getListOfApplicants(jobID);

      this.applicantsAwaitingApproval = this.applicantsList.awaiting;
      this.applicantsShortlisted = this.applicantsList.shortList;
      this.applicantsApproved = this.applicantsList.approve;
      this.applicantsDeclined = this.applicantsList.decline;
      this.preloaderPage = false;
    }
    catch (err) {
      this._sharedService.showRequestErrors(err);
    }
  }
  /**
   * Fetches list of jobs
   * @returns void
   */
  public async fetchAllJobs(): Promise<void> {
    try {
      this.listOfJobs = await this._businessService.getBusinessJobsMatchingCriteria(false, null);
    }
    catch (err) {
      this._sharedService.showRequestErrors(err);
    }
  }
}
