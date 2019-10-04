import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ReplacePipe } from '../../pipes/replace.pipe';
import { ClosureDayPipe } from '../../pipes/closure-day.pipe';
import { DaysLappedPipe } from '../../pipes/days-lapped.pipe';
import { ReactiveFormsModule } from '@angular/forms';
import { NgSelectModule } from '@ng-select/ng-select';
import { IndustryListPipe } from '../../pipes/industry-list.pipe';
import { CurrentUrlPipe } from '../../pipes/current-url.pipe';
import { SafePipe } from '../../pipes/safe.pipe';
import { UrlTypePipe } from '../../pipes/url-type.pipe';
import { InternationalPhoneModule } from 'ng4-intl-phone';
import { DpDatePickerModule } from 'ng2-date-picker';
import { FirstLetterUppercasePipe } from '../../pipes/first-letter-uppercase.pipe';
import { Ng5SliderModule } from 'ng5-slider';
import { WebcamModule } from 'ngx-webcam';

@NgModule({
  imports: [
    CommonModule,
    ReactiveFormsModule,
    NgSelectModule,
    InternationalPhoneModule,
    DpDatePickerModule,
    WebcamModule,
    Ng5SliderModule
  ],
  declarations: [
    ReplacePipe,
    ClosureDayPipe,
    DaysLappedPipe,
    IndustryListPipe,
    CurrentUrlPipe,
    SafePipe,
    UrlTypePipe,
    FirstLetterUppercasePipe
  ],
  providers: [
  ],
  exports: [
    ReplacePipe,
    ClosureDayPipe,
    DaysLappedPipe,
    NgSelectModule,
    IndustryListPipe,
    CurrentUrlPipe,
    SafePipe,
    UrlTypePipe,
    InternationalPhoneModule,
    DpDatePickerModule,
    FirstLetterUppercasePipe,
    WebcamModule,
    Ng5SliderModule
  ]
})
export class SharedModule { }
