import { Component, ElementRef, Input, OnInit, ViewChild } from '@angular/core';
import { FormControl, FormGroup } from '@angular/forms';
import { ApplicantsList, BusinessApplicant, BusinessCandidate, JobCriteria } from '../../../../../entities/models';
import { IMonthCalendarConfigInternal } from 'ng2-date-picker/month-calendar/month-calendar-config';
import { IMultiSelectOption, IMultiSelectSettings } from 'angular-2-dropdown-multiselect';
import { SharedService } from '../../../../services/shared.service';
import { BusinessService } from '../../../../services/business.service';
import { NgbModal } from '@ng-bootstrap/ng-bootstrap';
import { ToastrService } from 'ngx-toastr';
import { ActivatedRoute, Router } from '@angular/router';
import { CookieService } from 'ngx-cookie-service';
import { LabelType, Options } from 'ng5-slider';

@Component({
  selector: 'app-business-applicant-view',
  templateUrl: './business-applicant-view.component.html',
  styleUrls: ['./business-applicant-view.component.scss']
})
export class BusinessApplicantViewComponent implements OnInit {
  @Input() viewPage: number;

  @ViewChild('search') public search: ElementRef;
  @ViewChild('rendering') _rendering: ElementRef;
  @ViewChild('openFilters') _openFilters: ElementRef;
  @ViewChild('openButton') _openButton: ElementRef;
  @ViewChild('filterFont') _filterFont: ElementRef;
  @ViewChild('filterItem') _filterItem: ElementRef;

  public applicantsData = new Array<BusinessApplicant>();

  public listOfJobs: JobCriteria[];
  public applicantsList: ApplicantsList;

  public modalActiveClose;
  public candidateToView;
  public requestJobId: any;
  public preloaderPage = true;
  public totalCount = {
    number: 0
  };
  public totalCountFilter: number;
  public renderingApplicants: boolean;
  public articlesFirmPredefined: string[] = [];
  public articlesFirmSelectedName: string[];
  public articlesFirmSettings: IMultiSelectSettings = {
    displayAllSelectedText: true,
    selectionLimit: 0,
    showCheckAll: true,
    showUncheckAll: true,
  };
  public specializationSettings: IMultiSelectSettings = {
    displayAllSelectedText: true,
    enableSearch: true,
    selectionLimit: 0,
    showCheckAll: true,
    showUncheckAll: true,
  };
  public articlesFirmOptions: IMultiSelectOption[] = [];
  public checkOpenFilters = false;
  public paginationLoader = false;
  public paginationFilter = false;
  public pagination = 1;
  public loadMoreCheck = true;
  public filterForm: FormGroup;
  public config: IMonthCalendarConfigInternal;
  public selectedDateStart: string = '';
  public selectedDateEnd: string = '';
  public genderModel = [];
  public availabilityModel = [];
  public ethnicityModel = [];
  public locationModel = [];
  public qualificationLevelModel = [];
  public tertiaryEducationModel = [];
  public specializationModel = [];
  public yearsWorkModel = [];

  public genderOptions: IMultiSelectOption[];
  public availabilityOptions: IMultiSelectOption[];
  public ethnicityOptions: IMultiSelectOption[];
  public locationOptions: IMultiSelectOption[];
  public qualificationLevelOptions: IMultiSelectOption[];
  public tertiaryEducationOptions: IMultiSelectOption[];
  public specializationOptions: IMultiSelectOption[];
  public yearsWorkOptions: IMultiSelectOption[];

  public orderBy: string = '';
  public orderSort: boolean;
  public copyFilter: any;
  public candidate: BusinessCandidate;
  public listOfJobsCount: JobCriteria[];

  public minValue: number = null;
  public maxValue: number = null;
  public options: Options;
  public checkInitChange = false;

  constructor(
    private readonly _businessService: BusinessService,
    public readonly _sharedService: SharedService,
    private readonly _modalService: NgbModal,
    private readonly _route: ActivatedRoute,
    private readonly _cookieService: CookieService,
    private readonly _router: Router,
    private readonly _toastr: ToastrService
  ) {
    this._sharedService.checkSidebar = false;

    this._route.queryParams.subscribe(data => {
      if (data.jobId !== undefined) {
        this.requestJobId = Number(data.jobId);
      }
      else {
        this.requestJobId = null;
      }
    });

    this.genderOptions = this._sharedService.genderOptions;
    this.availabilityOptions = this._sharedService.availabilityOptions;
    this.ethnicityOptions = this._sharedService.ethnicityOptionsYes;
    this.locationOptions = this._sharedService.citiesWorking;
    this.qualificationLevelOptions = this._sharedService.configQualificationLevel;
    this.tertiaryEducationOptions = this._sharedService.configTertiaryEducation;
    this.specializationOptions = this._sharedService.specializationCandidate;
    this.yearsWorkOptions = this._sharedService.configYearsWork;

  }

