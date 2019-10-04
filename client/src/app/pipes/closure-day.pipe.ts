import { Pipe, PipeTransform } from '@angular/core';

@Pipe({
  name: 'closureDay'
})
export class ClosureDayPipe implements PipeTransform {

  transform(value: any, args?: any): any {
    const now = new Date();
    const closureDay = new Date(value);
    const diffDays = Math.round((closureDay.getTime() - now.getTime()) / (1000 * 60 * 60 * 24)) + 1;
    return diffDays;
  }

}
