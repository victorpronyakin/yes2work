import { Component, ElementRef, OnInit, ViewChild } from '@angular/core';
import { IMultiSelectOption, IMultiSelectSettings, IMultiSelectTexts } from 'angular-2-dropdown-multiselect';
import { articles } from '../../../../constants/articles.const';
import { CandidateApprove } from '../../../../../entities/models-admin';
import { AdminService } from '../../../../services/admin.service';
import { NgbModal } from '@ng-bootstrap/ng-bootstrap';
import { ToastrService } from 'ngx-toastr';
import { SharedService } from '../../../../services/shared.service';
import { Angular5Csv } from 'angular5-csv/Angular5-csv';
import { FormControl, FormGroup } from '@angular/forms';
import { IMonthCalendarConfigInternal } from 'ng2-date-picker/month-calendar/month-calendar-config';
import { LabelType, Options } from 'ng5-slider';
import { PaginationService } from '../../../../services/pagination.service';

@Component({
  selector: 'app-admin-all-candidates',
  templateUrl: './admin-all-candidates.component.html',
  styleUrls: ['./admin-all-candidates.component.scss']
})
export class AdminAllCandidatesComponent implements OnInit {

  @ViewChild('openFilters') _openFilters: ElementRef;
  @ViewChild('openButton') _openButton: ElementRef;
  @ViewChild('filterFont') _filterFont: ElementRef;
  @ViewChild('filterItem') _filterItem: ElementRef;
  @ViewChild('search') public search: ElementRef;

  public approveCandidateList = Array<CandidateApprove>();

  public modalActiveClose: any;
  public selectedId: number;

  public articles = articles;
  public articlesFirmPredefined: string[] = [];
  public articlesFirmSelectedName: string[] = [];
  public articlesFirmSettings: IMultiSelectSettings = {
    displayAllSelectedText: true,
    selectionLimit: 0,
    showCheckAll: true,
    showUncheckAll: true,
  };
  public articlesFirmOptions: IMultiSelectOption[] = [];
  public articlesFirmTextConfig: IMultiSelectTexts = {
    checkAll: 'Select all',
    uncheckAll: 'Deselect all',
    checked: 'item selected',
    checkedPlural: 'items selected',
    defaultTitle: 'Articles firm',
    allSelected: 'All selected',
  };
  public configEnabled: IMultiSelectTexts = {
    checkAll: 'Select all',
    uncheckAll: 'Deselect all',
    checked: 'item selected',
    checkedPlural: 'items selected',
    defaultTitle: 'Enabled',
    allSelected: 'All selected - Enabled',
  };
  public configProfile: IMultiSelectTexts = {
    checkAll: 'Select all',
    uncheckAll: 'Deselect all',
    checked: 'item selected',
    checkedPlural: 'items selected',
    defaultTitle: 'Profile Completed',
    allSelected: 'All selected - Profile Completed',
  };

  public enabledModel = [true];
  public enabledOptions: IMultiSelectOption[];

  public profileModel = [];
  public profileOptions: IMultiSelectOption[];

  public preloaderPage = true;
  public checkOpenFilters = false;
  public candidatesCountMatchingCriteria: number;

  public paginationLoader = false;
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

  public minValue: number = null;
  public maxValue: number = null;
  public options: Options;
  public checkInitChange = false;
  public searchField: string = '';
  public orderBy: string = '';
  public orderSort: boolean;

  public paginationFilter = true;

  public totalItems: number;
  public pager: any = {
    currentPage: 1
  };