  ngOnInit() {
    window.scrollTo(0, 0);

    this.filterForm = new FormGroup({
      gender: new FormControl(''),
      ethnicity: new FormControl(''),
      location: new FormControl(''),
      availability: new FormControl(''),
      video: new FormControl(null),
      highestQualification: new FormControl(''),
      field: new FormControl(''),
      orderBy: new FormControl(''),
      orderSort: new FormControl(''),
      eligibility: new FormControl('applicable'),
      yearsOfWorkExperience: new FormControl(''),
      assessmentCompleted: new FormControl(null),
    });

    if (this._cookieService.get('rendering') === 'false') {
      this.renderingApplicants = false;
      this.statusRendiring(this.renderingApplicants);
    } else if (this._cookieService.get('rendering') === 'true'){
      this.renderingApplicants = true;
      this.statusRendiring(this.renderingApplicants);
    } else {
      this.renderingApplicants = false;
      this.statusRendiring(this.renderingApplicants);
    }

    this.fetchListOfApplicants().then(response => {
      this.fetchAllJobs().then(() => {
        this.onResize();
      });
    });
    setTimeout(() => {
      this.checkInitChange = true;
    }, 1000);
  }

  /**
   * Search count
   * @return {Promise<void>}
   */
  public async getApplicantsCount(): Promise<void> {

    if (this.checkInitChange) {
      const data = {
        jobID: this.requestJobId,
        page: this.pagination,
        limit: 50,
        search: this.search.nativeElement.value,
        gender: this.checkingFormControl(this.genderModel, this._sharedService.genderOptions),
        ethnicity: this.checkingFormControl(this.ethnicityModel, this._sharedService.ethnicityOptions),
        location: this.checkingFormControl(this.locationModel, this._sharedService.citiesWorking),
        highestQualification: this.checkingFormControl(this.qualificationLevelModel, this._sharedService.configQualificationLevel),
        field: this.checkingFormControl(this.specializationModel, this._sharedService.specializationCandidate),
        availability: this.checkingFormControl(this.availabilityModel, this._sharedService.availabilityOptions),
        yearsOfWorkExperience: this.checkingFormControl(this.yearsWorkModel, this._sharedService.configYearsWork),
        video: this.filterForm.controls['video'].value,
        monthSalaryFrom: this.minValue,
        monthSalaryTo: this.maxValue,
        eligibility: this.filterForm.controls['eligibility'].value,
        orderBy: this.orderBy,
        orderSort: (this.orderSort === true) ? 'ASC' : (this.orderSort === false) ? 'DESC' : ''
      };

      try {
        const response = await this._businessService.getApplicantsCount(this.viewPage, data);
        this.totalCountFilter = response.countApplicants;
      }
      catch (err) {
        this._sharedService.showRequestErrors(err);
      }
    }
  }

  /**
   * Reset ethnicity
   * @param value {string}
   */
  public resetEthnicity(value) {
    if (value === 'all') {
      this.ethnicityOptions = this._sharedService.ethnicityOptionsAll;
    } else {
      this.ethnicityOptions = this._sharedService.ethnicityOptionsYes;
      this.ethnicityModel = [];
    }
  }

  /**
   * Checking form control
   * @param value {object}
   * @param options {array}
   * @returns {any}
   */
  public checkingFormControl(value, options) {
    let ret: any;
    (value && value.length === options.length) ? ret = 'All' : ret = value;
    return ret;
  }

  /**
   * Checking from control number
   * @param value {object}
   * @param options {array}
   * @returns {any}
   */
  public checkingFormControlNumber(value, options) {
    let ret: any;
    (value && value.length === options.length) ? ret = '' : ret = value;
    return ret;
  }

  /**
   * Reset filters
   */
  public resetFilterForm(): void{
    try {
      this.filterForm.reset();
      this.filterForm.patchValue({
        eligibility: 'applicable'
      });
      this.minValue = null;
      this.maxValue = null;

      setTimeout(() => {
        this.getApplicantsCount();
      }, 500);
    }
    catch (err) {
      this._sharedService.showRequestErrors(err);
    }
  }

