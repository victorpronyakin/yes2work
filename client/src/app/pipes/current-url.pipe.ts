import { Pipe, PipeTransform } from '@angular/core';

@Pipe({
  name: 'currentUrl'
})
export class CurrentUrlPipe implements PipeTransform {

  transform(value: any, args?: any): any {
    if(args.indexOf(value) !== -1){
      return true;
    }
    return false;
  }

}
