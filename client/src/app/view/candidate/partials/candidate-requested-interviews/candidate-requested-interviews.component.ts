import { ChangeDetectorRef, Component, OnInit } from '@angular/core';
import { INgxMyDpOptions } from 'ngx-mydatepicker';
import { CandidateService } from '../../../../services/candidate.service';
import { ToastrService } from 'ngx-toastr';
import { SharedService } from '../../../../services/shared.service';
import { CandidateOpportunities } from '../../../../../entities/models';
import { NgbModal } from '@ng-bootstrap/ng-bootstrap';
import { ActivatedRoute } from '@angular/router';
import { MapsAPILoader } from '@agm/core';
import { Observable } from 'rxjs/Observable';

@Component({
  selector: 'app-candidate-requested-interviews',
  templateUrl: './candidate-requested-interviews.component.html',
  styleUrls: ['./candidate-requested-interviews.component.scss']
})
export class CandidateRequestedInterviewsComponent implements OnInit {

  public myOptionsDate: INgxMyDpOptions = { dateFormat: 'mm.dd.yyyy' };
  public model: any = { date: { year: 2018, month: 10, day: 9 } };

  public interviewsRequest = Array<CandidateOpportunities>();
  public distanceInterviewsRequest = [];
  public modelStartDate: any;
  public modelEndDate: any;
  public modalActiveClose: any;
  public preloaderPage = true;
  public paginationLoader = false;
  public pagination = 1;
  public loadMoreCheck = true;

  public currentJob: any;
  public statusButtonJob: any;
  public jobArray: any;

  constructor(
    private readonly _candidateService: CandidateService,
    private readonly _toastr: ToastrService,
    private readonly _sharedService: SharedService,
    private readonly _modalService: NgbModal,
    private readonly _mapsAPILoader: MapsAPILoader,
    private readonly _ref: ChangeDetectorRef
  ) {
    this._sharedService.checkSidebar = false;
  }

  ngOnInit() {
    window.scrollTo(0, 0);
    this.getInterviewsRequest('', '', 0);
  }

  /**
   * Reset Array
   */
  public resetArrayPagination(): void{
    this.distanceInterviewsRequest = [];
    this.interviewsRequest = [];
    this.pagination = 1;
  }

  /**
   * Load pagination
   * @param start {date}
   * @param end {date}
   * @param status {number}
   */
  public loadPagination(start, end, status): void {
    if(!status || status === null) {
      status = 0;
    }
    this.pagination++;
    this.paginationLoader = true;
    this.getInterviewsRequest(start, end, status);
  }

  /**
   * Get application applied
   * @param dateStart {string}
   * @param dateEnd {string}
   * @param status {string}
   * @return {Promise<void>}
   */
  public async getInterviewsRequest(dateStart: string, dateEnd: string, status): Promise<void> {
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
          status: status
        };
        const response = await this._candidateService.getInterviewsRequest(data, this.pagination);

        this.sendDataDistanceAwaitingOpportunities(response).subscribe(data => {
          if(data){
            this.distanceInterviewsRequest.push(data);
          }
          this._ref.detectChanges();
        });

        if (response.pagination.total_count === this.interviewsRequest.length) {
          this.loadMoreCheck = false;
        }
        else if (response.pagination.total_count !== this.interviewsRequest.length) {
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
  public sendDataDistanceAwaitingOpportunities(response): Observable<any> {
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
          this.interviewsRequest.push(item);
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
