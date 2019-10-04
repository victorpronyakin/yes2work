import { Pipe, PipeTransform } from '@angular/core';

@Pipe({
  name: 'industryList'
})
export class IndustryListPipe implements PipeTransform {

  transform(value: any, args?: any): any {
    if (value !== undefined && value !== null && typeof value === 'object') {
      return value.join(', ');
    }
    return null;
  }

}
