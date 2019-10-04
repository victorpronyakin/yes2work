import { ChangeDetectorRef, Component, OnInit } from '@angular/core';
import { INgxMyDpOptions } from 'ngx-mydatepicker';
import { CandidateService } from '../../../../services/candidate.service';
import { ToastrService } from 'ngx-toastr';
import { SharedService } from '../../../../services/shared.service';
import { CandidateOpportunities, OpportunitiesList } from '../../../../../entities/models';
import { NgbModal } from '@ng-bootstrap/ng-bootstrap';
import { Router } from '@angular/router';
import { Observable } from 'rxjs/Observable';
import { MapsAPILoader } from '@agm/core';

@Component({
  selector: 'app-candidate-your-application',
  templateUrl: './candidate-your-application.component.html',
  styleUrls: ['./candidate-your-application.component.scss']
})
export class CandidateYourApplicationComponent implements OnInit {

  public myOptionsDate: INgxMyDpOptions = { dateFormat: 'mm.dd.yyyy' };
  public model: any = { date: { year: 2018, month: 10, day: 9 } };

  public opportunitiesJobs: OpportunitiesList;
  public awaitingOpportunities = Array<CandidateOpportunities>();
  public successfulOpportunities = Array<CandidateOpportunities>();
  public declinedOpportunities = Array<CandidateOpportunities>();

  public modalActiveClose: any;
  public preloaderPage = true;

  public distanceAwaitingOpportunities = [];
  public distanceSuccessfulOpportunities = [];
  public distanceDeclinedOpportunities = [];

  public currentJob: any;
  public statusButtonJob: any;
  public jobArray: any;

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
    this.getOpportunitiesApplication('', '');
  }

  /**
   * Reset Array
   */
  public resetArrayPagination(): void{
    this.awaitingOpportunities = [];
    this.successfulOpportunities = [];
    this.declinedOpportunities = [];
    this.distanceAwaitingOpportunities = [];
    this.distanceSuccessfulOpportunities = [];
    this.distanceDeclinedOpportunities = [];
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
   * Get application applied
   * @param dateStart {string}
   * @param dateEnd {string}
   * @param limit {string}
   * @return {Promise<void>}
   */
  public async getOpportunitiesApplication(dateStart: string, dateEnd: string, limit: string = '50'): Promise<void> {
    const startDate = new Date(dateStart);
    const endDate = new Date(dateEnd);
    if (startDate > endDate) {
      this._toastr.error('Date End not be shorter than the Date Start');
    }
    else{
      try {
        const data = {
          dateStart: (dateStart !== '') ? startDate.getDate() + '.' + (startDate.getMonth() + 1) + '.' + startDate.getFullYear() : dateStart,
          dateEnd: (dateEnd !== '') ? endDate.getDate() + '.' + (endDate.getMonth() + 1) + '.' + endDate.getFullYear() : dateEnd,
          limit: limit,
        };
        this.opportunitiesJobs = await this._candidateService.getOpportunitiesApplication(data);

        this.sendDataDistanceAwaitingOpportunities(this.opportunitiesJobs).subscribe(data => {
          if(data){
            this.distanceAwaitingOpportunities.push(data);
          }
          this._ref.detectChanges();
        });
        this.sendDataDistanceSuccessfulOpportunities(this.opportunitiesJobs).subscribe(data => {
          if(data){
            this.distanceSuccessfulOpportunities.push(data);
          }
          this._ref.detectChanges();
        });
        this.sendDataDistanceDeclinedOpportunities(this.opportunitiesJobs).subscribe(data => {
          if(data){
            this.distanceDeclinedOpportunities.push(data);
          }
          this._ref.detectChanges();
        });
        this.preloaderPage = false;
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
  public sendDataDistanceAwaitingOpportunities(response): Observable<any> {
    return new Observable(observer => {
      if(response.awaiting && response.awaiting.length > 0){
        response.awaiting.forEach((item) => {
          if(item.location){
            this._mapsAPILoader.load().then(() => {
              const distance = new google.maps.DistanceMatrixService();
              distance.getDistanceMatrix({
                origins: [response.candidateAddress],
                destinations: [item.location],
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
          this.awaitingOpportunities.push(item);
        });
      }
    });
  }

  /**
   * Send distance array
   * @param response
   * @returns {Observable|'../../../Observable".Observable|"../../Observable".Observable}
   */
  public sendDataDistanceSuccessfulOpportunities(response): Observable<any> {
    return new Observable(observer => {
      if(response.successful && response.successful.length > 0){
        response.successful.forEach((item) => {
          if(item.location){
            this._mapsAPILoader.load().then(() => {
              const distance = new google.maps.DistanceMatrixService();
              distance.getDistanceMatrix({
                origins: [response.candidateAddress],
                destinations: [item.location],
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
          this.successfulOpportunities.push(item);
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
  public sendDataDistanceDeclinedOpportunities(response): Observable<any> {
    return new Observable(observer => {
      if(response.decline && response.decline.length > 0){
        response.decline.forEach((item) => {
          if(item.location){
            this._mapsAPILoader.load().then(() => {
              const distance = new google.maps.DistanceMatrixService();
              distance.getDistanceMatrix({
                origins: [response.candidateAddress],
                destinations: [item.location],
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
    });
  }

  /**
   * Managed modal
   * @param content {any}
   * @param jobArray
   * @param job
   * @param status
   */
  public openVerticallyCentered(content: any, jobArray, job, status) {
    this.modalActiveClose = this._modalService.open(content, { centered: true, 'size': 'lg' });
    this.currentJob = job;
    this.statusButtonJob = status;
    this.jobArray = jobArray;
  }

}
