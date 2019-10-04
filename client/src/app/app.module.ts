import { BrowserModule } from '@angular/platform-browser';
import { NgModule } from '@angular/core';
import { AppComponent } from './app.component';
import { ReactiveFormsModule } from '@angular/forms';
import { HttpClientModule } from '@angular/common/http';
import { ApiService} from './services/api.service';
import { SettingsService } from './services/settings.service';
import { AuthService } from './services/auth.service';
import { AdminModule } from './view/admin/admin.module';
import { CandidateModule } from './view/candidate/candidate.module';
import { BusinessModule } from './view/business/business.module';
import { LandingModule } from './view/landing/landing.module';
import { AuthGuard } from './guard/auth.guard';
import { RoleGuard } from './guard/role.guard';
import { LoginGuard } from './guard/login.guard';
import { AppRouterModule } from './app-router.module';
import { NgbModule } from '@ng-bootstrap/ng-bootstrap';
import { BrowserAnimationsModule } from '@angular/platform-browser/animations';
import { ToastrModule } from 'ngx-toastr';
import { SettingsApiService } from './services/settings-api.service';
import { GooglePlaceModule } from 'ngx-google-places-autocomplete';
import { SharedService } from './services/shared.service';
import { MultiselectDropdownModule } from 'angular-2-dropdown-multiselect';
import { CommonModule } from '@angular/common';
import { Page404Component } from './page-404/page-404.component';
import { SharedModule } from './modules/shared/shared.module';
import { AuthModule } from './view/auth/auth.module';
import { CanDeactivateGuardGuard } from './guard/can-deactivate-guard.guard';
import { CheckCandidateCompleteGuard } from './guard/check-candidate-complete.guard';
import { DashboardGuard } from './guard/dashboard.guard';

@NgModule({
  declarations: [
    AppComponent,
    Page404Component
  ],
  imports: [
    CommonModule,
    BrowserModule,
    LandingModule,
    AdminModule,
    AuthModule,
    MultiselectDropdownModule,
    CandidateModule,
    BusinessModule,
    HttpClientModule,
    ReactiveFormsModule,
    AppRouterModule,
    NgbModule.forRoot(),
    BrowserAnimationsModule,
    ToastrModule.forRoot(),
    GooglePlaceModule,
    SharedModule
  ],
  providers: [
    ApiService,
    AuthService,
    SettingsService,
    AuthGuard,
    RoleGuard,
    LoginGuard,
    SettingsApiService,
    SharedService,
    CanDeactivateGuardGuard,
    CheckCandidateCompleteGuard,
    DashboardGuard
  ],
  bootstrap: [AppComponent]
})
export class AppModule { }
