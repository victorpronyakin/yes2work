import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { BusinessComponent } from './business.component';
import { RouterBusinessModule } from './router-business.module';
import { NgbAccordionModule, NgbModule } from '@ng-bootstrap/ng-bootstrap';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { SidebarComponent } from './partials/sidebar/sidebar.component';
import { ProfileDetailsComponent } from './partials/profile-details/profile-details.component';
import { BusinessNavbarComponent } from './partials/business-navbar/business-navbar.component';
import { BusinessService } from '../../services/business.service';
import { BusinessAddNewJobComponent } from './partials/business-add-new-job/business-add-new-job.component';
import { NgxMyDatePickerModule } from 'ngx-mydatepicker';
import { MultiselectDropdownModule } from 'angular-2-dropdown-multiselect';
import { BusinessJobPopupComponent } from './partials/business-job-popup/business-job-popup.component';
import { BusinessEditJobComponent } from './partials/business-edit-job/business-edit-job.component';
import { BrowseAllCandidatesComponent } from './partials/browse-all-candidates/browse-all-candidates.component';
import { BrowseAllCandidatesViewDetailsPopupComponent } from './partials/browse-all-candidates-view-details-popup/browse-all-candidates-view-details-popup.component';
import { BusinessApplicantsComponent } from './partials/business-applicants/business-applicants.component';
import { BusinessApplicantsAwaitingComponent } from './partials/business-applicants-awaiting/business-applicants-awaiting.component';
import { BusinessApplicantsShortlistedComponent } from './partials/business-applicants-shortlisted/business-applicants-shortlisted.component';
import { BusinessApplicantsApprovedComponent } from './partials/business-applicants-approved/business-applicants-approved.component';
import { BusinessApplicantsDeclinedComponent } from './partials/business-applicants-declined/business-applicants-declined.component';
import { SharedModule } from '../../modules/shared/shared.module';
import { BusinessFirstStepsPopupComponent } from './partials/business-first-steps-popup/business-first-steps-popup.component';
import { CookieService } from 'ngx-cookie-service';
import { BusinessFooterComponent } from './partials/business-footer/business-footer.component';
import { BusinessDashboardComponent } from './partials/business-dashboard/business-dashboard.component';
import { AvailabilityDatePipe } from '../../pipes/availability-date.pipe';
import { BusinessOldJobsComponent } from './partials/business-old-jobs/business-old-jobs.component';
import { Page404Component } from './partials/page-404/page-404.component';
import { BusinessVideoPopupComponent } from './partials/business-video-popup/business-video-popup.component';
import { BusinessAwaitingJobComponent } from './partials/business-awaiting-job/business-awaiting-job.component';
import { BusinessApprovedJobComponent } from './partials/business-approved-job/business-approved-job.component';
import { BusinessApplicantViewComponent } from './partials/business-applicant-view/business-applicant-view.component';

@NgModule({
  imports: [
    CommonModule,
    RouterBusinessModule,
    NgbModule,
    FormsModule,
    ReactiveFormsModule,
    MultiselectDropdownModule,
    NgxMyDatePickerModule.forRoot(),
    NgbAccordionModule.forRoot(),
    SharedModule
  ],
  declarations: [
    BusinessComponent,
    SidebarComponent,
    ProfileDetailsComponent,
    BusinessNavbarComponent,
    BusinessAddNewJobComponent,
    BusinessJobPopupComponent,
    BusinessEditJobComponent,
    BrowseAllCandidatesComponent,
    BrowseAllCandidatesViewDetailsPopupComponent,
    BusinessApplicantsComponent,
    BusinessApplicantsAwaitingComponent,
    BusinessApplicantsShortlistedComponent,
    BusinessApplicantsApprovedComponent,
    BusinessApplicantsDeclinedComponent,
    BusinessFirstStepsPopupComponent,
    BusinessFooterComponent,
    BusinessDashboardComponent,
    AvailabilityDatePipe,
    BusinessOldJobsComponent,
    Page404Component,
    BusinessVideoPopupComponent,
    BusinessAwaitingJobComponent,
    BusinessApprovedJobComponent,
    BusinessApplicantViewComponent
  ],
  providers: [
    BusinessService,
    CookieService,
  ]
})
export class BusinessModule { }
