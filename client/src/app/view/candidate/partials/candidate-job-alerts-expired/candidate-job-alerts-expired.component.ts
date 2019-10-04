import { ChangeDetectorRef, Component, OnInit } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { NgbModal } from '@ng-bootstrap/ng-bootstrap';
import { SharedService } from '../../../../services/shared.service';
import { ToastrService } from 'ngx-toastr';
import { CandidateService } from '../../../../services/candidate.service';
import { CandidateOpportunities } from '../../../../../entities/models';
import { INgxMyDpOptions } from 'ngx-mydatepicker';
import { MapsAPILoader } from '@agm/core';
import { Observable } from 'rxjs/Observable';

@Component({
  selector: 'app-candidate-job-alerts-expired',
  templateUrl: './candidate-job-alerts-expired.component.html',
  styleUrls: ['./candidate-job-alerts-expired.component.scss']
})
export class CandidateJobAlertsExpiredComponent implements OnInit {
  public myOptionsDate: INgxMyDpOptions = { dateFormat: 'mm.dd.yyyy' };
  public modelStartDate: any;
  public modelEndDate: any;

  public missedOpportunities = Array<CandidateOpportunities>();

  public currentlyOpenedCandidateJobId: number;
  public modalActiveClose: any;

  public preloaderPage = true;

  public paginationLoader = false;
  public pagination = 1;
  public loadMoreCheck = true;
  public clientId: number;
  public distanceMissedOpportunities = [];

  public currentJob: any;
  public statusButtonJob: any;
  public jobArray: any;

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

      this.getOpportunitiesMissed(start, end);
    });
  }

  /**
   * Reset Array
   */
  public resetArrayPagination(): void{
    this.distanceMissedOpportunities = [];
    this.missedOpportunities = [];
    this.pagination = 1;
  }

  /**
   * Load pagination
   */
  public loadPagination(start, end): void {
    this.pagination++;
    this.paginationLoader = true;
    this.getOpportunitiesMissed(start, end);
  }

  /**
   * Get opportunities new
   * @param dateStart {string}
   * @param dateEnd {string}
   * @param limit {string}
   * @return {Promise<void>}
   */
  public async getOpportunitiesMissed(dateStart: string, dateEnd: string, limit: string = '50'): Promise<void> {

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
        const response = await this._candidateService.getOpportunitiesMissed(data, this.pagination);

        this.sendDataDistanceMissedOpportunities(response).subscribe(data => {
          if(data){
            this.distanceMissedOpportunities.push(data);
          }
          this._ref.detectChanges();
        });

        if (response.pagination.total_count === this.missedOpportunities.length) {
          this.loadMoreCheck = false;
        }
        else if (response.pagination.total_count !== this.missedOpportunities.length) {
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
  public sendDataDistanceMissedOpportunities(response): Observable<any> {
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
          this.missedOpportunities.push(item);
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
    this.jobArray = jobArray;
    this.statusButtonJob = status;
  }

}
