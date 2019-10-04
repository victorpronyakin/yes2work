import { Component, ElementRef, OnInit, ViewChild } from '@angular/core';
import { SharedService } from '../../../services/shared.service';
import { Router } from '@angular/router';

@Component({
  selector: 'app-landing',
  templateUrl: './landing.component.html',
  styleUrls: ['./landing.component.scss']
})
export class LandingComponent implements OnInit {

  public checkSidebar = false;

  @ViewChild('backgroundVideo') backgroundVideo:ElementRef;

  constructor(
      private readonly _router: Router,
      public readonly _sharedService: SharedService
  ) {
    this._sharedService.checkSidebar = false;
  }

  ngOnInit() {
    /*if(this.getOS() === 'iOS'){
        this._router.navigate(['/home']);
    }*/
    this.backgroundVideo.nativeElement.muted = true;
    this.backgroundVideo.nativeElement.play();
  }

  public getOS(){
    const userAgent = window.navigator.userAgent,
        platform = window.navigator.platform,
        macosPlatforms = ['Macintosh', 'MacIntel', 'MacPPC', 'Mac68K'],
        windowsPlatforms = ['Win32', 'Win64', 'Windows', 'WinCE'],
        iosPlatforms = ['iPhone', 'iPad', 'iPod'];
    let os = null;

    if (macosPlatforms.indexOf(platform) !== -1) {
        os = 'Mac OS';
    } else if (iosPlatforms.indexOf(platform) !== -1) {
        os = 'iOS';
    } else if (windowsPlatforms.indexOf(platform) !== -1) {
        os = 'Windows';
    } else if (/Android/.test(userAgent)) {
        os = 'Android';
    } else if (!os && /Linux/.test(platform)) {
        os = 'Linux';
    }

    return os;
  }

  public closeSidebar(): void {
    this._sharedService.checkSidebar = false;
  }

  public openSidebar(): void {
    this._sharedService.checkSidebar = true;
  }

}
