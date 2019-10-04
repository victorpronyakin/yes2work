import { Pipe, PipeTransform } from '@angular/core';
import {MapsAPILoader} from "@agm/core";

@Pipe({
  name: 'distance'
})
export class DistancePipe implements PipeTransform {

  public callbackBind = this.callback.bind(this);

  constructor(
    private readonly _mapsAPILoader: MapsAPILoader
  ) { }

  public tst;

  transform(value: any, args?: any): any {
    if (value){
      this._mapsAPILoader.load().then(() => {
        const distance = new google.maps.DistanceMatrixService();
        distance.getDistanceMatrix(
          {
            origins: [value],
            destinations: [args],
            travelMode: google.maps.TravelMode.DRIVING,
          }, this.callbackBind);
      });
    }
    else {
      return this.tst;
    }
    return this.tst;
  }

  public callback (response, status) {
    let newDistance;
    newDistance = response;

    if (newDistance) {
      if(newDistance.rows){
        this.tst = newDistance.rows[0].elements[0].distance.text;
      }
      else {
        this.tst = '0 km';
      }
    }
    else {
      this.tst = '0 km';
    }
    return this.tst;
  }

}
