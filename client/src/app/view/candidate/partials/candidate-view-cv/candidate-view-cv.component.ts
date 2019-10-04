import { Component, ElementRef, OnInit, ViewChild } from '@angular/core';
import { SharedService } from '../../../../services/shared.service';
import { CandidateService } from '../../../../services/candidate.service';
import { AdminCandidateProfile } from '../../../../../entities/models-admin';
import { NgbModal } from '@ng-bootstrap/ng-bootstrap';

@Component({
  selector: 'app-candidate-view-cv',
  templateUrl: './candidate-view-cv.component.html',
  styleUrls: ['./candidate-view-cv.component.scss']
})
export class CandidateViewCvComponent implements OnInit {

  @ViewChild('videoPlayer') videoPlayer: ElementRef;
  @ViewChild('removeShortList') btnRemoveShortList: ElementRef;
  public candidate: AdminCandidateProfile;
  public nationality: string;
  public academicCertificates: object[] = [];
  public academicTranscripts: object[] = [];
  public creditChecks: object[] = [];
  public availability: string;
  public boards: string;
  public cv: object[] = [];
  public modalActiveClose: any;
  public preloaderPage = true;

  public criminalMore = false;
  public creditMore = false;
  public achievementsArray = [];
  public referencesArray = [];

  constructor(
    private readonly _candidateService: CandidateService,
    private readonly  _sharedService: SharedService,
    private readonly _modalService: NgbModal
  ) {
    this._sharedService.checkSidebar = false;
  }

  ngOnInit() {
   /* window.scrollTo(0, 0);
    this.getCandidateAchievement().then(response => {
      this.getCandidateReferences().then(() => {
        this.getSpecificCandidateProfile();
      });
    });*/
  }
/*
  /!**
   * Hide articles firm
   * @param elem
   *!/
  public hideArticlesFirm(elem): void {
    let nextSibling = elem.nextSibling;
    while(nextSibling && nextSibling.nodeType != 1) {
      nextSibling = nextSibling.nextSibling
    }
    nextSibling.style.opacity = 0;
    elem.style.opacity = 1;
  }

  /!**
   * Get candidate achievements
   * @return {Promise<void>}
   *!/
  public async getCandidateAchievement(): Promise<void> {
    this.achievementsArray = await this._candidateService.getCandidateAchievement();
  }

  /!**
   * Get candidate references
   * @return {Promise<void>}
   *!/
  public async getCandidateReferences(): Promise<void> {
    try {
      this.referencesArray = await this._candidateService.getCandidateReferences();
    }
    catch (err) {
      this._sharedService.showRequestErrors(err);
    }
  }

  public moreCriminal(): void{
    this.criminalMore = !this.criminalMore;
  }

  public moreCredit(): void{
    this.creditMore = !this.creditMore;
  }

  /!**
   * gets candidate details specified with id
   * @returns void
   *!/
  public async getSpecificCandidateProfile(): Promise<void> {
    try {
      this.candidate = await this._candidateService.getCandidateProfileDetails();

      const nationality = this._sharedService.getNationalityInHumanReadableForm(this.candidate.profile.nationality);
      this.nationality = (nationality) ? nationality : '-';
      this.availability = this._sharedService.getCandidateAvailabilityInHumanReadableForm(
        this.candidate.profile.availability, this.candidate.profile.availabilityPeriod, String(this.candidate.profile.dateAvailability)
      );
      this.boards = this._sharedService.getBoardsInHumanReadableForm(this.candidate.profile.boards);
      const metricCertificates = this.candidate.profile.matricCertificate;
      const tertiaryCertificates = this.candidate.profile.tertiaryCertificate;
      if(metricCertificates && tertiaryCertificates){
        this.academicCertificates = metricCertificates.concat(tertiaryCertificates);
      }
      else if(metricCertificates){
        this.academicCertificates = metricCertificates;
      }
      else if(tertiaryCertificates){
        this.academicCertificates = tertiaryCertificates;
      }
      this.academicCertificates = this.academicCertificates.filter((certificate) => certificate['approved'] === true);
      if(this.candidate.profile.universityManuscript){
        this.academicTranscripts = this.candidate.profile.universityManuscript.filter((certificate) => certificate['approved'] === true);
      }
      if(this.candidate.profile.creditCheck){
        this.creditChecks = this.candidate.profile.creditCheck.filter((check) => check.approved === true);
      }
      if(this.candidate.profile.cvFiles){
        this.cv = this.candidate.profile.cvFiles;
      }
      setTimeout(() => {
        this.preloaderPage = false;
      }, 1000);
      this.videoPlayer.nativeElement.load();


    }
    catch (err) {
      this._sharedService.showRequestErrors(err);
    }
  }

  /!**
   * Open modal
   * @param content
   *!/
  public openVerticallyCentered(content) {
    this.modalActiveClose = this._modalService.open(content, { centered: true, 'size': 'lg' });
  }*/

}
