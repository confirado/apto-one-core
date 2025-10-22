import { Component, Input } from '@angular/core';
import { ProgressElement } from '@apto-catalog-frontend-configuration-model';
import { Product, Section } from '@apto-catalog-frontend/store/product/product.model';

@Component({
    selector: 'apto-default-element-one-page',
    templateUrl: './default-element-one-page.component.html',
    styleUrls: ['./default-element-one-page.component.scss']
})
export class DefaultElementOnePageComponent {
    @Input()
    public element: ProgressElement | undefined;
    @Input()
    public section: Section | undefined;

    @Input()
    public product: Product | null | undefined;

    @Input()
    public isDialog = false;
}
