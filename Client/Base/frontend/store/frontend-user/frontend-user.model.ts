import { CustomerGroup } from '@apto-base-frontend/store/customer-group/customer-group.model';

export interface FrontendUser {
  isLoggedIn: boolean,
  id: string,
  userName: string,
  email: string,
  customerGroup: CustomerGroup,
  customerNumber: string
}
