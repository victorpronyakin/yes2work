import { Component, OnInit } from '@angular/core';
import { SharedService } from '../../../../services/shared.service';

@Component({
  selector: 'app-admin-reporting',
  templateUrl: './admin-reporting.component.html',
  styleUrls: ['./admin-reporting.component.scss']
})
export class AdminReportingComponent implements OnInit {

  public preloaderPage = true;

  constructor(
    private readonly _sharedService: SharedService
  ) {
    this._sharedService.checkSidebar = false;
  }

  ngOnInit() {
    window.scrollTo(0, 0);
    setTimeout(() => {
      this.preloaderPage = false;
    }, 2000);
  }

}
