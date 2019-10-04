import { Component, Input, NgZone, OnInit } from '@angular/core';
import { FormControl, FormGroup, Validators } from '@angular/forms';
import { AdminBusinessProfile } from '../../../../../entities/models-admin';
import { AdminService } from '../../../../services/admin.service';
import { MapsAPILoader } from "@agm/core";
import {} from '@types/googlemaps';
import { SharedService } from '../../../../services/shared.service';
import { ToastrService } from 'ngx-toastr';
import { articles } from '../../../../constants/articles.const';
import { industry } from '../../../../constants/industry.const';
import { IMultiSelectOption, IMultiSelectSettings, IMultiSelectTexts } from 'angular-2-dropdown-multiselect';
import { ValidateNumber } from '../../../../validators/custom.validator';

@Component({
  selector: 'app-client-profile-popup',
  templateUrl: './client-profile-popup.component.html',
  styleUrls: ['./client-profile-popup.component.scss']
})
export class ClientProfilePopupComponent implements OnInit {

  private _currentBusinessId: number;
  private _businessList = [];
  public preloaderPopup = true;

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

  public modalActiveClose: any;

  public componentForm = {
    street_number: 'short_name',
    route: 'long_name',
    locality: 'long_name',
    administrative_area_level_1: 'short_name',
    country: 'long_name',
    sublocality_level_2: 'long_name',
    postal_code: 'short_name'
  };
  public articles = articles;

  public jse: boolean;

  public articlesFirmTextConfigBus: IMultiSelectTexts = {
    checkAll: 'Select all',
    uncheckAll: 'Deselect all',
    checked: 'item selected',
    checkedPlural: 'items selected',
    defaultTitle: 'Industry',
    allSelected: 'All selected',
  };
  public articlesFirmSettingsBus: IMultiSelectSettings = {
    displayAllSelectedText: true,
    selectionLimit: 0,
    showCheckAll: true,
    showUncheckAll: true,
  };
  public optionsModelBus: string[];
  public indistrySelect: IMultiSelectOption[] = industry;

