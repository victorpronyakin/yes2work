import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { LoginComponent } from './login/login.component';
import { CandidateRegisterComponent } from './candidate-register/candidate-register.component';
import { BusinessRegisterComponent } from './business-register/business-register.component';
import { LoginGuard } from '../../guard/login.guard';
import { ForgotComponent } from './forgot/forgot.component';
import { AuthComponent } from './auth.component';

const routes: Routes = [
  { path: '', redirectTo: '', pathMatch: 'full' },
  { path: '', component: AuthComponent, children: [
    { path: '', redirectTo: '', pathMatch: 'full' },
    { path: 'login', canActivate: [ LoginGuard ], component: LoginComponent },
    { path: 'register/business', canActivate: [ LoginGuard ], component: BusinessRegisterComponent },
    { path: 'register/candidate', canActivate: [ LoginGuard ], component: CandidateRegisterComponent },
    { path: 'resetPassword', component: ForgotComponent }
  ]}
];

@NgModule({
  imports: [
    RouterModule.forChild(routes)
  ],
  exports: [ RouterModule ]
})
export class RouterAuthModule { }