  /**
   * Reset Array
   */
  public resetArrayPagination(): void{
    this.applicantsData = [];
    this.pagination = 1;
  }

  /**
   * Load pagination
   */
  public loadPagination(): void {
    this.pagination++;
    this.paginationLoader = true;
    this.searchFilter();
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
   * opens popup
   * @param content - content to be placed within
   * @param candidate - candidateId id to show within popup
   */
  public openVerticallyCentered(content: any, candidate) {
    this.candidateToView = candidate;
    this.modalActiveClose = this._modalService.open(content, { centered: true, size: 'lg', windowClass: 'xlModal applicant-popups' });
  }

  /**
   * opens popup
   * @param content - content to be placed within
   * @param candidate - candidateId id to show within popup
   */
  public openVerticallyCenters(content: any, candidate) {
    this.candidateToView = candidate;
    this.modalActiveClose = this._modalService.open(content, { centered: true, size: 'lg', windowClass: 'xlModal' });
  }

  /**
   * Fetches list of all applicants
   * @returns void
   */
  public async fetchListOfApplicants(): Promise<void> {

    const data = {
      jobID: this.requestJobId,
      page: this.pagination,
      limit: 50,
      search: this.search.nativeElement.value,
      gender: this.checkingFormControl(this.filterForm.controls['gender'].value, this._sharedService.genderOptions),
      ethnicity: this.checkingFormControl(this.filterForm.controls['ethnicity'].value, this._sharedService.ethnicityOptions),
      location: this.checkingFormControl(this.filterForm.controls['location'].value, this._sharedService.citiesWorking),
      highestQualification: this.checkingFormControl(this.filterForm.controls['highestQualification'].value, this._sharedService.configQualificationLevel),
      field: this.checkingFormControl(this.filterForm.controls['field'].value, this._sharedService.specializationCandidate),
      availability: this.checkingFormControl(this.filterForm.controls['availability'].value, this._sharedService.availabilityOptions),
      yearsOfWorkExperience: this.checkingFormControlNumber(this.filterForm.controls['yearsOfWorkExperience'].value, this._sharedService.configYearsWork),
      video: this.filterForm.controls['video'].value,
      monthSalaryFrom: this.minValue,
      monthSalaryTo: this.maxValue,
      eligibility: this.filterForm.controls['eligibility'].value,
      orderBy: this.orderBy,
      orderSort: (this.orderSort === true) ? 'ASC' : (this.orderSort === false) ? 'DESC' : ''
    };

    try {
      let response;
      if (this.viewPage === 1) {
        response = await this._businessService.getApplicantsAwaitingApproval(data);
      } else if (this.viewPage === 2) {
        response = await this._businessService.getApplicantsShortlisted(data);
      } else if (this.viewPage === 3) {
        response = await this._businessService.getApplicantsApproved(data);
      } else if (this.viewPage === 4) {
        response = await this._businessService.getApplicantsDeclined(data);
      }

      response.items.forEach((item) => {
        this.applicantsData.push(item);
      });

      if (response.pagination.total_count === this.applicantsData.length) {
        this.loadMoreCheck = false;
      }
      else if (response.pagination.total_count !== this.applicantsData.length) {
        this.loadMoreCheck = true;
      }

      this.paginationLoader = false;
      this.paginationFilter = false;
      this.preloaderPage = false;
      this.totalCount.number = response.pagination.total_count;
      this.totalCountFilter = response.pagination.total_count;
    }
    catch (err) {
      this._sharedService.showRequestErrors(err);
    }
  }

  /**
   * Search filters
   */
  public searchFilter() {
    this.orderBy = null;
    this.orderSort = null;
    this.paginationFilter = true;
    this.fetchListOfApplicants();

    this._openButton.nativeElement.innerHTML = 'Open more filters';
    this._openFilters.nativeElement.classList.remove('active');
    this._filterFont.nativeElement.classList.remove('active');
    this._filterItem.nativeElement.classList.remove('active');
    this.checkOpenFilters = false;
  }

  /**
   * Sort by table columns
   */
  public sortCandidate(column: string): void {
    this.resetArrayPagination();
    this.paginationFilter = true;

    this.orderBy = column;
    this.orderSort = !this.orderSort;

    this.fetchListOfApplicants();
  }

  /**
   * Add Candidate to Short List
   * @param candidate {object}
   * @param index {number}
   * @returns {Promise<void>}
   */
  public async declineCandidateApplication(candidate, index): Promise<void>{
    try {
      const data = await this._businessService.declineCandidateApplication(Number(candidate.candidateID), Number(candidate.jobID));

      if(this.viewPage === 1){
        this._sharedService.sidebarBusinessBadges.applicantAwaiting--;
        this._sharedService.sidebarBusinessBadges.applicantDecline++;
      } else if(this.viewPage === 2){
        this._sharedService.sidebarBusinessBadges.applicantShortlist--;
        this._sharedService.sidebarBusinessBadges.applicantDecline++;
      }

      this.applicantsData.splice(index, 1);
      this.totalCount.number--;
      this._toastr.success('Application was declined');
    }
    catch (err) {
      this._sharedService.showRequestErrors(err);
    }
  }

  /**
   * Add Candidate to Short List
   * @param candidate {object}
   * @param index {number}
   * @returns {Promise<void>}
   */
  public async addCandidateToShortList(candidate, index): Promise<void>{
    try {
      await this._businessService.addCandidateToShortList(Number(candidate.candidateID), Number(candidate.jobID));

      this._sharedService.sidebarBusinessBadges.applicantAwaiting--;
      this._sharedService.sidebarBusinessBadges.applicantShortlist++;

      this.applicantsData.splice(index, 1);
      this.totalCount.number--;

      this._toastr.success('Added to ShortList');
    }
    catch (err) {
      this._sharedService.showRequestErrors(err);
    }
  }

  /**
   * Add candidate to interview
   * @param candidate {object}
   * @param jobID {number}
   * @param index {number}
   * @return {Promise<void>}
   */
  public async setUpInterview(candidate, jobID, index): Promise<void> {
    try {
      const check = await this._businessService.setUpInterviewCandidate(Number(candidate.candidateID), Number(jobID));

      // if(check.check === 1){
      //   this._sharedService.sidebarBusinessBadges.applicantAll++;
      //   this._sharedService.sidebarBusinessBadges.applicantApprove++;
      // } else if(check.check === 2){
      //   this._sharedService.sidebarBusinessBadges.applicantAll++;
      //   this._sharedService.sidebarBusinessBadges.applicantApprove++;
      // }

      if(this.viewPage === 1){
        this._sharedService.sidebarBusinessBadges.applicantAwaiting--;
        this._sharedService.sidebarBusinessBadges.applicantApprove++;
      } else if(this.viewPage === 2){
        this._sharedService.sidebarBusinessBadges.applicantShortlist--;
        this._sharedService.sidebarBusinessBadges.applicantApprove++;
      }

      this.applicantsData.splice(index, 1);
      this.totalCount.number--;

      this._toastr.success('Success');
    }
    catch (err) {
      this._sharedService.showRequestErrors(err);
    }
  }

  /**
   * Open more filters
   */
  public openMoreFilters(): void {

    this.checkOpenFilters = !this.checkOpenFilters;

    if (this.checkOpenFilters === true) {
    this.options = {
        floor: 0,
        ceil: 35000,
        step: 3500,
        showTicks: true,
        showTicksValues: true,
        translate: (value: number, label: LabelType): string => {
          switch (label) {
            case LabelType.Low:
              return 'R' + value;
            case LabelType.High:
              return 'R' + value;
            default:
              return 'R' + value;
          }
        }
      };
      this.copyFilter = this.filterForm.controls;
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
   * Fetches list of jobs
   * @returns void
   */
  public async fetchAllJobs(): Promise<void> {
    try {
      this.listOfJobs = await this._businessService.getBusinessJobsMatchingCriteria(false, null);
    }
    catch (err) {
      this._sharedService.showRequestErrors(err);
    }
  }

  /**
   * Select change router
   * @param url
   * @param id
   */
  public routerApplicants(url, id): void {
    this._router.navigate([url], (id > 0) ? { queryParams: { jobId: id } } : {});
  }

  /**
   * Open modal
   * @param content
   */
  public openVerticallyCenter(content) {
    this.modalActiveClose = this._modalService.open(content, { centered: true, 'size': 'lg' });
  }

  /**
   * Hide articles firm
   * @param elem
   */
  // public hideArticlesFirm(elem): void {
  //   let nextSibling = elem.nextSibling;
  //   while(nextSibling && nextSibling.nodeType != 1) {
  //     nextSibling = nextSibling.nextSibling
  //   }
  //   nextSibling.style.opacity = 0;
  //   elem.style.opacity = 1;
  // }
}
