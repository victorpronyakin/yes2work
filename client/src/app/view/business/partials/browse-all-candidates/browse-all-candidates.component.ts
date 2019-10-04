import { Component, ElementRef, OnInit, ViewChild } from '@angular/core';
import { BusinessService } from '../../../../services/business.service';
import { SharedService } from '../../../../services/shared.service';
import { IMultiSelectOption, IMultiSelectSettings, IMultiSelectTexts } from 'angular-2-dropdown-multiselect';
import { articles } from '../../../../constants/articles.const';
import { NgbModal } from '@ng-bootstrap/ng-bootstrap';
import { CandidateByCriteria, JobCriteria } from '../../../../../entities/models';
import { CookieService } from 'ngx-cookie-service';
import { FormControl, FormGroup } from '@angular/forms';
import { IMonthCalendarConfigInternal } from 'ng2-date-picker/month-calendar/month-calendar-config';
import { ToastrService } from 'ngx-toastr';

@Component({
  selector: 'app-browse-all-candidates',
  templateUrl: './browse-all-candidates.component.html',
  styleUrls: ['./browse-all-candidates.component.scss']
})
export class BrowseAllCandidatesComponent implements OnInit {

  @ViewChild('rendering') _rendering: ElementRef;
  @ViewChild('openFilters') _openFilters: ElementRef;
  @ViewChild('openButton') _openButton: ElementRef;
  @ViewChild('filterFont') _filterFont: ElementRef;
  @ViewChild('filterItem') _filterItem: ElementRef;
  @ViewChild('contentPage') _contentPage: ElementRef;

  public articles = articles;
  public articlesFirmPredefined = [];
  public articlesFirmSelectedName: string[];
  public articlesFirmSettings: IMultiSelectSettings = {
    displayAllSelectedText: true,
    selectionLimit: 0,
    showCheckAll: true,
    showUncheckAll: true,
  };
  public genderModel = [];
  public genderOptions: IMultiSelectOption[];

  public availabilityModel = [];
  public availabilityOptions: IMultiSelectOption[];

  public ethnicityModel = [];
  public ethnicityOptions: IMultiSelectOption[];

  public locationModel = [];
  public locationOptions: IMultiSelectOption[];

  public qualificationModel = [];
  public qualificationOptions: IMultiSelectOption[];

  public nationalityModel = [];
  public nationalityOptions: IMultiSelectOption[];

  public articlesFirmOptions: IMultiSelectOption[] = [];
  public articlesFirmTextConfig: IMultiSelectTexts = {
    checkAll: 'Select all',
    uncheckAll: 'Deselect all',
    checked: 'item selected',
    checkedPlural: 'items selected',
    defaultTitle: 'Articles firm',
    allSelected: 'All selected - Articles firm',
  };
  public configGender: IMultiSelectTexts = {
    checkAll: 'Select all',
    uncheckAll: 'Deselect all',
    checked: 'item selected',
    checkedPlural: 'items selected',
    defaultTitle: 'Gender',
    allSelected: 'All selected - Gender',
  };
  public configAvailability: IMultiSelectTexts = {
    checkAll: 'Select all',
    uncheckAll: 'Deselect all',
    checked: 'item selected',
    checkedPlural: 'items selected',
    defaultTitle: 'Availability',
    allSelected: 'All selected - Availability',
  };
  public configEthnicity: IMultiSelectTexts = {
    checkAll: 'Select all',
    uncheckAll: 'Deselect all',
    checked: 'item selected',
    checkedPlural: 'items selected',
    defaultTitle: 'Ethnicity',
    allSelected: 'All selected - Ethnicity',
  };
  public configLocation: IMultiSelectTexts = {
    checkAll: 'Select all',
    uncheckAll: 'Deselect all',
    checked: 'item selected',
    checkedPlural: 'items selected',
    defaultTitle: 'Location',
    allSelected: 'All selected - Location',
  };
  public configQualification: IMultiSelectTexts = {
    checkAll: 'Select all',
    uncheckAll: 'Deselect all',
    checked: 'item selected',
    checkedPlural: 'items selected',
    defaultTitle: 'Qualification',
    allSelected: 'All selected - Qualification',
  };
  public configNationality: IMultiSelectTexts = {
    checkAll: 'Select all',
    uncheckAll: 'Deselect all',
    checked: 'item selected',
    checkedPlural: 'items selected',
    defaultTitle: 'Nationality',
    allSelected: 'All selected - Nationality',
  };

