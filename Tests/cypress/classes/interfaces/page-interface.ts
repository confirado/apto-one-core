import { IRequestData } from '../models';

export interface AbstractPage {
  visit(): void;
  isCorrectPage(): void;
  get initialQueryList(): IRequestData[];
  get initialAliasList(): string[];
}

export interface IPage {
  visit(): void;
  isCorrectPage(): void;
  hasCorrectPageContent(): void;
  initialQueryList: IRequestData[];
  initialCommandList: IRequestData[];
  initialCustomRequestList: IRequestData[];
  initialRequests: IRequestData[];
}
