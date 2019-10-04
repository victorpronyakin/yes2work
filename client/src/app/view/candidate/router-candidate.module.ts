import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { CandidateComponent } from './candidate.component';
import { ProfileDetailsComponent } from './partials/profile-details/profile-details.component';
import { PreferencesComponent } from './partials/preferences/preferences.component';
import { CandidateVideoComponent } from './partials/candidate-video/candidate-video.component';
import { CandidateAchievementsComponent } from './partials/candidate-achievements/candidate-achievements.component';
import { CandidateFindJobsComponent } from './partials/candidate-find-jobs/candidate-find-jobs.component';
import { CandidateDashboardComponent } from './partials/candidate-dashboard/candidate-dashboard.component';
import { Page404Component } from './partials/page-404/page-404.component';
import { Role } from '../../../entities/models';
import { RoleGuard } from '../../guard/role.guard';
import { AuthGuard } from '../../guard/auth.guard';
import { CandidateViewCvComponent } from './partials/candidate-view-cv/candidate-view-cv.component';
import { CandidateYourOpportunitiesComponent } from './partials/candidate-your-opportunities/candidate-your-opportunities.component';
import { CandidateJobAlertsNewComponent } from './partials/candidate-job-alerts-new/candidate-job-alerts-new.component';
import { CandidateJobAlertsDeclinedComponent } from './partials/candidate-job-alerts-declined/candidate-job-alerts-declined.component';
import { CandidateJobAlertsExpiredComponent } from './partials/candidate-job-alerts-expired/candidate-job-alerts-expired.component';
import { CandidateYourApplicationComponent } from './partials/candidate-your-application/candidate-your-application.component';
import { CandidateAwaitingApprovalComponent } from './partials/candidate-awaiting-approval/candidate-awaiting-approval.component';
import { CandidateApprovedApplicationsComponent } from './partials/candidate-approved-applications/candidate-approved-applications.component';
import { CandidateDeclinedApplicationsComponent } from './partials/candidate-declined-applications/candidate-declined-applications.component';
import { CandidateRequestedInterviewsComponent } from './partials/candidate-requested-interviews/candidate-requested-interviews.component';
import { CanDeactivateGuardGuard } from '../../guard/can-deactivate-guard.guard';
import { CandidateQualificationComponent } from './partials/candidate-qualification/candidate-qualification.component';
import { CheckCandidateCompleteGuard } from '../../guard/check-candidate-complete.guard';
import { CandidateVideoRecordingComponent } from './partials/candidate-video-recording/candidate-video-recording.component';

