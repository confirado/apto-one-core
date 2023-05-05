import {  Directive, ElementRef, Host, HostListener, Inject, InjectionToken, Input, ViewContainerRef } from '@angular/core';

export const WINDOW = new InjectionToken<Window>('WindowToken');

@Directive({
  selector: '[aptoTooltip]',
  providers: [
    { provide: WINDOW, useValue: window },
  ]
})
export class TooltipDirective {

  @Input() tooltipContentRef: HTMLElement;
  @Input() aaaa: HTMLElement;

  windowWidth: number;
  windowHeight: number;

  hostDimensions: DOMRect;
  tooltipDimensions: DOMRect;

  constructor(
    @Inject(WINDOW) private window: Window,
    private elementRef: ElementRef,
    @Host() private viewContainerRef: ViewContainerRef,
  ) {
  }

  ngOnDestroy() {
    this.closeToolTip();
  }

  @HostListener('window:resize')
  onResize() {
    this.windowWidth = this.window.innerWidth;
    this.windowHeight = this.window.innerHeight;
  }

  @HostListener('mouseenter')
  show() {
    this.showTooltipElement();
    this.calculateDimensions();
    this.positionTooltipElement();
  }

  @HostListener('mouseleave')
  hide() {
    this.closeToolTip();
  }

  ngOnInit() {
    this.positionHostElement();
    this.calculateScreenDimensions();
    this.closeToolTip();
  }

  positionTooltipElement() {

    let rules = {
      top: 'auto',
      left: 'auto',
      right: 'auto',
      bottom: 'auto',
      height: 'auto',
      width: 'auto',
    };

    // too high
    if (this.hostDimensions.top < this.tooltipDimensions.height) {
      rules.top = this.hostDimensions.height + 'px';

      // for some reason getBoundingClientRect() calculates the height wrong, so we give height here explicitly
      rules.height = this.tooltipDimensions.height + 'px';
    }

    // too low
    if (window.innerHeight - this.hostDimensions.bottom < this.tooltipDimensions.height) {
      rules.top = -(this.tooltipDimensions.height) + 'px';

      // for some reason getBoundingClientRect() calculates the height wrong, so we give height here explicitly
      rules.height = this.tooltipDimensions.height + 'px';
    }

    // too left
    if (this.hostDimensions.left < this.tooltipDimensions.width/2) {
      rules.left = -20 + 'px';
    }

    // too right
    if (window.innerWidth - this.hostDimensions.right < this.tooltipDimensions.width/2) {
      rules.right = -20 + 'px';
    }

    this.tooltipContentRef.style.height = rules['height'];
    this.tooltipContentRef.style.width = rules['width'];
    this.tooltipContentRef.style.right = rules['right'];
    this.tooltipContentRef.style.left = rules['left'];
    this.tooltipContentRef.style.top = rules['top'];

    // by default, we want that tooltip is positioned above it's parent element
    this.tooltipContentRef.style.bottom = rules['bottom'] === 'auto' ? this.hostDimensions.height + 'px' : rules['bottom'];

    this.calculateDimensions();
  }

  calculateScreenDimensions() {
    this.windowWidth = this.window.innerWidth;
    this.windowHeight = this.window.innerHeight;
  }

  calculateDimensions() {
    this.hostDimensions = this.elementRef.nativeElement.getBoundingClientRect();
    this.tooltipDimensions = this.tooltipContentRef.getBoundingClientRect();
  }

  showTooltipElement() {
    this.tooltipContentRef.style.display = 'block';
    this.tooltipContentRef.style.position = 'absolute';
    this.tooltipContentRef.style.zIndex = '5';
  }

  positionHostElement() {
    this.elementRef.nativeElement.style.position = 'relative';
    this.elementRef.nativeElement.style.overflow = 'visible';
  }

  private closeToolTip() {
    this.tooltipContentRef.style.display = 'none';
  }
}
