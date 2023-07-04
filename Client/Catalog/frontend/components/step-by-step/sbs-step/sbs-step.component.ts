import { Component, Input, OnInit } from '@angular/core';
import {translate, TranslatedValue} from '@apto-base-core/store/translated-value/translated-value.model';
import { setStep } from '@apto-catalog-frontend/store/configuration/configuration.actions';
import { ProgressElement, ProgressState, ProgressStep } from '@apto-catalog-frontend/store/configuration/configuration.model';
import { selectElementValues } from '@apto-catalog-frontend/store/configuration/configuration.selectors';
import { Element, Product } from '@apto-catalog-frontend/store/product/product.model';
import { Store } from '@ngrx/store';
import { Observable } from 'rxjs';
import {selectContentSnippet} from "@apto-base-frontend/store/content-snippets/content-snippets.selectors";
import {DialogSizesEnum} from "@apto-frontend/src/configs-static/dialog-sizes-enum";
import {DialogService} from "@apto-catalog-frontend/components/common/dialogs/dialog-service";
import { environment } from '@apto-frontend/src/environments/environment';
import {ActivatedRoute, Router} from '@angular/router';


@Component({
	selector: 'apto-sbs-step',
	templateUrl: './sbs-step.component.html',
	styleUrls: ['./sbs-step.component.scss'],
})
export class SbsStepComponent implements OnInit {
	@Input()
	public section: ProgressStep | undefined;

	@Input()
	public index: number | undefined;

	@Input()
	public status: string | undefined;

	@Input()
	public description: string | undefined;

	@Input()
	public last: boolean | undefined;

	@Input()
	public product: Product | null | undefined;

	@Input()
	public elements: ProgressElement[] | undefined | null;

	@Input()
	public active: boolean | undefined;

	@Input()
	public state: ProgressState | undefined;

  public readonly popUp$ = this.store.select(selectContentSnippet('aptoSummary.confirmSelectSectionDialog'));
  public locale: string;


	public constructor(private store: Store, private activatedRoute: ActivatedRoute, private dialogService: DialogService, private router: Router) {
    this.locale = environment.defaultLocale;
  }

	public opened(id: string, sectionList: string[]): boolean {
		return sectionList.includes(id);
	}

	public getElementValues(element: Element): Observable<TranslatedValue[] | null | undefined> {
		return this.store.select(selectElementValues(element));
	}

  public openPopUp(isStepByStep: boolean, callback: () => void) {
    let dialogMessage = '';
    let dialogTitle = '';
    let dialogButtonCancel = '';
    let dialogButtonAccept = '';

    this.popUp$.subscribe((next) => {
      if (next === null || isStepByStep === false) {
        callback();
        return;
      }
      next.children.forEach((value) => {
        if (value.name === 'title') {
          dialogTitle = translate(value.content, this.locale);
        }
        if (value.name === 'message') {
          dialogMessage = translate(value.content, this.locale);
        }
        if (value.name === 'buttonCancel') {
          dialogButtonCancel = translate(value.content, this.locale);
        }
        if (value.name === 'buttonAccept') {
          dialogButtonAccept = translate(value.content, this.locale);
        }
      });
      this.dialogService
        .openWarningDialog(DialogSizesEnum.md, dialogTitle, dialogMessage, dialogButtonCancel, dialogButtonAccept)
        .afterClosed()
        .subscribe((next) => {
          if (next === true) {
            callback();
          }
        });
    });
  }

	public setStep(section: ProgressStep | undefined, seoUrl: string, isStepByStep: boolean): void {
		// eslint-disable-next-line no-restricted-globals
		if (section && !this.state?.afterSteps.includes(section)) {
      if (isStepByStep) {
        this.openPopUp(isStepByStep, () => {
          this.store.dispatch(
            setStep({
              payload: {
                id: section.section.id,
              },
            })
          );
        });
      }
		}
	}

	public panelOpenState: boolean = false;

	public isActive: boolean = false;

	public ngOnInit(): void {}

	public togglePanel(): void {
		this.panelOpenState = !this.panelOpenState;
	}

	public isActiveSection(): void {
		this.isActive = true;
	}
}
