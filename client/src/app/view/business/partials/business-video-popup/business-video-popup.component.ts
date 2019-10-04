import { Component, Input, OnInit } from '@angular/core';
import { BusinessService } from '../../../../services/business.service';
import { SharedService } from '../../../../services/shared.service';

@Component({
  selector: 'app-business-video-popup',
  templateUrl: './business-video-popup.component.html',
  styleUrls: ['./business-video-popup.component.scss']
})
export class BusinessVideoPopupComponent implements OnInit {

  public _candidateToView: any;

  @Input() closePopup;
  @Input('candidateToView') set candidateToView(candidateToView) {
    if (candidateToView) {
      this._candidateToView = candidateToView;
    }
  }
  get candidateToView() {
    return this._candidateToView;
  }

  constructor(
    private readonly _businessService: BusinessService,
    private readonly _sharedService: SharedService
  ) { }

  ngOnInit() {
  }

  /**
   * Set status candidate profile
   * @param candidateID {number}
   * @param action {string}
   * @return {Promise<void>}
   */
  public async setStatusCandidateProfile(candidateID: number, action: string): Promise<void> {
    try {
      await this._businessService.setStatusCandidateProfile(candidateID, action);
    }
    catch (err) {
      this._sharedService.showRequestErrors(err);
    }
  }

}
