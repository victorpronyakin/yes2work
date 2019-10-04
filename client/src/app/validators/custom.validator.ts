import { AbstractControl } from '@angular/forms';

/**
 * validates closure date for business jobs add page
 * @param control
 */
export function ValidateApplicationClosureDate(control: AbstractControl) {
    const currentDate = new Date();
    const lastDateBeforeClosure = new Date().setMonth(currentDate.getMonth() + 1);
    if (control.value) {
        if (control.value.jsdate) {
            const selectedDate = control.value.jsdate.getTime();
            if ((selectedDate > lastDateBeforeClosure) || (selectedDate < currentDate.getTime())) {
                return { validApplicationClosureDate: true };
            } else {
                return null;
            }
        }
    }
}


/**
 * validates closure date for business jobs add page
 * @param control
 */
export function ValidateNumber(control: AbstractControl) {
  if (!control.value) {
    return { validNumber: true };
  }else if (control.value.length < 12 ) {
    return { validNumber: true };
  } else {
    return null;
  }
}

/**
 * validates closure date for business edit page
 * @param jobCreatedDate
 */
export const closureDateValidator = (jobCreatedDate) => {
    return (control: AbstractControl) => {
        const currentDate = new Date();
        const jobCreatedDateObject = new Date(jobCreatedDate);
        const lastDateBeforeClosure = jobCreatedDateObject.setMonth(jobCreatedDateObject.getMonth() + 1);
        if (control.value) {
            if (control.value.jsdate) {
                const selectedDate = control.value.jsdate.getTime();
                if ((selectedDate > lastDateBeforeClosure) || (selectedDate < currentDate.getTime())) {
                    return { validApplicationClosureDate: true };
                } else {
                    return null;
                }
            }
        }
        return null;
    };
};

export const jobClosureDateValidator = (jobClosureDate) => {
  return (control: AbstractControl) => {
      const currentDate = new Date();
      const jobClosureDateObject = new Date(jobClosureDate);
      if (control.value) {
          if (control.value.jsdate) {
              const selectedDate = control.value.jsdate.getTime();
              if ((selectedDate < currentDate.getTime())) {
                  return { validApplicationJobClosureDate: true };
              } else {
                  return null;
              }
          }
      }
      return null;
  };
};

/**
 * validates availability date
 * @param control
 */
export function ValidateAvailabilityDate(control: AbstractControl) {
  const currentDate = new Date();
  if (control.value) {
    if (control.value.jsdate) {
      const selectedDate = control.value.jsdate.getTime();
      if (selectedDate < currentDate.getTime()) {
        return { validAvailabilityDate: true };
      } else {
        return null;
      }
    }
  }
  else if(control.value === ''){
    return { validAvailabilityDateEnter: true };
  }
}

/**
 * validates id number
 * @param control
 * @returns {{}}
 * @constructor
 */
export function ValidateIdNumber(control: AbstractControl) {
  const idNumber = control.value;
  const validationErrors = {};

  if (idNumber !== undefined && idNumber !== null && idNumber.length > 0) {
    if (isNaN(idNumber)) {
      validationErrors['invalidIdNumber'] = 'Please enter a valid South African ID Number.';
    }

    if (idNumber.length !== 13) {
      validationErrors['invalidIdNumber'] = 'Please enter a valid South African ID Number.';
    }

    const yy = idNumber.substring(0, 2);
    const mm = idNumber.substring(2, 4);
    const dd = idNumber.substring(4, 6);

    const dob = new Date(yy, (mm - 1), dd);

    if (!(((dob.getFullYear() + '').substring(2, 4) === yy) && (dob.getMonth() === mm - 1) && (dob.getDate() == dd))) {
      validationErrors['invalidIdNumber'] = 'Please enter a valid South African ID Number.';
    }

    if (idNumber.substring(10, 11) > 1) {
      validationErrors['invalidIdNumber'] = 'Please enter a valid South African ID Number.';
    }

    if (idNumber.substring(11, 12) < 8) {
      validationErrors['invalidIdNumber'] = 'Please enter a valid South African ID Number.';
    }

    let ncheck = 0;
    let beven = false;

    for (let c = idNumber.length - 1; c >= 0; c--) {
      const cdigit = idNumber.charAt(c);
      let ndigit = parseInt(cdigit, 10);

      if (beven) {
          if ((ndigit *= 2) > 9) {
              ndigit -= 9;
          }
      }

      ncheck += ndigit;
      beven = !beven;
    }

    if ((ncheck % 10) !== 0) {
      validationErrors['invalidIdNumber'] = 'Please enter a valid South African ID Number.';
    }
  } else {
      validationErrors['invalidIdNumber'] = 'Please enter a valid South African ID Number.';
  }

  return (validationErrors) ? validationErrors : null;
}

/**
 * validates id number
 * @param control
 * @returns {{}}
 * @constructor
 */
export function CustomValidateIdNumber(control: AbstractControl) {
  const idNumber = control.value;
  const validationErrors = {};


  if (idNumber.length > 0) {
    if (isNaN(idNumber)) {
      validationErrors['invalidIdNumber'] = 'Please enter a valid South African ID Number.';
    }

    if (idNumber.length !== 13) {
      validationErrors['invalidIdNumber'] = 'Please enter a valid South African ID Number.';
    }

    const yy = idNumber.substring(0, 2);
    const mm = idNumber.substring(2, 4);
    const dd = idNumber.substring(4, 6);

    const dob = new Date(yy, (mm - 1), dd);

    const year = new Date();
    const fullYearNumber = year.getFullYear();
    const fullYear = Number(year.getFullYear().toString().substr(2,2));

    let lastCentury;
    let currentAge;
    if (fullYear < Number(yy)) {
      lastCentury = '19' + yy;
      currentAge = fullYearNumber - Number(lastCentury);
    } else {
      lastCentury = '20' + yy;
      currentAge = fullYearNumber - Number(lastCentury);
    }

    if (currentAge < 17) {
      validationErrors['invalidIdNumber'] = 'Your age does not meet the requirements, therefore you cannot register.';
    }
    if (currentAge > 35) {
      validationErrors['invalidIdNumber'] = 'Your age does not meet the requirements, therefore you cannot register.';
    }

    if (!(((dob.getFullYear() + '').substring(2, 4) === yy) && (dob.getMonth() === mm - 1) && (dob.getDate() == dd))) {
      validationErrors['invalidIdNumber'] = 'Please enter a valid South African ID Number.';
    }

    if (idNumber.substring(10, 11) > 1) {
      validationErrors['invalidIdNumber'] = 'Please enter a valid South African ID Number.';
    }

    if (idNumber.substring(11, 12) < 8) {
      validationErrors['invalidIdNumber'] = 'Please enter a valid South African ID Number.';
    }

    let ncheck = 0;
    let beven = false;

    for (let c = idNumber.length - 1; c >= 0; c--) {
      const cdigit = idNumber.charAt(c);
      let ndigit = parseInt(cdigit, 10);

      if (beven) {
        if ((ndigit *= 2) > 9) {
            ndigit -= 9;
        }
      }

      ncheck += ndigit;
      beven = !beven;
    }

    if ((ncheck % 10) !== 0) {
      validationErrors['invalidIdNumber'] = 'Please enter a valid South African ID Number.';
    }
  } else {
    validationErrors['invalidIdNumber'] = 'Please enter a valid South African ID Number.';
  }

  return (validationErrors) ? validationErrors : null;
}
