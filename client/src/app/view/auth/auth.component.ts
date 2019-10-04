import { Component, ElementRef, OnInit, ViewChild } from '@angular/core';
import { SharedService } from '../../services/shared.service';

@Component({
  selector: 'app-auth',
  templateUrl: './auth.component.html',
  styleUrls: ['./auth.component.scss']
})
export class AuthComponent implements OnInit {

  public checkSidebar = false;

  @ViewChild('backgroundVideo') backgroundVideo:ElementRef;

  constructor(
    public readonly sharedService: SharedService
  ) { }

  ngOnInit() {
    this.backgroundVideo.nativeElement.muted = true;
    this.backgroundVideo.nativeElement.play();
  }

  public closeSidebar(): void {
    this.sharedService.checkSidebar = false;
  }

  public openSidebar(): void {
    this.sharedService.checkSidebar = true;
  }

}
