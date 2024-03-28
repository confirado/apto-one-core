import { Component, Input, OnInit } from '@angular/core';
import { environment } from '@apto-frontend/src/environments/environment';
import { HttpClient } from '@angular/common/http';
import { NgIf, NgStyle } from '@angular/common';
import { JoinStylesPipe } from '@apto-base-frontend/pipes/join-styles.pipe';
import { DomSanitizer, SafeHtml } from '@angular/platform-browser';

/**
 * Example calls:
 *
 * Without arguments:
 *  <apto-svg [name]="'icons-default-rocket'"></apto-svg>
 *
 * With styles as argument:
 *  <apto-svg [name]="'icons-default-rocket'" [styles]="{fill: 'orange', width: '50px'}"></apto-svg>
 *
 * With styles and custom path:
 *  <apto-svg [name]="'icons-default-rocket'" [styles]="{fill: 'orange', width: '50px'}" [path]="mediaUrl + 'icons/svg'"></apto-svg>
 *
 * Tips:
 * If you want to be able to give width and height from parent element then remove width and height attributes
 * from svg itself or make them 100%
 */
@Component({
  selector: 'apto-svg',
  templateUrl: './svg.component.html',
  styleUrls: ['./svg.component.scss'],
  standalone: true,
  imports: [
    NgIf,
    JoinStylesPipe,
    NgStyle,
  ],
})
export class SVGComponent implements OnInit {

  @Input()
  name: string = '';

  /**
   * is not required. if given must be given with absolute path: mediaUrl + 'icons/svg'
   *
   * default is ${this.clientUrl}/assets/icons/svg
   */
  @Input()
  path: string | undefined;

  /**
   * is not required. Any valid css styles as object: { fill: 'red', width: '50px' }
   */
  @Input()
  styles: any;

  private readonly clientUrl = `${environment.api.client}`;

  protected svgPath: string;
  protected svgIcon: SafeHtml | undefined;

  public constructor(
    private httpClient: HttpClient,
    private sanitizer: DomSanitizer,
  ) {}

  public ngOnInit(): void {
    this.svgPath = this.path.length ? `${this.path}/` : `${this.clientUrl}/assets/icons/svg/`;

    if (this.name) {
      this.loadSvg(this.name);
    }
  }

  private loadSvg(name: string): void {
    this.httpClient.get(`${this.svgPath}/${name}.svg`, { responseType: 'text' })
      .subscribe((data) => this.svgIcon = this.sanitizer.bypassSecurityTrustHtml(data));
  }
}
