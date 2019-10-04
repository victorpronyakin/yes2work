import { Component, Input, NgZone, OnInit } from '@angular/core';
import { FormControl, FormGroup, Validators } from '@angular/forms';
import { AdminBusinessProfile } from '../../../../../entities/models-admin';
import { MapsAPILoader } from '@agm/core';
import { AdminService } from '../../../../services/admin.service';
import { SharedService } from '../../../../services/shared.service';
import { ToastrService } from 'ngx-toastr';
import { ValidateNumber } from '../../../../validators/custom.validator';

@Component({
  selector: 'app-admin-client-profile-popup',
  templateUrl: './admin-client-profile-popup.component.html',
  styleUrls: ['./admin-client-profile-popup.component.scss']
})
export class AdminClientProfilePopupComponent implements OnInit {

  private _currentBusinessId: number;
  private _businessList = [];

  @Input() closePopup;
  @Input('currentBusinessId') set currentBusinessId(currentBusinessId: number) {
    if (currentBusinessId) {
      this._currentBusinessId = currentBusinessId;
      this.getDetailsProfileBusiness(currentBusinessId);
    }
  }
  get currentBusinessId(): number {
    return this._currentBusinessId;
  }

  @Input('businessList') set businessList(businessList) {
    if (businessList) {
      this._businessList = businessList;
    }
  }
  get businessList() {
    return this._businessList;
  }

  public businessProfileDetails: AdminBusinessProfile;
  public businessForm: FormGroup;

  public componentForm = {
    street_number: 'short_name',
    route: 'long_name',
    locality: 'long_name',
    administrative_area_level_1: 'short_name',
    country: 'long_name',
    postal_code: 'short_name'
  };

  constructor(
    private readonly _adminService: AdminService,
    private readonly mapsAPILoader: MapsAPILoader,
    private readonly ngZone: NgZone,
    private readonly _sharedService: SharedService,
    private readonly _toastr: ToastrService
  ) {
    this._sharedService.checkSidebar = false;
  }

  ngOnInit() {
    this.businessForm = new FormGroup({
      firstName: new FormControl('', [Validators.required, Validators.minLength(2)]),
      lastName: new FormControl('', [Validators.required, Validators.minLength(2)]),
      jobTitle: new FormControl('', [Validators.required, Validators.minLength(2)]),
      phone: new FormControl('', [
        Validators.required,
        ValidateNumber
      ]),
      email: new FormControl('', Validators.compose([
        Validators.required,
        Validators.email
      ])),
      name: new FormControl('', [Validators.required, Validators.minLength(2)]),
      address: new FormControl('', [Validators.required, Validators.minLength(1)]),
      industry: new FormControl(''),
      description: new FormControl('')
    });
  }

  /**
   * Get details profile business
   * @param id {number}
   * @return {Promise<void>}
   */
  public async getDetailsProfileBusiness(id: number): Promise<void> {

    this.businessProfileDetails = await this._adminService.getDetailsProfileBusiness(id);

    this.businessForm.setValue({
      firstName: this.businessProfileDetails.user.firstName,
      lastName: this.businessProfileDetails.user.lastName,
      jobTitle: this.businessProfileDetails.user.jobTitle,
      phone: this.businessProfileDetails.user.phone,
      email: this.businessProfileDetails.user.email,
      name: this.businessProfileDetails.company.name,
      address: this.businessProfileDetails.company.address,
      industry: this.businessProfileDetails.company.industry,
      description: this.businessProfileDetails.company.description
    });
    this.mapsAPILoader.load().then(() => {
        const autocomplete = new google.maps.places.Autocomplete(
          (<HTMLInputElement>document.getElementById('search1')), { types: ['address'] });

        autocomplete.addListener('place_changed', () => {
          this.ngZone.run(() => {
            const place: google.maps.places.PlaceResult = autocomplete.getPlace();

            this.businessForm.controls.address.setValue(place.formatted_address);

            for (let i = 0; i < place.address_components.length; i++) {
              const addressType = place.address_components[i].types[0];
              if (this.componentForm[addressType]) {
                const valuePlace = place.address_components[i][this.componentForm[addressType]];
                (<HTMLInputElement>document.getElementById(addressType)).value = valuePlace;

                if (addressType === 'street_number') {
                  this.businessForm.controls.addressStreetNumber.setValue(valuePlace);
                } else if (addressType === 'route') {
                  this.businessForm.controls.addressStreet.setValue(valuePlace);
                } else if (addressType === 'locality') {
                  this.businessForm.controls.addressCity.setValue(valuePlace);
                } else if (addressType === 'administrative_area_level_1') {
                  this.businessForm.controls.addressState.setValue(valuePlace);
                } else if (addressType === 'country') {
                  this.businessForm.controls.addressCountry.setValue(valuePlace);
                } else if (addressType === 'postal_code') {
                  this.businessForm.controls.addressZipCode.setValue(valuePlace);
                }
              }
            }
            if(place.geometry === undefined || place.geometry === null ){
              return;
            }
          });
        });
      }
    );
  }

  /**
   * Update profile business
   * @return {Promise<void>}
   */
  public async updateBusinessProfile(): Promise<void> {

    this.businessProfileDetails.company.address = this.businessForm.value.address;
    this.businessProfileDetails.company.description = this.businessForm.value.description;
    this.businessProfileDetails.company.industry = (this.businessForm.value.industry === 'null')
      ? null
      : Number(this.businessForm.value.industry);
    this.businessProfileDetails.company.name = this.businessForm.value.name;

    this.businessProfileDetails.user.email = this.businessForm.value.email;
    this.businessProfileDetails.user.firstName = this.businessForm.value.firstName;
    this.businessProfileDetails.user.jobTitle = this.businessForm.value.jobTitle;
    this.businessProfileDetails.user.lastName = this.businessForm.value.lastName;
    this.businessProfileDetails.user.phone = this.businessForm.value.phone;

    try {
      await this._adminService.updateBusinessProfile(this.businessProfileDetails.user.id, this.businessProfileDetails);

      const getUpdateProfile = this._businessList.find(user => user.id === this.businessProfileDetails.user.id);

      getUpdateProfile.firstName = this.businessForm.value.firstName;
      getUpdateProfile.lastName = this.businessForm.value.lastName;
      getUpdateProfile.companyName = this.businessForm.value.name;
      getUpdateProfile.email = this.businessForm.value.email;
      getUpdateProfile.phone = this.businessForm.value.phone;

      this._toastr.success('Client has been updated');
      this.closePopup();
    } catch (err) {
      this._sharedService.showRequestErrors(err);
    }
  }

  /**
   * Managed business user
   * @param status {boolean}
   * @return {void}
   */
  public async managedBusinessUser(status: boolean): Promise<void> {
    const index = this._businessList.indexOf(this._currentBusinessId);
    await this._adminService.managedBusinessUser(this._currentBusinessId, status);
    this._businessList.splice(index, 1);
    this._toastr.success((status) ? 'Client has been approved' : 'Client has been declined');
    this.closePopup();
  }

}
