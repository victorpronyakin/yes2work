import { NgModule } from '@angular/core';
import { BusinessComponent } from './business.component';
import { RouterModule, Routes } from '@angular/router';
import { ProfileDetailsComponent } from './partials/profile-details/profile-details.component';
import { BusinessAddNewJobComponent } from './partials/business-add-new-job/business-add-new-job.component';
import { BusinessEditJobComponent } from './partials/business-edit-job/business-edit-job.component';
import { BrowseAllCandidatesComponent } from './partials/browse-all-candidates/browse-all-candidates.component';
import { BusinessApplicantsComponent } from './partials/business-applicants/business-applicants.component';
import { BusinessApplicantsApprovedComponent } from './partials/business-applicants-approved/business-applicants-approved.component';
import { BusinessApplicantsAwaitingComponent } from './partials/business-applicants-awaiting/business-applicants-awaiting.component';
import { BusinessApplicantsDeclinedComponent } from './partials/business-applicants-declined/business-applicants-declined.component';
import { BusinessApplicantsShortlistedComponent } from './partials/business-applicants-shortlisted/business-applicants-shortlisted.component';
import { BusinessDashboardComponent } from './partials/business-dashboard/business-dashboard.component';
import { BusinessOldJobsComponent } from './partials/business-old-jobs/business-old-jobs.component';
import { Page404Component } from './partials/page-404/page-404.component';
import { Role } from '../../../entities/models';
import { RoleGuard } from '../../guard/role.guard';
import { AuthGuard } from '../../guard/auth.guard';
import { CanDeactivateGuardGuard } from '../../guard/can-deactivate-guard.guard';
import { BusinessAwaitingJobComponent } from './partials/business-awaiting-job/business-awaiting-job.component';
import { BusinessApprovedJobComponent } from './partials/business-approved-job/business-approved-job.component';
import { DashboardGuard } from '../../guard/dashboard.guard';

const businessRoutes: Routes = [
  { path: '', redirectTo: 'business', pathMatch: 'full' },
  { path: 'business', component: BusinessComponent, children: [
    { path: '', redirectTo: 'dashboard', pathMatch: 'full' },
    { path: 'dashboard', canDeactivate: [CanDeactivateGuardGuard], canActivate:[ AuthGuard, RoleGuard, DashboardGuard ], data:{roles:[Role.clientRole]}, component: BusinessDashboardComponent },
    { path: 'candidates', canDeactivate: [CanDeactivateGuardGuard], canActivate:[ AuthGuard, RoleGuard ], data:{roles:[Role.clientRole]}, component: BrowseAllCandidatesComponent },
    { path: 'jobs/add', canDeactivate: [CanDeactivateGuardGuard], canActivate:[ AuthGuard, RoleGuard ], data:{roles:[Role.clientRole]}, component: BusinessAddNewJobComponent },
    { path: 'jobs/edit/:id', canDeactivate: [CanDeactivateGuardGuard], canActivate:[ AuthGuard, RoleGuard ], data:{roles:[Role.clientRole]}, component: BusinessEditJobComponent },
    { path: 'my_account', canDeactivate: [CanDeactivateGuardGuard], canActivate:[ AuthGuard, RoleGuard ], data:{roles:[Role.clientRole]}, component: ProfileDetailsComponent },
    { path: 'awaiting_job', canDeactivate: [CanDeactivateGuardGuard], canActivate:[ AuthGuard, RoleGuard ], data:{roles:[Role.clientRole]}, component: BusinessAwaitingJobComponent },
    { path: 'approved_job', canDeactivate: [CanDeactivateGuardGuard], canActivate:[ AuthGuard, RoleGuard ], data:{roles:[Role.clientRole]}, component: BusinessApprovedJobComponent },
    { path: 'old_jobs', canDeactivate: [CanDeactivateGuardGuard], canActivate:[ AuthGuard, RoleGuard ], data:{roles:[Role.clientRole]}, component: BusinessOldJobsComponent },
    { path: 'applicants', canDeactivate: [CanDeactivateGuardGuard], canActivate:[ AuthGuard, RoleGuard ], data:{roles:[Role.clientRole]}, component: BusinessApplicantsComponent },
    { path: 'applicants_approved', canDeactivate: [CanDeactivateGuardGuard], canActivate:[ AuthGuard, RoleGuard ], data:{roles:[Role.clientRole]}, component: BusinessApplicantsApprovedComponent },
    { path: 'applicants_declined', canDeactivate: [CanDeactivateGuardGuard], canActivate:[ AuthGuard, RoleGuard ], data:{roles:[Role.clientRole]}, component: BusinessApplicantsDeclinedComponent },
    { path: 'applicants_awaiting', canDeactivate: [CanDeactivateGuardGuard], canActivate:[ AuthGuard, RoleGuard ], data:{roles:[Role.clientRole]}, component: BusinessApplicantsAwaitingComponent },
    { path: 'applicants_shortlist', canDeactivate: [CanDeactivateGuardGuard], canActivate:[ AuthGuard, RoleGuard ], data:{roles:[Role.clientRole]}, component: BusinessApplicantsShortlistedComponent },
    { path: '**', canDeactivate: [CanDeactivateGuardGuard], canActivate:[ AuthGuard, RoleGuard ], data:{roles:[Role.clientRole]}, component: Page404Component }
  ] },
];

@NgModule({
  imports: [
    RouterModule.forChild(businessRoutes)
  ],
  exports: [RouterModule]
})
export class RouterBusinessModule { }