const candidateRoutes: Routes = [
  { path: '', redirectTo: 'candidate', pathMatch: 'full' },
  { path: 'candidate', component: CandidateComponent, children: [
    { path: '', redirectTo: 'dashboard', pathMatch: 'full' },
    { path: 'dashboard', canDeactivate: [ CanDeactivateGuardGuard ], canActivate:[ AuthGuard, RoleGuard, CheckCandidateCompleteGuard ], data:{roles:[Role.candidateRole]}, component: CandidateDashboardComponent },
    { path: 'profile_details', canDeactivate: [ CanDeactivateGuardGuard ], canActivate:[ AuthGuard, RoleGuard ], data:{roles:[Role.candidateRole]}, component: ProfileDetailsComponent },
    { path: 'preferences', canDeactivate: [ CanDeactivateGuardGuard ], canActivate:[ AuthGuard, RoleGuard ], data:{roles:[Role.candidateRole]}, component: PreferencesComponent },
    { path: 'video', canDeactivate: [ CanDeactivateGuardGuard ], canActivate:[ AuthGuard, RoleGuard ], data:{roles:[Role.candidateRole]}, component: CandidateVideoComponent },
    { path: 'video-recording', canDeactivate: [ CanDeactivateGuardGuard ], canActivate:[ AuthGuard, RoleGuard ], data:{roles:[Role.candidateRole]}, component: CandidateVideoRecordingComponent },
    { path: 'achievements', canDeactivate: [ CanDeactivateGuardGuard ], canActivate:[ AuthGuard, RoleGuard ], data:{roles:[Role.candidateRole]}, component: CandidateAchievementsComponent },
    { path: 'qualification', canDeactivate: [ CanDeactivateGuardGuard ], canActivate:[ AuthGuard, RoleGuard ], data:{roles:[Role.candidateRole]}, component: CandidateQualificationComponent },
    { path: 'find_jobs', canDeactivate: [ CanDeactivateGuardGuard ], canActivate:[ AuthGuard, RoleGuard, CheckCandidateCompleteGuard ], data:{roles:[Role.candidateRole]}, component: CandidateFindJobsComponent },
    { path: 'opportunities', canDeactivate: [ CanDeactivateGuardGuard ], canActivate:[ AuthGuard, RoleGuard, CheckCandidateCompleteGuard ], data:{roles:[Role.candidateRole]}, component: CandidateYourOpportunitiesComponent },
    { path: 'job_alerts_new', canDeactivate: [ CanDeactivateGuardGuard ], canActivate:[ AuthGuard, RoleGuard, CheckCandidateCompleteGuard ], data:{roles:[Role.candidateRole]}, component: CandidateJobAlertsNewComponent },
    { path: 'job_alerts_declined', canDeactivate: [ CanDeactivateGuardGuard ], canActivate:[ AuthGuard, RoleGuard, CheckCandidateCompleteGuard ], data:{roles:[Role.candidateRole]}, component: CandidateJobAlertsDeclinedComponent },
    { path: 'job_alerts_expired', canDeactivate: [ CanDeactivateGuardGuard ], canActivate:[ AuthGuard, RoleGuard, CheckCandidateCompleteGuard ], data:{roles:[Role.candidateRole]}, component: CandidateJobAlertsExpiredComponent },
    { path: 'applications', canDeactivate: [ CanDeactivateGuardGuard ], canActivate:[ AuthGuard, RoleGuard, CheckCandidateCompleteGuard ], data:{roles:[Role.candidateRole]}, component: CandidateYourApplicationComponent },
    { path: 'awaiting_approval', canDeactivate: [ CanDeactivateGuardGuard ], canActivate:[ AuthGuard, RoleGuard, CheckCandidateCompleteGuard ], data:{roles:[Role.candidateRole]}, component: CandidateAwaitingApprovalComponent },
    { path: 'approved_applications', canDeactivate: [ CanDeactivateGuardGuard ], canActivate:[ AuthGuard, RoleGuard, CheckCandidateCompleteGuard ], data:{roles:[Role.candidateRole]}, component: CandidateApprovedApplicationsComponent },
    { path: 'declined_applications', canDeactivate: [ CanDeactivateGuardGuard ], canActivate:[ AuthGuard, RoleGuard, CheckCandidateCompleteGuard ], data:{roles:[Role.candidateRole]}, component: CandidateDeclinedApplicationsComponent },
    { path: 'request_interviews', canDeactivate: [ CanDeactivateGuardGuard ], canActivate:[ AuthGuard, RoleGuard, CheckCandidateCompleteGuard ], data:{roles:[Role.candidateRole]}, component: CandidateRequestedInterviewsComponent },
    { path: 'view_cv', canDeactivate: [ CanDeactivateGuardGuard ], canActivate:[ AuthGuard, RoleGuard, CheckCandidateCompleteGuard ], data:{roles:[Role.candidateRole]}, component: CandidateViewCvComponent },
    { path: '**', canDeactivate: [ CanDeactivateGuardGuard ], canActivate:[ AuthGuard, RoleGuard ], data:{roles:[Role.candidateRole]}, component: Page404Component }
  ] },
];


@NgModule({
  imports: [
    RouterModule.forChild(candidateRoutes)
  ],
  exports: [RouterModule]
})
export class RouterCandidateModule { }
