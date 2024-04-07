import { Injectable } from '@angular/core';
import { FormControl, ValidationErrors } from '@angular/forms';

/* Here we should write all custom validators for all our 2.1 forms */

@Injectable({
  providedIn: 'root',
})
export class FormValidationsService {

  public emailValidator = (control: FormControl): ValidationErrors | null => {
    const email = control.value as string;
    const emailParts = email.split('@');

    if (emailParts.length !== 2) {
      return { invalidEmail: true };
    }

    const normalizedDomain = this.normalizeDomain(emailParts[1]);
    const normalizedEmail = `${emailParts[0]}@${normalizedDomain}`;

    return this.isValidEmail(normalizedEmail) ? null : { invalidEmail: true };
  };

  public normalizeDomain = (domain: string): string => {
    return domain.normalize('NFC').toLowerCase();
  };

  public isValidEmail = (email: string): boolean => {
    return /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/.test(email);
  };
}
