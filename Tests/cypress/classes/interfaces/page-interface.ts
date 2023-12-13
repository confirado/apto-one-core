import { IRequestData } from '../Models';

export interface AbstractPage {
  visit(): void;
  isCorrectPage(): void;
  get initialQueryList(): IRequestData[];
  get initialAliasList(): string[];
}

export interface IPage {
  visit(): void;
  isCorrectPage(): void;
  initialQueryList: IRequestData[];
  initialCommandList: IRequestData[];
  initialCustomRequestList: IRequestData[];
}
