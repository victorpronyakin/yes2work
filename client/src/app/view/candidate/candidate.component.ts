import { Component, OnInit } from '@angular/core';
import { SharedService } from '../../services/shared.service';

@Component({
  selector: 'app-candidate',
  templateUrl: './candidate.component.html',
  styleUrls: ['./candidate.component.scss']
})
export class CandidateComponent implements OnInit {

  constructor(
    public readonly sharedService: SharedService
  ) { }

  ngOnInit() {
    setTimeout(() => {
      this.sharedService.preloaderView = false;
    }, 3000);
  }

}
