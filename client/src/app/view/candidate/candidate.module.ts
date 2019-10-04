import { NgModule } from '@angular/core';
import { CandidateComponent } from './candidate.component';
import { CommonModule } from '@angular/common';
import { RouterCandidateModule } from './router-candidate.module';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { SidebarComponent } from './partials/sidebar/sidebar.component';
import { CandidateNavbarComponent } from './partials/candidate-navbar/candidate-navbar.component';
import { PreferencesComponent } from './partials/preferences/preferences.component';
import { ProfileDetailsComponent } from './partials/profile-details/profile-details.component';
import { CandidateService } from '../../services/candidate.service';
import { NgbModule } from '@ng-bootstrap/ng-bootstrap';
import { MultiselectDropdownModule } from 'angular-2-dropdown-multiselect';
import { NgxMyDatePickerModule } from 'ngx-mydatepicker';
import { CandidateVideoComponent } from './partials/candidate-video/candidate-video.component';
import { CandidateAchievementsComponent } from './partials/candidate-achievements/candidate-achievements.component';
import { CandidateFindJobsComponent } from './partials/candidate-find-jobs/candidate-find-jobs.component';
import { CandidateJobPopupComponent } from './partials/candidate-job-popup/candidate-job-popup.component';
import { CandidateFooterComponent } from './partials/candidate-footer/candidate-footer.component';
import { CandidateDashboardComponent } from './partials/candidate-dashboard/candidate-dashboard.component';
import { Page404Component } from './partials/page-404/page-404.component';
import { SharedModule } from '../../modules/shared/shared.module';
import { CandidateViewCvComponent } from './partials/candidate-view-cv/candidate-view-cv.component';
import { DateLeftPipe } from '../../pipes/date-left.pipe';
import { FormatTimePipe } from '../../pipes/format-time.pipe';
import { DistancePipe } from '../../pipes/distance.pipe';
import { CandidateYourOpportunitiesComponent } from './partials/candidate-your-opportunities/candidate-your-opportunities.component';
import { CandidateJobAlertsNewComponent } from './partials/candidate-job-alerts-new/candidate-job-alerts-new.component';
import { CandidateJobAlertsDeclinedComponent } from './partials/candidate-job-alerts-declined/candidate-job-alerts-declined.component';
import { CandidateJobAlertsExpiredComponent } from './partials/candidate-job-alerts-expired/candidate-job-alerts-expired.component';
import { CandidateYourApplicationComponent } from './partials/candidate-your-application/candidate-your-application.component';
import { CandidateAwaitingApprovalComponent } from './partials/candidate-awaiting-approval/candidate-awaiting-approval.component';
import { CandidateApprovedApplicationsComponent } from './partials/candidate-approved-applications/candidate-approved-applications.component';
import { CandidateDeclinedApplicationsComponent } from './partials/candidate-declined-applications/candidate-declined-applications.component';
import { CandidateRequestedInterviewsComponent } from './partials/candidate-requested-interviews/candidate-requested-interviews.component';
import { CandidateRequestedInterviewsIdComponent } from './partials/candidate-requested-interviews-id/candidate-requested-interviews-id.component';
import { CandidateQualificationComponent } from './partials/candidate-qualification/candidate-qualification.component';
import { ClickOutsideModule } from 'ng4-click-outside';
import { CandidateVideoRecordingComponent } from './partials/candidate-video-recording/candidate-video-recording.component';
import { ZiggeoModule } from 'angular-ziggeo/build/ziggeo';

@NgModule({
  imports: [
    CommonModule,
    RouterCandidateModule,
    ReactiveFormsModule,
    NgbModule,
    FormsModule,
    MultiselectDropdownModule,
    NgxMyDatePickerModule.forRoot(),
    SharedModule,
    ClickOutsideModule,
    ZiggeoModule
  ],
  declarations: [
    CandidateComponent,
    SidebarComponent,
    CandidateNavbarComponent,
    PreferencesComponent,
    ProfileDetailsComponent,
    CandidateVideoComponent,
    CandidateVideoRecordingComponent,
    CandidateAchievementsComponent,
    CandidateFindJobsComponent,
    CandidateJobPopupComponent,
    CandidateFooterComponent,
    CandidateDashboardComponent,
    Page404Component,
    CandidateViewCvComponent,
    DateLeftPipe,
    FormatTimePipe,
    DistancePipe,
    CandidateYourOpportunitiesComponent,
    CandidateJobAlertsNewComponent,
    CandidateJobAlertsDeclinedComponent,
    CandidateJobAlertsExpiredComponent,
    CandidateYourApplicationComponent,
    CandidateAwaitingApprovalComponent,
    CandidateApprovedApplicationsComponent,
    CandidateDeclinedApplicationsComponent,
    CandidateRequestedInterviewsComponent,
    CandidateRequestedInterviewsIdComponent,
    CandidateQualificationComponent
  ],
  providers: [
    CandidateService
  ]
})
export class CandidateModule { }
