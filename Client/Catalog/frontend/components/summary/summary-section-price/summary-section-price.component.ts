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
} from "@apto-catalog-frontend-configuration-selectors";
import { SectionPriceTableItem } from "@apto-catalog-frontend-configuration-model";
import { selectContentSnippet } from "@apto-base-frontend/store/content-snippets/content-snippets.selectors";

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
            this.showElement();
          } else {
            this.hideElement();
          }
          break;
        }
      }
    }
  }

  private showElement() {
    // get the height of the element's inner content, regardless of its actual size
    const sectionHeight = this.viewRef.element.nativeElement.scrollHeight;

    // have the element transition to the height of its inner content
    this.viewRef.element.nativeElement.style.height = sectionHeight + 'px';

    // when the next css transition finishes (which should be the one we just triggered)
    this.viewRef.element.nativeElement.addEventListener('transitionend', this.onTransitionEnd);
  }

  private hideElement() {
    // remove this event listener because it should only emitted on show transition but not on hide transition
    this.viewRef.element.nativeElement.removeEventListener('transitionend', this.onTransitionEnd);

    // get the height of the element's inner content, regardless of its actual size
    const sectionHeight = this.viewRef.element.nativeElement.scrollHeight;

    // temporarily disable all css transitions
    const elementTransition = this.viewRef.element.nativeElement.style.transition;
    this.viewRef.element.nativeElement.style.transition = '';

    // on the next frame (as soon as the previous style change has taken effect),
    // explicitly set the element's height to its current pixel height, so we
    // aren't transitioning out of 'auto'
    requestAnimationFrame(() => {
      this.viewRef.element.nativeElement.style.height = sectionHeight + 'px';
      this.viewRef.element.nativeElement.style.transition = elementTransition;

      // on the next frame (as soon as the previous style change has taken effect),
      // have the element transition to height: 0
      requestAnimationFrame(() => {
        this.viewRef.element.nativeElement.style.height = '0px';
      });
    });
  }

  private onTransitionEnd = () => {
    // remove this event listener so it only gets triggered once
    this.viewRef.element.nativeElement.removeEventListener('transitionend', this.onTransitionEnd);

    // remove "height" from the element's inline styles, so it can return to its initial value
    this.viewRef.element.nativeElement.style.height = null;
  }
}
