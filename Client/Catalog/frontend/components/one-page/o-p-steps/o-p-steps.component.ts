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

    private groupedStepExpandedStatusMap: Map<string, boolean> = new Map<string, boolean>();
    private stepExpandedStatusMap: Map<string, boolean> = new Map<string, boolean>();
    private expandedStatusInitialized: boolean = false;

    public uniqueGroups: Group[];
    public groupedSteps: GroupedSteps[];
    public stepsWithoutGroups: ProgressStep[];


    public constructor(private store: Store) {
    }


    public isGroupedStepExpanded(groupedStep: GroupedSteps): boolean {
      const key: string = groupedStep.group.id;
      return this.groupedStepExpandedStatusMap.has(key) && this.groupedStepExpandedStatusMap.get(key) === true;
    }
    public setGroupedStepExpanded(groupedStep: GroupedSteps, expanded: boolean): void {
      this.groupedStepExpandedStatusMap.set(groupedStep.group.id, expanded);
    }


    public isStepExpanded(step: ProgressStep): boolean {
      const key: string = step.section.id;
      return this.stepExpandedStatusMap.has(key) && this.stepExpandedStatusMap.get(key) === true;
    }
    public setStepExpanded(step: ProgressStep, expanded: boolean): void {
      this.stepExpandedStatusMap.set(step.section.id, expanded);
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
            for (const groupedStep of this.groupedSteps) {
              if (!this.groupedStepExpandedStatusMap.has(groupedStep.group.id)) {
                this.groupedStepExpandedStatusMap.set(groupedStep.group.id, false);
              }

              for (const step of groupedStep.steps) {
                if (!this.stepExpandedStatusMap.has(step.section.id)) {
                  this.stepExpandedStatusMap.set(step.section.id, false);
                }
              }
            }

            this.stepsWithoutGroups = steps.steps.filter(step => !step.section?.group);
            for (const stepWithoutGroup of this.stepsWithoutGroups) {
              if (!this.stepExpandedStatusMap.has(stepWithoutGroup.section.id)) {
                this.stepExpandedStatusMap.set(stepWithoutGroup.section.id, false);
              }
            }

            if (!this.expandedStatusInitialized) {
              // Expand first group
              if (this.groupedSteps && this.groupedSteps.length > 0) {
                this.setGroupedStepExpanded(this.groupedSteps[0], true);
              }

              // Expand first step
              if (this.groupedSteps && this.groupedSteps.length > 0 && this.groupedSteps[0].steps.length > 0) {
                this.setStepExpanded(this.groupedSteps[0].steps[0], true);
              }
              this.expandedStatusInitialized = true;
            }
        });
    }
}
