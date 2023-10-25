import {
  Component,
  ElementRef,
  HostBinding,
  Input,
  OnChanges,
  OnInit,
  SimpleChanges,
  ViewContainerRef
} from "@angular/core";
import { Section } from "@apto-catalog-frontend/store/product/product.model";
import { Store } from "@ngrx/store";
import {
  selectSectionPrice,
  selectSectionPriceTable
} from "@apto-catalog-frontend/store/configuration/configuration.selectors";
import { SectionPriceTableItem } from "@apto-catalog-frontend/store/configuration/configuration.model";
import {selectContentSnippet} from "@apto-base-frontend/store/content-snippets/content-snippets.selectors";

@Component({
  selector: 'apto-summary-section-price',
  templateUrl: './summary-section-price.component.html',
  styleUrls: ['./summary-section-price.component.scss']
})
export class SummarySectionPriceComponent implements OnInit, OnChanges {
  @Input() public section: Section = null;
  @Input() public expanded: boolean = true;
  @HostBinding('class.expanded') classExpanded: boolean = false;

  public readonly contentSnippets$ = this.store.select(selectContentSnippet('aptoSummary.sectionPrices'));
  public priceTable: SectionPriceTableItem[] = [];
  public sectionPrice: null | string = null;

  constructor(private readonly viewRef: ViewContainerRef, private store: Store) {
  }

  public ngOnInit() {
    this.store.select(selectSectionPriceTable(this.section)).subscribe((next: SectionPriceTableItem[]) => {
      this.priceTable = next;
    });

    this.store.select(selectSectionPrice(this.section)).subscribe((next: null | string) => {
      this.sectionPrice = next;
    });
  }

  public ngOnChanges(changes: SimpleChanges) {
    for (const propName in changes) {
      switch (propName) {
        case 'expanded': {
          this.classExpanded = changes[propName].currentValue;
          if (true === this.expanded) {
            this.showElement(this.viewRef.element);
          } else {
            this.hideElement(this.viewRef.element);
          }
          break;
        }
      }
    }
  }

  private showElement(element: ElementRef) {
    // get the height of the element's inner content, regardless of its actual size
    const sectionHeight = element.nativeElement.scrollHeight;

    // have the element transition to the height of its inner content
    element.nativeElement.style.height = sectionHeight + 'px';

    const onTransitionEnd = () => {
      // remove this event listener so it only gets triggered once
      element.nativeElement.removeEventListener('transitionend', onTransitionEnd);

      // remove "height" from the element's inline styles, so it can return to its initial value
      element.nativeElement.style.height = null;
    };

    // when the next css transition finishes (which should be the one we just triggered)
    element.nativeElement.addEventListener('transitionend', onTransitionEnd);
  }

  private hideElement(element: ElementRef) {
    // get the height of the element's inner content, regardless of its actual size
    const sectionHeight = element.nativeElement.scrollHeight;

    // temporarily disable all css transitions
    const elementTransition = element.nativeElement.style.transition;
    element.nativeElement.style.transition = '';

    // on the next frame (as soon as the previous style change has taken effect),
    // explicitly set the element's height to its current pixel height, so we
    // aren't transitioning out of 'auto'
    requestAnimationFrame(function() {
      element.nativeElement.style.height = sectionHeight + 'px';
      element.nativeElement.style.transition = elementTransition;

      // on the next frame (as soon as the previous style change has taken effect),
      // have the element transition to height: 0
      requestAnimationFrame(function() {
        element.nativeElement.style.height = '0px';
      });
    });
  }
}
