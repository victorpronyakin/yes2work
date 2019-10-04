import {
  AfterViewInit, ChangeDetectorRef, Component, ElementRef, EventEmitter, Input, OnInit, Output,
  ViewChild
} from '@angular/core';
import {CandidateService} from '../../../../services/candidate.service';
import {SharedService} from '../../../../services/shared.service';
import {ToastrService} from 'ngx-toastr';
import {NgbModal} from '@ng-bootstrap/ng-bootstrap';
import 'rxjs/add/observable/timer';
import 'rxjs/add/operator/map';
import 'rxjs/add/operator/take';
import {Router} from '@angular/router';
import {AdminCandidateProfile} from '../../../../../entities/models-admin';

@Component({
  selector: 'app-candidate-video',
  templateUrl: './candidate-video.component.html',
  styleUrls: ['./candidate-video.component.scss']
})
export class CandidateVideoComponent implements OnInit, AfterViewInit {
  @ViewChild('videoBlock') public videoBlock: ElementRef;
  @ViewChild('videoPlayer') public videoPlayer: ElementRef;
  @ViewChild('content') public content: ElementRef;
  @ViewChild('notDevicesNotAccess') public notDevicesNotAccess: ElementRef;
  @ViewChild('errorBrowser') public errorBrowser: ElementRef;
  @ViewChild('iframe') public iframe: ElementRef;
  @ViewChild('ziggeorecorder') private ziggeorecorder: any;

  @Input() constrains = {video: true, audio: true};
  @Input() fileName = 'my_recording';
  @Input() showVideoPlayer = true;

  @Output() startRecording = new EventEmitter();
  @Output() downloadRecording = new EventEmitter();
  @Output() fetchRecording = new EventEmitter();

  public videoObject: any;

  public preloaderPage = true;
  public buttonPreloader = false;
  public preloaderVideo = false;
  public preloaderNewVideo = false;
  public modalActiveClose: any;

  public format = 'video/webm';
  public _navigator = <any> navigator;
  public video;
  public videoRecordPopup = false;
  public videoUploadPopup = false;
  public videoRecordStatus = false;
  public tick = 1000;

  public candidateProfileDetails: AdminCandidateProfile;
  public ziggeoTitle: string;
  public visibilityLooking = false;
  public checkLooking: boolean;
  public videoUploadPopups = false;
  public visibleActivePopup = false;
  public finishButton = false;

  public checkVideo;
  public allowVideo;
  public checkBr: string;
  public recorder: any;

  constructor(
    public readonly _candidateService: CandidateService,
    public readonly _sharedService: SharedService,
    public readonly _toastr: ToastrService,
    public readonly _modalService: NgbModal,
    private readonly ref: ChangeDetectorRef,
    private readonly _router: Router
  ) {
    this._sharedService.checkSidebar = false;
  }

  ngOnInit() {
    window.scrollTo(0, 0);
    if (this._navigator.getUserMedia !== undefined) {
      this._navigator.getUserMedia = (this._navigator.mediaDevices.getUserMedia || this._navigator.getUserMedia || this._navigator.webkitGetUserMedia
        || this._navigator.mozGetUserMedia || this._navigator.msGetUserMedia);
    }

    this.getCandidateVideo().then(() => {
      this.getCandidateProfile();
    });

    this._sharedService.progressBar = Number(localStorage.getItem('progressBar'));
  }

  ngAfterViewInit () {
    this.recorder = this.ziggeorecorder.recorderInstance;
    
    this.recorder.on('processed', () => {
      this.finishButton = true;
      this.ref.detectChanges();
      const token = this.recorder.get('video');
      this.uploadVideo(token);
    }, this);
  
    this.recorder.on('rerecord', () => {
      this.finishButton = false;
      this.ref.detectChanges();
    }, this);
  }

  /**
   * Change status candidate
   * @param field {string}
   * @param value {boolean}
   */
  public changeStatusCandidate(field: string, value: boolean) {
    let error = true;
    if (this.candidateProfileDetails.profile.percentage < 50) {
      this._toastr.error('Your profile needs to be 50% complete');
      error = false;
      if (field === 'looking') {
        this.checkLooking = false;
      }
    }
    if (!this.candidateProfileDetails.profile.copyOfID || this.candidateProfileDetails.profile.copyOfID.length === 0) {
      this._toastr.error('Upload a copy of your ID in Edit Profile');
      error = false;
      if (field === 'looking') {
        this.checkLooking = false;
      }
    }
    if (this.candidateProfileDetails.profile.copyOfID[0] && !this.candidateProfileDetails.profile.copyOfID[0].approved) {
      this._toastr.error('Copy of your ID file is not approved by the administrator');
      error = false;
      if (field === 'looking') {
        this.checkLooking = false;
      }
    }
    if (!this.candidateProfileDetails.profile.video && this.candidateProfileDetails.allowVideo === false) {
      this._toastr.error('You need to upload video');
      error = false;
      if (field === 'looking') {
        this.checkLooking = false;
      }
    }
    if (this.candidateProfileDetails.profile.video && !this.candidateProfileDetails.profile.video.approved && this.candidateProfileDetails.allowVideo === false) {
      this._toastr.error('Your video is not approved by the administrator');
      error = false;
      if (field === 'looking') {
        this.checkLooking = false;
      }
    }
    if (error === true) {
      if (field === 'looking' && value === false) {
        this.closeLookingPopup(true, false);
      } else {
        const data = {[field]: value};

        this._candidateService.changeStatusCandidate(data).then(data => {
          this.checkLooking = data.looking;
          if (field === 'looking') {
            this._toastr.success('Your profile is now active');
          }
        }).catch(err => {
          this._sharedService.showRequestErrors(err);
        });
      }
    }
  }

