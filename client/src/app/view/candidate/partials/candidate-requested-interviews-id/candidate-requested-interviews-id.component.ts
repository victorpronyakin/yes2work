import { Component, Input, OnInit} from '@angular/core';
import { CandidateJobPopup } from '../../../../../entities/models';
import { MapsAPILoader } from '@agm/core';
import { SharedService } from '../../../../services/shared.service';
import { CandidateService } from '../../../../services/candidate.service';

@Component({
  selector: 'app-candidate-requested-interviews-id',
  templateUrl: './candidate-requested-interviews-id.component.html',
  styleUrls: ['./candidate-requested-interviews-id.component.scss']
})
export class CandidateRequestedInterviewsIdComponent implements OnInit {

  public _statusButtonJob;
  public _jobArray;
  public _job;
  public _jobAlertsTotal;
  public _findJobTotal;

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

  constructor(
    private readonly _candidateService: CandidateService,
    public readonly _sharedService: SharedService,
    private readonly _mapsAPILoader: MapsAPILoader
  ) {
    this._sharedService.checkSidebar = false;
  }

  ngOnInit() {
    this.getCandidateJob(this._job.interviewID);
  }

  /**
   * Get candidate job popup
   * @param id {number}
   * @return {Promise<void>}
   */
  public async getCandidateJob(id: number): Promise<void> {
    this.candidateJobInformation = await this._candidateService.getCandidateJobInterviewId(id);

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

}