  constructor(
    private readonly _adminService: AdminService,
    private readonly _mapsAPILoader: MapsAPILoader,
    private readonly _ngZone: NgZone,
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
      agentName: new FormControl(''),
      name: new FormControl('', [Validators.required, Validators.minLength(2)]),
      address: new FormControl('', Validators.required),
      addressCountry: new FormControl('', Validators.required),
      addressState: new FormControl('', Validators.required),
      addressZipCode: new FormControl('', Validators.required),
      addressCity: new FormControl('', Validators.required),
      addressSuburb: new FormControl('', Validators.required),
      addressStreet: new FormControl('', Validators.required),
      addressStreetNumber: new FormControl('', Validators.required),
      addressBuildName: new FormControl(''),
      addressUnit: new FormControl(''),
      industry: new FormControl(null, Validators.required),
      jse: new FormControl(''),
      companySize: new FormControl(null, Validators.required),
      description: new FormControl('', Validators.compose([
        Validators.required,
        Validators.maxLength(300)
      ]))
    });
  }

  /**
   * jse value check
   */
  public jseValue(value): void{
    value = !value;
    // this.jse = !this.jse;
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
      agentName: this.businessProfileDetails.user.agentName,
      name: this.businessProfileDetails.company.name,
      address: this.businessProfileDetails.company.address,
      addressCountry: this.businessProfileDetails.company.addressCountry,
      addressState: this.businessProfileDetails.company.addressState,
      addressZipCode: this.businessProfileDetails.company.addressZipCode,
      addressCity: this.businessProfileDetails.company.addressCity,
      addressSuburb: this.businessProfileDetails.company.addressSuburb,
      addressStreet: this.businessProfileDetails.company.addressStreet,
      addressStreetNumber: this.businessProfileDetails.company.addressStreetNumber,
      addressBuildName: this.businessProfileDetails.company.addressBuildName,
      addressUnit: this.businessProfileDetails.company.addressUnit,
      industry: this.businessProfileDetails.company.industry,
      jse: (this.businessProfileDetails.company.jse === null) ? false : this.businessProfileDetails.company.jse,
      companySize: this.businessProfileDetails.company.companySize,
      description: this.businessProfileDetails.company.description
    });

    this.jse = this.businessProfileDetails.company.jse;
    this._mapsAPILoader.load().then(() => {
        const autocomplete = new google.maps.places.Autocomplete(
          (<HTMLInputElement>document.getElementById('search1')), { types: ['address'] });

        autocomplete.addListener('place_changed', () => {
          this._ngZone.run(() => {
            const place: google.maps.places.PlaceResult = autocomplete.getPlace();

            this.businessForm.controls.address.setValue(place.formatted_address);
            this.businessForm.controls['addressStreetNumber'].setValue('');
            this.businessForm.controls['addressStreet'].setValue('');
            this.businessForm.controls['addressSuburb'].setValue('');
            this.businessForm.controls['addressCity'].setValue('');
            this.businessForm.controls['addressState'].setValue('');
            this.businessForm.controls['addressCountry'].setValue('');
            this.businessForm.controls['addressZipCode'].setValue('');
            for (let i = 0; i < place.address_components.length; i++) {
              let addressType = place.address_components[i].types[0];
              if (addressType === 'sublocality_level_1') {
                addressType = 'sublocality_level_2';
              }
              if (this.componentForm[addressType]) {
                const valuePlace = place.address_components[i][this.componentForm[addressType]];
                (<HTMLInputElement>document.getElementById(addressType)).value = valuePlace;

                if (addressType === 'street_number') {
                  this.businessForm.controls.addressStreetNumber.setValue(valuePlace);
                } else if (addressType === 'route') {
                  this.businessForm.controls.addressStreet.setValue(valuePlace);
                } else if (addressType === 'sublocality_level_2') {
                  this.businessForm.controls.addressSuburb.setValue(valuePlace);
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
    this.preloaderPopup = false;
  }

  /**
   * Update profile business
   * @return {Promise<void>}
   */
  public async updateBusinessProfile(): Promise<void> {

    this.businessProfileDetails.company.address = this.businessForm.value.address;
    this.businessProfileDetails.company.addressCountry = this.businessForm.value.addressCountry;
    this.businessProfileDetails.company.addressState = this.businessForm.value.addressState;
    this.businessProfileDetails.company.addressZipCode = this.businessForm.value.addressZipCode;
    this.businessProfileDetails.company.addressCity = this.businessForm.value.addressCity;
    this.businessProfileDetails.company.addressSuburb = this.businessForm.value.addressSuburb;
    this.businessProfileDetails.company.addressStreet = this.businessForm.value.addressStreet;
    this.businessProfileDetails.company.addressStreetNumber = this.businessForm.value.addressStreetNumber;
    this.businessProfileDetails.company.addressBuildName = this.businessForm.value.addressBuildName;
    this.businessProfileDetails.company.addressUnit = this.businessForm.value.addressUnit;
    this.businessProfileDetails.company.companySize = (this.businessForm.value.companySize === null)
      ? null
      : Number(this.businessForm.value.companySize);
    this.businessProfileDetails.company.description = this.businessForm.value.description;
    this.businessProfileDetails.company.industry = (this.businessForm.value.industry === undefined)
      ? null
      : this.businessForm.value.industry;
    this.businessProfileDetails.company.jse = this.jse;
    this.businessProfileDetails.company.name = this.businessForm.value.name;

    this.businessProfileDetails.user.email = this.businessForm.value.email;
    this.businessProfileDetails.user.firstName = this.businessForm.value.firstName;
    this.businessProfileDetails.user.jobTitle = this.businessForm.value.jobTitle;
    this.businessProfileDetails.user.lastName = this.businessForm.value.lastName;
    this.businessProfileDetails.user.phone = this.businessForm.value.phone;
    this.businessProfileDetails.user.agentName = this.businessForm.value.agentName;

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

}
