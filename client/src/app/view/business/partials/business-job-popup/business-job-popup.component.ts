import {Component, EventEmitter, forwardRef, Inject, Input, OnInit, Output} from '@angular/core';
import { SharedService } from '../../../../services/shared.service';
import { BusinessService } from '../../../../services/business.service';
import { IBusinessJobFullDetails } from '../../../../../entities/models';
// import { BusinessCurrentJobsComponent } from '../business-current-jobs/business-current-jobs.component';
import { ToastrService } from 'ngx-toastr';
import set = Reflect.set;
import { BusinessOldJobsComponent } from '../business-old-jobs/business-old-jobs.component';

@Component({
  selector: 'app-business-job-popup',
  templateUrl: './business-job-popup.component.html',
  styleUrls: ['./business-job-popup.component.scss']
})
export class BusinessJobPopupComponent implements OnInit {
  public _currentBusinessJobId: number;
  @Input() closePopup;
  @Input('currentBusinessJobId') set currentBusinessJobId(currentBusinessJobId: number) {
    if (currentBusinessJobId) {
      this._currentBusinessJobId = currentBusinessJobId;
      this.getSpecificBusinessJob(currentBusinessJobId);
    }
  }
  get currentBusinessJobId(): number {
    return this.currentBusinessJobId;
  }

  @Input() businessJobs = Array();
  @Output() deleteJob:  EventEmitter<any> = new EventEmitter();
  @Output() closeJob:  EventEmitter<any> = new EventEmitter();
  public currentBusinessJob: IBusinessJobFullDetails;
  public jobAvailability: string;
  public jobVideo: string;
  public nationality: string;
  public fields: string;
  public qualification: string;
  public yearsOfWorkExperience: string;
  public daysBeforeClosure: number;
  public location: string;
  public loaderPopup = true;
  public checkDate: number;

  constructor(
      private readonly _sharedService: SharedService,
      private readonly _businessService: BusinessService,
      private readonly _toastr: ToastrService
  ) {
    this._sharedService.checkSidebar = false;
  }

  ngOnInit() {

    /*emit.funcName.emit()*/
  }

  /**
   * fetches specified business job by id
   * @param id
   * @returns void
   */
  public async getSpecificBusinessJob(id: number): Promise<any> {
    try {
      const response = await this._businessService.getBusinessJobById(id);
      this.currentBusinessJob = response;
      this.jobAvailability = this.getAvailabilityInHuman(this.currentBusinessJob.availability);
      this.nationality = this._sharedService.getNationalityInHumanReadableForm(this.currentBusinessJob.nationality);
      this.qualification = this._sharedService.getQualificationInHumanReadableForm(this.currentBusinessJob.qualification);
      this.fields = this.currentBusinessJob['field'].join(', ');
      this.yearsOfWorkExperience = this.transformYearsWork(this.currentBusinessJob['yearsOfWorkExperience']);
      this.jobVideo = this.transformVideo(this.currentBusinessJob['video']);
      this.daysBeforeClosure = this._sharedService.getDifferenceInDays(
        new Date(), this.currentBusinessJob.closureDate);

      this.checkDate = Math.sign(this.daysBeforeClosure);
      this.loaderPopup = false;
    } catch (err) {
      this._sharedService.showRequestErrors(err);
    }
  }

  /**
   * gets availability of business job in human readable form
   * @param availability {number} - integer representation of job availability {0, 1, 2 or 3}
   * @returns {string}
   */
  public getAvailabilityInHuman(availability): string {
    if (availability == 0) {
      return 'All'
    } else {
      let res = '';
      availability.forEach((item, i) => {
        if (i === 1) {
          res += ', ';
        }
        res += this._sharedService.getAvailabilityInHumanReadableForm(item);
      });

      return res;
    }
  }

  /**
   * Transform years work data
   * @param data {number}
   * @returns {string}
   */
  public transformYearsWork(data) {
    let req = '';
    data.forEach((item, i) => {
      switch (item) {
        case '0':
          req += 'All';
          break;
        case '1':
          req = (data.length > 1 && i !== data.length - 1) ? '0, ' : '0';
          break;
        case '2':
          req += (data.length > 1 && i !== data.length - 1) ? '0-1 Year, ' : '0-1 Year';
          break;
        case '3':
          req += (data.length > 1 && i !== data.length - 1) ? '1-2 Year, ' : '1-2 Year';
          break;
        case '4':
          req += (data.length > 1 && i !== data.length - 1) ? '3-5 Years, ' : '3-5 Years';
          break;
        case '5':
          req += (data.length > 1 && i !== data.length - 1) ? '5+ Years, ' : '5+ Years';
          break;
      }
    });
    return req;
  }

  /**
   * Transform video data
   * @param data {number}
   */
  public transformVideo(data) {
    let req = '0';
    switch (data) {
      case 0:
        req = 'All';
        break;
      case 1:
        req = 'With Video';
        break;
      case 2:
        req = 'Without Video';
        break;
    }
    return req;
  }

  /**
   * closes business job specified with id
   * @param job
   * @returns void
   */
  public async closeBusinessJob(job): Promise<void> {
    try{
      await this._businessService.closeBusinessJob(job.id, { status: false });
      this.closeJob.emit(job);
      if (job.approve === true) {
        this._sharedService.sidebarBusinessBadges.jobApproved--;
        this._sharedService.sidebarBusinessBadges.jobOld++;
      } else {
        this._sharedService.sidebarBusinessBadges.jobAwaiting--;
        this._sharedService.sidebarBusinessBadges.jobOld++;
      }

      this._toastr.success('Job has been successfully closed!');
      this.closePopup();
    }
    catch (err) {
      this._sharedService.showRequestErrors(err);
    }
  }

    /**
     * deletes business job specified with id
     * @param job
     * @param status
     * @returns void
     */
  public async deleteBusinessJob(job, status): Promise<void> {

    try {
      await this._businessService.deleteBusinessJob(job.id);
      this.deleteJob.emit(job);
      this.closePopup();

      if(status === true){
        if (job.approve === true) {
          this._sharedService.sidebarBusinessBadges.jobApproved--;
        } else {
          this._sharedService.sidebarBusinessBadges.jobAwaiting--;
        }
      }
      else {
        this._sharedService.sidebarBusinessBadges.jobOld--;
      }
      this._toastr.success('Job has been successfully closed!');
    }
    catch (err) {
      this._sharedService.showRequestErrors(err);
    }
  }
}
