import { Observable, Subject } from 'rxjs';
import { ProgressElement, ProgressState } from '@apto-catalog-frontend/store/configuration/configuration.model';

// todo shall we implement this interface in our plugins?
export interface PluginInterface {
  destroy$: Subject<void>;
  progressState$: Observable<ProgressState>;
  progressState: ProgressState | null;
  formSavedFromSelectButton: boolean;

  setFormInputs(): void;
  getProgressElement(elementId: string): ProgressElement | null;
}
