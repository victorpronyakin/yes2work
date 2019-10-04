import { Pipe, PipeTransform } from '@angular/core';

@Pipe({
  name: 'dateLeft'
})
export class DateLeftPipe implements PipeTransform {

  transform(value: any, args?: any): any {
    if (value){
      const now = new Date();
      const closureDay = new Date(value);
      let diffDays = Math.round((closureDay.getTime() - now.getTime()) / (1000 * 60 * 60 * 24)) + 1;
      if(diffDays > 0){
        return diffDays;
      }
      else {
        return '0';
      }
    }
    else {
      return '-';
    }
  }

}
