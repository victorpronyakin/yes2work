import {Component, OnInit} from '@angular/core';
import {CandidateService} from '../../../../services/candidate.service';
import {SharedService} from '../../../../services/shared.service';
import {ToastrService} from 'ngx-toastr';

declare const PipeSDK: any;

@Component({
  selector: 'app-candidate-video-recording',
  templateUrl: './candidate-video-recording.component.html',
  styleUrls: ['./candidate-video-recording.component.scss']
})
export class CandidateVideoRecordingComponent implements OnInit {
  public recorderObject: any;
  public pipeParams = {
    size:
      {
        width: '100%',
        height: 400
      },
    qualityurl: 'avq/360p.xml',
    accountHash: '8e1b7aa07e4ad7cb2abb6ee1edc8fa9d',
    eid: 1,
    mrt: 120,
    dup: 1,
    cornerradius: 5,
    bgCol: '0xffffff',
    menuCol: '0x82118d',
    normalCol: '0x82118d',
    overCol: '0x82118d'
  };

  constructor(public readonly _candidateService: CandidateService,
              public readonly _sharedService: SharedService,
              public readonly _toastr: ToastrService) {
  }

  ngOnInit() {
    PipeSDK.insert('custom-id', this.pipeParams, function (recorderObject) {
      console.log(recorderObject);
    });


    // const size = {width: 640, height: 390};
    // const flashvars = {qualityurl: 'avq/360p.xml', accountHash: '953248c53e99c027fa6f2bc439b35e3a', eid: 1, mrt: 120};
    // setTimeout(() => {
    //   const pipe = document.createElement('script');
    //   pipe.type = 'text/javascript';
    //   pipe.async = true;
    //   pipe.src = ('https:' === document.location.protocol ? 'https://' : 'http://') + 'cdn.addpipe.com/1.3/pipe.js';
    //   const s = document.getElementsByTagName('script')[0];
    //   s.parentNode.insertBefore(pipe, s);
    // });
  }
}
