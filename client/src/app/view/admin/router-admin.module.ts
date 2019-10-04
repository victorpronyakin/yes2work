import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { AdminComponent } from './admin.component';
import { ProfileComponent } from './partials/profile/profile.component';
import { AdminNewClientsComponent } from './partials/admin-new-clients/admin-new-clients.component';
import { AdminAllClientsComponent } from './partials/admin-all-clients/admin-all-clients.component';
import { AdminNewCandidatesComponent } from './partials/admin-new-candidates/admin-new-candidates.component';
import { AdminAllCandidatesComponent } from './partials/admin-all-candidates/admin-all-candidates.component';
import { AdminNewJobsComponent } from './partials/admin-new-jobs/admin-new-jobs.component';
import { AdminAllJobsComponent } from './partials/admin-all-jobs/admin-all-jobs.component';
import { AdminAddNewClientComponent } from './partials/admin-add-new-client/admin-add-new-client.component';
import { AdminAddNewJobComponent } from './partials/admin-add-new-job/admin-add-new-job.component';
import { AdminAddNewCandidateComponent } from './partials/admin-add-new-candidate/admin-add-new-candidate.component';
import { AdminAllApplicantsComponent } from './partials/admin-all-applicants/admin-all-applicants.component';
import { AdminSetUpInterviewComponent } from './partials/admin-set-up-interview/admin-set-up-interview.component';
import { AdminPendingInterviewComponent } from './partials/admin-pending-interview/admin-pending-interview.component';
import { AdminSuccessfullPlacedComponent } from './partials/admin-successfull-placed/admin-successfull-placed.component';
import { AdminDocumentApprovalComponent } from './partials/admin-document-approval/admin-document-approval.component';
import { AdminManageSystemUsersComponent } from './partials/admin-manage-system-users/admin-manage-system-users.component';
import { AdminDashboardComponent } from './partials/admin-dashboard/admin-dashboard.component';
import { AdminReportingComponent } from './partials/admin-reporting/admin-reporting.component';
import { AdminActivityComponent } from './partials/admin-activity/admin-activity.component';
import { AdminEditCandidateProfileComponent } from './partials/admin-edit-candidate-profile/admin-edit-candidate-profile.component';
import { Page404Component } from './partials/page-404/page-404.component';
import { AdminVideoAwaitingComponent } from './partials/admin-video-awaiting/admin-video-awaiting.component';
import { Role } from '../../../entities/models';
import { RoleGuard } from '../../guard/role.guard';
import { AuthGuard } from '../../guard/auth.guard';
import { CanDeactivateGuardGuard } from '../../guard/can-deactivate-guard.guard';
import { AdminApplicantsAwaitingComponent } from './partials/admin-applicants-awaiting/admin-applicants-awaiting.component';
import { AdminApplicantsShortlistComponent } from './partials/admin-applicants-shortlist/admin-applicants-shortlist.component';
import { AdminClientDocumentApprovalComponent } from './partials/admin-client-document-approval/admin-client-document-approval.component';
import { AdminSwitchAccountsComponent } from './partials/admin-switch-accounts/admin-switch-accounts.component';
import { DashboardGuard } from '../../guard/dashboard.guard';