  public candidateToView: any;
  public modalActiveClose;
  public listOfJobs: JobCriteria[];
  public candidatesList = new Array<CandidateByCriteria>();
  public candidatesCountMatchingCriteria;
  public boards: string;
  public genderFilter = null;
  public qualificationFilter = null;
  public nationalityFilter = null;
  public ethnicityFilter = null;
  public locationFilter = null;
  public videoFilter = null;
  public availabilityFilter = null;
  public articlesFirmsForRequest = null;
  public checkSelectedJob = true;

  public preloaderPage = true;
  public checkOpenFilters = false;
  public totalCount: number;
  public totalCountFilter: number;
  public renderingApplicants: boolean = false;

  public paginationLoader = false;
  public pagination = 1;
  public loadMoreCheck = true;

  public filterForm: FormGroup;

  public config: IMonthCalendarConfigInternal;
  public selectedDateStart: string = '';
  public selectedDateEnd: string = '';
  public viewPopup: boolean;

  constructor(
      private readonly _businessService: BusinessService,
      private readonly _sharedService: SharedService,
      private readonly _modalService: NgbModal,
      private readonly _cookieService: CookieService,
      private readonly _toastr: ToastrService
  ) {
    this._sharedService.checkSidebar = false;

    this.genderOptions = [
      { id: 'Male', name: 'Male' },
      { id: 'Female', name: 'Female' }
    ];
    this.availabilityOptions = [
      { id: 1, name: 'Immediately' },
      { id: 2, name: 'Within 1 calendar month' },
      { id: 3, name: 'Within 3 calendar months' }
    ];
    this.ethnicityOptions = [
      { id: 'Black', name: 'Black' },
      { id: 'White', name: 'White' },
      { id: 'Coloured', name: 'Coloured' },
      { id: 'Indian', name: 'Indian' },
      { id: 'Oriental', name: 'Oriental' }
    ];
    this.locationOptions = [
      { id: 'Johannesburg', name: 'Johannesburg' },
      { id: 'Cape Town', name: 'Cape Town' },
      { id: 'Pretoria', name: 'Pretoria' },
      { id: 'Durban', name: 'Durban' },
      { id: 'International', name: 'International' },
      { id: 'Other', name: 'Other' }
    ];
    this.qualificationOptions = [
      { id: 1, name: 'Newly qualified CA' },
      { id: 2, name: 'Part qualified CA' }
    ];
    this.nationalityOptions = [
      { id: 1, name: 'South African Citizen (BBBEE)' },
      { id: 2, name: 'South African Citizen (Non-BBBEE)' },
      { id: 3, name: 'Non-South African (With Permit)' },
      { id: 4, name: 'Non-South African (Without Permit)' }
    ];
  }

  ngOnInit() {
    window.scrollTo(0, 0);
    this.filterForm = new FormGroup({
      articlesFirm: new FormControl(''),
      gender: new FormControl(''),
      ethnicity: new FormControl(''),
      qualification: new FormControl(''),
      video: new FormControl(''),
      availability: new FormControl(''),
      location: new FormControl(''),
      nationality: new FormControl(''),
      articlesCompletedStart: new FormControl(''),
      articlesCompletedEnd: new FormControl('')
    });
    this.articles.forEach((article) => {
      this.articlesFirmOptions.push({ id: article, name: article });
    });

    if (this._cookieService.get('rendering') === 'false') {
      this.renderingApplicants = false;
      this.statusRendiring(this.renderingApplicants);
    } else if(this._cookieService.get('rendering') === 'true'){
      this.renderingApplicants = true;
      this.statusRendiring(this.renderingApplicants);
    }
    else {
      this.renderingApplicants = false;
      this.statusRendiring(this.renderingApplicants);
    }

    this.fetchAllJobs().then(() => {
      this.fetchCandidatesByCriteria('', '', '', '', '', '', null, '', '', '', '').then(() => {
        this.fetchCandidatesCountByCriteria('', '', '', '', '', '', null, '', '', '', '');
        this.onResize();
        this.openVerticallyCenterPage(this._contentPage);
      });
    });
  }

