import { ChangeDetectorRef, Component, OnInit } from '@angular/core';
import { INgxMyDpOptions } from 'ngx-mydatepicker';
import { CandidateService } from '../../../../services/candidate.service';
import { ToastrService } from 'ngx-toastr';
import { SharedService } from '../../../../services/shared.service';
import { CandidateOpportunities, OpportunitiesJobsList } from '../../../../../entities/models';
import { NgbModal } from '@ng-bootstrap/ng-bootstrap';
import { Router } from '@angular/router';
import { MapsAPILoader } from '@agm/core';
import { Observable } from 'rxjs/Observable';
import {AdminCandidateProfile} from "../../../../../entities/models-admin";

@Component({
  selector: 'app-candidate-your-opportunities',
  templateUrl: './candidate-your-opportunities.component.html',
  styleUrls: ['./candidate-your-opportunities.component.scss']
})
export class CandidateYourOpportunitiesComponent implements OnInit {

  public myOptionsDate: INgxMyDpOptions = { dateFormat: 'mm.dd.yyyy' };
  public model: any = { date: { year: 2018, month: 10, day: 9 } };

  public opportunitiesJobs: OpportunitiesJobsList;
  public newOpportunities = Array<CandidateOpportunities>();
  public declinedOpportunities = Array<CandidateOpportunities>();
  public missedOpportunities = Array<CandidateOpportunities>();

  public modalActiveClose: any;
  public preloaderPage = true;

  public distanceNewOpportunities = [];
  public distanceDeclinedOpportunities = [];
  public distanceMissedOpportunities = [];

  public currentJob: any;
  public statusButtonJob: any;
  public jobArray: any;

  public checkVideo: any;
  public checkPercentage: any;
  public candidateProfileCV: any;
  public candidateProfileDetails: AdminCandidateProfile;
  public allowVideo = false;
  public checkLooking = false;

  constructor(
    private readonly _candidateService: CandidateService,
    private readonly _toastr: ToastrService,
    private readonly _sharedService: SharedService,
    private readonly _modalService: NgbModal,
    private readonly _router: Router,
    private readonly _mapsAPILoader: MapsAPILoader,
    private readonly _ref: ChangeDetectorRef
  ) {
    this._sharedService.checkSidebar = false;
  }

  ngOnInit() {
    window.scrollTo(0, 0);
    this.getCandidateProfileDetails().then(() => {
      this.getCandidateVideoStatus().then(() => {
        this.getJobOpportunities('', '');
      });
    });
  }

  /**
   * Get Candidate Video Status
   * @return {Promise<void>}
   */
  public async getCandidateVideoStatus(): Promise<void> {
    const response = await this._candidateService.getCandidateVideoStatus();
    this.allowVideo = response.allowVideo;
  }

  /**
   * Get details profile candidate
   * @return {Promise<void>}
   */
  public async getCandidateProfileDetails(): Promise<void> {
    this.candidateProfileDetails = await this._candidateService.getCandidateProfileDetails();

    this.checkVideo = this.candidateProfileDetails.profile.video;
    this.checkPercentage = this.candidateProfileDetails.profile.percentage;
    this.candidateProfileCV = this.candidateProfileDetails.profile.copyOfID;
    this.checkLooking = this.candidateProfileDetails.profile.looking;
  }

  /**
   * Reset Array
   */
  public resetArrayPagination(): void{
    this.newOpportunities = [];
    this.declinedOpportunities = [];
    this.missedOpportunities = [];
    this.distanceNewOpportunities = [];
    this.distanceDeclinedOpportunities = [];
    this.distanceMissedOpportunities = [];
  }

  /**
   * Select change router
   * @param url
   * @param dateStart
   * @param dateEnd
   */
  public routerOpportunities(url, dateStart, dateEnd): void {
    this._router.navigate([url.selectedValues[0]], (dateStart !== '' && dateEnd !== '') ? {
      queryParams: {
        dateStart: dateStart,
        dateEnd: dateEnd
      }}: (dateStart != '' && dateEnd == '') ? {
      queryParams: {
        dateStart: dateStart
      }}: (dateStart == '' && dateEnd != '') ? {
      queryParams: {
        dateEnd: dateEnd
      }}: {} );
  }

  /**
   * Get opportunities
   * @param dateStart {string}
   * @param dateEnd {string}
   * @return {Promise<void>}
   */
  public async getJobOpportunities(dateStart: string, dateEnd: string): Promise<void> {
    const startDate = new Date(dateStart);
    const endDate = new Date(dateEnd);
    if (startDate > endDate) {
      this._toastr.error('Date End not be shorter than the Date Start');
    }
    else{
      try {
        const data = {
          dateStart: (dateStart !== '') ? startDate.getDate() + '.' + (startDate.getMonth() + 1) + '.' + startDate.getFullYear() : dateStart,
          dateEnd: (dateEnd !== '') ? endDate.getDate() + '.' + (endDate.getMonth() + 1) + '.' + endDate.getFullYear() : dateEnd
        };
        this.opportunitiesJobs = await this._candidateService.getCandidateJobAlertsOpportunities(data.dateStart, data.dateEnd);

        this.preloaderPage = false;
        this.sendDataDistanceNewOpportunitiess(this.opportunitiesJobs).subscribe(data => {
          if(data){
            this.distanceNewOpportunities.push(data);
          }
          this._ref.detectChanges();
        });
        this.sendDataDistanceDeclinedOpportunities(this.opportunitiesJobs).subscribe(data => {
          if(data){
            this.distanceDeclinedOpportunities.unshift(data);
          }
          this._ref.detectChanges();
        });
        this.sendDataDistanceMissedOpportunities(this.opportunitiesJobs).subscribe(data => {
          if(data){
            this.distanceMissedOpportunities.push(data);
          }
          this._ref.detectChanges();
        });
      }
      catch (err) {
        this._sharedService.showRequestErrors(err);
      }
    }
  }

