import { Component, Input, OnInit } from '@angular/core';
import { FormControl, FormGroup, Validators } from '@angular/forms';
import { EditAdmin } from '../../../../../entities/models-admin';
import { AdminService } from '../../../../services/admin.service';
import { SharedService } from '../../../../services/shared.service';
import { ToastrService } from 'ngx-toastr';
import { ValidateNumber } from '../../../../validators/custom.validator';

@Component({
  selector: 'app-admin-create-admin-popup',
  templateUrl: './admin-create-admin-popup.component.html',
  styleUrls: ['./admin-create-admin-popup.component.scss']
})
export class AdminCreateAdminPopupComponent implements OnInit {

  public adminForm: FormGroup;
  public newAdmin = new EditAdmin({});
  private _adminsList = Array<EditAdmin>();

  @Input() closePopup;
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
      firstName: new FormControl('', [Validators.required, Validators.minLength(2)]),
      lastName: new FormControl('', [Validators.required, Validators.minLength(2)]),
      phone: new FormControl('', [
        Validators.required,
        ValidateNumber
      ]),
      email: new FormControl('', Validators.compose([
        Validators.required,
        Validators.email
      ])),
      role: new FormControl(null, [Validators.required])
    });
  }

  /**
   * Create new admin
   * @return {Promise<void>}
   */
  public async createNewAdmin(): Promise<void> {
    if(this.adminForm.valid) {
      this.newAdmin.firstName = this.adminForm.value.firstName;
      this.newAdmin.lastName = this.adminForm.value.lastName;
      this.newAdmin.phone = this.adminForm.value.phone;
      this.newAdmin.email = this.adminForm.value.email;
      this.newAdmin.roles = [this.adminForm.value.role];

      try {
        if (this.adminForm.valid){
          this.newAdmin.id = await this._adminService.createNewAdmin(this.newAdmin);
          this.closePopup();
          this._adminsList.push(this.newAdmin);
          this._toastr.success('Admin has been created');
        }
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
