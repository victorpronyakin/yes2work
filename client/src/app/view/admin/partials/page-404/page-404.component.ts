import { Component, OnInit } from '@angular/core';
import { SharedService } from '../../../../services/shared.service';

@Component({
  selector: 'app-page-404',
  templateUrl: './page-404.component.html',
  styleUrls: ['./page-404.component.scss']
})
export class Page404Component implements OnInit {

  constructor(
    public readonly sharedService: SharedService
  ) { }

  ngOnInit() {
    setTimeout(() => {
      this.sharedService.preloaderView = false;
    }, 3000);
  }

}