  /**
   * Send distance array
   * @param response
   * @returns {Observable|'../../../Observable".Observable|"../../Observable".Observable}
   */
  public sendDataDistanceNewOpportunitiess(response): Observable<any> {
    return new Observable(observer => {
      if(response.new && response.new.length > 0){
        response.new.forEach((item) => {
          if(item.companyAddress){
            this._mapsAPILoader.load().then(() => {
              const distance = new google.maps.DistanceMatrixService();
              distance.getDistanceMatrix({
                origins: [response.candidateAddress],
                destinations: [item.companyAddress],
                travelMode: google.maps.TravelMode.DRIVING,
              }, callback);

              function callback (response, status) {
                let newDistance;
                newDistance = response;

                if (newDistance) {
                  if(newDistance.rows){
                    if(newDistance.rows[0].elements[0].distance){
                      if(newDistance.rows[0].elements[0].distance.text){
                        const dataDistance = newDistance.rows[0].elements[0].distance.value / 1000;
                        const dataNewDistance = Math.ceil(dataDistance);
                        observer.next(dataNewDistance + ' km');
                      }
                    }
                    else if (newDistance.rows[0].elements[0].status){
                      observer.next('-');
                    }
                  }
                  else {
                    observer.next('-');
                  }
                }
                else {
                  observer.next('-');
                }
              }
            });
          }
          else{
            observer.next('-');
          }
          this.newOpportunities.push(item);
        });
      }
    });
  }

  /**
   * Send distance array
   * @param response
   * @returns {Observable|'../../../Observable".Observable|"../../Observable".Observable}
   */
  public sendDataDistanceDeclinedOpportunities(response): Observable<any> {
    return new Observable(observer => {
      if(response.decline && response.decline.length > 0){
        response.decline.forEach((item) => {
          if(item.companyAddress){
            this._mapsAPILoader.load().then(() => {
              const distance = new google.maps.DistanceMatrixService();
              distance.getDistanceMatrix({
                origins: [response.candidateAddress],
                destinations: [item.companyAddress],
                travelMode: google.maps.TravelMode.DRIVING,
              }, callback);

              function callback (response, status) {
                let newDistance;
                newDistance = response;

                if (newDistance) {
                  if(newDistance.rows){
                    if(newDistance.rows[0].elements[0].distance){
                      if(newDistance.rows[0].elements[0].distance.text){
                        const dataDistance = newDistance.rows[0].elements[0].distance.value / 1000;
                        const dataNewDistance = Math.ceil(dataDistance);
                        observer.next(dataNewDistance + ' km');
                      }
                    }
                    else if (newDistance.rows[0].elements[0].status){
                      observer.next('-');
                    }
                  }
                  else {
                    observer.next('-');
                  }
                }
                else {
                  observer.next('-');
                }
              }
            });
          }
          else{
            observer.next('-');
          }
          this.declinedOpportunities.push(item);
        });
      }
      else {
        observer.next('-');
      }
    });
  }

  /**
   * Send distance array
   * @param response
   * @returns {Observable|'../../../Observable".Observable|"../../Observable".Observable}
   */
  public sendDataDistanceMissedOpportunities(response): Observable<any> {
    return new Observable(observer => {
      if(response.expired && response.expired.length > 0){
        response.expired.forEach((item) => {
          if(item.companyAddress){
            this._mapsAPILoader.load().then(() => {
              const distance = new google.maps.DistanceMatrixService();
              distance.getDistanceMatrix({
                origins: [response.candidateAddress],
                destinations: [item.companyAddress],
                travelMode: google.maps.TravelMode.DRIVING,
              }, callback);

              function callback (response, status) {
                let newDistance;
                newDistance = response;

                if (newDistance) {
                  if(newDistance.rows){
                    if(newDistance.rows[0].elements[0].distance){
                      if(newDistance.rows[0].elements[0].distance.text){
                        const dataDistance = newDistance.rows[0].elements[0].distance.value / 1000;
                        const dataNewDistance = Math.ceil(dataDistance);
                        observer.next(dataNewDistance + ' km');
                      }
                    }
                    else if (newDistance.rows[0].elements[0].status){
                      observer.next('-');
                    }
                  }
                  else {
                    observer.next('-');
                  }
                }
                else {
                  observer.next('-');
                }
              }
            });
          }
          else{
            observer.next('-');
          }
          this.missedOpportunities.push(item);
        });
      }
    });
  }

  /**
   * Managed modal
   * @param content {any}
   * @param job
   * @param status
   * @param jobArray
   */
  public openVerticallyCentered(content: any, jobArray, job, status) {
    this.modalActiveClose = this._modalService.open(content, { centered: true, 'size': 'lg' });
    this.currentJob = job;
    this.jobArray = jobArray;
    this.statusButtonJob = status;
  }

}
