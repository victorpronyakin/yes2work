import { Pipe, PipeTransform } from '@angular/core';

@Pipe({
  name: 'urlLogging'
})
export class UrlLoggingPipe implements PipeTransform {

  public url = [
    {id: 1, url: '/admin/add_new_candidate'},
    {id: 2, url: '/admin/add_new_candidate'},
    {id: 3, url: '/admin/all_candidates'},
    {id:4, url: '/admin/all_candidates'},
    {id:5, url: '/admin/all_candidates'},
    {id:6, url: '/admin/candidate_document'},
    {id:7, url: '/admin/candidate_document'},
    {id:8, url: '/admin/new_candidates'},
    {id:9, url: '/admin/new_candidates'},
    {id: 10, url: '/admin/add_new_client'},
    {id: 11, url: '/admin/all_clients'},
    {id: 12, url: '/admin/all_clients'},
    {id: 13, url: '/admin/all_clients'},
    {id: 14, url: '/admin/all_clients'},
    {id: 15, url: '/admin/new_clients'},
    {id: 16, url: '/admin/new_clients'},
    {id: 17, url: '/admin/add_new_job'},
    {id: 18, url: '/admin/all_jobs'},
    {id: 19, url: '/admin/all_jobs'},
    {id: 20, url: '/admin/all_jobs'},
    {id: 21, url: '/admin/all_jobs'},
    {id: 22, url: '/admin/new_jobs'},
    {id: 23, url: '/admin/new_jobs'},
    {id: 24, url: '/admin/manage_system'},
    {id: 25, url: '/admin/manage_system'},
    {id: 26, url: '/admin/manage_system'},
    {id: 27, url: '/admin/candidate_video'},
    {id: 28, url: '/admin/candidate_video'},
    {id: 29, url: '/admin/candidate_video'},
    {id: 30, url: '/admin/candidate_video'},
  ];

  transform(value: any, args?: any): any {

    let urlOption = this.url.find(user => user.id === value);
    return urlOption.url;
  }

}
