import { Component, NgZone, OnInit } from '@angular/core';
import { FormControl, FormGroup, Validators } from '@angular/forms';
import { SharedService } from '../../../../services/shared.service';
import { ToastrService } from 'ngx-toastr';
import { MapsAPILoader } from '@agm/core';
import { AdminService } from '../../../../services/admin.service';
import { articles } from '../../../../constants/articles.const';
import { AdminBusinessProfile } from '../../../../../entities/models-admin';
import { Router } from '@angular/router';
import { industry } from '../../../../constants/industry.const';
import { IMultiSelectOption, IMultiSelectSettings, IMultiSelectTexts } from 'angular-2-dropdown-multiselect';
import { ValidateNumber } from '../../../../validators/custom.validator';

@Component({
  selector: 'app-admin-add-new-client',
  templateUrl: './admin-add-new-client.component.html',
  styleUrls: ['./admin-add-new-client.component.scss']
})
export class AdminAddNewClientComponent implements OnInit {

  public businessProfileDetails: AdminBusinessProfile;
  public businessForm: FormGroup;

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

  public preloaderPage = true;

  public jse: boolean;

  constructor(
    private readonly _adminService: AdminService,
    private readonly _mapsAPILoader: MapsAPILoader,
    private readonly _sharedService: SharedService,
    private readonly _toastr: ToastrService,
    private readonly _ngZone: NgZone,
    private readonly _router: Router
  ) {
    this._sharedService.checkSidebar = false;
  }

  ngOnInit() {
    window.scrollTo(0, 0);
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
      jse: new FormControl(false),
      companySize: new FormControl(null, Validators.required),
      description: new FormControl('', Validators.compose([
        Validators.required,
        Validators.maxLength(200),
      ]))
    });

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
                } else if (addressType === 'sublocality_level_2') {
                  this.businessForm.controls.addressSuburb.setValue(valuePlace);
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
    setTimeout(() => {
      this.preloaderPage = false;
    }, 500);
  }

  /**
   * jse value check
   */
  public jseValue(field, value): void{
    value = !value;
  }

  /**
   * Create profile business
   * @return {Promise<void>}
   */
  public async createBusinessProfile(): Promise<void> {

    const dataBusiness = {
      company: {
        address: this.businessForm.value.address,
        addressCountry: this.businessForm.value.addressCountry,
        addressState: this.businessForm.value.addressState,
        addressZipCode: this.businessForm.value.addressZipCode,
        addressCity: this.businessForm.value.addressCity,
        addressSuburb: this.businessForm.value.addressSuburb,
        addressStreet: this.businessForm.value.addressStreet,
        addressStreetNumber: this.businessForm.value.addressStreetNumber,
        addressBuildName: this.businessForm.value.addressBuildName,
        addressUnit: this.businessForm.value.addressUnit,
        companySize: (this.businessForm.value.companySize === null)
          ? null
          : Number(this.businessForm.value.companySize),
        description: this.businessForm.value.description,
        industry: (this.businessForm.value.industry === undefined)
          ? null
          : this.businessForm.value.industry,
        jse: this.jse,
        name: this.businessForm.value.name
      },
      user: {
        email: this.businessForm.value.email,
        firstName: this.businessForm.value.firstName,
        jobTitle: this.businessForm.value.jobTitle,
        lastName: this.businessForm.value.lastName,
        phone: this.businessForm.value.phone,
      }
    };

    try {
      await this._adminService.createBusinessProfile(dataBusiness);
      this._sharedService.sidebarAdminBadges.clientAll++;
      this._toastr.success('Client has been created');
      this._router.navigate(['/admin/all_clients']);
    } catch (err) {
      this._sharedService.showRequestErrors(err);
    }
  }

}
