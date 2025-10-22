import { Component } from '@angular/core';
import { ProgressStep } from '@apto-catalog-frontend-configuration-model';
import { selectProgressState } from '@apto-catalog-frontend-configuration-selectors';
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
    public stepsWithoutGroups: ProgressStep[];

    public constructor(private store: Store) {
    }

    public stepTrackBy(index: number, progressStep: ProgressStep): string {
        return progressStep.section.id;
    }

    public ngOnInit(): void {
        this.steps$.subscribe((steps) => {
            this.uniqueGroups = [...new Map(steps.steps.map(step => [step.section?.group?.id, step.section?.group])).values()]
                .filter(group => group != null);
            this.groupedSteps = this.uniqueGroups.map(group => (
                { group, steps: steps.steps.filter(step => group?.id === step.section?.group?.id) }
            ));
            this.stepsWithoutGroups = steps.steps.filter(step => !step.section?.group);
        });
    }
}