  constructor(
    private readonly _adminService: AdminService,
    private readonly _modalService: NgbModal,
    private readonly _toastr: ToastrService,
    private readonly _paginationService: PaginationService,
    public readonly _sharedService: SharedService
  ) {
    this._sharedService.checkSidebar = false;

    this.genderOptions = this._sharedService.genderOptions;
    this.availabilityOptions = this._sharedService.availabilityOptions;
    this.ethnicityOptions = this._sharedService.ethnicityOptionsYes;
    this.locationOptions = this._sharedService.citiesWorking;
    this.qualificationLevelOptions = this._sharedService.configQualificationLevel;
    this.tertiaryEducationOptions = this._sharedService.configTertiaryEducation;
    this.specializationOptions = this._sharedService.specializationCandidate;
    this.yearsWorkOptions = this._sharedService.configYearsWork;

    this.enabledOptions = [
      { id: true, name: 'Yes' },
      { id: false, name: 'No' }
    ];
    this.profileOptions = [
      { id: true, name: 'Yes' },
      { id: false, name: 'No' }
    ];
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
      enabled: new FormControl([true]),
      profileComplete: new FormControl('All')
    });

    setTimeout(() => {
      this.approveCandidateList = [];
      this.getAllCandidateList().then(() => {
        this.pager = this._paginationService.getPager(this.totalItems, 1);
      });
      this.getApplicantsCount();
    }, 500);
  }

  /**
   * Sort by table columns
   */
  public sortCandidate(column: string): void {
    this.resetArrayPagination();
    this.paginationFilter = true;

    this.orderBy = column;
    this.orderSort = !this.orderSort;

    this.getAllCandidateList();
  }

  /**
   * Set pagination page
   * @param {number} page
   */
  public setPage(page: number) {
    this.paginationLoader = true;
    this.approveCandidateList = [];
    this.pager = this._paginationService.getPager(this.totalItems, page);
    window.scrollTo(100, 0);

    this.getAllCandidateList();
  }

  /**
   * Reset Array
   */
  public resetArrayPagination(): void{
    this.approveCandidateList = [];
    this.pager.currentPage = 1;
  }

  /**
   * Reset sorting params
   */
  public resetSorting() {
    this.orderBy = null;
    this.orderSort = null;
  }

  /**
   * Get all candidates
   * @return {Promise<void>}
   */
  public async getAllCandidateList(): Promise<void> {
    const data = {
      csv: false,
      page: this.pager.currentPage,
      search: this.search.nativeElement.value,
      eligibility: this.filterForm.controls['eligibility'].value,
      ethnicity: this.checkingFormControl(this.ethnicityModel, this._sharedService.ethnicityOptions),
      gender: this.checkingFormControl(this.genderModel, this._sharedService.genderOptions),
      location: this.checkingFormControl(this.locationModel, this._sharedService.citiesWorking),
      highestQualification: this.checkingFormControl(this.qualificationLevelModel, this._sharedService.configQualificationLevel),
      field: this.checkingFormControl(this.specializationModel, this._sharedService.specializationCandidate),
      yearsOfWorkExperience: this.checkingFormControl(this.yearsWorkModel, this._sharedService.configYearsWork),
      availability: this.checkingFormControl(this.availabilityModel, this._sharedService.availabilityOptions),
      video: this.filterForm.controls['video'].value,
      monthSalaryFrom: this.minValue,
      monthSalaryTo: this.maxValue,
      orderBy: this.orderBy,
      //orderSort: (this.orderSort === true) ? 'ASC' : (this.orderSort === false) ? 'DESC' : '',
      orderSort: this.orderSort,
      enabled: this.checkingFormControl(this.enabledModel, [
        { id: true, name: 'Yes' },
        { id: false, name: 'No' }
      ]),
      profileComplete: this.checkingFormControl(this.profileModel,[
        { id: true, name: 'Yes' },
        { id: false, name: 'No' }
      ])
    };

    try {
      this._openButton.nativeElement.innerHTML = 'Open more filters';
      this._openFilters.nativeElement.classList.remove('active');
      this._filterFont.nativeElement.classList.remove('active');
      this._filterItem.nativeElement.classList.remove('active');

      const response = await this._adminService.getAllCandidateList(data);

      response.items.forEach((item) => {
        this.approveCandidateList.push(item);
      });

      this.totalItems = response.pagination.total_count;
      this.pager = this._paginationService.getPager(this.totalItems, this.pager.currentPage);

      if (response.pagination.total_count === this.approveCandidateList.length) {
        this.loadMoreCheck = false;
      }
      else if (response.pagination.total_count !== this.approveCandidateList.length){
        this.loadMoreCheck = true;
      }

      this.preloaderPage = false;
      this.paginationLoader = false;
      this.paginationFilter = false;
    }
    catch (err) {
      this._sharedService.showRequestErrors(err);
    }
  }

  /**
   * Search count
   * @return {Promise<void>}
   */
  public async getApplicantsCount(): Promise<void> {

    const data = {
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
      orderSort: (this.orderSort === true) ? 'ASC' : (this.orderSort === false) ? 'DESC' : '',
      enabled: this.checkingFormControl(this.enabledModel, [
        { id: true, name: 'Yes' },
        { id: false, name: 'No' }
      ]),
      profileComplete: this.checkingFormControl(this.profileModel,[
        { id: true, name: 'Yes' },
        { id: false, name: 'No' }
      ])
    };

    try {
      const response = await this._adminService.getAllCandidateListCount(data);
      this.candidatesCountMatchingCriteria = response.candidateCount;
    }
    catch (err) {
      this._sharedService.showRequestErrors(err);
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
   * Reset filters
   */
  public resetFilterForm(): void{
    try {
      this.filterForm.reset();
      this.filterForm.patchValue({
        eligibility: 'applicable',
        enabled: [true],
        profileComplete: null,
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
   * Hide articles firm
   * @param elem
   */
  public hideArticlesFirm(elem): void {
    let nextSibling = elem.nextSibling;
    while(nextSibling && nextSibling.nodeType != 1) {
      nextSibling = nextSibling.nextSibling
    }
    nextSibling.style.opacity = 0;
  }

  /**
   * Export CSV file
   * @return {Promise<void>}
   */
  public async exportDataCSV(): Promise<void>{

    const params = {
      csv: true,
      page: this.pager.currentPage,
      search: this.search.nativeElement.value,
      eligibility: this.filterForm.controls['eligibility'].value,
      ethnicity: this.checkingFormControl(this.ethnicityModel, this._sharedService.ethnicityOptions),
      gender: this.checkingFormControl(this.genderModel, this._sharedService.genderOptions),
      location: this.checkingFormControl(this.locationModel, this._sharedService.citiesWorking),
      highestQualification: this.checkingFormControl(this.qualificationLevelModel, this._sharedService.configQualificationLevel),
      field: this.checkingFormControl(this.specializationModel, this._sharedService.specializationCandidate),
      yearsOfWorkExperience: this.checkingFormControl(this.yearsWorkModel, this._sharedService.configYearsWork),
      availability: this.checkingFormControl(this.availabilityModel, this._sharedService.availabilityOptions),
      video: this.filterForm.controls['video'].value,
      monthSalaryFrom: this.minValue,
      monthSalaryTo: this.maxValue,
      orderBy: this.orderBy,
      orderSort: (this.orderSort === true) ? 'ASC' : (this.orderSort === false) ? 'DESC' : '',
      enabled: this.checkingFormControl(this.enabledModel, [
        { id: true, name: 'Yes' },
        { id: false, name: 'No' }
      ]),
      profileComplete: this.checkingFormControl(this.profileModel,[
        { id: true, name: 'Yes' },
        { id: false, name: 'No' }
      ])
    };

    try {
      const response = await this._adminService.getAllCandidateList(params);

      const options = {
        showLabels: true,
        headers: ['Name', 'Email' , 'Tel Number', 'Profile Completed', 'SMS', 'Referring Agent', 'Active']
      };

      new Angular5Csv(response, 'All candidates', options);
    }
    catch (err) {
      this._sharedService.showRequestErrors(err);
    }
  }

  /**
   * Managed candidate user
   * @param {CandidateApprove} user
   * @param {boolean} status
   * @return {Promise<void>}
   */
  public async managedCandidateUser(user: CandidateApprove, status: boolean): Promise<void> {
    try {
      await this._adminService.managedCandidateUser(user.id, status);

      const index = this.approveCandidateList.indexOf(user);
      this.approveCandidateList.splice(index, 1);
      this._toastr.success((status) ? 'Candidate has been approved' : 'Candidate has been declined');
    }
    catch (err) {
      this._sharedService.showRequestErrors(err);
    }
  }

  /**
   * Close more filters
   */
  public closeMoreFilters(): void{
    this.preloaderPage = true;
    this._openButton.nativeElement.innerHTML = 'Open more filters';
    this._openFilters.nativeElement.classList.remove('active');
    this._filterFont.nativeElement.classList.remove('active');
    this._filterItem.nativeElement.classList.remove('active');
    this.checkOpenFilters = false;
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
      // this.copyFilter = this.filterForm.controls;
      this._openButton.nativeElement.innerHTML = 'Close more filters';
      this._openFilters.nativeElement.classList.add('active');
      this._filterFont.nativeElement.classList.add('active');
      this._filterItem.nativeElement.classList.add('active');
      this.getApplicantsCount();
    }
    else {
      this._openButton.nativeElement.innerHTML = 'Open more filters';
      this._openFilters.nativeElement.classList.remove('active');
      this._filterFont.nativeElement.classList.remove('active');
      this._filterItem.nativeElement.classList.remove('active');
    }
  }

  /**
   * Delete candidate profile
   * @param id
   * @return {Promise<void>}
   */
  public async deleteCandidateProfile(id): Promise<void> {
    try {
      await this._adminService.deleteCandidateProfile(id);

      this.approveCandidateList = this.approveCandidateList.filter((listElement) => listElement.id !== id);
      this.modalActiveClose.dismiss();
      this._sharedService.sidebarAdminBadges.candidateAll--;
      this._toastr.success('Candidate has been deleted');
    }
    catch (err) {
      this._sharedService.showRequestErrors(err);
    }
  }

  /**
   * Update candidate status
   * @param id {number}
   * @param enabled {boolean}
   * @return {Promise<void>}
   */
  public async updateCandidateStatus(id: number, enabled) {
    enabled = !enabled;
    try {
      await this._adminService.updateCandidateStatus(id, enabled);
      this._toastr.success('Candidate status has been changed');
    }
    catch (err) {
      this._sharedService.showRequestErrors(err);
    }
  }

  /**
   * Managed modal
   * @param content {any} - content to be shown in popup
   * @param id {number} - job id to be used for fetching data and showing in popup
   */
  public openVerticallyCentered(content: any,  id: number): void {
    this.modalActiveClose = this._modalService.open(content, { centered: true, 'size': 'lg' });
    this.selectedId = id;
  }

  /**
   * Managed modal
   * @param content {any} - content to be shown in popup
   */
  public openVerticallyCenter(content: any) {
    this.modalActiveClose = this._modalService.open(content, { centered: true, 'size': 'sm' });
  }

}
