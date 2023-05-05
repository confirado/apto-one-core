import { RegisteredRoute } from "@apto-base-core/router/router-registry";
import { HomeComponent } from "@apto-base-frontend/components/home/home.component";

export const Routes: RegisteredRoute[] = [
  { route: { path: '', component: HomeComponent }, priority: 100 }
];
