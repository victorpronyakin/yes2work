import { Pipe, PipeTransform } from '@angular/core';
import { DatePipe } from '@angular/common';

@Pipe({
  name: 'availabilityDate'
})
export class AvailabilityDatePipe implements PipeTransform {

  transform(value: any, args?: any): any {
    const now = new Date();
    const closureDay = new Date(value);
    const dateP = new DatePipe("en-US");
    if (closureDay.getTime() < now.getTime()){
      return 'Immediately';
    } else {
      return dateP.transform(closureDay, 'MMM-dd-yyyy');
    }
  }

}