  /**
   * Reset filters
   */
  public resetFilterForm(): void{
    try {
      this.articlesFirmPredefined = [];
      this.articlesFirmSelectedName = [];
      this.genderModel = [];
      this.availabilityModel = [];
      this.ethnicityModel = [];
      this.locationModel = [];
      this.qualificationModel = [];
      this.nationalityModel = [];
      // this.articlesFirmOptions = [];

      this.filterForm.setValue({
        search: '',
        articlesFirm: [],
        gender: [],
        ethnicity: [],
        qualification: [],
        video: null,
        availability: [],
        location: [],
        nationality: [],
        articlesCompletedStart: '',
        articlesCompletedEnd: ''
      });

      setTimeout(() => {
        this.fetchCandidatesCountByCriteria('', '', '', '', '', '', null, '', '', '', '');
      }, 500);
    }
    catch (err) {
      this._sharedService.showRequestErrors(err);
    }
  }

  /**
   * Hide articles firm
   * @param elem
   */
  public hideArticlesFirm(elem): void {
    let nextSibling = elem.nextSibling;
    while(nextSibling && nextSibling.nodeType != 1) {
      nextSibling = nextSibling.nextSibling
    }
    nextSibling.style.opacity = 0;
    elem.style.opacity = 1;
  }

  /**
   * Reset Array
   */
  public resetArrayPagination(): void{
    this.candidatesList = [];
    this.pagination = 1;
    this._openButton.nativeElement.innerHTML = 'Open more filters';
    this._openFilters.nativeElement.classList.remove('active');
    this._filterFont.nativeElement.classList.remove('active');
    this._filterItem.nativeElement.classList.remove('active');
  }

  /**
   * Load pagination
   */
  public loadPagination(search, articlesFirm, gender, qualification, nationality, ethnicity, video, location, availability, articlesCompletedStart, articlesCompletedEnd): void {
    this.pagination++;
    this.paginationLoader = true;
    this.fetchCandidatesByCriteria(search, articlesFirm, gender, qualification, nationality, ethnicity, video, location, availability, articlesCompletedStart, articlesCompletedEnd);
  }

  /**
   * Open more filters
   */
  public openMoreFilters(): void {

    this.checkOpenFilters = !this.checkOpenFilters;

    if (this.checkOpenFilters === true) {
      this._openButton.nativeElement.innerHTML = 'Close more filters';
      this._openFilters.nativeElement.classList.add('active');
      this._filterFont.nativeElement.classList.add('active');
      this._filterItem.nativeElement.classList.add('active');
    }
    else {
      this._openButton.nativeElement.innerHTML = 'Open more filters';
      this._openFilters.nativeElement.classList.remove('active');
      this._filterFont.nativeElement.classList.remove('active');
      this._filterItem.nativeElement.classList.remove('active');
    }
  }

  /**
   * Resize window
   */
  public onResize(): void{
    if(window.innerWidth <= 1024){
      this.renderingApplicants = false;
      this.statusRendiring(false);
    }
  }

  /**
   * Status rendering
   * @param status {boolean}
   */
  public statusRendiring(status: boolean): void {
    if (status === true){
      this._rendering.nativeElement.classList.remove('cell-applicant');
    }
    else if (status === false) {
      this._rendering.nativeElement.classList.add('cell-applicant');
    }
    this._cookieService.set('rendering', String(status));
    this.renderingApplicants = status;
  }

  /**
   * fetches list of jobs
   * @returns void
   */
  public async fetchAllJobs(): Promise<void> {
    try {
      const response = await this._businessService.getBusinessJobsMatchingCriteria(false, null);
      this.listOfJobs = response;
    }
    catch (err){
      this._sharedService.showRequestErrors(err);
    }
  }

