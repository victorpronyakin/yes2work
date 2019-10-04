import { Component, Input, OnInit } from '@angular/core';
import { FormControl, FormGroup, Validators } from '@angular/forms';
import { EditAdmin } from '../../../../../entities/models-admin';
import { AdminService } from '../../../../services/admin.service';
import { SharedService } from '../../../../services/shared.service';
import { ToastrService } from 'ngx-toastr';
import { ValidateNumber } from '../../../../validators/custom.validator';

@Component({
  selector: 'app-admin-edit-admin-popup',
  templateUrl: './admin-edit-admin-popup.component.html',
  styleUrls: ['./admin-edit-admin-popup.component.scss']
})
export class AdminEditAdminPopupComponent implements OnInit {

  private _selectedAdmin = new EditAdmin({});
  private _adminsList = Array<EditAdmin>();

  public adminForm: FormGroup;

  @Input() closePopup;

  @Input('selectedAdmin') set selectedAdmin(selectedAdmin) {
    if (selectedAdmin) {
      this._selectedAdmin = selectedAdmin;
    }
  }
  get selectedAdmin() {
    return this._selectedAdmin;
  }

  @Input('adminsList') set adminsList(adminsList) {
    if (adminsList) {
      this._adminsList = adminsList;
    }
  }
  get adminsList() {
    return this._adminsList;
  }

  constructor(
    private readonly _adminService: AdminService,
    private readonly _sharedService: SharedService,
    private readonly _toastr: ToastrService
  ) {
    this._sharedService.checkSidebar = false;
  }

  ngOnInit() {
    this.adminForm = new FormGroup({
      firstName: new FormControl(this._selectedAdmin.firstName, [Validators.required, Validators.minLength(2)]),
      lastName: new FormControl(this._selectedAdmin.lastName, [Validators.required, Validators.minLength(2)]),
      phone: new FormControl(this._selectedAdmin.phone, [
        Validators.required,
        ValidateNumber
      ]),
      email: new FormControl(this._selectedAdmin.email, Validators.compose([
        Validators.required,
        Validators.email
      ])),
      role: new FormControl(this._selectedAdmin.roles[0], [Validators.required])
    });
  }

  /**
   * Edit admin
   * @return {Promise<void>}
   */
  public async editAdmin(): Promise<void> {
    if(this.adminForm.valid) {
      this._selectedAdmin.firstName = this.adminForm.value.firstName;
      this._selectedAdmin.lastName = this.adminForm.value.lastName;
      this._selectedAdmin.phone = this.adminForm.value.phone;
      this._selectedAdmin.email = this.adminForm.value.email;
      this._selectedAdmin.roles[0] = this.adminForm.value.role;

      try {
        await this._adminService.editAdmin(this._selectedAdmin);
        this.closePopup();
        this._toastr.success('Admin has been edited');
      }
      catch (err) {
        this._sharedService.showRequestErrors(err);
      }
    }
    else {
      this._sharedService.validateAllFormFields(this.adminForm);
    }
  }

}