  /**
   * Status looking popup
   * @param value
   * @param check
   */
  public closeLookingPopup(value, check) {
    this.videoUploadPopups = value;
    this.checkLooking = check;
  }

  /**
   * Status visible popup
   * @param value
   * @param check
   */
  public closeVisiblePopup(value, check) {
    this.visibleActivePopup = value;
  }

  /**
   * Send request looking job
   * @param field
   * @param value
   */
  public lookingJobToggle(field: string, value: boolean) {
    this.checkLooking = false;
    const data = {[field]: value};

    this._candidateService.changeStatusCandidate(data).then(data => {
      this.checkLooking = data.looking;
      this._toastr.error('Your profile is now disabled');
    }).catch(err => {
      this._sharedService.showRequestErrors(err);
    });
  }

  /**
   * Select change router
   * @param url
   */
  public routerApplicants(url): void {
    this._router.navigate([url]);
  }

  /**
   * Get candidate profile
   * @returns {Promise<void>}
   */
  public async getCandidateProfile(): Promise<any> {
    try {
      const data = await this._candidateService.getCandidateProfileDetails();
      this._sharedService.progressBar = data.profile.percentage;
      this.checkVideo = data.profile.video;
      this.allowVideo = data['allowVideo'];
      this.candidateProfileDetails = data;
      this.ziggeoTitle = data.user.firstName + '-' + data.user.id;
      if (this.candidateProfileDetails.profile.percentage < 50 || !this.candidateProfileDetails.profile.copyOfID ||
        !this.candidateProfileDetails.profile.copyOfID[0] ||
        !this.candidateProfileDetails.profile.copyOfID[0].approved ||
        (this.candidateProfileDetails.allowVideo === false && !this.candidateProfileDetails.profile.video) ||
        (this.candidateProfileDetails.allowVideo === false && this.candidateProfileDetails.profile.video && this.candidateProfileDetails.profile.video.approved === false)) {
        this.checkLooking = false;
        this.visibilityLooking = true;
      } else {
        this.checkLooking = this.candidateProfileDetails.profile.looking;
      }
    } catch (err) {
      this._sharedService.showRequestErrors(err);
    }
  }

  /**
   * Step to next page
   */
  public stepNextPage(): void {
    this._router.navigate(['/candidate/preferences']);
  }

  public openVideoPopup(): void {
    this.recorder.reset();
    this.finishButton = false;
    this.videoRecordPopup = true;
    this.ref.detectChanges();
  }
  
  public closeVideoPopup () {
    this.videoRecordPopup = false;
    this.ref.detectChanges();
  }

  public openUploadPopup(): void {
    this.videoUploadPopup = !this.videoUploadPopup;
  }

  /**
   * Upload candidate video
   * @return {Promise<void>}
   */
  public async uploadVideo(token: string): Promise<void> {
    this._candidateService.uploadVideo(token).then(video => {
      this.videoObject = video.video;
    });
  }

  /**
   * Remove candidate video
   * @return {Promise<void>}
   */
  public async removeVideo(): Promise<void> {
    this.preloaderVideo = true;
    this.modalActiveClose.dismiss();
    try {
      const response = await this._candidateService.removeVideo();

      this.videoObject = null;

      localStorage.setItem('progressBar', response.percentage);
      this._sharedService.progressBar = Number(localStorage.getItem('progressBar'));
      this.preloaderVideo = false;
      this._toastr.success('Video has been removed');
      this._sharedService.visibleErrorVideoIcon = true;
      this.candidateProfileDetails.profile.video = null;
      this.checkLooking = response.looking;
    } catch (err) {
      this.preloaderVideo = false;
      this._sharedService.showRequestErrors(err);
    }
  }

  /**
   * Get candidate video
   * @return {Promise<void>}
   */
  public async getCandidateVideo(): Promise<void> {
    try {
      const result = await this._candidateService.getCandidateVideo();
      this.videoObject = result.video;
      this.preloaderPage = false;
    } catch (err) {
      this._sharedService.showRequestErrors(err);
    }
  }

  /**
   * Open modal
   * @param content
   */
  public openVerticallyError(content) {
    this.modalActiveClose = this._modalService.open(content, {centered: true});
  }

  /**
   * Open modal
   * @param content
   */
  public openVerticallyCentered(content) {
    this.modalActiveClose = this._modalService.open(content, {centered: true, size: 'lg'});
  }

  /**
   * Open modal
   * @param content
   */
  public openVerticallyCenter(content) {
    this.modalActiveClose = this._modalService.open(content, {centered: true, size: 'sm', windowClass: 'width-min'});
  }

}