  /**
   * fetches all candidates matching criteria
   * @param search {string}
   * @param articlesFirm {string}
   * @param gender {string}
   * @param qualification {integer}
   * @param nationality {integer}
   * @param ethnicity {string}
   * @param video {integer]
   * @param location {string}
   * @param availability {integer}
   * @param articlesCompletedStart {string}
   * @param articlesCompletedEnd {string}
   */
  public async fetchCandidatesByCriteria(search, articlesFirm, gender, qualification, nationality, ethnicity, video, location, availability, articlesCompletedStart, articlesCompletedEnd): Promise<void> {
    if (this.genderOptions.length === this.genderModel.length) {
      gender = 'All';
    }

    if (this.qualificationOptions.length === this.qualificationModel.length) {
      qualification = 'All';
    }

    if (this.nationalityOptions.length === this.nationalityModel.length) {
      nationality = 'All';
    }

    if (this.ethnicityOptions.length === this.ethnicityModel.length) {
      ethnicity = 'All';
    }

    if (this.locationOptions.length === this.locationModel.length) {
      location = 'All';
    }

    if (this.availabilityOptions.length === this.availabilityModel.length) {
      availability = 'All';
    }

    const startDate = new Date(articlesCompletedStart);
    const endDate = new Date(articlesCompletedEnd);
    if (startDate > endDate) {
      this._toastr.error('Date Articles Completed End not be shorter than the Date Articles Completed Start');
    }
    else{
      try {
        /*this.resetArrayPagination();*/
        const response = await this._businessService.getBusinessCandidatesMatchingCriteria(search, articlesFirm, gender, qualification, nationality , ethnicity, video, location, availability, this.pagination, articlesCompletedStart, articlesCompletedEnd);
        response.items.forEach((item) => {
          item.dateAvailability = this._sharedService.getCandidateAvailabilityInHumanReadableForm(
            item.availability, item.availabilityPeriod, item.dateAvailability
          );
          this.candidatesList.push(item);
        });

        this.candidatesCountMatchingCriteria = response.pagination.total_count;

        if (response.pagination.total_count === this.candidatesList.length) {
          this.loadMoreCheck = false;
        }
        else if (response.pagination.total_count !== this.candidatesList.length) {
          this.loadMoreCheck = true;
        }
        this.paginationLoader = false;

        this.candidatesList.forEach((candidate) => {
          candidate['availabilityInHumanReadableForm'] = this._sharedService.getCandidateAvailabilityInHumanReadableForm(
            candidate.availability, candidate.availabilityPeriod, candidate.dateAvailability);
          candidate['boardsInHumanReadableForm'] = this._sharedService.getBoardsInHumanReadableForm(candidate.boards);
        });
        this._openButton.nativeElement.innerHTML = 'Open more filters';
        this._openFilters.nativeElement.classList.remove('active');
        this._filterFont.nativeElement.classList.remove('active');
        this._filterItem.nativeElement.classList.remove('active');
        this.checkOpenFilters = false;
        setTimeout(() => {
          this.preloaderPage = false;
        }, 2000);
      }
      catch (error) {
        this._sharedService.showRequestErrors(error)
      }
    }
  }

  /**
   * opens popup
   * @param content - content to be placed within
   * @param viewPopup - content to be placed within
   * @param candidate - candidateId id to show within popup
   */
  public openVerticallyCentered(content: any, candidate, viewPopup) {
    this.viewPopup = viewPopup;
    this.candidateToView = candidate;
    this.modalActiveClose = this._modalService.open(content, { centered: true, size: 'lg', windowClass: 'xlModal' });
  }

  /**
   * opens popup
   * @param content - content to be placed within
   */
  public openVerticallyCenter(content: any) {
    this.modalActiveClose = this._modalService.open(content, { centered: true, size: 'lg', windowClass: 'xlModal' });
  }

  /**
   * opens popup
   * @param content - content to be placed within
   */
  public openVerticallyCenterPage(content: any) {
    this.modalActiveClose = this._modalService.open(content, { centered: true });
  }

  /**
   * fetches candidate by criteria
   * @param search {string}
   * @param articlesFirm {string}
   * @param gender {string}
   * @param qualification {integer}
   * @param nationality {integer}
   * @param ethnicity {string}
   * @param video {integer]
   * @param location {string}
   * @param availability {integer}
   * @param articlesCompletedStart {string}
   * @param articlesCompletedEnd {string}
   */
  public fetchCandidatesCountByCriteria(search, articlesFirm?, gender?, qualification?, nationality?, ethnicity?, video?, location?, availability?, articlesCompletedStart?, articlesCompletedEnd?): void {

    if (this.genderOptions.length === this.genderModel.length) {
      gender = 'All';
    }

    if (this.qualificationOptions.length === this.qualificationModel.length) {
      qualification = 'All';
    }

    if (this.nationalityOptions.length === this.nationalityModel.length) {
      nationality = 'All';
    }

    if (this.ethnicityOptions.length === this.ethnicityModel.length) {
      ethnicity = 'All';
    }

    if (this.locationOptions.length === this.locationModel.length) {
      location = 'All';
    }

    if (this.availabilityOptions.length === this.availabilityModel.length) {
      availability = 'All';
    }

    const startDate = new Date(articlesCompletedStart);
    const endDate = new Date(articlesCompletedEnd);
    if (startDate > endDate) {
      this._toastr.error('Date Articles Completed End not be shorter than the Date Articles Completed Start');
    }
    // else{
    //   this._businessService.getBusinessCandidatesCount(
    //     search, articlesFirm, gender, qualification, nationality, ethnicity, video, location, availability, articlesCompletedStart, articlesCompletedEnd)
    //     .then((data) => { this.totalCountFilter = data.countCandidate; })
    //     .catch((error) => { this._sharedService.showRequestErrors(error); });
    // }
  }

