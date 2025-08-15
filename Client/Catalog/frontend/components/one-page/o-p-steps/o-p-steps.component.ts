import { Component } from '@angular/core';
import { ProgressStep } from '@apto-catalog-frontend/store/configuration/configuration.model';
import { selectProgressState } from '@apto-catalog-frontend/store/configuration/configuration.selectors';
import { selectProduct } from '@apto-catalog-frontend/store/product/product.selectors';
import { Store } from '@ngrx/store';
import { Group } from '@apto-catalog-frontend/store/product/product.model';

interface GroupedSteps {
    group: Group;
    steps: ProgressStep[];
}

@Component({
    selector: 'apto-o-p-steps',
    templateUrl: './o-p-steps.component.html',
    styleUrls: ['./o-p-steps.component.scss']
})
export class OPStepsComponent {
    public product$ = this.store.select(selectProduct);

    public readonly steps$ = this.store.select(selectProgressState);

    public uniqueGroups: Group[];
    public groupedSteps: GroupedSteps[];

    public constructor(private store: Store) {
    }

    public stepTrackBy(index: number, progressStep: ProgressStep): string {
        return progressStep.section.id;
    }

    public ngOnInit(): void {
        this.product$.subscribe((product) => {
            console.log('product', product);
        });

        this.steps$.subscribe((steps) => {
            console.log('steps', steps);

            this.uniqueGroups = [...new Map(steps.steps.map(step => [step.section.group.id, step.section.group])).values()];
            this.groupedSteps = this.uniqueGroups.map(group => (
                { group, steps: steps.steps.filter(step => group.id === step.section.group.id) }
            ));
            const stepsWithoutGroups = steps.steps.filter(step => !step.section.group);

            console.log('this.groupedSteps', this.groupedSteps);
            console.log('stepsWithoutGroups', stepsWithoutGroups);
        });
    }
}
