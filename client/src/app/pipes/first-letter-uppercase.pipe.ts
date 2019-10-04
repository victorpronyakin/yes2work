import { Pipe, PipeTransform } from '@angular/core';

@Pipe({
  name: 'firstLetterUppercase'
})
export class FirstLetterUppercasePipe implements PipeTransform {

  transform(value: any, args?: any): any {
    let newWord = '';
    if (value) {
      newWord = value[0].toUpperCase() + value.slice(1);
    }
    return newWord;
  }

}