  /**
   * sets articles firm criteria
   * @param search
   * @param event
   * @param gender {string}
   * @param qualification {integer}
   * @param nationality {integer}
   * @param ethnicity {string}
   * @param video {integer}
   * @param location {string}
   * @param availability {integer}
   * @param articlesCompletedStart {string}
   * @param articlesCompletedEnd {string}
   * @returns void
   */
  public async specifiedArticlesFirmCriteria(search, event, gender?, qualification?, nationality?, ethnicity?, video?, location?, availability?, articlesCompletedStart?, articlesCompletedEnd?): Promise<void> {

    if (this.genderOptions.length === this.genderModel.length) {
      gender = 'All';
    }

    if (this.qualificationOptions.length === this.qualificationModel.length) {
      qualification = 'All';
    }

    if (this.nationalityOptions.length === this.nationalityModel.length) {
      nationality = 'All';
    }

    if (this.ethnicityOptions.length === this.ethnicityModel.length) {
      ethnicity = 'All';
    }

    if (this.locationOptions.length === this.locationModel.length) {
      location = 'All';
    }

    if (this.availabilityOptions.length === this.availabilityModel.length) {
      availability = 'All';
    }

    if (this.checkSelectedJob === true) {
      if (this.articlesFirmSelectedName === null){
        this.articlesFirmSelectedName = [];
        this.articlesFirmPredefined = [];
      }
      else{
        this.articlesFirmSelectedName = (this.articlesFirmPredefined.length === this.articles.length)
          ? new Array('All')
          : this.articlesFirmPredefined;
      }

      const startDate = new Date(articlesCompletedStart);
      const endDate = new Date(articlesCompletedEnd);
      if (startDate > endDate) {
        this._toastr.error('Date Articles Completed End not be shorter than the Date Articles Completed Start');
      }
      else{
        try {
          // const data = await this._businessService.getBusinessCandidatesCount(search, this.articlesFirmSelectedName.join(','), gender, qualification, nationality, ethnicity, video, location, availability, articlesCompletedStart, articlesCompletedEnd);
          // this.totalCountFilter = data.countCandidate;
        }
        catch (err) {
          this._sharedService.showRequestErrors(err);
        }
      }
    }
    this.checkSelectedJob = true;
  }

  /**
   * sets candidate search filters according to selected job
   * @param jobId {number} - job id
   * @returns void
   */
  /*public setFiltersOnJobChange(jobId: any): void {
    if (jobId === 'null') {
      return;
    }
    const selectedJob = this.listOfJobs.filter((job) => job.id === +jobId);
    this.checkSelectedJob = false;
    if(selectedJob[0].articlesFirm.length > 0 && selectedJob[0].articlesFirm[0] === "All"){
        this.articlesFirmPredefined = this.articles;
    }
    else{
        this.articlesFirmPredefined = selectedJob[0].articlesFirm;
    }
    this.genderFilter = selectedJob[0].gender;
    this.qualificationFilter = selectedJob[0].qualification;
    this.nationalityFilter = selectedJob[0].nationality;
    this.ethnicityFilter = selectedJob[0].ethnicity;
    this.locationFilter = selectedJob[0].location;
    this.videoFilter = selectedJob[0].video;
    this.availabilityFilter = selectedJob[0].availability;
    this.articlesFirmSelectedName = (this.articlesFirmPredefined.length === this.articles.length)
        ? new Array('All')
        : this.articlesFirmPredefined;
    this.fetchCandidatesCountByCriteria(
        '', this.articlesFirmSelectedName.join(','), this.genderFilter, this.qualificationFilter, this.nationalityFilter,
        this.ethnicityFilter, this.videoFilter, this.locationFilter, this.availabilityFilter, this.selectedDateStart, this.selectedDateEnd);
    /!*this.handleSearch(this.articlesFirmSelectedName.join(','), this.genderFilter, this.qualificationFilter, this.nationalityFilter,
      this.ethnicityFilter, this.videoFilter, this.locationFilter, this.availabilityFilter);*!/
  }*/
}
