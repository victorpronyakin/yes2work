import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterLandingModule } from './router-landing.module';
import { LandingComponent } from './landing/landing.component';

@NgModule({
  imports: [
    CommonModule,
    RouterLandingModule
  ],
  declarations: [
    LandingComponent
  ]
})
export class LandingModule { }
