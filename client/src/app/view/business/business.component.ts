import { Component, OnInit } from '@angular/core';
import { SharedService } from '../../services/shared.service';

@Component({
  selector: 'app-business',
  templateUrl: './business.component.html',
  styleUrls: ['./business.component.scss']
})
export class BusinessComponent implements OnInit {

  constructor(
    public readonly sharedService: SharedService
  ) { }

  ngOnInit() {
    setTimeout(() => {
      this.sharedService.preloaderView = false;
    }, 3000);
  }

}
