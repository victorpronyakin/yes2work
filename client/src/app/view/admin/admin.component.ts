import { Component, OnInit } from '@angular/core';
import { SharedService } from '../../services/shared.service';

@Component({
  selector: 'app-admin',
  templateUrl: './admin.component.html',
  styleUrls: ['./admin.component.scss']
})
export class AdminComponent implements OnInit {

  constructor(
    public readonly sharedService: SharedService
  ) {
  }

  ngOnInit() {
    setTimeout(() => {
      this.sharedService.preloaderView = false;
    }, 3000);
  }
}
