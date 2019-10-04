import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterAuthModule } from "./router-auth.module";
import { FormsModule, ReactiveFormsModule } from "@angular/forms";
import { NgbModule } from '@ng-bootstrap/ng-bootstrap';
import { SharedModule } from '../../modules/shared/shared.module';
import { ForgotComponent } from './forgot/forgot.component';
import { LoginComponent } from './login/login.component';
import { CandidateRegisterComponent } from './candidate-register/candidate-register.component';
import { BusinessRegisterComponent } from './business-register/business-register.component';
import { AuthComponent } from './auth.component';
import { NgxMyDatePickerModule } from 'ngx-mydatepicker';
import {HttpClientJsonpModule, HttpClientModule} from "@angular/common/http";
import {ShareButtonModule, ShareButtonsModule} from "ngx-sharebuttons";

const customConfig = {
  include: ['facebook', 'twitter'],
  exclude: [],
  theme: 'modern-light',
  gaTracking: true,
  twitterAccount: 'Yes2Work',
  prop: {
    facebook: {
      icon: ['fab', 'facebook-square'],
    },
    twitter: {
      icon: ['fab', 'twitter-square'],
      text: 'Tweet'
    }
  }
};

@NgModule({
  imports: [
    CommonModule,
    RouterAuthModule,
    FormsModule,
    ReactiveFormsModule,
    NgbModule,
    SharedModule,
    NgxMyDatePickerModule.forRoot(),
    HttpClientModule,
    HttpClientJsonpModule,
    ShareButtonsModule.forRoot(customConfig),
    ShareButtonModule.forRoot(customConfig)
  ],
  declarations: [
    ForgotComponent,
    LoginComponent,
    CandidateRegisterComponent,
    BusinessRegisterComponent,
    AuthComponent
  ]
})
export class AuthModule { }
