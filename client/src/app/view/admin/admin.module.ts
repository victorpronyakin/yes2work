import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterAdminModule } from './router-admin.module';
import { NgbActiveModal, NgbModule } from '@ng-bootstrap/ng-bootstrap';
import { AdminService } from '../../services/admin.service';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { AdminComponent } from './admin.component';
import { ProfileComponent } from './partials/profile/profile.component';
import { AgmCoreModule } from '@agm/core';
import { BusinessJobViewPopupComponent } from './partials/business-job-view-popup/business-job-view-popup.component';
import { NgxMyDatePickerModule } from 'ngx-mydatepicker';
import { MultiselectDropdownModule } from 'angular-2-dropdown-multiselect';
import { AdminNavbarComponent } from './partials/admin-navbar/admin-navbar.component';
import { AdminSidebarComponent } from './partials/admin-sidebar/admin-sidebar.component';
import { AdminNewJobsComponent } from './partials/admin-new-jobs/admin-new-jobs.component';
import { AdminAllJobsComponent } from './partials/admin-all-jobs/admin-all-jobs.component';
import { AdminNewClientsComponent } from './partials/admin-new-clients/admin-new-clients.component';
import { AdminAllClientsComponent } from './partials/admin-all-clients/admin-all-clients.component';
import { AdminNewCandidatesComponent } from './partials/admin-new-candidates/admin-new-candidates.component';
import { AdminAllCandidatesComponent } from './partials/admin-all-candidates/admin-all-candidates.component';
import { ClientProfilePopupComponent } from './partials/client-profile-popup/client-profile-popup.component';
import { CandidateProfilePopupComponent } from './partials/candidate-profile-popup/candidate-profile-popup.component';
import { AdminClientProfilePopupComponent } from './partials/admin-client-profile-popup/admin-client-profile-popup.component';
import { AdminAddNewClientComponent } from './partials/admin-add-new-client/admin-add-new-client.component';
import { AdminAddNewJobComponent } from './partials/admin-add-new-job/admin-add-new-job.component';
import { AdminAddNewCandidateComponent } from './partials/admin-add-new-candidate/admin-add-new-candidate.component';
import { SharedModule } from '../../modules/shared/shared.module';
import { AdminSetUpInterviewComponent } from './partials/admin-set-up-interview/admin-set-up-interview.component';
import { AdminPendingInterviewComponent } from './partials/admin-pending-interview/admin-pending-interview.component';
import { AdminSuccessfullPlacedComponent } from './partials/admin-successfull-placed/admin-successfull-placed.component';
import { AdminAllApplicantsComponent } from './partials/admin-all-applicants/admin-all-applicants.component';
import { AdminDocumentApprovalComponent } from './partials/admin-document-approval/admin-document-approval.component';
import { AdminManageSystemUsersComponent } from './partials/admin-manage-system-users/admin-manage-system-users.component';
import { AdminCreateAdminPopupComponent } from './partials/admin-create-admin-popup/admin-create-admin-popup.component';
import { AdminEditAdminPopupComponent } from './partials/admin-edit-admin-popup/admin-edit-admin-popup.component';
import { AdminDashboardComponent } from './partials/admin-dashboard/admin-dashboard.component';
import { AdminFooterComponent } from './partials/admin-footer/admin-footer.component';
import { AdminActivityComponent } from './partials/admin-activity/admin-activity.component';
import { AdminReportingComponent } from './partials/admin-reporting/admin-reporting.component';
import { FileSizePipe } from '../../pipes/file-size.pipe';
import { UrlLoggingPipe } from '../../pipes/url-logging.pipe';
import { AdminEditCandidateProfileComponent } from './partials/admin-edit-candidate-profile/admin-edit-candidate-profile.component';
import { Page404Component } from './partials/page-404/page-404.component';
import { AdminVideoAwaitingComponent } from './partials/admin-video-awaiting/admin-video-awaiting.component';
import { AdminConfirmPopupComponent } from './partials/admin-confirm-popup/admin-confirm-popup.component';
import { AdminApplicantsAwaitingComponent } from './partials/admin-applicants-awaiting/admin-applicants-awaiting.component';
import { AdminApplicantsShortlistComponent } from './partials/admin-applicants-shortlist/admin-applicants-shortlist.component';
import { AdminClientDocumentApprovalComponent } from './partials/admin-client-document-approval/admin-client-document-approval.component';
import { PaginationService } from '../../services/pagination.service';
import { AdminSwitchAccountsComponent } from './partials/admin-switch-accounts/admin-switch-accounts.component';

@NgModule({
  imports: [
    CommonModule,
    RouterAdminModule,
    NgxMyDatePickerModule,
    MultiselectDropdownModule,
    NgbModule,
    FormsModule,
    ReactiveFormsModule,
    AgmCoreModule.forRoot(  {
      apiKey: 'AIzaSyCliFm7C1H1t_O5MFiN-SB3luq867neo4Y',
      libraries: ['places']
    }),
    SharedModule
  ],
  declarations: [
    AdminComponent,
    ProfileComponent,
    BusinessJobViewPopupComponent,
    AdminNavbarComponent,
    AdminSidebarComponent,
    AdminNewJobsComponent,
    AdminAllJobsComponent,
    AdminNewClientsComponent,
    AdminAllClientsComponent,
    AdminNewCandidatesComponent,
    AdminAllCandidatesComponent,
    ClientProfilePopupComponent,
    CandidateProfilePopupComponent,
    AdminClientProfilePopupComponent,
    AdminAddNewClientComponent,
    AdminAddNewJobComponent,
    AdminAddNewCandidateComponent,
    AdminSetUpInterviewComponent,
    AdminPendingInterviewComponent,
    AdminSuccessfullPlacedComponent,
    AdminAllApplicantsComponent,
    AdminDocumentApprovalComponent,
    AdminManageSystemUsersComponent,
    AdminCreateAdminPopupComponent,
    AdminEditAdminPopupComponent,
    AdminDashboardComponent,
    AdminFooterComponent,
    AdminActivityComponent,
    AdminReportingComponent,
    FileSizePipe,
    UrlLoggingPipe,
    AdminEditCandidateProfileComponent,
    Page404Component,
    AdminVideoAwaitingComponent,
    AdminConfirmPopupComponent,
    AdminApplicantsAwaitingComponent,
    AdminApplicantsShortlistComponent,
    AdminClientDocumentApprovalComponent,
    AdminSwitchAccountsComponent
  ],
  providers: [
    AdminService,
    PaginationService,
    NgbActiveModal
  ]
})

export class AdminModule { }
