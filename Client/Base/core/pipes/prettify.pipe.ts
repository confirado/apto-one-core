import { Pipe, PipeTransform } from '@angular/core';

@Pipe({
  name: 'prettify',
  pure: false
})
export class PrettifyPipe implements PipeTransform {

  transform(value: any): string {
    return this.syntaxHighlight(value);
  }

  syntaxHighlight(json: any): string {
    json = json.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    const inner = json.replace(/("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)/g, function (match: any) {
      let cls = 'number';
      if (/^"/.test(match)) {
        if (/:$/.test(match)) {
          cls = 'key';
        } else {
          cls = 'string';
        }
      } else if (/true|false/.test(match)) {
        cls = 'boolean';
      } else if (/null/.test(match)) {
        cls = 'null';
      }
      return '<span class="' + cls + '">' + match + '</span>';
    });

    return '<div class="prettify-pipe">' + inner + '</div>'
  }
}
