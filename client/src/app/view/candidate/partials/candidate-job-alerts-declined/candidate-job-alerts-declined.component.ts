import { ChangeDetectorRef, Component, OnInit } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { NgbModal } from '@ng-bootstrap/ng-bootstrap';
import { SharedService } from '../../../../services/shared.service';
import { ToastrService } from 'ngx-toastr';
import { CandidateService } from '../../../../services/candidate.service';
import { CandidateOpportunities } from '../../../../../entities/models';
import { INgxMyDpOptions } from 'ngx-mydatepicker';
import { Observable } from 'rxjs/Observable';
import { MapsAPILoader } from '@agm/core';

@Component({
  selector: 'app-candidate-job-alerts-declined',
  templateUrl: './candidate-job-alerts-declined.component.html',
  styleUrls: ['./candidate-job-alerts-declined.component.scss']
})
export class CandidateJobAlertsDeclinedComponent implements OnInit {

  public myOptionsDate: INgxMyDpOptions = { dateFormat: 'mm.dd.yyyy' };
  public modelStartDate: any;
  public modelEndDate: any;

  public declinedOpportunities = Array<CandidateOpportunities>();

  public modalActiveClose: any;
  public preloaderPage = true;
  public paginationLoader = false;
  public pagination = 1;
  public loadMoreCheck = true;
  public distanceDeclinedOpportunities = [];

  public currentJob: any;
  public statusButtonJob: any;
  public jobArray: any;
  public checkVideo: any;
  public checkPercentage: any;
  public candidateProfileCV: any;
  public allowVideo = true;
  public checkLooking = true;
  public accessSeenJob = {};

  constructor(
    private readonly _candidateService: CandidateService,
    private readonly _toastr: ToastrService,
    private readonly _sharedService: SharedService,
    private readonly _modalService: NgbModal,
    private readonly _route: ActivatedRoute,
    private readonly _mapsAPILoader: MapsAPILoader,
    private readonly _ref: ChangeDetectorRef
  ) {
    this._sharedService.checkSidebar = false;
  }

  ngOnInit() {
    window.scrollTo(0, 0);
    this._route.queryParams.subscribe(data => {
      let start = '';
      let end = '';
      if (typeof data.dateStart !== 'undefined') {
        start = data.dateStart;
        const startDate =  new Date(start);
        this.modelStartDate = {
          date:
            {
              year: startDate.getFullYear(),
              month: startDate.getMonth()+1,
              day: startDate.getDate()
            }
        };
      }
      if (typeof data.dateEnd !== 'undefined') {
        end = data.dateEnd;
        const endDate =  new Date(end);
        this.modelEndDate = {
          date:
            {
              year: endDate.getFullYear(),
              month: endDate.getMonth()+1,
              day: endDate.getDate()
            }
        };
      }

      this.getCandidateProfileDetails().then(() => {
        this.getCandidateVideoStatus().then(() => {
          this.getOpportunitiesDeclined(start, end);
        });
      })
    });
  }

  /**
   * Get Candidate Video Status
   * @return {Promise<void>}
   */
  public async getCandidateVideoStatus(): Promise<void> {
    const response = await this._candidateService.getCandidateVideoStatus();
    this.allowVideo = response.allowVideo;
    this.accessSeenJob['allowVideo'] = this.allowVideo;
  }

  /**
   * Get details profile candidate
   * @return {Promise<void>}
   */
  public async getCandidateProfileDetails(): Promise<void> {
    const response = await this._candidateService.getCandidateProfileDetails();

    this.checkVideo = response.profile.video;
    this.checkPercentage = response.profile.percentage;
    this.candidateProfileCV = response.profile.copyOfID;
    this.checkLooking = response.profile.looking;

    this.accessSeenJob['videoCheck'] = this.checkVideo;
    this.accessSeenJob['candidateProfileCV'] = this.candidateProfileCV;
    this.accessSeenJob['progressBar'] = this.checkPercentage;
    this.accessSeenJob['checkLooking'] = this.checkLooking;
  }

  /**
   * Reset Array
   */
  public resetArrayPagination(): void{
    this.distanceDeclinedOpportunities = [];
    this.declinedOpportunities = [];
    this.pagination = 1;
  }

  /**
   * Load pagination
   */
  public loadPagination(start, end): void {
    this.pagination++;
    this.paginationLoader = true;
    this.getOpportunitiesDeclined(start, end);
  }

  /**
   * Get opportunities new
   * @param dateStart {string}
   * @param dateEnd {string}
   * @param limit {string}
   * @return {Promise<void>}
   */
  public async getOpportunitiesDeclined(dateStart: string, dateEnd: string, limit: string = '50'): Promise<void> {

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
        const response = await this._candidateService.getOpportunitiesDeclined(data, this.pagination);

        this.sendDataDistanceDeclinedOpportunities(response).subscribe(data => {
          this.distanceDeclinedOpportunities.unshift(data);
          this._ref.detectChanges();
        });

        if (response.pagination.total_count === this.declinedOpportunities.length) {
          this.loadMoreCheck = false;
        }
        else if (response.pagination.total_count !== this.declinedOpportunities.length) {
          this.loadMoreCheck = true;
        }
        this.paginationLoader = false;

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
  public sendDataDistanceDeclinedOpportunities(response): Observable<any> {
    return new Observable(observer => {
      if(response.items && response.items.length > 0){
        response.items.forEach((item) => {
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
   * Approved opportunities
   * @param client
   * @param listItems
   * @return {Promise<void>}
   */
  /*public async approvedOpportunities(client, listItems): Promise<void> {
    try {
      await this._candidateService.approveJobPost(client.jobID, client.clientID);
      // this._sharedService.sidebarCandidateBadges.opportunitiesDeclined--;
      // this._sharedService.sidebarCandidateBadges.appliedAwaiting++;
      this._toastr.success('Job was been approved');
      const index = listItems.indexOf(client);
      listItems.splice(index, 1);
    }
    catch (err) {
      this._sharedService.showRequestErrors(err);
    }
  }*/

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
    this.jobArray = jobArray;
    this.statusButtonJob = status;
  }

}