const adminRouters: Routes = [
  { path: '', redirectTo: 'admin', pathMatch: 'full' },
  { path: 'admin', component: AdminComponent, children: [
    { path: '', redirectTo: 'dashboard', pathMatch: 'full' },
    { path: 'dashboard', canDeactivate: [CanDeactivateGuardGuard], canActivate:[ AuthGuard, RoleGuard, DashboardGuard ], data:{roles:[Role.adminRole, Role.superAdminRole]}, component: AdminDashboardComponent },
    { path: 'profile', canDeactivate: [CanDeactivateGuardGuard], canActivate:[ AuthGuard, RoleGuard ], data:{roles:[Role.adminRole, Role.superAdminRole]}, component: ProfileComponent },
    { path: 'new_clients', canDeactivate: [CanDeactivateGuardGuard], canActivate:[ AuthGuard, RoleGuard ], data:{roles:[Role.adminRole, Role.superAdminRole]}, component: AdminNewClientsComponent },
    { path: 'client_document', canDeactivate: [CanDeactivateGuardGuard], canActivate:[ AuthGuard, RoleGuard ], data:{roles:[Role.adminRole, Role.superAdminRole]}, component: AdminClientDocumentApprovalComponent },
    { path: 'all_clients', canDeactivate: [CanDeactivateGuardGuard], canActivate:[ AuthGuard, RoleGuard ], data:{roles:[Role.adminRole, Role.superAdminRole]}, component: AdminAllClientsComponent },
    { path: 'new_candidates', canDeactivate: [CanDeactivateGuardGuard], canActivate:[ AuthGuard, RoleGuard ], data:{roles:[Role.adminRole, Role.superAdminRole]}, component: AdminNewCandidatesComponent },
    { path: 'edit_candidate', canDeactivate: [CanDeactivateGuardGuard], canActivate:[ AuthGuard, RoleGuard ], data:{roles:[Role.adminRole, Role.superAdminRole]}, component: AdminEditCandidateProfileComponent },
    { path: 'all_candidates', canDeactivate: [CanDeactivateGuardGuard], canActivate:[ AuthGuard, RoleGuard ], data:{roles:[Role.adminRole, Role.superAdminRole]}, component: AdminAllCandidatesComponent },
    { path: 'new_jobs', canDeactivate: [CanDeactivateGuardGuard], canActivate:[ AuthGuard, RoleGuard ], data:{roles:[Role.adminRole, Role.superAdminRole]}, component: AdminNewJobsComponent },
    { path: 'all_jobs', canDeactivate: [CanDeactivateGuardGuard], canActivate:[ AuthGuard, RoleGuard ], data:{roles:[Role.adminRole, Role.superAdminRole]}, component: AdminAllJobsComponent },
    { path: 'add_new_client', canDeactivate: [CanDeactivateGuardGuard], canActivate:[ AuthGuard, RoleGuard ], data:{roles:[Role.adminRole, Role.superAdminRole]}, component: AdminAddNewClientComponent },
    { path: 'add_new_job', canDeactivate: [CanDeactivateGuardGuard], canActivate:[ AuthGuard, RoleGuard ], data:{roles:[Role.adminRole, Role.superAdminRole]}, component: AdminAddNewJobComponent },
    { path: 'add_new_candidate', canDeactivate: [CanDeactivateGuardGuard], canActivate:[ AuthGuard, RoleGuard ], data:{roles:[Role.adminRole, Role.superAdminRole]}, component: AdminAddNewCandidateComponent },
    { path: 'all_applicants', canDeactivate: [CanDeactivateGuardGuard], canActivate:[ AuthGuard, RoleGuard ], data:{roles:[Role.adminRole, Role.superAdminRole]}, component: AdminAllApplicantsComponent },
    { path: 'applications_awaiting', canDeactivate: [CanDeactivateGuardGuard], canActivate:[ AuthGuard, RoleGuard ], data:{roles:[Role.adminRole, Role.superAdminRole]}, component: AdminApplicantsAwaitingComponent },
    { path: 'applications_shortlist', canDeactivate: [CanDeactivateGuardGuard], canActivate:[ AuthGuard, RoleGuard ], data:{roles:[Role.adminRole, Role.superAdminRole]}, component: AdminApplicantsShortlistComponent },
    { path: 'set_up_interview', canDeactivate: [CanDeactivateGuardGuard], canActivate:[ AuthGuard, RoleGuard ], data:{roles:[Role.adminRole, Role.superAdminRole]}, component: AdminSetUpInterviewComponent },
    { path: 'pending_interview', canDeactivate: [CanDeactivateGuardGuard], canActivate:[ AuthGuard, RoleGuard ], data:{roles:[Role.adminRole, Role.superAdminRole]}, component: AdminPendingInterviewComponent },
    { path: 'successful_placed', canDeactivate: [CanDeactivateGuardGuard], canActivate:[ AuthGuard, RoleGuard ], data:{roles:[Role.adminRole, Role.superAdminRole]}, component: AdminSuccessfullPlacedComponent },
    { path: 'candidate_document', canDeactivate: [CanDeactivateGuardGuard], canActivate:[ AuthGuard, RoleGuard ], data:{roles:[Role.adminRole, Role.superAdminRole]}, component: AdminDocumentApprovalComponent },
    { path: 'candidate_video', canDeactivate: [CanDeactivateGuardGuard], canActivate:[ AuthGuard, RoleGuard ], data:{roles:[Role.adminRole, Role.superAdminRole]}, component: AdminVideoAwaitingComponent },
    { path: 'manage_system', canDeactivate: [CanDeactivateGuardGuard], canActivate:[ AuthGuard, RoleGuard ], data:{roles:[Role.adminRole, Role.superAdminRole]}, component: AdminManageSystemUsersComponent },
    { path: 'reporting', canDeactivate: [CanDeactivateGuardGuard], canActivate:[ AuthGuard, RoleGuard ], data:{roles:[Role.adminRole, Role.superAdminRole]}, component: AdminReportingComponent },
    { path: 'activity', canDeactivate: [CanDeactivateGuardGuard], canActivate:[ AuthGuard, RoleGuard ], data:{roles:[Role.adminRole, Role.superAdminRole]}, component: AdminActivityComponent },
    { path: 'switch_account', canDeactivate: [CanDeactivateGuardGuard], canActivate:[ AuthGuard, RoleGuard ], data:{roles:[Role.adminRole, Role.superAdminRole]}, component: AdminSwitchAccountsComponent },
    { path: '**', canDeactivate: [CanDeactivateGuardGuard], canActivate:[ AuthGuard, RoleGuard ], data:{roles:[Role.adminRole, Role.superAdminRole]}, component: Page404Component }
  ] },
];

@NgModule({
  imports: [
    RouterModule.forChild(adminRouters)
  ],
  exports: [RouterModule]
})
export class RouterAdminModule {
}
