import { Component, Input, OnInit } from '@angular/core';
import { ToastrService } from 'ngx-toastr';
import { SharedService } from '../../../../services/shared.service';
import { CandidateService } from '../../../../services/candidate.service';
import { CandidateJobPopup } from '../../../../../entities/models';
import { MapsAPILoader } from '@agm/core';
import { NgbModal } from '@ng-bootstrap/ng-bootstrap';
import { Router } from '@angular/router';

@Component({
  selector: 'app-candidate-job-popup',
  templateUrl: './candidate-job-popup.component.html',
  styleUrls: ['./candidate-job-popup.component.scss']
})
export class CandidateJobPopupComponent implements OnInit {

  public _statusButtonJob;
  public _jobArray;
  public _job;
  public _jobAlertsTotal;
  public _findJobTotal;
  public _accessSeenJob;

  public candidateJobInformation: CandidateJobPopup;
  public remaining: number;
  public status: number;
  public loaderPopup = true;
  public checkDate: number;

  @Input() closePopup;
  @Input('jobAlertsTotal') set jobAlertsTotal(jobAlertsTotal) {
    if (jobAlertsTotal) {
      this._jobAlertsTotal = jobAlertsTotal;
    }
  }

  @Input('findJobTotal') set findJobTotal(findJobTotal) {
    if (findJobTotal) {
      this._findJobTotal = findJobTotal;
    }
  }

  @Input('accessSeenJob') set accessSeenJob(accessSeenJob) {
    if (accessSeenJob) {
      this._accessSeenJob = accessSeenJob;
    }
  }

  @Input('job') set job(job) {
    if (job) {
      this._job = job;
    }
  }

  @Input('jobArray') set jobArray(jobArray) {
    if (jobArray) {
      this._jobArray = jobArray;
    }
  }

  @Input('statusButtonJob') set statusButtonJob(statusButtonJob) {
    if (statusButtonJob) {
      this._statusButtonJob = statusButtonJob;
    }
  }

  public callbackBind = this.callback.bind(this);
  public obj = {
    distance: ''
  };

  public modalActiveClose: any;

  constructor(
    private readonly _candidateService: CandidateService,
    private readonly _toastr: ToastrService,
    public readonly _sharedService: SharedService,
    private readonly _mapsAPILoader: MapsAPILoader,
    private readonly _modalService: NgbModal,
    private readonly _router: Router
  ) {
    this._sharedService.checkSidebar = false;
  }

  ngOnInit() {
    if(this._job.id){
      this.getCandidateJob(this._job.id);
    }
    else if(this._job.jobID) {
      this.getCandidateJob(this._job.jobID);
    }
    else if(this._job.jobId) {
      this.getCandidateJob(this._job.jobId);
    }
  }

  /**
   * Get candidate job popup
   * @param id {number}
   * @return {Promise<void>}
   */
  public async getCandidateJob(id: number): Promise<void> {
    this.candidateJobInformation = await this._candidateService.getCandidateJob(id);

    this._mapsAPILoader.load().then(() => {
      const distance = new google.maps.DistanceMatrixService();
      distance.getDistanceMatrix(
        {
          origins: [this.candidateJobInformation.candidateAddress],
          destinations: [this.candidateJobInformation.companyAddress],
          travelMode: google.maps.TravelMode.DRIVING,
        }, this.callbackBind);

      this._sharedService.getCandidateBadges();
    });

    this.status = this.candidateJobInformation['status'];

    const todayDate = new Date();
    const endDate = new Date(this.candidateJobInformation['endDate']);
    this.remaining = Math.round((endDate.getTime() - todayDate.getTime()) / (1000*60*60*24) + 1);

    this.checkDate = Math.sign(this.remaining);
    this.loaderPopup = false;
  }

  /**
   * Callback function from google distance
   * @param response
   * @param status
   */
  public callback (response, status) {
    let newDistance;
    newDistance = response;

    if (newDistance) {
      if(newDistance.rows){
        if(newDistance.rows[0].elements[0].distance){
          const dataDistance = newDistance.rows[0].elements[0].distance.value / 1000;
          const dataNewDistance = Math.ceil(dataDistance);
          this.obj.distance = ' - ' + dataNewDistance + ' km away';
        }
        else{
          this.obj.distance = '';
        }
      }
      else {
        this.obj.distance = '';
      }
    }
    else {
      this.obj.distance = '';
    }
  }

  /**
   * Approve job post
   * @param jobId {number}
   * @return {Promise<void>}
   */
  public async applyJobAlerts(jobId: number): Promise<void> {
    try {
      await this._candidateService.applyJobAlerts(jobId);
      const index = this._jobArray.indexOf(this._job);
      this._jobArray.splice(index, 1);
      this._sharedService.sidebarCandidateBadges.jobAlertsNew--;
      this._sharedService.sidebarCandidateBadges.applicantAwaiting++;
      this._toastr.success('Your application has been submitted for the employer to review');
      if(this._jobAlertsTotal){
        this._jobAlertsTotal.number--;
      }
      this.closePopup();
    }
    catch (err) {
      this._sharedService.showRequestErrors(err);
    }
  }

  /**
   * Approve job post
   * @param jobId {number}
   * @return {Promise<void>}
   */
  public async reApplyJobAlerts(jobId: number): Promise<void> {
    try {
      await this._candidateService.applyJobAlerts(jobId);
      const index = this._jobArray.indexOf(this._job);
      this._jobArray.splice(index, 1);
      this._sharedService.sidebarCandidateBadges.jobAlertsDeclined--;
      this._sharedService.sidebarCandidateBadges.applicantAwaiting++;
      this._toastr.success('Your application has been submitted for the employer to review');
      if(this._jobAlertsTotal){
        this._jobAlertsTotal.number--;
      }
      this.closePopup();
    }
    catch (err) {
      this._sharedService.showRequestErrors(err);
    }
  }


  /**
   * Decline job post
   * @param jobId {number}
   * @return {Promise<void>}
   */
  public async declineJobAlerts(jobId: number): Promise<void> {
     try {
       await this._candidateService.declineJobAlerts(jobId);
       const index = this._jobArray.indexOf(this._job);
       this._jobArray.splice(index, 1);
       this._sharedService.sidebarCandidateBadges.jobAlertsNew--;
       this._sharedService.sidebarCandidateBadges.jobAlertsDeclined++;
       this._toastr.success('Job post was declined');
       if(this._jobAlertsTotal){
       this._jobAlertsTotal.number--;
     }
      this.closePopup();
     }
     catch (err) {
      this._sharedService.showRequestErrors(err);
     }
  }

  /**
   * Open second popup
   */
  public openSecondPopup(content) {
    this.modalActiveClose = this._modalService.open(content, { centered: true, windowClass: 'second-popup', backdropClass: 'light-blue-backdrop' });
    this.closePopup();
  }

  /**
   * Routing to candidate personal information
   */
  public routerToProfile() {
    this._router.navigate(['/candidate/profile_details']);
  }
}
