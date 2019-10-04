import { Pipe, PipeTransform } from '@angular/core';
import {DomSanitizer} from "@angular/platform-browser";

@Pipe({
  name: 'urlType'
})
export class UrlTypePipe implements PipeTransform {

  constructor(private readonly _sanitizer: DomSanitizer){}

  transform(url: string) {
    if(url) {
      let newUrl = url.split('.');
      if(newUrl.length > 0) {
        const lastSplit = newUrl[newUrl.length - 1];
        if(lastSplit === 'doc' || lastSplit === 'docx'){
          url = 'https://docs.google.com/gview?url='+url+'&embedded=true';
        }
        return this._sanitizer.bypassSecurityTrustResourceUrl(url);
      }
      else {
        return url;
      }
    }
    else {
      return url;
    }
  }

}
