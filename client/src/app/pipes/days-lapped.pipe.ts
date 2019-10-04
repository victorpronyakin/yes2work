import { Pipe, PipeTransform } from '@angular/core';

@Pipe({
  name: 'daysLapped'
})
export class DaysLappedPipe implements PipeTransform {

  transform(value: any, args?: any): any {
    const now = new Date();
    const daysLapped = new Date(value);
    const diffDays = Math.round((now.getTime() - daysLapped.getTime()) / (1000 * 60 * 60 * 24));
    return diffDays;
  }

}
