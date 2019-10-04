import { Pipe, PipeTransform } from '@angular/core';

@Pipe({
  name: 'notSpace'
})


export class ReplacePipe implements PipeTransform {
  public stringValue: string;

  transform(value: any, args?: any): any {
    if (value !== null) {
      this.stringValue = value.replace(/\s+/g,'');
    }
    return this.stringValue;
  }

}
