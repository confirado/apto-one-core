import { Injectable } from '@angular/core';
import { randomBytes } from 'crypto';

@Injectable()
export class CryptoService {

  public static generateRandomString(length: number): string {
    const charset = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    const randomBytesArray = randomBytes(length);
    let result = '';
    for (let i = 0; i < randomBytesArray.length; i++) {
      const randomIndex = randomBytesArray[i] % charset.length;
      result += charset.charAt(randomIndex);
    }
    return result;
  }
}
