import { NgModule } from '@angular/core';
import {PreloadAllModules, RouterModule, Routes} from '@angular/router';
import { Page404Component } from './page-404/page-404.component';

const appRoutes: Routes = [
    { path: '', loadChildren: 'app/view/landing/landing.module#LandingModule' },
    { path: '', loadChildren: 'app/view/admin/admin.module#AdminModule' },
    { path: '', loadChildren: 'app/view/business/business.module#BusinessModule' },
    { path: '', loadChildren: 'app/view/candidate/candidate.module#CandidateModule' },
    { path: '', loadChildren: 'app/view/auth/auth.module#AuthModule' },
    { path: '**', component: Page404Component }
];

@NgModule({
  imports: [
    RouterModule.forRoot(appRoutes)
  ],
  exports: [RouterModule]
})
export class AppRouterModule { }


// @NgModule({
//   imports: [
//     RouterModule.forRoot(appRoutes, {
//       preloadingStrategy: PreloadAllModules
//     })
//   ],
//   exports: [RouterModule]
// })
// export class AppRouterModule { }
