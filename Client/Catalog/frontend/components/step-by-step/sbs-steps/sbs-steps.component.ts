import { Component, Inject, OnInit, ViewChild } from '@angular/core';
import { ProgressStep } from '@apto-catalog-frontend-configuration-model';
import { selectProgressState } from '@apto-catalog-frontend-configuration-selectors';
import { selectProduct } from '@apto-catalog-frontend/store/product/product.selectors';
import { Store } from '@ngrx/store';
import { BreakpointObserver, BreakpointState } from '@angular/cdk/layout';
import { ScreenSizesInterface } from '@apto-frontend/src/configs-static/screen-sizes-interface';
import { ScreenBreakpoints } from '@apto-frontend/src/configs-static/screen-breakpoints';

@Component({
	selector: 'apto-sbs-steps',
	templateUrl: './sbs-steps.component.html',
	styleUrls: ['./sbs-steps.component.scss'],
  providers: [
    {provide: 'BREAKPOINTS', useValue: ScreenBreakpoints}
  ]
})
export class SbsStepsComponent implements OnInit {
	public product$ = this.store.select(selectProduct);

	public readonly steps$ = this.store.select(selectProgressState);

  public isExpanded = true;
  public isDisabled = false;
  public hideToggle = false;

  public constructor(
    private store: Store,
    @Inject('BREAKPOINTS') private screenBreakPoints: ScreenSizesInterface,
    private breakpointObserver: BreakpointObserver,
  ) { }

  ngOnInit() {
    this.breakpointObserver.observe(
      [...Object.values(this.screenBreakPoints)]
    ).subscribe((result: BreakpointState) => {
      // on desktop we want to display left steps opened
      this.isExpanded = result.matches && result.breakpoints[this.screenBreakPoints.desktop];
      this.isDisabled = result.matches && result.breakpoints[this.screenBreakPoints.desktop];
      this.hideToggle = result.matches && result.breakpoints[this.screenBreakPoints.desktop];
    });
  }

	public stepTrackBy(index: number, section: ProgressStep): string {
		return section.section.id;
	}
}
