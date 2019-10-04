import { ChangeDetectorRef, Component, OnInit} from '@angular/core';
import { CandidateService } from '../../../../services/candidate.service';
import { NgbModal } from '@ng-bootstrap/ng-bootstrap';
import { SharedService } from '../../../../services/shared.service';
import { AdminCandidateProfile, AdminCandidateUserProfileVideo } from '../../../../../entities/models-admin';
import { ToastrService } from 'ngx-toastr';
import { MapsAPILoader } from '@agm/core';
import { Observable } from 'rxjs/Observable';

@Component({
  selector: 'app-candidate-find-jobs',
  templateUrl: './candidate-find-jobs.component.html',
  styleUrls: ['./candidate-find-jobs.component.scss']
})
export class CandidateFindJobsComponent implements OnInit {

  public findJobsArray = [];
  public modalActiveClose;
  public currentlyOpenedCandidateJobId: number;
  public candidateProfileDetails: AdminCandidateProfile;

  public preloaderPage = true;
  public checkVideo: AdminCandidateUserProfileVideo;
  public checkPercentage: any;

  public paginationLoader = false;
  public pagination = 1;
  public loadMoreCheck = true;

  public distance = [];
  public job;

  constructor(
    private readonly _candidateService: CandidateService,
    private _modalService: NgbModal,
    public _sharedService: SharedService,
    private readonly _toastr: ToastrService,
    private readonly _mapsAPILoader: MapsAPILoader,
    private readonly _ref: ChangeDetectorRef
  ) {
    this._sharedService.checkSidebar = false;
  }

  ngOnInit() {
    window.scrollTo(0, 0);
    this.getCandidateProfileDetails();
  }

  /**
   * Hide status job by id for Admin
   * @param client {object}
   * @param listItems {array}
   * @return {Promise<void>}
   */
  public async hideCandidateJob(client, listItems): Promise<void> {
    try {
      await this._candidateService.hideCandidateJob(client.id);
      this._toastr.success('Job was been declined');
      const index = listItems.indexOf(client);
      listItems.splice(index, 1);
    }
    catch (err) {
      this._sharedService.showRequestErrors(err);
    }
  }

  /**
   * Load pagination
   */
  public loadPagination(): void {
    this.pagination++;
    this.paginationLoader = true;
    this.getCandidateProfileDetails();
  }

  /**
   * Get details profile candidate
   * @return {Promise<void>}
   */
  public async getCandidateProfileDetails(): Promise<void> {
    this.candidateProfileDetails = await this._candidateService.getCandidateProfileDetails();

    this.checkVideo = this.candidateProfileDetails.profile.video;
    this.checkPercentage = this.candidateProfileDetails.profile.percentage;
    if (this.checkVideo && this.checkVideo.approved && this.checkPercentage >= 50) {
      this.getJobCandidate().subscribe(data => {
        this.distance.push(data);
        this._ref.detectChanges();
      });
    }
    else {
      this.preloaderPage = false;
    }
  }

  /**
   * Get candidate job
   */
  public getJobCandidate(): Observable<any> {

    return new Observable(observer => {
      try {
        const data = this._candidateService.getJobCandidate(this.pagination).then(response => {
          response.items.forEach((item) => {
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

              this.findJobsArray.push(item);
              if (response.pagination.total_count === this.findJobsArray.length) {
                this.loadMoreCheck = false;
              }
              else if (response.pagination.total_count !== this.findJobsArray.length) {
                this.loadMoreCheck = true;
              }
            });
          });

          this.paginationLoader = false;
          this.preloaderPage = false;
        });
      }
      catch (err) {
        this._sharedService.showRequestErrors(err);
      }
    });
  }

  /**
   * opens popup
   * @param content - content to be placed within
   * @param job - job id to show within popup
   */
  public openVerticallyCentered(content: any, job) {
    this.currentlyOpenedCandidateJobId = job.id;
    this.job = job;
    this.modalActiveClose = this._modalService.open(content, { centered: true, 'size': 'lg' });
  }

}
