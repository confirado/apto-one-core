import { Pipe, PipeTransform } from '@angular/core';

/**
 * Converts an object to a string that can be used as css inline style:
 *
 * Example input:
 * { textAlign: 'center', color: red }
 *
 * Example Output
 * "textAlign: 'center', color: red"
 */
@Pipe({
  name: 'joinStyles',
  pure: false,
  standalone: true,
})
export class JoinStylesPipe implements PipeTransform {
  transform(styles: { [key: string]: string }): string {
    if (!styles) {
      return '';
    }

    return Object.keys(styles)
      .map((key) => `${key}: ${styles[key]}`)
      .join(', ');
  }
}
